<?php
/**
 * The theme reads the studio's data from AK Brand Core and degrades to a
 * readable minimum when the plugin is off — it never stores the data itself.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

function ak_studio_has_core(): bool {
	return function_exists( 'ak_brand_studio' );
}

/**
 * @param string $key Studio key (see AK Brand Core).
 */
function ak_studio_get( string $key ): string {
	if ( ak_studio_has_core() ) {
		return ak_brand_studio( $key );
	}
	$fallback = array(
		'name'    => get_bloginfo( 'name' ),
		'short'   => 'AK',
		'promise' => get_bloginfo( 'description' ),
		'email'   => get_option( 'admin_email' ),
		'city'    => 'London',
		'booking' => '',
	);
	return (string) ( $fallback[ $key ] ?? '' );
}

/** @return array<int,array<string,mixed>> */
function ak_studio_movements(): array {
	return function_exists( 'ak_brand_movements' ) ? ak_brand_movements() : array();
}

/** @return array<int,array<string,string>> */
function ak_studio_platforms(): array {
	return function_exists( 'ak_brand_platforms' ) ? ak_brand_platforms() : array();
}

/** @return array<int,array<string,mixed>> */
function ak_studio_founders(): array {
	return function_exists( 'ak_brand_founders' ) ? ak_brand_founders() : array();
}

/**
 * The hero reel: one clip per platform. Media-library choices from the
 * Customizer win; the bundled clips are rendered placeholders until real
 * show footage replaces them.
 *
 * @return array<int,array<string,string>>
 */
function ak_studio_reel(): array {
	$placeholders = array( 'runway-a', 'runway-b', 'silk', 'pages' );
	$platforms    = ak_studio_platforms();
	$reel         = array();
	foreach ( $placeholders as $i => $file ) {
		$video_id  = (int) get_theme_mod( 'ak_reel_video_' . $i, 0 );
		$poster_id = (int) get_theme_mod( 'ak_reel_poster_' . $i, 0 );
		$reel[]    = array(
			'video'  => $video_id ? (string) wp_get_attachment_url( $video_id ) : get_theme_file_uri( "assets/media/{$file}.mp4" ),
			'poster' => $poster_id ? (string) wp_get_attachment_image_url( $poster_id, 'full' ) : get_theme_file_uri( "assets/media/{$file}.jpg" ),
			'label'  => $platforms[ $i ]['name'] ?? '',
			'kind'   => $platforms[ $i ]['kind'] ?? '',
		);
	}
	return $reel;
}
