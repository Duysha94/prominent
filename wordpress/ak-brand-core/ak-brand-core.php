<?php
/**
 * Plugin Name:       AK Brand Core
 * Plugin URI:        https://akbrand.studio
 * Description:       Everything the AK Brand Development Studio site knows and edits, independent of the theme: the page blocks (hero reel, page header, services, work, event, facts, brief form, journal, moving band), the project model, the project brief form and its inbox, search and sharing data, cookie consent, and the launch content installer.
 * Version:           2.0.1
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            AK Brand Development Studio
 * Author URI:        https://akbrand.studio
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ak-brand-core
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

define( 'AK_BRAND_CORE_VERSION', '2.0.1' );
define( 'AK_BRAND_CORE_FILE', __FILE__ );
define( 'AK_BRAND_CORE_DIR', plugin_dir_path( __FILE__ ) );

require_once AK_BRAND_CORE_DIR . 'includes/studio.php';
require_once AK_BRAND_CORE_DIR . 'includes/practice.php';
require_once AK_BRAND_CORE_DIR . 'includes/content-model.php';
require_once AK_BRAND_CORE_DIR . 'includes/brief.php';
require_once AK_BRAND_CORE_DIR . 'includes/seo.php';
require_once AK_BRAND_CORE_DIR . 'includes/consent.php';
require_once AK_BRAND_CORE_DIR . 'includes/blocks.php';
require_once AK_BRAND_CORE_DIR . 'includes/content/blocks.php';
require_once AK_BRAND_CORE_DIR . 'includes/content/media.php';
require_once AK_BRAND_CORE_DIR . 'includes/content/pages.php';
require_once AK_BRAND_CORE_DIR . 'includes/content/projects.php';
require_once AK_BRAND_CORE_DIR . 'includes/content/posts.php';
require_once AK_BRAND_CORE_DIR . 'includes/content/patterns.php';
require_once AK_BRAND_CORE_DIR . 'includes/installer.php';

/**
 * Rewrite rules for /work/ exist only once the post type is registered,
 * so flush on activation after registering, and on deactivation after removing.
 */
register_activation_hook(
	__FILE__,
	static function () {
		ak_brand_register_content_model();
		ak_brand_register_brief();
		ak_brand_seed_capabilities();
		if ( ! wp_next_scheduled( 'ak_brand_daily' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'ak_brand_daily' );
		}
		flush_rewrite_rules();
	}
);

register_deactivation_hook(
	__FILE__,
	static function () {
		wp_clear_scheduled_hook( 'ak_brand_daily' );
		flush_rewrite_rules();
	}
);

/* Keep the daily housekeeping scheduled even if activation was skipped (e.g. a mu-plugin copy). */
add_action(
	'init',
	static function () {
		if ( ! wp_next_scheduled( 'ak_brand_daily' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'ak_brand_daily' );
		}
	},
	20
);

/* Settings link on the Plugins screen. */
add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	static function ( $links ) {
		array_unshift(
			$links,
			'<a href="' . esc_url( admin_url( 'options-general.php?page=ak-studio' ) ) . '">' . esc_html__( 'Settings', 'ak-brand-core' ) . '</a>',
			'<a href="' . esc_url( admin_url( 'tools.php?page=ak-install' ) ) . '">' . esc_html__( 'Launch content', 'ak-brand-core' ) . '</a>'
		);
		return $links;
	}
);
