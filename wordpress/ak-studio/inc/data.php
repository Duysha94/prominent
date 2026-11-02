<?php
/**
 * The theme reads the studio's details from AK Brand Core and degrades to a
 * readable minimum when the plugin is off — it never stores them itself.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

function ak_studio_has_core(): bool {
	return function_exists( 'ak_brand_studio' );
}

/**
 * A studio detail (name, email, city, legal_name …), see AK Brand Core.
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
	);
	return (string) ( $fallback[ $key ] ?? '' );
}

/**
 * The footage bundled with the theme. The Hero reel block plays it until
 * films are chosen in the block's sidebar.
 *
 * @return array<int,array<string,string>>
 */
function ak_studio_reel(): array {
	$clips = array(
		array( 'file' => 'lfd-s7-campaign', 'label' => 'Photo & film', 'kind' => 'London Fashion Day · Season 7 campaign', 'focus' => '55% 35%' ),
		array( 'file' => 'lfd-s7-runway', 'label' => 'Shows & events', 'kind' => 'London Fashion Day · Season 7', 'focus' => '50% 45%' ),
		array( 'file' => 'ofd-s31-campaign', 'label' => 'Photo & film', 'kind' => 'Odessa Fashion Day · Season 31 campaign', 'focus' => '30% 30%' ),
		array( 'file' => 'lfd-westminster', 'label' => 'Photo & film', 'kind' => 'Campaign film · Westminster Bridge', 'focus' => '55% 30%' ),
	);
	$reel  = array();
	foreach ( $clips as $clip ) {
		$base   = 'assets/media/' . $clip['file'];
		$reel[] = array(
			'video'   => get_theme_file_uri( $base . '.mp4' ),
			'videoSm' => get_theme_file_uri( $base . '-sm.mp4' ),
			'poster'  => get_theme_file_uri( $base . '.jpg' ),
			'label'   => $clip['label'],
			'kind'    => $clip['kind'],
			'focus'   => $clip['focus'],
		);
	}
	return $reel;
}
add_filter( 'ak_brand_default_reel', static fn( $clips ) => $clips ? $clips : ak_studio_reel() );
