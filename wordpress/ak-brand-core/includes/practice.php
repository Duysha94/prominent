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
 * The platforms the founders built — the studio's strongest proof.
 *
 * @return array<int,array<string,string>>
 */
function ak_brand_platforms(): array {
	$platforms = array(
		array(
			'slug'    => 'london-fashion-day',
			'name'    => 'London Fashion Day',
			'kind'    => 'Runway platform · London',
			'founder' => 'Organised by the studio. Founded by Kostiantyn Lieontiev.',
			'note'    => 'An independent international runway for emerging and independent designers, organised by AK since 2023. Eight seasons in London — at St John’s Church, Hyde Park, and now at the historic Porchester Hall in Westminster.',
			'facts'   => array( 'Since' => '2023', 'Seasons' => '8', 'Home' => 'London' ),
			'url'     => 'https://londonfashionday.co.uk/',
			'social'  => 'https://www.instagram.com/london_fashionday',
			'project' => 'london-fashion-day',
			'video'   => 'lfd-s7-runway',
			'city'    => 'London',
		),
		array(
			'slug'    => 'odessa-fashion-day',
			'name'    => 'Odessa Fashion Day',
			'kind'    => 'Runway platform · Odesa',
			'founder' => 'Founded by Kostiantyn Lieontiev.',
			'note'    => 'A runway for Ukrainian designers since 2010. Thirty-two seasons on, it still opens twice a year in Odesa — plus a summer weekend of shows in the city’s Botanical Garden.',
			'facts'   => array( 'Since' => '2010', 'Seasons' => '32', 'Home' => 'Odesa' ),
			'url'     => 'https://ofd.org.ua/',
			'social'  => 'https://www.instagram.com/odessafashionday/',
			'project' => 'odessa-fashion-day',
			'video'   => 'ofd-s31-campaign',
			'city'    => 'Odesa',
		),
		array(
			'slug'    => 'keka',
			'name'    => 'KEKA',
			'kind'    => 'Fashion brand',
			'founder' => 'Founded by Kostiantyn Lieontiev.',
			'note'    => 'A label that ignores gender, age and convention — shown at London Fashion Day and at Fashion For Freedom during Milan Fashion Week, now being developed for the international market.',
			'facts'   => array(),
			'url'     => '',
			'social'  => 'https://www.instagram.com/keka_fashion/',
			'project' => 'keka',
			'video'   => '',
			'city'    => '',
		),
		array(
			'slug'    => 'coolbaba',
			'name'    => 'Cool’baba',
			'kind'    => 'Online magazine',
			'founder' => 'Founded by Andrii Karakushan.',
			'note'    => 'A media platform covering fashion, lifestyle and the creative industries — and an official media partner of London Fashion Day.',
			'facts'   => array(),
			'url'     => 'https://coolbaba.in.ua/',
			'social'  => '',
			'project' => '',
			'video'   => '',
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
			'short'  => 'Fashion producer and brand strategist. Founder of London Fashion Day, Odessa Fashion Day and KEKA.',
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

/**
 * Headline numbers. Each is a published fact; `count` animates when numeric.
 *
 * @return array<int,array<string,string>>
 */
function ak_brand_stats(): array {
	$stats = array(
		array( 'value' => '15', 'suffix' => '+', 'label' => 'Years producing fashion events' ),
		array( 'value' => '32', 'suffix' => '', 'label' => 'Seasons of Odessa Fashion Day' ),
		array( 'value' => '8', 'suffix' => '', 'label' => 'Seasons of London Fashion Day' ),
		array( 'value' => 'A+K', 'suffix' => '', 'label' => 'Founders, one letter each' ),
	);
	return (array) apply_filters( 'ak_brand_stats', $stats );
}

/**
 * The record, year by year.
 *
 * @return array<int,array<string,string>>
 */
function ak_brand_timeline(): array {
	$timeline = array(
		array( 'year' => '2010', 'title' => 'Odessa Fashion Day', 'text' => 'Kostiantyn Lieontiev starts Odessa Fashion Day: a runway for Ukrainian designers that grows to include a shopping space for local labels, master classes and free admission for guests.' ),
		array( 'year' => '2016', 'title' => 'Ukrainian Fashion Week', 'text' => 'OFD signs a memorandum of strategic partnership with Ukrainian Fashion Week. From 2020 it is also a partner of the national channel Fashion TV Ukraine.' ),
		array( 'year' => '2022', 'title' => 'Ukrainian designers in London', 'text' => 'The team presents Ukrainian designers at the International Women’s Forum in London — the show that inspired London Fashion Day. The same year: UWILL Paris Fashion Day.' ),
		array( 'year' => '2023', 'title' => 'London Fashion Day begins', 'text' => 'Season 1 on 22 April: six collections, four of them Ukrainian. Season 2 follows in October with eleven brands. Shows in Dubai and Vienna the same year.' ),
		array( 'year' => '2024', 'title' => 'Seasons 3 and 4', 'text' => 'Designers from Europe, Asia, Africa and the UK; more than 100 media outlets. Season 4 moves to St John’s Church, Hyde Park.' ),
		array( 'year' => '2025', 'title' => 'Fifteen years of OFD', 'text' => 'Odessa Fashion Day marks its 30th season. KEKA and Gorsset show at Fashion For Freedom during Milan Fashion Week. LFD Seasons 5 (HUMAN) and 6 (The Art of Being).' ),
		array( 'year' => '2026', 'title' => 'Porchester Hall', 'text' => 'LFD Season 7 at sunrise on Regent Street and in Hyde Park; OFD Season 32, ONE OF A KIND; Summer Weekend goes ahead after a night of attacks; Season 8 at Porchester Hall on 8 November.' ),
	);
	return (array) apply_filters( 'ak_brand_timeline', $timeline );
}

/**
 * The network around the seasons: partners named on the platforms' own pages.
 *
 * @return array<string,array<int,string>>
 */
function ak_brand_network(): array {
	$network = array(
		'Education'      => array( 'London College of Contemporary Arts (LCCA)', 'Fashion Academy Paris', 'Ushynsky University' ),
		'Production'     => array( 'Show Empire', 'Session Squad', 'Beauty Bureau', 'Dinara Ostr', 'Mariana Zabramna', 'Natalia Tomash' ),
		'International'  => array( 'Odessa Fashion Day', 'Georgia Fashion Week', 'Global Fashion Net', 'Ukrainian Fashion Week', 'Fashion TV Ukraine' ),
		'Media partners' => array( 'FASHION', 'CHIC TV', 'FAB UK Magazine', 'GLAM Week Magazine', 'London One Magazine', 'Fashion Frontier', 'London Business Journal', 'FOSTYLEN', 'Cool’baba Magazine', 'ML Business Magazine', 'PROMINENT Magazine', 'FLICKER Magazine', 'IKON Magazine', 'ARRANT Magazine', 'Milan Weekly Magazine', 'SURREAL Magazine' ),
	);
	return (array) apply_filters( 'ak_brand_network', $network );
}

/**
 * Choices for the brief form: the six disciplines plus a runway in London.
 *
 * @return array<string,string> value => label
 */
function ak_brand_brief_services(): array {
	$out = array();
	foreach ( ak_brand_movements() as $m ) {
		$out[ $m['slug'] ] = $m['name'];
	}
	$out['runway'] = 'A runway show in London';
	return (array) apply_filters( 'ak_brand_brief_services', $out );
}

