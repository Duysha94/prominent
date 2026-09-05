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
ok( ! array_key_exists( 'website-digital', ak_project_type_terms() ), 'Website / Digital is not a project type' );
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

echo "\n$pass passed, $fail failed\n";
exit( $fail ? 1 : 0 );
