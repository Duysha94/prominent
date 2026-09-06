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
 * only on a genuine first load — assets/js/ak-nav.js handles every later
 * navigation — and `assets/js/ak.js` dismisses it and clears `ak-booting` on the
 * window load event, with a hard timeout so a stalled asset can never trap
 * a visitor behind it.
 *
 * Disable it entirely with: add_filter( 'ak_page_loader', '__return_false' );
 */
function ak_page_loader() {
	if ( ! apply_filters( 'ak_page_loader', true ) ) {
		// Nothing to dismiss, so release the document immediately.
		echo '<script>document.documentElement.classList.remove("ak-booting");</script>';
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
 * Resolve a logo setting to a printable image.
 *
 * The Customizer stores a URL. Turning it back into an attachment buys the
 * intrinsic width and height, which is what keeps the header from jumping
 * while the image loads — a logo is above the fold on every single page, so
 * this is the theme's most visible layout shift if it is skipped.
 *
 * @param string $url Stored image URL.
 * @return array{url:string,w:int,h:int}|null
 */
function ak_logo_image( $url ) {
	if ( ! $url ) {
		return null;
	}
	$out = array(
		'url' => $url,
		'w'   => 0,
		'h'   => 0,
	);
	$id = attachment_url_to_postid( $url );
	if ( $id ) {
		$meta = wp_get_attachment_metadata( $id );
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
			$out['w'] = (int) $meta['width'];
			$out['h'] = (int) $meta['height'];
		}
	}
	return $out;
}

/**
 * The studio wordmark, in whichever artwork the current mode calls for.
 *
 * The studio holds two files: dark artwork that reads on paper, light
 * artwork that reads on ink. WordPress's custom-logo control holds one, so
 * both are printed and CSS shows whichever matches `data-theme` — no
 * flash, no JavaScript, and correct on a soft navigation, where a
 * script-swapped `src` would have been lost.
 *
 * Falling back matters as much as switching. The most common way to arrive
 * here is with only the standard WordPress logo set — the theme's own two
 * fields are one screen further in and easy to miss. In that case the one
 * image is used for BOTH modes rather than the header going blank in one of
 * them, which is what the previous version did whenever only one of the
 * two AK fields was filled.
 */
function ak_logo() {
	$home = home_url( '/' );
	$name = get_bloginfo( 'name', 'display' );

	// The WordPress custom logo is the floor both modes stand on.
	$core = '';
	if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
		$core_id = get_theme_mod( 'custom_logo' );
		$src     = $core_id ? wp_get_attachment_image_src( $core_id, 'full' ) : false;
		$core    = $src ? $src[0] : '';
	}

	$light = ak_logo_image( get_theme_mod( 'ak_logo_light', '' ) ?: $core );
	$dark  = ak_logo_image( get_theme_mod( 'ak_logo_dark', '' ) ?: $core );

	if ( ! $light && ! $dark ) {
		/*
		 * A <p>, not a heading.
		 *
		 * This was an <h5>, inherited from Zeyna's own header. It made the
		 * FIRST heading on every page of the site an h5, so the document
		 * outline opened at level five and then jumped to the page's h1 — a
		 * heading-order failure on every route at once, and the thing a
		 * screen-reader user hits before anything else. The wordmark is a
		 * link to the front page, not a section heading; only the page's own
		 * title is.
		 */
		printf(
			'<p class="site-title"><a href="%s" rel="home">%s</a></p>',
			esc_url( $home ),
			esc_html( $name )
		);
		return;
	}

	// One file for both modes: print it once, with no swap classes, so no
	// rule can hide it. Two files: print both and let CSS choose.
	$single = ! $light || ! $dark || $light['url'] === $dark['url'];

	printf( '<a class="ak-logo" href="%s" rel="home" aria-label="%s">', esc_url( $home ), esc_attr( $name ) );

	if ( $single ) {
		ak_logo_img( $light ? $light : $dark, '', $name );
	} else {
		ak_logo_img( $light, ' ak-logo__img--light', $name );
		ak_logo_img( $dark, ' ak-logo__img--dark', '' );
	}

	echo '</a>';
}

/**
 * Print one logo image.
 *
 * @param array  $img   Image array from ak_logo_image().
 * @param string $class Extra class, with its leading space.
 * @param string $alt   Alt text; empty marks the image decorative, which is
 *                      correct for the second copy of the same wordmark.
 */
function ak_logo_img( $img, $class, $alt ) {
	printf(
		'<img class="ak-logo__img%1$s" src="%2$s" alt="%3$s"%4$s%5$s decoding="async" fetchpriority="high"%6$s />',
		esc_attr( $class ),
		esc_url( $img['url'] ),
		esc_attr( $alt ),
		$img['w'] ? ' width="' . (int) $img['w'] . '"' : '',
		$img['h'] ? ' height="' . (int) $img['h'] . '"' : '',
		'' === $alt ? ' aria-hidden="true"' : ''
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
			$url = get_post_type_archive_link( AK_PROJECT_CPT );
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
