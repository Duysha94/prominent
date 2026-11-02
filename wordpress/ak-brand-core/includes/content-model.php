<?php
/**
 * The project model, moved out of the theme: same post type, taxonomies and
 * slugs as the ak-zeyna-child theme, so existing content and URLs carry over.
 * (Meta panels, modules and the preview capture follow in the next step.)
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'ak_brand_register_content_model' );

function ak_brand_register_content_model(): void {
	if ( post_type_exists( 'ak_project' ) ) {
		return; // The legacy theme is active and still owns the model.
	}

	register_post_type(
		'ak_project',
		array(
			'labels'       => array(
				'name'          => __( 'Projects', 'ak-brand-core' ),
				'singular_name' => __( 'Project', 'ak-brand-core' ),
				'add_new_item'  => __( 'Add project', 'ak-brand-core' ),
				'edit_item'     => __( 'Edit project', 'ak-brand-core' ),
				'all_items'     => __( 'All projects', 'ak-brand-core' ),
			),
			'public'       => true,
			'has_archive'  => 'work',
			'menu_icon'    => 'dashicons-portfolio',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'rewrite'      => array( 'slug' => 'work', 'with_front' => false ),
		)
	);

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
			'labels'            => array( 'name' => __( 'Capabilities', 'ak-brand-core' ) ),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
		)
	);
}

/**
 * The six movements as top-level capability terms (idempotent).
 */
function ak_brand_seed_capabilities(): void {
	foreach ( ak_brand_movements() as $movement ) {
		if ( ! term_exists( $movement['slug'], 'ak_capability' ) ) {
			wp_insert_term( $movement['title'], 'ak_capability', array( 'slug' => $movement['slug'] ) );
		}
	}
	foreach ( array( 'ak-owned' => 'AK Owned', 'client' => 'Client', 'collaboration' => 'Collaboration' ) as $slug => $name ) {
		if ( ! term_exists( $slug, 'ak_relationship' ) ) {
			wp_insert_term( $name, 'ak_relationship', array( 'slug' => $slug ) );
		}
	}
}
