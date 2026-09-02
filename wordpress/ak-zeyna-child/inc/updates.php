<?php
/**
 * Self-updates.
 *
 * The theme checks a published manifest twice a day and, when a newer build
 * exists, WordPress itself shows the standard update notice under
 * Appearance → Themes (and Dashboard → Updates) with a one-click update —
 * no more manual zip uploads. The check is silent and cached; if the
 * manifest is unreachable nothing breaks and nothing is shown.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AK_UPDATE_MANIFEST', 'https://duysha94.github.io/prominent/theme/update.json' );

/**
 * Fetch (and cache for 12h) the published update manifest.
 *
 * @return array
 */
function ak_update_manifest() {
	$cached = get_site_transient( 'ak_child_update_manifest' );
	if ( false !== $cached ) {
		return is_array( $cached ) ? $cached : array();
	}

	$data     = array();
	$response = wp_remote_get( AK_UPDATE_MANIFEST, array( 'timeout' => 8 ) );
	if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
		$decoded = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( is_array( $decoded ) ) {
			$data = $decoded;
		}
	}

	set_site_transient( 'ak_child_update_manifest', $data, 12 * HOUR_IN_SECONDS );
	return $data;
}

add_filter(
	'pre_set_site_transient_update_themes',
	function ( $transient ) {
		if ( empty( $transient ) || ! is_object( $transient ) ) {
			return $transient;
		}

		$manifest = ak_update_manifest();
		if ( empty( $manifest['version'] ) || empty( $manifest['package'] ) ) {
			return $transient;
		}

		$current = wp_get_theme( 'ak-zeyna-child' )->get( 'Version' );
		if ( $current && version_compare( $manifest['version'], $current, '>' ) ) {
			$transient->response['ak-zeyna-child'] = array(
				'theme'       => 'ak-zeyna-child',
				'new_version' => $manifest['version'],
				'url'         => ! empty( $manifest['details'] ) ? $manifest['details'] : 'https://akbrand.studio/',
				'package'     => $manifest['package'],
			);
		}

		return $transient;
	}
);
