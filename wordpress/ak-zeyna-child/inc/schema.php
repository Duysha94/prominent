<?php
/**
 * Structured data.
 *
 * A linked graph rather than a lone Organization blob, so a search engine can
 * connect Konstantin to London Fashion Day instead of treating them as two
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
				'slogan'      => 'Fashion & Brand Advisory',
				'description' => 'An independent creative and strategic practice specialising in brand development, fashion consulting and creative production.',
				'url'         => $site,
				'email'       => 'hello@akbrand.studio',
				'address'     => array(
					'@type'           => 'PostalAddress',
					'addressLocality' => 'London',
					'addressCountry'  => 'GB',
				),
				'areaServed'  => array(
					array(
						'@type' => 'City',
						'name'  => 'London',
					),
					array(
						'@type' => 'City',
						'name'  => 'Paris',
					),
					array(
						'@type' => 'City',
						'name'  => 'Dubai',
					),
				),
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
					array( '@id' => $site . '/studio#konstantin-lieontiev' ),
					array( '@id' => $site . '/studio#andrey-karakushan' ),
				),
			),
			array(
				'@type'       => 'Person',
				'@id'         => $site . '/studio#konstantin-lieontiev',
				'name'        => 'Konstantin Lieontiev',
				'jobTitle'    => 'Fashion producer, brand strategist',
				'worksFor'    => array( '@id' => $site . '/#studio' ),
				'founderOf'   => array(
					array(
						'@type' => 'Event',
						'name'  => 'London Fashion Day',
					),
					array(
						'@type' => 'Event',
						'name'  => 'Odessa Fashion Day',
					),
					array(
						'@type' => 'Brand',
						'name'  => 'KEKA',
					),
				),
			),
			array(
				'@type'     => 'Person',
				'@id'       => $site . '/studio#andrey-karakushan',
				'name'      => 'Andrey Karakushan',
				'jobTitle'  => 'Creative entrepreneur, digital and identity',
				'worksFor'  => array( '@id' => $site . '/#studio' ),
				'founderOf' => array(
					array(
						'@type' => 'Periodical',
						'name'  => "Cool'baba",
					),
				),
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
