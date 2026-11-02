<?php
/**
 * Theme supports, menus, editor setup and a little housekeeping.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		load_theme_textdomain( 'ak-studio', get_theme_file_path( 'languages' ) );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

		// The page editor shows pages with the site's own styles.
		add_theme_support( 'editor-styles' );
		add_editor_style( array( 'assets/css/main.css', 'assets/css/editor.css' ) );

		// Only the studio's colours in the editor's colour pickers.
		add_theme_support(
			'editor-color-palette',
			array(
				array( 'name' => __( 'Bone', 'ak-studio' ), 'slug' => 'ink', 'color' => '#f1ece2' ),
				array( 'name' => __( 'Stone', 'ak-studio' ), 'slug' => 'ink-dim', 'color' => '#b0a99d' ),
				array( 'name' => __( 'AK orange', 'ak-studio' ), 'slug' => 'accent', 'color' => '#f37021' ),
				array( 'name' => __( 'Stage', 'ak-studio' ), 'slug' => 'stage', 'color' => '#0d0c0b' ),
				array( 'name' => __( 'Stage raised', 'ak-studio' ), 'slug' => 'stage-raised', 'color' => '#161412' ),
			)
		);
		add_theme_support( 'disable-custom-gradients' );
		add_theme_support( 'editor-gradient-presets', array() );

		// WordPress's own sample patterns don't fit the design; the theme ships its own.
		remove_theme_support( 'core-block-patterns' );

		register_nav_menus(
			array(
				'primary' => __( 'Main menu', 'ak-studio' ),
				'cta'     => __( 'Header button', 'ak-studio' ),
			)
		);
	}
);

/*
 * WordPress gives classic themes a narrow 840px editing column. This theme
 * lays out the editor exactly like the site (assets/css/main.css), so that
 * column is switched off — the handle stays registered, empty, because other
 * editor styles depend on it.
 */
add_action(
	'wp_default_styles',
	static function ( WP_Styles $styles ) {
		if ( isset( $styles->registered['wp-editor-classic-layout-styles'] ) ) {
			$styles->remove( 'wp-editor-classic-layout-styles' );
			$styles->add( 'wp-editor-classic-layout-styles', false );
		}
	},
	20
);

/* The sign-in screen is light: it carries the logo with dark text. */
add_action(
	'login_enqueue_scripts',
	static function () {
		printf(
			'<style>#login h1 a{background:url(%s) center/contain no-repeat;width:220px;height:118px;}</style>',
			esc_url( get_theme_file_uri( 'assets/img/ak-logo-dark.svg' ) )
		);
	}
);
add_filter( 'login_headerurl', static fn() => home_url( '/' ) );
add_filter( 'login_headertext', static fn() => ak_studio_get( 'name' ) );

/* Patterns from the theme's patterns/ folder appear under this category. */
add_action(
	'init',
	static function () {
		register_block_pattern_category( 'ak-studio', array( 'label' => __( 'AK Studio', 'ak-studio' ) ) );
	}
);

/* On activation, pick up the menus the AK installer created. */
add_action(
	'after_switch_theme',
	static function () {
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$locations = is_array( $locations ) ? $locations : array();
		foreach ( array( 'primary' => 'AK Primary', 'cta' => 'AK Call to action' ) as $loc => $name ) {
			$menu = wp_get_nav_menu_object( $name );
			if ( empty( $locations[ $loc ] ) && $menu ) {
				$locations[ $loc ] = (int) $menu->term_id;
			}
		}
		set_theme_mod( 'nav_menu_locations', $locations );
	}
);

/**
 * Until a menu is assigned, the bar links to the main pages that exist.
 */
function ak_studio_nav_fallback(): void {
	$items = array();
	foreach ( array( 'work' => __( 'Work', 'ak-studio' ), 'services' => __( 'Services', 'ak-studio' ), 'show-in-london' => __( 'Show in London', 'ak-studio' ), 'about' => __( 'Studio', 'ak-studio' ), 'journal' => __( 'Journal', 'ak-studio' ), 'contact' => __( 'Contact', 'ak-studio' ) ) as $path => $label ) {
		$page = get_page_by_path( $path );
		if ( $page && 'publish' === $page->post_status ) {
			$items[ get_permalink( $page ) ] = $label;
		}
	}
	echo '<ul class="nav__list">';
	foreach ( $items as $url => $label ) {
		$current = untrailingslashit( $url ) === untrailingslashit( (string) get_permalink() ) && is_page();
		printf( '<li class="menu-item%s"><a href="%s"%s>%s</a></li>', $current ? ' current-menu-item' : '', esc_url( $url ), $current ? ' aria-current="page"' : '', esc_html( $label ) );
	}
	echo '</ul>';
}

/* Mark the current section in the menu: projects belong to Work, entries to the Journal. */
add_filter(
	'nav_menu_css_class',
	static function ( $classes, $item ) {
		$url  = untrailingslashit( (string) $item->url );
		$work = get_page_by_path( 'work' );
		if ( ( is_singular( 'ak_project' ) || is_tax( array( 'ak_project_type', 'ak_capability', 'ak_relationship' ) ) ) && $work && untrailingslashit( (string) get_permalink( $work ) ) === $url ) {
			$classes[] = 'current-menu-item';
		}
		$journal = (int) get_option( 'page_for_posts' );
		if ( $journal && (int) $item->object_id === $journal ) {
			$in_journal = is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_date() || is_author();
			$classes    = $in_journal ? array_merge( $classes, array( 'current-menu-item' ) ) : array_diff( $classes, array( 'current_page_parent', 'current-menu-parent' ) );
		}
		return $classes;
	},
	10,
	2
);

add_filter( 'excerpt_length', static fn() => 28 );
add_filter( 'excerpt_more', static fn() => '…' );

/* No emoji polyfill: the site never uses it. */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

/* Project groupings list everything, in the studio's order. */
add_action(
	'pre_get_posts',
	static function ( $q ) {
		if ( ! is_admin() && $q->is_main_query() && $q->is_tax( array( 'ak_project_type', 'ak_capability', 'ak_relationship' ) ) ) {
			$q->set( 'posts_per_page', 60 );
			$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
		}
	}
);
