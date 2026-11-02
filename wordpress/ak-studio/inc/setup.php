<?php
/**
 * Theme supports and menus.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		register_nav_menus(
			array(
				'primary' => __( 'Primary', 'ak-studio' ),
				'footer'  => __( 'Footer', 'ak-studio' ),
			)
		);
	}
);

/**
 * Until a menu is assigned, the bar links to the sections of the front page.
 */
function ak_studio_nav_fallback(): void {
	$home  = home_url( '/' );
	$items = array(
		'#work'     => __( 'Work', 'ak-studio' ),
		'#services' => __( 'Services', 'ak-studio' ),
		'#studio'   => __( 'Studio', 'ak-studio' ),
		'#contact'  => __( 'Contact', 'ak-studio' ),
	);
	echo '<ul class="nav__list">';
	foreach ( $items as $hash => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( is_front_page() ? $hash : $home . $hash ), esc_html( $label ) );
	}
	echo '</ul>';
}
