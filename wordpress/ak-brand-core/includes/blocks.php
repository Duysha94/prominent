<?php
/**
 * Editor blocks. Every page is built from blocks in the page editor: the
 * texts, videos, images and links live in the page, not in the theme.
 * These blocks are rendered on the server so the design stays consistent
 * while the content stays editable.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/* ── Helpers ───────────────────────────────────────────────────────── */

/**
 * Inline HTML allowed in block texts written with the editor's RichText.
 */
function ak_bk_inline( string $html ): string {
	$html = preg_replace( '/\*(.+?)\*/u', '<em>$1</em>', $html ); // *word* → italic accent.
	return wp_kses(
		(string) $html,
		array(
			'em'     => array(),
			'strong' => array(),
			'br'     => array(),
			'span'   => array( 'class' => true ),
			'a'      => array( 'href' => true, 'target' => true, 'rel' => true ),
		)
	);
}

function ak_bk_arrow( string $class = 'btn__arrow' ): string {
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M4 12 12 4M5.5 4H12v6.5" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>';
}

/**
 * A link button, or nothing when label or URL is empty.
 */
function ak_bk_button( string $label, string $url, string $variant = 'accent', bool $arrow = true ): string {
	if ( '' === trim( wp_strip_all_tags( $label ) ) || '' === trim( $url ) ) {
		return '';
	}
	$external = 0 === strpos( $url, 'http' ) && false === strpos( $url, (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	return '<a class="btn btn--' . esc_attr( $variant ) . '" href="' . esc_url( $url ) . '"' . ( $external ? ' target="_blank" rel="noopener"' : '' ) . ' data-magnetic>' . ak_bk_inline( $label ) . ( $arrow ? ak_bk_arrow() : '' ) . '</a>';
}

/**
 * Attributes of the first block of a type inside a post's content (cached).
 *
 * @return array<string,mixed>
 */
function ak_bk_first_block_attrs( int $post_id, string $name ): array {
	static $cache = array();
	$key = $post_id . '|' . $name;
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}
	$found = array();
	$walk  = static function ( array $blocks ) use ( &$walk, $name, &$found ) {
		foreach ( $blocks as $b ) {
			if ( $found ) {
				return;
			}
			if ( ( $b['blockName'] ?? '' ) === $name ) {
				$found = (array) $b['attrs'];
				return;
			}
			if ( ! empty( $b['innerBlocks'] ) ) {
				$walk( $b['innerBlocks'] );
			}
		}
	};
	$walk( parse_blocks( (string) get_post_field( 'post_content', $post_id ) ) );
	$cache[ $key ] = $found;
	return $found;
}

/**
 * An image tag for an attachment id, or a plain <img> for a URL.
 *
 * @param array<string,string> $attrs Extra attributes.
 */
function ak_bk_image( int $id, string $url, string $sizes = '100vw', array $attrs = array(), string $size = 'large' ): string {
	$attrs = array_merge( array( 'sizes' => $sizes, 'loading' => 'lazy', 'decoding' => 'async' ), $attrs );
	if ( $id && wp_attachment_is_image( $id ) ) {
		return wp_get_attachment_image( $id, $size, false, $attrs );
	}
	if ( '' !== $url ) {
		$out = '<img src="' . esc_url( $url ) . '"';
		foreach ( $attrs as $k => $v ) {
			if ( 'sizes' !== $k ) {
				$out .= ' ' . esc_attr( $k ) . '="' . esc_attr( (string) $v ) . '"';
			}
		}
		return $out . '>';
	}
	return '';
}

/* ── Cards shared by blocks and templates ──────────────────────────── */

/**
 * Card data for a project.
 *
 * @return array<string,mixed>
 */
function ak_brand_card_data( WP_Post $post ): array {
	$types  = get_the_terms( $post, 'ak_project_type' );
	$types  = is_array( $types ) ? $types : array();
	$date   = (string) get_post_meta( $post->ID, 'ak_date', true );
	$ts     = $date ? strtotime( $date . ' 23:59:59 ' . wp_timezone_string() ) : 0;
	$status = (string) get_post_meta( $post->ID, 'ak_status', true );
	$tag    = $status ?: ( $ts && $ts >= time() ? __( 'Upcoming', 'ak-brand-core' ) : (string) get_post_meta( $post->ID, 'ak_year', true ) );
	return array(
		'title' => (string) get_post_meta( $post->ID, 'ak_short_title', true ) ?: get_the_title( $post ),
		'kind'  => (string) get_post_meta( $post->ID, 'ak_kind', true ) ?: ( $types ? $types[0]->name : '' ),
		'place' => (string) get_post_meta( $post->ID, 'ak_location', true ),
		'tag'   => $tag,
		'types' => implode( ' ', wp_list_pluck( $types, 'slug' ) ),
		'text'  => get_the_excerpt( $post ),
		'image' => (int) get_post_thumbnail_id( $post ),
	);
}

function ak_brand_card( WP_Post $post, string $class = '', string $sizes = '(max-width: 900px) 92vw, 46vw', bool $hidden = false ): string {
	$c    = ak_brand_card_data( $post );
	$meta = implode( ' · ', array_filter( array( $c['kind'], $c['place'] ) ) );
	ob_start();
	?>
	<article class="card <?php echo esc_attr( $class ); ?>" data-types="<?php echo esc_attr( $c['types'] ); ?>"<?php echo $hidden ? ' hidden' : ''; ?>>
		<a class="card__link" href="<?php echo esc_url( get_permalink( $post ) ); ?>" data-cursor="<?php esc_attr_e( 'View', 'ak-brand-core' ); ?>">
			<?php $screens = ak_bk_first_block_attrs( $post->ID, 'ak/screens' ); ?>
			<?php $stage = $screens ? ak_bk_screens_stage( $screens, $sizes ) : ''; ?>
			<div class="card__media<?php echo $stage ? ' card__media--screens' : ''; ?>" data-reveal<?php echo $stage && preg_match( '/^#[0-9a-f]{3,8}$/i', (string) ( $screens['accent'] ?? '' ) ) ? ' style="--glow:' . esc_attr( $screens['accent'] ) . '"' : ''; ?>>
				<?php echo $stage ?: ak_bk_image( $c['image'], '', $sizes, array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( $c['tag'] ) : ?><span class="card__tag"><?php echo esc_html( $c['tag'] ); ?></span><?php endif; ?>
			</div>
			<div class="card__body">
				<?php if ( $meta ) : ?><p class="card__meta"><?php echo esc_html( $meta ); ?></p><?php endif; ?>
				<h3 class="card__title"><?php echo esc_html( $c['title'] ); ?></h3>
				<p class="card__text"><?php echo esc_html( $c['text'] ); ?></p>
			</div>
		</a>
	</article>
	<?php
	return (string) ob_get_clean();
}

function ak_brand_post_card( WP_Post $post ): string {
	$cats = get_the_category( $post->ID );
	ob_start();
	?>
	<article class="post-card">
		<a class="post-card__link" href="<?php echo esc_url( get_permalink( $post ) ); ?>" data-cursor="<?php esc_attr_e( 'Read', 'ak-brand-core' ); ?>">
			<div class="post-card__media" data-reveal>
				<?php echo ak_bk_image( (int) get_post_thumbnail_id( $post ), '', '(max-width: 900px) 92vw, 30vw', array( 'alt' => '' ), 'medium_large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<p class="post-card__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>"><?php echo esc_html( get_the_date( 'j M Y', $post ) ); ?></time><?php echo $cats ? ' · ' . esc_html( $cats[0]->name ) : ''; ?></p>
			<h3 class="post-card__title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
			<p class="post-card__text"><?php echo esc_html( get_the_excerpt( $post ) ); ?></p>
		</a>
	</article>
	<?php
	return (string) ob_get_clean();
}

/* ── Render callbacks ──────────────────────────────────────────────── */

/**
 * Page header: eyebrow, display heading, lead, an optional question and an
 * optional photograph or silent film behind it.
 *
 * @param array<string,mixed> $a Attributes.
 */
function ak_bk_render_page_header( array $a ): string {
	$size  = in_array( $a['size'] ?? '', array( 'large', 'compact', 'article', 'cover' ), true ) ? $a['size'] : 'large';
	$video = 'video' === ( $a['mediaType'] ?? '' ) && ! empty( $a['mediaUrl'] );
	$image = ! $video && ( ! empty( $a['mediaId'] ) || ! empty( $a['mediaUrl'] ) );
	$media = '';
	$focus = ak_brand_focus_from_point( $a['focus'] ?? null ) ?: ak_brand_focus( (int) ( $a['mediaId'] ?? 0 ) );
	$style = $focus ? ' style="object-position:' . esc_attr( $focus ) . '"' : '';
	if ( $video ) {
		$media = '<video muted loop playsinline preload="none" data-autoplay data-src="' . esc_url( $a['mediaUrl'] ) . '"' . ( ! empty( $a['posterUrl'] ) ? ' poster="' . esc_url( $a['posterUrl'] ) . '"' : '' ) . $style . '></video>';
	} elseif ( $image ) {
		$img_attrs = array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => '' );
		if ( $focus ) {
			$img_attrs['style'] = 'object-position:' . $focus;
		}
		$media = ak_bk_image( (int) ( $a['mediaId'] ?? 0 ), (string) ( $a['mediaUrl'] ?? '' ), '100vw', $img_attrs, 'full' );
	}
	$heading = (string) ( $a['heading'] ?? '' );
	if ( '' === trim( wp_strip_all_tags( $heading ) ) && is_singular() ) {
		$heading = esc_html( get_the_title() );
	}
	ob_start();
	?>
	<header class="page-head page-head--<?php echo esc_attr( $size ); ?><?php echo $media ? ' page-head--media' : ''; ?> ak-full">
		<?php if ( $media ) : ?>
			<div class="page-head__media" aria-hidden="true" data-parallax="12"><?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput ?><div class="page-head__scrim"></div></div>
		<?php endif; ?>
		<div class="wrap page-head__inner">
			<?php if ( ! empty( $a['eyebrow'] ) ) : ?>
				<p class="eyebrow" data-rise><?php echo ak_bk_inline( (string) $a['eyebrow'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<?php endif; ?>
			<h1 class="display page-head__title" data-split><?php echo ak_bk_inline( $heading ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
			<?php if ( ! empty( $a['lede'] ) || ! empty( $a['question'] ) ) : ?>
				<div class="page-head__foot" data-rise>
					<?php if ( ! empty( $a['lede'] ) ) : ?><p class="lede"><?php echo ak_bk_inline( (string) $a['lede'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
					<?php if ( ! empty( $a['question'] ) ) : ?>
						<p class="page-head__q"><span><?php echo esc_html( $a['questionLabel'] ?? __( 'The question', 'ak-brand-core' ) ); ?></span><?php echo ak_bk_inline( (string) $a['question'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</header>
	<?php
	return (string) ob_get_clean();
}

/**
 * Hero: the reel and the kinetic headline.
 *
 * @param array<string,mixed> $a Attributes.
 */
function ak_bk_render_hero( array $a ): string {
	$clips = array_values( array_filter( (array) ( $a['clips'] ?? array() ), static fn( $c ) => ! empty( $c['video'] ) || ! empty( $c['poster'] ) ) );
	if ( ! $clips ) {
		$clips = (array) apply_filters( 'ak_brand_default_reel', array() );
	}
	$total = count( $clips );
	$line  = static function ( string $phrase, int $times = 4 ): string {
		$one  = '<span class="kinetic__phrase">' . $phrase . '</span><i class="kinetic__slash">/</i>';
		$half = str_repeat( $one, $times );
		return '<span class="kinetic__track">' . $half . $half . '</span>';
	};
	$rows  = array_filter( array( 1 => $a['row1'] ?? '', 2 => $a['row2'] ?? '', 3 => $a['row3'] ?? '' ), static fn( $r ) => '' !== trim( wp_strip_all_tags( (string) $r ) ) );
	$plain = trim( implode( ' ', array_map( static fn( $r ) => wp_strip_all_tags( (string) $r ), $rows ) ) );
	$stats = array_values( array_filter( (array) ( $a['stats'] ?? array() ), static fn( $s ) => ! empty( $s['value'] ) || ! empty( $s['label'] ) ) );
	ob_start();
	?>
	<section class="hero ak-full" id="top" data-hero>
		<div class="hero__media" aria-hidden="true">
			<?php foreach ( $clips as $i => $clip ) : ?>
				<video class="hero__clip<?php echo 0 === $i ? ' is-on' : ''; ?>"<?php echo ! empty( $clip['focus'] ) && preg_match( '/^[\d.]+% [\d.]+%$/', (string) $clip['focus'] ) ? ' style="object-position:' . esc_attr( (string) $clip['focus'] ) . '"' : ''; ?> muted loop playsinline preload="<?php echo 0 === $i ? 'auto' : 'none'; ?>"
					poster="<?php echo esc_url( (string) ( $clip['poster'] ?? '' ) ); ?>" data-src="<?php echo esc_url( (string) ( $clip['video'] ?? '' ) ); ?>" data-src-sm="<?php echo esc_url( (string) ( $clip['videoSm'] ?? ( $clip['video'] ?? '' ) ) ); ?>"
					data-label="<?php echo esc_attr( wp_strip_all_tags( (string) ( $clip['label'] ?? '' ) ) ); ?>" data-kind="<?php echo esc_attr( wp_strip_all_tags( (string) ( $clip['kind'] ?? '' ) ) ); ?>"></video>
			<?php endforeach; ?>
			<div class="hero__scrim"></div>
		</div>
		<svg class="shape shape--gyro" viewBox="0 0 120 120" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-width=".8"><ellipse cx="60" cy="60" rx="54" ry="18"/><ellipse cx="60" cy="60" rx="54" ry="18" transform="rotate(60 60 60)"/><ellipse cx="60" cy="60" rx="54" ry="18" transform="rotate(120 60 60)"/><circle cx="60" cy="60" r="3" fill="currentColor"/></g></svg>
		<svg class="shape shape--cube" viewBox="0 0 100 100" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-width=".8"><path d="M50 8 88 30v40L50 92 12 70V30Z"/><path d="M50 8v42m0 0 38-20M50 50 12 30m38 20v42"/></g></svg>
		<div class="hero__inner">
			<div class="hero__meta wrap" data-rise>
				<?php if ( ! empty( $a['slate'] ) ) : ?>
					<p class="slate"><span class="rec" aria-hidden="true"></span>REC <span class="slate__sep">/</span> <?php echo ak_bk_inline( (string) $a['slate'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				<?php endif; ?>
				<?php if ( $total ) : ?>
					<div class="hero__reel" aria-hidden="true">
						<span class="hero__num" data-reel-num>01</span>
						<span class="hero__of">/ <?php echo esc_html( str_pad( (string) $total, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="hero__label" data-reel-label><?php echo esc_html( wp_strip_all_tags( (string) ( $clips[0]['label'] ?? '' ) ) ); ?></span>
						<span class="hero__kind" data-reel-kind><?php echo esc_html( wp_strip_all_tags( (string) ( $clips[0]['kind'] ?? '' ) ) ); ?></span>
						<span class="hero__bar"><span data-reel-bar></span></span>
					</div>
				<?php endif; ?>
			</div>
			<h1 class="kinetic">
				<span class="sr-only"><?php echo esc_html( $plain ); ?></span>
				<?php
				$speeds = array( 1 => array( -1, 0.6 ), 2 => array( 1, 0.42 ), 3 => array( -1, 0.78 ) );
				foreach ( $rows as $n => $row ) :
					?>
					<span class="kinetic__row<?php echo 2 === $n ? '' : ' kinetic__row--outline'; ?>" data-dir="<?php echo esc_attr( (string) $speeds[ $n ][0] ); ?>" data-speed="<?php echo esc_attr( (string) $speeds[ $n ][1] ); ?>" aria-hidden="true"><?php echo $line( ak_bk_inline( (string) $row ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php endforeach; ?>
			</h1>
			<div class="hero__intro wrap">
				<div class="hero__lede" data-rise>
					<?php if ( ! empty( $a['pillText'] ) ) : ?>
						<?php if ( ! empty( $a['pillUrl'] ) ) : ?>
							<a class="pill" href="<?php echo esc_url( (string) $a['pillUrl'] ); ?>"><span class="pill__dot" aria-hidden="true"></span><?php echo ak_bk_inline( (string) $a['pillText'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						<?php else : ?>
							<p class="pill"><span class="pill__dot" aria-hidden="true"></span><?php echo ak_bk_inline( (string) $a['pillText'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( ! empty( $a['lede'] ) ) : ?><p class="hero__text"><?php echo ak_bk_inline( (string) $a['lede'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
					<div class="hero__ctas">
						<?php echo ak_bk_button( (string) ( $a['primaryLabel'] ?? '' ), (string) ( $a['primaryUrl'] ?? '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo ak_bk_button( (string) ( $a['secondaryLabel'] ?? '' ), (string) ( $a['secondaryUrl'] ?? '' ), 'ghost', false ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</div>
				<?php if ( $stats ) : ?>
					<dl class="stats" data-rise>
						<?php foreach ( $stats as $s ) : ?>
							<div class="stat">
								<dt><?php echo esc_html( wp_strip_all_tags( (string) ( $s['label'] ?? '' ) ) ); ?></dt>
								<?php if ( is_numeric( $s['value'] ?? '' ) ) : ?>
									<dd><span data-count="<?php echo esc_attr( (string) $s['value'] ); ?>"><?php echo esc_html( (string) $s['value'] ); ?></span><?php echo esc_html( (string) ( $s['suffix'] ?? '' ) ); ?></dd>
								<?php else : ?>
									<dd><?php echo str_replace( '+', '<em>+</em>', esc_html( (string) ( $s['value'] ?? '' ) . ( $s['suffix'] ?? '' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></dd>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>
		</div>
		<a class="hero__scroll" href="#main" data-rise><span><?php esc_html_e( 'Scroll', 'ak-brand-core' ); ?></span><i aria-hidden="true"></i></a>
	</section>
	<?php
	return (string) ob_get_clean();
}

/**
 * Marquee band.
 *
 * @param array<string,mixed> $a Attributes.
 */
function ak_bk_render_marquee( array $a ): string {
	$items = array_values( array_filter( array_map( 'strval', (array) ( $a['items'] ?? array() ) ), static fn( $i ) => '' !== trim( $i ) ) );
	if ( ! $items ) {
		return '';
	}
	$row = '';
	foreach ( $items as $item ) {
		$row .= '<span class="band__item">' . ak_bk_inline( $item ) . '</span><span class="band__dot" aria-hidden="true">✦</span>';
	}
	$variant = 'outline' === ( $a['variant'] ?? '' ) ? ' band--outline' : '';
	$speed   = max( 10, min( 120, (int) ( $a['speed'] ?? 42 ) ) );
	return '<div class="band ak-full' . $variant . '" role="presentation"><div class="band__strip"><div class="band__track" style="animation-duration:' . $speed . 's" aria-hidden="true">' . $row . $row . '</div></div>'
		. '<ul class="sr-only">' . implode( '', array_map( static fn( $i ) => '<li>' . esc_html( wp_strip_all_tags( $i ) ) . '</li>', $items ) ) . '</ul></div>';
}

/**
 * Services: the child pages of the Services page, each with the question and
 * lead from its own page header.
 *
 * @param array<string,mixed> $a Attributes.
 */
function ak_bk_render_services( array $a ): string {
	$parent = get_page_by_path( (string) ( $a['parent'] ?? 'services' ) );
	if ( ! $parent ) {
		return '';
	}
	$current = is_page() ? get_queried_object_id() : 0;
	$pages   = get_pages( array( 'parent' => $parent->ID, 'sort_column' => 'menu_order', 'post_status' => 'publish' ) );
	$items   = '';
	$n       = 0;
	foreach ( $pages as $page ) {
		++$n;
		if ( ! empty( $a['excludeCurrent'] ) && $page->ID === $current ) {
			continue;
		}
		$h     = ak_bk_first_block_attrs( $page->ID, 'ak/page-header' );
		$q     = wp_strip_all_tags( (string) ( $h['question'] ?? '' ) );
		$line  = wp_strip_all_tags( (string) ( $h['lede'] ?? '' ) ) ?: get_the_excerpt( $page );
		$img   = ! empty( $h['mediaId'] ) ? wp_get_attachment_image_url( (int) $h['mediaId'], 'medium_large' ) : ( $h['mediaUrl'] ?? '' );
		$name  = (string) get_post_meta( $page->ID, 'ak_short_title', true ) ?: get_the_title( $page );
		$items .= '<li class="svc__item" data-rise><a class="svc__link" href="' . esc_url( get_permalink( $page ) ) . '" data-svc-img="' . esc_url( (string) $img ) . '" data-cursor="' . esc_attr__( 'Open', 'ak-brand-core' ) . '">'
			. '<span class="svc__num">' . esc_html( str_pad( (string) $n, 2, '0', STR_PAD_LEFT ) ) . '</span>'
			. '<span class="svc__name">' . esc_html( $name ) . '</span>'
			. '<span class="svc__q">' . esc_html( $q ) . '</span>'
			. '<span class="svc__line">' . esc_html( $line ) . '</span>'
			. ak_bk_arrow( 'svc__arrow' ) . '</a></li>';
	}
	if ( '' === $items ) {
		return '';
	}
	return '<div class="ak-services ak-wide"><ol class="svc" data-svc>' . $items . '</ol><div class="svc__float" aria-hidden="true" data-svc-float><img alt="" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" decoding="async"></div></div>';
}

/**
 * Projects matching the block's choices.
 *
 * @param array<string,mixed> $a Attributes.
 * @return WP_Post[]
 */
function ak_bk_work_query( array $a ): array {
	$args = array(
		'post_type'      => 'ak_project',
		'post_status'    => 'publish',
		'posts_per_page' => max( 1, min( 60, (int) ( $a['count'] ?? 6 ) ) ),
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	);
	$ids = array_values( array_filter( array_map( 'absint', (array) ( $a['ids'] ?? array() ) ) ) );
	if ( $ids ) {
		$args['post__in']       = $ids;
		$args['orderby']        = 'post__in';
		$args['posts_per_page'] = count( $ids );
		return get_posts( $args );
	}
	$tax = array();
	foreach ( array( 'types' => 'ak_project_type', 'disciplines' => 'ak_capability' ) as $key => $taxonomy ) {
		$slugs = array_filter( array_map( 'sanitize_title', array_map( 'trim', explode( ',', (string) ( $a[ $key ] ?? '' ) ) ) ) );
		if ( $slugs ) {
			$tax[] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $slugs );
		}
	}
	if ( $tax ) {
		$args['tax_query'] = count( $tax ) > 1 ? array_merge( array( 'relation' => 'OR' ), $tax ) : $tax; // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	if ( ! empty( $a['featured'] ) ) {
		$args['meta_query'] = array( array( 'key' => 'ak_featured', 'value' => '1' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	return get_posts( $args );
}

/**
 * Work: projects as a grid with filters, a horizontal rail, or feature cards.
 *
 * @param array<string,mixed> $a Attributes.
 */
function ak_bk_render_work( array $a ): string {
	$projects = ak_bk_work_query( $a );
	if ( ! $projects ) {
		return '';
	}
	$layout = in_array( $a['layout'] ?? 'grid', array( 'grid', 'rail', 'feature' ), true ) ? $a['layout'] : 'grid';
	$more   = ak_bk_button( (string) ( $a['moreLabel'] ?? '' ), (string) ( $a['moreUrl'] ?? '' ), 'ghost' );

	if ( 'rail' === $layout ) {
		$cards = '';
		foreach ( $projects as $p ) {
			$cards .= ak_brand_card( $p, 'card--rail', '(max-width: 900px) 80vw, 34vw' );
		}
		$end = '';
		if ( ! empty( $a['moreUrl'] ) ) {
			$end = '<a class="work__more" href="' . esc_url( (string) $a['moreUrl'] ) . '" data-cursor="' . esc_attr__( 'All', 'ak-brand-core' ) . '"><span class="display">' . ak_bk_inline( (string) ( $a['moreLabel'] ?: __( 'All *work*', 'ak-brand-core' ) ) ) . '</span>' . ak_bk_arrow( 'work__more-arrow' ) . '</a>';
		}
		return '<div class="work ak-full" data-rail><div class="work__viewport"><div class="work__track" data-rail-track>' . $cards . $end . '</div></div></div>';
	}

	if ( 'feature' === $layout ) {
		$out = '';
		foreach ( $projects as $p ) {
			$h     = ak_bk_first_block_attrs( $p->ID, 'ak/page-header' );
			$facts = array_slice( (array) ( ak_bk_first_block_attrs( $p->ID, 'ak/facts' )['items'] ?? array() ), 0, 3 );
			$c     = ak_brand_card_data( $p );
			$media = 'video' === ( $h['mediaType'] ?? '' ) && ! empty( $h['mediaUrl'] )
				? '<video muted loop playsinline preload="none" data-autoplay data-src="' . esc_url( (string) $h['mediaUrl'] ) . '" poster="' . esc_url( (string) ( $h['posterUrl'] ?? '' ) ) . '"' . ( ak_brand_focus( (int) ( $h['mediaId'] ?? 0 ) ) ? ' style="object-position:' . esc_attr( ak_brand_focus( (int) $h['mediaId'] ) ) . '"' : '' ) . '></video>'
				: ak_bk_image( $c['image'], '', '(max-width: 900px) 92vw, 46vw', array( 'alt' => '' ) );
			$dl    = '';
			foreach ( $facts as $f ) {
				$dl .= '<div><dt>' . esc_html( wp_strip_all_tags( (string) ( $f['label'] ?? '' ) ) ) . '</dt><dd>' . esc_html( wp_strip_all_tags( (string) ( $f['value'] ?? '' ) ) ) . '</dd></div>';
			}
			$out .= '<article class="platform" data-rise><a class="platform__link" href="' . esc_url( get_permalink( $p ) ) . '" data-cursor="' . esc_attr__( 'Open', 'ak-brand-core' ) . '">'
				. '<div class="platform__media" aria-hidden="true">' . $media . '</div><div class="platform__body">'
				. ( $c['kind'] ? '<p class="platform__kind">' . esc_html( $c['kind'] ) . '</p>' : '' )
				. '<h3 class="platform__name">' . esc_html( $c['title'] ) . '</h3>'
				. '<p class="platform__note">' . esc_html( $c['text'] ) . '</p>'
				. ( $dl ? '<dl class="platform__facts">' . $dl . '</dl>' : '' )
				. '<span class="platform__go">' . esc_html__( 'See the project', 'ak-brand-core' ) . ak_bk_arrow( 'link-arrow__icon' ) . '</span></div></a></article>';
		}
		return '<div class="platforms__big ak-wide">' . $out . '</div>' . ( $more ? '<p class="ak-more ak-wide">' . $more . '</p>' : '' );
	}

	// Grid, optionally with filter chips (?type=website preselects one).
	$columns = 3 === (int) ( $a['columns'] ?? 2 ) ? ' card-grid--3' : '';
	$chosen  = isset( $_GET['type'] ) ? sanitize_title( wp_unslash( $_GET['type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display filter only.
	$chips   = '';
	if ( ! empty( $a['showFilters'] ) ) {
		$present = array();
		foreach ( $projects as $p ) {
			foreach ( (array) get_the_terms( $p, 'ak_project_type' ) as $t ) {
				if ( $t instanceof WP_Term ) {
					$present[ $t->slug ] = $t;
				}
			}
		}
		$order = array( 'website', 'shoot', 'show', 'brand', 'platform' );
		uksort( $present, static fn( $x, $y ) => ( array_search( $x, $order, true ) === false ? 99 : array_search( $x, $order, true ) ) <=> ( array_search( $y, $order, true ) === false ? 99 : array_search( $y, $order, true ) ) );
		if ( ! isset( $present[ $chosen ] ) ) {
			$chosen = '';
		}
		$chips = '<div class="filters" role="group" aria-label="' . esc_attr__( 'Filter projects', 'ak-brand-core' ) . '" data-filters>'
			. '<button type="button" class="filter' . ( '' === $chosen ? ' is-on' : '' ) . '" aria-pressed="' . ( '' === $chosen ? 'true' : 'false' ) . '" data-filter="*">' . esc_html__( 'All', 'ak-brand-core' ) . '</button>';
		foreach ( $present as $slug => $term ) {
			$on     = $slug === $chosen;
			$chips .= '<button type="button" class="filter' . ( $on ? ' is-on' : '' ) . '" aria-pressed="' . ( $on ? 'true' : 'false' ) . '" data-filter="' . esc_attr( $slug ) . '">' . esc_html( $term->description ?: $term->name ) . '</button>';
		}
		$chips .= '</div>';
	}
	$cards = '';
	foreach ( $projects as $p ) {
		$hide   = '' !== $chosen && ! has_term( $chosen, 'ak_project_type', $p );
		$cards .= ak_brand_card( $p, '', $columns ? '(max-width: 900px) 92vw, 30vw' : '(max-width: 900px) 92vw, 46vw', $hide );
	}
	return '<div class="ak-work ak-wide">' . $chips . '<div class="card-grid' . $columns . '" data-filter-grid>' . $cards . '</div>'
		. ( $chips ? '<p class="filters__empty" hidden data-filter-empty>' . esc_html__( 'Nothing here yet.', 'ak-brand-core' ) . '</p>' : '' )
		. ( $more ? '<p class="ak-more">' . $more . '</p>' : '' ) . '</div>';
}

/**
 * A website shown in a browser window and on a phone, from two plain
 * screenshots. Used by the Website screens block and by website cards.
 *
 * @param array<string,mixed> $a     Block attributes.
 * @param string              $sizes sizes attribute for the desktop screenshot.
 */
function ak_bk_screens_stage( array $a, string $sizes = '(max-width: 900px) 92vw, 1000px' ): string {
	$desk  = ak_bk_image( (int) ( $a['desktopId'] ?? 0 ), (string) ( $a['desktopUrl'] ?? '' ), $sizes, array( 'alt' => '' ), 'full' );
	$phone = ak_bk_image( (int) ( $a['mobileId'] ?? 0 ), (string) ( $a['mobileUrl'] ?? '' ), '(max-width: 900px) 30vw, 300px', array( 'alt' => '' ), 'large' );
	if ( '' === $desk && '' === $phone ) {
		return '';
	}
	$domain = wp_strip_all_tags( (string) ( $a['domain'] ?? '' ) );
	$out    = '<div class="screens__stage' . ( $phone ? '' : ' screens__stage--desktop' ) . ( $desk ? '' : ' screens__stage--phone' ) . '">';
	if ( $desk ) {
		$out .= '<figure class="screens__browser"><div class="screens__bar" aria-hidden="true"><i></i><i></i><i></i><span>' . esc_html( $domain ) . '</span></div><div class="screens__view">' . $desk . '</div></figure>';
	}
	if ( $phone ) {
		$out .= '<figure class="screens__phone"><div class="screens__screen">' . $phone . '</div></figure>';
	}
	return $out . '</div>';
}

/**
 * Website screens block.
 *
 * @param array<string,mixed> $a Attributes.
 */
function ak_bk_render_screens( array $a ): string {
	$stage = ak_bk_screens_stage( $a );
	$pages = array_values( array_filter( (array) ( $a['pages'] ?? array() ), static fn( $p ) => ! empty( $p['url'] ) ) );
	$more  = array_values( array_filter( (array) ( $a['phones'] ?? array() ), static fn( $p ) => ! empty( $p['url'] ) ) );
	if ( '' === $stage && ! $pages && ! $more ) {
		return '';
	}
	$domain = wp_strip_all_tags( (string) ( $a['domain'] ?? '' ) );
	$glow   = preg_match( '/^#[0-9a-f]{3,8}$/i', (string) ( $a['accent'] ?? '' ) ) ? ' style="--glow:' . esc_attr( $a['accent'] ) . '"' : '';
	$out    = '<div class="screens ak-wide"' . $glow . '>' . $stage;
	$zoom   = static function ( array $p, string $sizes, string $size ): string {
		$full = ! empty( $p['id'] ) ? (string) wp_get_attachment_url( (int) $p['id'] ) : (string) $p['url'];
		return '<a class="screens__zoom" href="' . esc_url( $full ) . '">' . ak_bk_image( (int) ( $p['id'] ?? 0 ), (string) $p['url'], $sizes, array( 'alt' => '' ), $size ) . '</a>';
	};
	if ( $pages ) {
		$out .= '<div class="screens__pages">';
		foreach ( $pages as $p ) {
			$out .= '<figure class="screens__browser"><div class="screens__bar" aria-hidden="true"><i></i><i></i><i></i><span>' . esc_html( $domain ) . '</span></div><div class="screens__view">' . $zoom( $p, '(max-width: 900px) 92vw, 640px', 'full' ) . '</div></figure>';
		}
		$out .= '</div>';
	}
	if ( $more ) {
		$out .= '<div class="screens__phones">';
		foreach ( $more as $p ) {
			$out .= '<figure class="screens__phone"><div class="screens__screen">' . $zoom( $p, '(max-width: 900px) 45vw, 300px', 'large' ) . '</div></figure>';
		}
		$out .= '</div>';
	}
	return $out . '</div>';
}

/**
 * Facts: a row of label/value pairs, with an optional link.
 *
 * @param array<string,mixed> $a Attributes.
 */
function ak_bk_render_facts( array $a ): string {
	$items = array_values( array_filter( (array) ( $a['items'] ?? array() ), static fn( $i ) => ! empty( $i['label'] ) && ! empty( $i['value'] ) ) );
	if ( ! $items && empty( $a['linkUrl'] ) ) {
		return '';
	}
	$dl = '';
	foreach ( $items as $i ) {
		$dl .= '<div><dt>' . esc_html( wp_strip_all_tags( (string) $i['label'] ) ) . '</dt><dd>' . ak_bk_inline( (string) $i['value'] ) . '</dd></div>';
	}
	$link = '';
	if ( ! empty( $a['linkUrl'] ) ) {
		$label = (string) ( $a['linkLabel'] ?? '' ) ?: (string) wp_parse_url( (string) $a['linkUrl'], PHP_URL_HOST );
		$link  = '<a class="link-arrow" href="' . esc_url( (string) $a['linkUrl'] ) . '" target="_blank" rel="noopener">' . esc_html( wp_strip_all_tags( $label ) ) . ak_bk_arrow( 'link-arrow__icon' ) . '</a>';
	}
	return '<div class="facts ak-wide" data-rise><dl class="facts__list">' . $dl . '</dl>' . $link . '</div>';
}

/**
 * Event: upcoming until its date, then "latest".
 *
 * @param array<string,mixed> $a Attributes.
 */
function ak_bk_render_event( array $a ): string {
	$date = (string) ( $a['date'] ?? '' );
	$ts   = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ? strtotime( $date . ' 23:59:59 ' . wp_timezone_string() ) : 0;
	if ( ! $ts || empty( $a['title'] ) ) {
		return '';
	}
	$upcoming = $ts >= time();
	$image    = ak_bk_image( (int) ( $a['imageId'] ?? 0 ), (string) ( $a['imageUrl'] ?? '' ), '(max-width: 900px) 92vw, 40vw', array( 'alt' => '' ) );
	$eyebrow  = $upcoming ? ( $a['eyebrowUpcoming'] ?? '' ) : ( $a['eyebrowPast'] ?? '' );
	$primary  = $upcoming ? ak_bk_button( (string) ( $a['primaryLabel'] ?? '' ), (string) ( $a['primaryUrl'] ?? '' ) ) : ak_bk_button( (string) ( $a['pastLabel'] ?? '' ), (string) ( $a['pastUrl'] ?? '' ) );
	$second   = ak_bk_button( (string) ( $a['secondaryLabel'] ?? '' ), (string) ( $a['secondaryUrl'] ?? '' ), 'ghost', false );
	ob_start();
	?>
	<section class="event ak-full" id="<?php echo esc_attr( (string) ( $a['anchor'] ?? '' ) ?: 'event' ); ?>">
		<div class="wrap event__grid">
			<div class="event__media" data-reveal><?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<div class="event__body">
				<p class="eyebrow" data-rise><?php echo ak_bk_inline( trim( $eyebrow . ( ! empty( $a['label'] ) ? ' — ' . $a['label'] : '' ), ' —' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				<h2 class="event__title display" data-split><?php echo ak_bk_inline( (string) $a['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<div class="event__when" data-rise>
					<span class="event__day"><?php echo esc_html( wp_date( 'j', $ts ) ); ?></span>
					<span class="event__month"><?php echo esc_html( wp_date( 'F Y', $ts ) ); ?><?php if ( ! empty( $a['venue'] ) ) : ?><br><?php echo esc_html( wp_strip_all_tags( (string) $a['venue'] ) ); ?><?php endif; ?></span>
				</div>
				<?php if ( ! empty( $a['note'] ) ) : ?><p class="lede" data-rise><?php echo ak_bk_inline( (string) $a['note'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
				<?php if ( $primary || $second ) : ?><div class="event__ctas" data-rise><?php echo $primary . $second; // phpcs:ignore WordPress.Security.EscapeOutput ?></div><?php endif; ?>
			</div>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}

/**
 * Brief form.
 *
 * @param array<string,mixed> $a Attributes.
 */
function ak_bk_render_brief( array $a ): string {
	static $n = 0;
	++$n;
	$need = (string) ( $a['service'] ?? '' );
	if ( '' === $need && isset( $_GET['need'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- preselects a checkbox only.
		$need = sanitize_key( wp_unslash( $_GET['need'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	$form = ak_brand_brief_form(
		array(
			'compact' => ! empty( $a['compact'] ),
			'service' => $need,
			'id'      => 1 === $n ? 'brief' : 'brief-' . $n,
			'source'  => (string) ( $a['source'] ?? '' ),
		)
	);
	return '<div class="ak-brief">' . $form . '</div>';
}

/**
 * Latest journal entries.
 *
 * @param array<string,mixed> $a Attributes.
 */
function ak_bk_render_journal( array $a ): string {
	$args = array( 'posts_per_page' => max( 1, min( 12, (int) ( $a['count'] ?? 3 ) ) ), 'ignore_sticky_posts' => true );
	if ( ! empty( $a['category'] ) ) {
		$args['category_name'] = sanitize_title( (string) $a['category'] );
	}
	if ( is_singular( 'post' ) ) {
		$args['post__not_in'] = array( get_queried_object_id() );
	}
	$posts = get_posts( $args );
	if ( ! $posts ) {
		return '';
	}
	return '<div class="post-grid ak-wide">' . implode( '', array_map( 'ak_brand_post_card', $posts ) ) . '</div>';
}

/* ── Registration ──────────────────────────────────────────────────── */

add_filter(
	'block_categories_all',
	static fn( $cats ) => array_merge( array( array( 'slug' => 'ak', 'title' => __( 'AK Studio', 'ak-brand-core' ), 'icon' => null ) ), $cats ),
	10
);

/**
 * @return array<string,array<string,mixed>>
 */
function ak_bk_definitions(): array {
	$s = array( 'type' => 'string', 'default' => '' );
	$b = static fn( bool $d = false ) => array( 'type' => 'boolean', 'default' => $d );
	$n = static fn( int $d = 0 ) => array( 'type' => 'number', 'default' => $d );
	$arr = array( 'type' => 'array', 'default' => array() );
	return array(
		'page-header' => array(
			'title'       => __( 'Page header', 'ak-brand-core' ),
			'description' => __( 'Eyebrow, large heading, lead and an optional photo or silent film.', 'ak-brand-core' ),
			'icon'        => 'heading',
			'attributes'  => array( 'eyebrow' => $s, 'heading' => $s, 'lede' => $s, 'question' => $s, 'questionLabel' => array( 'type' => 'string', 'default' => 'The question' ), 'mediaId' => $n(), 'mediaUrl' => $s, 'mediaType' => array( 'type' => 'string', 'default' => 'image' ), 'posterUrl' => $s, 'size' => array( 'type' => 'string', 'default' => 'large' ), 'focus' => array( 'type' => 'object' ) ),
			'render'      => 'ak_bk_render_page_header',
		),
		'hero'        => array(
			'title'       => __( 'Hero reel', 'ak-brand-core' ),
			'description' => __( 'Full-screen films behind a moving three-line headline, with buttons and numbers.', 'ak-brand-core' ),
			'icon'        => 'format-video',
			'attributes'  => array( 'slate' => $s, 'row1' => $s, 'row2' => $s, 'row3' => $s, 'pillText' => $s, 'pillUrl' => $s, 'lede' => $s, 'primaryLabel' => $s, 'primaryUrl' => $s, 'secondaryLabel' => $s, 'secondaryUrl' => $s, 'clips' => $arr, 'stats' => $arr ),
			'render'      => 'ak_bk_render_hero',
		),
		'marquee'     => array(
			'title'       => __( 'Moving band', 'ak-brand-core' ),
			'description' => __( 'Words that scroll across an orange band.', 'ak-brand-core' ),
			'icon'        => 'leftright',
			'attributes'  => array( 'items' => $arr, 'variant' => array( 'type' => 'string', 'default' => 'band' ), 'speed' => $n( 42 ) ),
			'render'      => 'ak_bk_render_marquee',
		),
		'services'    => array(
			'title'       => __( 'Services list', 'ak-brand-core' ),
			'description' => __( 'The pages under Services, each with its question and lead. Edit a service on its own page.', 'ak-brand-core' ),
			'icon'        => 'list-view',
			'attributes'  => array( 'parent' => array( 'type' => 'string', 'default' => 'services' ), 'excludeCurrent' => $b( true ) ),
			'render'      => 'ak_bk_render_services',
		),
		'work'        => array(
			'title'       => __( 'Work', 'ak-brand-core' ),
			'description' => __( 'Projects as a grid with filters, a moving rail or large feature cards.', 'ak-brand-core' ),
			'icon'        => 'portfolio',
			'attributes'  => array( 'layout' => array( 'type' => 'string', 'default' => 'grid' ), 'types' => $s, 'disciplines' => $s, 'ids' => $arr, 'count' => $n( 6 ), 'columns' => $n( 2 ), 'featured' => $b(), 'showFilters' => $b(), 'moreLabel' => $s, 'moreUrl' => $s ),
			'render'      => 'ak_bk_render_work',
		),
		'screens'     => array(
			'title'       => __( 'Website screens', 'ak-brand-core' ),
			'description' => __( 'A website in a browser window and on a phone, from two ordinary screenshots — plus more pages if you like. The frames are drawn by the site.', 'ak-brand-core' ),
			'icon'        => 'desktop',
			'attributes'  => array( 'domain' => $s, 'desktopId' => $n(), 'desktopUrl' => $s, 'mobileId' => $n(), 'mobileUrl' => $s, 'pages' => $arr, 'phones' => $arr, 'accent' => $s ),
			'render'      => 'ak_bk_render_screens',
		),
		'facts'       => array(
			'title'       => __( 'Facts', 'ak-brand-core' ),
			'description' => __( 'A row of short facts — when, where, our part — with a link.', 'ak-brand-core' ),
			'icon'        => 'editor-table',
			'attributes'  => array( 'items' => $arr, 'linkLabel' => $s, 'linkUrl' => $s ),
			'render'      => 'ak_bk_render_facts',
		),
		'event'       => array(
			'title'       => __( 'Event', 'ak-brand-core' ),
			'description' => __( 'A dated event with an image. Shows as upcoming until the date, then as the latest.', 'ak-brand-core' ),
			'icon'        => 'calendar-alt',
			'attributes'  => array( 'eyebrowUpcoming' => array( 'type' => 'string', 'default' => 'Next on stage' ), 'eyebrowPast' => array( 'type' => 'string', 'default' => 'Latest season' ), 'label' => $s, 'title' => $s, 'date' => $s, 'venue' => $s, 'note' => $s, 'imageId' => $n(), 'imageUrl' => $s, 'primaryLabel' => $s, 'primaryUrl' => $s, 'pastLabel' => $s, 'pastUrl' => $s, 'secondaryLabel' => $s, 'secondaryUrl' => $s, 'anchor' => $s ),
			'render'      => 'ak_bk_render_event',
		),
		'brief'       => array(
			'title'       => __( 'Project brief form', 'ak-brand-core' ),
			'description' => __( 'The form clients use to start a project. Briefs arrive under Briefs in the dashboard.', 'ak-brand-core' ),
			'icon'        => 'email-alt',
			'attributes'  => array( 'compact' => $b(), 'service' => $s, 'source' => $s ),
			'render'      => 'ak_bk_render_brief',
		),
		'journal'     => array(
			'title'       => __( 'Latest journal entries', 'ak-brand-core' ),
			'description' => __( 'The newest posts as cards.', 'ak-brand-core' ),
			'icon'        => 'welcome-write-blog',
			'attributes'  => array( 'count' => $n( 3 ), 'category' => $s ),
			'render'      => 'ak_bk_render_journal',
		),
	);
}

add_action(
	'init',
	static function () {
		wp_register_script(
			'ak-blocks',
			plugins_url( 'assets/blocks.js', AK_BRAND_CORE_FILE ),
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data', 'wp-core-data', 'wp-server-side-render', 'wp-plugins', 'wp-editor', 'wp-hooks' ),
			AK_BRAND_CORE_VERSION,
			true
		);
		wp_register_style( 'ak-blocks-editor', plugins_url( 'assets/blocks-editor.css', AK_BRAND_CORE_FILE ), array(), AK_BRAND_CORE_VERSION );

		foreach ( ak_bk_definitions() as $slug => $def ) {
			$render = $def['render'];
			register_block_type(
				'ak/' . $slug,
				array(
					'api_version'           => 3,
					'title'                 => $def['title'],
					'description'           => $def['description'],
					'category'              => 'ak',
					'icon'                  => $def['icon'],
					'attributes'            => $def['attributes'],
					'supports'              => array( 'html' => false, 'anchor' => false, 'customClassName' => true ),
					'editor_script_handles' => array( 'ak-blocks' ),
					'editor_style_handles'  => array( 'ak-blocks-editor' ),
					'render_callback'       => static fn( $attributes ) => $render( (array) $attributes ),
				)
			);
		}

		// Styles for core blocks, chosen in the editor's Styles panel.
		$styles = array(
			'core/paragraph' => array( 'eyebrow' => 'Eyebrow', 'lead' => 'Lead', 'plate' => 'Letter plate' ),
			'core/heading'   => array( 'display' => 'Display', 'display-xl' => 'Display XL' ),
			'core/list'      => array( 'steps' => 'Steps', 'steps-row' => 'Steps in a row', 'cards' => 'Cards', 'timeline' => 'Timeline', 'marquee' => 'Marquee', 'credits' => 'Credits' ),
			'core/group'     => array( 'section' => 'Section', 'statement' => 'Statement (AK letters)', 'panel' => 'Panel', 'cta' => 'Call to action', 'founder' => 'Founder' ),
			'core/columns'   => array( 'split' => 'Split (first column stays in view)' ),
			'core/gallery'   => array( 'masonry' => 'Masonry with lightbox' ),
			'core/video'     => array( 'film' => 'Film with play button' ),
		);
		foreach ( $styles as $block => $list ) {
			foreach ( $list as $name => $label ) {
				register_block_style( $block, array( 'name' => $name, 'label' => $label ) );
			}
		}
	}
);

/* Editor-only data: project and term lists for the Work block's pickers. */
add_action(
	'enqueue_block_editor_assets',
	static function () {
		$types = get_terms( array( 'taxonomy' => 'ak_project_type', 'hide_empty' => false ) );
		$disc  = get_terms( array( 'taxonomy' => 'ak_capability', 'hide_empty' => false ) );
		wp_localize_script(
			'ak-blocks',
			'akBlocks',
			array(
				'types'       => is_array( $types ) ? array_map( static fn( $t ) => array( 'slug' => $t->slug, 'name' => $t->name ), $types ) : array(),
				'disciplines' => is_array( $disc ) ? array_map( static fn( $t ) => array( 'slug' => $t->slug, 'name' => $t->name ), $disc ) : array(),
				'reel'        => array_values( (array) apply_filters( 'ak_brand_default_reel', array() ) ),
			)
		);
	}
);
