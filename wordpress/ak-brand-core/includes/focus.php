<?php
/**
 * Focus points: where a photograph or film stays in frame when it is cropped
 * (cards, page headers, the event, feature cards). Each image keeps its own
 * point — set at launch on the main face — and it can be changed under
 * Media → (image) → Focus point.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * A focus point as CSS object-position ("48% 20%"), or '' when none is set.
 */
function ak_brand_focus( int $id ): string {
	$focus = trim( (string) get_post_meta( $id, '_ak_focus', true ) );
	return preg_match( '/^\d{1,3}(\.\d+)?% \d{1,3}(\.\d+)?%$/', $focus ) ? $focus : '';
}

/**
 * "x% y%" from a block's {x, y} focal point (0–1), or ''.
 *
 * @param mixed $point Focal point attribute.
 */
function ak_brand_focus_from_point( $point ): string {
	if ( ! is_array( $point ) || ! isset( $point['x'], $point['y'] ) ) {
		return '';
	}
	return round( (float) $point['x'] * 100, 1 ) . '% ' . round( (float) $point['y'] * 100, 1 ) . '%';
}

/* Every cropped image the site prints through WordPress gets its focus point. */
add_filter(
	'wp_get_attachment_image_attributes',
	static function ( $attr, $attachment ) {
		$focus = $attachment instanceof WP_Post ? ak_brand_focus( $attachment->ID ) : '';
		if ( $focus && false === strpos( (string) ( $attr['style'] ?? '' ), 'object-position' ) ) {
			$attr['style'] = trim( ( $attr['style'] ?? '' ) . ';object-position:' . $focus, ';' );
		}
		return $attr;
	},
	10,
	2
);

/* The field in the media library. */
add_filter(
	'attachment_fields_to_edit',
	static function ( $fields, $post ) {
		if ( ! wp_attachment_is_image( $post ) && ! wp_attachment_is( 'video', $post ) ) {
			return $fields;
		}
		$fields['ak_focus'] = array(
			'label' => __( 'Focus point', 'ak-brand-core' ),
			'input' => 'text',
			'value' => (string) get_post_meta( $post->ID, '_ak_focus', true ),
			'helps' => __( 'What stays in frame when the image is cropped, from the left and from the top — e.g. “50% 20%” keeps a face near the top centre. Empty: the centre.', 'ak-brand-core' ),
		);
		return $fields;
	},
	10,
	2
);
add_filter(
	'attachment_fields_to_save',
	static function ( $post, $attachment ) {
		if ( isset( $attachment['ak_focus'] ) ) {
			$v = trim( preg_replace( '/\s+/', ' ', str_replace( ',', ' ', (string) $attachment['ak_focus'] ) ) );
			if ( preg_match( '/^(\d{1,3}(?:\.\d+)?)%? (\d{1,3}(?:\.\d+)?)%?$/', $v, $m ) ) {
				update_post_meta( (int) $post['ID'], '_ak_focus', min( 100, (float) $m[1] ) . '% ' . min( 100, (float) $m[2] ) . '%' );
			} elseif ( '' === $v ) {
				delete_post_meta( (int) $post['ID'], '_ak_focus' );
			}
		}
		return $post;
	},
	10,
	2
);

/* Sharper resized photographs. */
add_filter( 'wp_editor_set_quality', static fn( $q, $mime = '' ) => 'image/jpeg' === $mime || 'image/webp' === $mime ? 90 : $q, 10, 2 );
add_filter( 'jpeg_quality', static fn() => 90 );
