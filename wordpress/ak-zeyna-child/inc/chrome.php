<?php
/**
 * The chrome the child owns: the loader, the logo, the menu fallback.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The boot sequence.
 *
 * The studio's own loader, not Zeyna's: the seam draws itself down the
 * screen while the monogram sets, and the panel lifts away. It is printed
 * only on a genuine first load — Barba handles every later navigation —
 * and `assets/js/ak.js` dismisses it and clears `first--load` on the
 * window load event, with a hard timeout so a stalled asset can never trap
 * a visitor behind it.
 *
 * Disable it entirely with: add_filter( 'ak_page_loader', '__return_false' );
 */
function ak_page_loader() {
	if ( ! apply_filters( 'ak_page_loader', true ) ) {
		// Nothing to dismiss, so release the document immediately.
		echo '<script>document.documentElement.classList.remove("first--load");</script>';
		return;
	}
	?>
	<div class="ak-loader" data-ak-loader role="status" aria-live="polite">
		<span class="screen-reader-text"><?php esc_html_e( 'Loading', 'ak-zeyna-child' ); ?></span>
		<span class="ak-loader__thread" aria-hidden="true"></span>
		<span class="ak-loader__mark" aria-hidden="true">
			<b class="ak-loader__a">A</b><b class="ak-loader__k">K</b>
		</span>
		<span class="ak-loader__caption mono" aria-hidden="true"><?php esc_html_e( 'Fashion & Brand Advisory — London', 'ak-zeyna-child' ); ?></span>
	</div>
	<?php
}

/**
 * The logotype, in both modes.
 *
 * WordPress's own custom-logo control carries a single image, so the studio's
 * two-artwork logotype (dark type for the light room, light type for the
 * dark one) is served from two Customizer settings. Both are printed; CSS
 * shows the one matching `data-theme`, which means the swap happens in the
 * same frame as the mode change with no flash and no JavaScript.
 *
 * Falls back to the WordPress custom logo, then to the site title.
 */
function ak_logo() {
	$light = get_theme_mod( 'ak_logo_light', '' );
	$dark  = get_theme_mod( 'ak_logo_dark', '' );
	$home  = home_url( '/' );
	$name  = get_bloginfo( 'name', 'display' );

	if ( $light || $dark ) {
		printf( '<a class="ak-logo" href="%s" rel="home" aria-label="%s">', esc_url( $home ), esc_attr( $name ) );
		if ( $light ) {
			printf(
				'<img class="ak-logo__img ak-logo__img--light" src="%s" alt="%s" decoding="async" />',
				esc_url( $light ),
				esc_attr( $name )
			);
		}
		if ( $dark ) {
			printf(
				'<img class="ak-logo__img ak-logo__img--dark" src="%s" alt="%s" decoding="async" %s />',
				esc_url( $dark ),
				esc_attr( $light ? '' : $name ),
				$light ? 'aria-hidden="true"' : ''
			);
		}
		echo '</a>';
		return;
	}

	if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	printf(
		'<h5 class="site-title"><a href="%s" rel="home">%s</a></h5>',
		esc_url( $home ),
		esc_html( $name )
	);
}

/**
 * If no menu is assigned yet, show the studio's own pages rather than
 * WordPress's alphabetical page dump — which on a fresh install renders
 * every page including the ones the import is about to tidy away.
 */
function ak_menu_fallback() {
	$pages = array(
		'home'     => __( 'Home', 'ak-zeyna-child' ),
		'work'     => __( 'Work', 'ak-zeyna-child' ),
		'services' => __( 'Services', 'ak-zeyna-child' ),
		'journal'  => __( 'Journal', 'ak-zeyna-child' ),
		'about'    => __( 'About', 'ak-zeyna-child' ),
		'contact'  => __( 'Contact', 'ak-zeyna-child' ),
	);

	echo '<ul id="primary-menu" class="menu">';
	foreach ( $pages as $slug => $label ) {
		if ( 'work' === $slug ) {
			$url = get_post_type_archive_link( 'portfolio' );
			$url = $url ? $url : home_url( '/work/' );
		} else {
			$page = get_page_by_path( $slug );
			if ( ! $page ) {
				continue;
			}
			$url = get_permalink( $page );
		}
		printf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}
	echo '</ul>';
}
