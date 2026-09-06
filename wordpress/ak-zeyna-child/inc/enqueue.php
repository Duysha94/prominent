<?php
/**
 * Assets — the AK theme's own, and the parent's, dropped.
 *
 * THE DISTINCTION THIS FILE IS BUILT ON
 * ------------------------------------
 * A LIBRARY dependency and a PARENT-THEME dependency are different things.
 * The child needs GSAP. That is not a reason to keep Zeyna's 133 KB
 * `scripts.js`, its Redux-configured Barba setup, its Lenis instance, or its
 * 197 KB plugin bundle carrying Flip, Observer, Draggable, MotionPath,
 * ScrollTo, TextPlugin and EasePack — none of which this theme calls.
 *
 * So the child now ships GSAP itself, from GreenSock's own npm distribution
 * (see assets/vendor/README.md), and every parent frontend asset is dropped.
 *
 * WHAT THE CHILD ACTUALLY CALLS, measured rather than assumed:
 *   gsap.quickTo, gsap.set, gsap.timeline, gsap.context, gsap.ticker,
 *   gsap.registerEase   → gsap core
 *   ScrollTrigger.refresh                        → ScrollTrigger
 *   SplitText                                    → SplitText
 * Three files, 123 KB, against the parent's 269 KB of GSAP alone.
 *
 * Every use is guarded with `if (!window.gsap) return`, so a library that
 * fails to load costs the animation and nothing else.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$dir = get_stylesheet_directory_uri();

		/* ── Styles ───────────────────────────────────────────────────────
		 * The parent stylesheet is gone. What the child genuinely inherited
		 * from it — WordPress's generated content classes, form controls,
		 * caption and alignment styling — is now in ak-wordpress.css, which
		 * is 3 KB rather than 98 KB and contains no Zeyna visual language at
		 * all. See that file for what was reimplemented and why.
		 */
		wp_enqueue_style(
			'ak-wordpress',
			$dir . '/assets/css/ak-wordpress.css',
			array(),
			AK_CHILD_VERSION
		);
		wp_enqueue_style(
			'ak-design-system',
			$dir . '/assets/css/ak.css',
			array( 'ak-wordpress' ),
			AK_CHILD_VERSION
		);
		wp_enqueue_style(
			'ak-system',
			$dir . '/assets/css/ak-system.css',
			array( 'ak-design-system' ),
			AK_CHILD_VERSION
		);

		/* ── Navigation ───────────────────────────────────────────────────
		 * No dependencies at all: fetch, History and DOMParser only. It must
		 * keep working when every other script on the page fails, and every
		 * link on the site works without it.
		 */
		wp_enqueue_script( 'ak-nav', $dir . '/assets/js/ak-nav.js', array(), AK_CHILD_VERSION, true );

		/* ── Motion libraries ─────────────────────────────────────────────
		 * Vendored from npm `gsap@3.13.0`, not copied out of the parent
		 * theme: Zeyna bundles its own under the licence that came with
		 * Zeyna, and carrying those forward into a theme that no longer
		 * depends on Zeyna would be redistributing someone else's files.
		 */
		wp_enqueue_script( 'ak-gsap', $dir . '/assets/vendor/gsap.min.js', array(), '3.13.0', true );
		wp_enqueue_script( 'ak-scrolltrigger', $dir . '/assets/vendor/ScrollTrigger.min.js', array( 'ak-gsap' ), '3.13.0', true );
		wp_enqueue_script( 'ak-splittext', $dir . '/assets/vendor/SplitText.min.js', array( 'ak-gsap' ), '3.13.0', true );

		wp_enqueue_script(
			'ak-motion',
			$dir . '/assets/js/ak.js',
			array( 'ak-gsap', 'ak-scrolltrigger', 'ak-splittext' ),
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
 * The parent theme's frontend, removed.
 *
 * This is the last step of docs/ZEYNA-EXIT-PLAN.md, and it only became safe
 * once the child owned the transition system (assets/js/ak-nav.js), its own
 * GSAP (assets/vendor/), its header markup (header.php no longer emits the
 * parent's `.pe-*` grid classes) and the WordPress content styles the parent
 * stylesheet had been quietly supplying (assets/css/ak-wordpress.css).
 *
 * Handles are DEREGISTERED as well as dequeued: WordPress re-adds a dequeued
 * handle the moment anything still declares it as a dependency, so dequeuing
 * alone is a removal that silently undoes itself.
 *
 * WooCommerce is the one conditional. The parent's product-gallery runtime
 * lives in `plugins.min.js` and `scripts.js` reaches it only through
 * `if (mainQuery.querySelector('.product--archive--gallery'))`. With
 * WooCommerce inactive there is no possible caller, so both go. If
 * WooCommerce is later activated on this site, the parent bundle is NOT
 * reintroduced — see ak_zeyna_woo_notice() below, which says so out loud
 * rather than letting a shop appear half-styled with no explanation.
 *
 * Priority 100: after the parent enqueues at 10 and the child at 20.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		$styles = array(
			'plugins',            // parent plugin CSS — no selector in ak.css uses it
			'general-sans-font',  // the design system is Fraunces / Bricolage / Martian Mono
			'style',              // the parent stylesheet, 98 KB
			'zeyna-parent',       // the child's own former handle for it
			'style-rtl',          // a SECOND full copy of the 98 KB parent
			                      // stylesheet, enqueued when is_rtl(). Dormant
			                      // on an English site and a door left open:
			                      // adding a right-to-left language would have
			                      // reintroduced the whole parent stylesheet.
			'woo-rtl-styles',
			'woo-styles',         // only meaningful with WooCommerce, and then not ours
			'woocommerce-blocks',
		);

		$scripts = array(
			'scripts',       // 133 KB: barba.init, Lenis, the parent's whole runtime
			'barba',         // replaced by assets/js/ak-nav.js
			'lenis',         // see the note on smooth scrolling below
			'zeyna-gsap',    // replaced by assets/vendor/gsap.min.js
			'gsap-plugins',  // 197 KB of plugins this theme does not call
			'three',         // 490 KB, Redux-gated, never used here
			'dotlottie',     // 1.8 MB, likewise
			'navigation',    // the parent's menu script, never enqueued by it anyway
			'plugins',       // Swiper et al
			'wishlist',
			'compare',
		);

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

/**
 * If WooCommerce is ever activated here, say so rather than half-working.
 *
 * The agency site should not load the parent theme's WooCommerce frontend
 * bundle merely because the plugin happens to be installed — that would
 * reintroduce `plugins.min.js`, `scripts.js` and 180 KB of parent shop CSS
 * through the back door, which is precisely what this exit removed.
 *
 * WooCommerce's own templates and styles still work; what is missing is
 * Zeyna's styling of them. An admin notice is the honest answer: a shop on
 * this site is a design decision that needs AK templates, not a silent
 * fallback to the theme the studio just left.
 */
add_action(
	'admin_notices',
	function () {
		if ( ! class_exists( 'WooCommerce' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		printf(
			'<div class="notice notice-warning"><p><strong>%s</strong> %s</p></div>',
			esc_html__( 'WooCommerce is active on the AK studio site.', 'ak-zeyna-child' ),
			esc_html__( 'The theme deliberately does not load the parent theme’s shop styling, so WooCommerce pages will render with WooCommerce’s own default appearance. AK shop templates need to be built before selling from this site.', 'ak-zeyna-child' )
		);
	}
);
