<?php
/**
 * Launch pages. Copy is written for clients, not as placeholder: every
 * claim about the studio's record comes from the platforms' own pages.
 *
 * Keys: title, slug, parent (slug), excerpt (lead paragraph), eyebrow,
 * heading (display heading, *word* = italic accent), movement, order,
 * template, content (block markup).
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<int,array<string,mixed>>
 */
function ak_brand_seed_pages(): array {
	// Shortcodes, so the address and the legal name follow Settings → AK Studio.
	$mail    = '[ak_studio_email]';
	$lfd     = '<a href="https://londonfashionday.co.uk/">London Fashion Day</a>';
	$apply   = 'https://londonfashionday.co.uk/designers-reg/';
	$updated = '2 November 2026';

	$pages = array();

	/* ── Home ─────────────────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Home',
		'slug'    => 'home',
		'order'   => 0,
		'excerpt' => 'An independent London studio for fashion and lifestyle brands. We build the brand, shoot the campaign, produce the show and get it seen — and we organise London Fashion Day, so we know the runway from the inside.',
		'eyebrow' => 'The studio',
		'heading' => 'Most agencies hand you a deck. *We hand you the show.*',
		'content' =>
			ak_b_p( 'AK is two founders, one letter each: Andrii Karakushan and Kostiantyn Lieontiev. Between us, a fashion producer who founded two runway platforms and a fashion label, and a digital entrepreneur who builds brands, websites and media.' ) .
			ak_b_p( 'Strategy, identity, photo and film, shows, websites, PR and paid media sit under one roof — so the idea agreed in the first meeting is the one that walks the runway, fills the feed and lands in the press.' ),
	);

	/* ── Services hub ─────────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Services',
		'slug'    => 'services',
		'order'   => 1,
		'eyebrow' => 'What we do',
		'heading' => 'Everything a brand needs to be seen — *under one roof.*',
		'excerpt' => 'Six disciplines, one team. Take a single service, or hand us the whole journey from first idea to international presence.',
		'content' =>
			ak_b_h( 'One brief, one team' ) .
			ak_b_p( 'Most growing brands end up managing five suppliers: a strategist, a designer, a photographer, a show producer and an agency for ads. Each does good work; none of it adds up. We run all six disciplines in-house and in sequence, so the positioning decided in week one is the one that appears on the runway, on the website and in the ad.' ) .
			ak_b_p( 'It also means one person accountable for the result. Every project is led by one of the founders, with the same crews, venues and media partners we rely on for our own seasons.' ) .
			ak_b_h( 'Ways to work with us' ) .
			ak_b_list(
				array(
					'<strong>A single project.</strong> A brand identity, a campaign shoot, a show, a website — clear scope, clear deadline.',
					'<strong>A full season.</strong> Strategy, campaign, runway and press planned backwards from one date: a show, a launch, an opening.',
					'<strong>Ongoing presence.</strong> PR, social and paid media month by month, reported in plain numbers.',
				),
				false,
				'is-style-cards'
			) .
			ak_b_faq(
				array(
					array( 'Do you only work with fashion brands?', 'Fashion is where we started and where most of our work is. The same disciplines serve lifestyle, beauty and founder-led businesses — if your brand needs to look right and be seen, we can help.' ),
					array( 'Can you get my collection into London Fashion Day?', 'London Fashion Day is curated: designer applications are reviewed individually, and selected designers receive a participation proposal with the format and fee. Alongside the show, we can prepare your brand to present — positioning, lookbook, campaign and press kit — so you arrive ready. See <a href="/show-in-london/">Show in London</a>.' ),
					array( 'What does it cost?', 'It depends on the scope, so we don’t publish a price list. Send a brief and you’ll receive a written proposal with the fee before any work begins.' ),
					array( 'Where do you work?', 'The studio is in London. Our founders have produced shows and campaigns in London, Odesa, Paris, Dubai, Vienna and Milan, and we work with brands wherever they are.' ),
					array( 'How soon can you start?', 'It depends on the season and the scope. If you are working towards a fixed date — a show, a launch, a fashion week — put it in the brief and we will tell you honestly whether it can be done well in the time.' ),
				)
			),
	);

	/* ── The six service pages ────────────────────────────────────────── */
	$pages[] = array(
		'title'    => 'Brand strategy',
		'slug'     => 'brand-strategy',
		'parent'   => 'services',
		'order'    => 1,
		'movement' => 'strategy',
		'eyebrow'  => 'Service 01 — Strategy',
		'heading'  => 'Know who you are *before you design anything.*',
		'excerpt'  => 'The work before the logo: what the brand is, who it is for, and why anyone should care. For new labels, relaunches and founders building a personal brand.',
		'content'  =>
			ak_b_h( 'What we do' ) .
			ak_b_p( 'Strategy is where most brands save money and where most of them lose it. A collection without a position looks like everyone else’s on the rail; a founder without a clear story says something different in every interview. We start with the questions a buyer, an editor and a customer will ask — and we don’t move to design until the answers hold.' ) .
			ak_b_p( 'Our strategy is written by people who have had to sell their own tickets, fill front rows and win over press for platforms and a label we built ourselves. It ends in decisions you can act on, not a presentation that sits in a folder.' ) .
			ak_b_h( 'What you get' ) .
			ak_b_list(
				array(
					'Brand concept and positioning',
					'Audience, market and competitor map',
					'Brand philosophy, values and tone of voice',
					'Relaunch or repositioning roadmap',
					'Personal brand strategy for founders and designers: positioning, communication and visual direction',
					'Identity foundations — the brief every designer, photographer and agency works from',
					'A twelve-month plan for growth, in order of priority',
				)
			) .
			ak_b_h( 'How it runs' ) .
			ak_b_steps(
				array(
					array( 'Discovery.', 'Conversations with you and, where useful, with customers, buyers or stockists.' ),
					array( 'Routes.', 'Two or three strategic directions, argued on paper, with the trade-offs spelt out.' ),
					array( 'Decision.', 'One direction, written up as a short brand book you can hand to anyone who works on the brand.' ),
				)
			) .
			ak_b_faq(
				array(
					array( 'Do I need strategy if I already have a logo?', 'Often, yes. A logo is the last answer in a long chain. If the brand isn’t selling, being remembered or being written about, the problem usually sits earlier in that chain — and a new logo won’t fix it.' ),
					array( 'Do you work with individuals?', 'Yes. Personal brand strategy — positioning, communication and visual direction — is part of the practice, for designers, founders and creative professionals.' ),
				)
			),
	);

	$pages[] = array(
		'title'    => 'Brand identity & creative direction',
		'slug'     => 'brand-identity',
		'parent'   => 'services',
		'order'    => 2,
		'movement' => 'identity',
		'eyebrow'  => 'Service 02 — Identity',
		'heading'  => 'An identity that works *at every size.*',
		'excerpt'  => 'How the brand looks and sounds — designed to hold up on a woven label, a runway backdrop and a six-second ad.',
		'content'  =>
			ak_b_h( 'What we do' ) .
			ak_b_p( 'An identity has to survive the real world: a phone screen, a shop window, a press wall, a stitched label, a dark venue. We design for those moments first and the presentation board last, so the system works the first time it meets an Instagram crop.' ) .
			ak_b_p( 'Creative direction keeps it honest after launch. The same eye that designs the identity art-directs the campaign, the show and the website — which is why the brand still looks like itself a season later.' ) .
			ak_b_h( 'What you get' ) .
			ak_b_list(
				array(
					'Logo and identity system',
					'Visual direction: colour, typography, image style',
					'Brand guidelines your team and partners can actually use',
					'Labels, tags, packaging and print designed alongside the digital assets',
					'Visual storytelling for campaigns and social',
					'Creative direction across shoots, shows and digital',
				)
			) .
			ak_b_h( 'How it runs' ) .
			ak_b_steps(
				array(
					array( 'Brief.', 'We start from the strategy — or write a short one with you if it doesn’t exist yet.' ),
					array( 'Routes in real use.', 'Two or three design directions shown where they will live: on a label, a post, a backdrop, a site.' ),
					array( 'System.', 'One route refined into a full identity, with every file and the guidelines to use it.' ),
				)
			) .
			ak_b_faq(
				array(
					array( 'Can you refresh rather than redesign?', 'Yes — and it is often the better choice. We will tell you what is worth keeping and what is holding the brand back.' ),
					array( 'Do you design packaging and print?', 'Yes, as part of the identity: labels, tags, packaging and print are designed with the digital assets so everything matches.' ),
				)
			),
	);

	$pages[] = array(
		'title'    => 'Photo & film production',
		'slug'     => 'photo-video-production',
		'parent'   => 'services',
		'order'    => 3,
		'movement' => 'image',
		'eyebrow'  => 'Service 03 — Image',
		'heading'  => 'Campaigns and films, *produced end to end.*',
		'excerpt'  => 'Campaigns, lookbooks and films — concept, casting, crew and post-production, from the team that produces London Fashion Day’s own campaigns.',
		'content'  =>
			ak_b_h( 'What we do' ) .
			ak_b_p( 'Every London Fashion Day season begins with a campaign: a concept, a location, a designer’s look, a model, a film. Season 7 was shot at sunrise on Regent Street. Season 8 was shot in a studio washed in blue, around the work of a student designer from the London College of Contemporary Arts. We produce those shoots ourselves, and we produce yours the same way.' ) .
			ak_b_p( 'That means one producer from moodboard to delivery, a crew we have worked with across seasons, and footage cut for where it will actually live — vertical for social, landscape for the website, stills for press.' ) .
			ak_b_h( 'What you get' ) .
			ak_b_list(
				array(
					'Campaign concept and creative direction',
					'Photo campaigns and lookbooks',
					'Promotional and campaign films',
					'Casting, styling, hair and make-up',
					'Locations, permissions and production management',
					'Edited stills and cuts for web, social and press',
				)
			) .
			ak_b_h( 'How it runs' ) .
			ak_b_steps(
				array(
					array( 'Concept.', 'A moodboard, a shot list and a plan for every format you need.' ),
					array( 'Pre-production.', 'Casting, crew, location and schedule — confirmed in writing.' ),
					array( 'Shoot.', 'One day or several, run by a producer who knows the brief.' ),
					array( 'Delivery.', 'Edit, colour grade and final files, sized for every channel.' ),
				)
			) .
			ak_b_faq(
				array(
					array( 'Can you shoot outside London?', 'Yes. We have produced shoots and shows in London, Odesa and beyond, travelling with a core team or hiring locally.' ),
					array( 'Can you work with our own photographer?', 'Of course. We can produce and art-direct a shoot with your photographer, or bring the whole crew.' ),
				)
			),
	);

	$pages[] = array(
		'title'    => 'Fashion show & event production',
		'slug'     => 'fashion-show-production',
		'parent'   => 'services',
		'order'    => 4,
		'movement' => 'experience',
		'eyebrow'  => 'Service 04 — Experience',
		'heading'  => 'Shows produced by the people *who run the runway.*',
		'excerpt'  => 'Runway shows, presentations, launches and events — produced by the team behind London Fashion Day and Odessa Fashion Day.',
		'content'  =>
			ak_b_h( 'What we do' ) .
			ak_b_p( 'We produce runway seasons in London and Odesa: in St John’s Church, Hyde Park; in Westminster’s Porchester Hall, opened in 1929; in a historic Odesa hotel; and in a university botanical garden. We know the running order measured in minutes, the fittings and rehearsals nobody budgets for, and which corners cannot be cut.' ) .
			ak_b_p( 'For your brand that means a show, presentation or launch built around the collection rather than the venue: concept and staging, casting and choreography, backstage and front-of-house, guest list and press — and content captured so the evening keeps working long after the lights go down.' ) .
			ak_b_h( 'What you get' ) .
			ak_b_list(
				array(
					'Independent fashion shows and presentations',
					'Show direction, staging and running order',
					'Casting, hair, make-up and backstage teams',
					'Product launches and brand presentations',
					'Creative events, guest lists and front-of-house',
					'Fashion-week-related production in London',
					'Photo and video coverage of the event',
				)
			) .
			ak_b_h( 'How it runs' ) .
			ak_b_steps(
				array(
					array( 'Concept and venue.', 'What the show needs to say, and the room that says it.' ),
					array( 'Production plan.', 'Budget, schedule, suppliers and running order, agreed up front.' ),
					array( 'Casting and rehearsal.', 'Models, fittings, hair and make-up tests, a full run-through.' ),
					array( 'Show day and after.', 'Backstage, front-of-house, press — then the content, edited and delivered.' ),
				)
			) .
			ak_b_faq(
				array(
					array( 'Is this the same as showing at London Fashion Day?', 'No. A London Fashion Day place is by application and selection. This service is for brands that want their own show, presentation or launch. If LFD is what you are after, see <a href="/show-in-london/">Show in London</a>.' ),
					array( 'Can you produce a show outside fashion week?', 'Yes. Independent shows and presentations often land better away from the fashion-week rush, when press and buyers have time to look.' ),
				)
			),
	);

	$pages[] = array(
		'title'    => 'Website development & digital',
		'slug'     => 'website-development',
		'parent'   => 'services',
		'order'    => 5,
		'movement' => 'digital',
		'eyebrow'  => 'Service 05 — Digital',
		'heading'  => 'Websites that turn attention *into orders.*',
		'excerpt'  => 'Websites, online stores and social presence — fast on a phone, clear on the first screen and easy for your team to update.',
		'content'  =>
			ak_b_h( 'What we do' ) .
			ak_b_p( 'A campaign, a show or a press feature sends people somewhere. Too often that somewhere is a slow template that looks like every competitor and answers none of the questions a first-time visitor has. We design and build sites that load fast, say what the brand is on the first screen and make the next step obvious.' ) .
			ak_b_p( 'We build on WordPress and WooCommerce, so you own the platform and can edit it yourself, and we set up the social and content side so the website and the feed tell the same story.' ) .
			ak_b_h( 'What you get' ) .
			ak_b_list(
				array(
					'Website design and development',
					'Online store set-up with WooCommerce',
					'Social media presence and content production',
					'Online brand positioning and search foundations',
					'Hosting, security and launch checklist',
					'Training, so your team can update everything',
				)
			) .
			ak_b_h( 'How it runs' ) .
			ak_b_steps(
				array(
					array( 'Structure.', 'Pages, products and content planned around what visitors need to decide.' ),
					array( 'Design.', 'Designed for the phone first and tested on real devices.' ),
					array( 'Build.', 'Development, content entry, speed and accessibility checks.' ),
					array( 'Launch.', 'Go-live, training and support while the first campaign runs.' ),
				)
			) .
			ak_b_faq(
				array(
					array( 'Which platform do you use?', 'Usually WordPress, with WooCommerce for online stores — open-source, widely supported and yours to keep. If you are already on another platform, we will tell you honestly whether it is worth moving.' ),
					array( 'Can you run our social media too?', 'Yes. Content production and social presence are part of the digital service, and we can run them alongside PR and paid promotion.' ),
				)
			),
	);

	$pages[] = array(
		'title'    => 'PR & promotion',
		'slug'     => 'pr-and-promotion',
		'parent'   => 'services',
		'order'    => 6,
		'movement' => 'visibility',
		'eyebrow'  => 'Service 06 — Visibility',
		'heading'  => 'Be written about. *Be seen.*',
		'excerpt'  => 'Press, partnerships and paid campaigns — two disciplines with equal weight: earning coverage and buying attention that converts.',
		'content'  =>
			ak_b_h( 'Communication & PR' ) .
			ak_b_p( 'London Fashion Day’s eighth season is carried by twenty official media partners, alongside partners from law firms to colleges. We build the same kind of network around your brand: a story worth telling, press materials that get opened, and relationships that last longer than one launch.' ) .
			ak_b_h( 'Promotion & paid media' ) .
			ak_b_p( 'Paid promotion multiplies whatever it lands on, so we run it last and measure it honestly: Google and YouTube for people already searching, Instagram and Facebook for discovery, with budgets planned around your launch dates and reported in plain numbers.' ) .
			ak_b_h( 'What you get' ) .
			ak_b_list(
				array(
					'Marketing and communication strategy',
					'Press materials, media outreach and PR support',
					'Media and brand partnerships',
					'Campaign development',
					'Google and YouTube promotion',
					'Meta advertising: Facebook and Instagram',
					'Audience growth, engagement and monthly reporting',
				)
			) .
			ak_b_h( 'How it runs' ) .
			ak_b_steps(
				array(
					array( 'Story.', 'What is genuinely new, and who needs to hear it.' ),
					array( 'Press and partners.', 'Materials, outreach and partnerships timed to your dates.' ),
					array( 'Paid launch.', 'Campaigns built on a site that is ready for the traffic.' ),
					array( 'Report.', 'Plain numbers, what worked, and what to change next month.' ),
				)
			) .
			ak_b_faq(
				array(
					array( 'Do you guarantee coverage?', 'No honest PR team can. We guarantee the work: a strong story, materials that get opened and a pitch to the right people.' ),
					array( 'Can you run ads on a small budget?', 'Yes, if the website is ready for the traffic. If it isn’t, we will say so — and fix that first.' ),
				)
			),
	);

	/* ── Show in London ───────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Show in London',
		'slug'    => 'show-in-london',
		'order'   => 2,
		'eyebrow' => 'For designers',
		'heading' => 'Your collection, *on a London runway.*',
		'excerpt' => 'We organise London Fashion Day — the independent runway platform for emerging and independent designers. Here is how a place works, and how we can help you arrive ready.',
		'content' =>
			ak_b_h( 'London Fashion Day in short' ) .
			ak_b_p( 'Since 2023, ' . $lfd . ' has given designers from the UK, Europe, Asia, Africa and beyond a professionally produced runway in London, in front of guests, press and industry. Recent seasons took place at St John’s Church, Hyde Park. Season 8 — <em>PAST. PRESENT. NEXT.</em> — takes place on 8 November 2026 at the historic Porchester Hall in Westminster: twelve fashion voices, six runway slots, opening with students of the London College of Contemporary Arts.' ) .
			ak_b_h( 'Who it is for' ) .
			ak_b_list(
				array(
					'Emerging clothing designers',
					'Independent fashion labels',
					'Established brands entering the UK market',
					'Recent fashion graduates',
					'Designers developing a new collection or creative direction',
				)
			) .
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
			ak_b_list(
				array(
					'Collection positioning and a press-ready brand story',
					'Lookbook and campaign shoots',
					'Press kit, media outreach and partnerships',
					'A website or online store ready for the attention a show brings',
					'Show content edited for social, press and buyers',
				)
			) .
			ak_b_buttons(
				array(
					array( 'Apply on londonfashionday.co.uk', $apply, 'is-style-fill' ),
					array( 'Talk to us first', '/contact/?need=runway', 'is-style-outline' ),
				)
			) .
			ak_b_h( 'Showing in Ukraine' ) .
			ak_b_p( '<a href="https://ofd.org.ua/">Odessa Fashion Day</a>, founded by our co-founder Kostiantyn Lieontiev, has presented Ukrainian designers in Odesa since 2010 — thirty-two seasons so far, plus SUMMER WEEKEND by OFD every August. Designers can apply through the OFD website.' ),
	);

	/* ── Studio ───────────────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Studio',
		'slug'    => 'about',
		'order'   => 3,
		'eyebrow' => 'About AK',
		'heading' => 'Two founders. *One letter each.*',
		'excerpt' => 'AK Brand Development Studio is an independent creative and strategic practice in London, working with fashion and lifestyle brands from the initial idea to international presence.',
		'content' =>
			ak_b_h( 'Why AK' ) .
			ak_b_p( 'A is for Andrii Karakushan. K is for Kostiantyn Lieontiev. The studio carries our initials because the work is ours: we lead every project personally, and we bring to it what we learned building runway platforms, a fashion label and a magazine of our own.' ) .
			ak_b_p( 'AK Brand Development Studio brings strategy, identity, photo and film, shows, websites, PR and promotion under one roof — and organises London Fashion Day under our joint leadership.' ) .
			ak_b_h( 'What we believe' ) .
			ak_b_list(
				array(
					'<strong>Production over presentations.</strong> A strategy is only as good as the show, the shoot and the site it produces.',
					'<strong>Talent deserves a door.</strong> We built two platforms so that designers could be seen before they were famous.',
					'<strong>Honest numbers.</strong> We tell clients what a season really costs, what worked and what didn’t.',
				),
				false,
				'is-style-cards'
			) .
			ak_b_quote( 'We cannot decide who will become the next important name in fashion. What we can do is make sure that talent has an opportunity to be seen. Sometimes, all a young designer needs is someone willing to open the door.', 'Kostiantyn Lieontiev, founder of London Fashion Day' ),
	);

	/* ── Contact ──────────────────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Contact',
		'slug'    => 'contact',
		'order'   => 4,
		'eyebrow' => 'Start a project',
		'heading' => 'Tell us what *you’re building.*',
		'excerpt' => 'The more we know, the better the first answer. Tell us about the brand, the date you’re working towards and what you need — we read every brief ourselves.',
		'content' =>
			ak_b_h( 'What happens next' ) .
			ak_b_steps(
				array(
					array( 'We read your brief', 'and come back with questions, or a time to talk.' ),
					array( 'You receive a proposal:', 'scope, team, timeline and fee, in writing.' ),
					array( 'Once you approve it,', 'we book the team and the dates.' ),
				)
			) .
			ak_b_h( 'Prefer email?' ) .
			ak_b_p( 'Write to ' . $mail . '. The studio is in London and works with brands wherever they are.' ) .
			ak_b_h( 'Designers' ) .
			ak_b_p( 'Looking to show your collection at London Fashion Day? Start with <a href="/show-in-london/">Show in London</a>.' ),
	);

	/* ── Journal (posts page) ─────────────────────────────────────────── */
	$pages[] = array(
		'title'   => 'Journal',
		'slug'    => 'journal',
		'order'   => 5,
		'eyebrow' => 'Journal',
		'heading' => 'Notes from the studio *and the runway.*',
		'excerpt' => 'What things really cost, what works, and what’s next on our platforms.',
		'content' => '',
	);

	/* ── Legal ────────────────────────────────────────────────────────── */
	$pages[] = array(
		'title'    => 'Privacy Policy',
		'slug'     => 'privacy-policy',
		'order'    => 20,
		'eyebrow'  => 'Legal',
		'excerpt'  => 'What happens to the information you give us through this website. Last updated ' . $updated . '.',
		'content'  =>
			ak_b_h( 'Who we are' ) .
			ak_b_p( '[ak_controller] (“we”, “us”) is an independent creative and strategic practice based in London, United Kingdom. We are the controller of the personal data described here. You can reach us at ' . $mail . '.' ) .
			ak_b_h( 'What we collect' ) .
			ak_b_list(
				array(
					'<strong>Your brief.</strong> When you use the project brief form we receive your name and email address, and anything else you choose to add: phone number, brand name, website or social link, the services you are interested in, a key date and your message.',
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
			ak_b_p( 'If this policy changes, the new version will appear on this page with a new date.' ),
	);

	$pages[] = array(
		'title'   => 'Cookie Policy',
		'slug'    => 'cookie-policy',
		'order'   => 21,
		'eyebrow' => 'Legal',
		'excerpt' => 'Which cookies this site uses, and how to change your choice. Last updated ' . $updated . '.',
		'content' =>
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
			ak_b_p( 'Questions? Write to ' . $mail . '.' ),
	);

	return (array) apply_filters( 'ak_brand_seed_pages', $pages );
}
