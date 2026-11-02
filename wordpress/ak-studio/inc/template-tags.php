<?php
/**
 * Small rendering helpers shared by the templates.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Display heading: escaped, *word* becomes the italic serif accent, new lines become breaks.
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
	$url  = $page ? get_permalink( $page ) : home_url( '/#brief' );
	return $need ? add_query_arg( 'need', $need, $url ) : $url;
}

/**
 * A responsive image.
 *
 * @param int                  $id    Attachment.
 * @param string               $sizes sizes attribute.
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
 * Lead paragraph of a page: its excerpt when set.
 */
function ak_studio_lede( ?WP_Post $post = null ): string {
	$post = $post ?: get_post();
	return $post && has_excerpt( $post ) ? (string) $post->post_excerpt : '';
}

/**
 * Page header: eyebrow, display heading, lead paragraph and optional media.
 *
 * @param array<string,mixed> $args eyebrow, heading, lede, image (id), extra (html), class, meta (html).
 */
function ak_studio_page_header( array $args = array() ): void {
	$post = get_post();
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow' => $post ? (string) get_post_meta( $post->ID, '_ak_eyebrow', true ) : '',
			'heading' => $post ? ( (string) get_post_meta( $post->ID, '_ak_heading', true ) ?: get_the_title( $post ) ) : '',
			'lede'    => $post ? ak_studio_lede( $post ) : '',
			'image'   => 0,
			'extra'   => '',
			'meta'    => '',
			'class'   => '',
		)
	);
	?>
	<header class="page-head <?php echo esc_attr( $args['class'] ); ?><?php echo $args['image'] ? ' page-head--media' : ''; ?>">
		<?php if ( $args['image'] ) : ?>
			<div class="page-head__media" aria-hidden="true" data-parallax="12">
				<?php echo ak_studio_img( (int) $args['image'], '100vw', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => '' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="page-head__scrim"></div>
			</div>
		<?php endif; ?>
		<div class="wrap page-head__inner">
			<?php if ( $args['eyebrow'] ) : ?>
				<p class="eyebrow" data-rise><?php echo esc_html( $args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="display page-head__title" data-split><?php echo ak_studio_heading( (string) $args['heading'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?></h1>
			<?php if ( $args['lede'] || $args['meta'] ) : ?>
				<div class="page-head__foot" data-rise>
					<?php if ( $args['lede'] ) : ?>
						<p class="lede"><?php echo esc_html( $args['lede'] ); ?></p>
					<?php endif; ?>
					<?php echo $args['meta']; // phpcs:ignore WordPress.Security.EscapeOutput -- built by templates from escaped parts. ?>
				</div>
			<?php endif; ?>
			<?php echo $args['extra']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</header>
	<?php
}

/**
 * Section heading row.
 *
 * @param array<string,string> $link url, label.
 */
function ak_studio_section_head( string $eyebrow, string $title, array $link = array(), string $class = '' ): void {
	?>
	<div class="section-head <?php echo esc_attr( $class ); ?>">
		<p class="eyebrow" data-rise><?php echo esc_html( $eyebrow ); ?></p>
		<h2 class="display section-head__title" data-split><?php echo ak_studio_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<?php if ( ! empty( $link['url'] ) ) : ?>
			<a class="link-arrow" href="<?php echo esc_url( $link['url'] ); ?>" data-rise><?php echo esc_html( $link['label'] ); ?><?php echo ak_studio_arrow( 'link-arrow__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * A project card.
 */
function ak_studio_project_card( WP_Post $post, string $class = '', string $sizes = '(max-width: 900px) 92vw, 46vw' ): void {
	$p     = ak_studio_project( $post );
	$short = (string) get_post_meta( $post->ID, 'ak_short_title', true );
	$label = $p['upcoming'] ?? false ? __( 'Upcoming', 'ak-studio' ) : ( $p['year'] ?? '' );
	?>
	<article class="card <?php echo esc_attr( $class ); ?>" data-type="<?php echo esc_attr( $p['type_slug'] ?? '' ); ?>" data-platform="<?php echo esc_attr( false !== stripos( $post->post_name, 'odessa' ) || false !== stripos( $post->post_name, 'ofd' ) ? 'ofd' : ( false !== stripos( $post->post_name, 'london-fashion-day' ) ? 'lfd' : 'brand' ) ); ?>">
		<a class="card__link" href="<?php echo esc_url( get_permalink( $post ) ); ?>" data-cursor="<?php esc_attr_e( 'View', 'ak-studio' ); ?>">
			<div class="card__media" data-reveal>
				<?php echo ak_studio_img( (int) get_post_thumbnail_id( $post ), $sizes, array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( $label ) : ?><span class="card__tag"><?php echo esc_html( $label ); ?></span><?php endif; ?>
			</div>
			<div class="card__body">
				<p class="card__meta"><?php echo esc_html( implode( ' · ', array_filter( array( $p['type'] ?? '', $p['location'] ?? '' ) ) ) ); ?></p>
				<h3 class="card__title"><?php echo esc_html( $short ?: get_the_title( $post ) ); ?></h3>
				<p class="card__text"><?php echo esc_html( get_the_excerpt( $post ) ); ?></p>
			</div>
		</a>
	</article>
	<?php
}

/**
 * A journal card.
 */
function ak_studio_post_card( WP_Post $post, string $class = '' ): void {
	$cats = get_the_category( $post->ID );
	?>
	<article class="post-card <?php echo esc_attr( $class ); ?>">
		<a class="post-card__link" href="<?php echo esc_url( get_permalink( $post ) ); ?>" data-cursor="<?php esc_attr_e( 'Read', 'ak-studio' ); ?>">
			<div class="post-card__media" data-reveal>
				<?php echo ak_studio_img( (int) get_post_thumbnail_id( $post ), '(max-width: 900px) 92vw, 30vw', array( 'alt' => '' ), 'medium_large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<p class="post-card__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c', $post ) ); ?>"><?php echo esc_html( get_the_date( 'j M Y', $post ) ); ?></time><?php echo $cats ? ' · ' . esc_html( $cats[0]->name ) : ''; ?></p>
			<h3 class="post-card__title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
			<p class="post-card__text"><?php echo esc_html( get_the_excerpt( $post ) ); ?></p>
		</a>
	</article>
	<?php
}

/**
 * The closing call to action used at the foot of most pages.
 */
function ak_studio_cta( string $need = '', string $title = '' ): void {
	get_template_part( 'template-parts/cta', null, array( 'need' => $need, 'title' => $title ) );
}

/**
 * The studio's profiles. Instagram, Facebook and LinkedIn are always named,
 * as plain text until their address is set in Settings → AK Studio; TikTok
 * appears only once it has an address.
 *
 * @return array<string,string> label => url ('' while not set)
 */
function ak_studio_profiles(): array {
	$out = array();
	foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'tiktok' => 'TikTok' ) as $key => $label ) {
		$url = ak_studio_get( $key );
		if ( $url || 'tiktok' !== $key ) {
			$out[ $label ] = $url;
		}
	}
	return $out;
}
