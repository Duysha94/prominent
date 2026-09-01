<?php
/**
 * The band: checkable studio facts on a pure-CSS marquee.
 *
 * The content is duplicated once for the loop; the duplicate is aria-hidden
 * and disappears entirely under reduced motion, where the band becomes a
 * static wrapping row.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ak_facts = array(
	array( 'Founded', 'London Fashion Day' ),
	array( 'Founded', 'Odessa Fashion Day' ),
	array( 'Working across', 'London · Paris · Dubai' ),
	array( 'Practice', 'Strategy · Identity · Production · Presence' ),
);
$ak_band_id = 'ak-band-' . wp_unique_id();
?>
<div class="ak-band" role="marquee" aria-label="<?php esc_attr_e( 'Studio facts', 'ak-zeyna-child' ); ?>">
	<input type="checkbox" class="ak-band__pause" id="<?php echo esc_attr( $ak_band_id ); ?>" />
	<div class="ak-band__track">
		<?php for ( $ak_copy = 0; $ak_copy < 2; $ak_copy++ ) : ?>
			<div <?php echo $ak_copy ? 'class="ak-band__copy" aria-hidden="true"' : 'class="ak-band__half"'; ?>>
				<?php foreach ( $ak_facts as $ak_fact ) : ?>
					<span class="ak-band__item">
						<span class="ak-band__key"><?php echo esc_html( $ak_fact[0] ); ?></span>
						<span class="ak-band__value"><?php echo esc_html( $ak_fact[1] ); ?></span>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endfor; ?>
	</div>
	<label class="ak-band__toggle" for="<?php echo esc_attr( $ak_band_id ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Pause or play the moving band', 'ak-zeyna-child' ); ?></span></label>
</div>
