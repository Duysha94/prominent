<?php
/**
 * Seeding the taxonomies and the confirmed project register.
 *
 * Runs through the deployment engine, so it is version-gated, idempotent and
 * reconciling like everything else the theme owns. Terms are invariants —
 * they are enforced on every deployment, because a term someone deleted by
 * accident should come back, whereas a project is created once and then
 * belongs to the owner.
 *
 * WHAT IS SEEDED AND WHAT IS NOT
 *
 * Seeded: title, relationship, address, owned/client status. Those are
 * confirmed facts.
 *
 * Not seeded: project type on anything whose nature is not established,
 * capabilities, descriptions, dates, deliverables, results, metrics. The
 * owner supplies those over time from the admin, and a classification is
 * never locked into the seed because it merely seems likely.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ensure every canonical term exists.
 *
 * @return array Report of created terms.
 */
function ak_seed_terms() {
	$created = array();

	foreach ( ak_relationship_terms() as $slug => $term ) {
		$created = array_merge( $created, ak_ensure_term( $term['name'], 'ak_relationship', $slug, 0, $term['desc'] ) );
	}

	foreach ( ak_project_type_terms() as $slug => $term ) {
		$created = array_merge( $created, ak_ensure_term( $term['name'], 'ak_project_type', $slug ) );
	}

	/*
	 * Capabilities: the six movements are parent terms, every service is a
	 * child. This is the mechanism that stops the editorial layer hiding the
	 * factual one — naming a group IMAGE cannot conceal "editorial
	 * photography" when that phrase is a term beneath it.
	 */
	foreach ( ak_movements() as $slug => $movement ) {
		$parent_id = 0;
		$existing  = get_term_by( 'slug', $slug, 'ak_capability' );
		if ( $existing && ! is_wp_error( $existing ) ) {
			$parent_id = (int) $existing->term_id;
		} else {
			$made = wp_insert_term( $movement['name'], 'ak_capability', array( 'slug' => $slug, 'description' => $movement['summary'] ) );
			if ( ! is_wp_error( $made ) ) {
				$parent_id = (int) $made['term_id'];
				ak_mark_term( $parent_id, 'ak_capability', 'movement_' . $slug );
				$created[] = $movement['name'];
			}
		}
		if ( ! $parent_id ) {
			continue;
		}
		foreach ( ak_movement_services( $movement ) as $service_slug => $label ) {
			$created = array_merge( $created, ak_ensure_term( $label, 'ak_capability', $service_slug, $parent_id ) );
		}
	}

	return $created;
}

/**
 * Create a term if it is missing, and adopt it if it already exists on the
 * canonical slug.
 *
 * Adoption matters: a term the owner made by hand on the right slug is the
 * right term, and replacing it would orphan every project assigned to it.
 *
 * @param string $name        Term name.
 * @param string $taxonomy    Taxonomy.
 * @param string $slug        Canonical slug.
 * @param int    $parent      Parent term ID.
 * @param string $description Optional description.
 * @return array Names created.
 */
function ak_ensure_term( $name, $taxonomy, $slug, $parent = 0, $description = '' ) {
	$existing = get_term_by( 'slug', $slug, $taxonomy );
	if ( $existing && ! is_wp_error( $existing ) ) {
		ak_mark_term( (int) $existing->term_id, $taxonomy, $taxonomy . '_' . $slug );
		return array();
	}
	$made = wp_insert_term(
		$name,
		$taxonomy,
		array( 'slug' => $slug, 'parent' => $parent, 'description' => $description )
	);
	if ( is_wp_error( $made ) ) {
		return array();
	}
	ak_mark_term( (int) $made['term_id'], $taxonomy, $taxonomy . '_' . $slug );
	return array( $name );
}


/**
 * The ID of a seeded post, or 0, plus any duplicates cleaned up.
 *
 * ak_find_seeded_post() returns array( post, duplicates ) rather than an ID —
 * an array that is always truthy, so treating it as an ID meant the "does this
 * already exist" test passed on the very first run, every project fell
 * straight through to the repair branch, and nothing was ever created. The
 * shape is unwrapped once, here.
 *
 * Duplicates are healed the same way the rest of the engine heals them: the
 * lowest ID wins and the rest are removed, so a double-run can never leave two
 * London Fashion Days in the register.
 *
 * @param string $seed_key Seed key.
 * @return int Post ID, or 0.
 */
function ak_seeded_project_id( $seed_key ) {
	$found = ak_find_seeded_post( $seed_key );
	foreach ( $found['duplicates'] as $duplicate ) {
		wp_delete_post( $duplicate, true );
	}
	return $found['post'] ? (int) $found['post']->ID : 0;
}

/**
 * The confirmed register.
 *
 * Relationship is confirmed for all ten. Type is supplied ONLY where the
 * owner has established the nature of the entity; where they have not, `type`
 * is null and the record publishes as an unclassified entry until factual
 * information arrives.
 *
 * @return array[]
 */
function ak_project_register() {
	$register = array(
		// AK OWNED.
		array( 'key' => 'lfd', 'title' => 'London Fashion Day', 'rel' => 'ak-owned', 'type' => 'platform', 'url' => 'https://londonfashionday.co.uk', 'order' => 1 ),
		array( 'key' => 'ofd', 'title' => 'Odessa Fashion Day', 'rel' => 'ak-owned', 'type' => 'platform', 'url' => 'https://ofd.org.ua', 'order' => 5 ),
		array( 'key' => 'coolbaba', 'title' => 'COOLBABA', 'rel' => 'ak-owned', 'type' => 'media-editorial', 'url' => 'https://coolbaba.in.ua', 'order' => 2 ),
		array( 'key' => 'prominent', 'title' => 'Prominent Magazine', 'rel' => 'ak-owned', 'type' => 'media-editorial', 'url' => 'https://prominentmagazine.co.uk', 'order' => 6 ),
		array( 'key' => 'fashion-frontier', 'title' => 'Fashion Frontier', 'rel' => 'ak-owned', 'type' => 'platform', 'url' => 'https://fashionfrontier.uk', 'order' => 9 ),
		array( 'key' => 'utrend', 'title' => 'Utrend Store', 'rel' => 'ak-owned', 'type' => 'retail-ecommerce', 'url' => 'https://utrendstore.co.uk', 'order' => 7 ),
		array( 'key' => 'keka', 'title' => 'KEKA', 'rel' => 'ak-owned', 'type' => 'fashion-brand', 'url' => 'https://keka.design', 'order' => 3 ),
		// CLIENT. No type: an address and a client relationship establish that
		// an engagement happened, not what it was.
		array( 'key' => 'wolax', 'title' => 'Wolax', 'rel' => 'client', 'type' => null, 'url' => 'https://wolax.co.uk', 'order' => 4 ),
		array( 'key' => 'lenie-boya', 'title' => 'Lenie Boya', 'rel' => 'client', 'type' => null, 'url' => 'https://lenieboya.com', 'order' => 8 ),
		array( 'key' => 'smyn', 'title' => 'Show Me Your Nails', 'rel' => 'client', 'type' => null, 'url' => 'https://showmeyournails.com', 'order' => 10 ),
	);

	/**
	 * Filter the confirmed project register.
	 *
	 * @param array[] $register Project seed entries.
	 */
	return apply_filters( 'ak_project_register', $register );
}

/**
 * The internal presentation-mode fixtures.
 *
 * Every one carries ak_fixture = 1, which excludes it from every public
 * query. They prove that the Image, Motion and Document modes render before
 * real material exists, and they are deleted rather than published once it
 * does.
 *
 * @return array[]
 */
function ak_project_fixtures() {
	return array(
		array( 'key' => 'fixture-image', 'title' => 'Fixture — image-led presentation', 'type' => 'photography' ),
		array( 'key' => 'fixture-motion', 'title' => 'Fixture — motion-led presentation', 'type' => 'film' ),
		array( 'key' => 'fixture-document', 'title' => 'Fixture — event document presentation', 'type' => 'event' ),
	);
}

/**
 * Create the register and the fixtures, once each.
 *
 * Existing records are adopted by seed key and their taxonomy assignments
 * repaired, but their editorial content is never overwritten: once a project
 * exists it belongs to the owner.
 *
 * @return array Report.
 */
function ak_seed_projects() {
	$report = array( 'created' => array(), 'healed' => array() );

	foreach ( ak_project_register() as $entry ) {
		$post_id = ak_seeded_project_id( 'project_' . $entry['key'] );

		if ( ! $post_id ) {
			$post_id = wp_insert_post(
				array(
					'post_type'   => AK_PROJECT_CPT,
					'post_status' => 'publish',
					'post_title'  => $entry['title'],
					'post_name'   => sanitize_title( $entry['title'] ),
				),
				true
			);
			if ( is_wp_error( $post_id ) ) {
				continue;
			}
			ak_mark_post( $post_id, 'project_' . $entry['key'] );
			update_post_meta( $post_id, 'ak_url', esc_url_raw( $entry['url'] ) );
			update_post_meta( $post_id, 'ak_owner', 'ak-owned' === $entry['rel'] ? 'AK Brand Development Studio' : $entry['title'] );
			// The website module is enabled and left in AUTO. If the capture
			// never verifies, the module simply does not render — the project
			// is publishable from the moment it is created.
			update_post_meta( $post_id, 'ak_wp_enabled', 1 );
			update_post_meta( $post_id, 'ak_wp_mode', 'auto' );
			$report['created'][] = $entry['title'];
		}

		// Position: seeded when still unset, never re-imposed.
		$existing_order = (int) get_post_field( 'menu_order', $post_id );
		if ( ! $existing_order && ! empty( $entry['order'] ) ) {
			wp_update_post( array( 'ID' => $post_id, 'menu_order' => (int) $entry['order'] ) );
		}

		// Relationship is confirmed and is enforced on every deployment.
		$rel = get_term_by( 'slug', $entry['rel'], 'ak_relationship' );
		if ( $rel && ! is_wp_error( $rel ) ) {
			$current = wp_get_object_terms( $post_id, 'ak_relationship', array( 'fields' => 'slugs' ) );
			if ( array( $entry['rel'] ) !== $current ) {
				wp_set_object_terms( $post_id, array( (int) $rel->term_id ), 'ak_relationship', false );
				$report['healed'][] = $entry['title'] . ' — relationship';
			}
		}

		/*
		 * Type is seeded ONCE and never re-enforced. The owner will supply
		 * factual project information over time, and a deployment that
		 * reasserted a seeded type every release would silently overwrite
		 * their correction. Where type is null it is never set at all.
		 */
		if ( $entry['type'] && ! get_post_meta( $post_id, '_ak_type_seeded', true ) ) {
			$type = get_term_by( 'slug', $entry['type'], 'ak_project_type' );
			if ( $type && ! is_wp_error( $type ) ) {
				wp_set_object_terms( $post_id, array( (int) $type->term_id ), 'ak_project_type', false );
			}
			update_post_meta( $post_id, '_ak_type_seeded', 1 );
		}
	}

	foreach ( ak_project_fixtures() as $entry ) {
		$post_id = ak_seeded_project_id( 'project_' . $entry['key'] );
		if ( ! $post_id ) {
			$post_id = wp_insert_post(
				array(
					'post_type'   => AK_PROJECT_CPT,
					'post_status' => 'publish',
					'post_title'  => $entry['title'],
					'post_name'   => sanitize_title( $entry['title'] ),
				),
				true
			);
			if ( is_wp_error( $post_id ) ) {
				continue;
			}
			ak_mark_post( $post_id, 'project_' . $entry['key'] );
			$type = get_term_by( 'slug', $entry['type'], 'ak_project_type' );
			if ( $type && ! is_wp_error( $type ) ) {
				wp_set_object_terms( $post_id, array( (int) $type->term_id ), 'ak_project_type', false );
			}
			$report['created'][] = $entry['title'];
		}
		// The fixture flag is an invariant: a fixture that lost it would
		// become public portfolio material, which is the one thing fixtures
		// must never do.
		if ( ! get_post_meta( $post_id, 'ak_fixture', true ) ) {
			update_post_meta( $post_id, 'ak_fixture', 1 );
			$report['healed'][] = $entry['title'] . ' — fixture flag restored';
		}
	}

	return $report;
}
