<?php
/**
 * Deletion scope — the namespace the deployment engine owns.
 *
 * The engine may delete only what it can name. Every object on the site falls
 * into exactly one of three namespaces, and only the first two are ever
 * touched:
 *
 *   AK       carries `_ak_managed`. Reconciled against the manifest; deleted
 *            when its seed key leaves it.
 *   LEGACY   carries POSITIVE evidence of the Zeyna / PeThemes / OCDI demo
 *            import. Purged on every deployment, because a demo can be
 *            re-imported after one.
 *   SYSTEM   everything else — WordPress's own objects, other plugins'
 *            objects, and anything a human made by hand. Never deleted, and
 *            never even queried for deletion.
 *
 * The previous version had no third namespace. It deleted every page, post and
 * project that did not carry `_ak_managed`, which made it a database-wide
 * garbage collector rather than the owner of a defined scope: a WooCommerce
 * page, a plugin's landing page or a page an editor wrote yesterday would all
 * have gone, purely for not being in a manifest that never claimed them.
 *
 * Legacy is therefore identified by EVIDENCE, not by absence. "Not ours" is
 * not evidence of anything.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AK_LEGACY_FLAG = '_ak_legacy';

/**
 * Hosts that only ever appear in vendor demo content.
 *
 * Taken from the parent theme's own importer config: `ocdi_import_files()` in
 * zeyna/inc/demo-import.php points every demo at themes.pethemes.com, and each
 * demo's preview lives on zeyna.pethemes.com. WordPress's WXR importer copies
 * `<guid>` verbatim, and Elementor stores absolute asset URLs inside
 * `_elementor_data` — so imported demo content carries the vendor's own domain
 * in the database, permanently.
 *
 * @return string[]
 */
function ak_legacy_hosts() {
	/**
	 * Filter the vendor hosts treated as proof of demo provenance.
	 *
	 * @param string[] $hosts Host substrings.
	 */
	return apply_filters(
		'ak_legacy_hosts',
		array(
			'pethemes.com',
			'themes.pethemes.com',
			'zeyna.pethemes.com',
		)
	);
}

/**
 * Does this post carry positive evidence of the demo import?
 *
 * @param int $post_id Post ID.
 * @return string|false Reason, or false when there is no evidence.
 */
function ak_legacy_evidence( $post_id ) {
	// Sticky: once identified, an object stays identified. Elementor rewrites
	// `_elementor_data` on save and would otherwise launder the evidence away.
	if ( get_post_meta( $post_id, AK_LEGACY_FLAG, true ) ) {
		return (string) get_post_meta( $post_id, AK_LEGACY_FLAG, true );
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return false;
	}

	$hosts = ak_legacy_hosts();

	// 1. The GUID. WordPress's importer preserves the source site's guid
	//    verbatim, so this survives editing, renaming and re-saving.
	foreach ( $hosts as $host ) {
		if ( false !== stripos( (string) $post->guid, $host ) ) {
			return 'guid host ' . $host;
		}
	}

	// 2. Vendor asset URLs in the body or in Elementor's stored JSON.
	$haystacks = array( (string) $post->post_content );
	foreach ( array( '_elementor_data', '_elementor_page_settings' ) as $meta_key ) {
		$value = get_post_meta( $post_id, $meta_key, true );
		if ( $value ) {
			$haystacks[] = is_string( $value ) ? $value : wp_json_encode( $value );
		}
	}
	foreach ( $haystacks as $hay ) {
		foreach ( $hosts as $host ) {
			if ( false !== stripos( $hay, $host ) ) {
				return 'vendor asset URL ' . $host;
			}
		}
	}

	// 3. Named by the demo's own Redux options as a chrome template. Captured
	//    before those fields are cleared — see ak_capture_redux_templates().
	$named = (array) get_option( 'akbrand_legacy_template_ids', array() );
	if ( in_array( (int) $post_id, array_map( 'intval', $named ), true ) ) {
		return 'referenced by demo Redux template setting';
	}

	return false;
}

/**
 * Record which posts the demo's Redux config points at, before it is cleared.
 *
 * Those template IDs are the only proof that a given `elementor_library` post
 * is the demo's header or footer rather than one the owner built. Clearing the
 * Redux fields first would destroy that proof, so it is captured and kept.
 */
function ak_capture_redux_templates() {
	remove_all_filters( 'option_pe-redux' );
	$redux = get_option( 'pe-redux' );
	if ( function_exists( 'ak_force_redux_chrome' ) ) {
		add_filter( 'option_pe-redux', 'ak_force_redux_chrome' );
	}
	if ( ! is_array( $redux ) ) {
		return;
	}

	$ids = (array) get_option( 'akbrand_legacy_template_ids', array() );
	foreach ( array( 'header_type', 'footer_template', 'page_transition_template', '404_page_template' ) as $field ) {
		if ( ! empty( $redux[ $field ] ) && is_numeric( $redux[ $field ] ) ) {
			$ids[] = (int) $redux[ $field ];
		}
	}

	$ids = array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );
	update_option( 'akbrand_legacy_template_ids', $ids, false );
}

/**
 * Which namespace an object belongs to.
 *
 * @param int $post_id Post ID.
 * @return string 'ak' | 'legacy' | 'system'
 */
function ak_scope_of( $post_id ) {
	if ( ak_is_managed( $post_id ) ) {
		return 'ak';
	}
	if ( ak_is_protected( $post_id ) ) {
		return 'system';
	}
	return ak_legacy_evidence( $post_id ) ? 'legacy' : 'system';
}

/**
 * Objects that are never deletable, whatever else is true of them.
 *
 * Belt and braces over the namespace rules: even if something here somehow
 * acquired legacy evidence, deleting it would break the site or destroy a
 * legal artefact.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function ak_is_protected( $post_id ) {
	$post_id = (int) $post_id;

	$reserved = array_map(
		'intval',
		array(
			get_option( 'page_on_front' ),
			get_option( 'page_for_posts' ),
			get_option( 'wp_page_for_privacy_policy' ),
			get_option( 'woocommerce_shop_page_id' ),
			get_option( 'woocommerce_cart_page_id' ),
			get_option( 'woocommerce_checkout_page_id' ),
			get_option( 'woocommerce_myaccount_page_id' ),
			get_option( 'woocommerce_terms_page_id' ),
		)
	);

	if ( in_array( $post_id, $reserved, true ) ) {
		return true;
	}

	// Post types another plugin owns and manages its own lifecycle for.
	$foreign = array( 'attachment', 'wpcf7_contact_form', 'product', 'shop_order', 'shop_coupon', 'revision', 'nav_menu_item', 'wp_block', 'wp_template', 'wp_template_part', 'wp_navigation', 'wp_global_styles', 'oembed_cache', 'user_request', 'customize_changeset' );

	/**
	 * Filter the post types the deployment engine will never delete.
	 *
	 * @param string[] $foreign Post type names.
	 */
	$foreign = apply_filters( 'ak_protected_post_types', $foreign );

	return in_array( get_post_type( $post_id ), $foreign, true );
}

/**
 * Post types the purge is allowed to LOOK at.
 *
 * Narrow by design. A type absent from this list is never queried, so an
 * unknown plugin's content cannot be deleted even by accident.
 *
 * @return string[]
 */
function ak_purgeable_post_types() {
	$types = array( 'page', 'post', 'portfolio', 'elementor_library', 'e-landing-page' );

	/**
	 * Filter the post types within the purge's reach.
	 *
	 * @param string[] $types Post type names.
	 */
	return apply_filters( 'ak_purgeable_post_types', $types );
}

/**
 * Is this menu legacy?
 *
 * A menu carries no provenance of its own, so it is judged by what is in it:
 * a menu is legacy when it is not ours AND at least one of its items points at
 * a legacy post or a vendor URL. A menu an editor built by hand, containing
 * ordinary links, is left alone.
 *
 * @param int $menu_id Menu term ID.
 * @return string|false Reason, or false.
 */
function ak_menu_legacy_evidence( $menu_id ) {
	if ( ak_is_managed( $menu_id, 'term' ) ) {
		return false;
	}

	$items = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) );
	if ( ! is_array( $items ) ) {
		return false;
	}

	$hosts = ak_legacy_hosts();
	foreach ( $items as $item ) {
		foreach ( $hosts as $host ) {
			if ( false !== stripos( (string) $item->url, $host ) ) {
				return 'item links to ' . $host;
			}
		}
		if ( 'post_type' === $item->type && $item->object_id && ak_legacy_evidence( (int) $item->object_id ) ) {
			return 'item points at demo content';
		}
	}

	return false;
}
