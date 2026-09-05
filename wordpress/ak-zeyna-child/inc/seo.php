<?php
/**
 * Meta descriptions.
 *
 * Neither Zeyna nor the child outputs one anywhere, so search snippets would
 * be scraped from whatever text a crawler finds first. Titles are already
 * handled by core (`title-tag` support in the parent); this file only adds
 * the description, and steps aside entirely when a dedicated SEO plugin is
 * detected so nothing is emitted twice.
 *
 * A page's own excerpt always wins — write one in the editor to override the
 * defaults below without touching code.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_head',
	function () {
		if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
			return;
		}

		/*
		 * These describe the practice, not one corner of it. An earlier set
		 * named four movements and nine services, and read — accurately, at
		 * the time — as a studio that builds websites and buys ads. The
		 * practice covers brand and personal brand strategy, identity and
		 * creative direction, photo and film production, events and fashion
		 * production, digital presence, and communication, PR and paid media.
		 * A description that names only the last two misstates the studio in
		 * exactly the place search results quote it.
		 */
		$descriptions = array(
			'front'    => 'AK Brand Development Studio is a fashion and brand advisory in London working across the whole development cycle — brand and personal brand strategy, identity and creative direction, photo and film production, fashion shows and events, digital presence, and communication, PR and paid media.',
			'journal'  => 'Notes from the studio floor — short, practical writing on positioning, identity, image, production and visibility in fashion.',
			'work'     => 'Selected work by AK Brand Development Studio: fashion platforms, media and editorial titles, brands and commissioned engagements, founded or produced by the studio.',
			'services' => 'Six movements — Strategy, Identity, Image, Experience, Digital and Visibility — covering the studio\'s full practice, from brand positioning and creative direction to photo campaigns, fashion film, fashion show production, websites, PR and paid media.',
			'about'    => 'AK Brand Development Studio was founded by Andrii Karakushan and Kostiantyn Lieontiev, the producers behind London Fashion Day and Odessa Fashion Day. A fashion and brand advisory based in London.',
			'contact'  => 'Tell us where the brand is. AK Brand Development Studio, London, United Kingdom — fashion and brand advisory across strategy, identity, image, experience, digital and visibility.',
		);

		$desc = '';

		if ( is_front_page() ) {
			$desc = $descriptions['front'];
		} elseif ( is_home() ) {
			$desc = $descriptions['journal'];
		} elseif ( is_post_type_archive( AK_PROJECT_CPT ) ) {
			$desc = $descriptions['work'];
		} elseif ( is_singular() ) {
			$id = get_queried_object_id();
			if ( has_excerpt( $id ) ) {
				$desc = wp_strip_all_tags( get_the_excerpt( $id ) );
			} else {
				$slug = get_post_field( 'post_name', $id );
				if ( isset( $descriptions[ $slug ] ) ) {
					$desc = $descriptions[ $slug ];
				} else {
					$desc = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $id ) ), 28, '…' );
				}
			}
		}

		if ( $desc ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
		}
	},
	4
);

/**
 * BreadcrumbList structured data — one small graph per inner page so Google
 * reads the site's shape (Home → Work → case, Home → Journal → entry)
 * instead of inferring it. The visible design deliberately has no crumb
 * trail; the eyebrow line plays that role for humans, this plays it for
 * crawlers, and both describe the same hierarchy.
 */
add_action(
	'wp_head',
	function () {
		if ( is_front_page() || is_404() || is_search() ) {
			return;
		}

		$crumbs = array(
			array( __( 'Home', 'ak-zeyna-child' ), home_url( '/' ) ),
		);

		if ( is_post_type_archive( AK_PROJECT_CPT ) ) {
			$crumbs[] = array( __( 'Work', 'ak-zeyna-child' ), get_post_type_archive_link( AK_PROJECT_CPT ) );
		} elseif ( is_singular( AK_PROJECT_CPT ) ) {
			$crumbs[] = array( __( 'Work', 'ak-zeyna-child' ), get_post_type_archive_link( AK_PROJECT_CPT ) );
			$crumbs[] = array( get_the_title(), get_permalink() );
		} elseif ( is_home() ) {
			$blog = (int) get_option( 'page_for_posts' );
			$crumbs[] = array( $blog ? get_the_title( $blog ) : __( 'Journal', 'ak-zeyna-child' ), $blog ? get_permalink( $blog ) : home_url( '/journal/' ) );
		} elseif ( is_singular( 'post' ) ) {
			$blog = (int) get_option( 'page_for_posts' );
			if ( $blog ) {
				$crumbs[] = array( get_the_title( $blog ), get_permalink( $blog ) );
			}
			$crumbs[] = array( get_the_title(), get_permalink() );
		} elseif ( is_page() ) {
			$crumbs[] = array( get_the_title(), get_permalink() );
		} else {
			return;
		}

		$list = array();
		foreach ( $crumbs as $i => $c ) {
			$list[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => $c[0],
				'item'     => $c[1],
			);
		}

		$json = wp_json_encode(
			array(
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => $list,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		);

		echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode output.
	},
	6
);
