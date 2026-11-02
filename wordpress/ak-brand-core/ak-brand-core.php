<?php
/**
 * Plugin Name:       AK Brand Core
 * Plugin URI:        https://akbrand.studio
 * Description:       The studio's data, independent of any theme: studio details, the six practice movements, the platforms the founders built, and the project model behind /work/. Switch the theme and none of it moves.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            AK Brand Development Studio
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ak-brand-core
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

define( 'AK_BRAND_CORE_VERSION', '0.1.0' );
define( 'AK_BRAND_CORE_FILE', __FILE__ );
define( 'AK_BRAND_CORE_DIR', plugin_dir_path( __FILE__ ) );

require_once AK_BRAND_CORE_DIR . 'includes/studio.php';
require_once AK_BRAND_CORE_DIR . 'includes/practice.php';
require_once AK_BRAND_CORE_DIR . 'includes/content-model.php';

/**
 * Rewrite rules for /work/ exist only once the post type is registered,
 * so flush on activation after registering, and on deactivation after removing.
 */
register_activation_hook(
	__FILE__,
	static function () {
		ak_brand_register_content_model();
		ak_brand_seed_capabilities();
		flush_rewrite_rules();
	}
);
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
