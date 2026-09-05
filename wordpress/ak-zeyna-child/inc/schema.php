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
				/*
				 * The full breadth, in the order the practice reads — not
				 * website development and advertising with the rest as an
				 * afterthought. This is one of the places a search engine
				 * quotes the studio back to itself, so a list that opened on
				 * web and ads would be the misstatement repeated widest.
				 */
				'knowsAbout'  => array(
					'Brand strategy and development',
					'Brand positioning',
					'Brand relaunch and repositioning',
					'Personal brand development',
					'Brand identity and creative direction',
					'Visual storytelling',
					'Photo campaign production',
					'Promotional video production',
					'Fashion show production',
					'Fashion week production',
					'Brand presentations and product launches',
					'Creative events',
					'Marketing and communication strategy',
					'Public relations',
					'Campaign development',
					'Website creation and development',
					'Digital presence and online brand positioning',
					'Digital promotion and advertising',
				),
				'founder'     => array(
					array( '@id' => $site . '/about#kostiantyn-lieontiev' ),
					array( '@id' => $site . '/about#andrii-karakushan' ),
				),
			),
			array(
				'@type'    => 'Person',
				'@id'      => $site . '/about#kostiantyn-lieontiev',
				'name'     => 'Kostiantyn Lieontiev',
				'jobTitle' => 'Fashion producer, brand strategist, media professional',
				'url'      => $site . '/about/',
				'worksFor' => array( '@id' => $site . '/#studio' ),
			),
			array(
				'@type'    => 'Person',
				'@id'      => $site . '/about#andrii-karakushan',
				'name'     => 'Andrii Karakushan',
				'jobTitle' => 'Creative entrepreneur — brand development, digital presence, visual communication',
				'url'      => $site . '/about/',
				'worksFor' => array( '@id' => $site . '/#studio' ),
			),
			/*
			 * The owned platforms, generated from the project model.
			 *
			 * These were four hard-coded blocks — and three of the seven
			 * AK-owned projects were simply missing from the graph, while the
			 * two founder @ids still pointed at the old name spellings and
			 * therefore matched nothing on the About page. Reading the
			 * register means the structured data cannot drift from the site
			 * again, and a project added in WordPress appears here on its own.
			 *
			 * A `description` is emitted only when the project has an excerpt,
			 * and a `founder` only where the source document actually names
			 * one. Ownership by the studio is asserted for all of them, which
			 * is the fact that IS established.
			 */
		);

		$ak_founder_of = array(
			'london-fashion-day' => $site . '/about#kostiantyn-lieontiev',
			'odessa-fashion-day' => $site . '/about#kostiantyn-lieontiev',
			'keka'               => $site . '/about#kostiantyn-lieontiev',
			'coolbaba'           => $site . '/about#andrii-karakushan',
		);

		foreach ( ak_published_projects() as $ak_project ) {
			$ak_rel = ak_project_relationship( $ak_project->ID );
			if ( ! $ak_rel || 'ak-owned' !== $ak_rel->slug ) {
				continue;
			}
			$ak_url  = (string) ak_project_meta( 'ak_url', '', $ak_project->ID );
			$ak_type = ak_project_type( $ak_project->ID );
			$ak_node = array(
				'@type'  => ( $ak_type && 'fashion-brand' === $ak_type->slug )
					? array( 'Organization', 'Brand' )
					: 'Organization',
				'@id'    => $site . '/#' . $ak_project->post_name,
				'name'   => get_the_title( $ak_project ),
				'parentOrganization' => array( '@id' => $site . '/#studio' ),
			);
			if ( $ak_url ) {
				$ak_node['url'] = $ak_url;
			}
			if ( has_excerpt( $ak_project ) ) {
				$ak_node['description'] = wp_strip_all_tags( get_the_excerpt( $ak_project ) );
			}
			if ( isset( $ak_founder_of[ $ak_project->post_name ] ) ) {
				$ak_node['founder'] = array( '@id' => $ak_founder_of[ $ak_project->post_name ] );
			}
			$graph[] = $ak_node;
		}


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
