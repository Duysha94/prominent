<?php
/**
 * Structured data.
 *
 * A linked graph rather than a lone Organization blob, so a search engine can
 * connect Kostiantyn to London Fashion Day instead of treating them as two
 * unrelated strings that happen to share a page.
 *
 * Edit the constants below; everything else follows from them.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_head',
	function () {
		$site = untrailingslashit( home_url() );

		$graph = array(
			array(
				'@type'     => 'WebSite',
				'@id'       => $site . '/#website',
				'url'       => $site,
				'name'      => 'AK Brand Development Studio',
				'publisher' => array( '@id' => $site . '/#studio' ),
				'inLanguage' => 'en-GB',
			),
			array(
				'@type'       => array( 'ProfessionalService', 'Organization' ),
				'@id'         => $site . '/#studio',
				'name'        => 'AK Brand Development Studio',
				'alternateName' => 'AK',
				'slogan'      => 'Fashion & Brand Advisory',
				'description' => 'An independent creative and strategic practice specialising in brand development, fashion consulting and creative production.',
				'disambiguatingDescription' => 'Named for its two co-owners: A is Andrii Karakushan, K is Kostiantyn Lieontiev.',
				'url'         => $site,
				'email'       => ak_studio_email(),
				// Read from the studio facts, not typed here: an owner who
				// changes the city in Customizer must not leave the structured
				// data claiming somewhere else.
				'address'     => array(
					'@type'           => 'PostalAddress',
					'addressLocality' => ak_studio( 'city' ),
					'addressCountry'  => ak_studio( 'country' ),
				),
				'areaServed'  => array_values(
					array_filter(
						array_map(
							function ( $ak_city ) {
								$ak_city = trim( $ak_city );
								return $ak_city ? array(
									'@type' => 'City',
									'name'  => $ak_city,
								) : null;
							},
							preg_split( '/\s*[·,|]\s*/u', ak_studio( 'cities' ) )
						)
					)
				),
				// sameAs is omitted entirely when no profile is set — an
				// empty sameAs array is worse than no claim at all.
				'sameAs'      => array_values( ak_socials() ),
				'knowsAbout'  => array(
					'Brand development',
					'Brand positioning',
					'Personal brand strategy',
					'Brand identity',
					'Creative direction',
					'Fashion consulting',
					'Fashion show production',
					'Photo and video campaign production',
					'Event production',
					'Public relations',
					'Website development',
					'Digital promotion',
				),
				'founder'     => array(
					array( '@id' => $site . '/about#konstantin-lieontiev' ),
					array( '@id' => $site . '/about#andrey-karakushan' ),
				),
			),
			array(
				'@type'    => 'Person',
				'@id'      => $site . '/about#konstantin-lieontiev',
				'name'     => 'Kostiantyn Lieontiev',
				'jobTitle' => 'Fashion producer, brand strategist',
				'url'      => $site . '/about/',
				'worksFor' => array( '@id' => $site . '/#studio' ),
			),
			array(
				'@type'    => 'Person',
				'@id'      => $site . '/about#andrey-karakushan',
				'name'     => 'Andrii Karakushan',
				'jobTitle' => 'Creative entrepreneur, digital and identity',
				'url'      => $site . '/about/',
				'worksFor' => array( '@id' => $site . '/#studio' ),
			),
			// The platforms as first-class nodes. schema.org's `founder`
			// property belongs to Organization (Person has no `founderOf`
			// in the released vocabulary), so the relationship is asserted
			// from the platform's side, where validators accept it.
			array(
				'@type'       => 'Organization',
				'@id'         => $site . '/#london-fashion-day',
				'name'        => 'London Fashion Day',
				'url'         => 'https://londonfashionday.co.uk/',
				'description' => 'International fashion platform supporting emerging designers.',
				'founder'     => array( '@id' => $site . '/about#konstantin-lieontiev' ),
			),
			array(
				'@type'       => 'Organization',
				'@id'         => $site . '/#odessa-fashion-day',
				'name'        => 'Odessa Fashion Day',
				'url'         => 'https://ofd.org.ua/',
				'description' => 'International fashion platform supporting emerging designers.',
				'founder'     => array( '@id' => $site . '/about#konstantin-lieontiev' ),
			),
			array(
				'@type'       => array( 'Organization', 'Brand' ),
				'@id'         => $site . '/#keka',
				'name'        => 'KEKA',
				'url'         => 'https://keka.design/',
				'description' => 'Fashion brand in development for the international market.',
				'founder'     => array( '@id' => $site . '/about#konstantin-lieontiev' ),
			),
			array(
				'@type'       => 'Organization',
				'@id'         => $site . '/#coolbaba',
				'name'        => "Cool'baba",
				'url'         => 'https://coolbaba.in.ua/',
				'description' => 'Online magazine covering fashion, lifestyle and creative industries.',
				'founder'     => array( '@id' => $site . '/about#andrey-karakushan' ),
			),
		);

		$json = wp_json_encode(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => $graph,
			),
			JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		);

		echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode output.
	},
	5
);
