<?php
/**
 * AK Core Project model: one post type, three taxonomies.
 *
 * The three taxonomies are deliberately separate, and collapsing any two of
 * them is what produced a portfolio that read as a web shop:
 *
 *   ak_relationship  how the project came to exist   (always known)
 *   ak_project_type  what the project IS             (often not yet known)
 *   ak_capability    what AK actually delivered      (many-to-many)
 *
 * A supplied URL identifies a project and gives us real digital material to
 * preview. It says nothing about what the project is. Type is never inferred
 * from a domain name, a website's appearance, a project name, a URL or
 * ownership — where the nature of the work is not established, the type stays
 * unset and the record publishes as a minimal, truthful entry.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AK_PROJECT_CPT = 'ak_project';

/**
 * The post type.
 *
 * Owns /work/. The legacy `portfolio` fallback that used to claim that slug
 * is gone: it existed so a WXR import landed somewhere, the deployment engine
 * replaced the import, and leaving it registered meant two post types called
 * Work fighting over one archive.
 */
add_action(
	'init',
	function () {
		register_post_type(
			AK_PROJECT_CPT,
			array(
				'labels'        => array(
					'name'               => __( 'Work', 'ak-zeyna-child' ),
					'singular_name'      => __( 'Project', 'ak-zeyna-child' ),
					'add_new_item'       => __( 'Add project', 'ak-zeyna-child' ),
					'edit_item'          => __( 'Edit project', 'ak-zeyna-child' ),
					'new_item'           => __( 'New project', 'ak-zeyna-child' ),
					'view_item'          => __( 'View project', 'ak-zeyna-child' ),
					'search_items'       => __( 'Search projects', 'ak-zeyna-child' ),
					'not_found'          => __( 'No projects yet', 'ak-zeyna-child' ),
					'all_items'          => __( 'All projects', 'ak-zeyna-child' ),
					'menu_name'          => __( 'Work', 'ak-zeyna-child' ),
				),
				'public'        => true,
				'menu_icon'     => 'dashicons-portfolio',
				'menu_position' => 5,
				'supports'      => array( 'title', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
				'has_archive'   => 'work',
				'rewrite'       => array( 'slug' => 'work', 'with_front' => false ),
				'show_in_rest'  => true,
				'rest_base'     => 'projects',
			)
		);

		register_taxonomy(
			'ak_relationship',
			AK_PROJECT_CPT,
			array(
				'label'             => __( 'Relationship', 'ak-zeyna-child' ),
				'hierarchical'      => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'work/relationship', 'with_front' => false ),
				// The conditional editor replaces the default box with a radio
				// list: a project has exactly one relationship, and a checkbox
				// list invites two.
				'meta_box_cb'       => 'ak_taxonomy_radio_box',
			)
		);

		register_taxonomy(
			'ak_project_type',
			AK_PROJECT_CPT,
			array(
				'label'             => __( 'Project type', 'ak-zeyna-child' ),
				'hierarchical'      => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'work/type', 'with_front' => false ),
				'meta_box_cb'       => 'ak_taxonomy_radio_box',
			)
		);

		/*
		 * Capabilities are hierarchical because the six movements ARE the
		 * parent terms. That is what keeps the editorial layer honest: every
		 * service in the factual layer exists as a term under the movement
		 * that carries it, so naming something IMAGE never hides
		 * "editorial photography" from a visitor or from a filter.
		 */
		/*
		 * Not publicly queryable. A public taxonomy would have given the site
		 * 49 capability archives at /work/capability/<service>/ — a second,
		 * accidental portfolio navigation an order of magnitude larger than
		 * the real one, mostly empty, and indexable. Capabilities are recorded
		 * per project and displayed inside it; the public navigation is the
		 * six editorial filters above.
		 */
		register_taxonomy(
			'ak_capability',
			AK_PROJECT_CPT,
			array(
				'label'              => __( 'Capabilities delivered', 'ak-zeyna-child' ),
				'hierarchical'       => true,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_rest'       => true,
				'show_admin_column'  => false,
				'rewrite'            => false,
			)
		);
	},
	5
);

/**
 * Classic editor for projects.
 *
 * The project screen is a set of conditional panels driven by the project
 * type, which the block editor cannot express without a bespoke plugin. The
 * post type stays `show_in_rest` so the REST API and future block work are
 * open; only the editing screen falls back.
 */
add_filter(
	'use_block_editor_for_post_type',
	function ( $use, $post_type ) {
		return AK_PROJECT_CPT === $post_type ? false : $use;
	},
	10,
	2
);

/**
 * The canonical relationship terms.
 *
 * @return array[] slug => array( name, label, description )
 */
function ak_relationship_terms() {
	return array(
		'ak-owned'      => array(
			'name'  => __( 'AK Owned', 'ak-zeyna-child' ),
			'label' => __( 'Founded', 'ak-zeyna-child' ),
			'desc'  => __( 'Created, owned, developed or operated by the studio.', 'ak-zeyna-child' ),
		),
		'client'        => array(
			'name'  => __( 'Client', 'ak-zeyna-child' ),
			'label' => __( 'Commissioned', 'ak-zeyna-child' ),
			'desc'  => __( 'Work performed for an external client.', 'ak-zeyna-child' ),
		),
		'collaboration' => array(
			'name'  => __( 'Collaboration', 'ak-zeyna-child' ),
			'label' => __( 'In collaboration', 'ak-zeyna-child' ),
			'desc'  => __( 'Developed jointly; authorship shared.', 'ak-zeyna-child' ),
		),
	);
}

/**
 * The canonical project types.
 *
 * `mode` is the presentation strategy the type selects. `filter` is the
 * concise editorial Work filter it falls under — and a filter renders only
 * when a published project actually carries it.
 *
 * WEBSITE / DIGITAL IS A REAL TYPE, and a manually selectable one. Some
 * engagements genuinely ARE primarily website design, website development,
 * e-commerce or a digital ecosystem, and the model has to be able to say so.
 * Its mode leads with the Website module.
 *
 * What remains forbidden is INFERENCE. A URL never selects this type, or any
 * type. A Platform, a Fashion Brand, a Media project or an Integrated project
 * may all carry the Website module — showing their live site — without
 * becoming Website projects. The module and the type are separate concepts and
 * neither implies the other:
 *
 *     Website / Digital  a kind of project        chosen by a person
 *     Website module     a section of a record    available to every type
 *
 * @return array[] slug => array( name, mode, filter )
 */
function ak_project_type_terms() {
	return array(
		'platform'           => array( 'name' => __( 'Platform', 'ak-zeyna-child' ), 'mode' => 'assembled', 'filter' => 'fashion' ),
		'media-editorial'    => array( 'name' => __( 'Media / Editorial', 'ak-zeyna-child' ), 'mode' => 'assembled', 'filter' => 'digital' ),
		'fashion-brand'      => array( 'name' => __( 'Fashion Brand', 'ak-zeyna-child' ), 'mode' => 'assembled', 'filter' => 'fashion' ),
		'fashion-production' => array( 'name' => __( 'Fashion Production', 'ak-zeyna-child' ), 'mode' => 'document', 'filter' => 'fashion' ),
		'website-digital'    => array( 'name' => __( 'Website / Digital', 'ak-zeyna-child' ), 'mode' => 'digital', 'filter' => 'digital' ),
		'retail-ecommerce'   => array( 'name' => __( 'Retail / E-commerce', 'ak-zeyna-child' ), 'mode' => 'digital', 'filter' => 'digital' ),
		'branding'           => array( 'name' => __( 'Branding', 'ak-zeyna-child' ), 'mode' => 'narrative', 'filter' => 'brand' ),
		'personal-branding'  => array( 'name' => __( 'Personal Branding', 'ak-zeyna-child' ), 'mode' => 'narrative', 'filter' => 'brand' ),
		'photography'        => array( 'name' => __( 'Photography', 'ak-zeyna-child' ), 'mode' => 'image', 'filter' => 'image' ),
		'campaign'           => array( 'name' => __( 'Campaign', 'ak-zeyna-child' ), 'mode' => 'image', 'filter' => 'image' ),
		'film'               => array( 'name' => __( 'Film', 'ak-zeyna-child' ), 'mode' => 'motion', 'filter' => 'film' ),
		'event'              => array( 'name' => __( 'Event', 'ak-zeyna-child' ), 'mode' => 'document', 'filter' => 'experience' ),
		'advertising'        => array( 'name' => __( 'Advertising', 'ak-zeyna-child' ), 'mode' => 'campaign', 'filter' => 'digital' ),
		'integrated'         => array( 'name' => __( 'Integrated', 'ak-zeyna-child' ), 'mode' => 'assembled', 'filter' => '' ),
	);
}

/**
 * The concise public Work filters, in display order.
 *
 * THE SERVICE TAXONOMY AND THE PUBLIC PORTFOLIO NAVIGATION ARE NOT THE SAME
 * THING. `ak_capability` carries 49 named services because the factual record
 * of what the studio does has to be complete. Turning 49 services into 49
 * portfolio filters would be unusable, and would also make Work look like a
 * capability list rather than a body of work.
 *
 * So: six editorial filters plus All. The detailed capabilities AK actually
 * delivered are shown inside each project, grouped by movement, where they
 * describe one piece of work rather than trying to navigate all of it.
 *
 * This is the mapping, not the menu — `ak_work_filters()` in query.php renders
 * only those with published content behind them. Projects typed Integrated,
 * and projects with no type established, appear under All alone: an integrated
 * project spans several of these and filing it under one would misdescribe it.
 *
 * @return string[] slug => label
 */
function ak_work_filter_labels() {
	/*
	 * Order matters as much as membership. With only Digital and Fashion
	 * populated, an alphabetical-ish order put DIGITAL first — so the most
	 * prominent categorisation on the Work page opened on the studio's digital
	 * layer, which is the exact misreading the whole architecture exists to
	 * prevent. Digital is last: it is the surface a brand lives on, not the
	 * practice.
	 */
	return array(
		'brand'      => __( 'Brand', 'ak-zeyna-child' ),
		'image'      => __( 'Image', 'ak-zeyna-child' ),
		'film'       => __( 'Film', 'ak-zeyna-child' ),
		'experience' => __( 'Experience', 'ak-zeyna-child' ),
		'fashion'    => __( 'Fashion', 'ak-zeyna-child' ),
		'digital'    => __( 'Digital', 'ak-zeyna-child' ),
	);
}

/**
 * A project's relationship term, or null.
 *
 * @param int $post_id Project ID.
 * @return WP_Term|null
 */
function ak_project_relationship( $post_id = 0 ) {
	$terms = get_the_terms( $post_id ? $post_id : get_the_ID(), 'ak_relationship' );
	return ( is_array( $terms ) && $terms ) ? $terms[0] : null;
}

/**
 * A project's type term, or null when the nature of the work is not
 * established. Null is a legitimate, publishable state.
 *
 * @param int $post_id Project ID.
 * @return WP_Term|null
 */
function ak_project_type( $post_id = 0 ) {
	$terms = get_the_terms( $post_id ? $post_id : get_the_ID(), 'ak_project_type' );
	return ( is_array( $terms ) && $terms ) ? $terms[0] : null;
}

/**
 * The front-end label for a relationship: FOUNDED, COMMISSIONED, IN COLLABORATION.
 *
 * @param int $post_id Project ID.
 * @return string
 */
function ak_relationship_label( $post_id = 0 ) {
	$term = ak_project_relationship( $post_id );
	if ( ! $term ) {
		return '';
	}
	$map = ak_relationship_terms();
	return isset( $map[ $term->slug ] ) ? $map[ $term->slug ]['label'] : $term->name;
}

/**
 * The presentation mode a project renders in.
 *
 * With no type, the mode is `record`: title, relationship, address, whatever
 * media exists. Nothing is claimed beyond what is known.
 *
 * @param int $post_id Project ID.
 * @return string
 */
function ak_project_mode( $post_id = 0 ) {
	$type = ak_project_type( $post_id );
	if ( ! $type ) {
		return 'record';
	}
	$map = ak_project_type_terms();
	return isset( $map[ $type->slug ] ) ? $map[ $type->slug ]['mode'] : 'assembled';
}

/**
 * A single-select radio metabox for a taxonomy that must hold one term.
 *
 * `None` is a real option, not an oversight: leaving a project type unset is
 * how the model refuses to guess.
 *
 * @param WP_Post $post Post being edited.
 * @param array   $box  Metabox arguments.
 */
function ak_taxonomy_radio_box( $post, $box ) {
	$taxonomy = $box['args']['taxonomy'];
	$terms    = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	$current  = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
	$current  = $current ? (int) $current[0] : 0;

	echo '<div class="ak-radio-tax">';
	wp_nonce_field( 'ak_tax_' . $taxonomy, 'ak_tax_nonce_' . $taxonomy );
	printf(
		'<label class="ak-radio"><input type="radio" name="ak_tax_%1$s" value="0" %2$s> <span>%3$s</span></label>',
		esc_attr( $taxonomy ),
		checked( $current, 0, false ),
		esc_html__( 'Not established yet', 'ak-zeyna-child' )
	);
	foreach ( $terms as $term ) {
		printf(
			'<label class="ak-radio"><input type="radio" name="ak_tax_%1$s" value="%2$d" %3$s> <span>%4$s</span></label>',
			esc_attr( $taxonomy ),
			(int) $term->term_id,
			checked( $current, (int) $term->term_id, false ),
			esc_html( $term->name )
		);
	}
	if ( 'ak_project_type' === $taxonomy ) {
		echo '<p class="description">' . esc_html__( 'Leave unset until the nature of the project is established. Do not infer it from the domain name, the website, the project name or who owns it — the record publishes perfectly well without a type.', 'ak-zeyna-child' ) . '</p>';
	}
	echo '</div>';
}

/**
 * Persist the radio taxonomies.
 *
 * @param int $post_id Post ID.
 */
add_action(
	'save_post_' . AK_PROJECT_CPT,
	function ( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( array( 'ak_relationship', 'ak_project_type' ) as $taxonomy ) {
			$nonce = 'ak_tax_nonce_' . $taxonomy;
			// Absent field means the box was not rendered (quick edit, REST):
			// leave the existing terms alone rather than clearing them.
			if ( ! isset( $_POST[ $nonce ], $_POST[ 'ak_tax_' . $taxonomy ] ) ) {
				continue;
			}
			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ $nonce ] ) ), 'ak_tax_' . $taxonomy ) ) {
				continue;
			}
			$term_id = (int) $_POST[ 'ak_tax_' . $taxonomy ];
			wp_set_object_terms( $post_id, $term_id ? array( $term_id ) : array(), $taxonomy, false );
		}
	}
);
