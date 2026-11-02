<?php
/**
 * Customizer: the hero reel. Footage is presentation, so it is a theme mod.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	static function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_section(
			'ak_reel',
			array(
				'title'       => __( 'Hero reel', 'ak-studio' ),
				'description' => __( 'One clip per platform, in the order of AK Brand Core’s platforms. Leave empty to use the bundled placeholders.', 'ak-studio' ),
				'priority'    => 30,
			)
		);
		for ( $i = 0; $i < 4; $i++ ) {
			$wp_customize->add_setting( 'ak_reel_video_' . $i, array( 'sanitize_callback' => 'absint' ) );
			$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					'ak_reel_video_' . $i,
					array(
						/* translators: %d: clip number. */
						'label'     => sprintf( __( 'Clip %d — video', 'ak-studio' ), $i + 1 ),
						'section'   => 'ak_reel',
						'mime_type' => 'video',
					)
				)
			);
			$wp_customize->add_setting( 'ak_reel_poster_' . $i, array( 'sanitize_callback' => 'absint' ) );
			$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					'ak_reel_poster_' . $i,
					array(
						/* translators: %d: clip number. */
						'label'     => sprintf( __( 'Clip %d — poster', 'ak-studio' ), $i + 1 ),
						'section'   => 'ak_reel',
						'mime_type' => 'image',
					)
				)
			);
		}
	}
);
