<?php
/**
 * The six services as rows: number, name, the question it answers, one line,
 * and a photograph that follows the pointer on hover.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$movements = ak_studio_movements();
if ( ! $movements ) {
	return;
}
?>
<ol class="svc" data-svc>
	<?php foreach ( $movements as $i => $m ) : ?>
		<?php
		$page  = ak_studio_service_page( $m['slug'] );
		$href  = $page ? get_permalink( $page ) : ak_studio_contact_url( $m['slug'] );
		$img   = ! empty( $m['image'] ) ? ak_studio_seed_image( $m['image'] ) : 0;
		$src   = $img ? wp_get_attachment_image_url( $img, 'medium_large' ) : '';
		$line  = $page && has_excerpt( $page ) ? $page->post_excerpt : $m['line'];
		?>
		<li class="svc__item" data-rise>
			<a class="svc__link" href="<?php echo esc_url( $href ); ?>" data-svc-img="<?php echo esc_url( (string) $src ); ?>" data-cursor="<?php esc_attr_e( 'Open', 'ak-studio' ); ?>">
				<span class="svc__num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
				<span class="svc__name"><?php echo esc_html( $m['name'] ); ?></span>
				<span class="svc__q"><?php echo esc_html( $m['question'] ); ?></span>
				<span class="svc__line"><?php echo esc_html( $line ); ?></span>
				<?php echo ak_studio_arrow( 'svc__arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		</li>
	<?php endforeach; ?>
</ol>
<div class="svc__float" aria-hidden="true" data-svc-float><img alt="" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" decoding="async"></div>
