<?php
/**
 * The practice and the platforms: canonical content from
 * docs/CAPABILITIES-SYSTEM.md and the founders' brief, exposed as filterable
 * data so the theme renders them and never hard-codes them.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Six movements, in the order a founder asks the questions.
 *
 * @return array<int,array<string,mixed>>
 */
function ak_brand_movements(): array {
	$movements = array(
		array(
			'slug'         => 'strategy',
			'title'        => 'Strategy',
			'question'     => 'Who are we?',
			'covers'       => 'What the brand is, and who it is for.',
			'capabilities' => array( 'Brand concept development', 'Brand positioning', 'Brand strategy', 'Brand relaunch', 'Repositioning', 'Brand philosophy', 'Identity foundations', 'Strategic guidance for business growth', 'Personal brand strategy', 'Personal positioning', 'Personal identity', 'Personal communication strategy', 'Personal visual direction', 'Personal content direction' ),
		),
		array(
			'slug'         => 'identity',
			'title'        => 'Identity',
			'question'     => 'What do we look like?',
			'covers'       => 'What it looks and sounds like.',
			'capabilities' => array( 'Brand identity development', 'Visual direction', 'Logo and identity design', 'Brand guidelines', 'Visual storytelling', 'Creative direction' ),
		),
		array(
			'slug'         => 'image',
			'title'        => 'Image',
			'question'     => 'Who shoots this?',
			'covers'       => 'The pictures and the films.',
			'capabilities' => array( 'Creative direction', 'Photo campaigns', 'Promotional video production', 'Visual storytelling', 'Campaign production' ),
		),
		array(
			'slug'         => 'experience',
			'title'        => 'Experience',
			'question'     => 'Who produces the show?',
			'covers'       => 'What happens in the room.',
			'capabilities' => array( 'Brand presentations', 'Product launches', 'Creative events', 'Independent fashion shows', 'Fashion show production', 'Fashion-week-related production' ),
		),
		array(
			'slug'         => 'digital',
			'title'        => 'Digital',
			'question'     => 'Who builds the site?',
			'covers'       => 'Where it lives online.',
			'capabilities' => array( 'Website creation', 'Website development', 'Social media presence', 'Digital content production', 'Online brand positioning' ),
		),
		array(
			'slug'         => 'visibility',
			'title'        => 'Visibility',
			'question'     => 'Who gets us written about?',
			'covers'       => 'How it is seen and heard.',
			// Two clusters with equal weight, never merged (CAPABILITIES-SYSTEM.md).
			'clusters'     => array(
				'Communication & PR'     => array( 'Marketing strategy', 'Brand communication', 'PR support', 'Campaign development' ),
				'Promotion & paid media' => array( 'Digital advertising campaigns', 'Google promotion', 'YouTube promotion', 'Meta, Facebook and Instagram advertising', 'Audience growth strategies', 'Digital visibility', 'Engagement' ),
			),
			'capabilities' => array(),
		),
	);
	return (array) apply_filters( 'ak_brand_movements', $movements );
}

/**
 * The platforms the founders built — the studio's strongest proof.
 *
 * @return array<int,array<string,string>>
 */
function ak_brand_platforms(): array {
	$platforms = array(
		array(
			'slug'    => 'london-fashion-day',
			'name'    => 'London Fashion Day',
			'kind'    => 'Fashion platform',
			'founder' => 'Founded and produced by Kostiantyn Lieontiev',
			'note'    => 'An international platform created to support emerging designers.',
			'city'    => 'London',
		),
		array(
			'slug'    => 'odessa-fashion-day',
			'name'    => 'Odessa Fashion Day',
			'kind'    => 'Fashion platform',
			'founder' => 'Founded and produced by Kostiantyn Lieontiev',
			'note'    => 'Built to develop international creative communities.',
			'city'    => 'Odesa',
		),
		array(
			'slug'    => 'keka',
			'name'    => 'KEKA',
			'kind'    => 'Fashion brand',
			'founder' => 'Founded by Kostiantyn Lieontiev',
			'note'    => 'Currently being developed for the international market.',
			'city'    => '',
		),
		array(
			'slug'    => 'coolbaba',
			'name'    => 'Cool’baba',
			'kind'    => 'Online magazine',
			'founder' => 'Founded by Andrii Karakushan',
			'note'    => 'A media platform covering fashion, lifestyle and the creative industries.',
			'city'    => '',
		),
	);
	return (array) apply_filters( 'ak_brand_platforms', $platforms );
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
			'role'   => 'Creative entrepreneur — digital, identity and media',
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
			'role'   => 'Fashion producer and brand strategist',
			'bio'    => array(
				'Kostiantyn Lieontiev is a fashion producer, brand strategist and media professional with extensive experience in brand development and creative industries.',
				'Before focusing on international fashion projects, he spent nine years managing a regional branch of a major advertising holding, where he was responsible for business development, advertising campaigns and strategic client development.',
				'He is the founder and producer of the international fashion platforms London Fashion Day and Odessa Fashion Day, created to support emerging designers and develop international creative communities. He is also the founder of the fashion brand KEKA, currently being developed for the international market.',
				'Through his work he has collaborated with designers, brands and creative teams across London, Paris and Dubai.',
			),
		),
	);
	return (array) apply_filters( 'ak_brand_founders', $founders );
}
