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
 * INVARIANT — no confirmed Zeyna / PeThemes / OCDI residue.
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
 *     · the `pe-redux` option, overwritten wholesale from the demo JSON
 *     · `ocdi_after_import_setup()` → nav_menu_locations, show_on_front and
 *                       page_on_front, pointed at demo objects
 *     · OCDI's own bookkeeping options
 *
 * SCOPE. This deletes only objects carrying POSITIVE evidence of that import —
 * a vendor GUID, a vendor asset URL inside the body or Elementor's JSON, or a
 * template the demo's own Redux config names. See inc/deployment/scope.php.
 *
 * The first version of this function had no such test: it deleted every page,
 * post and project that did not carry `_ak_managed`. That made it a
 * database-wide garbage collector rather than the owner of a defined scope — a
 * WooCommerce page, another plugin's landing page or a page an editor wrote
 * yesterday would all have gone, purely for not being in a manifest that never
 * claimed them. "Not ours" is not evidence of anything.
 *
 * It runs on EVERY deployment, not once. Written as a run-once migration
 * first, and testing found the flaw immediately: activate the AK theme, then
 * import a Zeyna demo afterwards, and the migration is already recorded as
 * done — so "Main Hub, NYC", the demo pages and the demo menu all survive
 * untouched. A migration describes a transition; this is an invariant.
 *
 * It does not touch users, core settings, attachments, or any table wholesale.
 *
 * @param array $report Report, by reference.
 * @return string
 */
function ak_purge_legacy( &$report ) {
	$removed = array(
		'posts'   => 0,
		'menus'   => 0,
		'options' => 0,
		'widgets' => 0,
	);
	$found_evidence = false;

	// Capture the demo's own template pointers BEFORE anything clears them —
	// they are the only proof that a given elementor_library post is the
	// demo's header rather than one the owner built.
	ak_capture_redux_templates();

	// ── Demo content posts ────────────────────────────────────────────────
	// Only types the purge is allowed to look at, and within those only posts
	// carrying POSITIVE evidence of the demo import. "Not in the manifest" is
	// not evidence: a page an editor wrote yesterday, a WooCommerce page and a
	// plugin's landing page are all absent from the manifest and none of them
	// is ours to delete.
	foreach ( ak_purgeable_post_types() as $type ) {
		if ( ! post_type_exists( $type ) ) {
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
			if ( 'legacy' !== ak_scope_of( $id ) ) {
				continue;
			}
			$why                 = ak_legacy_evidence( $id );
			$found_evidence      = true;
			$report['deleted'][] = 'legacy ' . $type . ' — ' . get_the_title( $id ) . ' (#' . $id . '; ' . $why . ')';
			wp_delete_post( $id, true );
			$removed['posts']++;
		}
	}

	// ── Demo menus ────────────────────────────────────────────────────────
	foreach ( (array) wp_get_nav_menus() as $menu ) {
		$why = ak_menu_legacy_evidence( $menu->term_id );
		if ( ! $why ) {
			continue;
		}
		$found_evidence      = true;
		$report['deleted'][] = 'legacy menu — ' . $menu->name . ' (' . $why . ')';
		wp_delete_nav_menu( $menu->term_id );
		$removed['menus']++;
	}

	// ── OCDI bookkeeping ──────────────────────────────────────────────────
	// Unambiguously the importer's own state: nothing else writes these keys.
	foreach ( array( 'ocdi_importer_data', 'pt-ocdi_importer_data', 'ocdi_current_importer_data', 'pt-ocdi_current_importer_data' ) as $opt ) {
		if ( false !== get_option( $opt, false ) ) {
			delete_option( $opt );
			$found_evidence = true;
			$removed['options']++;
		}
	}

	// ── Demo branding inside pe-redux ─────────────────────────────────────
	// Gated on evidence. pe-redux is a plugin-owned option and the child theme
	// reads none of these fields — the footer, contact details and profiles all
	// come from inc/studio.php — so clearing them is hygiene, not a
	// requirement, and hygiene is not a reason to overwrite something an owner
	// may have typed. Only touched when the site actually shows demo residue.
	//
	// Read RAW, via ak_redux_raw(). `pe-redux` is plugin-owned and may be
	// filtered by Redux itself; a filtered value is not what is stored, and
	// this branch writes back what it reads.
	$redux = ak_redux_raw();

	if ( $found_evidence && is_array( $redux ) ) {
		// Only fields a demo fills with its own agency's details — the ones
		// that would show a visitor another studio's footer, address, phone,
		// email or social profiles if anything ever rendered them.
		//
		// The structural keys (`header_type`, `footer_template`, the
		// `transition_*` set, `page_loader`) are deliberately absent. Nothing
		// in this theme reads them any more, so resetting them would be the
		// deployment engine writing a plugin's settings for no reader — which
		// is exactly the overreach this engine is built not to commit.
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

	// ── Widget placements ─────────────────────────────────────────────────
	// DEACTIVATED, NOT DELETED. This build renders no widget areas, so a
	// widget sitting in one is unreachable — but "unreachable" is not
	// "disposable", and a widget instance is core-owned data that may hold
	// text nobody has a copy of. Moving the placements to wp_inactive_widgets
	// makes them invisible and fully recoverable from Appearance → Widgets.
	$sidebars = get_option( 'sidebars_widgets' );
	if ( is_array( $sidebars ) ) {
		$inactive = isset( $sidebars['wp_inactive_widgets'] ) && is_array( $sidebars['wp_inactive_widgets'] )
			? $sidebars['wp_inactive_widgets']
			: array();

		foreach ( $sidebars as $area => $widgets ) {
			if ( 'wp_inactive_widgets' === $area || 'array_version' === $area || ! is_array( $widgets ) || ! $widgets ) {
				continue;
			}
			$inactive           = array_merge( $inactive, $widgets );
			$sidebars[ $area ]  = array();
			$removed['widgets'] += count( $widgets );
		}

		if ( $removed['widgets'] ) {
			$sidebars['wp_inactive_widgets'] = array_values( array_unique( $inactive ) );
			update_option( 'sidebars_widgets', $sidebars );
			$report['deleted'][] = $removed['widgets'] . ' widget placements moved to Inactive Widgets (recoverable)';
		}
	}

	return sprintf(
		'%d posts, %d menus, %d options, %d widgets deactivated',
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
	foreach ( array( 'ak_purge_legacy', 'ak_drop_placeholders' ) as $rule ) {
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
