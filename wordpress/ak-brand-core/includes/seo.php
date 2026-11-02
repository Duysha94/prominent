<?php
/**
 * Search and sharing: meta description, Open Graph and structured data.
 * Steps aside automatically when a dedicated SEO plugin is active.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

function ak_brand_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/**
 * One-sentence description of the current view.
 */
function ak_brand_seo_description(): string {
	$text = '';
	if ( is_front_page() ) {
		$front = (int) get_option( 'page_on_front' );
		$text  = $front ? get_post_field( 'post_excerpt', $front ) : '';
		$text  = $text ?: ak_brand_studio( 'description' );
	} elseif ( is_home() ) {
		$text = (string) get_post_field( 'post_excerpt', (int) get_option( 'page_for_posts' ) );
	} elseif ( is_post_type_archive( 'ak_project' ) ) {
		$text = __( 'Seasons, campaigns and brands produced by AK Brand Development Studio — including London Fashion Day and Odessa Fashion Day.', 'ak-brand-core' );
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		$text = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( (string) $post->post_content ) ), 30, '…' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$text = term_description();
	}
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $text ) ) );
	return $text ?: ak_brand_studio( 'description' );
}

/**
 * Share image for the current view.
 *
 * @return array{url:string,width:int,height:int}|null
 */
function ak_brand_seo_image(): ?array {
	$id = 0;
	if ( is_singular() && has_post_thumbnail() ) {
		$id = (int) get_post_thumbnail_id();
	}
	if ( ! $id && is_page() ) {
		$movement = (string) get_post_meta( get_queried_object_id(), '_ak_movement', true );
		foreach ( ak_brand_movements() as $m ) {
			if ( $m['slug'] === $movement && ! empty( $m['image'] ) ) {
				$id = ak_brand_seed_attachment( $m['image'] );
			}
		}
	}
	if ( ! $id ) {
		$id = ak_brand_seed_attachment( 'still-lfd-westminster-9.jpg' );
	}
	if ( $id ) {
		$img = wp_get_attachment_image_src( $id, 'large' );
		if ( $img ) {
			return array( 'url' => $img[0], 'width' => (int) $img[1], 'height' => (int) $img[2] );
		}
	}
	$fallback = (string) apply_filters( 'ak_brand_default_share_image', '' );
	return $fallback ? array( 'url' => $fallback, 'width' => 1600, 'height' => 900 ) : null;
}

add_filter(
	'document_title_parts',
	static function ( $parts ) {
		if ( ak_brand_seo_plugin_active() ) {
			return $parts;
		}
		if ( is_front_page() ) {
			$parts['title']   = ak_brand_studio( 'name' );
			$parts['tagline'] = __( 'Brand & fashion development studio in London', 'ak-brand-core' );
		} elseif ( is_post_type_archive( 'ak_project' ) ) {
			$parts['title'] = __( 'Work', 'ak-brand-core' );
		}
		return $parts;
	}
);

add_action(
	'wp_head',
	static function () {
		if ( ak_brand_seo_plugin_active() || is_404() || is_search() ) {
			return;
		}
		$desc  = ak_brand_seo_description();
		$title = wp_get_document_title();
		$url   = is_singular() ? get_permalink() : ( is_front_page() ? home_url( '/' ) : ( is_post_type_archive( 'ak_project' ) ? get_post_type_archive_link( 'ak_project' ) : '' ) );
		$img   = ak_brand_seo_image();
		echo "\n" . '<meta name="description" content="' . esc_attr( wp_html_excerpt( $desc, 300, '…' ) ) . '">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( ak_brand_studio( 'name' ) ) . '">' . "\n";
		echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( wp_html_excerpt( $desc, 300, '…' ) ) . '">' . "\n";
		echo '<meta property="og:locale" content="en_GB">' . "\n";
		if ( $url ) {
			echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
		}
		if ( $img ) {
			echo '<meta property="og:image" content="' . esc_url( $img['url'] ) . '">' . "\n";
			echo '<meta property="og:image:width" content="' . (int) $img['width'] . '">' . "\n";
			echo '<meta property="og:image:height" content="' . (int) $img['height'] . '">' . "\n";
		}
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		if ( is_singular( 'post' ) ) {
			echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
		}
	},
	2
);

/**
 * Structured data graph for the current view.
 *
 * @return array<int,array<string,mixed>>
 */
function ak_brand_schema_graph(): array {
	$home  = home_url( '/' );
	$orgid = $home . '#organization';
	$same  = array_values( array_filter( array( ak_brand_studio( 'instagram' ), ak_brand_studio( 'linkedin' ), ak_brand_studio( 'facebook' ), ak_brand_studio( 'tiktok' ) ) ) );
	$org   = array(
		'@type'       => 'Organization',
		'@id'         => $orgid,
		'name'        => ak_brand_studio( 'name' ),
		'url'         => $home,
		'email'       => ak_brand_studio( 'email' ),
		'description' => ak_brand_studio( 'description' ),
		'address'     => array( '@type' => 'PostalAddress', 'addressLocality' => ak_brand_studio( 'city' ), 'addressCountry' => 'GB' ),
		'areaServed'  => 'Worldwide',
		'knowsAbout'  => array_column( ak_brand_movements(), 'name' ),
		'founder'     => array_map(
			static fn( $f ) => array( '@type' => 'Person', 'name' => $f['name'], 'jobTitle' => $f['role'] ),
			ak_brand_founders()
		),
	);
	if ( ak_brand_studio( 'legal_name' ) ) {
		$org['legalName'] = ak_brand_studio( 'legal_name' );
	}
	if ( $same ) {
		$org['sameAs'] = $same;
	}
	if ( has_site_icon() ) {
		$org['logo'] = get_site_icon_url( 512 );
	}
	$graph = array(
		$org,
		array(
			'@type'     => 'WebSite',
			'@id'       => $home . '#website',
			'url'       => $home,
			'name'      => ak_brand_studio( 'name' ),
			'publisher' => array( '@id' => $orgid ),
			'inLanguage' => 'en-GB',
		),
	);

	if ( is_page() ) {
		$movement = (string) get_post_meta( get_queried_object_id(), '_ak_movement', true );
		if ( $movement ) {
			$graph[] = array(
				'@type'       => 'Service',
				'name'        => get_the_title(),
				'serviceType' => get_the_title(),
				'description' => ak_brand_seo_description(),
				'url'         => get_permalink(),
				'provider'    => array( '@id' => $orgid ),
				'areaServed'  => 'Worldwide',
			);
		}
	}

	if ( is_singular( 'ak_project' ) ) {
		$p   = ak_brand_project();
		$img = ak_brand_seo_image();
		if ( $p['date'] ) {
			$event = array(
				'@type'               => 'Event',
				'name'                => get_the_title(),
				'description'         => ak_brand_seo_description(),
				'startDate'           => $p['date'],
				'eventStatus'         => 'https://schema.org/EventScheduled',
				'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
				'location'            => array(
					'@type'   => 'Place',
					'name'    => $p['venue'] ?: $p['location'],
					'address' => array( '@type' => 'PostalAddress', 'addressLocality' => $p['location'], 'addressCountry' => 'London' === $p['location'] ? 'GB' : 'UA' ),
				),
				'organizer'           => array( '@id' => $orgid ),
				'url'                 => get_permalink(),
			);
			if ( $img ) {
				$event['image'] = $img['url'];
			}
			$graph[] = $event;
		} else {
			$graph[] = array(
				'@type'       => 'CreativeWork',
				'name'        => get_the_title(),
				'description' => ak_brand_seo_description(),
				'url'         => get_permalink(),
				'creator'     => array( '@id' => $orgid ),
				'image'       => $img['url'] ?? null,
			);
		}
	}

	if ( is_singular( 'post' ) ) {
		$img     = ak_brand_seo_image();
		$graph[] = array(
			'@type'         => 'BlogPosting',
			'headline'      => get_the_title(),
			'description'   => ak_brand_seo_description(),
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'url'           => get_permalink(),
			'author'        => array( '@id' => $orgid ),
			'publisher'     => array( '@id' => $orgid ),
			'image'         => $img['url'] ?? null,
			'inLanguage'    => 'en-GB',
		);
	}

	return (array) apply_filters( 'ak_brand_schema_graph', $graph );
}

add_action(
	'wp_head',
	static function () {
		if ( ak_brand_seo_plugin_active() || is_404() ) {
			return;
		}
		$data = array(
			'@context' => 'https://schema.org',
			'@graph'   => array_values( ak_brand_schema_graph() ),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	},
	3
);

/* Sitemaps: no author pages (they would publish the admin login name), and no
   discipline listing, which has no address of its own. */
add_filter(
	'wp_sitemaps_add_provider',
	static fn( $provider, $name ) => 'users' === $name ? false : $provider,
	10,
	2
);
add_filter(
	'wp_sitemaps_taxonomies',
	static function ( $taxonomies ) {
		unset( $taxonomies['ak_capability'] );
		return $taxonomies;
	}
);
