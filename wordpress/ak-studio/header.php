<?php
/**
 * Site header.
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
	<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<span class="brand__mark"><?php echo esc_html( ak_studio_get( 'short' ) ); ?></span>
		<span class="brand__name"><?php esc_html_e( 'Brand Development', 'ak-studio' ); ?><br><?php esc_html_e( 'Studio', 'ak-studio' ); ?></span>
	</a>
	<nav class="nav" id="menu" aria-label="<?php esc_attr_e( 'Primary', 'ak-studio' ); ?>">
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
		?>
	</nav>
	<div class="site-head__end">
		<a class="btn btn--accent btn--sm" href="<?php echo esc_url( is_front_page() ? '#contact' : home_url( '/#contact' ) ); ?>" data-magnetic>
			<?php esc_html_e( 'Start a project', 'ak-studio' ); ?>
			<svg class="btn__arrow" viewBox="0 0 16 16" aria-hidden="true"><path d="M4 12 12 4M5.5 4H12v6.5" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
		</a>
		<button class="menu-btn" type="button" aria-expanded="false" aria-controls="menu"><?php esc_html_e( 'Menu', 'ak-studio' ); ?></button>
	</div>
</header>
