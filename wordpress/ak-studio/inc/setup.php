<?php
/**
 * Theme supports, menus, template routing and a little housekeeping.
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
		add_theme_support( 'editor-styles' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_editor_style( 'assets/css/editor.css' );
		register_nav_menus(
			array(
				'primary'  => __( 'Primary', 'ak-studio' ),
				'services' => __( 'Footer — services', 'ak-studio' ),
				'footer'   => __( 'Footer — legal', 'ak-studio' ),
			)
		);
	}
);

/* On activation, pick up the menus the AK installer created. */
add_action(
	'after_switch_theme',
	static function () {
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$locations = is_array( $locations ) ? $locations : array();
		foreach ( array( 'primary' => 'AK Primary', 'services' => 'AK Services', 'footer' => 'AK Footer' ) as $loc => $name ) {
			$menu = wp_get_nav_menu_object( $name );
			if ( empty( $locations[ $loc ] ) && $menu ) {
				$locations[ $loc ] = (int) $menu->term_id;
			}
		}
		set_theme_mod( 'nav_menu_locations', $locations );
	}
);

/**
 * Service pages (any page with a discipline set) use page-service.php.
 *
 * @param string[] $templates Candidate templates.
 * @return string[]
 */
add_filter(
	'page_template_hierarchy',
	static function ( $templates ) {
		$id = get_queried_object_id();
		if ( $id && get_post_meta( $id, '_ak_movement', true ) ) {
			array_unshift( $templates, 'page-service.php' );
		}
		return $templates;
	}
);

/**
 * Until a menu is assigned, the bar links to the main pages that exist.
 */
function ak_studio_nav_fallback(): void {
	$items = array(
		home_url( '/work/' ) => __( 'Work', 'ak-studio' ),
	);
	foreach ( array( 'services' => __( 'Services', 'ak-studio' ), 'show-in-london' => __( 'Show in London', 'ak-studio' ), 'about' => __( 'Studio', 'ak-studio' ), 'journal' => __( 'Journal', 'ak-studio' ), 'contact' => __( 'Contact', 'ak-studio' ) ) as $path => $label ) {
		$page = get_page_by_path( $path );
		if ( $page ) {
			$items[ get_permalink( $page ) ] = $label;
		}
	}
	echo '<ul class="nav__list">';
	foreach ( $items as $url => $label ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/* Mark the current section in the menu for archives and singles. */
add_filter(
	'nav_menu_css_class',
	static function ( $classes, $item ) {
		$url = untrailingslashit( (string) $item->url );
		if ( ( is_singular( 'ak_project' ) || is_post_type_archive( 'ak_project' ) ) && untrailingslashit( home_url( '/work' ) ) === $url ) {
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

/* Body classes the stylesheet keys on. */
add_filter(
	'body_class',
	static function ( $classes ) {
		if ( is_page() && get_post_meta( get_queried_object_id(), '_ak_movement', true ) ) {
			$classes[] = 'is-service';
		}
		return $classes;
	}
);

/* No emoji polyfill: the site never uses it. */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/* A comfortable number of projects on /work/: all of them. */
add_action(
	'pre_get_posts',
	static function ( $q ) {
		if ( ! is_admin() && $q->is_main_query() && $q->is_post_type_archive( 'ak_project' ) ) {
			$q->set( 'posts_per_page', 60 );
			$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
		}
	}
);
