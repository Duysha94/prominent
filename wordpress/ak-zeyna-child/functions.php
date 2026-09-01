<?php
/**
 * AK Brand Development Studio — child theme for Zeyna.
 *
 * Keeps Zeyna's header, footer, menu and Barba page transitions, and layers
 * the studio's design system and motion on top.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AK_CHILD_VERSION', '1.0.0' );

/**
 * ACF shim.
 *
 * Zeyna's menu filter (zeyna_wp_nav_menu_objects) calls ACF's get_field()
 * for every menu item with no function_exists guard, so on an install
 * without ACF the whole front end fatals the moment a menu is assigned to
 * `menu-1`. Child functions.php loads after plugins and before the parent's
 * functions.php, so a null-returning stub here lets every such call degrade
 * gracefully — and steps aside whenever the real ACF is active.
 */
if ( ! function_exists( 'get_field' ) ) {
	function get_field( $selector, $post_id = false, $format_value = true ) {
		return null;
	}
}

/**
 * The studio's public email address, in ONE place.
 *
 * Swap the address here and every template, the JSON-LD graph and the
 * contact-page fallback follow. The Contact Form 7 form keeps its own copy
 * in the database — change that one in the form's Mail tab.
 */
function ak_studio_email() {
	return apply_filters( 'ak_studio_email', 'hello@akbrand.studio' );
}

require_once get_stylesheet_directory() . '/inc/enqueue.php';
require_once get_stylesheet_directory() . '/inc/seo.php';
require_once get_stylesheet_directory() . '/inc/theme-mode.php';
require_once get_stylesheet_directory() . '/inc/schema.php';
require_once get_stylesheet_directory() . '/inc/setup.php';
require_once get_stylesheet_directory() . '/inc/case-meta.php';

/**
 * Mark the document so the design system can scope itself without competing
 * with Zeyna on specificity.
 */
add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'ak';
		return $classes;
	}
);

/**
 * A polite live region for announcing the new page title after a Barba
 * navigation, plus the Seam.
 *
 * Both are printed in the footer, which Zeyna renders OUTSIDE the Barba
 * container (get_footer() is called after </main>), so they persist across
 * navigations instead of being torn down and rebuilt.
 */
add_action(
	'wp_footer',
	function () {
		get_template_part( 'template-parts/ak-grain' );
		get_template_part( 'template-parts/ak-seam' );
		echo '<div id="ak-route-announcer" class="screen-reader-text" role="status" aria-live="polite"></div>';
	},
	5
);
