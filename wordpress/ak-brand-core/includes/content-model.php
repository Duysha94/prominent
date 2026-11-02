<?php
/**
 * The project model, moved out of the theme: same post type, taxonomies and
 * slugs as the ak-zeyna-child theme, so existing content and URLs carry over.
 * Plus the few fields a page needs beyond its title and content.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'ak_brand_register_content_model' );

function ak_brand_register_content_model(): void {
	if ( post_type_exists( 'ak_project' ) ) {
		return; // The legacy theme is active and still owns the model.
	}

	// Taxonomies first: their /work/type/… and /work/relationship/… rules must come
	// before the project's own /work/{name}/… rules, or those would swallow them.
	register_taxonomy(
		'ak_relationship',
		'ak_project',
		array(
			'labels'            => array( 'name' => __( 'Relationship', 'ak-brand-core' ) ),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'work/relationship', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'ak_project_type',
		'ak_project',
		array(
			'labels'            => array( 'name' => __( 'Project type', 'ak-brand-core' ) ),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'work/type', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'ak_capability',
		'ak_project',
		array(
			'labels'            => array( 'name' => __( 'Disciplines', 'ak-brand-core' ) ),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
		)
	);

	register_post_type(
		'ak_project',
		array(
			'labels'        => array(
				'name'          => __( 'Projects', 'ak-brand-core' ),
				'singular_name' => __( 'Project', 'ak-brand-core' ),
				'add_new_item'  => __( 'Add project', 'ak-brand-core' ),
				'edit_item'     => __( 'Edit project', 'ak-brand-core' ),
				'all_items'     => __( 'All projects', 'ak-brand-core' ),
				'menu_name'     => __( 'Work', 'ak-brand-core' ),
			),
			'public'        => true,
			'has_archive'   => 'work',
			'menu_icon'     => 'dashicons-portfolio',
			'menu_position' => 21,
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields' ),
			'rewrite'       => array( 'slug' => 'work', 'with_front' => false ),
		)
	);

	foreach ( ak_brand_project_fields() as $key => $field ) {
		register_post_meta(
			'ak_project',
			$key,
			array(
				'type'              => 'boolean' === $field['type'] ? 'boolean' : ( 'integer' === $field['type'] ? 'integer' : 'string' ),
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => static fn( $v ) => ak_brand_sanitize_field( $v, $field['type'] ),
				'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
			)
		);
	}
	foreach ( array( '_ak_eyebrow', '_ak_heading', '_ak_movement' ) as $key ) {
		register_post_meta(
			'page',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => static fn() => current_user_can( 'edit_pages' ),
			)
		);
	}
}

/**
 * Project fields: one definition drives registration, the editor box and the theme.
 *
 * @return array<string,array<string,string>>
 */
function ak_brand_project_fields(): array {
	return array(
		'ak_year'     => array( 'label' => 'Year', 'type' => 'text', 'help' => 'e.g. 2026' ),
		'ak_date'     => array( 'label' => 'Event date', 'type' => 'date', 'help' => 'Optional. A future date marks the project as upcoming.' ),
		'ak_location' => array( 'label' => 'City', 'type' => 'text', 'help' => 'e.g. London' ),
		'ak_venue'    => array( 'label' => 'Venue', 'type' => 'text', 'help' => 'e.g. Porchester Hall, Westminster' ),
		'ak_role'     => array( 'label' => 'Our part', 'type' => 'text', 'help' => 'One line, e.g. “Organiser, producer, campaign and press”.' ),
		'ak_url'      => array( 'label' => 'External link', 'type' => 'url', 'help' => 'Official site of the event or brand.' ),
		'ak_credits'  => array( 'label' => 'Credits', 'type' => 'textarea', 'help' => 'One per line as “Role: Names”.' ),
		'ak_gallery'  => array( 'label' => 'Gallery', 'type' => 'ids', 'help' => 'Images shown on the project page, in order.' ),
		'ak_film'     => array( 'label' => 'Film (with sound)', 'type' => 'integer', 'help' => 'A video from the media library, played on request.' ),
		'ak_loop'     => array( 'label' => 'Loop (silent)', 'type' => 'integer', 'help' => 'A short silent video used behind the project title.' ),
		'ak_featured' => array( 'label' => 'Show on the home page', 'type' => 'boolean', 'help' => '' ),
	);
}

/**
 * @param mixed  $value Raw value.
 * @param string $type  Field type.
 * @return mixed
 */
function ak_brand_sanitize_field( $value, string $type ) {
	switch ( $type ) {
		case 'url':
			return esc_url_raw( (string) $value );
		case 'textarea':
			return sanitize_textarea_field( (string) $value );
		case 'date':
			return preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $value ) ? (string) $value : '';
		case 'integer':
			return absint( $value );
		case 'boolean':
			return (bool) $value;
		case 'ids':
			return implode( ',', array_filter( array_map( 'absint', explode( ',', (string) $value ) ) ) );
		default:
			return sanitize_text_field( (string) $value );
	}
}

/**
 * A project's details, ready for templates.
 *
 * @param int|WP_Post|null $post Project.
 * @return array<string,mixed>
 */
function ak_brand_project( $post = null ): array {
	$post = get_post( $post );
	if ( ! $post ) {
		return array();
	}
	$out = array();
	foreach ( ak_brand_project_fields() as $key => $field ) {
		$out[ substr( $key, 3 ) ] = get_post_meta( $post->ID, $key, true );
	}
	$out['gallery'] = array_values( array_filter( array_map( 'absint', explode( ',', (string) $out['gallery'] ) ) ) );
	$credits        = array();
	foreach ( preg_split( '/\r\n|\n/', (string) $out['credits'] ) as $line ) {
		$parts = array_map( 'trim', explode( ':', $line, 2 ) );
		if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
			$credits[ $parts[0] ] = $parts[1];
		}
	}
	$out['credits']  = $credits;
	$ts              = $out['date'] ? strtotime( $out['date'] . ' 23:59:59 ' . wp_timezone_string() ) : 0;
	$out['upcoming'] = $ts && $ts >= time();
	$out['date_h']   = $ts ? wp_date( 'j F Y', $ts ) : '';
	$out['disciplines'] = array();
	$terms              = get_the_terms( $post, 'ak_capability' );
	if ( is_array( $terms ) ) {
		$order = array_column( ak_brand_movements(), 'slug' );
		usort( $terms, static fn( $a, $b ) => array_search( $a->slug, $order, true ) <=> array_search( $b->slug, $order, true ) );
		foreach ( $terms as $t ) {
			$out['disciplines'][ $t->slug ] = $t->name;
		}
	}
	$rel                 = get_the_terms( $post, 'ak_relationship' );
	$out['relationship'] = is_array( $rel ) ? $rel[0]->name : '';
	$type                = get_the_terms( $post, 'ak_project_type' );
	$out['type']         = is_array( $type ) ? $type[0]->name : '';
	$out['type_slug']    = is_array( $type ) ? $type[0]->slug : '';
	return $out;
}

/**
 * The six disciplines as top-level capability terms, plus relationship and type terms (idempotent).
 */
function ak_brand_seed_capabilities(): void {
	foreach ( ak_brand_movements() as $movement ) {
		if ( ! term_exists( $movement['slug'], 'ak_capability' ) ) {
			wp_insert_term( $movement['name'], 'ak_capability', array( 'slug' => $movement['slug'] ) );
		}
	}
	foreach ( array( 'ak-owned' => 'AK platform', 'client' => 'Client', 'collaboration' => 'Collaboration' ) as $slug => $name ) {
		if ( ! term_exists( $slug, 'ak_relationship' ) ) {
			wp_insert_term( $name, 'ak_relationship', array( 'slug' => $slug ) );
		}
	}
	foreach ( array( 'platform' => 'Platform', 'season' => 'Season', 'campaign' => 'Campaign', 'brand' => 'Brand' ) as $slug => $name ) {
		if ( ! term_exists( $slug, 'ak_project_type' ) ) {
			wp_insert_term( $name, 'ak_project_type', array( 'slug' => $slug ) );
		}
	}
}

/* ── Editor boxes ──────────────────────────────────────────────────── */

add_action(
	'add_meta_boxes',
	static function () {
		add_meta_box( 'ak-project-details', __( 'Project details', 'ak-brand-core' ), 'ak_brand_render_project_box', 'ak_project', 'normal', 'high' );
		add_meta_box( 'ak-page-header', __( 'Page header', 'ak-brand-core' ), 'ak_brand_render_page_box', 'page', 'side', 'default' );
	}
);

add_action(
	'admin_enqueue_scripts',
	static function ( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'ak_project' !== get_post_type() ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'ak-brand-admin', plugins_url( 'assets/admin.js', AK_BRAND_CORE_FILE ), array( 'jquery' ), AK_BRAND_CORE_VERSION, true );
	}
);

function ak_brand_render_project_box( WP_Post $post ): void {
	wp_nonce_field( 'ak_project_save', 'ak_project_nonce' );
	echo '<table class="form-table" role="presentation">';
	foreach ( ak_brand_project_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		$id    = 'ak-field-' . $key;
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
		switch ( $field['type'] ) {
			case 'textarea':
				echo '<textarea class="large-text" rows="4" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '">' . esc_textarea( (string) $value ) . '</textarea>';
				break;
			case 'boolean':
				echo '<label><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="1" ' . checked( (bool) $value, true, false ) . '> ' . esc_html__( 'Yes', 'ak-brand-core' ) . '</label>';
				break;
			case 'ids':
			case 'integer':
				$multi = 'ids' === $field['type'];
				echo '<input type="text" class="regular-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( (string) $value ) . '"> ';
				echo '<button type="button" class="button" data-ak-media="' . esc_attr( $id ) . '" data-multiple="' . ( $multi ? '1' : '0' ) . '" data-type="' . ( in_array( $key, array( 'ak_film', 'ak_loop' ), true ) ? 'video' : 'image' ) . '">' . esc_html__( 'Choose', 'ak-brand-core' ) . '</button>';
				break;
			default:
				$type = 'date' === $field['type'] ? 'date' : ( 'url' === $field['type'] ? 'url' : 'text' );
				echo '<input type="' . esc_attr( $type ) . '" class="regular-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( (string) $value ) . '">';
		}
		if ( $field['help'] ) {
			echo '<p class="description">' . esc_html( $field['help'] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</table>';
}

add_action(
	'save_post_ak_project',
	static function ( $post_id ) {
		if ( ! isset( $_POST['ak_project_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ak_project_nonce'] ) ), 'ak_project_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( ak_brand_project_fields() as $key => $field ) {
			$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised per type below.
			update_post_meta( $post_id, $key, ak_brand_sanitize_field( $raw, $field['type'] ) );
		}
	}
);

function ak_brand_render_page_box( WP_Post $post ): void {
	wp_nonce_field( 'ak_page_save', 'ak_page_nonce' );
	$eyebrow  = (string) get_post_meta( $post->ID, '_ak_eyebrow', true );
	$heading  = (string) get_post_meta( $post->ID, '_ak_heading', true );
	$movement = (string) get_post_meta( $post->ID, '_ak_movement', true );
	?>
	<p><label for="ak-eyebrow"><strong><?php esc_html_e( 'Eyebrow', 'ak-brand-core' ); ?></strong></label><br>
		<input type="text" class="widefat" id="ak-eyebrow" name="_ak_eyebrow" value="<?php echo esc_attr( $eyebrow ); ?>"></p>
	<p><label for="ak-heading"><strong><?php esc_html_e( 'Display heading', 'ak-brand-core' ); ?></strong></label><br>
		<textarea class="widefat" rows="2" id="ak-heading" name="_ak_heading"><?php echo esc_textarea( $heading ); ?></textarea>
		<span class="description"><?php esc_html_e( 'The large heading. Empty uses the page title. Wrap a word in *asterisks* to set it in italics.', 'ak-brand-core' ); ?></span></p>
	<p><label for="ak-movement"><strong><?php esc_html_e( 'Service discipline', 'ak-brand-core' ); ?></strong></label><br>
		<select class="widefat" id="ak-movement" name="_ak_movement">
			<option value=""><?php esc_html_e( '— Not a service page —', 'ak-brand-core' ); ?></option>
			<?php foreach ( ak_brand_movements() as $m ) : ?>
				<option value="<?php echo esc_attr( $m['slug'] ); ?>" <?php selected( $movement, $m['slug'] ); ?>><?php echo esc_html( $m['name'] ); ?></option>
			<?php endforeach; ?>
		</select></p>
	<p class="description"><?php esc_html_e( 'The excerpt (under the title) is used as the lead paragraph.', 'ak-brand-core' ); ?></p>
	<?php
}

add_action(
	'save_post_page',
	static function ( $post_id ) {
		if ( ! isset( $_POST['ak_page_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ak_page_nonce'] ) ), 'ak_page_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
		foreach ( array( '_ak_eyebrow', '_ak_heading', '_ak_movement' ) as $key ) {
			$value = isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '';
			update_post_meta( $post_id, $key, $value );
		}
	}
);

/* Pages get an excerpt box: it is the lead paragraph under the heading. */
add_action( 'init', static fn() => add_post_type_support( 'page', 'excerpt' ) );
