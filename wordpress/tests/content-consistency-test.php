<?php
/**
 * Site-wide content consistency.
 *
 * Content drifts in a way code does not: a stale string survives in one
 * template, one comment, one alt attribute, one JSON-LD node, long after the
 * visible copy around it has been corrected. Every check below is here because
 * something was actually found in that state.
 *
 * Usage: copy beside wp-load.php and run
 *
 *   php content-consistency-test.php
 *
 * @package ak-zeyna-child
 */

$_SERVER['HTTP_HOST'] = '127.0.0.1:9410';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
require dirname( __FILE__ ) . '/wp-load.php';

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

$routes = array(
	'home'     => home_url( '/' ),
	'about'    => home_url( '/about/' ),
	'services' => home_url( '/services/' ),
	'work'     => get_post_type_archive_link( AK_PROJECT_CPT ),
	'contact'  => home_url( '/contact/' ),
	'journal'  => home_url( '/journal/' ),
	'privacy'  => home_url( '/privacy/' ),
	'search'   => home_url( '/?s=fashion' ),
	'project'  => get_permalink( get_page_by_path( 'london-fashion-day', OBJECT, AK_PROJECT_CPT ) ),
);

$html = array();
foreach ( $routes as $name => $url ) {
	$r = wp_remote_get( $url, array( 'timeout' => 20 ) );
	$html[ $name ] = is_wp_error( $r ) ? '' : wp_remote_retrieve_body( $r );
	ok( '' !== $html[ $name ], "$name responds" );
}

echo "\n— Founder spellings —\n";
/*
 * The old spellings survived in anchor ids and JSON-LD @ids long after the
 * visible names were corrected, which meant the About links and the structured
 * data pointed at fragments that no longer existed.
 */
foreach ( $html as $name => $body ) {
	ok( false === stripos( $body, 'Andrey' ), "$name: no 'Andrey' anywhere in the markup" );
	ok( false === stripos( $body, 'Konstantin' ), "$name: no 'Konstantin' anywhere in the markup" );
}
ok( false !== strpos( $html['about'], 'id="andrii-karakushan"' ), 'about: anchor uses the correct spelling' );
ok( false !== strpos( $html['about'], 'id="kostiantyn-lieontiev"' ), 'about: anchor uses the correct spelling' );
// Every JSON-LD @id fragment must resolve to a real anchor on About.
preg_match_all( '~/about#([a-z-]+)~', $html['home'], $m );
$unresolved = array();
foreach ( array_unique( $m[1] ) as $fragment ) {
	if ( false === strpos( $html['about'], 'id="' . $fragment . '"' ) ) {
		$unresolved[] = $fragment;
	}
}
ok( ! $unresolved, 'structured data: every founder @id resolves to an About anchor' . ( $unresolved ? ' — missing ' . implode( ', ', $unresolved ) : '' ) );

echo "\n— The six movements, everywhere —\n";
/*
 * "Strategy · Identity · Production · Presence" outlived three separate
 * corrections: in the footer nav, in a meta description, and last of all in
 * the studio-facts marquee, which scrolls across several routes.
 */
foreach ( $html as $name => $body ) {
	ok( false === stripos( $body, 'Production &middot; Presence' ) && false === stripos( $body, 'Production · Presence' ),
		"$name: the old four-movement string is gone" );
}
foreach ( array( 'Strategy', 'Identity', 'Image', 'Experience', 'Digital', 'Visibility' ) as $movement ) {
	ok( false !== strpos( $html['home'], $movement ), "home: the footer names $movement" );
}

echo "\n— Positioning —\n";
/*
 * Website development and digital promotion are genuine capabilities and MUST
 * appear. What must not appear is the studio describing itself as a web or
 * advertising agency. The brand name contains "Development Studio", so it is
 * removed before matching.
 */
$banned = array( 'web agency', 'web design agency', 'web development agency', 'digital agency', 'advertising agency', 'development studio' );
foreach ( $html as $name => $body ) {
	$text = str_ireplace( 'AK Brand Development Studio', 'AK', wp_strip_all_tags( $body ) );
	$hits = array();
	foreach ( $banned as $phrase ) {
		if ( false !== stripos( $text, $phrase ) ) {
			$hits[] = $phrase;
		}
	}
	ok( ! $hits, "$name: no web/ad-agency self-description" . ( $hits ? ' — found ' . implode( ', ', $hits ) : '' ) );
}
// And the genuine capabilities are still stated.
ok( false !== stripos( $html['services'], 'Website development' ), 'services: website development is still offered' );
ok( false !== stripos( $html['services'], 'Digital advertising campaigns' ), 'services: digital advertising is still offered' );
ok( false !== stripos( $html['services'], 'Fashion show production' ), 'services: fashion show production is stated' );
ok( false !== stripos( $html['services'], 'PR support' ), 'services: PR is stated separately from advertising' );

echo "\n— Services matches the source document —\n";
$expected = array(
	'Brand Strategy &amp; Development', 'Personal Brand Development',
	'Brand Identity &amp; Creative Direction', 'Photo &amp; Video Production',
	'Events &amp; Fashion Production', 'Digital Presence',
	'Marketing &amp; Communication', 'Digital Promotion',
);
foreach ( $expected as $area ) {
	ok( false !== strpos( $html['services'], $area ), 'services: ' . html_entity_decode( $area ) . ' is named' );
}
ok( 46 === ak_service_count(), 'the service list matches the source document (' . ak_service_count() . ')' );
foreach ( array( 'Editorial photography', 'Fashion film', 'Event curation' ) as $invented ) {
	ok( false === stripos( $html['services'], $invented ), "services: '$invented' was not in the source and is not claimed" );
}

echo "\n— Routes —\n";
ok( false !== strpos( $routes['journal'], '/journal/' ), 'journal lives at /journal/' );
$post = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 1 ) );
if ( $post ) {
	ok( false !== strpos( get_permalink( $post[0] ), '/journal/' ), 'journal posts live under /journal/ too' );
}
ok( false !== strpos( $html['search'], 'aks-' ), 'search results use the AK system, not the parent theme' );

echo "\n$pass passed, $fail failed\n";
exit( $fail ? 1 : 0 );
