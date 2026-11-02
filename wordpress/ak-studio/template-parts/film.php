<?php
/**
 * A film with sound, played on request.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$id = (int) ( $args['id'] ?? 0 );
if ( ! $id ) {
	return;
}
$src    = wp_get_attachment_url( $id );
$poster = get_the_post_thumbnail_url( $id, 'full' );
$cap    = wp_get_attachment_caption( $id );
if ( ! $src ) {
	return;
}
?>
<section class="film section" aria-label="<?php esc_attr_e( 'Film', 'ak-studio' ); ?>">
	<div class="wrap">
		<figure class="film__frame" data-film>
			<video preload="none" playsinline <?php echo $poster ? 'poster="' . esc_url( $poster ) . '"' : ''; ?> src="<?php echo esc_url( $src ); ?>"></video>
			<button type="button" class="film__play" data-film-play data-cursor="<?php esc_attr_e( 'Play', 'ak-studio' ); ?>">
				<span class="film__icon" aria-hidden="true"></span>
				<span class="film__label"><?php esc_html_e( 'Play the film', 'ak-studio' ); ?></span>
			</button>
			<?php if ( $cap ) : ?>
				<figcaption><?php echo esc_html( $cap ); ?></figcaption>
			<?php endif; ?>
		</figure>
	</div>
</section>
