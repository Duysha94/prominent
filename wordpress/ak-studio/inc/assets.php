<?php
/**
 * Styles, fonts and the motion stack (GSAP + ScrollTrigger + Lenis, shipped
 * with the theme — no CDN at runtime).
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	static function () {
		$v    = AK_STUDIO_VERSION;
		$vend = array( 'in_footer' => true, 'strategy' => 'defer' );

		wp_enqueue_style( 'ak-studio', get_theme_file_uri( 'assets/css/main.css' ), array(), $v );

		wp_register_script( 'ak-gsap', get_theme_file_uri( 'assets/vendor/gsap.min.js' ), array(), '3.15.0', $vend );
		wp_register_script( 'ak-gsap-st', get_theme_file_uri( 'assets/vendor/ScrollTrigger.min.js' ), array( 'ak-gsap' ), '3.15.0', $vend );
		wp_register_script( 'ak-lenis', get_theme_file_uri( 'assets/vendor/lenis.min.js' ), array(), '1.3.27', $vend );
		wp_enqueue_script( 'ak-studio', get_theme_file_uri( 'assets/js/main.js' ), array( 'ak-gsap', 'ak-gsap-st', 'ak-lenis' ), $v, $vend );

		// Block library styles are replaced by the theme's own prose styles.
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'classic-theme-styles' );
	},
	20
);

/**
 * The display faces are needed for the first paint; the hero poster is the
 * largest image on the front page.
 */
add_action(
	'wp_head',
	static function () {
		foreach ( array( 'archivo-latin-wdth-normal.woff2', 'instrument-serif-latin-400-italic.woff2' ) as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( get_theme_file_uri( 'assets/fonts/' . $font ) )
			);
		}
		if ( is_front_page() ) {
			$reel = ak_studio_reel();
			if ( $reel ) {
				printf( '<link rel="preload" href="%s" as="image" fetchpriority="high">' . "\n", esc_url( $reel[0]['poster'] ) );
			}
		}
	},
	1
);

/**
 * Script is running: entrance styles may hide things until they animate in.
 */
add_action(
	'wp_head',
	static function () {
		echo "<script>document.documentElement.classList.add('js')</script>\n";
	},
	0
);

/* The theme's default share image when a page has none. */
add_filter( 'ak_brand_default_share_image', static fn() => get_theme_file_uri( 'assets/media/lfd-westminster.jpg' ) );
