<?php
/**
 * Launch pages, built entirely from blocks so every word, film, image and
 * button can be changed in the page editor. The copy leads with what the
 * studio does for people and businesses; the studio's own platforms are the
 * proof, not the subject.
 *
 * Keys: title, slug, parent (slug), order, excerpt (search description),
 * short (short title for lists), content (block markup).
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * The closing call to action used by most pages (a synced pattern).
 */
function ak_seed_cta(): string {
	return ak_b_pattern( 'cta' );
}

/**
 * A service page's closing call to action, with the service preselected in the brief.
 */
function ak_seed_service_cta( string $slug, string $heading ): string {
	return ak_b_styled_group(
		ak_b_display( $heading ) .
		ak_b_lead( 'Tell us what you are working on. You will receive a written proposal — scope, timeline and fee — before any work begins.' ) .
		ak_b_buttons( array( array( 'Start a project', '/contact/?need=' . $slug ) ) ),
		'cta'
	);
}

/**
 * Founder card.
 *
 * @param string[] $paras Paragraphs.
 */
function ak_seed_founder( string $letter, string $name, string $role, array $paras ): string {
	$inner = ak_b_p( $letter, 'is-style-plate' ) . ak_b_h( $name, 3 ) . ak_b_eyebrow( $role );
	foreach ( $paras as $p ) {
		$inner .= ak_b_p( $p );
	}
	return ak_b_styled_group( $inner, 'founder' );
}

/**
 * A work section for a service page.
 *
 * @param array<string,mixed> $work Attributes for ak/work.
 */
function ak_seed_service_work( string $heading, array $work ): string {
	return ak_b_section(
		ak_b_eyebrow( 'Our work' ) .
		ak_b_display( $heading ) .
		ak_b_ak( 'work', $work )
	);
}

/**
 * @return array<int,array<string,mixed>>
 */
function ak_brand_seed_pages(): array {
	$mail    = '[ak_studio_email]';
	$updated = '2 November 2026';
	$img     = static fn( string $file ): array => ak_b_media( $file, 'full' );
	$header  = static function ( array $a, string $media = '' ) use ( $img ): string {
		if ( $media ) {
			$m             = $img( $media );
			$a['mediaId']  = $m['id'];
			$a['mediaUrl'] = $m['url'];
		}
		return ak_b_ak( 'page-header', $a );
	};
	$reel = static fn( string $file, string $label, string $kind, string $focus = '' ): array => array(
		'video'   => ak_b_theme_asset( "media/{$file}.mp4" ),
		'videoSm' => ak_b_theme_asset( "media/{$file}-sm.mp4" ),
		'poster'  => ak_b_theme_asset( "media/{$file}.jpg" ),
		'label'   => $label,
		'kind'    => $kind,
		'focus'   => $focus,
	);
	$s8       = $img( 'lfd-s8-campaign-profile.jpg' );
	$event_s8 = array(
		'eyebrowUpcoming' => 'For designers',
		'eyebrowPast'     => 'Latest season',
		'label'           => 'London Fashion Day · Season 8',
		'title'           => 'PAST. PRESENT. NEXT.',
		'date'            => '2026-11-08',
		'venue'           => 'Porchester Hall, Westminster',
		'note'            => 'London Fashion Day, the runway platform our studio manages, returns with twelve fashion voices. Designers who want to show at a future season can start with us.',
		'imageId'         => $s8['id'],
		'imageUrl'        => $s8['url'],
		'primaryLabel'    => 'Register as a guest',
		'primaryUrl'      => 'https://londonfashionday.co.uk/guest-registration/',
		'pastLabel'       => 'See the season',
		'pastUrl'         => '/work/london-fashion-day-season-8/',
		'secondaryLabel'  => 'Show at a future season',
		'secondaryUrl'    => '/show-in-london/',
	);

	$pages = array();

	/* ── Home ─────────────────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Home',
		'slug'    => 'home',
		'order'   => 0,
		'excerpt' => 'An independent London studio that helps people and businesses build brands that get noticed: strategy and identity, websites and online stores, photo and film, shows and events, PR and promotion.',
		'content' =>
			ak_b_ak(
				'hero',
				array(
					'slate'          => 'London — Brand development studio',
					'row1'           => 'From the initial idea',
					'row2'           => 'To international <em>presence</em>',
					'row3'           => 'Brands · Websites · Shoots · Shows',
					'lede'           => 'We help people and businesses build brands that get noticed — strategy and identity, websites and online stores, photo and film, launches, shows, PR and promotion. One London studio, one team, from the first idea to international presence.',
					'primaryLabel'   => 'Start a project',
					'primaryUrl'     => '#brief-form',
					'secondaryLabel' => 'See our work',
					'secondaryUrl'   => '/work/',
					'clips'          => array(
						$reel( 'lfd-s7-campaign', 'Photo & film', 'London Fashion Day · Season 7 campaign', '55% 35%' ),
						$reel( 'lfd-s7-runway', 'Shows & events', 'London Fashion Day · Season 7', '50% 45%' ),
						$reel( 'ofd-s31-campaign', 'Photo & film', 'Odessa Fashion Day · Season 31 campaign', '30% 30%' ),
						$reel( 'lfd-westminster', 'Photo & film', 'Campaign film · Westminster Bridge', '55% 30%' ),
					),
					'stats'          => array(
						array( 'value' => '15', 'suffix' => '+', 'label' => 'Years of experience' ),
						array( 'value' => '10', 'suffix' => '', 'label' => 'Websites in our portfolio' ),
						array( 'value' => '40', 'suffix' => '+', 'label' => 'Runway seasons in London and Odesa' ),
						array( 'value' => 'A+K', 'suffix' => '', 'label' => 'Founders, one letter each' ),
					),
				)
			) .
			ak_b_ak( 'marquee', array( 'items' => array( 'Brand strategy', 'Identity', 'Websites', 'Online stores', 'Photo & film', 'Fashion shows', 'Launches & events', 'PR', 'Paid media' ) ) ) .
			ak_b_styled_group(
				ak_b_eyebrow( 'What we do' ) .
				ak_b_display( 'You bring the idea. <em>We make it seen.</em>', 2, 'display-xl' ) .
				ak_b_p( 'A fashion label, a beauty salon, an interiors company, an online shop, a magazine, a personal brand — whatever you are building, it needs the same things to succeed: a clear position, an identity people remember, a website that sells, pictures worth sharing, a launch, and a reason for the press to care.' ) .
				ak_b_p( 'We do all of it under one roof. Two founders, one letter each — Andrii Karakushan and Kostiantyn Lieontiev — with the crews, venues and media partners we have built over fifteen years of producing fashion events and campaigns.' ) .
				ak_b_p( '<a href="/about/">Meet the studio</a>' ),
				'statement'
			) .
			ak_b_section(
				ak_b_eyebrow( 'Services' ) .
				ak_b_display( 'Six services. <em>One team.</em>' ) .
				ak_b_ak( 'services' ) .
				ak_b_buttons( array( array( 'All services', '/services/', 'is-style-outline' ) ) )
			) .
			ak_b_section(
				ak_b_eyebrow( 'Websites' ) .
				ak_b_display( 'Websites we have <em>designed and built.</em>' ) .
				ak_b_lead( 'Online stores, magazines, brand sites and booking sites — fast on a phone, clear on the first screen and easy to update yourself.' ) .
				ak_b_ak( 'work', array( 'types' => 'website', 'count' => 6, 'columns' => 3, 'moreLabel' => 'All websites', 'moreUrl' => '/work/?type=website' ) )
			) .
			ak_b_section(
				ak_b_eyebrow( 'Selected work' ) .
				ak_b_display( 'Shoots, shows <em>and brands.</em>' ) .
				ak_b_ak( 'work', array( 'layout' => 'rail', 'types' => 'shoot,show,brand', 'featured' => true, 'count' => 8, 'moreLabel' => 'All *work*', 'moreUrl' => '/work/' ) )
			) .
			ak_b_section(
				ak_b_eyebrow( 'How we work' ) .
				ak_b_display( 'From brief <em>to presence.</em>' ) .
				ak_b_list(
					array(
						'<strong>Brief</strong> Tell us about the brand, the date you are working towards and what you need. We read every brief ourselves.',
						'<strong>Proposal</strong> A written proposal: scope, team, timeline and fee — agreed before any work begins.',
						'<strong>Production</strong> Strategy, design, shoots, shows and builds in one plan, with one team accountable for all of it.',
						'<strong>Presence</strong> Launch, press and promotion — then the numbers, so you know what worked and what to do next.',
					),
					true,
					'is-style-steps-row'
				)
			) .
			ak_b_section(
				ak_b_eyebrow( 'Platforms we manage' ) .
				ak_b_display( 'We practise <em>what we sell.</em>' ) .
				ak_b_lead( 'The studio manages two runway platforms — London Fashion Day and Odessa Fashion Day: 8 seasons in London, 33 in Odesa and SUMMER WEEKEND by OFD since 2015. Every service we offer, we use there first.' ) .
				ak_b_ak( 'work', array( 'layout' => 'feature', 'types' => 'platform', 'count' => 2 ) )
			) .
			ak_b_ak( 'event', $event_s8 ) .
			ak_b_section(
				ak_b_eyebrow( 'The founders' ) .
				ak_b_display( 'One letter <em>each.</em>' ) .
				ak_b_columns(
					array(
						ak_seed_founder( 'A', 'Andrii Karakushan', 'Co-founder — digital, identity and media', array( 'Brand development, websites and visual communication. Founder of the online magazine Cool’baba.' ) ),
						ak_seed_founder( 'K', 'Kostiantyn Lieontiev', 'Co-founder — fashion production and brand strategy', array( 'Fashion producer and brand strategist. Heads London Fashion Day and Odessa Fashion Day.' ) ),
					)
				) .
				ak_b_buttons( array( array( 'About the studio', '/about/', 'is-style-outline' ) ) )
			) .
			ak_b_section(
				ak_b_eyebrow( 'Journal' ) .
				ak_b_display( 'Notes from the studio <em>and the runway.</em>' ) .
				ak_b_ak( 'journal', array( 'count' => 3 ) ) .
				ak_b_buttons( array( array( 'All entries', '/journal/', 'is-style-outline' ) ) )
			) .
			ak_b_section(
				ak_b_columns(
					array(
						ak_b_eyebrow( 'Start a project' ) .
						ak_b_display( 'Tell us what <em>you’re building.</em>' ) .
						ak_b_lead( 'A few lines are enough to start. We read every brief ourselves and reply with questions, a time to talk, or a proposal.' ) .
						ak_b_p( 'Or write to ' . $mail ),
						ak_b_ak( 'brief', array( 'compact' => true, 'source' => 'Home page' ) ),
					),
					'split'
				)
			),
	);

	/* ── Services hub ─────────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Services',
		'slug'    => 'services',
		'order'   => 1,
		'excerpt' => 'Six services, one team: brand strategy, identity, websites and online stores, photo and film, fashion shows and events, PR and promotion.',
		'content' =>
			$header(
				array(
					'eyebrow' => 'Services',
					'heading' => 'Everything your brand needs <em>to be seen.</em>',
					'lede'    => 'Six services, one team. Take a single service, or hand us the whole journey from first idea to international presence.',
				)
			) .
			ak_b_section( ak_b_ak( 'services', array( 'excludeCurrent' => false ) ) ) .
			ak_b_section(
				ak_b_h( 'One brief, one team' ) .
				ak_b_p( 'Most growing businesses end up managing five suppliers: a strategist, a designer, a web developer, a photographer and an agency for ads. Each does good work; none of it adds up. We run every discipline in-house and in sequence, so the positioning decided in week one is the one that appears on the website, in the campaign and in the ad.' ) .
				ak_b_p( 'It also means one person accountable for the result. Every project is led by one of the founders, with the same crews, venues and media partners we rely on for the platforms we manage.' ) .
				ak_b_h( 'Ways to work with us' ) .
				ak_b_list(
					array(
						'<strong>A single project.</strong> A brand identity, a website, a campaign shoot, a launch — clear scope, clear deadline.',
						'<strong>A full launch.</strong> Strategy, identity, website, campaign and press, planned backwards from one date.',
						'<strong>Ongoing presence.</strong> PR, social and paid media month by month, reported in plain numbers.',
					),
					false,
					'is-style-cards'
				)
			) .
			ak_b_section(
				ak_b_eyebrow( 'Our work' ) .
				ak_b_display( 'See it <em>in practice.</em>' ) .
				ak_b_buttons(
					array(
						array( 'Websites', '/work/?type=website', 'is-style-outline' ),
						array( 'Photo & film', '/work/?type=shoot', 'is-style-outline' ),
						array( 'Shows & events', '/work/?type=show', 'is-style-outline' ),
						array( 'Brands', '/work/?type=brand', 'is-style-outline' ),
					)
				)
			) .
			ak_b_section(
				ak_b_faq(
					array(
						array( 'Who do you work with?', 'Fashion labels and designers, beauty and lifestyle businesses, shops, salons, studios, magazines and founders building a personal brand. If your business needs to look right and be seen, we can help.' ),
						array( 'What does it cost?', 'It depends on the scope, so prices are given on request. Send a brief and you will receive a written proposal with the fee before any work begins.' ),
						array( 'Can you take over a website we already have?', 'Yes. We will look at what you have, tell you honestly what is worth keeping, and either improve it or rebuild it.' ),
						array( 'Where do you work?', 'The studio is in London. We have managed shows and directed campaigns in London, Odesa, Paris, Dubai, Vienna and Milan, and we work with clients wherever they are.' ),
						array( 'How soon can you start?', 'It depends on the scope. If you are working towards a fixed date — a launch, an opening, a show — put it in the brief and we will tell you honestly whether it can be done well in the time.' ),
					)
				)
			) .
			ak_seed_cta(),
	);

	/* ── Six service pages ────────────────────────────────────────────── */
	$service = static function ( array $s ) use ( $header ): array {
		return array(
			'title'   => $s['title'],
			'short'   => $s['short'],
			'slug'    => $s['slug'],
			'parent'  => 'services',
			'order'   => $s['order'],
			'excerpt' => $s['lede'],
			'content' =>
				$header(
					array(
						'eyebrow'  => $s['eyebrow'],
						'heading'  => $s['heading'],
						'lede'     => $s['lede'],
						'question' => $s['question'],
					),
					$s['image']
				) .
				ak_b_section(
					ak_b_h( 'What we do' ) . implode( '', array_map( 'ak_b_p', $s['what'] ) ) .
					ak_b_h( 'What you get' ) . ak_b_list( $s['get'] ) .
					ak_b_h( 'How it runs' ) . ak_b_steps( $s['steps'] )
				) .
				ak_seed_service_work( $s['work_heading'], $s['work'] ) .
				ak_b_section( ak_b_faq( $s['faq'] ) ) .
				ak_seed_service_cta( $s['slug'], $s['cta'] ),
		);
	};

	$pages[] = $service(
		array(
			'title'        => 'Brand strategy',
			'short'        => 'Brand strategy',
			'slug'         => 'brand-strategy',
			'order'        => 1,
			'eyebrow'      => 'Service 01 — Strategy',
			'heading'      => 'Know who you are <em>before you design anything.</em>',
			'lede'         => 'The work before the logo: what your brand is, who it is for, and why anyone should choose it. For new businesses, relaunches and founders building a personal brand.',
			'question'     => 'Who are we?',
			'image'        => 'lfd-s7-campaign-on-set.jpg',
			'what'         => array(
				'Strategy is where most businesses save money and where most of them lose it. A brand without a position looks like everyone else on the shelf, the feed or the high street; a founder without a clear story says something different in every conversation. We start with the questions a customer, a buyer and a journalist will ask — and we don’t move to design until the answers hold.',
				'Our strategy is written by people who have had to sell their own tickets, fill front rows and win over press for the runway platforms we manage. It ends in decisions you can act on, not a presentation that sits in a folder.',
			),
			'get'          => array(
				'Brand concept and positioning',
				'Audience, market and competitor map',
				'Brand philosophy, values and tone of voice',
				'Relaunch or repositioning roadmap',
				'Personal brand strategy for founders and creatives',
				'Identity foundations — the brief every designer, photographer and developer works from',
				'A twelve-month plan for growth, in order of priority',
			),
			'steps'        => array(
				array( 'Discovery.', 'Conversations with you and, where useful, with customers, buyers or stockists.' ),
				array( 'Routes.', 'Two or three strategic directions, argued on paper, with the trade-offs spelt out.' ),
				array( 'Decision.', 'One direction, written up as a short brand book you can hand to anyone who works on the brand.' ),
			),
			'work_heading' => 'Brands we <em>have shaped.</em>',
			'work'         => array( 'types' => 'brand,platform', 'count' => 3, 'columns' => 3, 'moreLabel' => 'All work', 'moreUrl' => '/work/' ),
			'faq'          => array(
				array( 'Do I need strategy if I already have a logo?', 'Often, yes. A logo is the last answer in a long chain. If the business isn’t selling, being remembered or being written about, the problem usually sits earlier in that chain — and a new logo won’t fix it.' ),
				array( 'Do you work with individuals?', 'Yes. Personal brand strategy — positioning, communication and visual direction — is part of the practice, for founders, designers and creative professionals.' ),
			),
			'cta'          => 'Start with <em>the strategy.</em>',
		)
	);

	$pages[] = $service(
		array(
			'title'        => 'Brand identity & creative direction',
			'short'        => 'Identity & creative direction',
			'slug'         => 'brand-identity',
			'order'        => 2,
			'eyebrow'      => 'Service 02 — Identity',
			'heading'      => 'An identity that works <em>at every size.</em>',
			'lede'         => 'How your brand looks and sounds — designed to hold up on a sign, a label, a website and a six-second ad.',
			'question'     => 'What do we look like?',
			'image'        => 'lfd-s8-campaign-close-up.jpg',
			'what'         => array(
				'An identity has to survive the real world: a phone screen, a shop window, a business card, a stitched label, a dark venue. We design for those moments first and the presentation board last, so the system works the first time it meets an Instagram crop.',
				'Creative direction keeps it honest after launch. The same eye that designs the identity art-directs the website, the shoot and the launch — which is why the brand still looks like itself a year later.',
			),
			'get'          => array(
				'Logo and identity system',
				'Visual direction: colour, typography, image style',
				'Brand guidelines your team and partners can actually use',
				'Signage, labels, packaging and print designed alongside the digital assets',
				'Visual storytelling for campaigns and social',
				'Creative direction across shoots, websites and events',
			),
			'steps'        => array(
				array( 'Brief.', 'We start from the strategy — or write a short one with you if it doesn’t exist yet.' ),
				array( 'Routes in real use.', 'Two or three design directions shown where they will live: on a sign, a post, a website, a label.' ),
				array( 'System.', 'One route refined into a full identity, with every file and the guidelines to use it.' ),
			),
			'work_heading' => 'Identities <em>in use.</em>',
			'work'         => array( 'disciplines' => 'identity', 'count' => 3, 'columns' => 3, 'moreLabel' => 'All work', 'moreUrl' => '/work/' ),
			'faq'          => array(
				array( 'Can you refresh rather than redesign?', 'Yes — and it is often the better choice. We will tell you what is worth keeping and what is holding the brand back.' ),
				array( 'Do you design packaging and print?', 'Yes, as part of the identity: signage, labels, packaging and print are designed with the digital assets so everything matches.' ),
			),
			'cta'          => 'Give your brand <em>a face.</em>',
		)
	);

	$pages[] = $service(
		array(
			'title'        => 'Website development & digital',
			'short'        => 'Websites & digital',
			'slug'         => 'website-development',
			'order'        => 3,
			'eyebrow'      => 'Service 03 — Websites',
			'heading'      => 'Websites that turn attention <em>into orders.</em>',
			'lede'         => 'Websites, online stores and booking sites — fast on a phone, clear on the first screen and easy for you to update.',
			'question'     => 'Who builds the site?',
			'image'        => 'web-wall.jpg',
			'what'         => array(
				'A recommendation, an advert or a press feature sends people somewhere. Too often that somewhere is a slow template that looks like every competitor and answers none of the questions a first-time visitor has. We design and build sites that load fast, say what the business is on the first screen and make the next step obvious — buy, book, call or enquire.',
				'We have built online stores for fashion labels, a website for a London interiors company, magazines in English and Ukrainian, platform sites with registrations and tickets, and a new site for a Marylebone salon. We build on WordPress and WooCommerce, so you own the platform and can edit everything yourself.',
			),
			'get'          => array(
				'Website design and development',
				'Online stores with WooCommerce',
				'Booking, enquiry and registration forms',
				'Multilingual sites',
				'Social media presence and content production',
				'Search foundations, speed and accessibility checks',
				'Training, so your team can update everything',
			),
			'steps'        => array(
				array( 'Structure.', 'Pages, products and content planned around what visitors need to decide.' ),
				array( 'Design.', 'Designed for the phone first and tested on real devices.' ),
				array( 'Build.', 'Development, content entry, speed and accessibility checks.' ),
				array( 'Launch.', 'Go-live, training and support while the first campaign runs.' ),
			),
			'work_heading' => 'Websites we have <em>designed and built.</em>',
			'work'         => array( 'types' => 'website', 'count' => 12, 'columns' => 3, 'moreLabel' => 'All websites', 'moreUrl' => '/work/?type=website' ),
			'faq'          => array(
				array( 'Which platform do you use?', 'Usually WordPress, with WooCommerce for online stores — open-source, widely supported and yours to keep. If you are already on another platform, we will tell you honestly whether it is worth moving.' ),
				array( 'Can you add booking to our site?', 'Yes. We connect the booking system you already use — for example Fresha for salons — or build enquiry and registration forms that arrive straight in your inbox.' ),
				array( 'Can you run our social media too?', 'Yes. Content production and social presence are part of the digital service, and we can run them alongside PR and paid promotion.' ),
			),
			'cta'          => 'Let’s build <em>your website.</em>',
		)
	);

	$pages[] = $service(
		array(
			'title'        => 'Photo & film production',
			'short'        => 'Photo & film',
			'slug'         => 'photo-video-production',
			'order'        => 4,
			'eyebrow'      => 'Service 04 — Photo & film',
			'heading'      => 'Campaigns and films, <em>produced end to end.</em>',
			'lede'         => 'Campaigns, lookbooks, product shoots and films — concept, casting, crew and post-production, from the team that directs the campaigns of London Fashion Day and Odessa Fashion Day.',
			'question'     => 'Who shoots this?',
			'image'        => 'lfd-s7-campaign-regent-street.jpg',
			'what'         => array(
				'Every season of the platforms we manage begins with a campaign: a concept, a location, a look, a model, a film. Season 7 of London Fashion Day was shot at sunrise on Regent Street; Season 8 in a studio washed in blue, around a student designer’s work. We direct those shoots, and we direct yours the same way — for a collection, a product, a space or a person.',
				'That means one producer from moodboard to delivery, a crew we have worked with for years, and footage cut for where it will actually live — vertical for social, landscape for the website, stills for press.',
			),
			'get'          => array(
				'Campaign concept and creative direction',
				'Photo campaigns, lookbooks and product photography',
				'Promotional and campaign films',
				'Casting, styling, hair and make-up',
				'Locations, permissions and production management',
				'Edited stills and cuts for web, social and press',
			),
			'steps'        => array(
				array( 'Concept.', 'A moodboard, a shot list and a plan for every format you need.' ),
				array( 'Pre-production.', 'Casting, crew, location and schedule — confirmed in writing.' ),
				array( 'Shoot.', 'One day or several, run by a producer who knows the brief.' ),
				array( 'Delivery.', 'Edit, colour grade and final files, sized for every channel.' ),
			),
			'work_heading' => 'Shoots <em>and films.</em>',
			'work'         => array( 'types' => 'shoot', 'count' => 6, 'columns' => 2, 'moreLabel' => 'All photo & film', 'moreUrl' => '/work/?type=shoot' ),
			'faq'          => array(
				array( 'Can you shoot outside London?', 'Yes. We have produced shoots and shows in London, Odesa and beyond, travelling with a core team or hiring locally.' ),
				array( 'Can you work with our own photographer?', 'Of course. We can direct the shoot with your photographer, or bring the whole crew.' ),
			),
			'cta'          => 'Plan <em>your shoot.</em>',
		)
	);

	$pages[] = $service(
		array(
			'title'        => 'Fashion show & event production',
			'short'        => 'Shows & events',
			'slug'         => 'fashion-show-production',
			'order'        => 5,
			'eyebrow'      => 'Service 05 — Shows & events',
			'heading'      => 'Shows and launches by the people <em>who run the runway.</em>',
			'lede'         => 'Runway shows, presentations, product launches and events — by the team that manages London Fashion Day and Odessa Fashion Day.',
			'question'     => 'Who produces the show?',
			'image'        => 'lfd-s7-gorsset.jpg',
			'what'         => array(
				'We manage runway seasons in London and Odesa: in St John’s Church, Hyde Park; in Westminster’s Porchester Hall; in a historic Odesa hotel; and in a university botanical garden. We know the running order measured in minutes, the fittings and rehearsals nobody budgets for, and which corners cannot be cut.',
				'For your business that means a show, presentation, opening or launch built around what you are showing rather than the venue: concept and staging, casting and choreography, backstage and front-of-house, guest list and press — and content captured so the evening keeps working long after the lights go down.',
			),
			'get'          => array(
				'Independent fashion shows and presentations',
				'Product launches, openings and brand presentations',
				'Show direction, staging and running order',
				'Casting, hair, make-up and backstage teams',
				'Guest lists, front-of-house and press',
				'Photo and video coverage of the event',
			),
			'steps'        => array(
				array( 'Concept and venue.', 'What the event needs to say, and the room that says it.' ),
				array( 'Production plan.', 'Schedule, suppliers and running order, agreed up front.' ),
				array( 'Casting and rehearsal.', 'Models, fittings, hair and make-up tests, a full run-through.' ),
				array( 'Show day and after.', 'Backstage, front-of-house, press — then the content, edited and delivered.' ),
			),
			'work_heading' => 'Seasons <em>we have managed.</em>',
			'work'         => array( 'types' => 'show', 'count' => 6, 'columns' => 3, 'moreLabel' => 'All shows & events', 'moreUrl' => '/work/?type=show' ),
			'faq'          => array(
				array( 'Is this the same as showing at London Fashion Day?', 'No. A London Fashion Day place is by application and selection. This service is for businesses that want their own show, presentation or launch. If LFD is what you are after, see <a href="/show-in-london/">Show in London</a>.' ),
				array( 'Do you only produce fashion events?', 'Fashion is where we started, but the same craft works for a product launch, a salon or shop opening, or a brand presentation for press and partners.' ),
			),
			'cta'          => 'Plan <em>your event.</em>',
		)
	);

	$pages[] = $service(
		array(
			'title'        => 'PR & promotion',
			'short'        => 'PR & promotion',
			'slug'         => 'pr-and-promotion',
			'order'        => 6,
			'eyebrow'      => 'Service 06 — PR & promotion',
			'heading'      => 'Be written about. <em>Be seen.</em>',
			'lede'         => 'Press, partnerships and paid campaigns — two disciplines with equal weight: earning coverage and buying attention that turns into customers.',
			'question'     => 'Who gets us seen?',
			'image'        => 'ofd-s31-londonska-hotel.jpg',
			'what'         => array(
				'London Fashion Day’s eighth season is carried by twenty official media partners, alongside partners from law firms to colleges, and the magazines PROMINENT, Fashion Frontier and COOL’BABA, whose websites we built. We build the same kind of network around your business: a story worth telling, press materials that get opened, and relationships that last longer than one launch.',
				'Paid promotion multiplies whatever it lands on, so we run it last and measure it honestly: Google and YouTube for people already searching, Instagram and Facebook for discovery, with budgets planned around your dates and reported in plain numbers.',
			),
			'get'          => array(
				'Marketing and communication strategy',
				'Press materials, media outreach and PR support',
				'Media and brand partnerships',
				'Google and YouTube promotion',
				'Meta advertising: Facebook and Instagram',
				'Audience growth, engagement and monthly reporting',
			),
			'steps'        => array(
				array( 'Story.', 'What is genuinely new, and who needs to hear it.' ),
				array( 'Press and partners.', 'Materials, outreach and partnerships timed to your dates.' ),
				array( 'Paid launch.', 'Campaigns built on a site that is ready for the traffic.' ),
				array( 'Report.', 'Plain numbers, what worked, and what to change next month.' ),
			),
			'work_heading' => 'Stories <em>we have told.</em>',
			'work'         => array( 'disciplines' => 'visibility', 'count' => 6, 'columns' => 3, 'moreLabel' => 'All work', 'moreUrl' => '/work/' ),
			'faq'          => array(
				array( 'Do you guarantee coverage?', 'No honest PR team can. We guarantee the work: a strong story, materials that get opened and a pitch to the right people.' ),
				array( 'Can you run ads on a small budget?', 'Yes, if the website is ready for the traffic. If it isn’t, we will say so — and fix that first.' ),
			),
			'cta'          => 'Get <em>your brand seen.</em>',
		)
	);

	/* ── Work ─────────────────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Work',
		'slug'    => 'work',
		'order'   => 2,
		'excerpt' => 'Websites we built, shoots we directed and runway platforms we manage — the work of AK Brand Development Studio.',
		'content' =>
			$header(
				array(
					'eyebrow' => 'Work',
					'heading' => 'Websites, shoots, <em>shows and brands.</em>',
					'lede'    => 'Websites we have designed and built, shoots and campaigns we have directed, and the runway platforms we manage.',
					'size'    => 'compact',
				),
				'web-wall.jpg'
			) .
			ak_b_section( ak_b_ak( 'work', array( 'showFilters' => true, 'count' => 60, 'columns' => 2 ) ) ) .
			ak_seed_cta(),
	);

	/* ── Show in London ───────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Show in London',
		'slug'    => 'show-in-london',
		'order'   => 3,
		'excerpt' => 'Show your collection at London Fashion Day, the independent runway platform our studio manages — how selection works, and how we help designers arrive ready.',
		'content' =>
			$header(
				array(
					'eyebrow' => 'For designers',
					'heading' => 'Your collection, <em>on a London runway.</em>',
					'lede'    => 'Our studio manages London Fashion Day — the independent runway platform for emerging and independent designers. Here is how a place works, and how we can help you arrive ready.',
				),
				'still-lfd-westminster-9.jpg'
			) .
			ak_b_ak( 'event', array_merge( $event_s8, array( 'eyebrowUpcoming' => 'Next on stage', 'secondaryLabel' => '', 'secondaryUrl' => '', 'anchor' => 'season' ) ) ) .
			ak_b_section(
				ak_b_h( 'London Fashion Day in short' ) .
				ak_b_p( 'Since 2023, <a href="https://londonfashionday.co.uk/">London Fashion Day</a> has given designers from the UK, Europe, Asia, Africa and beyond a professionally produced runway in London, in front of guests, press and industry. Recent seasons took place at St John’s Church, Hyde Park. Season 8 — <em>PAST. PRESENT. NEXT.</em> — takes place on 8 November 2026 at the historic Porchester Hall in Westminster: twelve fashion voices, six runway slots, opening with students of the London College of Contemporary Arts.' ) .
				ak_b_h( 'Who it is for' ) .
				ak_b_list( array( 'Emerging clothing designers', 'Independent fashion labels', 'Established brands entering the UK market', 'Recent fashion graduates', 'Designers developing a new collection or creative direction' ) ) .
				ak_b_h( 'How selection works' ) .
				ak_b_steps(
					array(
						array( 'Apply.', 'Tell the London Fashion Day team about your label, your collection and your presentation goals through the designer application.' ),
						array( 'Review.', 'Every application is reviewed individually for programme fit, visual identity and readiness for a professional runway.' ),
						array( 'Proposal.', 'Selected designers receive a full participation proposal by email: presentation format, production scope, fee and next steps. An application is not a booking and does not reserve a slot.' ),
					)
				) .
				ak_b_h( 'What a season includes' ) .
				ak_b_p( 'Participation is a full presentation journey rather than a slot: show direction and backstage structure; a runway team of models, beauty and choreography; targeted guest and media outreach; and content that keeps the collection visible after the show. The exact scope depends on the format offered to you.' ) .
				ak_b_h( 'How the studio can help' ) .
				ak_b_p( 'Many designers come to us before they apply, or once they are selected, to make the most of the moment:' ) .
				ak_b_list( array( 'Collection positioning and a press-ready brand story', 'Lookbook and campaign shoots', 'Press kit, media outreach and partnerships', 'A website or online store ready for the attention a show brings', 'Show content edited for social, press and buyers' ) ) .
				ak_b_buttons(
					array(
						array( 'Apply on londonfashionday.co.uk', 'https://londonfashionday.co.uk/designers-reg/' ),
						array( 'Talk to us first', '/contact/?need=runway', 'is-style-outline' ),
					)
				) .
				ak_b_h( 'Showing in Ukraine' ) .
				ak_b_p( '<a href="https://ofd.org.ua/">Odessa Fashion Day</a>, which our studio also manages, has presented Ukrainian designers in Odesa since 2010 — 33 seasons, the 33rd on 6–8 November 2026 — and its summer edition, SUMMER WEEKEND by OFD, since 2015. Designers can apply through the OFD website.' )
			) .
			ak_b_section( ak_b_gallery( array( 'lfd-s7-gorsset.jpg', 'lfd-s6-lcca-showcase.jpg', 'lfd-s5-elen-ost.jpg', 'lfd-s6-sara-k-couture.jpg', 'lfd-s7-riri-couture.jpg', 'lfd-s6-mogardi.jpg' ) ) ) .
			ak_seed_cta(),
	);

	/* ── Studio ───────────────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Studio',
		'slug'    => 'about',
		'order'   => 4,
		'excerpt' => 'AK Brand Development Studio is an independent London studio founded by Andrii Karakushan and Kostiantyn Lieontiev — one letter each.',
		'content' =>
			$header(
				array(
					'eyebrow' => 'About AK',
					'heading' => 'Two founders. <em>One letter each.</em>',
					'lede'    => 'AK Brand Development Studio is an independent creative and strategic studio in London. We help people and businesses build brands that get noticed — from the initial idea to international presence.',
				),
				'lfd-s7-kostiantyn-lieontiev-runway.jpg'
			) .
			ak_b_section(
				ak_b_h( 'Why AK' ) .
				ak_b_p( 'A is for Andrii Karakushan. K is for Kostiantyn Lieontiev. The studio carries our initials because the work is ours: we lead every project personally, and we bring to it what we have learned managing runway platforms, directing campaigns and building websites.' ) .
				ak_b_p( 'AK Brand Development Studio brings strategy, identity, websites, photo and film, shows, PR and promotion under one roof — and manages the runway platforms London Fashion Day and Odessa Fashion Day.' ) .
				ak_b_h( 'What we believe' ) .
				ak_b_list(
					array(
						'<strong>Production over presentations.</strong> A strategy is only as good as the website, the shoot and the launch it produces.',
						'<strong>Talent deserves a door.</strong> The platforms we manage exist so that people can be seen before they are famous.',
						'<strong>Honest numbers.</strong> We tell clients what things really cost, what worked and what didn’t.',
					),
					false,
					'is-style-cards'
				) .
				ak_b_quote( 'We cannot decide who will become the next important name in fashion. What we can do is make sure that talent has an opportunity to be seen. Sometimes, all a young designer needs is someone willing to open the door.', 'Kostiantyn Lieontiev, London Fashion Day' )
			) .
			ak_b_section(
				ak_b_eyebrow( 'The founders' ) .
				ak_b_display( 'A is for Andrii. <em>K is for Kostiantyn.</em>' ) .
				ak_b_columns(
					array(
						ak_seed_founder(
							'A',
							'Andrii Karakushan',
							'Co-founder — digital, identity and media',
							array(
								'Andrii Karakushan is a creative entrepreneur specialising in brand development, digital presence and visual communication.',
								'He has experience managing a multi-brand retail space for emerging designers, supporting young fashion brands through retail presentation and brand promotion.',
								'He is the founder of the online magazine Cool’baba, a media platform focused on fashion, lifestyle and creative industries.',
								'His expertise includes website development, brand identity, digital communication and building online ecosystems for businesses and creative projects.',
							)
						),
						ak_seed_founder(
							'K',
							'Kostiantyn Lieontiev',
							'Co-founder — fashion production and brand strategy',
							array(
								'Kostiantyn Lieontiev is a fashion producer, brand strategist and media professional with extensive experience in brand development and creative industries.',
								'Before focusing on international fashion projects, he spent nine years managing a regional branch of a major advertising holding, where he was responsible for business development, advertising campaigns and strategic client development.',
								'He heads the international fashion platforms London Fashion Day and Odessa Fashion Day, which support emerging designers and build international creative communities, and is developing the fashion brand KEKA for the international market.',
								'Through his work he has collaborated with designers, brands and creative teams across London, Paris and Dubai.',
							)
						),
					)
				)
			) .
			ak_b_section(
				ak_b_eyebrow( 'The record' ) .
				ak_b_display( 'Sixteen years <em>of building brands.</em>' ) .
				ak_b_list(
					array(
						'<strong>2010</strong> Odessa Fashion Day begins — a runway for Ukrainian designers that grows to include a shopping space for local labels, master classes and free admission for guests.',
						'<strong>2015</strong> The first SUMMER WEEKEND by OFD: a summer edition of shows in Odesa.',
						'<strong>2016</strong> OFD signs a memorandum of strategic partnership with Ukrainian Fashion Week; from 2020 it is also a partner of the national channel Fashion TV Ukraine.',
						'<strong>2022</strong> The team presents Ukrainian designers at the International Women’s Forum in London — the show that inspired London Fashion Day.',
						'<strong>2023</strong> London Fashion Day Season 1 on 22 April: six collections, four of them Ukrainian. Season 2 follows in October with eleven brands.',
						'<strong>2024</strong> Seasons 3 and 4: designers from Europe, Asia, Africa and the UK, and more than 100 media outlets. A campaign film on Westminster Bridge.',
						'<strong>2025</strong> Odessa Fashion Day marks its 30th season. KEKA and Gorsset show at Fashion For Freedom during Milan Fashion Week.',
						'<strong>2026</strong> LFD Season 7 on Regent Street and in Hyde Park, OFD Season 32, SUMMER WEEKEND in the botanical garden, a new website for a Marylebone salon — and in November, OFD Season 33 and LFD Season 8 at Porchester Hall.',
					),
					false,
					'is-style-timeline'
				)
			) .
			ak_b_section(
				ak_b_eyebrow( 'Who we work alongside' ) .
				ak_b_display( 'Colleges, crews, <em>platforms and press.</em>' ) .
				ak_b_eyebrow( 'Education' ) .
				ak_b_list( array( 'London College of Contemporary Arts', 'Fashion Academy Paris', 'Ushynsky University' ), false, 'is-style-marquee' ) .
				ak_b_eyebrow( 'Production' ) .
				ak_b_list( array( 'Show Empire', 'Session Squad', 'Beauty Bureau', 'Dinara Ostr', 'Mariana Zabramna', 'Natalia Tomash' ), false, 'is-style-marquee' ) .
				ak_b_eyebrow( 'International' ) .
				ak_b_list( array( 'Odessa Fashion Day', 'Georgia Fashion Week', 'Global Fashion Net', 'Ukrainian Fashion Week', 'Fashion TV Ukraine' ), false, 'is-style-marquee' ) .
				ak_b_eyebrow( 'Media partners' ) .
				ak_b_list( array( 'FASHION', 'CHIC TV', 'FAB UK Magazine', 'GLAM Week Magazine', 'London One Magazine', 'Fashion Frontier', 'London Business Journal', 'FOSTYLEN', 'Cool’baba Magazine', 'PROMINENT Magazine', 'FLICKER Magazine', 'IKON Magazine', 'ARRANT Magazine', 'SURREAL Magazine' ), false, 'is-style-marquee' )
			) .
			ak_seed_cta(),
	);

	/* ── Contact ──────────────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Contact',
		'slug'    => 'contact',
		'order'   => 5,
		'excerpt' => 'Start a project with AK Brand Development Studio: tell us about your brand, your date and what you need. We read every brief ourselves.',
		'content' =>
			$header(
				array(
					'eyebrow' => 'Start a project',
					'heading' => 'Tell us what <em>you’re building.</em>',
					'lede'    => 'The more we know, the better the first answer. Tell us about your business, the date you’re working towards and what you need — we read every brief ourselves.',
					'size'    => 'compact',
				)
			) .
			ak_b_section(
				ak_b_columns(
					array(
						ak_b_ak( 'brief', array( 'source' => 'Contact page' ) ),
						ak_b_h( 'What happens next' ) .
						ak_b_steps(
							array(
								array( 'We read your brief', 'and come back with questions, or a time to talk.' ),
								array( 'You receive a proposal:', 'scope, team, timeline and fee, in writing.' ),
								array( 'Once you approve it,', 'we book the team and the dates.' ),
							)
						) .
						ak_b_h( 'Prefer email?' ) .
						ak_b_p( 'Write to ' . $mail . '. The studio is in London and works with clients wherever they are.' ) .
						ak_b_h( 'Designers' ) .
						ak_b_p( 'Looking to show your collection at London Fashion Day? Start with <a href="/show-in-london/">Show in London</a>.' ),
					),
					'split'
				)
			),
	);

	/* ── Journal (posts page: the entries follow this header) ─────────── */
	$pages[] = array(
		'title'   => 'Journal',
		'slug'    => 'journal',
		'order'   => 6,
		'excerpt' => 'Notes from the studio and the runway: what things really cost, what works, and what’s next.',
		'content' =>
			$header(
				array(
					'eyebrow' => 'Journal',
					'heading' => 'Notes from the studio <em>and the runway.</em>',
					'lede'    => 'What things really cost, what works, and what’s next — from the studio and the runway platforms we manage.',
					'size'    => 'compact',
				)
			),
	);

	/* ── Legal ────────────────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Privacy Policy',
		'slug'    => 'privacy-policy',
		'order'   => 20,
		'excerpt' => 'What happens to the information you give us through this website.',
		'content' =>
			$header( array( 'eyebrow' => 'Legal', 'heading' => 'Privacy Policy', 'lede' => 'What happens to the information you give us through this website. Last updated ' . $updated . '.', 'size' => 'compact' ) ) .
			ak_b_section(
				ak_b_h( 'Who we are' ) .
				ak_b_p( '[ak_controller] (“we”, “us”) is an independent creative and strategic studio based in London, United Kingdom. We are the controller of the personal data described here. You can reach us at ' . $mail . '.' ) .
				ak_b_h( 'What we collect' ) .
				ak_b_list(
					array(
						'<strong>Your brief.</strong> When you use the project brief form we receive your name and email address, and anything else you choose to add: phone number, brand or company name, website or social link, the services you are interested in, a key date and your message.',
						'<strong>Your emails.</strong> If you write to us directly, we receive what you send.',
						'<strong>Analytics, only with your consent.</strong> If you accept analytics cookies, Google Analytics records how pages on this site are used (pages viewed, approximate location, device and browser). If you decline, no analytics cookies are set.',
					)
				) .
				ak_b_h( 'Why we use it, and on what basis' ) .
				ak_b_list(
					array(
						'To read and answer your enquiry and, if you ask us to, to prepare a proposal — necessary to take steps at your request before entering into a contract, and in our legitimate interest in responding to people who contact us.',
						'To understand how the site is used and improve it — on the basis of your consent, which you can withdraw at any time through “Cookie settings” at the foot of every page.',
					)
				) .
				ak_b_p( 'We do not sell your data, use it for automated decision-making or add you to a mailing list you did not ask to join.' ) .
				ak_b_h( 'Who else sees it' ) .
				ak_b_p( 'Only the studio’s founders and the people working on your enquiry. Our website host and email provider process data on our behalf to run the site and deliver messages. If you accept analytics, Google processes usage data as our processor; this may involve transfer to the United States under the safeguards Google provides, such as the UK International Data Transfer Addendum.' ) .
				ak_b_h( 'How long we keep it' ) .
				ak_b_p( 'Briefs that do not become projects are deleted automatically within twelve months. Where an enquiry becomes a project, we keep the correspondence for as long as the working relationship needs it and for the period required by our accounting and legal obligations afterwards.' ) .
				ak_b_h( 'Your rights' ) .
				ak_b_p( 'Under UK data protection law you may ask for a copy of the data we hold about you, ask us to correct or delete it, object to or restrict how we use it, ask for it in a portable format, and withdraw consent at any time. Write to ' . $mail . ' and we will respond within one month. If you are unhappy with our answer, you can complain to the Information Commissioner’s Office at <a href="https://ico.org.uk/">ico.org.uk</a>.' ) .
				ak_b_h( 'Security' ) .
				ak_b_p( 'The site is served over an encrypted connection, briefs are stored in a password-protected area of the site that only the studio can access, and we keep the software up to date.' ) .
				ak_b_h( 'Changes' ) .
				ak_b_p( 'If this policy changes, the new version will appear on this page with a new date.' )
			),
	);

	$pages[] = array(
		'title'   => 'Cookie Policy',
		'slug'    => 'cookie-policy',
		'order'   => 21,
		'excerpt' => 'Which cookies this site uses, and how to change your choice.',
		'content' =>
			$header( array( 'eyebrow' => 'Legal', 'heading' => 'Cookie Policy', 'lede' => 'Which cookies this site uses, and how to change your choice. Last updated ' . $updated . '.', 'size' => 'compact' ) ) .
			ak_b_section(
				ak_b_h( 'Strictly necessary' ) .
				ak_b_p( 'These make the site work and are set without asking:' ) .
				ak_b_list(
					array(
						'<code>ak_consent</code> — remembers whether you accepted or declined analytics cookies, so we don’t ask on every page. Kept for six months.',
						'<code>wordpress_*</code> and <code>wp-settings-*</code> — set only for the studio’s own team when they sign in to manage the site.',
					)
				) .
				ak_b_h( 'Analytics — only if you accept' ) .
				ak_b_p( 'If you choose “Accept analytics”, Google Analytics sets <code>_ga</code> and <code>_ga_*</code> cookies to count visits and see which pages are useful. They are kept for up to two years. If you choose “No thanks”, they are never set, and Google receives no identifying cookie data from this site.' ) .
				ak_b_h( 'Changing your mind' ) .
				ak_b_p( 'Use “Cookie settings” at the foot of any page to change your choice at any time. You can also delete cookies in your browser settings.' ) .
				ak_b_p( 'Questions? Write to ' . $mail . '.' )
			),
	);

	return (array) apply_filters( 'ak_brand_seed_pages', $pages );
}
