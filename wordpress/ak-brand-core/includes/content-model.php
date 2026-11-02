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
			'has_archive'   => false, // /work/ is an ordinary, editable page.
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
				'type'              => 'boolean' === $field['type'] ? 'boolean' : 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => static fn( $v ) => ak_brand_sanitize_field( $v, $field['type'] ),
				'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
			)
		);
	}
	register_post_meta(
		'page',
		'ak_short_title',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => static fn() => current_user_can( 'edit_pages' ),
		)
	);
}

/**
 * Project fields: one definition drives registration, the editor box and the theme.
 *
 * @return array<string,array<string,string>>
 */
function ak_brand_project_fields(): array {
	return array(
		'ak_short_title' => array( 'label' => 'Card title', 'type' => 'text' ),
		'ak_kind'        => array( 'label' => 'Card label', 'type' => 'text' ),
		'ak_location'    => array( 'label' => 'City', 'type' => 'text' ),
		'ak_year'        => array( 'label' => 'Year', 'type' => 'text' ),
		'ak_status'      => array( 'label' => 'Status', 'type' => 'text' ),
		'ak_date'        => array( 'label' => 'Event date', 'type' => 'date' ),
		'ak_featured'    => array( 'label' => 'Show on the home page', 'type' => 'boolean' ),
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
	$ts              = $out['date'] ? strtotime( $out['date'] . ' 23:59:59 ' . wp_timezone_string() ) : 0;
	$out['upcoming'] = $ts && $ts >= time();
	$type            = get_the_terms( $post, 'ak_project_type' );
	$out['type']     = is_array( $type ) ? $type[0]->name : '';
	$out['types']    = is_array( $type ) ? wp_list_pluck( $type, 'slug' ) : array();
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
	foreach ( array( 'ak-owned' => 'Managed by AK', 'client' => 'Client', 'collaboration' => 'Collaboration' ) as $slug => $name ) {
		if ( ! term_exists( $slug, 'ak_relationship' ) ) {
			wp_insert_term( $name, 'ak_relationship', array( 'slug' => $slug ) );
		}
	}
	$order = 0;
	foreach ( array( 'website' => 'Websites', 'shoot' => 'Photo & film', 'show' => 'Shows & events', 'brand' => 'Brands', 'platform' => 'Platforms' ) as $slug => $name ) {
		$term = term_exists( $slug, 'ak_project_type' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'ak_project_type', array( 'slug' => $slug ) );
		} else {
			wp_update_term( (int) $term['term_id'], 'ak_project_type', array( 'name' => $name ) );
		}
		if ( ! is_wp_error( $term ) ) {
			update_term_meta( (int) $term['term_id'], 'ak_order', ++$order );
		}
	}
	// Types from the first version of the site, retired once empty.
	foreach ( array( 'season', 'campaign' ) as $slug ) {
		$old = get_term_by( 'slug', $slug, 'ak_project_type' );
		if ( $old && 0 === (int) $old->count ) {
			wp_delete_term( $old->term_id, 'ak_project_type' );
		}
	}
}

/* Pages get an excerpt box: it is the lead paragraph under the heading. */
add_action( 'init', static fn() => add_post_type_support( 'page', 'excerpt' ) );
