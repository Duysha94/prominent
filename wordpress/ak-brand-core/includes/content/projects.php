<?php
/**
 * Launch projects: the websites the studio designed and built, the seasons
 * and campaigns it produced, its own platforms and brand. Each project is a
 * page of blocks — header, facts, story, film, gallery — fully editable.
 * Facts, names and quotes come from the sites themselves and from
 * londonfashionday.co.uk and ofd.org.ua.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build a project page from its parts.
 *
 * @param array<string,mixed> $p Project definition.
 */
function ak_seed_project_content( array $p ): string {
	$h = array(
		'eyebrow' => $p['eyebrow'],
		'heading' => $p['title'],
		'size'    => 'cover',
	);
	if ( ! empty( $p['screens'] ) ) {
		// Websites: the title on its own, then the site itself, large.
		$h['size'] = 'large';
		$h['lede'] = $p['excerpt'];
	} elseif ( ! empty( $p['header_video'] ) ) {
		$v              = ak_b_media( $p['header_video'] );
		$poster         = ak_b_media( $p['header_poster'] ?? $p['cover'], 'full' );
		$h['mediaType'] = 'video';
		$h['mediaId']   = $v['id'];
		$h['mediaUrl']  = $v['url'];
		$h['posterUrl'] = $poster['url'];
	} else {
		$m             = ak_b_media( $p['header'] ?? $p['cover'], 'full' );
		$h['mediaId']  = $m['id'];
		$h['mediaUrl'] = $m['url'];
	}
	$facts = array();
	foreach ( $p['facts'] as $label => $value ) {
		$facts[] = array( 'label' => $label, 'value' => $value );
	}
	$out  = ak_b_ak( 'page-header', $h );
	if ( ! empty( $p['screens'] ) ) {
		$sc   = $p['screens'];
		$shot = static function ( string $file ): array {
			$m = ak_b_media( $file, 'full' );
			return array( 'id' => $m['id'], 'url' => $m['full'] );
		};
		$desk  = $shot( $sc['desktop'] );
		$phone = $shot( $sc['phone'] );
		$out  .= ak_b_ak(
			'screens',
			array(
				'domain'     => $sc['domain'],
				'accent'     => $sc['accent'],
				'desktopId'  => $desk['id'],
				'desktopUrl' => $desk['url'],
				'mobileId'   => $phone['id'],
				'mobileUrl'  => $phone['url'],
				'pages'      => array_values( array_filter( array_map( $shot, $sc['pages'] ), static fn( $x ) => $x['id'] ) ),
				'phones'     => array_values( array_filter( array_map( $shot, $sc['phones'] ), static fn( $x ) => $x['id'] ) ),
			)
		);
	}
	$out .= ak_b_ak( 'facts', array( 'items' => $facts, 'linkLabel' => $p['link'][0] ?? '', 'linkUrl' => $p['link'][1] ?? '' ) );
	$body = ak_b_lead( $p['lead'] ) . $p['body'];
	if ( ! empty( $p['credits'] ) ) {
		$items = array();
		foreach ( $p['credits'] as $role => $names ) {
			$items[] = '<strong>' . $role . '</strong> ' . $names;
		}
		$body .= ak_b_h( 'Credits', 3 ) . ak_b_list( $items, false, 'is-style-credits' );
	}
	$out .= ak_b_section( $body );
	if ( ! empty( $p['film'] ) ) {
		$out .= ak_b_section( ak_b_video( $p['film'][0], $p['film'][1], $p['film'][2] ?? '' ) );
	}
	if ( ! empty( $p['gallery'] ) ) {
		$out .= ak_b_section( ak_b_gallery( $p['gallery'] ) );
	}
	return $out . ak_b_pattern( 'cta' );
}

/**
 * A website project.
 *
 * @param array<string,mixed> $w Website definition.
 * @return array<string,mixed>
 */
function ak_seed_website( array $w ): array {
	$k      = $w['key'];
	$exists = static fn( $f ) => is_readable( AK_BRAND_CORE_DIR . 'seed/media/' . $f );
	return array(
		'slug'         => $w['slug'],
		'title'        => $w['title'],
		'short'        => $w['short'] ?? $w['title'],
		'kind'         => $w['kind'],
		'location'     => $w['location'] ?? '',
		'year'         => '',
		'status'       => $w['status'] ?? '',
		'order'        => $w['order'],
		'featured'     => false,
		'relationship' => $w['relationship'],
		'types'        => array( 'website' ),
		'disciplines'  => $w['disciplines'] ?? array( 'digital' ),
		'cover'        => "web-{$k}-cover.jpg",
		'screens'      => array(
			'domain'  => $w['domain'],
			'accent'  => $w['accent'] ?? '#f37021',
			'desktop' => "web-{$k}-desktop.jpg",
			'phone'   => "web-{$k}-phone.jpg",
			'pages'   => array_values( array_filter( array( "web-{$k}-desktop-2.jpg", "web-{$k}-desktop-3.jpg" ), $exists ) ),
			'phones'  => array_values( array_filter( array( "web-{$k}-phone-2.jpg", "web-{$k}-phone-3.jpg" ), $exists ) ),
		),
		'eyebrow'      => '<a href="/work/?type=website">Work</a> / Websites',
		'excerpt'      => $w['excerpt'],
		'facts'        => $w['facts'],
		'link'         => array( $w['domain'], 'https://' . $w['domain'] . '/' ),
		'lead'         => $w['lead'],
		'body'         => implode( '', array_map( 'ak_b_p', $w['body'] ) ),
	);
}

/**
 * @return array<int,array<string,mixed>>
 */
function ak_brand_seed_projects(): array {
	$projects = array();

	/* ── Websites ─────────────────────────────────────────────────────── */
	$projects[] = ak_seed_website(
		array(
			'key'          => 'smyn-salon',
			'accent'       => '#8c1c2b',
			'slug'         => 'show-me-your-nails',
			'title'        => 'Show Me Your Nails',
			'kind'         => 'Salon website',
			'location'     => 'London',
			'order'        => 1,
			'relationship' => 'client',
			'domain'       => 'showmeyournails.co.uk',
			'excerpt'      => 'A new website for a nail and beauty salon in Marylebone, with booking a few taps away.',
			'facts'        => array( 'Client' => 'Show Me Your Nails, Marylebone', 'What' => 'Website for a nail and beauty salon', 'Year' => '2026', 'Our part' => 'Design and development' ),
			'lead'         => 'A new website for Show Me Your Nails, a nail and beauty salon at 20 Seymour Place in Marylebone — Russian manicure, brows, massage and facials.',
			'body'         => array(
				'The site is built around booking: Fresha, a WhatsApp chat and a direct phone line are one tap away on every screen, next to the salon’s address in Marylebone and its opening hours — all in the salon’s deep burgundy and soft serif type.',
				'On a salon site the treatment, the time and the confirmation should never be more than a few taps apart on a phone — that was the brief.',
			),
		)
	);
	$projects[] = ak_seed_website(
		array(
			'key'          => 'lenieboya',
			'accent'       => '#b3261e',
			'slug'         => 'lenie-boya',
			'title'        => 'Lenie Boya',
			'kind'         => 'Online store',
			'location'     => 'London',
			'order'        => 3,
			'relationship' => 'client',
			'domain'       => 'lenieboya.com',
			'excerpt'      => 'An online store for a British luxury womenswear label.',
			'facts'        => array( 'Client' => 'Lenie Boya', 'What' => 'Online store for a British luxury womenswear label', 'Our part' => 'Website design and development' ),
			'lead'         => 'An online store for Lenie Boya, a British luxury womenswear label designing intelligent, elegant and timeless pieces for the modern woman.',
			'body'         => array(
				'Collections, bespoke services and the label’s story sit together in one shop, with product pages built for premium dresses and tailored silhouettes.',
				'On a phone the store works like an app, with its own bar of shortcuts at the bottom of the screen for the shop, the basket and the account.',
			),
		)
	);
	$projects[] = ak_seed_website(
		array(
			'key'          => 'wolax',
			'accent'       => '#b8892d',
			'slug'         => 'wolax-interiors',
			'title'        => 'Wolax Interiors',
			'kind'         => 'Company website',
			'location'     => 'London',
			'order'        => 5,
			'relationship' => 'client',
			'domain'       => 'wolax.co.uk',
			'excerpt'      => 'A website for a London company making bespoke furniture and interior fit-outs.',
			'facts'        => array( 'Client' => 'Wolax Interiors London', 'What' => 'Website for a bespoke furniture and fit-out company', 'Our part' => 'Website design and development' ),
			'lead'         => 'A website for Wolax Interiors, who design, manufacture and install bespoke furniture and interior fit-outs for offices, hospitality and homes across London, the UK and Europe.',
			'body'         => array(
				'The site leads with the work itself — tailored interiors photographed large — then explains the services, from interior design and project management to budgeting and bespoke manufacturing.',
				'Every route through the site ends in the same place: a request for a quote.',
			),
		)
	);
	$projects[] = ak_seed_website(
		array(
			'key'          => 'keka',
			'accent'       => '#c4520f',
			'slug'         => 'keka-website',
			'title'        => 'KEKA — brand website',
			'short'        => 'KEKA website',
			'kind'         => 'Brand website',
			'order'        => 7,
			'relationship' => 'ak-owned',
			'disciplines'  => array( 'digital', 'identity' ),
			'domain'       => 'keka.design',
			'excerpt'      => 'A film-led brand site for the fashion label KEKA, with lookbooks, news and a store on the way.',
			'facts'        => array( 'Brand' => 'KEKA', 'What' => 'Brand website with lookbooks and news', 'Store' => 'Online store coming soon', 'Our part' => 'Design and development' ),
			'lead'         => 'The home of KEKA, a fashion label that treats clothing as a language of identity and self-expression.',
			'body'         => array(
				'Film-led and editorial: a runway film opens the site, followed by the collections, lookbooks from each season and the label’s news from London and beyond.',
				'The online store is being prepared in the same system, so shopping will sit alongside the stories.',
			),
		)
	);
	$projects[] = ak_seed_website(
		array(
			'key'          => 'utrend',
			'accent'       => '#c2185b',
			'slug'         => 'ultimate-trend-store',
			'title'        => 'Ultimate Trend Store',
			'short'        => 'Utrend Store',
			'kind'         => 'Online store',
			'location'     => 'London',
			'order'        => 9,
			'relationship' => 'ak-owned',
			'domain'       => 'utrendstore.co.uk',
			'excerpt'      => 'An online store for designer clothing, shoes and accessories, shipping worldwide from London.',
			'facts'        => array( 'What' => 'Online store for designer clothing, shoes and accessories', 'Our part' => 'Design, development and online store' ),
			'lead'         => 'Ultimate Trend Store — an online destination for modern fashion: designer clothing, footwear and statement accessories, shipped worldwide from London.',
			'body'         => array(
				'Shoppers browse by category or by brand, with new arrivals and trending pieces up front, a secure checkout and free worldwide shipping.',
			),
		)
	);
	$projects[] = ak_seed_website(
		array(
			'key'          => 'prominent',
			'accent'       => '#2f5bd3',
			'slug'         => 'prominent-magazine',
			'title'        => 'PROMINENT Magazine',
			'short'        => 'PROMINENT',
			'kind'         => 'Magazine',
			'location'     => 'London',
			'order'        => 11,
			'relationship' => 'ak-owned',
			'disciplines'  => array( 'digital', 'visibility' ),
			'domain'       => 'prominentmagazine.co.uk',
			'excerpt'      => 'A London fashion and culture magazine: collections, designer interviews, jewellery and beauty.',
			'facts'        => array( 'What' => 'London fashion and culture magazine', 'Our part' => 'Design, development and editorial platform' ),
			'lead'         => 'PROMINENT is a London fashion and culture magazine covering runway collections, designer interviews, jewellery, beauty and the people shaping creative life.',
			'body'         => array(
				'An editorial design built on large type and full-height images, with the collections, interviews and conversations each given their own section.',
			),
		)
	);
	$projects[] = ak_seed_website(
		array(
			'key'          => 'frontier',
			'accent'       => '#2f5bd3',
			'slug'         => 'fashion-frontier',
			'title'        => 'Fashion Frontier',
			'kind'         => 'News site',
			'location'     => 'London',
			'order'        => 13,
			'relationship' => 'ak-owned',
			'disciplines'  => array( 'digital', 'visibility' ),
			'domain'       => 'fashionfrontier.uk',
			'excerpt'      => 'Independent fashion news from London: runway reports, designer profiles and culture.',
			'facts'        => array( 'What' => 'Independent fashion news site', 'Our part' => 'Design, development and editorial platform' ),
			'lead'         => 'Fashion Frontier brings UK and global fashion news, runway reports, designer profiles and culture stories — independent and London-based.',
			'body'         => array(
				'A newspaper rhythm on screen: a lead story, a running list of the latest posts and sections for news, runway and culture, set in a crisp serif.',
			),
		)
	);
	$projects[] = ak_seed_website(
		array(
			'key'          => 'coolbaba',
			'accent'       => '#3949ab',
			'slug'         => 'coolbaba',
			'title'        => 'COOL’BABA',
			'kind'         => 'Magazine',
			'order'        => 15,
			'relationship' => 'ak-owned',
			'disciplines'  => array( 'digital', 'visibility' ),
			'domain'       => 'coolbaba.in.ua',
			'excerpt'      => 'A Ukrainian online magazine about fashion, culture, beauty and lifestyle, founded by Andrii Karakushan.',
			'facts'        => array( 'What' => 'Ukrainian online magazine', 'Founded' => '2009', 'Founder' => 'Andrii Karakushan', 'Our part' => 'Design, development and editorial platform' ),
			'lead'         => 'COOL’BABA is a Ukrainian online magazine about fashion, culture, beauty, design, travel, events and people — and an official media partner of London Fashion Day.',
			'body'         => array(
				'Built like a print magazine on screen: a cover story, a “today in five minutes” digest, sections for fashion, culture, beauty and lifestyle, and an app-like menu on phones.',
			),
		)
	);
	$projects[] = ak_seed_website(
		array(
			'key'          => 'lfd',
			'accent'       => '#2f5bd3',
			'slug'         => 'london-fashion-day-website',
			'title'        => 'London Fashion Day — website',
			'short'        => 'LFD website',
			'kind'         => 'Platform website',
			'location'     => 'London',
			'order'        => 17,
			'relationship' => 'ak-owned',
			'domain'       => 'londonfashionday.co.uk',
			'excerpt'      => 'The online home of the runway platform our studio manages, with registration and tickets for every season.',
			'facts'        => array( 'What' => 'Platform website with registrations and tickets', 'Our part' => 'Design, development and content' ),
			'lead'         => 'The online home of London Fashion Day — and the system behind every season.',
			'body'         => array(
				'Season news, galleries and the running order sit alongside online registration for designers, models, media and guests.',
				'Guests choose the shows they want to see and receive tickets with a QR code for check-in; designers apply through a step-by-step form reviewed by the selection team.',
			),
		)
	);
	$projects[] = ak_seed_website(
		array(
			'key'          => 'ofd',
			'accent'       => '#d6a419',
			'slug'         => 'odessa-fashion-day-website',
			'title'        => 'Odessa Fashion Day — website',
			'short'        => 'OFD website',
			'kind'         => 'Platform website',
			'location'     => 'Odesa',
			'order'        => 18,
			'relationship' => 'ak-owned',
			'domain'       => 'ofd.org.ua',
			'excerpt'      => 'A bilingual home for Odessa Fashion Day: news, seasons and registrations in Ukrainian and English.',
			'facts'        => array( 'What' => 'Bilingual platform website', 'Languages' => 'Ukrainian and English', 'Our part' => 'Design, development and content' ),
			'lead'         => 'A bilingual home for Odessa Fashion Day: news in Ukrainian and English, season programmes and schedules, and registration for designers, models, media and guests.',
			'body'         => array(
				'The season campaign opens the site full-screen, and a legal hub covers media consent and consent for under-18 models — the paperwork every show needs, online.',
			),
		)
	);

	/* ── Seasons, shoots and films ────────────────────────────────────── */
	$projects[] = array(
		'slug'         => 'london-fashion-day-season-8',
		'title'        => 'London Fashion Day Season 8: PAST. PRESENT. NEXT.',
		'short'        => 'LFD Season 8',
		'kind'         => 'Runway season & campaign',
		'location'     => 'London',
		'year'         => '2026',
		'date'         => '2026-11-08',
		'order'        => 2,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'types'        => array( 'show', 'shoot' ),
		'disciplines'  => array( 'strategy', 'image', 'experience', 'visibility' ),
		'cover'        => 'lfd-s8-campaign-portrait.jpg',
		'eyebrow'      => '<a href="/work/?type=show">Work</a> / Shows & events',
		'excerpt'      => 'Twelve fashion voices at the historic Porchester Hall — and a campaign built around a student designer and an emerging model.',
		'facts'        => array( 'When' => '8 November 2026', 'Where' => 'Porchester Hall, Westminster', 'Our part' => 'Season management; campaign direction; production and press' ),
		'link'         => array( 'londonfashionday.co.uk', 'https://londonfashionday.co.uk/' ),
		'lead'         => 'Fashion has always been a conversation between generations. Season 8 asks what we inherit, what we create today and what we choose to pass on.',
		'body'         =>
			ak_b_p( '<strong>PAST</strong> is cultural memory, heritage and craftsmanship. <strong>PRESENT</strong> is the moment those traditions meet technology. <strong>NEXT</strong> is a new generation of names and ideas — and a question about what kind of fashion we want to leave behind. The venue makes the point: Porchester Hall opened in 1929 and has been part of London’s cultural life for almost a century.' ) .
			ak_b_h( 'The campaign' ) .
			ak_b_p( 'Rather than turning to established names, the campaign puts emerging talent at the centre: a look by Gabrielle Nitu, a student at the London College of Contemporary Arts, worn by a young Nigerian model at the start of her career on the London scene. Dinara Ostr directed the film around movement, form and transformation; Mariana Zabramna shot the stills; Andrii Karakushan and Kostiantyn Lieontiev produced.' ) .
			ak_b_h( 'The runway' ) .
			ak_b_p( 'Six runway slots from 13:00 to 18:00 on Sunday 8 November 2026, opening with students of LCCA and followed by Tiarael Fashion, STUDIO CATA, Heymers Education, Erzindjan’s, Tasha Ivy, NILDET, SHICHO, SULITUDE, Yanou Santangelo, NIKA and LEV3L UP. Sterling Law is the season’s exclusive partner, with twenty official media partners.' ) .
			ak_b_quote( 'We are not here to predict the future of fashion. We are here to make space for those who will create it.', 'Kostiantyn Lieontiev, London Fashion Day' ),
		'credits'      => array( 'Campaign film' => 'Dinara Ostr', 'Photography' => 'Mariana Zabramna', 'Look' => 'Gabrielle Nitu, London College of Contemporary Arts', 'Make-up' => '@makeup.byandzelika', 'Production' => 'Andrii Karakushan, Kostiantyn Lieontiev' ),
		'gallery'      => array( 'lfd-s8-campaign-profile.jpg', 'lfd-s8-campaign-close-up.jpg', 'lfd-s8-campaign-seated.jpg', 'lfd-s8-campaign-detail.jpg', 'lfd-s8-campaign-fringe.jpg', 'lfd-s8-campaign-movement.jpg', 'lfd-s8-campaign-standing.jpg' ),
	);

	$projects[] = array(
		'slug'         => 'london-fashion-day-season-7',
		'title'        => 'London Fashion Day Season 7: Fashion Beyond Borders',
		'short'        => 'LFD Season 7',
		'kind'         => 'Runway season & campaign film',
		'location'     => 'London',
		'year'         => '2026',
		'date'         => '2026-05-01',
		'order'        => 4,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'types'        => array( 'show', 'shoot' ),
		'disciplines'  => array( 'strategy', 'image', 'experience', 'visibility' ),
		'cover'        => 'lfd-s7-campaign-regent-street.jpg',
		'eyebrow'      => '<a href="/work/?type=show">Work</a> / Shows & events',
		'excerpt'      => 'A campaign film shot at sunrise on Regent Street and a day of shows in Hyde Park, built on one idea: fashion as a language beyond borders.',
		'facts'        => array( 'When' => '1 May 2026', 'Where' => 'St John’s Church, Hyde Park', 'Our part' => 'Season management; direction of the campaign film; show day and press' ),
		'link'         => array( 'londonfashionday.co.uk', 'https://londonfashionday.co.uk/' ),
		'lead'         => 'Season 7 treated clothing as a language that exists beyond borders, beyond spoken words and beyond fixed identity.',
		'body'         =>
			ak_b_p( 'The campaign was captured at sunrise on Regent Street: Iranian model Amirsam Mohammadzadeh, whose life and work connect him to Ukraine, in a look by the Ukrainian brand YADVIGA NETYKSHA — transparent fabrics that reveal the body while concealing the face.' ) .
			ak_b_quote( 'No matter how much we search for perfection in style, trends, or fashion directions, at the centre of it all there is always a person: their individuality, their identity.', 'Kostiantyn Lieontiev, London Fashion Day' ) .
			ak_b_h( 'The day' ) .
			ak_b_p( 'On 1 May 2026, St John’s Church, Hyde Park became a continuous sequence of presentations from 13:00: the London College of Contemporary Arts showcase, then Brera Milano, Anabelle Clothing, Amira, RICKY XTOPHA, Olimzoda Mahin, SOFI RICH and MALUSHA Bags, LEV3L UP, Atelier Pardis, Mur Mur, KEKA, Riri Couture and Gorsset. Attendance was free of charge, organised through selected show slots.' ),
		'credits'      => array( 'Campaign concept' => 'Kostiantyn Lieontiev', 'Art direction' => 'Pardis', 'Film' => 'Dinara Ostr', 'Backstage photography' => 'Natalia Wise', 'Music' => 'Eduard Nester', 'Model' => 'Amirsam Mohammadzadeh', 'Look' => 'YADVIGA NETYKSHA' ),
		'film'         => array( 'lfd-s7-campaign-film.mp4', 'lfd-s7-campaign-film.jpg', 'Season 7 campaign film, shot at sunrise on Regent Street.' ),
		'gallery'      => array( 'lfd-s7-gorsset.jpg', 'lfd-s7-campaign-sunrise.jpg', 'lfd-s7-riri-couture.jpg', 'lfd-s7-campaign-on-set.jpg', 'lfd-s7-keka.jpg', 'lfd-s7-mur-mur.jpg', 'lfd-s7-campaign-crossing.jpg', 'lfd-s7-anabelle-clothing.jpg', 'lfd-s7-backstage-lacing.jpg', 'lfd-s7-kostiantyn-lieontiev-runway.jpg' ),
	);

	$projects[] = array(
		'slug'         => 'campaign-film-westminster',
		'title'        => 'Campaign film: Westminster Bridge',
		'short'        => 'Westminster campaign film',
		'kind'         => 'Campaign film',
		'location'     => 'London',
		'year'         => '2024',
		'order'        => 6,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'types'        => array( 'shoot' ),
		'disciplines'  => array( 'image' ),
		'cover'        => 'still-lfd-westminster-17.jpg',
		'header'       => 'still-lfd-westminster-9.jpg',
		'eyebrow'      => '<a href="/work/?type=shoot">Work</a> / Photo & film',
		'excerpt'      => 'A red tulle dress on Westminster Bridge at sunrise — the campaign film for London Fashion Day’s spring 2024 season.',
		'facts'        => array( 'When' => 'Spring 2024', 'Where' => 'Westminster Bridge, London', 'Our part' => 'Direction of the campaign film' ),
		'link'         => array( 'londonfashionday.co.uk', 'https://londonfashionday.co.uk/' ),
		'lead'         => 'Big Ben, the river and the first light of the day: a campaign film for London Fashion Day’s spring 2024 season, shot on Westminster Bridge.',
		'body'         => ak_b_p( 'One look, one location, one morning. The camera tilts with the wind in the tulle and the city does the rest — the kind of film a brand can cut into a season’s worth of posts, a website header and a runway opener.' ),
		'film'         => array( 'lfd-westminster-film.mp4', 'lfd-westminster-film.jpg', 'Campaign film, Westminster Bridge, 2024.' ),
		'gallery'      => array( 'still-lfd-westminster-9.jpg', 'still-lfd-westminster-15.jpg', 'still-lfd-westminster-17.jpg' ),
	);

	$projects[] = array(
		'slug'         => 'odessa-fashion-day-season-32',
		'title'        => 'Odessa Fashion Day Season 32: ONE OF A KIND',
		'short'        => 'OFD Season 32',
		'kind'         => 'Runway season',
		'location'     => 'Odesa',
		'year'         => '2026',
		'date'         => '2026-05-16',
		'order'        => 8,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'types'        => array( 'show' ),
		'disciplines'  => array( 'strategy', 'image', 'experience' ),
		'cover'        => 'ofd-s32-runway-poppies.jpg',
		'eyebrow'      => '<a href="/work/?type=show">Work</a> / Shows & events',
		'excerpt'      => 'A season about the value of individuality, with a campaign starring Miss Ukraine Kateryna Zakharchenko.',
		'facts'        => array( 'When' => '16–17 May 2026', 'Where' => 'Luxury Gallery, Sady Peremohy, Odesa', 'Our part' => 'Season management; campaign direction; show production' ),
		'link'         => array( 'ofd.org.ua', 'https://ofd.org.ua/' ),
		'lead'         => 'ONE OF A KIND: a conversation about the person and their uniqueness at a time when the world leans towards standardisation.',
		'body'         =>
			ak_b_p( 'The official campaign starred Kateryna Zakharchenko, Miss Ukraine, in a look by designer Dima Makeiev from his Hôtel Le Mirage collection — semi-transparent textures, sculptural forms and a deep, dark palette.' ) .
			ak_b_p( 'The main day of shows took place at Luxury Gallery in the Sady Peremohy shopping centre, with design students of Ushynsky University, VEO BRAND with Mira Models, Magnat Dress, Stahova, Saiman El Sabari, BUDU, Babyface, CIAO CIAO, Mint &amp; Lonza New York, Nimblescissors and Tetyana Paliy.' ),
		'gallery'      => array( 'ofd-s32-runway-blue.jpg', 'ofd-s32-runway-embroidery.jpg', 'ofd-s32-runway-summer.jpg', 'ofd-s32-runway-silk.jpg', 'ofd-s32-runway-draped.jpg', 'ofd-s32-runway-white.jpg', 'ofd-s32-fashion-talk.jpg' ),
	);

	$projects[] = array(
		'slug'         => 'summer-weekend-by-ofd-2026',
		'title'        => 'SUMMER WEEKEND by OFD 2026',
		'short'        => 'Summer Weekend 2026',
		'kind'         => 'Summer shows',
		'location'     => 'Odesa',
		'year'         => '2026',
		'date'         => '2026-08-08',
		'order'        => 10,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'types'        => array( 'show' ),
		'disciplines'  => array( 'experience', 'image' ),
		'cover'        => 'sw26-botanical-garden.jpg',
		'eyebrow'      => '<a href="/work/?type=show">Work</a> / Shows & events',
		'excerpt'      => 'Two days of summer and cruise collections in Odesa. The second went ahead the morning after a night of missile and drone attacks.',
		'facts'        => array( 'When' => '8–9 August 2026', 'Where' => 'MarAmax Academy · Botanical Garden of Odesa University', 'Our part' => 'Management of the summer season of shows' ),
		'link'         => array( 'ofd.org.ua', 'https://ofd.org.ua/' ),
		'lead'         => 'The summer edition of Odessa Fashion Day, first held in 2015 — and, this year, a decision made together to carry on.',
		'body'         =>
			ak_b_p( 'SUMMER WEEKEND by OFD opened on 8 August at MarAmax Academy with a presentation by DeClassic, the label of designer Anastasiia Mykytyn. During the night of 8 to 9 August, Odesa came under a large-scale combined missile and drone attack. The team seriously considered cancelling the second day; the decision to continue was made collectively — and designers, models, make-up artists, stylists, photographers and partners all came.' ) .
			ak_b_p( 'On 9 August the shows went ahead in the Botanical Garden of Odesa I. I. Mechnikov National University, with summer and cruise collections from PANACEA, LUMÉA, Yankovska Helen &amp; Lady Forever, Soul Swim, PODOLSKA, Nimble Scissors and KEOSMY by yesundress.' ) .
			ak_b_quote( 'We do not romanticise the conditions in which we work. But we do not want to allow those conditions to take away Ukrainian designers’ ability to create, develop and be seen.', 'Kostiantyn Lieontiev, Odessa Fashion Day' ),
		'gallery'      => array( 'sw26-denim.jpg', 'sw26-linen.jpg', 'sw26-outerwear.jpg', 'sw26-cruise.jpg', 'sw26-black.jpg', 'sw26-tailoring.jpg', 'sw26-maramax-academy.jpg' ),
	);

	$projects[] = array(
		'slug'         => 'london-fashion-day-season-6',
		'title'        => 'London Fashion Day Season 6: The Art of Being',
		'short'        => 'LFD Season 6',
		'kind'         => 'Runway season',
		'location'     => 'London',
		'year'         => '2025',
		'date'         => '2025-11-01',
		'order'        => 12,
		'featured'     => false,
		'relationship' => 'ak-owned',
		'types'        => array( 'show' ),
		'disciplines'  => array( 'strategy', 'experience', 'visibility' ),
		'cover'        => 'lfd-s6-lenie-boya-malusha.jpg',
		'header_video' => 'lfd-s6-runway-loop.mp4',
		'header_poster' => 'lfd-s6-runway-loop.jpg',
		'eyebrow'      => '<a href="/work/?type=show">Work</a> / Shows & events',
		'excerpt'      => 'Authenticity as the new luxury: a record number of guests, a pop-up space and an expanded line-up in Hyde Park.',
		'facts'        => array( 'When' => '1 November 2025', 'Where' => 'St John’s Church, Hyde Park', 'Our part' => 'Season management; casting, show day and press' ),
		'link'         => array( 'londonfashionday.co.uk', 'https://londonfashionday.co.uk/' ),
		'lead'         => 'Under the theme The Art of Being, Season 6 argued that authenticity is the new luxury — in the collections, and in the casting, production and storytelling around them.',
		'body'         =>
			ak_b_p( 'The sixth season took place on 1 November 2025 inside St John’s Church in central London, with a record number of guests, a Pop-Up Corner Space and an expanded designer line-up.' ) .
			ak_b_quote( 'In 2025, being yourself is a creative statement.', 'Kostiantyn Lieontiev, London Fashion Day' ) .
			ak_b_p( 'The London College of Contemporary Arts opened the day with five emerging talents, followed by Relique London, VELGUD presented by Global Fashion Net, TIDE CHEN, Sara K Couture, LENIE BOYA × MALUSHA, Maison Sophy G, KochHouse, Mogardi, CHARS, FRENCHEYE and The Moodi Studio.' ),
		'gallery'      => array( 'lfd-s6-lcca-showcase.jpg', 'lfd-s6-sara-k-couture.jpg', 'lfd-s6-mogardi.jpg', 'lfd-s6-relique-london.jpg', 'lfd-s6-maison-sophy-g.jpg', 'lfd-s6-kochhouse.jpg', 'lfd-s6-kostiantyn-lieontiev-backstage.jpg' ),
	);

	$projects[] = array(
		'slug'         => 'odessa-fashion-day-season-31',
		'title'        => 'Odessa Fashion Day Season 31: UNYIELDING',
		'short'        => 'OFD Season 31',
		'kind'         => 'Runway season & campaign film',
		'location'     => 'Odesa',
		'year'         => '2025',
		'date'         => '2025-11-14',
		'order'        => 14,
		'featured'     => false,
		'relationship' => 'ak-owned',
		'types'        => array( 'show', 'shoot' ),
		'disciplines'  => array( 'image', 'experience' ),
		'cover'        => 'ofd-s31-londonska-hotel.jpg',
		'eyebrow'      => '<a href="/work/?type=show">Work</a> / Shows & events',
		'excerpt'      => 'Two days, two landmark venues and one theme: resilience, strength and creative movement forward.',
		'facts'        => array( 'When' => '14–15 November 2025', 'Where' => 'Sady Peremohy · Londonska Hotel, Odesa', 'Our part' => 'Season management; direction of the campaign film; two days of shows' ),
		'link'         => array( 'ofd.org.ua', 'https://ofd.org.ua/' ),
		'lead'         => 'UNYIELDING: resilience, strength and creative movement forward despite the challenges of today.',
		'body'         =>
			ak_b_p( 'Day one opened in the Luxury Gallery of the Sady Peremohy shopping mall with Effetto, Anna Dobrydneva by Sadnytskyy, Mur Mur and Maison Sophy G. Day two moved to the Londonska Hotel, one of Odesa’s landmark buildings, with thirteen collections — from upcycling and avant-garde forms to classic lines.' ) .
			ak_b_p( 'The campaign film set a sculptural navy gown against coral light.' ),
		'film'         => array( 'ofd-s31-campaign-film.mp4', 'ofd-s31-campaign-film.jpg', 'Season 31 campaign film.' ),
		'gallery'      => array( 'still-ofd-s31-campaign-21.jpg', 'ofd-s31-luxury-gallery.jpg', 'ofd-s31-londonska-red.jpg', 'ofd-s31-luxury-gallery-2.jpg', 'ofd-s31-londonska-embroidery.jpg', 'ofd-s31-londonska-patchwork.jpg', 'ofd-s31-luxury-gallery-3.jpg', 'ofd-s31-londonska-lace.jpg' ),
	);

	$projects[] = array(
		'slug'         => 'london-fashion-day-season-5',
		'title'        => 'London Fashion Day Season 5: HUMAN',
		'short'        => 'LFD Season 5',
		'kind'         => 'Runway season',
		'location'     => 'London',
		'year'         => '2025',
		'date'         => '2025-04-26',
		'order'        => 16,
		'featured'     => false,
		'relationship' => 'ak-owned',
		'types'        => array( 'show' ),
		'disciplines'  => array( 'strategy', 'image', 'experience' ),
		'cover'        => 'lfd-s5-elen-ost.jpg',
		'eyebrow'      => '<a href="/work/?type=show">Work</a> / Shows & events',
		'excerpt'      => 'A season that put the person, not the standard, at the front of fashion — with a campaign made with KEKA.',
		'facts'        => array( 'When' => '26 April 2025', 'Where' => 'St John’s Church, Hyde Park', 'Our part' => 'Season management; campaign with KEKA; show day' ),
		'link'         => array( 'londonfashionday.co.uk', 'https://londonfashionday.co.uk/' ),
		'lead'         => 'HUMAN — a declaration of freedom, self-expression and authenticity that brings the person, not the standard, to the front of fashion.',
		'body'         =>
			ak_b_p( 'The official campaign was created with the Ukrainian label KEKA, whose philosophy goes beyond gender, age and traditional norms: Zain Shan as the face of the season, direction and video by Dinara Ostr, photography by Nicolo Urbani and production by Kostiantyn Lieontiev.' ) .
			ak_b_quote( 'This season is not about the image — it’s about the person.', 'Kostiantyn Lieontiev, London Fashion Day' ) .
			ak_b_p( 'On the runway: Lenie Boya, HOME NEST &amp; MALUSHA Bag Design Studio, the London College of Contemporary Arts showcase, Panache by Elvira, Fashion Paris Academy, Gorsset, KEKA by Novikov and ELEN OST, among others.' ),
		'gallery'      => array( 'lfd-s5-keka-by-novikov.jpg', 'lfd-s5-gorsset.jpg', 'lfd-s5-keka-by-novikov-2.jpg', 'lfd-s5-panache-by-elvira.jpg', 'lfd-s5-elen-ost-2.jpg' ),
	);

	/* ── Our own platforms and brand ──────────────────────────────────── */
	$projects[] = array(
		'slug'          => 'london-fashion-day',
		'title'         => 'London Fashion Day',
		'short'         => 'London Fashion Day',
		'kind'          => 'Runway platform · London',
		'location'      => 'London',
		'year'          => 'Since 2023',
		'order'         => 19,
		'featured'      => false,
		'relationship'  => 'ak-owned',
		'types'         => array( 'platform' ),
		'disciplines'   => array( 'strategy', 'identity', 'image', 'experience', 'visibility' ),
		'cover'         => 'still-lfd-westminster-9.jpg',
		'header_video'  => 'lfd-s6-runway-loop.mp4',
		'header_poster' => 'lfd-s6-runway-loop.jpg',
		'eyebrow'       => '<a href="/work/?type=platform">Work</a> / Platforms',
		'excerpt'       => 'An independent international runway for emerging and independent designers — eight seasons since 2023, managed by the studio.',
		'facts'         => array( 'Since' => '2023', 'Seasons' => '8', 'Home' => 'London' ),
		'link'          => array( 'londonfashionday.co.uk', 'https://londonfashionday.co.uk/' ),
		'lead'          => 'London Fashion Day began with a single show: in 2022 the team presented Ukrainian designers at the International Women’s Forum in London, and the response made the case for a permanent platform.',
		'body'          =>
			ak_b_p( 'Season 1 followed on 22 April 2023: six collections, four of them by Ukrainian designers. Since then the platform has grown season by season — designers from Europe, Asia, Africa and the UK, more than 100 media outlets, a home at St John’s Church, Hyde Park, and Season 8 at the historic Porchester Hall in Westminster.' ) .
			ak_b_h( 'What the studio does for the platform' ) .
			ak_b_list(
				array(
					'Season concepts and campaign themes — HUMAN, The Art of Being, Fashion Beyond Borders, PAST. PRESENT. NEXT.',
					'Campaign shoots and films, made with long-standing collaborators such as director Dinara Ostr',
					'Every show day: running order, backstage, casting, beauty and technical partners',
					'The website, designer applications, guest registration and tickets',
					'Partnerships with the London College of Contemporary Arts, Show Empire and twenty official media partners',
				)
			) .
			ak_b_quote( 'Sometimes, all a young designer needs is someone willing to open the door.', 'Kostiantyn Lieontiev, London Fashion Day' ),
		'gallery'       => array( 'lfd-s7-kostiantyn-lieontiev-runway.jpg', 'lfd-s6-lcca-showcase.jpg', 'lfd-s5-elen-ost.jpg', 'still-lfd-westminster-17.jpg', 'lfd-s7-riri-couture.jpg', 'lfd-s6-kostiantyn-lieontiev-backstage.jpg' ),
	);

	$projects[] = array(
		'slug'          => 'odessa-fashion-day',
		'title'         => 'Odessa Fashion Day',
		'short'         => 'Odessa Fashion Day',
		'kind'          => 'Runway platform · Odesa',
		'location'      => 'Odesa',
		'year'          => 'Since 2010',
		'order'         => 20,
		'featured'      => false,
		'relationship'  => 'ak-owned',
		'types'         => array( 'platform' ),
		'disciplines'   => array( 'strategy', 'identity', 'image', 'experience', 'visibility' ),
		'cover'         => 'still-ofd-s31-campaign-13.jpg',
		'header_video'  => 'ofd-s31-campaign-loop.mp4',
		'header_poster' => 'ofd-s31-campaign-loop.jpg',
		'eyebrow'       => '<a href="/work/?type=platform">Work</a> / Platforms',
		'excerpt'       => 'A runway for Ukrainian designers since 2010 — 33 seasons in Odesa and a summer edition since 2015, managed by the studio.',
		'facts'         => array( 'Since' => '2010', 'Seasons' => '33', 'Home' => 'Odesa' ),
		'link'          => array( 'ofd.org.ua', 'https://ofd.org.ua/' ),
		'lead'          => 'Odessa Fashion Day is a cultural initiative that supports and promotes Ukrainian fashion brands, in Odesa and across Ukraine.',
		'body'          =>
			ak_b_p( 'Each season brings together designers, bloggers, retail, journalists, buyers and the public. Around the shows sit Fashion Shopping Place — a shopping space for Ukrainian labels — master classes and talks. Admission is free, by designer invitation or registration. In 2016 OFD signed a memorandum of strategic partnership with Ukrainian Fashion Week; from 2020 it has been a partner of the national channel Fashion TV Ukraine.' ) .
			ak_b_h( 'Through the war' ) .
			ak_b_p( 'The platform has kept working through the full-scale war: Season 30 marked fifteen years, Season 31 took over two of the city’s landmark venues, Season 32 followed in May 2026, Season 33 takes place on 6–8 November 2026, and SUMMER WEEKEND by OFD — held since 2015 — went ahead the day after a large-scale attack on Odesa.' ) .
			ak_b_quote( 'OFD is a platform for ideas, meanings and people who are shaping the future of fashion right now.', 'Kostiantyn Lieontiev, Odessa Fashion Day' ),
		'gallery'       => array( 'ofd-s32-runway-poppies.jpg', 'ofd-s31-londonska-hotel.jpg', 'sw26-botanical-garden.jpg', 'ofd-s32-runway-embroidery.jpg', 'ofd-s31-luxury-gallery.jpg', 'ofd-s32-fashion-talk.jpg' ),
	);

	$projects[] = array(
		'slug'         => 'keka',
		'title'        => 'KEKA',
		'short'        => 'KEKA',
		'kind'         => 'Fashion label',
		'location'     => 'London',
		'year'         => '',
		'order'        => 21,
		'featured'     => true,
		'relationship' => 'ak-owned',
		'types'        => array( 'brand' ),
		'disciplines'  => array( 'strategy', 'identity', 'image', 'experience' ),
		'cover'        => 'lfd-s7-keka.jpg',
		'eyebrow'      => '<a href="/work/?type=brand">Work</a> / Brands',
		'excerpt'      => 'A label that ignores gender, age and convention — on the runway in London and Milan, and being developed for the international market.',
		'facts'        => array( 'Shown at' => 'London Fashion Day; Fashion For Freedom, Milan Fashion Week 2025', 'Our part' => 'Direction of positioning, campaigns and runway; website design and development' ),
		'link'         => array( 'keka.design', 'https://keka.design/' ),
		'lead'         => 'KEKA is a fashion label with a philosophy that goes beyond gender, age and traditional norms.',
		'body'         =>
			ak_b_p( 'It has shown at London Fashion Day across several seasons and created the official campaign for Season 5, HUMAN. In September 2025 KEKA joined Fashion For Freedom during Milan Fashion Week, reimagining Tudor echoes as a contemporary cultural and political statement.' ) .
			ak_b_p( 'The label is now being developed for the international market — with the same methods the studio brings to clients: positioning, campaigns, runway, press and a <a href="/work/keka-website/">website</a> built for it.' ),
		'gallery'      => array( 'lfd-s5-keka-by-novikov.jpg', 'lfd-s5-keka-by-novikov-2.jpg', 'web-keka-mobile.jpg' ),
	);

	return (array) apply_filters( 'ak_brand_seed_projects', $projects );
}
