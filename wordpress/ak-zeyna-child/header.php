<?php
/**
 * The header — taken over from Zeyna.
 *
 * The parent's header.php is replaced rather than filtered for three
 * reasons, each of which was previously impossible from the outside:
 *
 *  1. THE LOADER. Zeyna's loader is printed by `zeyna_page_loader()` and
 *     configured entirely through Redux; its JavaScript is what removes
 *     `ak-booting` from <html>. Disable it in Redux and the class is never
 *     removed — which is why the header used to render invisible. The child
 *     now prints its own loader and clears the class itself, so the boot
 *     sequence belongs to this theme and cannot be changed by a demo import.
 *  2. THE LOGO. WordPress's custom-logo control holds one image. The studio
 *     has two — dark artwork for ATELIER, light artwork for RUNWAY — so both
 *     are printed and CSS shows whichever matches the current mode.
 *  3. THE MODE SWITCH on small screens. Appending it to the menu puts it
 *     inside the mobile panel, where it cannot be reached without opening
 *     the panel first. Here it sits in the header bar at every width.
 *
 * Everything else — Barba's wrapper attribute, the popups, the cursor, the
 * grid background, the skip link — is called exactly as the parent calls it.
 *
 * @package ak-zeyna-child
 */

?>
<!doctype html>
<html class="ak-booting" <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
	<noscript>
		<style>
		/*
		 * With JavaScript off, the loader is a full-screen overlay that
		 * nothing will ever dismiss, and `html.ak-booting body` locks
		 * scrolling behind it. There is a 3-second CSS failsafe, but three
		 * seconds of a frozen page that also swallows every click is not a
		 * degraded experience — it is a broken one, and it is invisible to
		 * anyone testing with JavaScript on.
		 *
		 * A first-paint loader is a JavaScript enhancement. Without
		 * JavaScript there is nothing to wait for, so there is no loader.
		 */
		.ak-loader { display: none !important; }
		html.ak-booting body { overflow: visible !important; animation: none !important; }
		/* The reveal animations are driven by scroll timelines and JS; with
		   neither, anything that starts at opacity 0 would never arrive. */
		.ak-rise, .ak-vf, [data-ak-cut] { opacity: 1 !important; transform: none !important; animation: none !important; }
		.ak-transition { display: none !important; }
		</style>
	</noscript>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<?php
	/*
	 * A `<span hidden class="layout--colors">` sat here. It was a probe, not
	 * content: Zeyna's scripts.js read its computed styles to discover the
	 * palette Redux had printed. Nothing reads it now — the parent script is
	 * dequeued, and the AK palette is declared in ak.css and switched by
	 * data-theme on <html> — so an empty hidden element with a parent theme's
	 * class name is exactly the "parent-generated hidden element" this exit
	 * was meant to remove.
	 */
	?>

	<div id="page" class="site">

		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'ak-zeyna-child' ); ?></a>

		<?php
		/*
		 * The parent's transition element is gone.
		 *
		 * zeyna_page_transitions() printed `.page--transitions`, built
		 * entirely from Redux keys, and the parent's scripts.js called
		 * barba.init() only when it found that element. The child forced the
		 * Redux keys to something survivable and left the element standing so
		 * navigation stayed soft.
		 *
		 * AK owns the transition now (assets/js/ak-nav.js), so the element
		 * below is ours: one sheet of the studio's own paper or ink, coloured
		 * from data-theme, animated by CSS, and inert when JavaScript is
		 * absent. Nothing about navigation depends on it.
		 */
		?>
		<div class="ak-transition" aria-hidden="true"></div>
		<?php ak_page_loader(); ?>

		<div class="ak-chrome">

			<header id="masthead" class="site-header ak-chrome__bar">

				<div class="ak-chrome__side">
					<div class="site-branding">
						<?php ak_logo(); ?>
					</div><!-- .site-branding -->
				</div>

				<div class="ak-chrome__side ak-chrome__side--end">

					<?php
					// The switch lives in the header bar itself, so it is one
					// tap away on a phone instead of hidden behind the menu.
					ak_mode_toggle( 'ak-mode--header' );
					?>

					<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary', 'ak-zeyna-child' ); ?>">
						<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Menu', 'ak-zeyna-child' ); ?></button>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
								'fallback_cb'    => 'ak_menu_fallback',
							)
						);
						?>
					</nav><!-- #site-navigation -->

				</div>

			</header><!-- #masthead -->

		</div>
