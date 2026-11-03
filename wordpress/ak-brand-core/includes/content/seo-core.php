<?php
/**
 * The SEO core: for every page, project and journal entry — the Yoast SEO
 * title, meta description, focus keyphrase, synonyms, related keyphrases and
 * the extended keyword list the page is written for.
 *
 * The installer writes the Yoast fields (only where nobody has filled them in
 * by hand); wordpress/SEO.md is generated from this file for reading.
 *
 * Keys: "page:<path>", "project:<slug>", "post:<slug>".
 * Fields: t = SEO title, d = meta description, k = focus keyphrase,
 *         s = synonyms, r = related keyphrases (Yoast Premium, up to 4),
 *         x = extended keywords.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string,array<string,mixed>>
 */
function ak_brand_seed_seo(): array {
	return array(

		/* ── Main pages ───────────────────────────────────────────── */

		'page:home'                               => array(
			't' => 'Branding Agency in London — AK Brand Development Studio',
			'd' => 'Branding agency in London: brand strategy, identity, websites, photo and film, fashion shows and PR. One team, from first idea to international presence.',
			'k' => 'branding agency London',
			's' => array( 'brand agency London', 'brand development studio London', 'branding studio London', 'creative agency London' ),
			'r' => array( 'brand development studio', 'website design London', 'fashion show production London', 'creative studio London' ),
			'x' => array( 'branding agency London', 'brand agency London', 'brand development agency London', 'brand development studio London', 'brand development studio', 'branding studio London', 'independent branding agency London', 'full-service branding agency London', 'creative agency London', 'creative studio London', 'independent creative studio London', 'brand building agency', 'branding for small business London', 'branding for startups London', 'branding for fashion brands', 'fashion branding agency London', 'beauty brand agency London', 'rebranding agency London', 'brand strategy and identity London', 'website design London', 'online store design London', 'fashion photography and film London', 'fashion show production London', 'fashion PR London', 'brand launch London', 'brand from idea to launch', 'international brand development', 'UK brand development', 'AK Brand Development Studio', 'AK Studio London', 'AK brand studio', 'akbrand.studio', 'Andrii Karakushan', 'Kostiantyn Lieontiev', 'London Fashion Day', 'Odessa Fashion Day', 'Marylebone', 'Westminster', 'Chelsea', 'Mayfair', 'Kensington', 'Soho', 'Shoreditch' ),
		),

		'page:services'                           => array(
			't' => 'Brand Development Services in London — AK Studio',
			'd' => 'Brand development services in London: strategy, identity, websites and online stores, photo and film, fashion shows, PR and promotion. Take one or all six.',
			'k' => 'brand development services',
			's' => array( 'branding services London', 'brand building services', 'brand services', 'branding and marketing services London' ),
			'r' => array( 'branding services London', 'brand strategy and identity', 'website and online store design', 'creative production London' ),
			'x' => array( 'brand development services', 'branding services London', 'brand building services', 'branding packages London', 'brand strategy services', 'brand identity services', 'logo and identity design', 'creative direction services', 'website design services London', 'web development services London', 'ecommerce website services', 'photo and video production services', 'fashion campaign production', 'fashion show production services', 'event production London', 'PR services London', 'fashion PR services', 'paid social advertising', 'digital marketing for brands', 'brand launch services', 'rebrand services', 'personal branding services', 'one team branding and marketing', 'branding agency services UK' ),
		),

		'page:about'                              => array(
			't' => 'About AK Brand Development Studio — London Studio',
			'd' => 'AK Brand Development Studio is an independent London studio founded by Andrii Karakushan and Kostiantyn Lieontiev, who manage London and Odessa Fashion Day.',
			'k' => 'AK Brand Development Studio',
			's' => array( 'AK Studio London', 'AK brand studio', 'akbrand.studio' ),
			'r' => array( 'Andrii Karakushan', 'Kostiantyn Lieontiev', 'independent creative studio London', 'brand development studio London' ),
			'x' => array( 'AK Brand Development Studio', 'AK Studio London', 'AK brand studio', 'about AK Brand Development Studio', 'akbrand.studio', 'Andrii Karakushan', 'Андрій Каракушан', 'Андрей Каракушан', 'Kostiantyn Lieontiev', 'Костянтин Лєонтьєв', 'Константин Леонтьев', 'founders of London Fashion Day management', 'Odessa Fashion Day management', 'COOL’BABA founder', 'KEKA fashion brand', 'independent creative studio London', 'strategic studio London', 'brand development studio London', 'Ukrainian founders London', 'Ukrainian creative agency London', 'creative directors London', 'fashion industry experience', 'runway platforms London and Odesa', 'brands that get noticed' ),
		),

		'page:contact'                            => array(
			't' => 'Start a Project — Contact AK Brand Development Studio',
			'd' => 'Start a project with AK Brand Development Studio in London: tell us about your brand, your date and what you need. We read every brief and reply.',
			'k' => 'start a project',
			's' => array( 'contact AK Brand Development Studio', 'start a branding project', 'brief a branding agency' ),
			'r' => array( 'branding agency London contact', 'website design quote London', 'fashion show enquiry London' ),
			'x' => array( 'contact AK Brand Development Studio', 'AK Studio contact', 'ak@akbrand.studio', 'start a branding project', 'brief a branding agency', 'branding agency London contact', 'branding proposal London', 'website design quote London', 'website design enquiry', 'photo shoot enquiry London', 'fashion show enquiry London', 'show at London Fashion Day enquiry', 'PR agency enquiry London', 'creative brief form', 'project brief', 'hire a branding agency London' ),
		),

		'page:work'                               => array(
			't' => 'Portfolio: Websites, Campaigns & Shows | AK Studio',
			'd' => 'The portfolio of AK Brand Development Studio: websites we built, fashion campaigns and films we directed, and the London and Odesa runways we manage.',
			'k' => 'portfolio',
			's' => array( 'branding agency portfolio', 'web design portfolio London', 'case studies', 'our work' ),
			'r' => array( 'website design portfolio', 'fashion campaign portfolio', 'fashion show case studies' ),
			'x' => array( 'branding agency portfolio', 'branding portfolio London', 'web design portfolio London', 'website design portfolio', 'ecommerce website examples', 'fashion website examples', 'salon website example', 'magazine website examples', 'fashion campaign portfolio', 'fashion film portfolio', 'campaign film London', 'fashion show case studies', 'runway show production portfolio', 'London Fashion Day seasons', 'Odessa Fashion Day seasons', 'KEKA', 'Lenie Boya', 'Wolax Interiors', 'Show Me Your Nails', 'Ultimate Trend Store', 'PROMINENT Magazine', 'Fashion Frontier', 'COOL’BABA' ),
		),

		'page:journal'                            => array(
			't' => 'Branding and Fashion Journal | AK Brand Development Studio',
			'd' => 'Our branding and fashion journal: what a fashion week show really costs, identities that work in the feed, websites that sell, and news from our runways.',
			'k' => 'branding and fashion journal',
			's' => array( 'branding blog', 'fashion business blog', 'studio journal', 'fashion week news' ),
			'r' => array( 'fashion week advice for designers', 'branding advice for small business', 'London Fashion Day news' ),
			'x' => array( 'branding blog', 'branding advice', 'branding advice for small business', 'fashion business advice', 'fashion week advice for designers', 'how much does a fashion show cost', 'brand identity for social media', 'personal brand vs company brand', 'website conversion advice', 'paid ads advice', 'London Fashion Day news', 'Odessa Fashion Day news', 'SUMMER WEEKEND by OFD', 'fashion news London' ),
		),

		'page:show-in-london'                     => array(
			't' => 'Runway Show in London for Designers | London Fashion Day',
			'd' => 'A runway show in London for emerging and independent designers: how a place at London Fashion Day works, what it includes and how we help you arrive ready.',
			'k' => 'runway show in London',
			's' => array( 'show at London Fashion Day', 'fashion show in London for designers', 'London runway for emerging designers', 'catwalk show London' ),
			'r' => array( 'show at a fashion week in London', 'fashion week for emerging designers', 'London Fashion Day designers', 'international designers London runway' ),
			'x' => array( 'runway show in London', 'runway show London', 'show at London Fashion Day', 'London Fashion Day designers', 'London Fashion Day application', 'London Fashion Day participation', 'how to show at a fashion week in London', 'show your collection in London', 'fashion show for emerging designers London', 'fashion week for independent designers', 'fashion week for emerging designers', 'catwalk show London', 'London fashion show for designers', 'international designers London runway', 'Ukrainian designers London', 'designers from Europe Asia Africa London show', 'fashion show participation fee', 'runway slot London', 'fashion week runway slot', 'collection presentation London', 'fashion show November 2026 London', 'London Fashion Day Season 8', 'Porchester Hall fashion show', 'St John’s Church Hyde Park fashion show', 'press coverage for designers', 'fashion show photos and video', 'model casting London', 'lookbook before a show', 'runway preparation for designers' ),
		),

		/* ── Services ─────────────────────────────────────────────── */

		'page:services/brand-strategy'            => array(
			't' => 'Brand Strategy Agency in London — AK Brand Development Studio',
			'd' => 'Brand strategy agency in London: positioning, audience, story and naming for new businesses, relaunches and founders building a personal brand.',
			'k' => 'brand strategy agency London',
			's' => array( 'brand strategy London', 'brand strategy consultant London', 'brand positioning London', 'brand strategist London' ),
			'r' => array( 'brand positioning', 'personal brand strategy', 'rebrand strategy', 'brand strategy for startups' ),
			'x' => array( 'brand strategy agency London', 'brand strategy London', 'brand strategy consultant London', 'brand strategist London', 'brand positioning London', 'brand positioning agency', 'brand platform', 'brand story', 'brand messaging', 'tone of voice', 'naming agency London', 'brand naming', 'target audience research', 'competitor analysis branding', 'brand strategy for startups', 'brand strategy for small business', 'brand strategy for fashion brands', 'brand strategy for beauty brands', 'rebrand strategy', 'relaunch a brand', 'personal brand strategy', 'personal branding for founders', 'personal branding London', 'founder brand', 'brand workshop London', 'brand strategy workshop', 'go-to-market strategy UK', 'brand for international market', 'launching a brand in the UK' ),
		),

		'page:services/brand-identity'            => array(
			't' => 'Brand Identity Design in London — AK Brand Development Studio',
			'd' => 'Brand identity design in London: logo, type, colour, packaging and creative direction that hold up on a sign, a label, a website and a six-second ad.',
			'k' => 'brand identity design London',
			's' => array( 'brand identity agency London', 'visual identity design', 'logo design London', 'creative direction London' ),
			'r' => array( 'logo design London', 'creative direction', 'rebranding London', 'brand guidelines' ),
			'x' => array( 'brand identity design London', 'brand identity agency London', 'visual identity design', 'visual identity London', 'logo design London', 'logo and brand identity', 'logo designer London', 'brand guidelines', 'brand book', 'typography and colour palette', 'packaging design London', 'label design for fashion brands', 'fashion brand identity', 'beauty brand identity', 'salon branding', 'restaurant branding London', 'creative direction London', 'creative director London', 'art direction', 'rebranding London', 'rebrand agency London', 'brand refresh', 'identity for social media', 'brand identity for Instagram', 'signage and print design', 'business cards and stationery' ),
		),

		'page:services/website-development'       => array(
			't' => 'Website Design & Development in London | AK Studio',
			'd' => 'Website design in London: brand sites, online stores and booking websites — fast on a phone, clear on the first screen and easy to update. See our work.',
			'k' => 'website design London',
			's' => array( 'web design London', 'website development London', 'web design agency London', 'website designer London' ),
			'r' => array( 'ecommerce website design', 'online store for fashion brands', 'salon booking website', 'WordPress website design' ),
			'x' => array( 'website design London', 'web design London', 'web design agency London', 'website development London', 'website designer London', 'website development company London', 'bespoke website design', 'custom website design London', 'WordPress website design London', 'WooCommerce website', 'Shopify website design London', 'ecommerce website design London', 'online store design', 'online store for fashion brands', 'fashion website design', 'luxury fashion website', 'beauty salon website design', 'nail salon website', 'salon booking website', 'booking website design', 'interior design website', 'magazine website design', 'news website design', 'bilingual website design', 'multilingual website', 'mobile-first website', 'fast website', 'website redesign London', 'small business website London', 'website with online booking', 'SEO-friendly website', 'website maintenance London', 'landing page design' ),
		),

		'page:services/photo-video-production'    => array(
			't' => 'Photo & Film Production in London — Fashion Campaigns',
			'd' => 'Photo and film production in London: fashion campaigns, lookbooks, product shoots and brand films — concept, casting, crew and post-production, end to end.',
			'k' => 'photo and film production London',
			's' => array( 'photo and video production London', 'fashion campaign production London', 'fashion shoot London', 'brand film production London' ),
			'r' => array( 'fashion campaign shoot', 'fashion film production', 'lookbook photography', 'product photography London' ),
			'x' => array( 'photo and film production London', 'photo and video production London', 'video production London', 'fashion photography London', 'fashion photographer London', 'fashion campaign shoot London', 'fashion campaign production', 'campaign shoot London', 'lookbook photography London', 'lookbook shoot', 'ecommerce photography', 'product photography London', 'jewellery photography', 'beauty photography London', 'brand photography London', 'fashion film production', 'fashion film London', 'brand film', 'campaign film', 'social media video production', 'reels and short-form video', 'behind the scenes video', 'model casting London', 'stylist and make-up team', 'location shoot London', 'studio shoot London', 'creative production agency London', 'post-production and retouching', 'fashion week photography' ),
		),

		'page:services/fashion-show-production'   => array(
			't' => 'Fashion Show Production in London — Runway & Events',
			'd' => 'Fashion show production in London by the team that manages London Fashion Day: runway shows, presentations, product launches and brand events, end to end.',
			'k' => 'fashion show production London',
			's' => array( 'fashion show organiser London', 'runway show production', 'catwalk show production', 'fashion event production London' ),
			'r' => array( 'fashion event production', 'product launch event London', 'runway show organiser', 'brand event agency London' ),
			'x' => array( 'fashion show production London', 'fashion show production', 'fashion show organiser London', 'fashion show organizer', 'runway show production', 'runway show organiser', 'catwalk show production', 'catwalk show London', 'fashion event production London', 'fashion event agency London', 'fashion presentation London', 'collection presentation', 'product launch event London', 'brand launch event London', 'brand event agency London', 'event production London', 'pop-up event London', 'salon opening event', 'show producer', 'model casting for runway', 'backstage management', 'running order', 'fittings and rehearsals', 'show venue London', 'Porchester Hall events', 'Hyde Park venue fashion show', 'charity fashion show London', 'fashion show for brands', 'event photography and video' ),
		),

		'page:services/pr-and-promotion'          => array(
			't' => 'Fashion PR Agency in London — PR & Promotion | AK Studio',
			'd' => 'Fashion PR agency in London: press coverage, media partnerships and paid campaigns that turn attention into customers, from the team behind LFD.',
			'k' => 'fashion PR agency London',
			's' => array( 'PR agency London', 'brand PR London', 'fashion PR London', 'PR and promotion' ),
			'r' => array( 'press coverage for brands', 'media partnerships', 'paid social advertising', 'brand promotion' ),
			'x' => array( 'fashion PR agency London', 'fashion PR London', 'PR agency London', 'brand PR London', 'beauty PR London', 'lifestyle PR agency', 'PR for small business London', 'PR for designers', 'PR for emerging designers', 'press release writing', 'press kit', 'press coverage for brands', 'media relations', 'media partnerships', 'magazine features', 'fashion magazine coverage', 'influencer marketing London', 'brand partnerships', 'paid social advertising', 'Meta ads for fashion brands', 'Instagram ads', 'TikTok ads', 'Google Ads', 'digital marketing for fashion brands', 'brand promotion', 'launch campaign', 'brand awareness campaign', 'performance marketing' ),
		),

		/* ── Legal ────────────────────────────────────────────────── */

		'page:privacy-policy'                     => array(
			't' => 'Privacy Policy | AK Brand Development Studio',
			'd' => 'How AK Brand Development Studio handles the information you send through this website: briefs, emails, spam checks, analytics and your rights under UK GDPR.',
		),
		'page:cookie-policy'                      => array(
			't' => 'Cookie Policy | AK Brand Development Studio',
			'd' => 'Which cookies the AK Brand Development Studio website uses, what they do, how long they are kept and how to change your choice at any time.',
		),

		/* ── Websites ─────────────────────────────────────────────── */

		'project:show-me-your-nails'              => array(
			't' => 'Nail Salon Website Design: Show Me Your Nails, Marylebone',
			'd' => 'Nail salon website design for Show Me Your Nails in Marylebone, London: treatments, prices and online booking a few taps away on a phone.',
			'k' => 'nail salon website',
			's' => array( 'nail salon website design', 'beauty salon website', 'salon website London' ),
			'r' => array( 'salon booking website', 'Show Me Your Nails' ),
			'x' => array( 'nail salon website design', 'nail salon website', 'beauty salon website design', 'salon website London', 'salon booking website', 'website with online booking', 'Show Me Your Nails', 'showmeyournails.co.uk', 'nail salon Marylebone', 'Russian manicure London', 'beauty salon Marylebone', 'Seymour Place' ),
		),
		'project:lenie-boya'                      => array(
			't' => 'Luxury Womenswear Online Store: Lenie Boya | AK Studio',
			'd' => 'A luxury womenswear online store for Lenie Boya, the British label: collections, bespoke services and checkout, designed and built by our London studio.',
			'k' => 'luxury womenswear online store',
			's' => array( 'luxury fashion online store', 'luxury fashion website', 'fashion ecommerce website' ),
			'r' => array( 'Lenie Boya', 'online store for fashion brands' ),
			'x' => array( 'luxury fashion online store', 'luxury fashion website design', 'womenswear online store', 'fashion ecommerce website', 'online store for fashion brands', 'British luxury womenswear', 'Lenie Boya', 'lenieboya.com', 'bespoke fashion website' ),
		),
		'project:wolax-interiors'                 => array(
			't' => 'Interiors Company Website: Wolax Interiors London',
			'd' => 'A website for Wolax Interiors, a London company that designs, makes and installs bespoke furniture and fit-outs for offices, hospitality and homes.',
			'k' => 'interiors company website',
			's' => array( 'interior design website', 'furniture company website', 'fit-out company website' ),
			'r' => array( 'Wolax Interiors', 'bespoke furniture London' ),
			'x' => array( 'interiors company website', 'interior design website', 'furniture company website', 'fit-out company website', 'bespoke furniture London', 'interior fit-out London', 'Wolax Interiors', 'wolax.co.uk', 'website for construction and interiors' ),
		),
		'project:keka-website'                    => array(
			't' => 'Fashion Brand Website: KEKA | AK Brand Development Studio',
			'd' => 'A film-led fashion brand website for KEKA: runway films, lookbooks and news, with an online store on the way. Designed and built by our London studio.',
			'k' => 'fashion brand website',
			's' => array( 'fashion label website', 'brand website with lookbooks', 'fashion website design' ),
			'r' => array( 'KEKA', 'lookbook website' ),
			'x' => array( 'fashion brand website', 'fashion label website', 'fashion website design', 'lookbook website', 'film-led website', 'KEKA', 'keka.design', 'KEKA fashion brand', 'gender-free fashion brand' ),
		),
		'project:ultimate-trend-store'            => array(
			't' => 'Designer Clothing Online Store: Ultimate Trend Store',
			'd' => 'A designer clothing online store for Ultimate Trend Store: clothing, shoes and accessories, shipping worldwide from London. Designed and built by us.',
			'k' => 'designer clothing online store',
			's' => array( 'fashion ecommerce website', 'online clothing store', 'fashion online shop' ),
			'r' => array( 'Ultimate Trend Store', 'ecommerce website design London' ),
			'x' => array( 'fashion ecommerce website', 'online clothing store', 'designer clothing online store', 'fashion online shop design', 'ecommerce website design London', 'Ultimate Trend Store', 'utrendstore.co.uk', 'worldwide shipping fashion store' ),
		),
		'project:prominent-magazine'              => array(
			't' => 'Fashion Magazine Website: PROMINENT Magazine London',
			'd' => 'A fashion magazine website for PROMINENT, the London fashion and culture magazine: collections, designer interviews, jewellery and beauty.',
			'k' => 'fashion magazine website',
			's' => array( 'magazine website design', 'online magazine website', 'editorial website' ),
			'r' => array( 'PROMINENT Magazine', 'London fashion magazine' ),
			'x' => array( 'fashion magazine website', 'magazine website design', 'online magazine website', 'editorial website design', 'London fashion magazine', 'PROMINENT Magazine', 'prominentmagazine.co.uk', 'fashion and culture magazine' ),
		),
		'project:fashion-frontier'                => array(
			't' => 'Fashion News Website: Fashion Frontier | AK Studio',
			'd' => 'A fashion news website for Fashion Frontier: independent runway reports, designer profiles and culture stories from London, designed and built by us.',
			'k' => 'fashion news website',
			's' => array( 'news website design', 'fashion news site', 'independent fashion news' ),
			'r' => array( 'Fashion Frontier', 'news website design' ),
			'x' => array( 'fashion news website', 'news website design', 'independent fashion news', 'runway reports', 'designer profiles', 'Fashion Frontier', 'fashionfrontier.uk', 'London fashion news' ),
		),
		'project:coolbaba'                        => array(
			't' => 'COOL’BABA: Ukrainian Online Magazine Website',
			'd' => 'COOL’BABA, the Ukrainian online magazine about fashion, culture, beauty and lifestyle founded by Andrii Karakushan in 2009: website and editorial platform.',
			'k' => 'Ukrainian online magazine',
			's' => array( 'COOLBABA', 'COOL’BABA magazine', 'Ukrainian fashion magazine' ),
			'r' => array( 'COOLBABA', 'Ukrainian fashion media' ),
			'x' => array( 'Ukrainian online magazine', 'COOLBABA', 'COOL’BABA', 'coolbaba.in.ua', 'Ukrainian fashion magazine', 'Ukrainian fashion media', 'Ukrainian lifestyle magazine', 'Andrii Karakushan', 'media partner London Fashion Day', 'Ukrainian fashion media London' ),
		),
		'project:london-fashion-day-website'      => array(
			't' => 'London Fashion Day Website: Registration & Tickets',
			'd' => 'The London Fashion Day website: season news, programmes, guest registration and tickets — designed, built and run by AK Brand Development Studio.',
			'k' => 'London Fashion Day website',
			's' => array( 'londonfashionday.co.uk', 'fashion event website', 'event registration website' ),
			'r' => array( 'event website with registration', 'fashion week website' ),
			'x' => array( 'London Fashion Day website', 'londonfashionday.co.uk', 'fashion event website', 'fashion week website', 'event registration website', 'event website with tickets', 'guest registration system', 'designer application form' ),
		),
		'project:odessa-fashion-day-website'      => array(
			't' => 'Odessa Fashion Day: a Bilingual Website | AK Studio',
			'd' => 'A bilingual website for Odessa Fashion Day: news, season programmes and registration in Ukrainian and English, designed and built by our studio.',
			'k' => 'bilingual website',
			's' => array( 'bilingual website design', 'multilingual website', 'Ukrainian and English website' ),
			'r' => array( 'Odessa Fashion Day website', 'fashion week website' ),
			'x' => array( 'bilingual website design', 'multilingual website', 'Ukrainian and English website', 'Odessa Fashion Day website', 'ofd.org.ua', 'fashion week website', 'event registration website' ),
		),

		/* ── Photo & film ─────────────────────────────────────────── */

		'project:campaign-film-westminster'       => array(
			't' => 'Fashion Campaign Film on Westminster Bridge, London',
			'd' => 'A fashion campaign film shot in London at sunrise on Westminster Bridge — one red tulle look, Big Ben and the river — for London Fashion Day, spring 2024.',
			'k' => 'fashion campaign film London',
			's' => array( 'fashion film London', 'campaign film', 'Westminster Bridge fashion shoot' ),
			'r' => array( 'fashion film production', 'London Fashion Day campaign' ),
			'x' => array( 'fashion campaign film London', 'fashion film London', 'fashion film production', 'campaign film', 'Westminster Bridge fashion shoot', 'sunrise fashion shoot London', 'Big Ben fashion film', 'London Fashion Day campaign', 'red tulle dress' ),
		),

		/* ── Runway seasons ───────────────────────────────────────── */

		'project:london-fashion-day-season-8'     => array(
			't' => 'London Fashion Day Season 8: PAST. PRESENT. NEXT.',
			'd' => 'London Fashion Day Season 8 at Porchester Hall, 8 November 2026: twelve fashion voices and a campaign built around a student designer and an emerging model.',
			'k' => 'London Fashion Day Season 8',
			's' => array( 'LFD Season 8 campaign', 'PAST PRESENT NEXT', 'London Fashion Day 2026 campaign' ),
			'r' => array( 'London Fashion Day campaign', 'Porchester Hall fashion show' ),
			'x' => array( 'London Fashion Day Season 8', 'London Fashion Day Season 8 campaign', 'London Fashion Day official campaign', 'PAST PRESENT NEXT', 'PAST PRESENT NEXT fashion concept', 'Porchester Hall fashion show', 'London Fashion Day 8 November 2026', 'fashion campaign London 2026', 'student designer campaign', 'emerging model London' ),
		),
		'project:london-fashion-day-season-7'     => array(
			't' => 'London Fashion Day Season 7: Fashion Beyond Borders',
			'd' => 'London Fashion Day Season 7: a campaign film shot at sunrise on Regent Street and a day of shows at St John’s Church, Hyde Park, on 1 May 2026.',
			'k' => 'London Fashion Day Season 7',
			's' => array( 'LFD Season 7', 'Fashion Beyond Borders', 'London Fashion Day May 2026' ),
			'r' => array( 'Regent Street fashion film', 'Hyde Park fashion show' ),
			'x' => array( 'London Fashion Day Season 7', 'LFD Season 7', 'Fashion Beyond Borders', 'London Fashion Day May 2026', 'Regent Street fashion film', 'Regent Street campaign shoot', 'St John’s Church Hyde Park fashion show', 'Hyde Park fashion show' ),
		),
		'project:london-fashion-day-season-6'     => array(
			't' => 'London Fashion Day Season 6: The Art of Being',
			'd' => 'London Fashion Day Season 6, The Art of Being: authenticity as the new luxury, a record number of guests and a pop-up space in Hyde Park, 1 November 2025.',
			'k' => 'London Fashion Day Season 6',
			's' => array( 'LFD Season 6', 'The Art of Being', 'London Fashion Day November 2025' ),
			'r' => array( 'Hyde Park fashion show', 'fashion pop-up London' ),
			'x' => array( 'London Fashion Day Season 6', 'LFD Season 6', 'The Art of Being', 'London Fashion Day November 2025', 'authenticity new luxury fashion', 'Hyde Park fashion show', 'fashion pop-up London' ),
		),
		'project:london-fashion-day-season-5'     => array(
			't' => 'London Fashion Day Season 5: HUMAN, with KEKA',
			'd' => 'London Fashion Day Season 5, HUMAN: a season that put the person, not the standard, at the front of fashion, with an official campaign made with KEKA.',
			'k' => 'London Fashion Day Season 5',
			's' => array( 'LFD Season 5', 'London Fashion Day HUMAN', 'London Fashion Day April 2025' ),
			'r' => array( 'KEKA campaign', 'fashion campaign London' ),
			'x' => array( 'London Fashion Day Season 5', 'LFD Season 5', 'London Fashion Day HUMAN', 'London Fashion Day April 2025', 'KEKA campaign', 'KEKA London Fashion Day', 'St John’s Church Hyde Park fashion show' ),
		),
		'project:london-fashion-day'              => array(
			't' => 'London Fashion Day — Runway for Independent Designers',
			'd' => 'London Fashion Day is an independent international runway for emerging and independent designers: eight seasons in London since 2023, managed by our studio.',
			'k' => 'London Fashion Day',
			's' => array( 'LFD', 'LFD London', 'London Fashion Day runway' ),
			'r' => array( 'independent designers London', 'international runway London', 'emerging designers London' ),
			'x' => array( 'London Fashion Day', 'LFD', 'LFD London', 'London Fashion Day runway', 'London Fashion Day 2026', 'London Fashion Day seasons', 'international runway London', 'independent designers London', 'emerging designers London', 'Ukrainian designers London', 'fashion events London', 'London fashion shows 2026', 'Porchester Hall', 'St John’s Church Hyde Park', 'Kostiantyn Lieontiev', 'Andrii Karakushan' ),
		),
		'project:odessa-fashion-day'              => array(
			't' => 'Odessa Fashion Day — 33 Seasons of Ukrainian Fashion',
			'd' => 'Odessa Fashion Day: a runway for Ukrainian designers since 2010, with 33 seasons in Odesa and a summer edition since 2015, managed by our studio.',
			'k' => 'Odessa Fashion Day',
			's' => array( 'OFD', 'Odesa Fashion Day', 'Одеса Фешн Дей' ),
			'r' => array( 'Ukrainian fashion week', 'Ukrainian designers' ),
			'x' => array( 'Odessa Fashion Day', 'OFD', 'Odesa Fashion Day', 'Odessa Fashion Day 2026', 'Ukrainian fashion week', 'Ukrainian designers', 'Ukrainian fashion brands', 'fashion week Odesa', 'Одеса Фешн Дей', 'Одесса Фешн Дей', 'Fashion Shopping Place', 'SUMMER WEEKEND by OFD', 'Oleh Shkilniuk' ),
		),
		'project:odessa-fashion-day-season-32'    => array(
			't' => 'Odessa Fashion Day Season 32: ONE OF A KIND',
			'd' => 'Odessa Fashion Day Season 32, ONE OF A KIND: the value of individuality, with a campaign starring Miss Ukraine Kateryna Zakharchenko, 16–17 May 2026.',
			'k' => 'Odessa Fashion Day Season 32',
			's' => array( 'OFD Season 32', 'ONE OF A KIND', 'Odessa Fashion Day May 2026' ),
			'r' => array( 'Kateryna Zakharchenko', 'Miss Ukraine campaign' ),
			'x' => array( 'Odessa Fashion Day Season 32', 'OFD Season 32', 'ONE OF A KIND', 'Odessa Fashion Day May 2026', 'Kateryna Zakharchenko', 'Miss Ukraine campaign', 'Sady Peremohy', 'Luxury Gallery Odesa' ),
		),
		'project:odessa-fashion-day-season-31'    => array(
			't' => 'Odessa Fashion Day Season 31: UNYIELDING',
			'd' => 'Odessa Fashion Day Season 31, UNYIELDING: two days, two landmark Odesa venues and one theme — resilience, strength and creative movement forward.',
			'k' => 'Odessa Fashion Day Season 31',
			's' => array( 'OFD Season 31', 'UNYIELDING', 'Odessa Fashion Day November 2025' ),
			'r' => array( 'Londonska Hotel fashion show', 'Ukrainian fashion resilience' ),
			'x' => array( 'Odessa Fashion Day Season 31', 'OFD Season 31', 'UNYIELDING', 'Odessa Fashion Day November 2025', 'Londonska Hotel fashion show', 'Sady Peremohy', 'Ukrainian fashion resilience' ),
		),
		'project:summer-weekend-by-ofd-2026'      => array(
			't' => 'SUMMER WEEKEND by OFD 2026 — Summer Shows in Odesa',
			'd' => 'SUMMER WEEKEND by OFD 2026: two days of summer and cruise collections in Odesa, the summer edition of Odessa Fashion Day held since 2015.',
			'k' => 'SUMMER WEEKEND by OFD',
			's' => array( 'Summer Weekend Odesa', 'OFD summer edition', 'Odessa Fashion Day summer' ),
			'r' => array( 'summer collections show', 'cruise collections' ),
			'x' => array( 'SUMMER WEEKEND by OFD', 'SUMMER WEEKEND by OFD 2026', 'Summer Weekend Odesa', 'OFD summer edition', 'Odessa Fashion Day summer', 'summer and cruise collections', 'MarAmax Academy', 'Botanical Garden of Odesa University', 'DeClassic' ),
		),

		/* ── Brands ───────────────────────────────────────────────── */

		'project:keka'                            => array(
			't' => 'KEKA Fashion Brand — Positioning, Campaigns & Runway',
			'd' => 'KEKA, a fashion brand beyond gender, age and convention: on the runway in London and Milan, with positioning, campaigns and website by our studio.',
			'k' => 'KEKA fashion brand',
			's' => array( 'KEKA', 'KEKA label', 'KEKA design' ),
			'r' => array( 'gender-free fashion', 'brand development for fashion labels' ),
			'x' => array( 'KEKA fashion brand', 'KEKA', 'KEKA label', 'keka.design', 'gender-free fashion', 'genderless fashion brand', 'Ukrainian fashion brand', 'Fashion For Freedom Milan', 'Milan Fashion Week 2025', 'brand development for fashion labels', 'fashion brand international market' ),
		),

		/* ── Journal ──────────────────────────────────────────────── */

		'post:london-fashion-day-season-8-porchester-hall' => array(
			't' => 'LFD Season 8: London Fashion Day at Porchester Hall',
			'd' => 'LFD Season 8: London Fashion Day returns to Porchester Hall on 8 November 2026 with twelve fashion voices, the PAST. PRESENT. NEXT. concept and tickets.',
			'k' => 'LFD Season 8',
			's' => array( 'London Fashion Day 2026', 'London Fashion Day Season 8', 'London Fashion Day Porchester Hall', 'LFD 2026', 'London Fashion Day November 2026', 'London Fashion Day Season Eight' ),
			'r' => array( 'London Fashion Day registration', 'Porchester Hall fashion show', 'London fashion events November 2026' ),
			'x' => array( 'London Fashion Day', 'London Fashion Day 2026', 'London Fashion Day Season 8', 'London Fashion Day 8 November 2026', 'LFD 2026', 'LFD Season 8', 'LFD 8th season', 'London Fashion Day Porchester Hall', 'London Fashion Day PAST PRESENT NEXT', 'London Fashion Day registration', 'London Fashion Day guest registration', 'London Fashion Day tickets', 'London Fashion Day runway schedule', 'London Fashion Day designers', 'London Fashion Day fashion show', 'London Fashion Day emerging designers', 'London Fashion Day independent designers', 'London fashion events November 2026', 'London fashion shows November 2026', 'Porchester Hall fashion show', 'Porchester Hall London fashion event', 'Porchester Hall Bayswater', 'fashion events Westminster London', 'independent fashion London', 'emerging fashion designers UK', 'international runway London', 'London runway November 2026', 'LFD designers lineup', 'London Fashion Day LCCA Showcase', 'London College of Contemporary Arts', 'Tiarael Fashion', 'STUDIO CATA', 'Heymers Education', 'Erzindjan’s', 'Tasha Ivy', 'NILDET', 'SHICHO', 'SULITUDE', 'Yanou Santangelo', 'NIKA', 'LEV3L UP', 'fashion heritage and identity', 'fashion craftsmanship', 'next generation fashion designers', 'fashion future London', 'fashion and cultural heritage', 'PAST PRESENT NEXT fashion concept', 'London Fashion Day Gabrielle Nitu', 'Gabrielle Nitu designer', 'Dinara Ostr', 'Mariana Zabramna', 'Kostiantyn Lieontiev', 'Костянтин Лєонтьєв', 'Андрій Каракушан', 'Andrii Karakushan', 'AK Brand Development Studio', 'London Fashion Day official campaign', 'London Fashion Day fashion film', 'fashion campaign London 2026', 'emerging creative talents London', 'fashion education UK', 'international fashion platform UK', 'Ukrainian fashion media London', 'COOLBABA media partner London Fashion Day', 'Odessa Fashion Day international partner', 'Georgia Fashion Week international partner', 'independent fashion brands UK', 'fashion events London 2026', 'London fashion runway 2026', 'fashion media London', 'London Fashion Day news', 'London Fashion Day programme', 'London Fashion Day November 8' ),
		),
		'post:summer-weekend-by-ofd-2026-went-ahead' => array(
			't' => 'SUMMER WEEKEND by OFD 2026: the Second Day Went Ahead',
			'd' => 'After a night of missile and drone attacks on Odesa, the team behind SUMMER WEEKEND by OFD 2026 decided together to carry on with the shows on 9 August.',
			'k' => 'SUMMER WEEKEND by OFD 2026',
			's' => array( 'Summer Weekend Odesa 2026', 'Odessa Fashion Day August 2026', 'OFD summer 2026' ),
			'r' => array( 'Odessa Fashion Day news', 'fashion in wartime Ukraine' ),
			'x' => array( 'SUMMER WEEKEND by OFD 2026', 'Summer Weekend Odesa 2026', 'Odessa Fashion Day August 2026', 'OFD summer 2026', 'Odessa Fashion Day news', 'fashion in wartime Ukraine', 'Ukrainian fashion resilience', 'MarAmax Academy', 'DeClassic', 'Anastasiia Mykytyn', 'Oleh Shkilniuk' ),
		),
		'post:what-it-costs-to-show-at-a-fashion-week' => array(
			't' => 'How Much Does It Cost to Show at a Fashion Week?',
			'd' => 'What it really costs to show at a fashion week: the runway slot, models, styling, shoot, travel and press — where the money goes and what a show is worth.',
			'k' => 'cost to show at a fashion week',
			's' => array( 'fashion week show cost', 'how much does a fashion show cost', 'runway show cost', 'fashion week participation fee' ),
			'r' => array( 'runway slot price', 'show at London Fashion Day', 'fashion show budget' ),
			'x' => array( 'cost to show at a fashion week', 'how much does it cost to show at fashion week', 'how much does a fashion show cost', 'fashion week show cost', 'runway show cost', 'runway slot price', 'fashion week participation fee', 'fashion show budget', 'fashion show budget template', 'cost of a catwalk show', 'model fees fashion show', 'lookbook shoot cost', 'emerging designer fashion week', 'is fashion week worth it', 'show at London Fashion Day', 'London Fashion Day designers' ),
		),
		'post:design-the-identity-at-ad-size-first' => array(
			't' => 'Brand Identity for Social Media: Design at Ad Size First',
			'd' => 'On social media a brand identity lives at 1080 pixels wide, cropped to 4:5, behind a play button. Why we design for the feed first, the board last.',
			'k' => 'brand identity for social media',
			's' => array( 'identity design for Instagram', 'logo for social media', 'brand identity in the feed' ),
			'r' => array( 'brand identity design London', 'fashion brand identity' ),
			'x' => array( 'brand identity for social media', 'identity design for Instagram', 'logo for social media', 'brand identity in the feed', 'social media branding', 'fashion brand identity', 'Instagram 4:5 crop', 'brand identity design London', 'identity design advice' ),
		),
		'post:your-company-is-not-your-personal-brand' => array(
			't' => 'Personal Brand vs Company Brand: Two Different Jobs',
			'd' => 'The founder and the company are two media properties with two different jobs. How to build a personal brand without the company stalling — and vice versa.',
			'k' => 'personal brand vs company brand',
			's' => array( 'founder brand', 'personal branding for founders', 'company brand' ),
			'r' => array( 'personal brand strategy', 'brand strategy agency London' ),
			'x' => array( 'personal brand vs company brand', 'personal branding for founders', 'founder brand', 'founder-led brand', 'personal brand strategy', 'company brand strategy', 'personal branding London', 'brand strategy agency London' ),
		),
		'post:fix-the-website-before-you-buy-the-traffic' => array(
			't' => 'Ads Not Converting? Fix the Website Before Buying Traffic',
			'd' => 'Ads not converting? Every pound of promotion lands on a page. If it is slow or unclear, ads buy a faster exit. What to fix on the website first.',
			'k' => 'ads not converting',
			's' => array( 'website conversion', 'landing page conversion', 'paid ads not working' ),
			'r' => array( 'website speed on mobile', 'website design London' ),
			'x' => array( 'ads not converting', 'paid ads not working', 'website conversion', 'website conversion rate', 'landing page conversion', 'website speed on mobile', 'slow website', 'fix website before ads', 'Meta ads landing page', 'Google Ads landing page', 'website design London' ),
		),
	);
}
