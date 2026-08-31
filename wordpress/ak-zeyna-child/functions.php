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

require_once get_stylesheet_directory() . '/inc/enqueue.php';
require_once get_stylesheet_directory() . '/inc/theme-mode.php';
require_once get_stylesheet_directory() . '/inc/schema.php';

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
		get_template_part( 'template-parts/ak-seam' );
		echo '<div id="ak-route-announcer" class="screen-reader-text" role="status" aria-live="polite"></div>';
	},
	5
);
