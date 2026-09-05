<?php
/**
 * The project editor: conditional panels, never one form with every field.
 *
 * The Project Type control is the first thing on the screen. Choosing it
 * reveals that type's panel and hides the rest, so a photography project is
 * never asked for a venue and an event is never asked for a director of
 * photography. Choosing nothing is a first-class option: the always-visible
 * panel plus the website module is a complete, publishable record.
 *
 * The switcher is 40 lines of vanilla JS against data attributes. There is no
 * ACF dependency, because the project data does not need one.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Which panels a mode shows, beyond the always-visible ones.
 *
 * @return array[] mode => panel slugs
 */
function ak_mode_panels() {
	return array(
		'record'    => array(),
		'assembled' => array( 'modules' ),
		'narrative' => array( 'narrative', 'modules' ),
		'image'     => array( 'image', 'modules' ),
		'motion'    => array( 'motion', 'modules' ),
		'document'  => array( 'event', 'image', 'modules' ),
		'campaign'  => array( 'narrative', 'modules' ),
		// Website / Digital and Retail / E-commerce: the engagement really was
		// the site, so the Website module leads and the narrative supports it.
		'digital'   => array( 'narrative', 'image', 'modules' ),
	);
}

/**
 * Panel titles.
 *
 * @return string[]
 */
function ak_panel_titles() {
	return array(
		'always'    => __( 'Project data', 'ak-zeyna-child' ),
		'website'   => __( 'Website module', 'ak-zeyna-child' ),
		'narrative' => __( 'Narrative', 'ak-zeyna-child' ),
		'image'     => __( 'Images', 'ak-zeyna-child' ),
		'motion'    => __( 'Film', 'ak-zeyna-child' ),
		'event'     => __( 'Event information', 'ak-zeyna-child' ),
		'modules'   => __( 'Modules', 'ak-zeyna-child' ),
	);
}

add_action(
	'add_meta_boxes',
	function () {
		foreach ( ak_panel_titles() as $panel => $title ) {
			add_meta_box(
				'ak_panel_' . $panel,
				$title,
				'ak_render_panel',
				AK_PROJECT_CPT,
				'always' === $panel || 'website' === $panel ? 'normal' : 'normal',
				'always' === $panel ? 'high' : 'default',
				array( 'panel' => $panel )
			);
		}
	}
);

/**
 * Render one panel.
 *
 * @param WP_Post $post Project.
 * @param array   $box  Metabox args.
 */
function ak_render_panel( $post, $box ) {
	$panel  = $box['args']['panel'];
	$fields = array_filter(
		ak_project_meta_fields(),
		function ( $field ) use ( $panel ) {
			return $field['panel'] === $panel;
		}
	);

	if ( 'always' === $panel ) {
		wp_nonce_field( 'ak_project_meta', 'ak_project_meta_nonce' );
	}

	echo '<div class="ak-panel" data-ak-panel="' . esc_attr( $panel ) . '">';

	if ( 'website' === $panel ) {
		ak_render_website_status( $post );
	}
	if ( 'modules' === $panel ) {
		echo '<p class="description">' . esc_html__( 'An ordered JSON array of typed blocks. A missing module is absent, not empty — nothing is forced through a fixed sequence.', 'ak-zeyna-child' ) . '</p>';
	}

	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $fields as $key => $field ) {
		if ( ! empty( $field['readonly'] ) ) {
			continue;
		}
		ak_render_field( $post, $key, $field );
	}
	echo '</tbody></table></div>';
}

/**
 * Render one field.
 *
 * @param WP_Post $post  Project.
 * @param string  $key   Meta key.
 * @param array   $field Field definition.
 */
function ak_render_field( $post, $key, $field ) {
	$value = get_post_meta( $post->ID, $key, true );
	$id    = esc_attr( $key );

	echo '<tr><th scope="row"><label for="' . $id . '">' . esc_html( $field['label'] ) . '</label></th><td>';

	if ( 'boolean' === $field['type'] ) {
		printf(
			'<label><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s> %3$s</label>',
			$id,
			checked( (bool) $value, true, false ),
			esc_html__( 'Yes', 'ak-zeyna-child' )
		);
	} elseif ( 'ak_wp_mode' === $key ) {
		echo '<select id="' . $id . '" name="' . $id . '">';
		foreach ( ak_wp_modes() as $mode => $label ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $mode ), selected( $value, $mode, false ), esc_html( $label ) );
		}
		echo '</select>';
	} elseif ( 'integer' === $field['type'] ) {
		// Media fields: an ID box plus the media library, so nothing needs FTP
		// and nothing breaks if JS is unavailable.
		printf(
			'<input type="number" id="%1$s" name="%1$s" value="%2$d" class="small-text" min="0"> <button type="button" class="button ak-pick-media" data-target="%1$s">%3$s</button> <span class="ak-media-name">%4$s</span>',
			$id,
			(int) $value,
			esc_html__( 'Choose…', 'ak-zeyna-child' ),
			$value ? esc_html( get_the_title( (int) $value ) ) : ''
		);
	} elseif ( in_array( $key, array( 'ak_wp_narrative', 'ak_context', 'ak_positioning', 'ak_credits', 'ak_modules' ), true ) ) {
		printf(
			'<textarea id="%1$s" name="%1$s" rows="%2$d" class="large-text code">%3$s</textarea>',
			$id,
			'ak_modules' === $key ? 10 : 4,
			esc_textarea( (string) $value )
		);
	} else {
		printf( '<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text">', $id, esc_attr( (string) $value ) );
	}

	if ( ! empty( $field['help'] ) ) {
		echo '<p class="description">' . esc_html( $field['help'] ) . '</p>';
	}
	echo '</td></tr>';
}

/**
 * The website module's live status, on the edit screen only.
 *
 * This is where "capture pending" and "capture failed" belong. They are
 * administrative facts about tooling, and they never appear on the front end
 * in any form.
 *
 * @param WP_Post $post Project.
 */
function ak_render_website_status( $post ) {
	$module  = ak_website_module( $post->ID );
	$status  = (string) get_post_meta( $post->ID, 'ak_wp_status', true );
	$checked = (int) get_post_meta( $post->ID, 'ak_wp_checked', true );

	$states = array(
		'auto'        => __( 'AUTO — a verified capture is being shown.', 'ak-zeyna-child' ),
		'manual'      => __( 'MANUAL — your own media is being shown.', 'ak-zeyna-child' ),
		'live'        => __( 'LIVE — the real site is embedded.', 'ak-zeyna-child' ),
		'unavailable' => __( 'UNAVAILABLE — no website module is rendered. The project publishes normally from its own media; visitors see no placeholder and no error.', 'ak-zeyna-child' ),
	);

	echo '<div class="ak-status ak-status--' . esc_attr( $module['state'] ) . '">';
	echo '<p><strong>' . esc_html( $states[ $module['state'] ] ) . '</strong></p>';
	if ( $status ) {
		printf(
			'<p class="description">%s%s</p>',
			esc_html( $status ),
			$checked ? esc_html( sprintf( /* translators: %s: human time diff */ __( ' Last checked %s ago.', 'ak-zeyna-child' ), human_time_diff( $checked ) ) ) : ''
		);
	}
	if ( $module['url'] ) {
		printf(
			'<p><a class="button" href="%s">%s</a></p>',
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=ak_refresh_capture&post=' . $post->ID ), 'ak_refresh_capture_' . $post->ID ) ),
			esc_html__( 'Request a capture refresh now', 'ak-zeyna-child' )
		);
	}
	echo '<p class="description">' . esc_html__( 'Automatic capture is an enhancement, never a dependency. If it fails, upload your own desktop, mobile or recorded preview below and set the source to owner-supplied — the project publishes either way.', 'ak-zeyna-child' ) . '</p>';
	echo '</div>';
}

/**
 * Save every panel.
 *
 * @param int $post_id Project ID.
 */
add_action(
	'save_post_' . AK_PROJECT_CPT,
	function ( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['ak_project_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ak_project_meta_nonce'] ) ), 'ak_project_meta' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( ak_project_meta_fields() as $key => $field ) {
			if ( ! empty( $field['readonly'] ) ) {
				continue;
			}
			if ( 'boolean' === $field['type'] ) {
				update_post_meta( $post_id, $key, isset( $_POST[ $key ] ) ? 1 : 0 );
				continue;
			}
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}
			$raw = wp_unslash( $_POST[ $key ] );
			if ( 'integer' === $field['type'] ) {
				update_post_meta( $post_id, $key, absint( $raw ) );
				continue;
			}
			$sanitize = isset( $field['sanitize'] ) ? $field['sanitize'] : 'sanitize_text_field';
			update_post_meta( $post_id, $key, call_user_func( $sanitize, $raw ) );
		}
	},
	20
);

/**
 * Editor assets: the panel switcher and the media picker.
 */
add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen || AK_PROJECT_CPT !== $screen->post_type || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script(
			'ak-project-admin',
			get_stylesheet_directory_uri() . '/assets/js/ak-project-admin.js',
			array(),
			AK_CHILD_VERSION,
			true
		);
		wp_localize_script(
			'ak-project-admin',
			'akProjectAdmin',
			array(
				'panels' => ak_mode_panels(),
				'modes'  => wp_list_pluck( ak_project_type_terms(), 'mode' ),
				'terms'  => ak_project_type_term_ids(),
			)
		);
		wp_enqueue_style(
			'ak-project-admin',
			get_stylesheet_directory_uri() . '/assets/css/ak-project-admin.css',
			array(),
			AK_CHILD_VERSION
		);
	}
);

/**
 * term_id => type slug, so the switcher can map a radio value to a mode.
 *
 * @return array<int,string>
 */
function ak_project_type_term_ids() {
	$out = array();
	foreach ( ak_project_type_terms() as $slug => $type ) {
		$term = get_term_by( 'slug', $slug, 'ak_project_type' );
		if ( $term && ! is_wp_error( $term ) ) {
			$out[ (int) $term->term_id ] = $slug;
		}
	}
	return $out;
}

/**
 * Admin columns that say what the model actually knows.
 */
add_filter(
	'manage_' . AK_PROJECT_CPT . '_posts_columns',
	function ( $columns ) {
		$out = array();
		foreach ( $columns as $key => $label ) {
			$out[ $key ] = $label;
			if ( 'title' === $key ) {
				$out['ak_code']    = __( 'Code', 'ak-zeyna-child' );
				$out['ak_website'] = __( 'Website module', 'ak-zeyna-child' );
			}
		}
		return $out;
	}
);

add_action(
	'manage_' . AK_PROJECT_CPT . '_posts_custom_column',
	function ( $column, $post_id ) {
		if ( 'ak_code' === $column ) {
			echo esc_html( ak_project_code( $post_id ) );
			if ( ak_project_meta( 'ak_fixture', false, $post_id ) ) {
				echo ' <strong>' . esc_html__( '· fixture, not public', 'ak-zeyna-child' ) . '</strong>';
			}
		}
		if ( 'ak_website' === $column ) {
			$module = ak_website_module( $post_id );
			echo esc_html( strtoupper( $module['state'] ) );
		}
	},
	10,
	2
);
