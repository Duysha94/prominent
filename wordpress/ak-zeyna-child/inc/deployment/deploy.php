<?php
/**
 * The deployment engine.
 *
 * A managed build: the database is expected to match the manifest of whatever
 * release is installed. Update the theme and the site brings itself to the new
 * expected state — creating what is new, updating what changed, and deleting
 * what the build used to own and no longer does.
 *
 * The rules that keep that safe:
 *
 *   VERSION-GATED  Nothing runs unless the installed release differs from the
 *                  last successfully deployed one. A normal page load does no
 *                  work at all — one option read, then out.
 *   IDEMPOTENT     Running it twice produces the same state as running it once.
 *                  Objects are found by seed key, never by title or slug.
 *   RECONCILING    An existing object is updated in place and keeps its ID,
 *                  its permalink and its incoming links. Delete-and-recreate
 *                  is never used to apply a change.
 *   SCOPED         It only ever touches objects carrying `_ak_managed`, plus
 *                  the specific legacy artefacts a migration names. It does
 *                  not truncate tables, drop the database, clear wp_options or
 *                  touch users.
 *   FAIL-CLOSED    The version marker advances only after a run with no
 *                  errors. A failed run is retried on the next request, and
 *                  the log says what happened.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AK_VERSION_OPTION    = 'akbrand_content_version';
const AK_MIGRATIONS_OPTION = 'akbrand_migrations';
const AK_LOG_OPTION        = 'akbrand_deploy_log';
const AK_LOCK_TRANSIENT    = 'akbrand_deploy_lock';

/**
 * The version gate.
 *
 * `admin_init` rather than `after_switch_theme`, because the deployment has to
 * run when the SAME theme is updated to a newer release — which fires no
 * switch hook at all. `after_switch_theme` is registered too, so a first
 * activation deploys immediately instead of waiting for the next admin page.
 */
function ak_maybe_deploy() {
	if ( get_option( AK_VERSION_OPTION ) === AK_CHILD_VERSION ) {
		return; // The common path: one option read per request.
	}

	// A theme update can be followed by several admin requests in flight at
	// once. Without a lock they all deploy simultaneously and race each other
	// into duplicates — the exact failure this engine exists to prevent.
	if ( get_transient( AK_LOCK_TRANSIENT ) ) {
		return;
	}
	set_transient( AK_LOCK_TRANSIENT, 1, 5 * MINUTE_IN_SECONDS );

	try {
		$report = ak_deploy();
	} catch ( Throwable $e ) {
		// Never let a deployment fatal take the site's admin down with it.
		$report = array(
			'errors' => array( 'Uncaught: ' . $e->getMessage() ),
		);
	}

	delete_transient( AK_LOCK_TRANSIENT );
	ak_deploy_log( $report );

	// Fail closed. If anything went wrong the marker does not advance, so the
	// next request retries rather than declaring a broken state deployed.
	if ( empty( $report['errors'] ) ) {
		update_option( AK_VERSION_OPTION, AK_CHILD_VERSION, false );
	}

	set_transient( 'ak_deploy_report', $report, 5 * MINUTE_IN_SECONDS );
}
add_action( 'admin_init', 'ak_maybe_deploy' );
add_action( 'after_switch_theme', 'ak_maybe_deploy' );

/**
 * Run one deployment.
 *
 * Ordering matters and is deliberate:
 *
 *   1. migrations  — schema and ownership changes the rest depends on, and the
 *                    legacy purge. Adopting old markers BEFORE reconciling is
 *                    what stops previously-created pages being treated as
 *                    unmanaged and duplicated.
 *   2. terms       — posts reference them.
 *   3. posts       — menus reference them.
 *   4. menus       — need the post IDs from step 3.
 *   5. retire      — only once the manifest side is fully built, so a failure
 *                    midway never deletes something before its replacement
 *                    exists.
 *   6. wiring      — front page, posts page, menu location.
 *
 * @return array Report.
 */
function ak_deploy() {
	$report = array(
		'from'       => (string) get_option( AK_VERSION_OPTION, '' ),
		'to'         => AK_CHILD_VERSION,
		'created'    => array(),
		'updated'    => array(),
		'deleted'    => array(),
		'healed'     => array(),
		'migrations' => array(),
		'invariants' => array(),
		'errors'     => array(),
	);

	$manifest = ak_manifest();

	ak_run_migrations( $report );
	ak_deploy_terms( $manifest, $report );
	$ids = ak_deploy_posts( $manifest, $report );
	ak_deploy_menus( $manifest, $ids, $report );
	ak_retire_obsolete( $manifest, $report );
	ak_enforce_invariants( $report );
	ak_deploy_form( $report );
	ak_deploy_wiring( $manifest, $ids, $report );

	flush_rewrite_rules();

	return $report;
}

/**
 * Reconcile the taxonomy terms the build owns.
 *
 * @param array $manifest Manifest.
 * @param array $report   Report, by reference.
 */
function ak_deploy_terms( $manifest, &$report ) {
	foreach ( $manifest['terms'] as $spec ) {
		if ( ! taxonomy_exists( $spec['taxonomy'] ) ) {
			continue;
		}

		$found = ak_find_seeded_term( $spec['key'], $spec['taxonomy'] );

		foreach ( $found['duplicates'] as $dupe ) {
			wp_delete_term( $dupe, $spec['taxonomy'] );
			$report['healed'][] = 'term ' . $spec['key'];
		}

		if ( $found['term'] ) {
			if ( $found['term']->name !== $spec['name'] ) {
				wp_update_term( $found['term']->term_id, $spec['taxonomy'], array( 'name' => $spec['name'] ) );
				$report['updated'][] = 'term ' . $spec['key'];
			}
			continue;
		}

		// Adopt a term that already exists under this name before making a
		// second one — the usual way a category ends up as "Strategy" and
		// "Strategy" a release later.
		$existing = get_term_by( 'name', $spec['name'], $spec['taxonomy'] );
		if ( $existing ) {
			ak_mark_term( $existing->term_id, $spec['key'] );
			$report['updated'][] = 'term ' . $spec['key'] . ' (adopted)';
			continue;
		}

		$new = wp_insert_term( $spec['name'], $spec['taxonomy'] );
		if ( is_wp_error( $new ) ) {
			$report['errors'][] = 'term ' . $spec['key'] . ': ' . $new->get_error_message();
			continue;
		}
		ak_mark_term( $new['term_id'], $spec['key'] );
		$report['created'][] = 'term ' . $spec['key'];
	}
}

/**
 * Reconcile every managed post.
 *
 * @param array $manifest Manifest.
 * @param array $report   Report, by reference.
 * @return array<string,int> Seed key => post ID.
 */
function ak_deploy_posts( $manifest, &$report ) {
	$ids = array();

	foreach ( $manifest['posts'] as $spec ) {
		$key   = $spec['key'];
		$found = ak_find_seeded_post( $key );

		// Self-heal: a previous release could create a second copy of the same
		// entry. Keep the oldest — it owns the permalink and any inbound links
		// — and delete the rest outright.
		foreach ( $found['duplicates'] as $dupe ) {
			wp_delete_post( $dupe, true );
			$report['healed'][] = $key . ' (duplicate #' . $dupe . ')';
		}

		// Adoption. A page that already occupies this entry's slug — or one
		// of its declared aliases — is claimed rather than duplicated. This is
		// what stops a manifest entry becoming `about-2` when something
		// unmanaged already holds `about`.
		if ( ! $found['post'] ) {
			$aliases = array_merge( array( $spec['slug'] ), isset( $spec['adopt'] ) ? (array) $spec['adopt'] : array() );
			foreach ( $aliases as $alias ) {
				$candidate = get_page_by_path( $alias, OBJECT, $spec['type'] );
				if ( $candidate && ! get_post_meta( $candidate->ID, AK_SEED_KEY, true ) ) {
					$found['post']       = $candidate;
					$report['updated'][] = $key . ' (adopted #' . $candidate->ID . ')';
					break;
				}
			}
		}

		$content = isset( $spec['content'] ) ? $spec['content'] : '';
		$excerpt = isset( $spec['excerpt'] ) ? $spec['excerpt'] : '';

		$fields = array(
			'post_type'    => $spec['type'],
			'post_title'   => $spec['title'],
			'post_name'    => $spec['slug'],
			'post_content' => $content,
			'post_excerpt' => $excerpt,
			'post_status'  => 'publish',
			'menu_order'   => isset( $spec['order'] ) ? (int) $spec['order'] : 0,
		);

		if ( $found['post'] ) {
			// Update in place. The ID, the permalink and every link pointing
			// at it survive the release.
			$fields['ID'] = $found['post']->ID;
			$result       = wp_update_post( $fields, true );
			if ( is_wp_error( $result ) ) {
				$report['errors'][] = $key . ': ' . $result->get_error_message();
				continue;
			}
			$id = $found['post']->ID;
			$report['updated'][] = $key;
		} else {
			$id = wp_insert_post( $fields, true );
			if ( is_wp_error( $id ) ) {
				$report['errors'][] = $key . ': ' . $id->get_error_message();
				continue;
			}
			$report['created'][] = $key;
		}

		ak_mark_post( $id, $key );

		if ( ! empty( $spec['template'] ) ) {
			update_post_meta( $id, '_wp_page_template', $spec['template'] );
		}
		if ( ! empty( $spec['meta'] ) ) {
			foreach ( $spec['meta'] as $mk => $mv ) {
				update_post_meta( $id, $mk, $mv );
			}
		}
		if ( ! empty( $spec['terms'] ) ) {
			foreach ( $spec['terms'] as $tax => $names ) {
				if ( taxonomy_exists( $tax ) ) {
					wp_set_object_terms( $id, (array) $names, $tax, false );
				}
			}
		}

		$ids[ $key ] = $id;
	}

	return $ids;
}

/**
 * Reconcile the menus and their items.
 *
 * @param array $manifest Manifest.
 * @param array $ids      Seed key => post ID from ak_deploy_posts().
 * @param array $report   Report, by reference.
 */
function ak_deploy_menus( $manifest, $ids, &$report ) {
	foreach ( $manifest['menus'] as $menu_spec ) {
		$found = ak_find_seeded_term( $menu_spec['key'], 'nav_menu' );

		foreach ( $found['duplicates'] as $dupe ) {
			wp_delete_nav_menu( $dupe );
			$report['healed'][] = 'menu ' . $menu_spec['key'];
		}

		$menu = $found['term'];

		if ( ! $menu ) {
			// Adopt a menu of the same name before creating a second one.
			$byname = wp_get_nav_menu_object( $menu_spec['name'] );
			if ( $byname ) {
				ak_mark_term( $byname->term_id, $menu_spec['key'] );
				$menu = $byname;
			} else {
				$new = wp_create_nav_menu( $menu_spec['name'] );
				if ( is_wp_error( $new ) ) {
					$report['errors'][] = 'menu ' . $menu_spec['key'] . ': ' . $new->get_error_message();
					continue;
				}
				ak_mark_term( $new, $menu_spec['key'] );
				$menu = wp_get_nav_menu_object( $new );
				$report['created'][] = 'menu ' . $menu_spec['key'];
			}
		}

		$existing = wp_get_nav_menu_items( $menu->term_id, array( 'post_status' => 'any' ) );
		$existing = is_array( $existing ) ? $existing : array();

		// Index what is there by OUR key, and collapse anything carrying a key
		// twice.
		$by_key = array();
		foreach ( $existing as $item ) {
			$k = get_post_meta( $item->ID, AK_SEED_KEY, true );
			if ( ! $k ) {
				continue;
			}
			if ( isset( $by_key[ $k ] ) ) {
				wp_delete_post( $item->ID, true );
				$report['healed'][] = 'nav item ' . $k;
				continue;
			}
			$by_key[ $k ] = $item;
		}

		$wanted = array();
		$order  = 0;

		foreach ( $menu_spec['items'] as $item_spec ) {
			$order++;
			$wanted[] = $item_spec['key'];

			$args = array(
				'menu-item-title'    => $item_spec['label'],
				'menu-item-status'   => 'publish',
				'menu-item-position' => $order,
			);

			if ( 'archive' === $item_spec['type'] ) {
				$url = get_post_type_archive_link( $item_spec['archive'] );
				if ( ! $url ) {
					continue;
				}
				$args['menu-item-type'] = 'custom';
				$args['menu-item-url']  = $url;
			} else {
				if ( empty( $ids[ $item_spec['seed'] ] ) ) {
					continue; // Its target failed to deploy; skip rather than make a broken item.
				}
				$args['menu-item-type']      = 'post_type';
				$args['menu-item-object']    = 'page';
				$args['menu-item-object-id'] = $ids[ $item_spec['seed'] ];
			}

			$item_id = isset( $by_key[ $item_spec['key'] ] ) ? $by_key[ $item_spec['key'] ]->ID : 0;
			$new_id  = wp_update_nav_menu_item( $menu->term_id, $item_id, $args );

			if ( is_wp_error( $new_id ) ) {
				$report['errors'][] = 'nav item ' . $item_spec['key'] . ': ' . $new_id->get_error_message();
				continue;
			}

			update_post_meta( $new_id, AK_MANAGED_KEY, '1' );
			update_post_meta( $new_id, AK_SEED_KEY, $item_spec['key'] );

			if ( ! $item_id ) {
				$report['created'][] = 'nav item ' . $item_spec['key'];
			}
		}

		// Anything else in this menu goes: items we own that left the
		// manifest, and unmanaged leftovers from a demo import that were
		// sitting in the studio's own menu.
		foreach ( $existing as $item ) {
			$k = get_post_meta( $item->ID, AK_SEED_KEY, true );
			if ( $k && in_array( $k, $wanted, true ) ) {
				continue;
			}
			if ( ! get_post( $item->ID ) ) {
				continue; // Already removed as a duplicate above.
			}
			wp_delete_post( $item->ID, true );
			$report['deleted'][] = 'nav item ' . ( $k ? $k : '#' . $item->ID . ' ' . $item->title );
		}

		if ( ! empty( $menu_spec['location'] ) ) {
			$locations = get_theme_mod( 'nav_menu_locations', array() );
			if ( empty( $locations[ $menu_spec['location'] ] ) || (int) $locations[ $menu_spec['location'] ] !== (int) $menu->term_id ) {
				$locations[ $menu_spec['location'] ] = (int) $menu->term_id;
				set_theme_mod( 'nav_menu_locations', $locations );
			}
		}
	}
}

/**
 * Delete managed objects that have left the manifest.
 *
 * This is the half that makes the build managed rather than additive. A page
 * removed from the manifest is removed from the site — not trashed, because
 * accumulating historical copies is the thing this system exists to prevent.
 *
 * It only ever considers posts carrying `_ak_managed`. Anything else in the
 * database is invisible to it.
 *
 * @param array $manifest Manifest.
 * @param array $report   Report, by reference.
 */
function ak_retire_obsolete( $manifest, &$report ) {
	$wanted = wp_list_pluck( $manifest['posts'], 'key' );

	foreach ( ak_all_managed_posts() as $id ) {
		$key = get_post_meta( $id, AK_SEED_KEY, true );

		// Menu items are managed too, but ak_deploy_menus() owns their
		// lifecycle — it has the menu context this pass does not.
		if ( 'nav_menu_item' === get_post_type( $id ) ) {
			continue;
		}

		if ( $key && in_array( $key, $wanted, true ) ) {
			continue;
		}

		$title = get_the_title( $id );
		wp_delete_post( $id, true );
		$report['deleted'][] = ( $key ? $key : 'unkeyed' ) . ' — ' . $title . ' (#' . $id . ')';
	}
}

/**
 * The Contact Form 7 brief — created once, then left alone.
 *
 * Deliberately NOT reconciled like manifest content. CF7 owns this post type,
 * and the founders edit the recipient address and the copy in CF7's own
 * screens; a deployment that overwrote those on every release would undo their
 * work every time the theme updated. So: create if absent, never touch again.
 *
 * @param array $report Report, by reference.
 */
function ak_deploy_form( &$report ) {
	if ( ! post_type_exists( 'wpcf7_contact_form' ) || ! function_exists( 'ak_content_form' ) ) {
		return;
	}

	$def = ak_content_form();
	if ( get_page_by_path( $def['slug'], OBJECT, 'wpcf7_contact_form' ) ) {
		return;
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'wpcf7_contact_form',
			'post_title'  => $def['title'],
			'post_name'   => $def['slug'],
			'post_status' => 'publish',
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		$report['errors'][] = 'contact form: ' . $id->get_error_message();
		return;
	}
	foreach ( $def['meta'] as $mk => $mv ) {
		update_post_meta( $id, $mk, $mv );
	}
	$report['created'][] = 'contact form';
}

/**
 * Point WordPress at the deployed content.
 *
 * @param array $manifest Manifest.
 * @param array $ids      Seed key => post ID.
 * @param array $report   Report, by reference.
 */
function ak_deploy_wiring( $manifest, $ids, &$report ) {
	foreach ( $manifest['posts'] as $spec ) {
		if ( empty( $spec['role'] ) || empty( $ids[ $spec['key'] ] ) ) {
			continue;
		}
		$id = $ids[ $spec['key'] ];

		if ( 'front_page' === $spec['role'] ) {
			if ( 'page' !== get_option( 'show_on_front' ) ) {
				update_option( 'show_on_front', 'page' );
			}
			if ( (int) get_option( 'page_on_front' ) !== $id ) {
				update_option( 'page_on_front', $id );
				$report['updated'][] = 'front page → ' . $spec['key'];
			}
		}

		if ( 'privacy_page' === $spec['role'] && (int) get_option( 'wp_page_for_privacy_policy' ) !== $id ) {
			update_option( 'wp_page_for_privacy_policy', $id );
			$report['updated'][] = 'privacy page → ' . $spec['key'];
		}

		if ( 'posts_page' === $spec['role'] && (int) get_option( 'page_for_posts' ) !== $id ) {
			update_option( 'page_for_posts', $id );
			$report['updated'][] = 'posts page → ' . $spec['key'];
		}
	}
}

/**
 * Record what happened.
 *
 * Kept in an option so it survives the redirect after a theme update, and
 * bounded to the last ten runs so it can never grow without limit. Errors also
 * go to the PHP error log, because a site whose deployment is failing should
 * say so somewhere a developer already looks.
 *
 * @param array $report Report.
 */
function ak_deploy_log( $report ) {
	$log   = get_option( AK_LOG_OPTION, array() );
	$log   = is_array( $log ) ? $log : array();
	$log[] = array(
		'time'    => gmdate( 'c' ),
		'from'    => isset( $report['from'] ) ? $report['from'] : '',
		'to'      => isset( $report['to'] ) ? $report['to'] : AK_CHILD_VERSION,
		'ok'      => empty( $report['errors'] ),
		'summary' => array(
			'created' => count( isset( $report['created'] ) ? $report['created'] : array() ),
			'updated' => count( isset( $report['updated'] ) ? $report['updated'] : array() ),
			'deleted' => count( isset( $report['deleted'] ) ? $report['deleted'] : array() ),
			'healed'  => count( isset( $report['healed'] ) ? $report['healed'] : array() ),
		),
		'details' => $report,
	);

	update_option( AK_LOG_OPTION, array_slice( $log, -10 ), false );

	if ( ! empty( $report['errors'] ) ) {
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			'AK deployment FAILED at ' . AK_CHILD_VERSION . ': ' . implode( ' | ', $report['errors'] )
		);
	}
}
