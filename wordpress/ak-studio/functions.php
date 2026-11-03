<?php
/**
 * AK Studio theme bootstrap.
 *
 * Presentation only. Every page is built from blocks in the page editor and
 * every fact, project and form comes from WordPress and the AK Brand Core
 * plugin, so switching or updating the theme moves no content.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

define( 'AK_STUDIO_VERSION', '2.3.0' );

require_once get_theme_file_path( 'inc/data.php' );
require_once get_theme_file_path( 'inc/setup.php' );
require_once get_theme_file_path( 'inc/assets.php' );
require_once get_theme_file_path( 'inc/template-tags.php' );
