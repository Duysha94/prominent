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

/**
 * One source of truth for the version.
 *
 * It was a hard-coded second copy, and it drifted: the stylesheet said 1.2.0
 * while this said 1.0.0. That silently broke the two things keyed to it —
 * asset cache-busting, and the content sync that fires when the installed
 * version changes. Read it from the stylesheet header so it cannot drift again.
 */
define( 'AK_CHILD_VERSION', wp_get_theme( 'ak-zeyna-child' )->get( 'Version' ) ?: '1.0.0' );

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
 * A live preview of an external site's front page, as an image.
 *
 * Uses WordPress.com's mShots service: it screenshots the URL, caches the
 * capture on their CDN and re-captures periodically — so these cards always
 * show the platform's CURRENT front page and update themselves when the
 * site changes, at zero cost to this site's own performance (one lazy
 * image per card, no scripts).
 */
function ak_live_preview( $url, $title ) {
	$shot = 'https://s0.wp.com/mshots/v1/' . rawurlencode( $url ) . '?w=760';
	printf(
		'<img loading="lazy" decoding="async" referrerpolicy="no-referrer" width="760" height="570" src="%s" alt="%s" />',
		esc_url( $shot ),
		/* translators: %s: platform name */
		esc_attr( sprintf( __( '%s — live front page', 'ak-zeyna-child' ), $title ) )
	);
}

require_once get_stylesheet_directory() . '/inc/studio.php';
require_once get_stylesheet_directory() . '/inc/chrome.php';
require_once get_stylesheet_directory() . '/inc/enqueue.php';
require_once get_stylesheet_directory() . '/inc/seo.php';
require_once get_stylesheet_directory() . '/inc/updates.php';
// The deployment system. Order matters: the registry defines the ownership
// markers the manifest keys on, and the engine consumes both.
require_once get_stylesheet_directory() . '/inc/deployment/registry.php';
require_once get_stylesheet_directory() . '/inc/deployment/scope.php';
require_once get_stylesheet_directory() . '/inc/deployment/manifest.php';
require_once get_stylesheet_directory() . '/inc/deployment/migrations.php';
require_once get_stylesheet_directory() . '/inc/deployment/deploy.php';
require_once get_stylesheet_directory() . '/inc/deployment/notice.php';
require_once get_stylesheet_directory() . '/inc/content.php';
require_once get_stylesheet_directory() . '/inc/theme-mode.php';
require_once get_stylesheet_directory() . '/inc/schema.php';
require_once get_stylesheet_directory() . '/inc/setup.php';
require_once get_stylesheet_directory() . '/inc/case-meta.php';

/**
 * Mark the document so the design system can scope itself without competing
 * with Zeyna on specificity.
 */
/**
 * Translations.
 *
 * Registered on `after_setup_theme`, which is before `init` and therefore
 * before any translation call runs — so WordPress 6.7's
 * `_load_textdomain_just_in_time` notice cannot fire. The site ships
 * English only; this exists so adding a language is a translation job
 * rather than a development job.
 */
add_action(
	'after_setup_theme',
	function () {
		load_child_theme_textdomain( 'ak-zeyna-child', get_stylesheet_directory() . '/languages' );
	}
);

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
