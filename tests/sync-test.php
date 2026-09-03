<?php
/**
 * Sync logic test.
 *
 * `ak_sync_content()` trashes posts, so its decision table is verified here
 * against a fake database rather than trusted. Run: php tests/sync-test.php
 */

define( 'ABSPATH', __DIR__ );
define( 'AK_CHILD_VERSION', '1.2.0' );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['db']    = array();   // id => post object
$GLOBALS['meta']  = array();   // id => [key => value]
$GLOBALS['next']  = 1;
$GLOBALS['trash'] = array();

class FakePost {
	public $ID, $post_type, $post_name, $post_title, $post_content, $post_excerpt, $menu_order = 0;
	public function __construct( $a ) { foreach ( $a as $k => $v ) { $this->$k = $v; } }
}

function fake_insert( $type, $slug, $title, $content = '', $excerpt = '' ) {
	$id = $GLOBALS['next']++;
	$GLOBALS['db'][ $id ] = new FakePost( array(
		'ID' => $id, 'post_type' => $type, 'post_name' => $slug,
		'post_title' => $title, 'post_content' => $content, 'post_excerpt' => $excerpt,
	) );
	return $id;
}

// ── WordPress surface the sync code touches ────────────────────────────
function get_option( $k, $d = false ) { return $GLOBALS['opt'][ $k ] ?? $d; }
function update_option( $k, $v ) { $GLOBALS['opt'][ $k ] = $v; return true; }
function set_transient( $k, $v, $t = 0 ) { $GLOBALS['tr'][ $k ] = $v; return true; }
function get_transient( $k ) { return $GLOBALS['tr'][ $k ] ?? false; }
function delete_transient( $k ) { unset( $GLOBALS['tr'][ $k ] ); }
function add_action( ...$a ) {}
function add_filter( ...$a ) {}
function __( $t, $d = '' ) { return $t; }
function esc_html( $t ) { return $t; }
function esc_html__( $t, $d = '' ) { return $t; }
function esc_attr( $t ) { return $t; }
function current_user_can( $c ) { return true; }
function get_current_screen() { return null; }
function wp_json_encode( $v ) { return json_encode( $v ); }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function get_stylesheet_directory() { return dirname( __DIR__ ) . '/wordpress/ak-zeyna-child'; }
function home_url( $p = '/' ) { return 'https://akbrand.studio' . $p; }
function ak_studio_email() { return 'ak@akbrand.studio'; }
function taxonomy_exists( $t ) { return true; }
function post_type_exists( $t ) { return 'wpcf7_contact_form' === $t; }
function wp_set_object_terms( ...$a ) {}
function flush_rewrite_rules() {}
function is_wp_error( $x ) { return false; }
function get_theme_mod( $k, $d = array() ) { return $GLOBALS['mods'][ $k ] ?? $d; }
function set_theme_mod( $k, $v ) { $GLOBALS['mods'][ $k ] = $v; }
function wp_get_nav_menu_object( $n ) { return $GLOBALS['menu'] ?? false; }
function wp_create_nav_menu( $n ) { $GLOBALS['menu'] = (object) array( 'term_id' => 99 ); return 99; }
function wp_get_nav_menu_items( $id ) { return $GLOBALS['menu_items'] ?? array(); }
function get_post_type_archive_link( $t ) { return 'https://akbrand.studio/work/'; }
function untrailingslashit( $s ) { return rtrim( $s, '/\\' ); }
function wp_update_nav_menu_item( $m, $i, $a ) { $GLOBALS['menu_items'][] = (object) array( 'object_id' => $a['menu-item-object-id'] ?? 0, 'url' => $a['menu-item-url'] ?? '' ); }
function ak_wire_imported_content() { $GLOBALS['wired'] = true; }

function get_page_by_path( $slug, $out = OBJECT, $type = 'page' ) {
	foreach ( $GLOBALS['db'] as $p ) {
		if ( $p->post_name === $slug && $p->post_type === $type ) { return $p; }
	}
	return null;
}
function get_post( $id ) { return $GLOBALS['db'][ $id ] ?? null; }
function get_post_meta( $id, $k, $single = false ) { return $GLOBALS['meta'][ $id ][ $k ] ?? ''; }
function update_post_meta( $id, $k, $v ) { $GLOBALS['meta'][ $id ][ $k ] = $v; return true; }
function wp_insert_post( $a, $err = false ) {
	return fake_insert( $a['post_type'], $a['post_name'], $a['post_title'], $a['post_content'] ?? '', $a['post_excerpt'] ?? '' );
}
function wp_update_post( $a ) {
	$p = $GLOBALS['db'][ $a['ID'] ];
	foreach ( array( 'post_title', 'post_content', 'post_excerpt' ) as $f ) {
		if ( isset( $a[ $f ] ) ) { $p->$f = $a[ $f ]; }
	}
	return $a['ID'];
}
function wp_trash_post( $id ) { $GLOBALS['trash'][] = $GLOBALS['db'][ $id ]->post_name; unset( $GLOBALS['db'][ $id ] ); }
function get_posts( $args ) {
	$out = array();
	foreach ( $GLOBALS['db'] as $id => $p ) {
		if ( ! in_array( $p->post_type, (array) $args['post_type'], true ) ) { continue; }
		if ( isset( $args['meta_key'] ) && empty( $GLOBALS['meta'][ $id ][ $args['meta_key'] ] ) ) { continue; }
		$out[] = $id;
	}
	return $out;
}

require get_stylesheet_directory() . '/inc/content.php';
require get_stylesheet_directory() . '/inc/sync.php';

// ── Fixtures: what a real site looks like before an update ─────────────
$stale_case = fake_insert( 'portfolio', 'old-retired-project', 'Old project' );
$GLOBALS['meta'][ $stale_case ]['_ak_import'] = '1';

$stale_news = fake_insert( 'post', 'old-news-item', 'Old news' );
$GLOBALS['meta'][ $stale_news ]['_ak_import'] = '1';

$their_page = fake_insert( 'page', 'our-own-page', 'A page they wrote themselves' );  // no _ak_import

// A shipped case the founders have since rewritten by hand.
$edited = fake_insert( 'portfolio', 'placeholder-brand-identity', 'REAL CLIENT', '<p>Their real story.</p>', 'their excerpt' );
$GLOBALS['meta'][ $edited ]['_ak_import'] = '1';
$GLOBALS['meta'][ $edited ]['_ak_slug']   = 'placeholder-brand-identity';
$GLOBALS['meta'][ $edited ]['_ak_hash']   = md5( 'something the theme wrote earlier' );

$report = ak_sync_content();

// ── Assertions ────────────────────────────────────────────────────────
$fail = 0;
function check( $label, $cond ) {
	global $fail;
	echo ( $cond ? "  PASS  " : "  FAIL  " ) . $label . "\n";
	if ( ! $cond ) { $fail++; }
}

echo "SYNC DECISION TABLE\n";
check( 'retires an AK case dropped from the manifest', in_array( 'old-retired-project', $GLOBALS['trash'], true ) );
check( 'retires an AK news post dropped from the manifest', in_array( 'old-news-item', $GLOBALS['trash'], true ) );
check( "NEVER touches a page the theme did not create", ! in_array( 'our-own-page', $GLOBALS['trash'], true ) );
check( 'creates every manifest page', count( array_intersect( array( 'home', 'services', 'about', 'contact', 'journal' ), $report['created'] ) ) === 5 );
check( 'creates NO page on the /work/ route (archive owns it)', ! in_array( 'work', $report['created'], true ) );
check( 'creates the case studies and journal entries', count( $report['created'] ) >= 14 );
check( 'leaves a hand-edited case alone', in_array( 'placeholder-brand-identity', $report['skipped'], true ) );
$e = get_page_by_path( 'placeholder-brand-identity', OBJECT, 'portfolio' );
check( "  ...and its words survive verbatim", $e && 'REAL CLIENT' === $e->post_title && str_contains( $e->post_content, 'Their real story' ) );
check( 'builds the Primary menu', in_array( 'Primary menu', $report['created'], true ) );
check( 'hangs it on menu-1', ! empty( $GLOBALS['mods']['nav_menu_locations']['menu-1'] ) );
check( 'creates the contact form', in_array( 'Contact form', $report['created'], true ) );
check( 'wires the front page', ! empty( $GLOBALS['wired'] ) );

// Second run must be a no-op — sync is idempotent.
$before = count( $GLOBALS['db'] );
$second = ak_sync_content();
check( 'second run creates nothing new (idempotent)', empty( $second['created'] ) && count( $GLOBALS['db'] ) === $before );
check( 'second run updates nothing', empty( $second['updated'] ) );
check( 'second run trashes nothing', empty( $second['retired'] ) );

echo $fail ? "\n$fail FAILED\n" : "\nAll checks passed.\n";
exit( $fail ? 1 : 0 );
