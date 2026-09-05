<?php
/**
 * Ownership markers — how the build tells its own objects apart.
 *
 * Everything the AK build creates carries two pieces of metadata:
 *
 *   _ak_managed  = '1'        — this object belongs to the AK build
 *   _ak_seed_key = 'ak_about' — WHICH object in the manifest it is
 *
 * The seed key is the identity. Titles and slugs are not: a title is editable
 * in wp-admin and a slug silently becomes `about-2` the moment anything else
 * claims `about`, so a deployment keyed on either creates a duplicate the
 * first time an editor renames a page. The seed key never changes and is never
 * shown to anyone.
 *
 * The same two markers are used for posts (post meta) and for menus and terms
 * (term meta), so one set of lookups covers every managed object type.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AK_MANAGED_KEY = '_ak_managed';
const AK_SEED_KEY    = '_ak_seed_key';

/**
 * Find the post for a seed key.
 *
 * Returns the OLDEST match and reports any others. More than one match means a
 * previous release created a duplicate; the caller heals it rather than
 * adding a third.
 *
 * @param string $seed_key Stable machine identifier.
 * @return array{post:?WP_Post,duplicates:int[]}
 */
function ak_find_seeded_post( $seed_key ) {
	$ids = get_posts(
		array(
			'post_type'        => 'any',
			'post_status'      => array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ),
			'posts_per_page'   => -1,
			'orderby'          => 'ID',
			'order'            => 'ASC',
			'fields'           => 'ids',
			'suppress_filters' => false,
			'no_found_rows'    => true,
			'meta_query'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => AK_SEED_KEY,
					'value' => $seed_key,
				),
			),
		)
	);

	if ( ! $ids ) {
		return array(
			'post'       => null,
			'duplicates' => array(),
		);
	}

	$keep = array_shift( $ids );
	return array(
		'post'       => get_post( $keep ),
		'duplicates' => $ids,
	);
}

/**
 * Every post this build manages, whatever its seed key.
 *
 * Used to find objects that have dropped out of the manifest.
 *
 * @return int[]
 */
function ak_all_managed_posts() {
	return get_posts(
		array(
			'post_type'      => 'any',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => AK_MANAGED_KEY,
					'compare' => 'EXISTS',
				),
			),
		)
	);
}

/**
 * Stamp ownership onto a post.
 *
 * @param int    $post_id  Post ID.
 * @param string $seed_key Stable machine identifier.
 */
function ak_mark_post( $post_id, $seed_key ) {
	update_post_meta( $post_id, AK_MANAGED_KEY, '1' );
	update_post_meta( $post_id, AK_SEED_KEY, $seed_key );
}

/**
 * Find the term for a seed key — menus, categories, project categories.
 *
 * @param string $seed_key Stable machine identifier.
 * @param string $taxonomy Taxonomy name.
 * @return array{term:?WP_Term,duplicates:int[]}
 */
function ak_find_seeded_term( $seed_key, $taxonomy ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'orderby'    => 'term_id',
			'order'      => 'ASC',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => AK_SEED_KEY,
					'value' => $seed_key,
				),
			),
		)
	);

	if ( is_wp_error( $terms ) || ! $terms ) {
		return array(
			'term'       => null,
			'duplicates' => array(),
		);
	}

	$keep = array_shift( $terms );
	return array(
		'term'       => $keep,
		'duplicates' => wp_list_pluck( $terms, 'term_id' ),
	);
}

/**
 * Stamp ownership onto a term.
 *
 * @param int    $term_id  Term ID.
 * @param string $seed_key Stable machine identifier.
 */
function ak_mark_term( $term_id, $seed_key ) {
	update_term_meta( $term_id, AK_MANAGED_KEY, '1' );
	update_term_meta( $term_id, AK_SEED_KEY, $seed_key );
}

/**
 * Is this object one of ours?
 *
 * @param int    $id   Post or term ID.
 * @param string $kind 'post' or 'term'.
 * @return bool
 */
function ak_is_managed( $id, $kind = 'post' ) {
	return 'term' === $kind
		? (bool) get_term_meta( $id, AK_MANAGED_KEY, true )
		: (bool) get_post_meta( $id, AK_MANAGED_KEY, true );
}
