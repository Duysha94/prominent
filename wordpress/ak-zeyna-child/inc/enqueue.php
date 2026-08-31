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
		// The parent stylesheet is enqueued by Zeyna under the handle `style`.
		// Depending on it guarantees ours loads after, so the design system
		// wins on equal specificity without a single !important.
		wp_enqueue_style(
			'ak-design-system',
			get_stylesheet_directory_uri() . '/assets/css/ak.css',
			array( 'style' ),
			AK_CHILD_VERSION
		);

		wp_enqueue_script(
			'ak-motion',
			get_stylesheet_directory_uri() . '/assets/js/ak.js',
			array( 'zeyna-gsap', 'gsap-plugins' ),
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
