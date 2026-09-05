<?php
/**
 * Project meta.
 *
 * Registered through register_post_meta so everything is typed, sanitised,
 * revisioned and REST-addressable — no ACF, and no plugin dependency the
 * project data does not need.
 *
 * Modules are one ordered JSON array rather than a page builder. The visual
 * system governs every project no matter how it was assembled, and a project
 * that genuinely has only two sections has only two sections.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Every project meta key: type, sanitiser, and the panel it belongs to.
 *
 * `panel` drives the conditional editor. `always` fields show for every
 * project type, including none.
 *
 * @return array[]
 */
function ak_project_meta_fields() {
	return array(
		// Always visible.
		'ak_short_title'   => array( 'type' => 'string', 'panel' => 'always', 'label' => __( 'Short title', 'ak-zeyna-child' ), 'sanitize' => 'sanitize_text_field' ),
		'ak_code'          => array( 'type' => 'string', 'panel' => 'always', 'label' => __( 'Project code', 'ak-zeyna-child' ), 'sanitize' => 'sanitize_text_field', 'help' => __( 'Left blank, one is generated from the relationship and a sequence.', 'ak-zeyna-child' ) ),
		'ak_owner'         => array( 'type' => 'string', 'panel' => 'always', 'label' => __( 'Owner or client', 'ak-zeyna-child' ), 'sanitize' => 'sanitize_text_field' ),
		'ak_year'          => array( 'type' => 'string', 'panel' => 'always', 'label' => __( 'Year', 'ak-zeyna-child' ), 'sanitize' => 'sanitize_text_field' ),
		'ak_location'      => array( 'type' => 'string', 'panel' => 'always', 'label' => __( 'Location', 'ak-zeyna-child' ), 'sanitize' => 'sanitize_text_field' ),
		'ak_url'           => array( 'type' => 'string', 'panel' => 'always', 'label' => __( 'Live address', 'ak-zeyna-child' ), 'sanitize' => 'esc_url_raw', 'help' => __( 'An address identifies the project. It does not make it a website project and it does not set the project type.', 'ak-zeyna-child' ) ),
		'ak_featured'      => array( 'type' => 'boolean', 'panel' => 'always', 'label' => __( 'Feature on the homepage', 'ak-zeyna-child' ) ),
		'ak_fixture'       => array( 'type' => 'boolean', 'panel' => 'always', 'label' => __( 'Internal fixture — never public', 'ak-zeyna-child' ), 'help' => __( 'Excluded from the Work index, filters, filter counts, related work, the homepage, feeds and the sitemap. Reachable only while logged in.', 'ak-zeyna-child' ) ),

		// Website preview module — available to every project type.
		'ak_wp_enabled'    => array( 'type' => 'boolean', 'panel' => 'website', 'label' => __( 'Show the website module', 'ak-zeyna-child' ) ),
		'ak_wp_mode'       => array( 'type' => 'string', 'panel' => 'website', 'label' => __( 'Preview source', 'ak-zeyna-child' ), 'sanitize' => 'ak_sanitize_wp_mode', 'default' => 'auto' ),
		'ak_wp_desktop_id' => array( 'type' => 'integer', 'panel' => 'website', 'label' => __( 'Desktop preview (manual)', 'ak-zeyna-child' ) ),
		'ak_wp_mobile_id'  => array( 'type' => 'integer', 'panel' => 'website', 'label' => __( 'Mobile preview (manual)', 'ak-zeyna-child' ) ),
		'ak_wp_video_id'   => array( 'type' => 'integer', 'panel' => 'website', 'label' => __( 'Screen recording (manual)', 'ak-zeyna-child' ) ),
		'ak_wp_auto_off'   => array( 'type' => 'boolean', 'panel' => 'website', 'label' => __( 'Disable automatic capture', 'ak-zeyna-child' ) ),
		'ak_wp_auto_ok'    => array( 'type' => 'boolean', 'panel' => 'website', 'label' => __( 'Automatic capture verified', 'ak-zeyna-child' ), 'readonly' => true ),
		'ak_wp_checked'    => array( 'type' => 'integer', 'panel' => 'website', 'label' => __( 'Last checked', 'ak-zeyna-child' ), 'readonly' => true ),
		'ak_wp_status'     => array( 'type' => 'string', 'panel' => 'website', 'label' => __( 'Last capture result', 'ak-zeyna-child' ), 'sanitize' => 'sanitize_text_field', 'readonly' => true ),
		'ak_wp_narrative'  => array( 'type' => 'string', 'panel' => 'website', 'label' => __( 'Digital narrative', 'ak-zeyna-child' ), 'sanitize' => 'wp_kses_post' ),

		// Modules — the ordered JSON array.
		'ak_modules'       => array( 'type' => 'string', 'panel' => 'modules', 'label' => __( 'Modules', 'ak-zeyna-child' ), 'sanitize' => 'ak_sanitize_modules' ),

		// Narrative types.
		'ak_context'       => array( 'type' => 'string', 'panel' => 'narrative', 'label' => __( 'Context', 'ak-zeyna-child' ), 'sanitize' => 'wp_kses_post' ),
		'ak_positioning'   => array( 'type' => 'string', 'panel' => 'narrative', 'label' => __( 'Positioning', 'ak-zeyna-child' ), 'sanitize' => 'wp_kses_post' ),

		// Image / motion types.
		'ak_gallery'       => array( 'type' => 'string', 'panel' => 'image', 'label' => __( 'Gallery (attachment IDs)', 'ak-zeyna-child' ), 'sanitize' => 'ak_sanitize_id_list' ),
		'ak_film_id'       => array( 'type' => 'integer', 'panel' => 'motion', 'label' => __( 'Film', 'ak-zeyna-child' ) ),
		'ak_film_poster'   => array( 'type' => 'integer', 'panel' => 'motion', 'label' => __( 'Film poster', 'ak-zeyna-child' ) ),

		// Event / fashion production.
		'ak_event_date'    => array( 'type' => 'string', 'panel' => 'event', 'label' => __( 'Date', 'ak-zeyna-child' ), 'sanitize' => 'sanitize_text_field' ),
		'ak_event_venue'   => array( 'type' => 'string', 'panel' => 'event', 'label' => __( 'Venue', 'ak-zeyna-child' ), 'sanitize' => 'sanitize_text_field' ),
		'ak_event_role'    => array( 'type' => 'string', 'panel' => 'event', 'label' => __( 'AK role', 'ak-zeyna-child' ), 'sanitize' => 'sanitize_text_field' ),

		// Credits, for every mode that has them.
		'ak_credits'       => array( 'type' => 'string', 'panel' => 'always', 'label' => __( 'Credits', 'ak-zeyna-child' ), 'sanitize' => 'ak_sanitize_credits', 'help' => __( 'One per line, as Role: Name.', 'ak-zeyna-child' ) ),
	);
}

/**
 * Preview source modes.
 *
 * @return string[]
 */
function ak_wp_modes() {
	return array(
		'auto'   => __( 'Automatic capture', 'ak-zeyna-child' ),
		'manual' => __( 'Owner-supplied media', 'ak-zeyna-child' ),
		'live'   => __( 'Live embed', 'ak-zeyna-child' ),
		'off'    => __( 'No website module', 'ak-zeyna-child' ),
	);
}

/**
 * Sanitise the preview mode to a known value.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function ak_sanitize_wp_mode( $value ) {
	$value = is_string( $value ) ? $value : '';
	return array_key_exists( $value, ak_wp_modes() ) ? $value : 'auto';
}

/**
 * Sanitise a comma-separated list of attachment IDs.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function ak_sanitize_id_list( $value ) {
	$ids = array_filter( array_map( 'absint', preg_split( '/[^0-9]+/', (string) $value ) ) );
	return implode( ',', $ids );
}

/**
 * Sanitise credits: one `Role: Name` per line, nothing else.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function ak_sanitize_credits( $value ) {
	$lines = preg_split( '/\R/', (string) $value );
	$out   = array();
	foreach ( $lines as $line ) {
		$line = sanitize_text_field( trim( $line ) );
		if ( '' !== $line ) {
			$out[] = $line;
		}
	}
	return implode( "\n", $out );
}

/**
 * Sanitise the module array.
 *
 * Only module types the theme can render survive, so a hand-edited or
 * imported value cannot smuggle an arbitrary block into the page.
 *
 * @param mixed $value Raw JSON.
 * @return string
 */
function ak_sanitize_modules( $value ) {
	$decoded = json_decode( (string) $value, true );
	if ( ! is_array( $decoded ) ) {
		return '';
	}
	$allowed = array_keys( ak_module_types() );
	$clean   = array();
	foreach ( $decoded as $module ) {
		if ( ! is_array( $module ) || empty( $module['type'] ) || ! in_array( $module['type'], $allowed, true ) ) {
			continue;
		}
		$entry = array( 'type' => $module['type'] );
		if ( isset( $module['text'] ) ) {
			$entry['text'] = wp_kses_post( $module['text'] );
		}
		if ( isset( $module['title'] ) ) {
			$entry['title'] = sanitize_text_field( $module['title'] );
		}
		if ( isset( $module['ids'] ) ) {
			$entry['ids'] = array_values( array_filter( array_map( 'absint', (array) $module['ids'] ) ) );
		}
		if ( isset( $module['id'] ) ) {
			$entry['id'] = absint( $module['id'] );
		}
		$clean[] = $entry;
	}
	return $clean ? wp_json_encode( $clean ) : '';
}

/**
 * The renderable module types.
 *
 * @return string[] type => label
 */
function ak_module_types() {
	return array(
		'statement'  => __( 'Text statement', 'ak-zeyna-child' ),
		'image_full' => __( 'Full image', 'ak-zeyna-child' ),
		'image_pair' => __( 'Image pair', 'ak-zeyna-child' ),
		'gallery'    => __( 'Gallery', 'ak-zeyna-child' ),
		'film'       => __( 'Film', 'ak-zeyna-child' ),
		'website'    => __( 'Website preview', 'ak-zeyna-child' ),
		'credits'    => __( 'Credits', 'ak-zeyna-child' ),
		'event'      => __( 'Event information', 'ak-zeyna-child' ),
	);
}

/**
 * Register every field.
 */
add_action(
	'init',
	function () {
		foreach ( ak_project_meta_fields() as $key => $field ) {
			register_post_meta(
				AK_PROJECT_CPT,
				$key,
				array(
					'type'              => $field['type'],
					'single'            => true,
					'show_in_rest'      => true,
					'default'           => isset( $field['default'] ) ? $field['default'] : ( 'boolean' === $field['type'] ? false : ( 'integer' === $field['type'] ? 0 : '' ) ),
					'sanitize_callback' => isset( $field['sanitize'] ) ? $field['sanitize'] : null,
					'auth_callback'     => function ( $allowed, $meta_key, $post_id ) {
						return current_user_can( 'edit_post', $post_id );
					},
				)
			);
		}
	},
	6
);

/**
 * A project meta value with a default.
 *
 * @param string $key     Meta key.
 * @param mixed  $default Fallback.
 * @param int    $post_id Project ID.
 * @return mixed
 */
function ak_project_meta( $key, $default = '', $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$value   = get_post_meta( $post_id, $key, true );
	return ( '' === $value || null === $value ) ? $default : $value;
}
