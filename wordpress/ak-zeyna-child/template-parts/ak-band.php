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

/*
 * Checkable facts only, and the practice line is read from the factual layer.
 *
 * It was hard-coded as "Strategy · Identity · Production · Presence" — the old
 * four — so a band that scrolls across About and other routes was still
 * announcing a practice two thirds the real size, with photography, film,
 * shows and PR nowhere in it. Sixth place for a stale string to hide.
 */
$ak_facts = array(
	array( 'Platform', 'London Fashion Day' ),
	array( 'Platform', 'Odessa Fashion Day' ),
	array( 'Working across', ak_studio( 'cities' ) ),
	array( 'Practice', implode( ' · ', wp_list_pluck( ak_movements(), 'name' ) ) ),
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
