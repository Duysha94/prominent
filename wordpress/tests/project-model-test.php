<?php
/**
 * AK Core Project model — test suite.
 *
 * Runs against a REAL WordPress install. The rules under test are all rules
 * about what the site is allowed to CLAIM, so a passing suite is the guarantee
 * that a future change cannot quietly reintroduce an inferred project type, a
 * published fixture, an empty filter, or a capture failure rendered as
 * portfolio work.
 *
 * Usage: copy beside wp-load.php and run
 *
 *   php project-model-test.php
 *
 * It mutates the database it runs against. Never point it at production.
 *
 * @package ak-zeyna-child
 */

$_SERVER['HTTP_HOST'] = '127.0.0.1:9410';
$_SERVER['REQUEST_URI'] = '/wp-admin/';
$_SERVER['REQUEST_METHOD'] = 'GET';
define( 'WP_ADMIN', true );
require dirname( __FILE__ ) . '/wp-load.php';
wp_set_current_user( 1 );

$pass = 0;
$fail = 0;
function ok( $c, $m ) {
	global $pass, $fail;
	if ( $c ) {
		$pass++;
		echo "  PASS  $m\n";
	} else {
		$fail++;
		echo "  FAIL  $m\n";
	}
}
function project( $title ) {
	$p = get_page_by_title( $title, OBJECT, AK_PROJECT_CPT );
	return $p ? $p->ID : 0;
}

echo "\n— Model registration —\n";
ok( post_type_exists( AK_PROJECT_CPT ), 'ak_project post type exists' );
foreach ( array( 'ak_relationship', 'ak_project_type', 'ak_capability' ) as $tax ) {
	ok( taxonomy_exists( $tax ), "$tax taxonomy exists" );
}
ok( ! post_type_exists( 'portfolio' ) || 'work' !== get_post_type_object( 'portfolio' )->rewrite['slug'],
	'no second post type claims /work/' );

echo "\n— The factual layer is complete —\n";
$movements = ak_movements();
ok( 6 === count( $movements ), 'six movements' );
$areas = 0;
foreach ( $movements as $m ) {
	$areas += count( $m['areas'] );
}
ok( 8 === $areas, 'all eight confirmed practice areas are present' );
ok( ak_service_count() > 40, 'the full service list is carried (' . ak_service_count() . ' services)' );

// Every service must exist as a term BENEATH its movement. This is the check
// that stops the editorial layer hiding the factual one.
$missing = array();
foreach ( $movements as $slug => $m ) {
	$parent = get_term_by( 'slug', $slug, 'ak_capability' );
	foreach ( ak_movement_services( $m ) as $service_slug => $label ) {
		$term = get_term_by( 'slug', $service_slug, 'ak_capability' );
		if ( ! $term || ! $parent || (int) $term->parent !== (int) $parent->term_id ) {
			$missing[] = $label;
		}
	}
}
ok( ! $missing, 'every service is a term under its movement' . ( $missing ? ': missing ' . implode( ', ', array_slice( $missing, 0, 3 ) ) : '' ) );

echo "\n— A URL is not a project type —\n";
/*
 * The rule under test is INFERENCE, not existence. Website / Digital is a
 * legitimate type — some engagements really are primarily a website, an
 * e-commerce build or a digital ecosystem — and a person may select it. What
 * must never happen is a URL selecting it, or any type, on its own.
 */
$typed_from_url = array();
foreach ( ak_published_projects() as $proj ) {
	$has_url = (bool) ak_project_meta( 'ak_url', '', $proj->ID );
	$type    = ak_project_type( $proj->ID );
	if ( $has_url && $type && 'website-digital' === $type->slug && ! get_post_meta( $proj->ID, '_ak_type_seeded', true ) ) {
		$typed_from_url[] = $proj->post_title;
	}
}
ok( ! $typed_from_url, 'no project was typed Website / Digital merely for having a URL' );
$with_url = 0;
$typed = 0;
foreach ( ak_published_projects() as $proj ) {
	if ( ak_project_meta( 'ak_url', '', $proj->ID ) ) {
		$with_url++;
		if ( ak_project_type( $proj->ID ) ) {
			$typed++;
		}
	}
}
ok( $with_url > $typed, "projects with a URL outnumber typed ones ($with_url with an address, $typed classified)" );
foreach ( array( 'Wolax', 'Lenie Boya', 'Show Me Your Nails' ) as $title ) {
	$id = project( $title );
	ok( $id && ak_project_meta( 'ak_url', '', $id ), "$title has a live address" );
	ok( $id && null === ak_project_type( $id ), "$title has NO project type despite having one" );
}
$lfd = project( 'London Fashion Day' );
$type = $lfd ? ak_project_type( $lfd ) : null;
ok( $type && 'platform' === $type->slug, 'London Fashion Day is a Platform, not a website build' );

echo "\n— Website preview states —\n";
ok( 'unavailable' === ak_website_module( $lfd )['state'],
	'an unverified capture resolves to UNAVAILABLE, not to a pending plate' );
// Render the module in its unavailable state and prove nothing reaches the page.
ob_start();
ak_render_website_module( $lfd, ak_website_module( $lfd ) );
$rendered = ob_get_clean();
ok( '' === trim( $rendered ), 'UNAVAILABLE renders no markup at all' );
ok( false === stripos( $rendered, 'pending' ) && false === stripos( $rendered, 'unavailable' ) && false === stripos( $rendered, 'failed' ),
	'no capture status wording can reach the front end' );

// Owner-supplied media outranks the automatic capture, and rescues the module.
update_post_meta( $lfd, 'ak_wp_mode', 'manual' );
update_post_meta( $lfd, 'ak_wp_desktop_id', 999999 );
ok( 'manual' === ak_website_module( $lfd )['state'], 'owner-supplied media gives MANUAL' );
update_post_meta( $lfd, 'ak_wp_mode', 'auto' );
ok( 'manual' === ak_website_module( $lfd )['state'],
	'AUTO with no verified capture falls back to owner media rather than failing' );
update_post_meta( $lfd, 'ak_wp_desktop_id', 0 );
update_post_meta( $lfd, 'ak_wp_enabled', 0 );
ok( 'unavailable' === ak_website_module( $lfd )['state'], 'the owner can switch the module off entirely' );
update_post_meta( $lfd, 'ak_wp_enabled', 1 );

echo "\n— Fixtures are never public —\n";
$fixtures = get_posts( array( 'post_type' => AK_PROJECT_CPT, 'posts_per_page' => -1, 'fields' => 'ids',
	'meta_key' => 'ak_fixture', 'meta_value' => '1' ) );
ok( count( $fixtures ) >= 3, 'presentation-mode fixtures exist (' . count( $fixtures ) . ')' );
$public_ids = wp_list_pluck( ak_published_projects(), 'ID' );
ok( ! array_intersect( $fixtures, $public_ids ), 'no fixture appears in the public register' );
$counted = 0;
foreach ( ak_work_filters() as $slug => $f ) {
	$counted += ( 'all' === $slug ) ? 0 : $f['count'];
}
ok( $counted <= count( $public_ids ), 'fixtures contribute nothing to filter counts' );

echo "\n— Filters are generated from published content —\n";
$filters = ak_work_filters();
ok( isset( $filters['all'] ), 'All is always present' );
foreach ( $filters as $slug => $f ) {
	if ( 'all' === $slug ) {
		continue;
	}
	ok( $f['count'] > 0, "filter $slug has real content behind it ({$f['count']})" );
}
ok( ! isset( $filters['image'] ) && ! isset( $filters['film'] ),
	'no empty Image or Film shelf is exposed' );

echo "\n— Layout follows the material —\n";
ok( 'register' === ak_work_layout(), 'with no covers, the index is a register' );
ok( 0 === ak_project_cover( $lfd ), 'a project with no verified media has no cover' );

echo "\n— Seeding is idempotent, and projects are not reconciled —\n";
$before = count( get_posts( array( 'post_type' => AK_PROJECT_CPT, 'posts_per_page' => -1, 'fields' => 'ids', 'post_status' => 'any' ) ) );
ak_seed_projects();
ak_seed_projects();
$after = count( get_posts( array( 'post_type' => AK_PROJECT_CPT, 'posts_per_page' => -1, 'fields' => 'ids', 'post_status' => 'any' ) ) );
ok( $before === $after, "re-seeding creates no duplicates ($before then $after)" );

// A type the owner changed must survive a deployment.
$coolbaba = project( 'COOLBABA' );
$film = get_term_by( 'slug', 'film', 'ak_project_type' );
wp_set_object_terms( $coolbaba, array( (int) $film->term_id ), 'ak_project_type', false );
ak_seed_projects();
$still = ak_project_type( $coolbaba );
ok( $still && 'film' === $still->slug, "the owner's own classification survives a deployment" );
// Put it back.
$media = get_term_by( 'slug', 'media-editorial', 'ak_project_type' );
wp_set_object_terms( $coolbaba, array( (int) $media->term_id ), 'ak_project_type', false );

// The retire pass must never delete a project.
$report = array( 'deleted' => array(), 'skipped' => array() );
ak_retire_obsolete( ak_manifest(), $report );
$survivors = count( get_posts( array( 'post_type' => AK_PROJECT_CPT, 'posts_per_page' => -1, 'fields' => 'ids', 'post_status' => 'any' ) ) );
ok( $survivors === $after, "the retire pass deletes no projects ($survivors remain)" );

echo "\n— Website / Digital is a real type, but never inferred —\n";
$types = ak_project_type_terms();
ok( isset( $types['website-digital'] ), 'Website / Digital is a selectable project type' );
ok( 'digital' === $types['website-digital']['mode'], 'it selects a website-led presentation mode' );
ok( isset( $types['retail-ecommerce'] ) && 'digital' === $types['retail-ecommerce']['mode'], 'Retail / E-commerce leads with the website too' );
// The module and the type stay separate in both directions.
$lfd_module = ak_website_module( $lfd );
ok( 'platform' === ak_project_type( $lfd )->slug,
	'a Platform carrying a website URL is still a Platform' );
// Give a Platform a working website module: it must not change type.
update_post_meta( $lfd, 'ak_wp_mode', 'manual' );
update_post_meta( $lfd, 'ak_wp_desktop_id', 999999 );
ok( 'manual' === ak_website_module( $lfd )['state'] && 'platform' === ak_project_type( $lfd )->slug,
	'a Platform can show its live site without becoming a Website project' );
update_post_meta( $lfd, 'ak_wp_mode', 'auto' );
update_post_meta( $lfd, 'ak_wp_desktop_id', 0 );
// Default module order differs by mode, and only by mode.
$digital_order = wp_list_pluck( ak_default_modules( $lfd, 'digital' ), 'type' );
$platform_order = wp_list_pluck( ak_default_modules( $lfd, 'assembled' ), 'type' );
ok( 'website' === reset( $digital_order ), 'digital mode leads with the website module' );
ok( 'website' === end( $platform_order ), 'every other mode places it last' );

echo "\n— Public navigation is concise; the service vocabulary stays rich —\n";
ok( count( ak_work_filter_labels() ) <= 7, 'at most seven editorial filters (' . count( ak_work_filter_labels() ) . ')' );
ok( ak_service_count() > 40, 'the detailed service vocabulary is unchanged (' . ak_service_count() . ')' );
$cap_tax = get_taxonomy( 'ak_capability' );
ok( ! $cap_tax->publicly_queryable, 'capabilities are not 49 public archives' );
ok( false === $cap_tax->rewrite, 'capabilities have no public permalinks' );
ok( $cap_tax->show_ui, 'capabilities are still editable in the admin' );

echo "\n— Project codes are persistent data, not a calculation —\n";
$code_before = get_post_meta( $lfd, 'ak_code', true );
ok( 'AK-LFD' === $code_before, "London Fashion Day carries a readable stored code ($code_before)" );
$all_stored = true;
foreach ( ak_published_projects() as $proj ) {
	if ( '' === (string) get_post_meta( $proj->ID, 'ak_code', true ) ) {
		$all_stored = false;
	}
}
ok( $all_stored, 'every published project has a stored code' );
// Nothing about the code may come from the post ID.
$derived_from_id = false;
foreach ( ak_published_projects() as $proj ) {
	if ( false !== strpos( (string) get_post_meta( $proj->ID, 'ak_code', true ), (string) $proj->ID ) ) {
		$derived_from_id = true;
	}
}
ok( ! $derived_from_id, 'no code contains its post ID' );
// Owner override, then a deployment.
update_post_meta( $lfd, 'ak_code', 'AK-LONDON-01' );
ak_mark_owner_edited( $lfd, 'ak_code' );
ak_seed_projects();
ok( 'AK-LONDON-01' === get_post_meta( $lfd, 'ak_code', true ), "an owner's own code survives a deployment" );
ok( 'AK-LONDON-01' === ak_project_code( $lfd ), 'and it is what renders' );
update_post_meta( $lfd, 'ak_code', $code_before );

echo "\n— Field ownership is enforced —\n";
ok( 'release' === ak_field_class( 'ak_fixture' ), 'the fixture flag is release-managed' );
ok( 'release' === ak_field_class( 'tax:ak_relationship' ), 'the confirmed relationship is release-managed' );
ok( 'derived' === ak_field_class( 'ak_wp_status' ), 'capture status is derived' );
ok( 'owner' === ak_field_class( 'ak_url' ), 'the website URL is owner-managed' );
ok( 'owner' === ak_field_class( 'ak_modules' ), 'modules are owner-managed' );
ok( 'owner' === ak_field_class( 'ak_never_heard_of_it' ), 'an unlisted field defaults to owner-managed' );

// Representative owner-managed content, edited, then put through a deployment.
$ofd = project( 'Odessa Fashion Day' );
$edits = array(
	'ak_url'          => 'https://ofd.example/edited-by-the-owner',
	'ak_owner'        => 'Edited owner line',
	'ak_year'         => '2019',
	'ak_location'     => 'Odessa',
	'ak_wp_narrative' => 'A paragraph the owner wrote.',
	'ak_credits'      => "Photography: Someone\nDirection: Someone else",
	'ak_wp_mode'      => 'manual',
);
foreach ( $edits as $key => $value ) {
	update_post_meta( $ofd, $key, $value );
	ak_mark_owner_edited( $ofd, $key );
}
wp_update_post( array( 'ID' => $ofd, 'post_title' => 'Odessa Fashion Day', 'post_excerpt' => 'An owner-written summary.', 'menu_order' => 42 ) );
ak_mark_owner_edited( $ofd, 'post_excerpt' );
ak_mark_owner_edited( $ofd, 'menu_order' );
// Two deployments, because a bug that survives one often fires on the next.
ak_seed_projects();
ak_seed_projects();
foreach ( $edits as $key => $value ) {
	ok( $value === get_post_meta( $ofd, $key, true ), "owner-managed $key survives a deployment" );
}
ok( 'An owner-written summary.' === get_post( $ofd )->post_excerpt, 'owner-managed excerpt survives' );
ok( 42 === (int) get_post_field( 'menu_order', $ofd ), 'owner-managed ordering survives' );

// Clearing a field is an edit too: the seed must not helpfully put it back.
update_post_meta( $ofd, 'ak_url', '' );
ak_mark_owner_edited( $ofd, 'ak_url' );
ak_seed_projects();
ok( '' === get_post_meta( $ofd, 'ak_url', true ), 'a field the owner CLEARED stays cleared' );
update_post_meta( $ofd, 'ak_url', 'https://ofd.org.ua' );

// Release-managed state is restored, because the build owns it.
wp_set_object_terms( $ofd, array(), 'ak_relationship', false );
ak_seed_projects();
$rel = ak_project_relationship( $ofd );
ok( $rel && 'ak-owned' === $rel->slug, 'release-managed relationship is restored when it drifts' );
$fixture_id = get_posts( array( 'post_type' => AK_PROJECT_CPT, 'posts_per_page' => 1, 'fields' => 'ids',
	'meta_key' => 'ak_fixture', 'meta_value' => '1' ) )[0];
delete_post_meta( $fixture_id, 'ak_fixture' );
ak_seed_projects();
ok( '1' == get_post_meta( $fixture_id, 'ak_fixture', true ), 'release-managed fixture flag is restored when it drifts' );

echo "\n— Previously found defects stay fixed —\n";
$home = wp_remote_get( home_url( '/' ) );
$work = wp_remote_get( get_post_type_archive_link( AK_PROJECT_CPT ) );
$case = wp_remote_get( get_permalink( $lfd ) );
$svc  = wp_remote_get( home_url( '/services/' ) );
$bodies = array( 'home' => wp_remote_retrieve_body( $home ), 'work' => wp_remote_retrieve_body( $work ),
	'case' => wp_remote_retrieve_body( $case ), 'services' => wp_remote_retrieve_body( $svc ) );
foreach ( $bodies as $name => $body ) {
	if ( ! $body ) {
		ok( false, "$name page could not be fetched" );
		continue;
	}
	ok( false === strpos( $body, '<h5 class="site-title"' ), "$name: no h5 site-title before the h1" );
	ok( false !== strpos( $body, '<main' ), "$name: has a <main> landmark" );
	ok( false === stripos( $body, 'capture pending' ) && false === stripos( $body, 'preview unavailable' )
		&& false === stripos( $body, 'capture failed' ), "$name: no capture status wording on the front end" );
	ok( false === strpos( $body, 's0.wp.com/mshots' ), "$name: no unverified capture URL is emitted" );
}
ok( false === strpos( $bodies['home'], 'Built and run by the studio' ),
	'home: the false ownership grouping over client projects is gone' );
ok( false !== strpos( $bodies['home'], 'Commissioned' ),
	'home: client work is presented as commissioned' );

echo "\n$pass passed, $fail failed\n";
exit( $fail ? 1 : 0 );
