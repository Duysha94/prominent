<?php
/**
 * Ordered, run-once migrations.
 *
 * A migration is for work that must happen exactly once in a site's history —
 * adopting an old ownership scheme, purging a legacy import. The manifest
 * reconcile handles ongoing state; migrations handle transitions.
 *
 * Each has an id and is recorded in `akbrand_migrations` once it succeeds, so
 * a site that has already run it never runs it again however many releases
 * pass. Order is the array order and must not be rearranged: later migrations
 * assume earlier ones have completed.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The migrations, in order.
 *
 * @return array<string,callable>
 */
function ak_migrations() {
	return array(
		'001_adopt_legacy_markers' => 'ak_migration_adopt_legacy_markers',
	);
}

/**
 * Run whatever has not run yet.
 *
 * @param array $report Report, by reference.
 */
function ak_run_migrations( &$report ) {
	$done = get_option( AK_MIGRATIONS_OPTION, array() );
	$done = is_array( $done ) ? $done : array();

	foreach ( ak_migrations() as $id => $callback ) {
		if ( isset( $done[ $id ] ) || ! is_callable( $callback ) ) {
			continue;
		}

		try {
			$result = call_user_func( $callback, $report );
		} catch ( Throwable $e ) {
			$report['errors'][] = 'migration ' . $id . ': ' . $e->getMessage();
			return; // Stop the chain: a later migration may depend on this one.
		}

		$done[ $id ] = gmdate( 'c' );
		update_option( AK_MIGRATIONS_OPTION, $done, false );
		$report['migrations'][] = $id . ( $result ? ' — ' . $result : '' );
	}
}

/**
 * 001 — adopt the previous ownership scheme.
 *
 * Releases up to 1.3.2 marked their content with `_ak_import` and `_ak_slug`.
 * The engine keys on `_ak_seed_key`, so without this every one of those pages
 * looks unmanaged: the reconcile would create a SECOND copy of each and the
 * retire pass would never clean the first. This is the single most important
 * migration in the file.
 *
 * @param array $report Report, by reference.
 * @return string
 */
function ak_migration_adopt_legacy_markers( &$report ) {
	$map = array(
		'home'     => 'ak_home',
		'services' => 'ak_services',
		'journal'  => 'ak_journal',
		'about'    => 'ak_about',
		'contact'  => 'ak_contact',
		'privacy'  => 'ak_privacy',
		'what-it-costs-to-show-at-a-fashion-week' => 'ak_post_fashion_week_cost',
		'design-the-identity-at-ad-size-first'    => 'ak_post_ad_size_first',
		'your-company-is-not-your-personal-brand' => 'ak_post_personal_brand',
		'fix-the-website-before-you-buy-the-traffic' => 'ak_post_fix_the_website',
	);

	$adopted = 0;

	$legacy = get_posts(
		array(
			'post_type'      => 'any',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'trash' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_ak_import', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);

	foreach ( $legacy as $id ) {
		if ( get_post_meta( $id, AK_SEED_KEY, true ) ) {
			continue; // Already adopted.
		}

		$slug = get_post_meta( $id, '_ak_slug', true );
		$slug = $slug ? $slug : get_post_field( 'post_name', $id );

		if ( isset( $map[ $slug ] ) ) {
			ak_mark_post( $id, $map[ $slug ] );
			$adopted++;
			continue;
		}

		// Everything else the old scheme created — the six placeholder case
		// studies among them — is left marked managed but WITHOUT a manifest
		// key, so the retire pass removes it. That is the intended outcome:
		// it was ours, we no longer ship it.
		update_post_meta( $id, AK_MANAGED_KEY, '1' );
	}

	// Menu items from the old append-only sync carried `_ak_menu`.
	$old_nav = get_posts(
		array(
			'post_type'      => 'nav_menu_item',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_ak_menu', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		)
	);
	foreach ( $old_nav as $id ) {
		update_post_meta( $id, AK_MANAGED_KEY, '1' );
	}

	// The Primary menu itself, so the menu reconcile adopts rather than
	// duplicates it.
	$primary = wp_get_nav_menu_object( 'Primary' );
	if ( $primary && ! get_term_meta( $primary->term_id, AK_SEED_KEY, true ) ) {
		ak_mark_term( $primary->term_id, 'ak_menu_primary' );
	}

	return $adopted . ' adopted, ' . count( $old_nav ) . ' nav items claimed';
}

/**
 * INVARIANT — no unmanaged content in a managed build.
 *
 * PROVENANCE, established from the parent theme's own source rather than
 * guessed (zeyna/inc/demo-import.php):
 *
 *   Zeyna ships a One Click Demo Import config. `ocdi_import_files()` points
 *   OCDI at `https://themes.pethemes.com/zeyna/demos/xml/<demo>.xml` and
 *   `.../redux/<demo>.json`. Importing a demo therefore writes:
 *
 *     · a WXR import  → pages, posts, portfolio entries, attachments, terms,
 *                       nav_menu terms, nav_menu_item posts, and all postmeta
 *                       including Elementor's `_elementor_data`
 *     · elementor_library posts — the demo header, footer, 404 and loader
 *                       templates that `pe-redux` then points at
 *     · the `pe-redux` option, overwritten wholesale from the demo JSON.
 *                       This is where a demo's contact block, social links and
 *                       copyright line live — the likeliest home of
 *                       "Main Hub, NYC" and "ZEYNA CREATIVE"
 *     · `ocdi_after_import_setup()` → nav_menu_locations, show_on_front and
 *                       page_on_front, pointed at demo objects
 *     · OCDI's own bookkeeping options
 *
 * The purge is written against those CLASSES, not against a list of strings.
 * A string list would only remove the phrases someone happened to notice; the
 * class removes the artefact whatever it says. It is also why this cannot be
 * verified against the production database from here — see
 * docs/LEGACY-DATA-AUDIT.md.
 *
 * Scope: content objects and theme-owned options. It does not touch users,
 * core settings, or any table wholesale.
 *
 * This runs on EVERY deployment, not once. It was written as a run-once
 * migration first, and testing found the flaw immediately: activate the AK
 * theme, then import a Zeyna demo afterwards, and the migration is already
 * recorded as done — so "Main Hub, NYC", the demo pages and the demo menu all
 * survive untouched. A one-time migration describes a transition. This is an
 * invariant: no unmanaged content exists in a managed build, at every release,
 * whenever the demo happened to arrive.
 *
 * @param array $report Report, by reference.
 * @return string
 */
function ak_purge_unmanaged( &$report ) {
	$removed = array(
		'posts'   => 0,
		'menus'   => 0,
		'options' => 0,
		'widgets' => 0,
	);

	// ── Demo content posts ────────────────────────────────────────────────
	// Everything of a content type that is NOT ours. This build is managed and
	// the install started clean, so an unmanaged page is by definition demo
	// residue. Attachments are deliberately excluded: a demo image and an
	// owner's upload are indistinguishable without provenance markers, and
	// deleting the owner's media would be unrecoverable.
	$types = array( 'page', 'post', 'portfolio', 'elementor_library', 'e-landing-page' );
	foreach ( $types as $type ) {
		if ( ! post_type_exists( $type ) && 'e-landing-page' !== $type ) {
			continue;
		}
		$posts = get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		foreach ( $posts as $id ) {
			if ( ak_is_managed( $id ) ) {
				continue;
			}
			// The privacy policy page is a legal artefact; if the owner made
			// one it stays, and our own is separately managed.
			if ( (int) get_option( 'wp_page_for_privacy_policy' ) === (int) $id ) {
				continue;
			}
			$report['deleted'][] = 'legacy ' . $type . ' — ' . get_the_title( $id ) . ' (#' . $id . ')';
			wp_delete_post( $id, true );
			$removed['posts']++;
		}
	}

	// ── Demo menus ────────────────────────────────────────────────────────
	$menus = wp_get_nav_menus();
	foreach ( (array) $menus as $menu ) {
		if ( ak_is_managed( $menu->term_id, 'term' ) ) {
			continue;
		}
		$report['deleted'][] = 'legacy menu — ' . $menu->name;
		wp_delete_nav_menu( $menu->term_id );
		$removed['menus']++;
	}

	// ── OCDI bookkeeping ──────────────────────────────────────────────────
	// The importer's own state. Removing it means a future demo import starts
	// clean rather than believing it has already run.
	foreach ( array( 'ocdi_importer_data', 'pt-ocdi_importer_data', 'ocdi_current_importer_data', 'pt-ocdi_current_importer_data' ) as $opt ) {
		if ( false !== get_option( $opt, false ) ) {
			delete_option( $opt );
			$removed['options']++;
		}
	}

	// ── Demo branding inside pe-redux ─────────────────────────────────────
	// The option itself is live theme configuration and is NOT deleted —
	// dropping it would reset every Zeyna setting the site legitimately uses.
	// Only the fields a demo fills with its own agency's details are cleared.
	// Read the option RAW. inc/setup.php filters `option_pe-redux` to force the
	// chrome and transition keys, so a filtered read reports values that are
	// not in the database — the purge would "clear" them, the filter would
	// report them set again, and every single deployment would rewrite the
	// option forever. Testing caught exactly that: `demo branding cleared from
	// pe-redux` on run after run with nothing actually changing.
	remove_all_filters( 'option_pe-redux' );
	$redux = get_option( 'pe-redux' );

	if ( is_array( $redux ) ) {
		// Only fields a demo fills with its own agency's details. The keys the
		// child forces at runtime are deliberately absent: the filter already
		// governs them, and clearing them here would be a second system
		// writing the same setting.
		$demo_fields = array(
			'404_page_template',
			'loader_logo',
			'transition_logo',
			'transition_caption',
			'transition_repeater_captions',
			'footer_copyright',
			'footer_text',
			'contact_address',
			'contact_phone',
			'contact_email',
			'social_links',
		);
		$dirty = false;
		foreach ( $demo_fields as $field ) {
			if ( array_key_exists( $field, $redux ) && '' !== $redux[ $field ] && array() !== $redux[ $field ] ) {
				$redux[ $field ] = is_array( $redux[ $field ] ) ? array() : '';
				$dirty           = true;
				$removed['options']++;
			}
		}
		if ( $dirty ) {
			update_option( 'pe-redux', $redux );
			$report['deleted'][] = 'demo branding cleared from pe-redux';
		}
	}

	// Put the chrome filter back: the rest of this request still renders
	// through it, and removing it permanently would hand the header and footer
	// back to whatever a demo import last set.
	if ( function_exists( 'ak_force_redux_chrome' ) ) {
		add_filter( 'option_pe-redux', 'ak_force_redux_chrome' );
	}

	// ── Demo widgets ──────────────────────────────────────────────────────
	// The importer fills Zeyna's sidebars. This build renders no widget areas,
	// so any instance present is demo residue.
	$sidebars = get_option( 'sidebars_widgets' );
	if ( is_array( $sidebars ) ) {
		foreach ( $sidebars as $area => $widgets ) {
			if ( 'wp_inactive_widgets' === $area || ! is_array( $widgets ) || ! $widgets ) {
				continue;
			}
			$sidebars[ $area ] = array();
			$removed['widgets'] += count( $widgets );
		}
		if ( $removed['widgets'] ) {
			update_option( 'sidebars_widgets', $sidebars );
			$report['deleted'][] = $removed['widgets'] . ' demo widget placements';
		}
	}

	return sprintf(
		'%d posts, %d menus, %d options, %d widgets',
		$removed['posts'],
		$removed['menus'],
		$removed['options'],
		$removed['widgets']
	);
}

/**
 * INVARIANT — no placeholder case studies.
 *
 * Six posts titled "Client Name", each carrying invented figures: PRESS 38,
 * PRESS 52, CVR +2.4pp, LCP 0.9s. Fabricated results on a live agency site are
 * a liability, not a placeholder. Migration 001 leaves them managed-but-
 * unkeyed and the retire pass then deletes them; this is the belt-and-braces
 * for an install where they arrived through the old WXR import and never
 * carried `_ak_import` at all. Like the purge above it runs on every
 * deployment, so re-importing the old WXR cannot bring them back.
 *
 * @param array $report Report, by reference.
 * @return string
 */
function ak_drop_placeholders( &$report ) {
	if ( ! post_type_exists( 'portfolio' ) ) {
		return 'portfolio type absent';
	}

	$gone = 0;
	foreach ( get_posts(
		array(
			'post_type'      => 'portfolio',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'trash' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	) as $id ) {
		$slug = get_post_field( 'post_name', $id );
		if ( 0 !== strpos( $slug, 'placeholder-' ) ) {
			continue;
		}
		$report['deleted'][] = 'placeholder case — ' . $slug . ' (#' . $id . ')';
		wp_delete_post( $id, true );
		$gone++;
	}

	return $gone . ' placeholder cases removed';
}

/**
 * Enforce every invariant.
 *
 * Runs on each deployment, after the manifest has been reconciled — so
 * adoption has already claimed anything worth keeping and whatever is left
 * unmanaged really is residue.
 *
 * @param array $report Report, by reference.
 */
function ak_enforce_invariants( &$report ) {
	foreach ( array( 'ak_purge_unmanaged', 'ak_drop_placeholders' ) as $rule ) {
		try {
			$result = call_user_func_array( $rule, array( &$report ) );
			if ( $result ) {
				$report['invariants'][] = $rule . ' — ' . $result;
			}
		} catch ( Throwable $e ) {
			$report['errors'][] = 'invariant ' . $rule . ': ' . $e->getMessage();
		}
	}
}
