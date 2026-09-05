<?php
/**
 * The canonical manifest — what this release expects to exist.
 *
 * This file is the single description of managed site state. The deployment
 * engine reconciles the database against it: anything here that is missing is
 * created, anything here that exists is updated in place, and anything the
 * build previously created that is NO LONGER here is deleted.
 *
 * Every entry has a `key`. That key is the object's identity for the lifetime
 * of the site — it is stored as `_ak_seed_key`, it is never displayed, and it
 * must never be reused for a different object. Renaming a page changes its
 * title, not its key. Changing a key means "delete the old object and create a
 * new one", so do that deliberately or not at all.
 *
 * Nothing else in the theme may define content. If a page, a menu item or a
 * case study is described anywhere but here, two sources of truth exist and
 * they will drift.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The manifest.
 *
 * @return array{
 *   version:string,
 *   posts:array<int,array>,
 *   menus:array<int,array>,
 *   terms:array<int,array>,
 *   options:array<string,mixed>
 * }
 */
function ak_manifest() {
	$manifest = array(
		'version' => AK_CHILD_VERSION,
		'posts'   => array_merge( ak_manifest_pages(), ak_manifest_journal(), ak_manifest_projects() ),
		'menus'   => ak_manifest_menus(),
		'terms'   => ak_manifest_terms(),
	);

	/**
	 * Filter the canonical manifest before it is deployed.
	 *
	 * @param array $manifest The manifest.
	 */
	return apply_filters( 'ak_manifest', $manifest );
}

/**
 * Pages.
 *
 * There is deliberately no `work` PAGE. `/work/` belongs to the portfolio
 * archive, and a page sharing that slug wins the route — pages outrank
 * post-type archives — which hid the archive, 404'd every case study and sent
 * the old redirect into an infinite loop. The menu links to the archive.
 *
 * @return array[]
 */
function ak_manifest_pages() {
	return array(
		array(
			'key'   => 'ak_home',
			'type'  => 'page',
			'slug'  => 'home',
			'title' => __( 'Home', 'ak-zeyna-child' ),
			'order' => 1,
			'role'  => 'front_page',
		),
		array(
			'key'      => 'ak_services',
			'type'     => 'page',
			'slug'     => 'services',
			'title'    => __( 'Services', 'ak-zeyna-child' ),
			'order'    => 3,
			'template' => 'page-templates/template-services.php',
		),
		array(
			'key'   => 'ak_journal',
			'type'  => 'page',
			'slug'  => 'journal',
			'title' => __( 'Journal', 'ak-zeyna-child' ),
			'order' => 4,
			'role'  => 'posts_page',
		),
		array(
			'key'      => 'ak_about',
			'type'     => 'page',
			'slug'     => 'about',
			'title'    => __( 'About', 'ak-zeyna-child' ),
			'order'    => 5,
			'template' => 'page-templates/template-about.php',
		),
		array(
			'key'      => 'ak_contact',
			'type'     => 'page',
			'slug'     => 'contact',
			'title'    => __( 'Contact', 'ak-zeyna-child' ),
			'order'    => 6,
			'template' => 'page-templates/template-contact.php',
		),
		array(
			'key'     => 'ak_privacy',
			'type'    => 'page',
			'slug'    => 'privacy',
			'title'   => __( 'Privacy', 'ak-zeyna-child' ),
			'order'   => 7,
			'content' => ak_manifest_file( 'privacy.html' ),
			// WordPress creates its own privacy page, and the legacy purge
			// deliberately spares it. Without adoption the deployment would
			// then add a SECOND privacy page beside it. Claim the existing one
			// instead — it already holds the ID the Settings → Privacy screen
			// points at.
			'adopt'   => array( 'privacy-policy' ),
			'role'    => 'privacy_page',
		),
	);
}

/**
 * Journal articles. Bodies live in content/journal/*.html.
 *
 * @return array[]
 */
function ak_manifest_journal() {
	$posts = array(
		array(
			'key'     => 'ak_post_fashion_week_cost',
			'slug'    => 'what-it-costs-to-show-at-a-fashion-week',
			'title'   => 'What it actually costs to show at a fashion week',
			'cat'     => 'Production',
			'excerpt' => 'The honest breakdown a young designer cannot find anywhere else — what a runway slot really costs, where the money actually goes, and what a show is genuinely worth.',
		),
		array(
			'key'     => 'ak_post_ad_size_first',
			'slug'    => 'design-the-identity-at-ad-size-first',
			'title'   => 'Design the identity at ad size first',
			'cat'     => 'Identity',
			'excerpt' => 'A logo that only works at billboard scale is a logo that will be cropped, shrunk and ignored by the algorithm that decides whether anyone sees it.',
		),
		array(
			'key'     => 'ak_post_personal_brand',
			'slug'    => 'your-company-is-not-your-personal-brand',
			'title'   => 'Your company is not your personal brand',
			'cat'     => 'Strategy',
			'excerpt' => 'Founders in fashion are usually better known than their labels — or invisible behind them. Both are strategy problems, and both are fixable.',
		),
		array(
			'key'     => 'ak_post_fix_the_website',
			'slug'    => 'fix-the-website-before-you-buy-the-traffic',
			'title'   => 'Fix the website before you buy the traffic',
			'cat'     => 'Presence',
			'excerpt' => 'Every pound of promotion lands on a page. If that page is slow or unclear, promotion just buys a faster exit.',
		),
	);

	$out = array();
	foreach ( $posts as $p ) {
		$body  = ak_manifest_file( 'journal/' . $p['slug'] . '.html' );
		$out[] = array(
			'key'     => $p['key'],
			'type'    => 'post',
			'slug'    => $p['slug'],
			'title'   => $p['title'],
			'excerpt' => $p['excerpt'],
			'content' => $body ? $body : '<p>' . esc_html( $p['excerpt'] ) . '</p>',
			'terms'   => array( 'category' => $p['cat'] ),
		);
	}
	return $out;
}

/**
 * Projects.
 *
 * DELIBERATELY EMPTY at this release. The six entries that used to live here
 * were placeholders — six posts titled "Client Name", each carrying invented
 * figures (PRESS 38, PRESS 52, CVR +2.4pp, LCP 0.9s). Fabricated results on a
 * live agency site are worse than an empty section, so they are removed, and
 * the deployment engine deletes them from any install that received them.
 *
 * Real projects land here once their register (founded / client) and the
 * disciplines AK actually delivered are confirmed. See
 * docs/AK-BRAND-CONTENT-COMPLETENESS.md.
 *
 * @return array[]
 */
function ak_manifest_projects() {
	/**
	 * Filter the project entries.
	 *
	 * @param array[] $projects Project manifest entries.
	 */
	return apply_filters( 'ak_manifest_projects', array() );
}

/**
 * The primary menu.
 *
 * Items are identified by seed key like everything else, so re-ordering them
 * in wp-admin does not make the next deployment think they are missing.
 *
 * @return array[]
 */
function ak_manifest_menus() {
	return array(
		array(
			'key'      => 'ak_menu_primary',
			'name'     => 'Primary',
			'location' => 'menu-1',
			'items'    => array(
				array(
					'key'   => 'ak_nav_home',
					'type'  => 'post_type',
					'seed'  => 'ak_home',
					'label' => __( 'Home', 'ak-zeyna-child' ),
				),
				array(
					'key'      => 'ak_nav_work',
					'type'     => 'archive',
					'archive'  => 'portfolio',
					'label'    => __( 'Work', 'ak-zeyna-child' ),
				),
				array(
					'key'   => 'ak_nav_services',
					'type'  => 'post_type',
					'seed'  => 'ak_services',
					'label' => __( 'Services', 'ak-zeyna-child' ),
				),
				array(
					'key'   => 'ak_nav_journal',
					'type'  => 'post_type',
					'seed'  => 'ak_journal',
					'label' => __( 'Journal', 'ak-zeyna-child' ),
				),
				array(
					'key'   => 'ak_nav_about',
					'type'  => 'post_type',
					'seed'  => 'ak_about',
					'label' => __( 'About', 'ak-zeyna-child' ),
				),
				array(
					'key'   => 'ak_nav_contact',
					'type'  => 'post_type',
					'seed'  => 'ak_contact',
					'label' => __( 'Contact', 'ak-zeyna-child' ),
				),
			),
		),
	);
}

/**
 * Taxonomy terms the build owns.
 *
 * @return array[]
 */
function ak_manifest_terms() {
	$out = array();
	foreach ( array( 'Strategy', 'Identity', 'Production', 'Presence' ) as $name ) {
		$out[] = array(
			'key'      => 'ak_cat_' . strtolower( $name ),
			'taxonomy' => 'category',
			'name'     => $name,
		);
	}
	return $out;
}

/**
 * Read a content file shipped with the theme.
 *
 * @param string $relative Path under content/.
 * @return string Empty string when the file is missing or unreadable.
 */
function ak_manifest_file( $relative ) {
	$base = realpath( get_stylesheet_directory() . '/content' );
	$path = realpath( get_stylesheet_directory() . '/content/' . $relative );

	// Never read outside the theme's own content directory, whatever the
	// manifest asks for.
	if ( ! $base || ! $path || 0 !== strpos( $path, $base ) || ! is_readable( $path ) ) {
		return '';
	}
	return (string) file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
}
