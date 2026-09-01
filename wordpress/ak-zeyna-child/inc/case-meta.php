<?php
/**
 * Case-study meta, read with fallbacks.
 *
 * The import file populates these keys; every reader below survives their
 * absence, so a case created by hand in the admin renders sensibly from the
 * moment it has only a title.
 *
 * Keys: ak_headline, ak_category, ak_year, ak_lead, ak_movements (comma
 * list), ak_summary, ak_measures (JSON [{key,value}]), ak_chapters (JSON
 * [{title,body,mkey,mval}]), ak_position (JSON {statement, rejected[]}).
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A single meta string with a default.
 *
 * @param string $key     Meta key.
 * @param string $default Fallback.
 * @param int    $post_id Optional post ID.
 * @return string
 */
function ak_meta( $key, $default = '', $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$value   = get_post_meta( $post_id, $key, true );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * JSON meta decoded to an array, or the default when absent or malformed.
 *
 * @param string $key     Meta key.
 * @param array  $default Fallback.
 * @param int    $post_id Optional post ID.
 * @return array
 */
function ak_meta_json( $key, $default = array(), $post_id = 0 ) {
	$raw = ak_meta( $key, '', $post_id );
	if ( '' === $raw ) {
		return $default;
	}
	$decoded = json_decode( $raw, true );
	return is_array( $decoded ) ? $decoded : $default;
}

/**
 * Movements as a list of labels.
 *
 * @param int $post_id Optional post ID.
 * @return string[]
 */
function ak_case_movements( $post_id = 0 ) {
	$raw = ak_meta( 'ak_movements', '', $post_id );
	if ( '' === $raw ) {
		return array();
	}
	return array_filter( array_map( 'trim', explode( ',', $raw ) ) );
}

/**
 * Echo the measure-frame HUD for a set of measures.
 *
 * @param array  $measures [{key,value}] pairs.
 * @param string $label    Optional caption on the top dimension line.
 */
function ak_measure_hud( $measures, $label = '' ) {
	?>
	<div class="ak-measure-hud" aria-hidden="true">
		<span class="ak-tick ak-tick--tl"></span><span class="ak-tick ak-tick--tr"></span>
		<span class="ak-tick ak-tick--bl"></span><span class="ak-tick ak-tick--br"></span>
		<span class="ak-dim ak-dim--top"></span>
		<?php if ( $label ) : ?>
			<span class="ak-dim-label"><?php echo esc_html( $label ); ?></span>
		<?php endif; ?>
		<?php
		$i = 0;
		foreach ( (array) $measures as $m ) {
			if ( empty( $m['key'] ) || empty( $m['value'] ) ) {
				continue;
			}
			$side = ( 0 === $i % 2 ) ? 'right' : 'left';
			$top  = 28 + ( intdiv( $i, 2 ) * 26 ) + ( ( 0 === $i % 2 ) ? 0 : 13 );
			printf(
				'<span class="ak-callout ak-callout--%1$s" style="top:%2$d%%"><i class="ak-callout__leader"></i><b class="ak-callout__key">%3$s</b><b class="ak-callout__value">%4$s</b></span>',
				esc_attr( $side ),
				(int) $top,
				esc_html( $m['key'] ),
				esc_html( $m['value'] )
			);
			$i++;
		}
		?>
	</div>
	<?php
}
