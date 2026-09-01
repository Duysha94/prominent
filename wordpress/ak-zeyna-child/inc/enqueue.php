<?php
/**
 * Assets.
 *
 * Zeyna already enqueues GSAP (handle `zeyna-gsap`), its plugin bundle
 * (`gsap-plugins`, which contains SplitText, ScrollTrigger, Flip and Observer),
 * Lenis and Barba. Shipping our own copies would mean two GSAP instances
 * fighting over the same ticker and roughly 130KB of duplicate JavaScript, so
 * this file declares dependencies on the parent's handles instead.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		// Zeyna enqueues its stylesheet as wp_enqueue_style( 'style',
		// get_stylesheet_uri() ) — and under a child theme that URI resolves
		// to THIS theme's header-only style.css, so the parent's actual CSS
		// (all of the header/menu/footer chrome) would never load. Enqueue it
		// explicitly, after the parent's plugins.css, exactly as Zeyna orders
		// it for itself.
		wp_enqueue_style(
			'zeyna-parent',
			get_template_directory_uri() . '/style.css',
			array( 'plugins' ),
			'1.5.0'
		);

		// Loading after both the parent CSS and the child header stylesheet
		// means the design system wins on equal specificity without a single
		// !important.
		wp_enqueue_style(
			'ak-design-system',
			get_stylesheet_directory_uri() . '/assets/css/ak.css',
			array( 'zeyna-parent', 'style' ),
			AK_CHILD_VERSION
		);

		// Depend only on parent handles that actually exist: if a Zeyna
		// update renames one, WordPress would otherwise drop the script
		// silently. This way ak.js still loads and its own feature guards
		// degrade gracefully instead.
		$deps = array_values(
			array_filter(
				array( 'zeyna-gsap', 'gsap-plugins' ),
				function ( $handle ) {
					return wp_script_is( $handle, 'registered' ) || wp_script_is( $handle, 'enqueued' );
				}
			)
		);

		wp_enqueue_script(
			'ak-motion',
			get_stylesheet_directory_uri() . '/assets/js/ak.js',
			$deps,
			AK_CHILD_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	},
	20
);

/**
 * Preload the one face the largest headline is set in.
 *
 * Exactly one: preloading several fonts at highest priority ahead of the CSS
 * makes them compete with the element that actually decides LCP. The other two
 * arrive with the stylesheet behind metric-matched fallbacks, so the swap does
 * not shift layout.
 */
add_action(
	'wp_head',
	function () {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_stylesheet_directory_uri() . '/assets/fonts/fraunces-italic.woff2' )
		);
	},
	1
);
