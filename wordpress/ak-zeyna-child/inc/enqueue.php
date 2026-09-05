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
		// No dependency on the parent's `plugins` stylesheet any more. It is
		// dequeued below, and WordPress re-adds a dequeued handle whenever
		// something still declares it as a dependency — so the dependency has
		// to go first or the dequeue silently does nothing.
		wp_enqueue_style(
			'zeyna-parent',
			get_template_directory_uri() . '/style.css',
			array(),
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

/**
 * Drop parent assets this build provably does not use.
 *
 * Step 1 of docs/ZEYNA-EXIT-PLAN.md. Measured before writing: the parent puts
 * 410 KB of JavaScript on every page for two features the child actually uses
 * (Barba and GSAP), plus a stylesheet for plugins that never run and a webfont
 * the design system replaced.
 *
 * Each removal below was checked rather than assumed:
 *
 *  · `plugins` (JS, 278 KB) — `scripts.js` declares only jQuery as a
 *    dependency, and the single library reference it contains is
 *    `new Swiper(...)`, reached solely through
 *    `if (mainQuery.querySelector('.product--archive--gallery'))` — a
 *    WooCommerce product archive. So it is dropped ONLY when WooCommerce is
 *    inactive. With WooCommerce present the branch can fire and the file
 *    stays.
 *  · `plugins` (CSS) — nothing in ak.css uses any selector it defines.
 *  · `general-sans-font` — the design system is Fraunces, Bricolage
 *    Grotesque and Martian Mono. Nothing references General Sans.
 *  · `three` (490 KB) and `dotlottie` (1.8 MB) — Redux-gated in the parent
 *    and never used by the child. They do not load on a site without those
 *    options set; dequeuing means they cannot start loading if one is.
 *
 * Deliberately NOT dropped: `scripts` (carries barba.init), `barba`,
 * `zeyna-gsap`, `gsap-plugins`, `lenis`, and the parent stylesheet. Those are
 * steps 3 to 5, and each needs its replacement built first.
 *
 * Priority 100: after the parent enqueues at 10 and the child at 20.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		$styles = array( 'plugins', 'general-sans-font' );
		$scripts = array( 'three', 'dotlottie' );

		// Swiper lives in plugins.min.js and scripts.js reaches it only on a
		// WooCommerce product archive. No WooCommerce, no possible caller.
		if ( ! class_exists( 'WooCommerce' ) ) {
			$scripts[] = 'plugins';
		}

		foreach ( $styles as $handle ) {
			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}
		foreach ( $scripts as $handle ) {
			wp_dequeue_script( $handle );
			wp_deregister_script( $handle );
		}
	},
	100
);
