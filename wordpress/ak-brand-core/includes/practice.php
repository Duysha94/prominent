<?php
/**
 * The practice and the platforms: the studio's canonical facts, exposed as
 * filterable data so the theme renders them and never hard-codes them.
 *
 * Every fact here is taken from the founders' brief or from the platforms'
 * own published record (londonfashionday.co.uk, ofd.org.ua).
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Six disciplines ("movements"), in the order a founder asks the questions.
 * `page` is the slug of the service page under /services/.
 *
 * @return array<int,array<string,mixed>>
 */
function ak_brand_movements(): array {
	$movements = array(
		array(
			'slug'         => 'strategy',
			'page'         => 'brand-strategy',
			'title'        => 'Strategy',
			'name'         => 'Brand strategy',
			'question'     => 'Who are we?',
			'line'         => 'Concept, positioning and the story the brand tells — for labels, companies and founders.',
			'covers'       => 'What the brand is, and who it is for.',
			'image'        => 'lfd-s7-campaign-on-set.jpg',
			'capabilities' => array( 'Brand concept development', 'Brand positioning', 'Brand strategy', 'Brand relaunch', 'Repositioning', 'Brand philosophy', 'Identity foundations', 'Strategic guidance for business growth', 'Personal brand strategy', 'Personal positioning', 'Personal communication strategy', 'Personal visual direction' ),
		),
		array(
			'slug'         => 'identity',
			'page'         => 'brand-identity',
			'title'        => 'Identity',
			'name'         => 'Identity & creative direction',
			'question'     => 'What do we look like?',
			'line'         => 'Logo, visual language and guidelines built to hold up from a woven label to a billboard.',
			'covers'       => 'What it looks and sounds like.',
			'image'        => 'lfd-s8-campaign-close-up.jpg',
			'capabilities' => array( 'Brand identity development', 'Visual direction', 'Logo and identity design', 'Brand guidelines', 'Visual storytelling', 'Creative direction' ),
		),
		array(
			'slug'         => 'image',
			'page'         => 'photo-video-production',
			'title'        => 'Image',
			'name'         => 'Photo & film',
			'question'     => 'Who shoots this?',
			'line'         => 'Campaigns, lookbooks and films — concept, casting, crew and post-production.',
			'covers'       => 'The pictures and the films.',
			'image'        => 'lfd-s7-campaign-regent-street.jpg',
			'capabilities' => array( 'Creative direction', 'Photo campaigns', 'Promotional video production', 'Visual storytelling', 'Campaign production' ),
		),
		array(
			'slug'         => 'experience',
			'page'         => 'fashion-show-production',
			'title'        => 'Experience',
			'name'         => 'Fashion shows & events',
			'question'     => 'Who produces the show?',
			'line'         => 'Runway shows, presentations and launches from the team behind London Fashion Day.',
			'covers'       => 'What happens in the room.',
			'image'        => 'lfd-s7-gorsset.jpg',
			'capabilities' => array( 'Brand presentations', 'Product launches', 'Creative events', 'Independent fashion shows', 'Fashion show production', 'Fashion-week-related production' ),
		),
		array(
			'slug'         => 'digital',
			'page'         => 'website-development',
			'title'        => 'Digital',
			'name'         => 'Websites & digital',
			'question'     => 'Who builds the site?',
			'line'         => 'Websites, online stores and social presence that turn attention into orders.',
			'covers'       => 'Where it lives online.',
			'image'        => 'lfd-s6-lcca-showcase.jpg',
			'capabilities' => array( 'Website creation', 'Website development', 'Social media presence', 'Digital content production', 'Online brand positioning' ),
		),
		array(
			'slug'         => 'visibility',
			'page'         => 'pr-and-promotion',
			'title'        => 'Visibility',
			'name'         => 'PR & promotion',
			'question'     => 'Who gets us seen?',
			'line'         => 'Press, partnerships and paid campaigns on Google, YouTube, Instagram and Facebook.',
			'covers'       => 'How it is seen and heard.',
			'image'        => 'ofd-s31-londonska-hotel.jpg',
			// Two clusters with equal weight, never merged (CAPABILITIES-SYSTEM.md).
			'clusters'     => array(
				'Communication & PR'     => array( 'Marketing strategy', 'Brand communication', 'PR support', 'Campaign development' ),
				'Promotion & paid media' => array( 'Digital advertising campaigns', 'Google promotion', 'YouTube promotion', 'Meta, Facebook and Instagram advertising', 'Audience growth strategies', 'Digital visibility', 'Engagement' ),
			),
			'capabilities' => array( 'Marketing strategy', 'Brand communication', 'PR support', 'Campaign development', 'Google, YouTube and Meta advertising', 'Audience growth' ),
		),
	);
	return (array) apply_filters( 'ak_brand_movements', $movements );
}


/**
 * The founders, verbatim from the brief. AK is one letter each.
 *
 * @return array<int,array<string,mixed>>
 */
function ak_brand_founders(): array {
	$founders = array(
		array(
			'letter' => 'A',
			'name'   => 'Andrii Karakushan',
			'role'   => 'Co-founder — digital, identity and media',
			'short'  => 'Brand development, websites and visual communication. Founder of the online magazine Cool’baba.',
			'bio'    => array(
				'Andrii Karakushan is a creative entrepreneur specialising in brand development, digital presence and visual communication.',
				'He has experience managing a multi-brand retail space for emerging designers, supporting young fashion brands through retail presentation and brand promotion.',
				'He is the founder of the online magazine Cool’baba, a media platform focused on fashion, lifestyle and creative industries.',
				'His expertise includes website development, brand identity, digital communication and building online ecosystems for businesses and creative projects.',
			),
		),
		array(
			'letter' => 'K',
			'name'   => 'Kostiantyn Lieontiev',
			'role'   => 'Co-founder — fashion production and brand strategy',
			'short'  => 'Fashion producer and brand strategist. Heads London Fashion Day and Odessa Fashion Day.',
			'bio'    => array(
				'Kostiantyn Lieontiev is a fashion producer, brand strategist and media professional with extensive experience in brand development and creative industries.',
				'Before focusing on international fashion projects, he spent nine years managing a regional branch of a major advertising holding, where he was responsible for business development, advertising campaigns and strategic client development.',
				'He heads the international fashion platforms London Fashion Day and Odessa Fashion Day, which support emerging designers and build international creative communities, and is developing the fashion brand KEKA for the international market.',
				'Through his work he has collaborated with designers, brands and creative teams across London, Paris and Dubai.',
			),
		),
	);
	return (array) apply_filters( 'ak_brand_founders', $founders );
}




/**
 * Choices for the brief form: the pages under Services (their short titles),
 * plus a runway in London.
 *
 * @return array<string,string> value => label
 */
function ak_brand_brief_services(): array {
	$out    = array();
	$parent = get_page_by_path( 'services' );
	if ( $parent ) {
		foreach ( get_pages( array( 'parent' => $parent->ID, 'sort_column' => 'menu_order', 'post_status' => 'publish' ) ) as $page ) {
			$out[ $page->post_name ] = (string) get_post_meta( $page->ID, 'ak_short_title', true ) ?: $page->post_title;
		}
	}
	if ( ! $out ) {
		foreach ( ak_brand_movements() as $m ) {
			$out[ $m['page'] ] = $m['name'];
		}
	}
	$out['runway'] = __( 'A runway show in London', 'ak-brand-core' );
	return (array) apply_filters( 'ak_brand_brief_services', $out );
}
