<?php
/**
 * The header — taken over from Zeyna.
 *
 * The parent's header.php is replaced rather than filtered for three
 * reasons, each of which was previously impossible from the outside:
 *
 *  1. THE LOADER. Zeyna's loader is printed by `zeyna_page_loader()` and
 *     configured entirely through Redux; its JavaScript is what removes
 *     `first--load` from <html>. Disable it in Redux and the class is never
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
<html class="first--load ajax--first" <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( true ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php wp_body_open(); ?>
	<span hidden class="layout--colors"></span>

	<div id="page" class="site">

		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'ak-zeyna-child' ); ?></a>

		<?php
		// Step 2 of docs/ZEYNA-EXIT-PLAN.md: the dead calls are gone.
		// zeyna_mouse_cursor() drew a custom cursor the motion system bans,
		// zeyna_grid_layout_bg() a decorative grid from a demo option, and
		// zeyna_popups() renders nothing at all unless Redux popups are
		// configured. None contributed anything to this build.
		//
		// zeyna_page_transitions() STAYS. The parent calls barba.init() only
		// when `.page--transitions` is in the document, so removing it turns
		// every soft navigation into a full page load. It goes at step 3,
		// once the child prints its own transition element.
		if ( function_exists( 'zeyna_page_transitions' ) ) {
			zeyna_page_transitions();
		}

		ak_page_loader();
		?>

		<div class="pe-section header--default">

			<header id="masthead" class="site-header pe-wrapper pe-items-center">

				<div class="pe-col-6">
					<div class="site-branding">
						<?php ak_logo(); ?>
					</div><!-- .site-branding -->
				</div>

				<div class="pe-col-6 pe-items-right">

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
