<?php
/**
 * The parent theme's remaining frontend, detached.
 *
 * Dequeuing assets is not the same as functional independence. Zeyna registers
 * three things on frontend hooks that fire whatever templates the child
 * overrides, and each of them survived every earlier step of the exit because
 * none of them is an enqueue:
 *
 *  1. wp_footer @100 — echoes `$option['js_editor']` from `pe-redux` raw into
 *     a <script> tag. On a site where the Zeyna demo has been imported, that
 *     is arbitrary JavaScript from the demo, running on every page of the
 *     studio's website. This is the single most important removal in the file.
 *
 *  2. body_class — zeyna_body_classes() adds Redux-driven classes:
 *     page--loader--active, page--transitions--active, loader__*,
 *     body--grained, smooth-scroll, show--footer/hide--footer, and whatever
 *     `get_field('page_layout')` returns. They describe a runtime this theme
 *     no longer has, and `hide--footer` in particular would hide a footer the
 *     child renders itself.
 *
 *  3. wp_head — shop_archive_css() and, in inc/static.php,
 *     zeyna_woo_styles_footer(). Both are WooCommerce-only, and both would
 *     reintroduce parent shop styling the moment WooCommerce is activated.
 *
 * All three are removed on `wp` — late enough that the parent has certainly
 * registered them, early enough to precede rendering. Nothing here fails if
 * the parent changes: remove_action() on a hook that is not there is a no-op,
 * and this file makes no assumption that any of them exist.
 *
 * NOT removed: zeyna_pingback_header(), which is WordPress's own standard
 * pingback link and has nothing to do with the theme.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp',
	function () {
		if ( is_admin() ) {
			return;
		}

		/*
		 * NO PARENT, NOTHING TO DETACH.
		 *
		 * Two of the removals below identify a callback by the FILE it was
		 * declared in — they are closures, so there is no name to pass to
		 * remove_action() — and the file test is "declared inside
		 * get_template_directory()".
		 *
		 * In a standalone theme get_template_directory() and
		 * get_stylesheet_directory() are the same path, and that test would
		 * then match the theme's OWN closures: functions.php registers one on
		 * wp_footer, and a future one at priority 100 would be silently
		 * unhooked by the module written to remove the parent's. A detacher
		 * that can detach its own theme is a trap laid for whoever converts
		 * this theme later, which is a documented next step rather than a
		 * hypothetical.
		 *
		 * So: if there is no parent, this whole pass is a no-op.
		 */
		if ( get_template_directory() === get_stylesheet_directory() ) {
			return;
		}

		/*
		 * The js_editor injection.
		 *
		 * Registered as a closure, so it has no name to remove — the hook has
		 * to be walked and the callback matched by where it was declared.
		 * Fragile-looking, and the alternative is worse: leaving a demo's
		 * arbitrary JavaScript running on a live studio site.
		 */
		global $wp_filter;
		if ( isset( $wp_filter['wp_footer']->callbacks[100] ) ) {
			foreach ( $wp_filter['wp_footer']->callbacks[100] as $id => $cb ) {
				if ( ! ( $cb['function'] instanceof Closure ) ) {
					continue;
				}
				$ref  = new ReflectionFunction( $cb['function'] );
				$file = (string) $ref->getFileName();
				if ( false !== strpos( $file, get_template_directory() ) ) {
					unset( $wp_filter['wp_footer']->callbacks[100][ $id ] );
				}
			}
		}

		remove_action( 'wp_head', 'shop_archive_css' );
		remove_action( 'wp_footer', 'zeyna_woo_styles_footer', 5 );
		remove_action( 'wp_footer', 'zeyna_ajax_add_to_cart_redirect' );

		/*
		 * zeyna_wp_nav_menu_objects — a filter that ECHOES markup.
		 *
		 * For any menu item with the right ACF fields set it prints a
		 * <span class="sub--wrap--overlay"> and an Elementor template into
		 * the middle of the menu, and adds parent classes to the item. The
		 * child ships a get_field() shim returning null, so it is inert here
		 * — but on the owner's real site, with ACF and Elementor active and a
		 * demo imported, it fires. A filter that echoes is not something to
		 * leave attached to a menu this theme renders itself.
		 */
		remove_filter( 'wp_nav_menu_objects', 'zeyna_wp_nav_menu_objects', 10 );

		/*
		 * nav_menu_link_attributes at priority 100.
		 *
		 * It does not merge — it ASSIGNS
		 * `$atts['class'] = "pe--styled--object text--anim--inner menu--link"`,
		 * discarding whatever the child put there, on every menu link on the
		 * site. Registered as a closure, so again it has to be matched by the
		 * file it was declared in.
		 */
		global $wp_filter;
		if ( isset( $wp_filter['nav_menu_link_attributes']->callbacks[100] ) ) {
			foreach ( $wp_filter['nav_menu_link_attributes']->callbacks[100] as $id => $cb ) {
				if ( ! ( $cb['function'] instanceof Closure ) ) {
					continue;
				}
				$ref = new ReflectionFunction( $cb['function'] );
				if ( false !== strpos( (string) $ref->getFileName(), get_template_directory() ) ) {
					unset( $wp_filter['nav_menu_link_attributes']->callbacks[100][ $id ] );
				}
			}
		}
	},
	1
);

/**
 * The parent's custom-header <style> block.
 *
 * Zeyna registers `zeyna_header_style` as the `wp-head-callback` for
 * add_theme_support('custom-header'). It prints a <style> element into every
 * page's head, styling `.site-title` and `.site-description` against a
 * Customizer header-text colour the AK design does not use.
 *
 * Removing the theme support outright would also remove the Customizer panel,
 * which is harmless but changes the admin. Replacing just the callback leaves
 * the feature intact and stops the output.
 */
add_action(
	'after_setup_theme',
	function () {
		$support = get_theme_support( 'custom-header' );
		if ( ! $support || ! is_array( $support ) ) {
			return;
		}
		$args = (array) $support[0];
		if ( empty( $args['wp-head-callback'] ) || 'zeyna_header_style' !== $args['wp-head-callback'] ) {
			return;
		}
		$args['wp-head-callback'] = '__return_false';
		add_theme_support( 'custom-header', $args );
	},
	20
);

/**
 * Strip the parent's Redux-derived body classes.
 *
 * A filter rather than a remove_filter, because zeyna_body_classes() also adds
 * `hfeed` and `no-sidebar` — WordPress conventions the site legitimately uses.
 * Removing the whole callback to be rid of the Redux half would take those
 * with it.
 *
 * Priority 99: after the parent's own filter at the default 10.
 */
add_filter(
	'body_class',
	function ( $classes ) {
		$drop = array(
			'page--loader--active',
			'page--transitions--active',
			'body--grained',
			'smooth-scroll',
			'hide--footer',
			'show--footer',
			'layout--switched',
		);
		return array_values(
			array_filter(
				$classes,
				function ( $class ) use ( $drop ) {
					// `loader__*` is a family, not one class.
					return ! in_array( $class, $drop, true ) && 0 !== strpos( $class, 'loader__' );
				}
			)
		);
	},
	99
);

/**
 * WooCommerce: the parent's shop templates, refused.
 *
 * THE DEFECT THIS FIXES. inc/enqueue.php prints an admin notice saying that
 * with WooCommerce active, "WooCommerce pages will render with WooCommerce's
 * own default appearance". That was not true. WooCommerce resolves every
 * template through `wc_get_template()`, which looks in the CHILD theme's
 * `woocommerce/` folder, then the PARENT's, and only then falls back to the
 * plugin's own. Zeyna ships a complete override set — archive-product.php,
 * single-product.php, cart/, checkout/, myaccount/, loop/, notices/ — so a
 * shop would have rendered entirely through parent templates that call
 * removed helpers and expect a stylesheet that is no longer enqueued: not
 * WooCommerce's default appearance, and not the studio's either.
 *
 * This is the strict conditional the exit calls for. Any template path that
 * resolves inside the parent theme directory is rejected in favour of
 * WooCommerce's own default, so a shop is unstyled-but-whole rather than
 * broken. The CHILD's own `woocommerce/` folder is untouched: when AK builds
 * shop templates they take precedence exactly as they should, and this filter
 * never sees them, because $template only differs from $default_path when a
 * theme file was found.
 *
 * The filter costs nothing when WooCommerce is absent — the hook never fires.
 */
add_filter(
	'woocommerce_locate_template',
	function ( $template, $template_name, $template_path ) {
		unset( $template_path );

		$parent = wp_normalize_path( get_template_directory() );
		$child  = wp_normalize_path( get_stylesheet_directory() );

		// A child theme IS its own parent when the theme runs standalone; in
		// that case there is nothing to refuse.
		if ( $parent === $child ) {
			return $template;
		}

		if ( 0 !== strpos( wp_normalize_path( (string) $template ), $parent . '/' ) ) {
			return $template;
		}

		$fallback = WC()->plugin_path() . '/templates/' . ltrim( $template_name, '/' );

		return file_exists( $fallback ) ? $fallback : $template;
	},
	10,
	3
);
