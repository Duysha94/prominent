<?php
/**
 * AK Studio theme bootstrap.
 *
 * Presentation only: every fact, page and project comes from WordPress and
 * the AK Brand Core plugin, so switching or updating the theme moves nothing.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

define( 'AK_STUDIO_VERSION', '1.0.0' );

require_once get_theme_file_path( 'inc/data.php' );
require_once get_theme_file_path( 'inc/setup.php' );
require_once get_theme_file_path( 'inc/assets.php' );
require_once get_theme_file_path( 'inc/template-tags.php' );
require_once get_theme_file_path( 'inc/customizer.php' );
