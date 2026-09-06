<?php
/**
 * Content types, activation wiring, and the menu-borne mode switch.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The portfolio fallback used to live here.
 *
 * It registered a `portfolio` post type on /work/ so that a WXR import landed
 * somewhere real on a site without Pe Core. The deployment engine replaced the
 * import, and the AK Core Project model (inc/projects/model.php) now owns
 * /work/ properly — with the three taxonomies the work actually needs rather
 * than one flat `project-categories`.
 *
 * Registering both meant two post types called Work fighting over one archive
 * slug, and two Work entries in the admin menu. On a full Zeyna install Pe Core
 * still registers its own `portfolio`; that is a foreign post type, the
 * deployment scope protects it, and it no longer collides because it keeps its
 * own archive.
 */

/**
 * Content wiring used to live here.
 *
 * `ak_wire_imported_content()` and `ak_adopt_imported_content()` pointed the
 * front page, posts page and menu location at content that arrived through a
 * WXR import, and reclaimed slugs the importer had suffixed. Both are gone:
 * the deployment engine does that work from the manifest, keyed on seed keys
 * rather than on slugs and titles, and it runs on every release rather than
 * once at import. Leaving them would have meant two systems writing
 * `page_on_front` from different sources.
 */

/*
 * THE REDUX CHROME FILTER IS GONE.
 *
 * `ak_force_redux_chrome()` used to filter `option_pe-redux` on every read,
 * pinning eight keys: `header_type` and `footer_template` back to `default`
 * so a demo import could not replace the studio's header and footer with
 * Elementor templates, and six transition keys so Zeyna's Redux-built overlay
 * came out as one plain sheet instead of a demo logo and caption on the
 * parent's dark background. `page_loader` was forced off for the same reason.
 *
 * Every one of those was a way of taming a parent that is now gone:
 *
 *   · header.php and footer.php are the child's own. Neither calls
 *     `zeyna_footer_template()` any more, and neither reads a Redux key, so
 *     `header_type` and `footer_template` have no reader on the frontend.
 *   · the transition element is `.ak-transition`, printed by header.php and
 *     animated by assets/js/ak-nav.js. `zeyna_page_transitions()` is never
 *     called, so `transition_type`, `transition_direction`, `transitions_*`
 *     and `transition_elements*` have no reader either. The old comment here
 *     said the `.page--transitions` element "must stay" because Zeyna calls
 *     `barba.init()` only when it finds one; Barba is gone, so that
 *     requirement went with it.
 *   · the loader is `ak_page_loader()`. `zeyna_page_loader()` is never
 *     called, so `page_loader` has no reader.
 *
 * A filter that pins settings nothing reads is not neutral. It made every
 * read of a plugin-owned option report values that are not in the database,
 * which is why the deployment engine had to detach it for raw reads (see
 * ak_redux_raw() in inc/deployment/scope.php). Removing it is the honest
 * end state: the AK frontend does not read Zeyna's configuration at all, so
 * it has nothing to force.
 *
 * WHAT DELIBERATELY REMAINS: the deployment engine still knows how to
 * RECOGNISE Redux data — ak_capture_redux_templates() records which
 * elementor_library posts the demo config pointed at, and ak_purge_unmanaged()
 * clears demo branding out of `pe-redux` when it finds import evidence. That
 * is legacy cleanup on a site that once ran the demo, not a rendering
 * dependency. Nothing on the frontend consults it.
 */

/**
 * Customizer: the two things the founders swap themselves.
 *
 * The logo goes through WordPress's own custom-logo control (Zeyna renders
 * it), so only the hero video needs a home. Two uploads — AV1-in-mp4 and an
 * H.264 fallback — surfaced under one panel so nothing requires FTP.
 */
add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'ak_studio',
			array(
				'title'       => __( 'AK Studio', 'ak-zeyna-child' ),
				'priority'    => 30,
				'description' => __( 'Two logotypes, one per mode. Leave either empty and the site logo set under Site Identity is used for both — which is why a single dark wordmark otherwise appears in dark mode too.', 'ak-zeyna-child' ),
			)
		);

		foreach ( array(
			'ak_logo_light'      => __( 'Logotype for ATELIER (light mode) — dark artwork', 'ak-zeyna-child' ),
			'ak_logo_dark'       => __( 'Logotype for RUNWAY (dark mode) — light artwork', 'ak-zeyna-child' ),
			'ak_hero_video_av1'  => __( 'Hero video — AV1 .mp4 (preferred)', 'ak-zeyna-child' ),
			'ak_hero_video_h264' => __( 'Hero video — H.264 .mp4 (fallback)', 'ak-zeyna-child' ),
			'ak_hero_poster'     => __( 'Hero poster image (shown before playback)', 'ak-zeyna-child' ),
		) as $key => $label ) {
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => 'ak_hero_video_h264' === $key ? 'https://akbrand.studio/wp-content/uploads/2026/03/OFD29COMP-online-video-cutter.com_-1.mp4' : '',
					'sanitize_callback' => 'esc_url_raw',
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Upload_Control(
					$wp_customize,
					$key,
					array(
						'label'   => $label,
						'section' => 'ak_studio',
					)
				)
			);
		}
	}
);
