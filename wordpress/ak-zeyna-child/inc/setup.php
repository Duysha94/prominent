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
 * One-time wiring: point WordPress at the imported content.
 *
 * Registered for BOTH `after_switch_theme` and `import_end`, because the
 * recommended order is activate-then-import — at activation the imported
 * pages and menu do not exist yet, and the importer firing `import_end`
 * is the first moment they all do. Every step is guarded: running early
 * is harmless, and re-running never overwrites a manual choice.
 */
function ak_wire_imported_content() {
	$front = get_page_by_path( 'home' );
	if ( $front && 'page' !== get_option( 'show_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front->ID );
	}

	$blog = get_page_by_path( 'journal' );
	if ( $blog && ! get_option( 'page_for_posts' ) ) {
		update_option( 'page_for_posts', $blog->ID );
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['menu-1'] ) ) {
		$menu = get_term_by( 'name', 'Primary', 'nav_menu' );
		if ( $menu ) {
			$locations['menu-1'] = (int) $menu->term_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'ak_wire_imported_content' );
add_action( 'import_end', 'ak_wire_imported_content' );

/**
 * The ATELIER / RUNWAY switch, appended to the primary menu.
 *
 * Appending through wp_nav_menu_items means Zeyna's header.php stays
 * untouched — the brief was to keep the theme's header — and the control
 * still lands where a visitor looks for it.
 */
add_filter(
	'wp_nav_menu_items',
	function ( $items, $args ) {
		if ( ! isset( $args->theme_location ) || 'menu-1' !== $args->theme_location ) {
			return $items;
		}
		ob_start();
		?>
		<li class="menu-item ak-menu-mode">
			<button type="button" class="ak-mode" data-ak-mode aria-pressed="false">
				<span class="ak-mode__label">Atelier</span>
				<span class="ak-mode__track" aria-hidden="true"><span class="ak-mode__knob"></span></span>
			</button>
		</li>
		<?php
		return $items . ob_get_clean();
	},
	10,
	2
);

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
				'title'    => __( 'AK Studio', 'ak-zeyna-child' ),
				'priority' => 30,
			)
		);

		foreach ( array(
			'ak_hero_video_av1'  => __( 'Hero video — AV1 .mp4 (preferred)', 'ak-zeyna-child' ),
			'ak_hero_video_h264' => __( 'Hero video — H.264 .mp4 (fallback)', 'ak-zeyna-child' ),
			'ak_hero_poster'     => __( 'Hero poster image (shown before playback)', 'ak-zeyna-child' ),
		) as $key => $label ) {
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => '',
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
