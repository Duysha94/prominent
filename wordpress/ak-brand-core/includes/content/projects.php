<?php
/**
 * Launch projects: the platforms the studio runs and the seasons it produced.
 * Facts, names and quotes come from londonfashionday.co.uk and ofd.org.ua.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<int,array<string,mixed>>
 */
function ak_brand_seed_projects(): array {
	$projects = array();

	$projects[] = array(
		'slug'         => 'london-fashion-day-season-8',
		'title'        => 'London Fashion Day Season 8: PAST. PRESENT. NEXT.',
		'short'        => 'LFD Season 8',
		'order'        => 1,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'type'         => 'season',
		'disciplines'  => array( 'strategy', 'image', 'experience', 'visibility' ),
		'meta'         => array(
			'ak_year'     => '2026',
			'ak_date'     => '2026-11-08',
			'ak_location' => 'London',
			'ak_venue'    => 'Porchester Hall, Westminster',
			'ak_role'     => 'Organiser and producer: season concept, campaign, production and press',
			'ak_url'      => 'https://londonfashionday.co.uk/',
			'ak_credits'  => "Campaign film: Dinara Ostr\nPhotography: Mariana Zabramna\nLook: Gabrielle Nitu, London College of Contemporary Arts\nMake-up: @makeup.byandzelika\nProduction: Andrii Karakushan, Kostiantyn Lieontiev\nShoot hosted by: YourSpace",
		),
		'cover'        => 'lfd-s8-campaign-portrait.jpg',
		'gallery'      => array( 'lfd-s8-campaign-profile.jpg', 'lfd-s8-campaign-close-up.jpg', 'lfd-s8-campaign-seated.jpg', 'lfd-s8-campaign-detail.jpg', 'lfd-s8-campaign-fringe.jpg', 'lfd-s8-campaign-movement.jpg', 'lfd-s8-campaign-standing.jpg' ),
		'excerpt'      => 'Twelve fashion voices at the historic Porchester Hall — and a campaign built around a student designer and an emerging model.',
		'content'      =>
			ak_b_p( 'Fashion has always been a conversation between generations. Season 8 asks what we inherit, what we create today and what we choose to pass on. <strong>PAST</strong> is cultural memory, heritage and craftsmanship. <strong>PRESENT</strong> is the moment those traditions meet technology. <strong>NEXT</strong> is a new generation of names and ideas — and a question about what kind of fashion we want to leave behind.' ) .
			ak_b_p( 'The venue makes the point. Porchester Hall opened in 1929 and has been part of London’s cultural life for almost a century: a room where heritage is part of the conversation rather than a backdrop.' ) .
			ak_b_h( 'The campaign' ) .
			ak_b_p( 'Rather than turning to established names, the Season 8 campaign puts emerging talent at the centre: a look by Gabrielle Nitu, a student at the London College of Contemporary Arts, worn by a young Nigerian model at the start of her career on the London scene. Dinara Ostr directed the film around movement, form and transformation; Mariana Zabramna shot the stills; Andrii Karakushan and Kostiantyn Lieontiev produced.' ) .
			ak_b_h( 'The runway' ) .
			ak_b_p( 'Six runway slots from 13:00 to 18:00 on Sunday 8 November 2026, opening with students of LCCA and followed by Tiarael Fashion, STUDIO CATA, Heymers Education, Erzindjan’s, Tasha Ivy, NILDET, SHICHO, SULITUDE, Yanou Santangelo, NIKA and LEV3L UP.' ) .
			ak_b_p( 'Sterling Law is the season’s exclusive partner. LCCA joins as educational and institutional partner, Show Empire as technical partner, Session Squad and Sasha Mamedova for beauty and Beauty Bureau for image, with Odessa Fashion Day and Georgia Fashion Week as international partners and twenty official media partners.' ) .
			ak_b_quote( 'We are not here to predict the future of fashion. We are here to make space for those who will create it.', 'Kostiantyn Lieontiev, founder of London Fashion Day' ),
	);

	$projects[] = array(
		'slug'         => 'london-fashion-day-season-7',
		'title'        => 'London Fashion Day Season 7: Fashion Beyond Borders',
		'short'        => 'LFD Season 7',
		'order'        => 2,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'type'         => 'season',
		'disciplines'  => array( 'strategy', 'image', 'experience', 'visibility' ),
		'meta'         => array(
			'ak_year'     => '2026',
			'ak_date'     => '2026-05-01',
			'ak_location' => 'London',
			'ak_venue'    => 'St John’s Church, Hyde Park',
			'ak_role'     => 'Organiser and producer: season concept, campaign film, show day and press',
			'ak_url'      => 'https://londonfashionday.co.uk/',
			'ak_credits'  => "Campaign concept: Kostiantyn Lieontiev\nArt direction: Pardis\nFilm: Dinara Ostr\nBackstage photography: Natalia Wise\nMusic: Eduard Nester\nModel: Amirsam Mohammadzadeh\nLook: YADVIGA NETYKSHA",
		),
		'cover'        => 'lfd-s7-campaign-regent-street.jpg',
		'film'         => 'lfd-s7-campaign-film.mp4',
		'film_poster'  => 'lfd-s7-campaign-film.jpg',
		'gallery'      => array( 'lfd-s7-gorsset.jpg', 'lfd-s7-campaign-sunrise.jpg', 'lfd-s7-riri-couture.jpg', 'lfd-s7-campaign-on-set.jpg', 'lfd-s7-keka.jpg', 'lfd-s7-mur-mur.jpg', 'lfd-s7-campaign-crossing.jpg', 'lfd-s7-anabelle-clothing.jpg', 'lfd-s7-backstage-lacing.jpg', 'lfd-s7-kostiantyn-lieontiev-runway.jpg' ),
		'excerpt'      => 'A campaign shot at sunrise on Regent Street and a day of shows in Hyde Park, built on one idea: fashion as a language beyond borders.',
		'content'      =>
			ak_b_p( 'Season 7 treated clothing as a language that exists beyond borders, beyond spoken words and beyond fixed identity. The campaign was captured at sunrise on Regent Street: Iranian model Amirsam Mohammadzadeh, whose life and work connect him to Ukraine, in a look by the Ukrainian brand YADVIGA NETYKSHA — transparent fabrics that reveal the body while concealing the face.' ) .
			ak_b_quote( 'No matter how much we search for perfection in style, trends, or fashion directions, at the centre of it all there is always a person: their individuality, their identity.', 'Kostiantyn Lieontiev, founder and producer of London Fashion Day' ) .
			ak_b_h( 'The day' ) .
			ak_b_p( 'On 1 May 2026, St John’s Church, Hyde Park became a continuous sequence of presentations from 13:00: the London College of Contemporary Arts showcase, then Brera Milano, Anabelle Clothing, Amira, RICKY XTOPHA, Olimzoda Mahin, SOFI RICH and MALUSHA Bags, LEV3L UP, Atelier Pardis, Mur Mur, KEKA, Riri Couture and Gorsset.' ) .
			ak_b_p( 'Attendance was free of charge, organised through selected show slots. Between shows the church stayed a working space for interviews, networking and content, with NITISI as official general partner and Show Empire delivering the technical production.' ),
	);

	$projects[] = array(
		'slug'         => 'london-fashion-day',
		'title'        => 'London Fashion Day',
		'short'        => 'London Fashion Day',
		'order'        => 3,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'type'         => 'platform',
		'disciplines'  => array( 'strategy', 'identity', 'image', 'experience', 'visibility' ),
		'meta'         => array(
			'ak_year'     => '2023 — now',
			'ak_location' => 'London',
			'ak_venue'    => 'St John’s Church, Hyde Park · Porchester Hall',
			'ak_role'     => 'Organiser: concept, production, campaigns, partnerships and press',
			'ak_url'      => 'https://londonfashionday.co.uk/',
			'ak_credits'  => "Founder: Kostiantyn Lieontiev\nOrganised by: AK Brand Development Studio, under the joint leadership of Kostiantyn Lieontiev and Andrii Karakushan\nSeasons: 8 (2023 — 2026)",
		),
		'cover'        => 'still-lfd-westminster-9.jpg',
		'loop'         => 'lfd-s6-runway-loop.mp4',
		'gallery'      => array( 'lfd-s7-kostiantyn-lieontiev-runway.jpg', 'lfd-s6-lcca-showcase.jpg', 'lfd-s5-elen-ost.jpg', 'still-lfd-westminster-17.jpg', 'lfd-s7-riri-couture.jpg', 'lfd-s6-kostiantyn-lieontiev-backstage.jpg', 'lfd-s6-mogardi.jpg' ),
		'excerpt'      => 'An independent international runway for emerging and independent designers — organised by the studio, eight seasons since 2023.',
		'content'      =>
			ak_b_p( 'London Fashion Day began with a single show. In 2022 the team presented Ukrainian designers at the International Women’s Forum in London, and the response made the case for a permanent platform. Season 1 followed on 22 April 2023: six collections, four of them by Ukrainian designers.' ) .
			ak_b_p( 'It has grown season by season since: eleven brands in Season 2, designers from Europe, Asia, Africa and the UK in Seasons 3 and 4, and more than 100 media outlets. From Season 4 the platform found a home at St John’s Church, Hyde Park; Season 8 moves to the historic Porchester Hall in Westminster on 8 November 2026.' ) .
			ak_b_h( 'What the studio does for the platform' ) .
			ak_b_list(
				array(
					'Season concepts and campaign themes — HUMAN, The Art of Being, Fashion Beyond Borders, PAST. PRESENT. NEXT.',
					'Campaign shoots and films, made with long-standing collaborators such as director Dinara Ostr',
					'Every show day: running order, backstage, casting, beauty and technical partners',
					'Designer applications, guest registration and front-of-house',
					'Partnerships with the London College of Contemporary Arts, Show Empire and twenty official media partners',
				)
			) .
			ak_b_quote( 'Sometimes, all a young designer needs is someone willing to open the door.', 'Kostiantyn Lieontiev, founder of London Fashion Day' ),
	);

	$projects[] = array(
		'slug'         => 'odessa-fashion-day-season-32',
		'title'        => 'Odessa Fashion Day Season 32: ONE OF A KIND',
		'short'        => 'OFD Season 32',
		'order'        => 4,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'type'         => 'season',
		'disciplines'  => array( 'strategy', 'image', 'experience' ),
		'meta'         => array(
			'ak_year'     => '2026',
			'ak_date'     => '2026-05-16',
			'ak_location' => 'Odesa',
			'ak_venue'    => 'Luxury Gallery, Sady Peremohy',
			'ak_role'     => 'Founder’s platform: season concept, campaign and show production',
			'ak_url'      => 'https://ofd.org.ua/',
			'ak_credits'  => "Campaign: Kateryna Zakharchenko, Miss Ukraine\nCampaign look: Dima Makeiev, Hôtel Le Mirage collection\nFounder: Kostiantyn Lieontiev",
		),
		'cover'        => 'ofd-s32-runway-poppies.jpg',
		'gallery'      => array( 'ofd-s32-runway-blue.jpg', 'ofd-s32-runway-embroidery.jpg', 'ofd-s32-runway-summer.jpg', 'ofd-s32-runway-silk.jpg', 'ofd-s32-runway-draped.jpg', 'ofd-s32-runway-white.jpg', 'ofd-s32-fashion-talk.jpg' ),
		'excerpt'      => 'A season about the value of individuality, with a campaign starring Miss Ukraine Kateryna Zakharchenko.',
		'content'      =>
			ak_b_p( 'On 16–17 May 2026 the 32nd season of Odessa Fashion Day took place under the name ONE OF A KIND: a conversation about the person and their uniqueness at a time when the world leans towards standardisation and repetition. True luxury, the season argued, is the ability not to lose yourself.' ) .
			ak_b_p( 'The official campaign starred Kateryna Zakharchenko, Miss Ukraine, in a look by designer Dima Makeiev from his Hôtel Le Mirage collection — semi-transparent textures, sculptural forms and a deep, dark palette.' ) .
			ak_b_h( 'The runway' ) .
			ak_b_p( 'The main day of shows took place at Luxury Gallery in the Sady Peremohy shopping centre, with design students of Ushynsky University, VEO BRAND with Mira Models, Magnat Dress, Stahova, Saiman El Sabari, BUDU, Babyface, CIAO CIAO, Mint &amp; Lonza New York, Nimblescissors and Tetyana Paliy.' ) .
			ak_b_p( 'Behind every season stands a team that keeps believing in Ukrainian fashion: producers, stylists, photographers, videographers, model agencies, partners and media — many of whom stay behind the scenes so that designers can be seen.' ),
	);

	$projects[] = array(
		'slug'         => 'london-fashion-day-season-6',
		'title'        => 'London Fashion Day Season 6: The Art of Being',
		'short'        => 'LFD Season 6',
		'order'        => 5,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'type'         => 'season',
		'disciplines'  => array( 'strategy', 'experience', 'visibility' ),
		'meta'         => array(
			'ak_year'     => '2025',
			'ak_date'     => '2025-11-01',
			'ak_location' => 'London',
			'ak_venue'    => 'St John’s Church, Hyde Park',
			'ak_role'     => 'Organiser and producer: concept, casting, show day and press',
			'ak_url'      => 'https://londonfashionday.co.uk/',
			'ak_credits'  => "Founder and producer: Kostiantyn Lieontiev\nOpening showcase: London College of Contemporary Arts",
		),
		'cover'        => 'lfd-s6-lenie-boya-malusha.jpg',
		'loop'         => 'lfd-s6-runway-loop.mp4',
		'gallery'      => array( 'lfd-s6-lcca-showcase.jpg', 'lfd-s6-sara-k-couture.jpg', 'lfd-s6-mogardi.jpg', 'lfd-s6-relique-london.jpg', 'lfd-s6-maison-sophy-g.jpg', 'lfd-s6-kochhouse.jpg', 'lfd-s6-kostiantyn-lieontiev-backstage.jpg' ),
		'excerpt'      => 'Authenticity as the new luxury: a record number of guests, a pop-up space and an expanded line-up in Hyde Park.',
		'content'      =>
			ak_b_p( 'The sixth season took place on 1 November 2025 inside St John’s Church in central London, with a record number of guests, a Pop-Up Corner Space and an expanded designer line-up. Under the theme The Art of Being, it argued that authenticity is the new luxury — in the collections, and in the casting, production and storytelling around them.' ) .
			ak_b_quote( 'In 2025, being yourself is a creative statement.', 'Kostiantyn Lieontiev, founder and producer of London Fashion Day' ) .
			ak_b_h( 'The runway' ) .
			ak_b_p( 'The London College of Contemporary Arts opened the day with five emerging talents, followed by Relique London, VELGUD presented by Global Fashion Net, TIDE CHEN, Sara K Couture, LENIE BOYA × MALUSHA, Maison Sophy G, KochHouse, Mogardi, CHARS, FRENCHEYE and The Moodi Studio — from couture silhouettes to streetwear with a story.' ),
	);

	$projects[] = array(
		'slug'         => 'summer-weekend-by-ofd-2026',
		'title'        => 'SUMMER WEEKEND by OFD 2026',
		'short'        => 'Summer Weekend 2026',
		'order'        => 6,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'type'         => 'season',
		'disciplines'  => array( 'experience', 'image' ),
		'meta'         => array(
			'ak_year'     => '2026',
			'ak_date'     => '2026-08-08',
			'ak_location' => 'Odesa',
			'ak_venue'    => 'MarAmax Academy · Botanical Garden of Odesa University',
			'ak_role'     => 'Founder’s platform: summer season of shows',
			'ak_url'      => 'https://ofd.org.ua/',
			'ak_credits'  => "Founder: Kostiantyn Lieontiev\nProducer: Oleh Shkilniuk\nOpening presentation: DeClassic by Anastasiia Mykytyn",
		),
		'cover'        => 'sw26-botanical-garden.jpg',
		'gallery'      => array( 'sw26-denim.jpg', 'sw26-linen.jpg', 'sw26-outerwear.jpg', 'sw26-cruise.jpg', 'sw26-black.jpg', 'sw26-tailoring.jpg', 'sw26-maramax-academy.jpg' ),
		'excerpt'      => 'Two days of summer and cruise collections in Odesa. The second went ahead the morning after a night of missile and drone attacks.',
		'content'      =>
			ak_b_p( 'SUMMER WEEKEND by OFD is the Odessa Fashion Day team’s annual summer project. In 2026 it opened on 8 August at MarAmax Academy with a presentation by DeClassic, the label of designer Anastasiia Mykytyn, reinterpreting the classic women’s suit through deconstruction.' ) .
			ak_b_p( 'During the night of 8 to 9 August, Odesa and the surrounding region came under a large-scale combined missile and drone attack. The team seriously considered cancelling the second day. The decision to continue was made together — and the designers, models, make-up artists, stylists, photographers and partners all came.' ) .
			ak_b_p( 'On 9 August the shows went ahead in the Botanical Garden of Odesa I. I. Mechnikov National University, with summer and cruise collections from PANACEA, LUMÉA, Yankovska Helen &amp; Lady Forever, Soul Swim, PODOLSKA, Nimble Scissors and KEOSMY by yesundress.' ) .
			ak_b_quote( 'We do not romanticise the conditions in which we work. But we do not want to allow those conditions to take away Ukrainian designers’ ability to create, develop and be seen.', 'Kostiantyn Lieontiev, founder of Odessa Fashion Day' ),
	);

	$projects[] = array(
		'slug'         => 'odessa-fashion-day',
		'title'        => 'Odessa Fashion Day',
		'short'        => 'Odessa Fashion Day',
		'order'        => 7,
		'featured'     => false,
		'relationship' => 'ak-owned',
		'type'         => 'platform',
		'disciplines'  => array( 'strategy', 'identity', 'image', 'experience', 'visibility' ),
		'meta'         => array(
			'ak_year'     => '2010 — now',
			'ak_location' => 'Odesa',
			'ak_venue'    => 'Sady Peremohy · Londonska Hotel · Botanical Garden',
			'ak_role'     => 'Founded by Kostiantyn Lieontiev',
			'ak_url'      => 'https://ofd.org.ua/',
			'ak_credits'  => "Founder: Kostiantyn Lieontiev\nProducer: Oleh Shkilniuk\nPartners: Ukrainian Fashion Week (since 2016), Fashion TV Ukraine (since 2020)",
		),
		'cover'        => 'still-ofd-s31-campaign-13.jpg',
		'film'         => 'ofd-s31-campaign-film.mp4',
		'film_poster'  => 'ofd-s31-campaign-film.jpg',
		'gallery'      => array( 'ofd-s32-runway-poppies.jpg', 'ofd-s31-londonska-hotel.jpg', 'sw26-botanical-garden.jpg', 'ofd-s32-runway-embroidery.jpg', 'ofd-s31-luxury-gallery.jpg', 'ofd-s32-fashion-talk.jpg' ),
		'excerpt'      => 'A runway for Ukrainian designers since 2010 — thirty-two seasons in Odesa, and still opening twice a year.',
		'content'      =>
			ak_b_p( 'Odessa Fashion Day is a cultural initiative that supports and promotes Ukrainian fashion brands, in Odesa and across Ukraine. Each season brings together designers, bloggers, retail, journalists, buyers and the public. Around the shows sit Fashion Shopping Place — a shopping space for Ukrainian labels — master classes and talks. Admission is free, by designer invitation or registration.' ) .
			ak_b_p( 'In 2016 OFD signed a memorandum of strategic partnership with Ukrainian Fashion Week; from 2020 it has been a partner of the national channel Fashion TV Ukraine. Odessa Fashion Day®, Summer Weekend and Odessa Fashion Night are registered trademarks in Ukraine.' ) .
			ak_b_h( 'Through the war' ) .
			ak_b_p( 'The platform has kept working through the full-scale war. Season 30, in April 2025, marked fifteen years. Season 31, UNYIELDING, took over two of the city’s landmark venues; Season 32, ONE OF A KIND, followed in May 2026; and in August SUMMER WEEKEND by OFD went ahead the day after a large-scale attack on Odesa.' ) .
			ak_b_quote( 'OFD is a platform for ideas, meanings and people who are shaping the future of fashion right now.', 'Kostiantyn Lieontiev, founder of Odessa Fashion Day' ),
	);

	$projects[] = array(
		'slug'         => 'odessa-fashion-day-season-31',
		'title'        => 'Odessa Fashion Day Season 31: UNYIELDING',
		'short'        => 'OFD Season 31',
		'order'        => 8,
		'featured'     => false,
		'relationship' => 'ak-owned',
		'type'         => 'season',
		'disciplines'  => array( 'image', 'experience' ),
		'meta'         => array(
			'ak_year'     => '2025',
			'ak_date'     => '2025-11-14',
			'ak_location' => 'Odesa',
			'ak_venue'    => 'Sady Peremohy · Londonska Hotel',
			'ak_role'     => 'Founder’s platform: campaign film and two days of shows',
			'ak_url'      => 'https://ofd.org.ua/',
			'ak_credits'  => "Founder: Kostiantyn Lieontiev\nVenues: Luxury Gallery, Sady Peremohy; Londonska Hotel",
		),
		'cover'        => 'ofd-s31-londonska-hotel.jpg',
		'film'         => 'ofd-s31-campaign-film.mp4',
		'film_poster'  => 'ofd-s31-campaign-film.jpg',
		'gallery'      => array( 'still-ofd-s31-campaign-21.jpg', 'ofd-s31-luxury-gallery.jpg', 'ofd-s31-londonska-red.jpg', 'ofd-s31-luxury-gallery-2.jpg', 'ofd-s31-londonska-embroidery.jpg', 'ofd-s31-londonska-patchwork.jpg', 'ofd-s31-luxury-gallery-3.jpg', 'ofd-s31-londonska-lace.jpg' ),
		'excerpt'      => 'Two days, two landmark venues and one theme: resilience, strength and creative movement forward.',
		'content'      =>
			ak_b_p( 'Season 31 was held over two days under the theme UNYIELDING — resilience, strength and creative movement forward despite the challenges of today. The campaign film set a sculptural navy gown against coral light.' ) .
			ak_b_p( 'Day one opened in the Luxury Gallery of the Sady Peremohy shopping mall with Effetto, Anna Dobrydneva by Sadnytskyy, Mur Mur and Maison Sophy G. Day two moved to the Londonska Hotel, one of Odesa’s landmark buildings, with thirteen collections from MODIVA, MAGNAT, Vasylieva Natalia Upcycling, TwinS Style x Lady Forever, EYKE, Mint x Vzutik, Lady’s Atelier x Lady Forever, SAIMAN EL SABARI, AN&amp;KIREN, ArtLeila Design x Lady Forever, VALER Glass, PODOLSKA and Nimble Scissors — from upcycling and avant-garde forms to classic lines.' ),
	);

	$projects[] = array(
		'slug'         => 'london-fashion-day-season-5',
		'title'        => 'London Fashion Day Season 5: HUMAN',
		'short'        => 'LFD Season 5',
		'order'        => 9,
		'featured'     => false,
		'relationship' => 'ak-owned',
		'type'         => 'season',
		'disciplines'  => array( 'strategy', 'image', 'experience' ),
		'meta'         => array(
			'ak_year'     => '2025',
			'ak_date'     => '2025-04-26',
			'ak_location' => 'London',
			'ak_venue'    => 'St John’s Church, Hyde Park',
			'ak_role'     => 'Organiser and producer: theme, campaign with KEKA, show day',
			'ak_url'      => 'https://londonfashionday.co.uk/',
			'ak_credits'  => "Campaign: KEKA\nFace of the season: Zain Shan\nDirection and video: Dinara Ostr\nPhotography: Nicolo Urbani\nProduction: Kostiantyn Lieontiev",
		),
		'cover'        => 'lfd-s5-elen-ost.jpg',
		'gallery'      => array( 'lfd-s5-keka-by-novikov.jpg', 'lfd-s5-gorsset.jpg', 'lfd-s5-keka-by-novikov-2.jpg', 'lfd-s5-panache-by-elvira.jpg', 'lfd-s5-elen-ost-2.jpg' ),
		'excerpt'      => 'A season that put the person, not the standard, at the front of fashion — with a campaign made with KEKA.',
		'content'      =>
			ak_b_p( 'Season 5 opened at St John’s Church, Hyde Park on 26 April 2025 under one word: HUMAN — a declaration of freedom, self-expression and authenticity that brings the person, not the standard, to the front of fashion.' ) .
			ak_b_p( 'The official campaign was created with the Ukrainian label KEKA, whose philosophy goes beyond gender, age and traditional norms: Zain Shan as the face of the season, direction and video by Dinara Ostr, photography by Nicolo Urbani and production by Kostiantyn Lieontiev.' ) .
			ak_b_quote( 'This season is not about the image — it’s about the person.', 'Kostiantyn Lieontiev, founder of London Fashion Day' ) .
			ak_b_p( 'On the runway: Lenie Boya, HOME NEST &amp; MALUSHA Bag Design Studio, the London College of Contemporary Arts showcase, Panache by Elvira, Fashion Paris Academy, Gorsset, KEKA by Novikov and ELEN OST, among others.' ),
	);

	$projects[] = array(
		'slug'         => 'keka',
		'title'        => 'KEKA',
		'short'        => 'KEKA',
		'order'        => 10,
		'featured'     => false,
		'relationship' => 'ak-owned',
		'type'         => 'brand',
		'disciplines'  => array( 'strategy', 'identity', 'image', 'experience' ),
		'meta'         => array(
			'ak_year'     => 'Ongoing',
			'ak_location' => 'London · Milan · Odesa',
			'ak_venue'    => '',
			'ak_role'     => 'Founder’s label: positioning, campaigns and runway',
			'ak_url'      => 'https://www.instagram.com/keka_fashion/',
			'ak_credits'  => "Founder: Kostiantyn Lieontiev\nShown at: London Fashion Day; Fashion For Freedom, Milan Fashion Week 2025",
		),
		'cover'        => 'lfd-s7-keka.jpg',
		'gallery'      => array( 'lfd-s5-keka-by-novikov.jpg', 'lfd-s5-keka-by-novikov-2.jpg' ),
		'excerpt'      => 'A label that ignores gender, age and convention — on the runway in London and Milan, and being developed for the international market.',
		'content'      =>
			ak_b_p( 'KEKA is the fashion brand founded by Kostiantyn Lieontiev, with a philosophy that goes beyond gender, age and traditional norms. It has shown at London Fashion Day across several seasons and created the official campaign for Season 5, HUMAN.' ) .
			ak_b_p( 'In September 2025 KEKA joined Fashion For Freedom during Milan Fashion Week, reimagining Tudor echoes as a contemporary cultural and political statement. The brand is now being developed for the international market — using the same methods the studio brings to clients: positioning, campaigns, runway and press.' ),
	);

	return (array) apply_filters( 'ak_brand_seed_projects', $projects );
}
