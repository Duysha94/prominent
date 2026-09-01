<?php
/**
 * Render the child theme's templates OUTSIDE WordPress.
 *
 * Defines just enough of the WP surface for the templates to run, with fixture
 * data shaped like the import file, and emits one static HTML page per
 * template. The point is to look at the real PHP output in a real browser
 * before anyone installs anything — a template that lints clean can still
 * render nonsense.
 *
 * Usage: php scripts/stub-render.php <template> > out.html
 */

define( 'ABSPATH', '/' );
define( 'AK_STUB', true );

error_reporting( E_ALL & ~E_DEPRECATED );

$THEME = __DIR__ . '/../wordpress/ak-zeyna-child';

/* ── Escaping / i18n ─────────────────────────────────────────────────── */
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $s ) { return esc_html( $s ); }
function esc_url( $s ) { return esc_html( $s ); }
function esc_url_raw( $s ) { return $s; }
function __( $s, $d = null ) { return $s; }
function _e( $s, $d = null ) { echo $s; }
function esc_html__( $s, $d = null ) { return esc_html( $s ); }
function esc_html_e( $s, $d = null ) { echo esc_html( $s ); }
function esc_attr__( $s, $d = null ) { return esc_attr( $s ); }
function esc_attr_e( $s, $d = null ) { echo esc_attr( $s ); }

/* ── URLs / options ──────────────────────────────────────────────────── */
function home_url( $p = '' ) { return 'http://127.0.0.1:4181' . $p; }
function get_theme_mod( $k, $default = '' ) { return $default; }
function get_post_type_archive_link( $t ) { return home_url( '/work/' ); }
function wp_unique_id( $p = '' ) { static $n = 0; return $p . ( ++$n ); }
function post_type_exists( $t ) { return 'wpcf7_contact_form' === $t; }
function taxonomy_exists( $t ) { return false; }

/* ── Fixture content ─────────────────────────────────────────────────── */
class AK_Stub_Post {
	public $ID; public $post_title; public $post_name; public $meta; public $excerpt; public $content;
	public function __construct( $id, $title, $name, $meta = array(), $excerpt = '', $content = '' ) {
		$this->ID = $id; $this->post_title = $title; $this->post_name = $name;
		$this->meta = $meta; $this->excerpt = $excerpt; $this->content = $content;
	}
}

$GLOBALS['ak_fixture_cases'] = array(
	new AK_Stub_Post( 2001, 'Client Name', 'placeholder-collection-launch', array(
		'ak_headline' => 'A first collection, shown in London.',
		'ak_category' => 'Collection launch', 'ak_year' => '2026',
		'ak_movements' => 'Strategy, Identity, Production',
		'ak_summary' => 'Placeholder. A designer with a finished collection and no route to an audience. Position, identity and a runway slot inside one season.',
		'ak_measures' => '[{"key":"LOOKS","value":"24"},{"key":"PRESS","value":"38"},{"key":"RUNWAY","value":"LDN"}]',
		'ak_chapters' => '[{"title":"The position","body":"Placeholder chapter about the position.","mkey":"DECK","mval":"1 page"},{"title":"The show","body":"Placeholder chapter about the production.","mkey":"LOOKS","mval":"24"},{"title":"The coverage","body":"Placeholder chapter about the press.","mkey":"PRESS","mval":"38 titles"}]',
		'ak_position' => '{"statement":"Placeholder positioning line for this brand.","rejected":["Safe version","Category version","Trend version"]}',
	), 'Placeholder summary.' ),
	new AK_Stub_Post( 2002, 'Client Name', 'placeholder-brand-identity', array(
		'ak_headline' => 'An identity built to survive the feed.',
		'ak_category' => 'Brand identity', 'ak_year' => '2026',
		'ak_movements' => 'Strategy, Identity',
		'ak_summary' => 'Placeholder identity case.',
		'ak_measures' => '[{"key":"MARKS","value":"3"},{"key":"SCALE","value":"4:5"}]',
		'ak_chapters' => '[{"title":"Drawn at ad size","body":"Placeholder.","mkey":"CROP","mval":"4:5"}]',
	) ),
	new AK_Stub_Post( 2003, 'Client Name', 'placeholder-personal-brand', array(
		'ak_headline' => 'A founder who stopped hiding behind the brand.',
		'ak_category' => 'Personal brand', 'ak_year' => '2025',
		'ak_movements' => 'Strategy, Presence', 'ak_summary' => 'Placeholder.',
		'ak_measures' => '[]', 'ak_chapters' => '[]',
	) ),
	new AK_Stub_Post( 2004, 'Client Name', 'placeholder-website', array(
		'ak_headline' => 'A store that finally looked like the clothes.',
		'ak_category' => 'E-commerce', 'ak_year' => '2025',
		'ak_movements' => 'Identity, Presence', 'ak_summary' => 'Placeholder.',
		'ak_measures' => '[]', 'ak_chapters' => '[]',
	) ),
);

$GLOBALS['ak_fixture_posts'] = array(
	new AK_Stub_Post( 3001, 'What it actually costs to show at a fashion week', 'costs', array(), 'Placeholder standfirst about the honest cost breakdown a young designer cannot find anywhere else.', '<p>Placeholder body paragraph one.</p><h2>A subheading</h2><p>Placeholder body paragraph two with a bit more length to see rhythm and measure at real reading width.</p><blockquote>The pull quote sits in the display face.</blockquote><p>Closing paragraph.</p>' ),
	new AK_Stub_Post( 3002, 'Design the identity at ad size first', 'ad-size', array(), 'Placeholder standfirst about ad-size-first identity design.' ),
	new AK_Stub_Post( 3003, 'Your company is not your personal brand', 'personal', array(), 'Placeholder standfirst about founders and labels.' ),
	new AK_Stub_Post( 3004, 'Fix the website before you buy the traffic', 'traffic', array(), 'Placeholder standfirst about promotion landing on pages.' ),
);

$GLOBALS['ak_current'] = null;
$GLOBALS['ak_loop'] = array( 'items' => array(), 'i' => -1 );

function ak_stub_set_loop( $items ) { $GLOBALS['ak_loop'] = array( 'items' => $items, 'i' => -1 ); }
function have_posts() { $l = &$GLOBALS['ak_loop']; return $l['i'] + 1 < count( $l['items'] ); }
function the_post() { $l = &$GLOBALS['ak_loop']; $l['i']++; $GLOBALS['ak_current'] = $l['items'][ $l['i'] ]; }
function get_the_ID() { return $GLOBALS['ak_current'] ? $GLOBALS['ak_current']->ID : 0; }
function get_the_title( $p = 0 ) { return $GLOBALS['ak_current'] ? $GLOBALS['ak_current']->post_title : 'Untitled'; }
function the_title() { echo esc_html( get_the_title() ); }
function get_permalink( $p = null ) {
	if ( is_object( $p ) ) { return home_url( '/' . $p->post_name . '/' ); }
	return $GLOBALS['ak_current'] ? home_url( '/' . $GLOBALS['ak_current']->post_name . '/' ) : home_url( '/' );
}
function the_permalink() { echo esc_url( get_permalink() ); }
function get_post_meta( $id, $key, $single = false ) {
	foreach ( array_merge( $GLOBALS['ak_fixture_cases'], $GLOBALS['ak_fixture_posts'] ) as $p ) {
		if ( $p->ID === $id ) { return isset( $p->meta[ $key ] ) ? $p->meta[ $key ] : ''; }
	}
	return '';
}
function get_the_excerpt() { return $GLOBALS['ak_current'] ? $GLOBALS['ak_current']->excerpt : ''; }
function get_the_content() { return $GLOBALS['ak_current'] ? $GLOBALS['ak_current']->content : ''; }
function the_content() { echo $GLOBALS['ak_current'] ? $GLOBALS['ak_current']->content : ''; }
function wp_reset_postdata() {}
function post_class() { echo 'class="ak-case"'; }
function has_post_thumbnail() { return false; }
function the_post_thumbnail() {}
function get_the_date( $f = 'j F Y' ) { return 'c' === $f ? '2026-08-01T12:00:00+00:00' : 'Aug 2026'; }
function get_the_category() { $c = new stdClass(); $c->name = 'Production'; return array( $c ); }
function the_posts_pagination() {}
function previous_post_link( $f = '', $l = '' ) {}
function next_post_link( $f = '', $l = '' ) {}
function get_next_post() { return isset( $GLOBALS['ak_fixture_cases'][1] ) ? $GLOBALS['ak_fixture_cases'][1] : null; }
function get_previous_post() { return null; }
function get_page_by_path( $path ) { return new AK_Stub_Post( 100, ucfirst( $path ), $path ); }
function get_posts( $args ) {
	if ( isset( $args['post_type'] ) && 'wpcf7_contact_form' === $args['post_type'] ) {
		return array( new AK_Stub_Post( 4001, 'AK — Project brief', 'ak-project-brief' ) );
	}
	return array();
}

class WP_Query {
	public $items;
	private $i = -1;
	public function __construct( $args ) {
		$this->items = ( isset( $args['post_type'] ) && 'post' === $args['post_type'] )
			? $GLOBALS['ak_fixture_posts'] : $GLOBALS['ak_fixture_cases'];
		if ( isset( $args['posts_per_page'] ) && $args['posts_per_page'] > 0 ) {
			$this->items = array_slice( $this->items, 0, $args['posts_per_page'] );
		}
	}
	public function have_posts() { return $this->i + 1 < count( $this->items ); }
	public function the_post() { $this->i++; $GLOBALS['ak_current'] = $this->items[ $this->i ]; }
}

/* ── CF7: emit markup shaped like the plugin's real output, generated from
       the same field set as the import file, so the form CSS is exercised. */
function do_shortcode( $sc ) {
	return <<<HTML
<div class="wpcf7">
<form class="wpcf7-form init" novalidate>
<div class="ak-grid-2">
<label class="ak-field"><span class="ak-field__label">Your name *</span>
<span class="wpcf7-form-control-wrap"><input type="text" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" autocomplete="name"></span></label>
<label class="ak-field"><span class="ak-field__label">Email *</span>
<span class="wpcf7-form-control-wrap"><input type="email" class="wpcf7-form-control wpcf7-email" autocomplete="email"></span></label>
</div>
<div class="ak-grid-2">
<label class="ak-field"><span class="ak-field__label">Brand or company</span>
<span class="wpcf7-form-control-wrap"><input type="text" class="wpcf7-form-control wpcf7-text"></span></label>
<label class="ak-field"><span class="ak-field__label">Website or Instagram</span>
<span class="wpcf7-form-control-wrap"><input type="text" class="wpcf7-form-control wpcf7-text"></span></label>
</div>
<label class="ak-field"><span class="ak-field__label">Where is the brand now? *</span>
<span class="wpcf7-form-control-wrap"><select class="wpcf7-form-control wpcf7-select"><option>Just an idea</option><option>Launching — first collection or first product</option><option>Trading — selling, but growth is flat</option><option>Scaling — growing, and the seams are showing</option></select></span></label>
<label class="ak-field"><span class="ak-field__label">What do you think you need? *</span>
<span class="wpcf7-form-control-wrap"><select class="wpcf7-form-control wpcf7-select"><option>Brand strategy and positioning</option><option>Not sure — tell me what I need</option></select></span></label>
<label class="ak-field"><span class="ak-field__label">Tell us about the project *</span>
<span class="wpcf7-form-control-wrap"><textarea class="wpcf7-form-control wpcf7-textarea"></textarea></span>
<span class="wpcf7-not-valid-tip">This field is required.</span></label>
<p><input type="submit" value="Send the brief" class="wpcf7-form-control wpcf7-submit"></p>
<div class="wpcf7-response-output">A field or two needs attention below.</div>
</form>
</div>
HTML;
}

/* ── Theme shell ─────────────────────────────────────────────────────── */
function zeyna_barba( $body ) { return $body ? 'data-barba="wrapper"' : 'data-barba="container"'; }
function get_header() {
	echo <<<HTML
<!doctype html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>AK stub render</title>
<script>(function(){try{var t=localStorage.getItem('ak-theme');if(!t){t=matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-theme',t);}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
<link rel="stylesheet" href="theme/assets/css/ak.css">
</head><body class="ak" data-barba="wrapper">
<header style="padding:1rem 2rem;display:flex;gap:2rem;align-items:center;border-bottom:1px solid var(--rule)">
<span style="font-family:var(--font-display);font-style:italic;font-size:1.25rem">AK</span>
<nav style="margin-left:auto;display:flex;gap:1rem;align-items:center;font-size:.8125rem;flex-wrap:wrap;justify-content:flex-end">
<a href="index-front-page.html" style="color:inherit;text-decoration:none">Home</a>
<a href="index-archive-portfolio.html" style="color:inherit;text-decoration:none">Work</a>
<a href="index-template-services.html" style="color:inherit;text-decoration:none">Services</a>
<a href="index-template-contact.html" style="color:inherit;text-decoration:none">Contact</a>
<button type="button" class="ak-mode" data-ak-mode aria-pressed="false"><span class="ak-mode__label">Atelier</span><span class="ak-mode__track" aria-hidden="true"><span class="ak-mode__knob"></span></span></button>
</nav>
</header>
HTML;
}
function get_footer() {
	$grain = file_get_contents( $GLOBALS['THEME'] . '/template-parts/ak-grain.php' );
	$grain = preg_replace( '/<\?php.*?\?>/s', '', $grain );
	$seam = file_get_contents( $GLOBALS['THEME'] . '/template-parts/ak-seam.php' );
	$seam = preg_replace( '/<\?php.*?\?>/s', '', $seam );
	echo '<footer style="border-top:1px solid var(--rule);padding:3rem 2rem;background:var(--bg-sunk)"><p style="font-family:var(--font-mono);font-size:.5625rem;letter-spacing:.18em;text-transform:uppercase;color:var(--text-faint)">Zeyna footer placeholder — outside the container</p></footer>';
	echo $grain;
	echo $seam;
	echo '<script src="zeyna-js/gsap.min.js"></script><script src="zeyna-js/gsap-plugins.min.js"></script><script src="theme/assets/js/ak.js"></script></body></html>';
}
function get_template_part( $slug ) {
	include $GLOBALS['THEME'] . '/' . $slug . '.php';
}

/* ── Load helpers + run the requested template ───────────────────────── */
function wp_kses( $s, $allowed ) { return $s; }
function ak_studio_email() { return 'hello@akbrand.studio'; }

require $THEME . '/inc/case-meta.php';

$template = isset( $argv[1] ) ? $argv[1] : 'front-page';
$map = array(
	'front-page'         => '/front-page.php',
	'archive-portfolio'  => '/archive-portfolio.php',
	'single-portfolio'   => '/single-portfolio.php',
	'template-services'  => '/page-templates/template-services.php',
	'template-about'     => '/page-templates/template-about.php',
	'template-contact'   => '/page-templates/template-contact.php',
	'home'               => '/home.php',
	'single'             => '/single.php',
);
if ( ! isset( $map[ $template ] ) ) {
	fwrite( STDERR, "unknown template\n" );
	exit( 1 );
}

// Seed the main loop for templates that use it directly.
if ( in_array( $template, array( 'archive-portfolio', 'home' ), true ) ) {
	ak_stub_set_loop( 'home' === $template ? $GLOBALS['ak_fixture_posts'] : $GLOBALS['ak_fixture_cases'] );
} elseif ( 'single-portfolio' === $template ) {
	ak_stub_set_loop( array( $GLOBALS['ak_fixture_cases'][0] ) );
} elseif ( 'single' === $template ) {
	ak_stub_set_loop( array( $GLOBALS['ak_fixture_posts'][0] ) );
} else {
	ak_stub_set_loop( array( new AK_Stub_Post( 100, ucfirst( $template ), $template ) ) );
}

include $THEME . $map[ $template ];
