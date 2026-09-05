<?php
/**
 * The Website Preview module, and its graceful states.
 *
 * The module is available to ANY project. It is not a project type, and a URL
 * does not create one. For an owned platform its website is one surface of a
 * larger ecosystem, so the module is a section of the record, never its
 * subject.
 *
 * FOUR STATES, and the fourth is the one that matters:
 *
 *   AUTO         a generated capture exists and has been verified
 *   MANUAL       the owner supplied a screenshot, image set or recording
 *   LIVE         an embed of the real site, where the site permits it
 *   UNAVAILABLE  render the project from its normal editorial media, with no
 *                preview module at all
 *
 * UNAVAILABLE renders NOTHING. No frame, no plate, no placeholder, and above
 * all no message. "Capture pending", "capture failed" and "preview
 * unavailable" are administrative status, shown on the edit screen and in
 * the site health panel — never as part of the public design. A public
 * portfolio that displays its own broken tooling is worse than one that
 * simply does not show a preview.
 *
 * The automated capture is an enhancement. It must never become a dependency
 * that prevents a project from being published, which is why every resolution
 * path below can end in "no module" without anything else degrading.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve which state a project's website module is in.
 *
 * Order is deliberate: owner-supplied media outranks anything automatic,
 * because the owner is the authority on their own project and because the
 * capture service is the part that fails.
 *
 * @param int $post_id Project ID.
 * @return array{state:string,url:string,desktop:int,mobile:int,video:int,capture:string,narrative:string}
 */
function ak_website_module( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	$out = array(
		'state'     => 'unavailable',
		'url'       => (string) ak_project_meta( 'ak_url', '', $post_id ),
		'desktop'   => (int) ak_project_meta( 'ak_wp_desktop_id', 0, $post_id ),
		'mobile'    => (int) ak_project_meta( 'ak_wp_mobile_id', 0, $post_id ),
		'video'     => (int) ak_project_meta( 'ak_wp_video_id', 0, $post_id ),
		'capture'   => '',
		'narrative' => (string) ak_project_meta( 'ak_wp_narrative', '', $post_id ),
	);

	// The owner can switch the whole module off, and that is not a failure.
	if ( ! ak_project_meta( 'ak_wp_enabled', false, $post_id ) ) {
		return $out;
	}

	$mode = ak_sanitize_wp_mode( ak_project_meta( 'ak_wp_mode', 'auto', $post_id ) );
	if ( 'off' === $mode ) {
		return $out;
	}

	$has_manual = $out['desktop'] || $out['mobile'] || $out['video'];

	// MANUAL: explicitly chosen, or automatic asked for but only manual media
	// exists. Either way the visitor sees real material.
	if ( 'manual' === $mode && $has_manual ) {
		$out['state'] = 'manual';
		return $out;
	}

	if ( 'live' === $mode && $out['url'] ) {
		$out['state'] = 'live';
		return $out;
	}

	if ( 'auto' === $mode && $out['url'] && ! ak_project_meta( 'ak_wp_auto_off', false, $post_id ) ) {
		/*
		 * Verified, not merely requested. The capture service returns a
		 * placeholder image while it works, and rendering that placeholder is
		 * exactly the "capture pending" plate this module exists to avoid — so
		 * AUTO requires a stored verification, written by the capture check.
		 */
		if ( ak_project_meta( 'ak_wp_auto_ok', false, $post_id ) ) {
			$out['state']   = 'auto';
			$out['capture'] = ak_capture_url( $out['url'] );
			return $out;
		}
	}

	// Automatic failed or was never verified — fall back to owner media if any
	// exists, and to nothing at all if it does not.
	if ( $has_manual ) {
		$out['state'] = 'manual';
		return $out;
	}

	return $out;
}

/**
 * The capture service URL for a site.
 *
 * WordPress.com's mShots: it screenshots the URL, caches on their CDN and
 * re-captures periodically, at no cost to this site's performance. One lazy
 * image, no scripts.
 *
 * @param string $url  Site address.
 * @param int    $width Capture width.
 * @return string
 */
function ak_capture_url( $url, $width = 1200 ) {
	return 'https://s0.wp.com/mshots/v1/' . rawurlencode( $url ) . '?w=' . (int) $width;
}

/**
 * Verify that the capture service actually produced a capture.
 *
 * Writes `ak_wp_auto_ok`, `ak_wp_checked` and a human-readable
 * `ak_wp_status`. The status string is for the edit screen; it is never
 * printed on the front end.
 *
 * Returns false, quietly, on any failure. A capture check that cannot reach
 * the internet must not prevent a project from being published — it just
 * leaves the module in UNAVAILABLE, and the project renders from its own
 * media.
 *
 * @param int $post_id Project ID.
 * @return bool Whether a usable capture exists.
 */
function ak_verify_capture( $post_id ) {
	$url = (string) ak_project_meta( 'ak_url', '', $post_id );
	if ( ! $url ) {
		ak_record_capture( $post_id, false, __( 'No address set.', 'ak-zeyna-child' ) );
		return false;
	}

	$response = wp_remote_get(
		ak_capture_url( $url ),
		array( 'timeout' => 15, 'redirection' => 3 )
	);

	if ( is_wp_error( $response ) ) {
		ak_record_capture( $post_id, false, $response->get_error_message() );
		return false;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$type = (string) wp_remote_retrieve_header( $response, 'content-type' );
	$body = wp_remote_retrieve_body( $response );

	if ( 200 !== $code || 0 !== strpos( $type, 'image/' ) ) {
		/* translators: %d: HTTP status code */
		ak_record_capture( $post_id, false, sprintf( __( 'Capture service returned %d.', 'ak-zeyna-child' ), $code ) );
		return false;
	}

	/*
	 * mShots answers a not-yet-ready capture with a small grey placeholder
	 * image, at HTTP 200 with an image content type — so status code alone
	 * cannot tell "captured" from "still working". The placeholder is an order
	 * of magnitude smaller than any real screenshot; anything under 8 KB is
	 * treated as not ready, which keeps the placeholder off the front end.
	 */
	if ( strlen( $body ) < 8192 ) {
		ak_record_capture( $post_id, false, __( 'Capture not ready yet — the service returned its placeholder.', 'ak-zeyna-child' ) );
		return false;
	}

	ak_record_capture( $post_id, true, __( 'Capture verified.', 'ak-zeyna-child' ) );
	return true;
}

/**
 * Store the outcome of a capture check.
 *
 * @param int    $post_id Project ID.
 * @param bool   $ok      Whether a usable capture exists.
 * @param string $status  Admin-facing explanation.
 */
function ak_record_capture( $post_id, $ok, $status ) {
	update_post_meta( $post_id, 'ak_wp_auto_ok', $ok ? 1 : 0 );
	update_post_meta( $post_id, 'ak_wp_checked', time() );
	update_post_meta( $post_id, 'ak_wp_status', $status );
}

/**
 * Refresh, requested from the edit screen.
 */
add_action(
	'admin_post_ak_refresh_capture',
	function () {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		check_admin_referer( 'ak_refresh_capture_' . $post_id );
		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( esc_html__( 'You cannot refresh this capture.', 'ak-zeyna-child' ) );
		}
		ak_verify_capture( $post_id );
		wp_safe_redirect( add_query_arg( 'ak_captured', '1', get_edit_post_link( $post_id, 'raw' ) ) );
		exit;
	}
);

/**
 * A weekly sweep, so captures stay current without anyone asking.
 *
 * Only projects in AUTO with automatic capture enabled are touched, and a
 * failure only ever moves a project to UNAVAILABLE — never to a broken plate.
 */
add_action(
	'ak_capture_sweep',
	function () {
		$projects = get_posts(
			array(
				'post_type'      => AK_PROJECT_CPT,
				'posts_per_page' => 25,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				'meta_query'     => array(
					array( 'key' => 'ak_wp_enabled', 'value' => '1' ),
					array( 'key' => 'ak_wp_mode', 'value' => 'auto' ),
				),
			)
		);
		foreach ( $projects as $post_id ) {
			if ( ak_project_meta( 'ak_wp_auto_off', false, $post_id ) ) {
				continue;
			}
			ak_verify_capture( $post_id );
		}
	}
);

add_action(
	'init',
	function () {
		if ( ! wp_next_scheduled( 'ak_capture_sweep' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'weekly', 'ak_capture_sweep' );
		}
	}
);

/**
 * Deactivation must not leave the sweep scheduled against a theme that is
 * no longer there.
 */
add_action(
	'switch_theme',
	function () {
		wp_clear_scheduled_hook( 'ak_capture_sweep' );
	}
);
