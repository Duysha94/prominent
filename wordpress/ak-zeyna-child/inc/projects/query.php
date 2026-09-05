<?php
/**
 * Public queries over projects.
 *
 * Two rules live here, and both are the same rule applied to different
 * things: what the public sees is derived from what actually exists.
 *
 *   FILTERS  a filter renders when a published project falls under it, and
 *            not before. No IMAGE (0), no empty shelves.
 *   LAYOUT   a record with cover media renders as a visual composition; a
 *            record without renders as a typographic entry. Never a grid of
 *            identical placeholder plates.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fixtures are internal, everywhere, always.
 *
 * They exist so the Image, Motion and Document modes can be proven before
 * real material arrives. They are excluded from the Work index, the filters,
 * the filter counts, related work, the homepage, feeds and the sitemap, and
 * they are reachable only while logged in. They are deleted, not published,
 * once the real project exists.
 */
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( current_user_can( 'edit_posts' ) ) {
			return;
		}
		$types = (array) $query->get( 'post_type' );
		if ( ! in_array( AK_PROJECT_CPT, $types, true ) && ! $query->is_feed() && ! $query->is_search() ) {
			return;
		}
		$meta = (array) $query->get( 'meta_query' );
		$meta[] = array(
			'relation' => 'OR',
			array( 'key' => 'ak_fixture', 'compare' => 'NOT EXISTS' ),
			array( 'key' => 'ak_fixture', 'value' => '1', 'compare' => '!=' ),
		);
		$query->set( 'meta_query', $meta );
	}
);

/**
 * A fixture is 404 to the public, not merely absent from the index.
 */
add_action(
	'template_redirect',
	function () {
		if ( ! is_singular( AK_PROJECT_CPT ) || current_user_can( 'edit_posts' ) ) {
			return;
		}
		if ( ak_project_meta( 'ak_fixture', false ) ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
			include get_query_template( '404' );
			exit;
		}
	}
);

/**
 * Fixtures out of the sitemap too.
 */
add_filter(
	'wp_sitemaps_posts_query_args',
	function ( $args, $post_type ) {
		if ( AK_PROJECT_CPT !== $post_type ) {
			return $args;
		}
		$args['meta_query'] = array(
			'relation' => 'OR',
			array( 'key' => 'ak_fixture', 'compare' => 'NOT EXISTS' ),
			array( 'key' => 'ak_fixture', 'value' => '1', 'compare' => '!=' ),
		);
		return $args;
	},
	10,
	2
);

/**
 * Every published, public project.
 *
 * @param int $limit How many.
 * @return WP_Post[]
 */
function ak_published_projects( $limit = -1 ) {
	return get_posts(
		array(
			'post_type'      => AK_PROJECT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			'meta_query'     => array(
				'relation' => 'OR',
				array( 'key' => 'ak_fixture', 'compare' => 'NOT EXISTS' ),
				array( 'key' => 'ak_fixture', 'value' => '1', 'compare' => '!=' ),
			),
		)
	);
}

/**
 * The Work filters that have content behind them.
 *
 * Returns `all` plus one entry per editorial filter with at least one
 * published project. Untyped projects appear only under `all`, so the counts
 * deliberately do not sum to it: a count is a fact about published content,
 * never a target to fill.
 *
 * @return array[] slug => array( label, count )
 */
function ak_work_filters() {
	$labels = ak_work_filter_labels();
	$types  = ak_project_type_terms();
	$counts = array();

	foreach ( ak_published_projects() as $project ) {
		$type = ak_project_type( $project->ID );
		if ( ! $type || ! isset( $types[ $type->slug ] ) ) {
			continue;
		}
		$filter = $types[ $type->slug ]['filter'];
		$counts[ $filter ] = isset( $counts[ $filter ] ) ? $counts[ $filter ] + 1 : 1;
	}

	$out = array( 'all' => array( 'label' => __( 'All', 'ak-zeyna-child' ), 'count' => count( ak_published_projects() ) ) );
	foreach ( $labels as $slug => $label ) {
		if ( ! empty( $counts[ $slug ] ) ) {
			$out[ $slug ] = array( 'label' => $label, 'count' => $counts[ $slug ] );
		}
	}
	return $out;
}

/**
 * Which editorial filter a project falls under, or '' when it has no type.
 *
 * @param int $post_id Project ID.
 * @return string
 */
function ak_project_filter( $post_id = 0 ) {
	$type = ak_project_type( $post_id );
	if ( ! $type ) {
		return '';
	}
	$types = ak_project_type_terms();
	return isset( $types[ $type->slug ] ) ? $types[ $type->slug ]['filter'] : '';
}

/**
 * The cover a project index card can lead with, or 0.
 *
 * Real media only, in preference order: the featured image, the owner's
 * manual desktop preview, a verified automatic capture, the first gallery
 * image. A capture that has not been verified is not a cover — that is the
 * whole point of the verification, and it is why the index never becomes a
 * wall of placeholder plates.
 *
 * @param int $post_id Project ID.
 * @return int|string Attachment ID, or a capture URL, or 0.
 */
function ak_project_cover( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( has_post_thumbnail( $post_id ) ) {
		return (int) get_post_thumbnail_id( $post_id );
	}

	$manual = (int) ak_project_meta( 'ak_wp_desktop_id', 0, $post_id );
	if ( $manual ) {
		return $manual;
	}

	$module = ak_website_module( $post_id );
	if ( 'auto' === $module['state'] && $module['capture'] ) {
		return $module['capture'];
	}

	$gallery = ak_sanitize_id_list( ak_project_meta( 'ak_gallery', '', $post_id ) );
	if ( $gallery ) {
		$ids = explode( ',', $gallery );
		return (int) $ids[0];
	}

	return 0;
}

/**
 * Does this project have real media to lead with?
 *
 * @param int $post_id Project ID.
 * @return bool
 */
function ak_project_has_media( $post_id = 0 ) {
	return (bool) ak_project_cover( $post_id );
}

/**
 * How the Work index should render.
 *
 * `grid` once ANY published project has real media, `register` while none
 * does. Within a grid, records without media stay typographic entries rather
 * than becoming placeholder plates — so the index is mixed for as long as the
 * material is mixed, and it becomes fully visual on its own as covers arrive.
 *
 * @return string `register` or `grid`
 */
function ak_work_layout() {
	foreach ( ak_published_projects() as $project ) {
		if ( ak_project_has_media( $project->ID ) ) {
			return 'grid';
		}
	}
	return 'register';
}

/**
 * The project code — read, never recomputed.
 *
 * The Tech Pack language leans on these: a code is printed in the margin rail,
 * in the register, on the card, in the "next project" link and in the spec
 * block, and the owner is expected to be able to say one out loud. So it is
 * stored project data, not a render-time calculation.
 *
 * Two earlier versions were wrong in the same way. `AK-O-YY-{post_id % 1000}`
 * produced AK-O-—-096..105, a run of numbers that says nothing except how many
 * rows the database happened to hold, and that changes on any re-import.
 * Deriving it from the project's position in its register was no better: it is
 * stable only until someone reorders the list or a record is unpublished, at
 * which point every code after it silently shifts.
 *
 * A code is now generated ONCE, written to `ak_code`, and read from there
 * forever. It is owner-managed, so the owner may retype it and no deployment
 * will overwrite them.
 *
 * @param int $post_id Project ID.
 * @return string
 */
function ak_project_code( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$stored  = (string) get_post_meta( $post_id, 'ak_code', true );
	if ( '' !== $stored ) {
		return $stored;
	}

	// Absent only for a project created outside the seeder. Mint one and keep
	// it — the generation happens at most once per project, ever.
	$code = ak_mint_project_code( get_the_title( $post_id ), $post_id );
	update_post_meta( $post_id, 'ak_code', $code );
	return $code;
}

/**
 * Mint a code from a title: AK-LFD, AK-KEKA, AK-SMYN.
 *
 * Initials for a multi-word title, the first four letters for a single word.
 * Collisions get a numeric suffix, checked against what is actually stored so
 * two projects can never share a code.
 *
 * @param string $title   Project title.
 * @param int    $post_id Project being minted for, excluded from the collision check.
 * @return string
 */
function ak_mint_project_code( $title, $post_id = 0 ) {
	$clean = trim( preg_replace( '/[^A-Za-z0-9 ]/', ' ', (string) $title ) );
	$words = preg_split( '/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY );

	if ( ! $words ) {
		$stem = 'PRJ';
	} elseif ( count( $words ) > 1 ) {
		$stem = '';
		foreach ( $words as $word ) {
			$stem .= strtoupper( substr( $word, 0, 1 ) );
		}
		$stem = substr( $stem, 0, 5 );
	} else {
		$stem = strtoupper( substr( $words[0], 0, 4 ) );
	}

	$base  = 'AK-' . $stem;
	$code  = $base;
	$n     = 1;
	while ( ak_code_taken( $code, $post_id ) ) {
		$n++;
		$code = $base . '-' . $n;
	}
	return $code;
}

/**
 * Is a code already in use by another project?
 *
 * @param string $code    Candidate code.
 * @param int    $post_id Project to ignore.
 * @return bool
 */
function ak_code_taken( $code, $post_id = 0 ) {
	$hits = get_posts(
		array(
			'post_type'      => AK_PROJECT_CPT,
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => 2,
			'fields'         => 'ids',
			'meta_key'       => 'ak_code',
			'meta_value'     => $code,
		)
	);
	return (bool) array_diff( array_map( 'intval', $hits ), array( (int) $post_id ) );
}
