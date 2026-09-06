<?php
/**
 * AK Brand Development Studio — the studio's theme.
 *
 * Installed as a child of Zeyna, and no longer built on it. Zeyna's header,
 * footer, menu, page transitions, stylesheet, JavaScript and Redux
 * configuration have each been taken over by this theme; nothing in the
 * parent renders, executes or is read at runtime. docs/ZEYNA-EXIT-PLAN.md
 * records the full inventory, what replaced each item, and the one reason
 * `Template: zeyna` is still in style.css.
 *
 * inc/zeyna-exit.php is the only file that still knows the parent exists,
 * and its entire job is to detach what the parent registers on hooks that
 * fire regardless of which templates this theme overrides.
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
 * ak_live_preview() used to live here.
 *
 * It printed the capture service's URL straight into the page. When the
 * service has not yet produced a capture it answers with a grey placeholder,
 * at HTTP 200 with an image content type — so the homepage rendered a row of
 * grey plates, which is capture failure displayed as portfolio work.
 *
 * Captures now go through inc/projects/preview.php, which verifies that a
 * real capture exists before anything is shown, and through
 * ak_project_cover(), which returns media only when there is verified media.
 * A project with none renders as a typographic entry rather than a broken
 * frame. The automated capture is an enhancement; it is never a dependency
 * that stops a project being published.
 */

require_once get_stylesheet_directory() . '/inc/studio.php';
require_once get_stylesheet_directory() . '/inc/chrome.php';
require_once get_stylesheet_directory() . '/inc/enqueue.php';
require_once get_stylesheet_directory() . '/inc/zeyna-exit.php';
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
/*
 * The AK Core Project model. Order is the dependency order: the factual
 * capability layer, then the post type and taxonomies that carry it, then the
 * meta, then the things that read all three.
 */
require_once get_stylesheet_directory() . '/inc/projects/capabilities.php';
require_once get_stylesheet_directory() . '/inc/projects/model.php';
require_once get_stylesheet_directory() . '/inc/projects/meta.php';
require_once get_stylesheet_directory() . '/inc/projects/ownership.php';
require_once get_stylesheet_directory() . '/inc/projects/preview.php';
require_once get_stylesheet_directory() . '/inc/projects/query.php';
require_once get_stylesheet_directory() . '/inc/projects/modules.php';
require_once get_stylesheet_directory() . '/inc/projects/seed.php';
if ( is_admin() ) {
	require_once get_stylesheet_directory() . '/inc/projects/admin.php';
}

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
 * A polite live region for announcing the new page title after a soft
 * navigation, plus the Seam.
 *
 * Both are printed in the footer, which is OUTSIDE `[data-ak-container]`
 * (get_footer() is called after </main>), so they persist across navigations
 * instead of being torn down and rebuilt — and the announcer in particular
 * must persist, since an element replaced in the same tick as the text it
 * announces is not announced at all.
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
