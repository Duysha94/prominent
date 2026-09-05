<?php
/**
 * Field ownership: who is allowed to write what, and when.
 *
 * Three classes, and the boundary between them is the contract that makes it
 * safe to ship theme updates to a site the owner is actively editing.
 *
 *   RELEASE-MANAGED  the build owns it. Enforced on every deployment, because
 *                    a site whose structural markers drift cannot be reasoned
 *                    about. Seed keys, the managed marker, the confirmed
 *                    relationship of a canonical seeded record, the fixture
 *                    flag, version and migration state.
 *
 *   OWNER-MANAGED    the owner owns it. The build may SEED an initial value
 *                    into an empty field, and after that it never writes to it
 *                    again — not to correct it, not to restore it, not on a
 *                    later release. Titles, descriptions, project type,
 *                    capabilities, covers, hero media, galleries, video, the
 *                    website URL, manual previews, case-study depth, modules,
 *                    credits, ordering, featured state, presentation choices.
 *
 *   DERIVED          computed from other factual state and stored only as a
 *                    cache. Never authored, never a source of truth.
 *
 * THE RULE THAT MATTERS: once an owner-managed value has been edited, a later
 * deployment must not silently restore the seed value. "Edited" includes
 * clearing a field — an owner who deletes a seeded description meant to delete
 * it, and a deployment that helpfully puts it back is a bug that looks like a
 * haunting.
 *
 * Enforcement is a recorded set of touched keys per project (`_ak_owner_edited`),
 * written on save from the edit screen and consulted by the seeder. It is
 * deliberately not a value comparison: comparing the current value to the seed
 * cannot distinguish "never touched" from "edited back to the same thing", and
 * gets the clearing case wrong.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AK_OWNER_EDITED = '_ak_owner_edited';

/**
 * The ownership class of every project field the build knows about.
 *
 * Anything not listed is owner-managed by default: a field the build does not
 * name is not a field the build may write.
 *
 * @return array<string,string> field key => release|owner|derived
 */
function ak_field_ownership() {
	$map = array(
		// RELEASE-MANAGED — structural, enforced every deployment.
		AK_SEED_KEY         => 'release',
		AK_MANAGED_KEY      => 'release',
		'_ak_legacy'        => 'release',
		'_ak_type_seeded'   => 'release',
		AK_OWNER_EDITED     => 'release',
		'ak_fixture'        => 'release',
		'tax:ak_relationship' => 'release',

		// DERIVED — cached from other state, never authored.
		'ak_wp_auto_ok'     => 'derived',
		'ak_wp_checked'     => 'derived',
		'ak_wp_status'      => 'derived',

		// OWNER-MANAGED — seeded once into an empty field, then never touched.
		'post_title'        => 'owner',
		'post_excerpt'      => 'owner',
		'post_content'      => 'owner',
		'menu_order'        => 'owner',
		'_thumbnail_id'     => 'owner',
		'ak_short_title'    => 'owner',
		'ak_code'           => 'owner',
		'ak_owner'          => 'owner',
		'ak_year'           => 'owner',
		'ak_location'       => 'owner',
		'ak_url'            => 'owner',
		'ak_featured'       => 'owner',
		'ak_credits'        => 'owner',
		'ak_modules'        => 'owner',
		'ak_context'        => 'owner',
		'ak_positioning'    => 'owner',
		'ak_gallery'        => 'owner',
		'ak_film_id'        => 'owner',
		'ak_film_poster'    => 'owner',
		'ak_event_date'     => 'owner',
		'ak_event_venue'    => 'owner',
		'ak_event_role'     => 'owner',
		'ak_wp_enabled'     => 'owner',
		'ak_wp_mode'        => 'owner',
		'ak_wp_desktop_id'  => 'owner',
		'ak_wp_mobile_id'   => 'owner',
		'ak_wp_video_id'    => 'owner',
		'ak_wp_auto_off'    => 'owner',
		'ak_wp_narrative'   => 'owner',
		'tax:ak_project_type' => 'owner',
		'tax:ak_capability'   => 'owner',
	);

	/**
	 * Filter the field ownership map.
	 *
	 * @param array<string,string> $map field key => release|owner|derived.
	 */
	return apply_filters( 'ak_field_ownership', $map );
}

/**
 * The ownership class of one field.
 *
 * @param string $key Field key, or `tax:<taxonomy>`.
 * @return string release|owner|derived
 */
function ak_field_class( $key ) {
	$map = ak_field_ownership();
	return isset( $map[ $key ] ) ? $map[ $key ] : 'owner';
}

/**
 * Has the owner edited this field on this project?
 *
 * @param int    $post_id Project ID.
 * @param string $key     Field key.
 * @return bool
 */
function ak_owner_edited( $post_id, $key ) {
	$edited = get_post_meta( $post_id, AK_OWNER_EDITED, true );
	return is_array( $edited ) && in_array( $key, $edited, true );
}

/**
 * Record that the owner has edited a field.
 *
 * @param int    $post_id Project ID.
 * @param string $key     Field key.
 */
function ak_mark_owner_edited( $post_id, $key ) {
	if ( 'owner' !== ak_field_class( $key ) ) {
		return;
	}
	$edited = get_post_meta( $post_id, AK_OWNER_EDITED, true );
	$edited = is_array( $edited ) ? $edited : array();
	if ( ! in_array( $key, $edited, true ) ) {
		$edited[] = $key;
		update_post_meta( $post_id, AK_OWNER_EDITED, $edited );
	}
}

/**
 * Seed a value into a field, but only if the build is still allowed to.
 *
 * Writes when the field is empty AND the owner has never edited it. Returns
 * whether it wrote, so callers can report honestly.
 *
 * @param int    $post_id Project ID.
 * @param string $key     Meta key.
 * @param mixed  $value   Value to seed.
 * @return bool
 */
function ak_seed_field( $post_id, $key, $value ) {
	if ( 'owner' !== ak_field_class( $key ) ) {
		update_post_meta( $post_id, $key, $value );
		return true;
	}
	if ( ak_owner_edited( $post_id, $key ) ) {
		return false;
	}
	$current = get_post_meta( $post_id, $key, true );
	if ( '' !== $current && null !== $current && 0 !== $current && '0' !== $current ) {
		return false;
	}
	update_post_meta( $post_id, $key, $value );
	return true;
}

/**
 * Record every owner-managed field the edit screen just submitted.
 *
 * Runs at priority 30, after the meta and taxonomy savers at 20, so it sees
 * the values that were actually stored.
 *
 * Presence in the POST is what counts, not a change in value: opening the
 * editor and pressing Update is the owner asserting the current state of the
 * record, including the parts they chose to leave as they are. Anything softer
 * than that lets a deployment overwrite a value the owner has looked at and
 * approved.
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
			if ( 'owner' !== ak_field_class( $key ) ) {
				continue;
			}
			// Checkboxes are absent from the POST when unticked, which is
			// itself an edit — the whole panel was submitted.
			if ( 'boolean' === $field['type'] || isset( $_POST[ $key ] ) ) {
				ak_mark_owner_edited( $post_id, $key );
			}
		}

		foreach ( array( 'ak_project_type', 'ak_capability' ) as $taxonomy ) {
			if ( isset( $_POST[ 'ak_tax_' . $taxonomy ] ) || isset( $_POST[ 'tax_input' ][ $taxonomy ] ) ) {
				ak_mark_owner_edited( $post_id, 'tax:' . $taxonomy );
			}
		}

		foreach ( array( 'post_title', 'post_excerpt', 'post_content', 'menu_order' ) as $core ) {
			if ( isset( $_POST[ $core ] ) || ( 'menu_order' === $core && isset( $_POST['menu_order'] ) ) ) {
				ak_mark_owner_edited( $post_id, $core );
			}
		}
		if ( isset( $_POST['_thumbnail_id'] ) ) {
			ak_mark_owner_edited( $post_id, '_thumbnail_id' );
		}
	},
	30
);

/**
 * A featured image set through the media modal never passes through save_post
 * with a `_thumbnail_id` in $_POST, so it is caught at the meta layer instead.
 */
add_action(
	'updated_post_meta',
	function ( $meta_id, $post_id, $meta_key ) {
		if ( '_thumbnail_id' === $meta_key && AK_PROJECT_CPT === get_post_type( $post_id ) ) {
			ak_mark_owner_edited( $post_id, '_thumbnail_id' );
		}
	},
	10,
	3
);
