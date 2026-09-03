<?php
/**
 * Content sync.
 *
 * Runs once whenever the installed theme version changes — that is, right
 * after a theme update — and reconciles the database with the manifest in
 * `inc/content.php`:
 *
 *   MISSING   → created
 *   UNTOUCHED → refreshed to the shipped version
 *   EDITED    → left exactly as the founders left it
 *   RETIRED   → moved to the Trash (never hard-deleted)
 *
 * "Untouched" is decided by a hash of precisely what sync last wrote. If the
 * post's current title/content/excerpt still hash to that value, nobody has
 * edited it and it is safe to refresh. The moment anyone edits it in
 * wp-admin the hash stops matching and the post becomes theirs — sync will
 * never overwrite it again.
 *
 * "Retired" means a post this theme created (it carries `_ak_import`) whose
 * slug is no longer in the manifest: the old projects, news items and pages
 * from a previous build. Those go to the Trash, where they are recoverable
 * for 30 days.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fire the sync when the installed version no longer matches the code.
 */
add_action(
	'admin_init',
	function () {
		$installed = get_option( 'ak_synced_version' );
		if ( AK_CHILD_VERSION === $installed ) {
			return;
		}
		$report = ak_sync_content();
		update_option( 'ak_synced_version', AK_CHILD_VERSION );
		set_transient( 'ak_sync_report', $report, 5 * MINUTE_IN_SECONDS );
	}
);

/**
 * Reconcile the database with the manifest.
 *
 * @return array{created:string[],updated:string[],skipped:string[],retired:string[]}
 */
function ak_sync_content() {
	$report   = array(
		'created' => array(),
		'updated' => array(),
		'skipped' => array(),
		'retired' => array(),
	);
	$manifest = ak_content_manifest();
	$keep     = array();

	foreach ( $manifest as $item ) {
		$type    = $item['type'];
		$slug    = $item['slug'];
		$keep[]  = $type . ':' . $slug;
		$title   = $item['title'];
		$content = isset( $item['content'] ) ? $item['content'] : '';
		$excerpt = isset( $item['excerpt'] ) ? $item['excerpt'] : '';
		$hash    = md5( $title . '|' . $content . '|' . $excerpt );

		$existing = get_page_by_path( $slug, OBJECT, $type );

		if ( ! $existing ) {
			$id = wp_insert_post(
				array(
					'post_type'    => $type,
					'post_name'    => $slug,
					'post_title'   => $title,
					'post_content' => $content,
					'post_excerpt' => $excerpt,
					'post_status'  => 'publish',
					'menu_order'   => isset( $item['order'] ) ? (int) $item['order'] : 0,
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			ak_sync_apply_meta( $id, $item, $hash );
			$report['created'][] = $slug;
			continue;
		}

		// Has anyone edited it since we last wrote it?
		$stored = get_post_meta( $existing->ID, '_ak_hash', true );
		$actual = md5( $existing->post_title . '|' . $existing->post_content . '|' . $existing->post_excerpt );

		if ( $stored && $stored !== $actual ) {
			// Theirs now. Leave the words alone; still keep the plumbing
			// (template, taxonomy, ownership markers) current.
			ak_sync_apply_meta( $existing->ID, $item, $stored );
			$report['skipped'][] = $slug;
			continue;
		}

		if ( $stored === $hash ) {
			ak_sync_apply_meta( $existing->ID, $item, $hash );
			continue; // Already current — nothing to report.
		}

		wp_update_post(
			array(
				'ID'           => $existing->ID,
				'post_title'   => $title,
				'post_content' => $content,
				'post_excerpt' => $excerpt,
				'menu_order'   => isset( $item['order'] ) ? (int) $item['order'] : $existing->menu_order,
			)
		);
		ak_sync_apply_meta( $existing->ID, $item, $hash );
		$report['updated'][] = $slug;
	}

	// Retire what the theme created and no longer ships.
	$ours = get_posts(
		array(
			'post_type'      => array( 'page', 'post', 'portfolio' ),
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_ak_import', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);
	foreach ( $ours as $id ) {
		$post = get_post( $id );
		if ( ! $post ) {
			continue;
		}
		if ( ! in_array( $post->post_type . ':' . $post->post_name, $keep, true ) ) {
			wp_trash_post( $id );
			$report['retired'][] = $post->post_name;
		}
	}

	// WordPress ships a starter post and page. They are not ours, so the
	// retire pass above leaves them — but on a studio site they are noise.
	// Removed once, on the first sync only, and never touched again.
	if ( ! get_option( 'ak_synced_version' ) ) {
		foreach ( array( array( 'hello-world', 'post' ), array( 'sample-page', 'page' ) ) as $starter ) {
			$post = get_page_by_path( $starter[0], OBJECT, $starter[1] );
			if ( $post && 'publish' === $post->post_status && ! get_post_meta( $post->ID, '_ak_import', true ) ) {
				wp_trash_post( $post->ID );
				$report['retired'][] = $starter[0];
			}
		}
	}

	ak_sync_menu( $report );
	ak_sync_form( $report );

	// Point WordPress at the freshly-synced content. On a first activation
	// the pages did not exist yet when `after_switch_theme` fired, so this
	// is the first moment the wiring can succeed.
	if ( function_exists( 'ak_wire_imported_content' ) ) {
		ak_wire_imported_content();
	}

	flush_rewrite_rules();
	return $report;
}

/**
 * The primary menu, built and hung on Zeyna's `menu-1` location.
 *
 * Items are reconciled every run, so adding a page to the manifest also
 * adds it to the navigation. An existing menu the founders have rearranged
 * keeps its order — only missing items are appended.
 *
 * @param array $report Sync report, by reference.
 */
function ak_sync_menu( &$report ) {
	$menu = wp_get_nav_menu_object( 'Primary' );
	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( 'Primary' );
		if ( is_wp_error( $menu_id ) ) {
			return;
		}
		$menu             = wp_get_nav_menu_object( $menu_id );
		$report['created'][] = __( 'Primary menu', 'ak-zeyna-child' );
	}

	$existing  = wp_get_nav_menu_items( $menu->term_id );
	$have_ids  = array();
	$have_urls = array();
	foreach ( (array) $existing as $item ) {
		$have_ids[]  = (int) $item->object_id;
		$have_urls[] = untrailingslashit( $item->url );
	}

	$order = 0;
	foreach ( ak_content_menu() as $entry ) {
		$order++;

		// The Work entry is a link to the portfolio archive, not a page —
		// giving it a page would put a second claimant on the /work/ route.
		if ( 'archive' === $entry['type'] ) {
			$url = get_post_type_archive_link( 'portfolio' );
			if ( ! $url || in_array( untrailingslashit( $url ), $have_urls, true ) ) {
				continue;
			}
			wp_update_nav_menu_item(
				$menu->term_id,
				0,
				array(
					'menu-item-title'    => $entry['label'],
					'menu-item-url'      => $url,
					'menu-item-type'     => 'custom',
					'menu-item-status'   => 'publish',
					'menu-item-position' => $order,
				)
			);
			continue;
		}

		$page = get_page_by_path( $entry['slug'] );
		if ( ! $page || in_array( (int) $page->ID, $have_ids, true ) ) {
			continue;
		}
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'     => $entry['label'],
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page->ID,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $order,
			)
		);
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['menu-1'] ) ) {
		$locations['menu-1'] = (int) $menu->term_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}

/**
 * The Contact Form 7 project brief.
 *
 * Created once, then left alone — the founders will edit the recipient and
 * the copy in CF7's own screens, and sync must never undo that. If Contact
 * Form 7 is not active there is nothing to create and nothing to warn
 * about; the contact page already falls back to a mailto link.
 *
 * @param array $report Sync report, by reference.
 */
function ak_sync_form( &$report ) {
	if ( ! post_type_exists( 'wpcf7_contact_form' ) ) {
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
		return;
	}
	foreach ( $def['meta'] as $key => $value ) {
		update_post_meta( $id, $key, $value );
	}
	$report['created'][] = __( 'Contact form', 'ak-zeyna-child' );
}

/**
 * Write the plumbing: ownership markers, template, custom fields, taxonomy.
 *
 * @param int    $id   Post ID.
 * @param array  $item Manifest entry.
 * @param string $hash Content hash to record.
 */
function ak_sync_apply_meta( $id, $item, $hash ) {
	update_post_meta( $id, '_ak_import', '1' );
	update_post_meta( $id, '_ak_slug', $item['slug'] );
	update_post_meta( $id, '_ak_hash', $hash );

	if ( ! empty( $item['template'] ) ) {
		update_post_meta( $id, '_wp_page_template', $item['template'] );
	}

	if ( ! empty( $item['meta'] ) ) {
		foreach ( $item['meta'] as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
	}

	if ( ! empty( $item['terms'] ) ) {
		foreach ( $item['terms'] as $taxonomy => $term ) {
			if ( taxonomy_exists( $taxonomy ) ) {
				wp_set_object_terms( $id, $term, $taxonomy );
			}
		}
	}
}

/**
 * Tell the founders what the update did — and what it deliberately did not
 * touch, so an edited case study never disappears without explanation.
 */
add_action(
	'admin_notices',
	function () {
		$report = get_transient( 'ak_sync_report' );
		if ( ! $report || ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		delete_transient( 'ak_sync_report' );

		$lines = array();
		if ( $report['created'] ) {
			/* translators: %s: comma-separated list of page slugs */
			$lines[] = sprintf( __( 'Added: %s', 'ak-zeyna-child' ), implode( ', ', $report['created'] ) );
		}
		if ( $report['updated'] ) {
			/* translators: %s: comma-separated list of page slugs */
			$lines[] = sprintf( __( 'Updated: %s', 'ak-zeyna-child' ), implode( ', ', $report['updated'] ) );
		}
		if ( $report['retired'] ) {
			/* translators: %s: comma-separated list of page slugs */
			$lines[] = sprintf( __( 'Moved to Trash (no longer part of the site): %s', 'ak-zeyna-child' ), implode( ', ', $report['retired'] ) );
		}
		if ( $report['skipped'] ) {
			/* translators: %s: comma-separated list of page slugs */
			$lines[] = sprintf( __( 'Left untouched because you have edited them: %s', 'ak-zeyna-child' ), implode( ', ', $report['skipped'] ) );
		}
		if ( ! $lines ) {
			return;
		}
		printf(
			'<div class="notice notice-success is-dismissible"><p><strong>%s</strong></p><ul style="margin-left:1.2em;list-style:disc"><li>%s</li></ul></div>',
			esc_html__( 'AK theme — content synchronised', 'ak-zeyna-child' ),
			implode( '</li><li>', array_map( 'esc_html', $lines ) )
		);
	}
);

/**
 * If the update channel cannot be reached, say so once rather than leaving
 * the theme silently unable to update itself.
 */
add_action(
	'admin_notices',
	function () {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || ! in_array( $screen->id, array( 'themes', 'update-core', 'dashboard' ), true ) ) {
			return;
		}
		if ( ! current_user_can( 'update_themes' ) ) {
			return;
		}
		$manifest = function_exists( 'ak_update_manifest' ) ? ak_update_manifest() : array();
		if ( ! empty( $manifest['version'] ) ) {
			return;
		}
		printf(
			'<div class="notice notice-warning"><p><strong>%s</strong> %s <code>%s</code></p></div>',
			esc_html__( 'AK theme: automatic updates are not reachable.', 'ak-zeyna-child' ),
			esc_html__( 'The theme cannot read its update channel, so it will not offer one-click updates until the address below serves a manifest. Everything else works normally.', 'ak-zeyna-child' ),
			esc_html( AK_UPDATE_MANIFEST )
		);
	}
);
