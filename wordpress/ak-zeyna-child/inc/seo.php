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

		$descriptions = array(
			'front'    => 'AK Brand Development Studio is a fashion and brand advisory in London — brand strategy, identity, campaign and runway production, and digital presence, from an idea to an international presence.',
			'journal'  => 'Notes from the studio floor — short, practical writing on positioning, identity, production and presence in fashion.',
			'work'     => 'Selected work by AK Brand Development Studio: brand strategy, identity systems, campaign production and runway shows across London, Paris and Dubai.',
			'services' => 'Four movements — Strategy, Identity, Production, Presence — covering nine services, from brand positioning and identity to campaign production, runway shows and digital promotion.',
			'about'    => 'Founded by Konstantin Lieontiev and Andrey Karakushan — the producers behind London Fashion Day and Odessa Fashion Day — AK Brand Development Studio works across London, Paris and Dubai.',
			'contact'  => 'Tell us where the brand is. We reply within two working days with an honest read on which movements your project needs. AK Brand Development Studio, London, United Kingdom.',
		);

		$desc = '';

		if ( is_front_page() ) {
			$desc = $descriptions['front'];
		} elseif ( is_home() ) {
			$desc = $descriptions['journal'];
		} elseif ( is_post_type_archive( 'portfolio' ) ) {
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
