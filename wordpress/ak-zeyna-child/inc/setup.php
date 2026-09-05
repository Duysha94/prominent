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
 * Portfolio CPT + taxonomy, as a FALLBACK only.
 *
 * On a full Zeyna install these come from the Pe Core companion plugin, and
 * this block steps aside (post_type_exists guards). Registering them here as
 * well means the import works on a site where Pe Core is not active — the
 * case studies land somewhere real either way.
 */
add_action(
	'init',
	function () {
		if ( ! post_type_exists( 'portfolio' ) ) {
			register_post_type(
				'portfolio',
				array(
					'label'        => __( 'Work', 'ak-zeyna-child' ),
					'public'       => true,
					'menu_icon'    => 'dashicons-portfolio',
					'menu_position' => 5,
					'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
					'has_archive'  => true,
					'rewrite'      => array( 'slug' => 'work' ),
					'show_in_rest' => true,
				)
			);
		}

		if ( ! taxonomy_exists( 'project-categories' ) ) {
			register_taxonomy(
				'project-categories',
				'portfolio',
				array(
					'label'        => __( 'Project categories', 'ak-zeyna-child' ),
					'hierarchical' => true,
					'show_in_rest' => true,
					'rewrite'      => array( 'slug' => 'work-category' ),
				)
			);
		}
	},
	5
);

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

/**
 * The AK chrome is not optional.
 *
 * Zeyna's demo import sets `header_type` and `footer_template` to Elementor
 * templates, which silently replaces the studio's header and footer with
 * demo content (the ZEYNA CREATIVE footer, demo contact block, demo email).
 * Force both keys back to the default chrome the child styles.
 *
 * The same filter settles the page transition, for the same reason. Zeyna's
 * transition overlay is built entirely from Redux: type, direction, curve,
 * and — the visible part — a logo and a caption pulled from `transition_logo`
 * and `transition_caption`. Left alone, a demo import fills those in, and
 * every navigation flashes the parent theme's own loader panel, on the
 * parent's dark `--secondaryBackground`, regardless of which mode the
 * visitor is in.
 *
 * The element itself must stay: Zeyna calls `barba.init()` only when
 * `.page--transitions` is in the document (js/scripts.js), so removing it
 * turns every soft navigation into a full page load. So it stays, and is
 * reduced to one plain overlay with no elements inside it — a sheet of the
 * studio's own paper or ink, coloured from `data-theme` in ak.css.
 *
 * The loader is a separate matter: the child prints its own and never calls
 * `zeyna_page_loader()`, so `page_loader` is turned off here too rather than
 * left to run a second, invisible boot sequence.
 */
add_filter( 'option_pe-redux', 'ak_force_redux_chrome' );

/**
 * Force the chrome and transition keys, whatever Redux has stored.
 *
 * A named function rather than a closure so the deployment engine can detach
 * it for one raw read and put it straight back — see ak_purge_unmanaged().
 *
 * @param mixed $option The stored pe-redux option.
 * @return mixed
 */
function ak_force_redux_chrome( $option ) {
		if ( ! is_array( $option ) ) {
			return $option;
		}

		$option['header_type']     = 'default';
		$option['footer_template'] = 'default';

		// Keep transitions running, but as ours.
		$option['page_transitions']        = true;
		$option['transition_type']         = 'overlay';
		// One of up/down/left/right — Zeyna's CSS has a rule per direction and
		// any other value leaves the overlay parked at its 70vh resting size,
		// a permanent dark band across the foot of every page.
		$option['transition_direction']    = 'up';
		$option['transitions_curved']      = false;
		$option['transitions_fade_simple'] = false;
		$option['transition_elements_type'] = 'default';
		$option['transition_elements']     = array();
		$option['page_transition_template'] = '';

		$option['page_loader'] = false;

		return $option;
}

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
