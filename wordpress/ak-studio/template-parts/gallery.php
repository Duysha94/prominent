<?php
/**
 * A project's photographs, opening full screen on click.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$ids = array_values( array_filter( array_map( 'absint', (array) ( $args['ids'] ?? array() ) ) ) );
if ( ! $ids ) {
	return;
}
?>
<section class="gallery section" aria-label="<?php esc_attr_e( 'Photographs', 'ak-studio' ); ?>">
	<div class="wrap">
		<div class="gallery__grid" data-gallery>
			<?php foreach ( $ids as $i => $id ) : ?>
				<?php
				$full    = wp_get_attachment_image_url( $id, 'full' );
				$caption = wp_get_attachment_caption( $id );
				$meta    = wp_get_attachment_metadata( $id );
				$wide    = $meta && ! empty( $meta['width'] ) && $meta['width'] > $meta['height'];
				?>
				<figure class="gallery__item<?php echo $wide ? ' gallery__item--wide' : ''; ?>" data-reveal>
					<a href="<?php echo esc_url( (string) $full ); ?>" data-lightbox data-cursor="<?php esc_attr_e( 'Zoom', 'ak-studio' ); ?>">
						<?php echo ak_studio_img( $id, $wide ? '(max-width: 900px) 92vw, 62vw' : '(max-width: 600px) 92vw, (max-width: 1200px) 46vw, 31vw' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
					<?php if ( $caption ) : ?>
						<figcaption><?php echo esc_html( $caption ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
	<dialog class="lightbox" data-lightbox-dialog aria-label="<?php esc_attr_e( 'Photograph', 'ak-studio' ); ?>">
		<img alt="" data-lightbox-img>
		<p class="lightbox__cap" data-lightbox-cap></p>
		<button type="button" class="lightbox__close" data-lightbox-close><?php esc_html_e( 'Close', 'ak-studio' ); ?></button>
		<button type="button" class="lightbox__nav lightbox__nav--prev" data-lightbox-prev aria-label="<?php esc_attr_e( 'Previous', 'ak-studio' ); ?>">←</button>
		<button type="button" class="lightbox__nav lightbox__nav--next" data-lightbox-next aria-label="<?php esc_attr_e( 'Next', 'ak-studio' ); ?>">→</button>
	</dialog>
</section>
