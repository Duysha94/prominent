<?php
/**
 * The network around the seasons, as slow marquees of names.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$network = ak_studio_network();
if ( ! $network ) {
	return;
}
?>
<section class="network section">
	<div class="wrap">
		<?php ak_studio_section_head( __( 'Who we work alongside', 'ak-studio' ), __( 'Colleges, crews, *platforms and press.*', 'ak-studio' ) ); ?>
	</div>
	<?php $dir = 1; ?>
	<?php foreach ( $network as $group => $names ) : ?>
		<?php
		$row = '';
		foreach ( $names as $n ) {
			$row .= '<span>' . esc_html( $n ) . '</span><i aria-hidden="true">✦</i>';
		}
		$dir = -$dir;
		?>
		<div class="network__row">
			<p class="network__label wrap"><?php echo esc_html( $group ); ?></p>
			<div class="marquee<?php echo $dir > 0 ? ' marquee--reverse' : ''; ?>">
				<div class="marquee__track"><?php echo $row . $row; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></div>
			</div>
			<ul class="sr-only"><?php foreach ( $names as $n ) : ?><li><?php echo esc_html( $n ); ?></li><?php endforeach; ?></ul>
		</div>
	<?php endforeach; ?>
</section>
