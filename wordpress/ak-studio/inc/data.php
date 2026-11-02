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
		'name'       => get_bloginfo( 'name' ),
		'short'      => 'AK',
		'promise'    => get_bloginfo( 'description' ),
		'discipline' => '',
		'email'      => get_option( 'admin_email' ),
		'city'       => 'London',
		'country'    => '',
		'booking'    => '',
	);
	return (string) ( $fallback[ $key ] ?? '' );
}

/** @return array<int,array<string,mixed>> */
function ak_studio_movements(): array {
	return function_exists( 'ak_brand_movements' ) ? ak_brand_movements() : array();
}

/** @return array<int,array<string,mixed>> */
function ak_studio_platforms(): array {
	return function_exists( 'ak_brand_platforms' ) ? ak_brand_platforms() : array();
}

/** @return array<int,array<string,mixed>> */
function ak_studio_founders(): array {
	return function_exists( 'ak_brand_founders' ) ? ak_brand_founders() : array();
}

/** @return array<int,array<string,string>> */
function ak_studio_stats(): array {
	return function_exists( 'ak_brand_stats' ) ? ak_brand_stats() : array();
}

/** @return array<int,array<string,string>> */
function ak_studio_timeline(): array {
	return function_exists( 'ak_brand_timeline' ) ? ak_brand_timeline() : array();
}

/** @return array<string,array<int,string>> */
function ak_studio_network(): array {
	return function_exists( 'ak_brand_network' ) ? ak_brand_network() : array();
}

/** @return array<string,mixed> */
function ak_studio_event(): array {
	return function_exists( 'ak_brand_event' ) ? ak_brand_event() : array( 'enabled' => false );
}

/** @return array<string,mixed> */
function ak_studio_project( $post = null ): array {
	return function_exists( 'ak_brand_project' ) ? ak_brand_project( $post ) : array();
}

/**
 * The page that holds a service, by its discipline slug.
 */
function ak_studio_service_page( string $movement ): ?WP_Post {
	static $cache = null;
	if ( null === $cache ) {
		$cache = array();
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'posts_per_page' => 20,
				'meta_key'       => '_ak_movement', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_compare'   => '!=',
				'meta_value'     => '', // phpcs:ignore WordPress.DB.SlowDBQuery
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			)
		);
		foreach ( $pages as $p ) {
			$cache[ (string) get_post_meta( $p->ID, '_ak_movement', true ) ] = $p;
		}
	}
	return $cache[ $movement ] ?? null;
}

/**
 * An image from the launch media by file name, when the plugin installed it.
 */
function ak_studio_seed_image( string $file ): int {
	return function_exists( 'ak_brand_seed_attachment' ) ? ak_brand_seed_attachment( $file ) : 0;
}

/**
 * The hero reel: real footage from the studio's platforms, bundled with the
 * theme so the first screen never depends on the database. A clip chosen in
 * the Customizer replaces the bundled one in the same slot.
 *
 * @return array<int,array<string,string>>
 */
function ak_studio_reel(): array {
	$clips = array(
		array( 'file' => 'lfd-s7-campaign', 'label' => 'London Fashion Day', 'kind' => 'Season 7 campaign · Regent Street' ),
		array( 'file' => 'lfd-s7-runway', 'label' => 'London Fashion Day', 'kind' => 'Season 7 runway · Hyde Park' ),
		array( 'file' => 'ofd-s31-campaign', 'label' => 'Odessa Fashion Day', 'kind' => 'Season 31 campaign' ),
		array( 'file' => 'lfd-westminster', 'label' => 'London Fashion Day', 'kind' => 'Campaign · Westminster Bridge' ),
	);
	$reel  = array();
	foreach ( $clips as $i => $clip ) {
		$video_id  = (int) get_theme_mod( 'ak_reel_video_' . $i, 0 );
		$poster_id = (int) get_theme_mod( 'ak_reel_poster_' . $i, 0 );
		$base      = 'assets/media/' . $clip['file'];
		$reel[]    = array(
			'video'    => $video_id ? (string) wp_get_attachment_url( $video_id ) : get_theme_file_uri( $base . '.mp4' ),
			'video_sm' => $video_id ? (string) wp_get_attachment_url( $video_id ) : get_theme_file_uri( $base . '-sm.mp4' ),
			'poster'   => $poster_id ? (string) wp_get_attachment_image_url( $poster_id, 'full' ) : get_theme_file_uri( $base . '.jpg' ),
			'label'    => $clip['label'],
			'kind'     => $clip['kind'],
		);
	}
	return (array) apply_filters( 'ak_studio_reel', $reel );
}
