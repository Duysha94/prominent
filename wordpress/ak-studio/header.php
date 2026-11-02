<?php
/**
 * Site header: the AK logo, the main menu and the header button — all three
 * set in the dashboard (Site Identity, Appearance → Menus).
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#0d0c0b">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main"><?php esc_html_e( 'Skip to content', 'ak-studio' ); ?></a>
<div class="grain" aria-hidden="true"></div>
<div class="cursor" aria-hidden="true"><span class="cursor__dot"></span><span class="cursor__ring"><span class="cursor__label"></span></span></div>

<header class="site-head" data-head>
	<?php ak_studio_logo(); ?>
	<nav class="nav" id="menu" aria-label="<?php esc_attr_e( 'Main menu', 'ak-studio' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav__list',
				'fallback_cb'    => 'ak_studio_nav_fallback',
				'depth'          => 1,
			)
		);
		$email = ak_studio_get( 'email' );
		?>
		<div class="nav__extra">
			<?php if ( $email ) : ?><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a><?php endif; ?>
			<span><?php echo esc_html( ak_studio_get( 'city' ) ); ?></span>
		</div>
	</nav>
	<div class="site-head__end">
		<?php ak_studio_header_button(); ?>
		<button class="menu-btn" type="button" aria-expanded="false" aria-controls="menu" data-close="<?php esc_attr_e( 'Close', 'ak-studio' ); ?>"><?php esc_html_e( 'Menu', 'ak-studio' ); ?></button>
	</div>
</header>
