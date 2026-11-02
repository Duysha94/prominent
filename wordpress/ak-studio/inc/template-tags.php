<?php
/**
 * Small rendering helpers shared by the templates.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Display heading: escaped, *word* becomes the italic serif accent.
 */
function ak_studio_heading( string $text ): string {
	$html = esc_html( $text );
	$html = preg_replace( '/\*(.+?)\*/u', '<em>$1</em>', $html );
	return nl2br( (string) $html, false );
}

function ak_studio_arrow( string $class = 'btn__arrow' ): string {
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M4 12 12 4M5.5 4H12v6.5" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>';
}

/**
 * URL of the contact page, optionally preselecting a need in the brief.
 */
function ak_studio_contact_url( string $need = '' ): string {
	$page = get_page_by_path( 'contact' );
	$url  = $page ? get_permalink( $page ) : home_url( '/' );
	return $need ? add_query_arg( 'need', $need, $url ) : $url;
}

/**
 * A responsive image.
 *
 * @param array<string,string> $attrs Extra attributes.
 */
function ak_studio_img( int $id, string $sizes = '100vw', array $attrs = array(), string $size = 'large' ): string {
	if ( ! $id ) {
		return '';
	}
	$attrs = array_merge( array( 'sizes' => $sizes, 'loading' => 'lazy', 'decoding' => 'async' ), $attrs );
	return wp_get_attachment_image( $id, $size, false, $attrs );
}

/**
 * Whether a post's content opens with its own header block (hero or page header),
 * in which case the template adds no title of its own.
 */
function ak_studio_opens_with_header( int $post_id = 0 ): bool {
	$post_id = $post_id ?: get_the_ID();
	if ( ! $post_id ) {
		return false;
	}
	foreach ( parse_blocks( (string) get_post_field( 'post_content', $post_id ) ) as $block ) {
		if ( empty( $block['blockName'] ) ) {
			continue;
		}
		return in_array( $block['blockName'], array( 'ak/hero', 'ak/page-header' ), true );
	}
	return false;
}

/**
 * A plain page header for views without a header block of their own
 * (journal entries, archives, search, pages started from scratch).
 *
 * @param array<string,string> $args eyebrow, heading (escaped HTML), lede, meta (HTML), extra (HTML), size.
 */
function ak_studio_title_header( array $args = array() ): void {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow' => '',
			'heading' => esc_html( get_the_title() ),
			'lede'    => '',
			'meta'    => '',
			'extra'   => '',
			'size'    => 'compact',
		)
	);
	?>
	<header class="page-head page-head--<?php echo esc_attr( $args['size'] ); ?> ak-full">
		<div class="wrap page-head__inner">
			<?php if ( $args['eyebrow'] ) : ?>
				<p class="eyebrow" data-rise><?php echo wp_kses_post( $args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="display page-head__title" data-split><?php echo wp_kses_post( $args['heading'] ); ?></h1>
			<?php if ( $args['lede'] || $args['meta'] ) : ?>
				<div class="page-head__foot" data-rise>
					<?php if ( $args['lede'] ) : ?><p class="lede"><?php echo esc_html( $args['lede'] ); ?></p><?php endif; ?>
					<?php echo wp_kses_post( $args['meta'] ); ?>
				</div>
			<?php endif; ?>
			<?php echo $args['extra']; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts by the templates. ?>
		</div>
	</header>
	<?php
}

/**
 * A project card (from AK Brand Core, with a plain fallback).
 */
function ak_studio_card( WP_Post $post, string $class = '' ): string {
	if ( function_exists( 'ak_brand_card' ) ) {
		return ak_brand_card( $post, $class );
	}
	return '<article class="card ' . esc_attr( $class ) . '"><a class="card__link" href="' . esc_url( get_permalink( $post ) ) . '"><div class="card__media">' . ak_studio_img( (int) get_post_thumbnail_id( $post ), '(max-width: 900px) 92vw, 46vw', array( 'alt' => '' ) ) . '</div><div class="card__body"><h3 class="card__title">' . esc_html( get_the_title( $post ) ) . '</h3><p class="card__text">' . esc_html( get_the_excerpt( $post ) ) . '</p></div></a></article>';
}

/**
 * A journal card (from AK Brand Core, with a plain fallback).
 */
function ak_studio_post_card( WP_Post $post ): string {
	if ( function_exists( 'ak_brand_post_card' ) ) {
		return ak_brand_post_card( $post );
	}
	return '<article class="post-card"><a class="post-card__link" href="' . esc_url( get_permalink( $post ) ) . '"><div class="post-card__media">' . ak_studio_img( (int) get_post_thumbnail_id( $post ), '(max-width: 900px) 92vw, 30vw', array( 'alt' => '' ), 'medium_large' ) . '</div><p class="post-card__meta">' . esc_html( get_the_date( 'j M Y', $post ) ) . '</p><h3 class="post-card__title">' . esc_html( get_the_title( $post ) ) . '</h3><p class="post-card__text">' . esc_html( get_the_excerpt( $post ) ) . '</p></a></article>';
}

/**
 * A synced pattern the studio edits once and uses everywhere ("cta", "footer").
 */
function ak_studio_pattern( string $key ): string {
	return function_exists( 'ak_brand_render_pattern' ) ? ak_brand_render_pattern( $key ) : '';
}

/**
 * The logo: the studio's full lockup — the orange AK mark with the name in
 * white, so it reads on the dark site. (A dark-text version for light
 * backgrounds is in assets/img/ak-logo-dark.svg.)
 */
function ak_studio_logo(): void {
	$svg = (string) file_get_contents( get_theme_file_path( 'assets/img/ak-logo-light.svg' ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
	$svg = preg_replace( '#<title[^>]*>.*?</title>#', '', $svg );
	$svg = str_replace( array( ' role="img"', ' aria-labelledby="t"' ), array( ' aria-hidden="true" focusable="false"', '' ), $svg );
	echo '<a class="brand" href="' . esc_url( home_url( '/' ) ) . '" rel="home" aria-label="' . esc_attr( ak_studio_get( 'name' ) ) . '">';
	echo '<span class="brand__logo">' . $svg . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput -- the theme's own SVG.
	echo '</a>';
}

/**
 * The header button: the first item of the "Header button" menu, or a link
 * to the contact page until that menu exists.
 */
function ak_studio_header_button(): void {
	$label = __( 'Start a project', 'ak-studio' );
	$url   = ak_studio_contact_url();
	$locs  = get_nav_menu_locations();
	if ( ! empty( $locs['cta'] ) ) {
		$items = wp_get_nav_menu_items( (int) $locs['cta'] );
		if ( $items ) {
			$label = $items[0]->title;
			$url   = $items[0]->url;
		} else {
			return;
		}
	}
	printf(
		'<a class="btn btn--accent btn--sm site-head__cta" href="%s" data-magnetic>%s%s</a>',
		esc_url( $url ),
		esc_html( $label ),
		ak_studio_arrow() // phpcs:ignore WordPress.Security.EscapeOutput
	);
}

/**
 * Reading time of the current entry, in minutes.
 */
function ak_studio_reading_time(): int {
	return max( 1, (int) round( str_word_count( wp_strip_all_tags( (string) get_the_content() ) ) / 220 ) );
}

/**
 * The next project in the studio's order, wrapping round.
 */
function ak_studio_next_project(): void {
	$ordered = get_posts(
		array(
			'post_type'      => 'ak_project',
			'posts_per_page' => 200,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'fields'         => 'ids',
		)
	);
	$pos = array_search( get_the_ID(), $ordered, true );
	if ( false === $pos || count( $ordered ) < 2 ) {
		return;
	}
	$next  = get_post( $ordered[ ( $pos + 1 ) % count( $ordered ) ] );
	$short = (string) get_post_meta( $next->ID, 'ak_short_title', true );
	?>
	<nav class="next-project ak-full" aria-label="<?php esc_attr_e( 'Next project', 'ak-studio' ); ?>">
		<a href="<?php echo esc_url( get_permalink( $next ) ); ?>" data-cursor="<?php esc_attr_e( 'Next', 'ak-studio' ); ?>">
			<div class="next-project__media" aria-hidden="true"><?php echo ak_studio_img( (int) get_post_thumbnail_id( $next ), '100vw', array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<div class="wrap next-project__inner">
				<p class="eyebrow"><?php esc_html_e( 'Next project', 'ak-studio' ); ?></p>
				<p class="display next-project__title"><?php echo esc_html( $short ?: get_the_title( $next ) ); ?></p>
			</div>
		</a>
	</nav>
	<?php
}
