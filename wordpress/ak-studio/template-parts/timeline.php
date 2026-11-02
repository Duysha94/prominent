<?php
/**
 * The record, year by year.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$items = ak_studio_timeline();
if ( ! $items ) {
	return;
}
?>
<section class="timeline section">
	<div class="wrap">
		<?php ak_studio_section_head( __( 'The record', 'ak-studio' ), __( 'Sixteen years *on the runway.*', 'ak-studio' ) ); ?>
		<ol class="timeline__list">
			<?php foreach ( $items as $item ) : ?>
				<li class="timeline__item" data-rise>
					<span class="timeline__year"><?php echo esc_html( $item['year'] ); ?></span>
					<div class="timeline__body">
						<h3><?php echo esc_html( $item['title'] ); ?></h3>
						<p><?php echo esc_html( $item['text'] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
