<?php
/**
 * Launch journal: the four essays already published on akbrand.studio, plus
 * two news notes from the studio's platforms.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<int,array<string,mixed>>
 */
function ak_brand_seed_posts(): array {
	// The essays are kept as plain HTML and arrive in the editor as paragraph and heading blocks.
	$file = static function ( string $slug ): string {
		$path = AK_BRAND_CORE_DIR . 'seed/journal/' . $slug . '.html';
		$html = is_readable( $path ) ? (string) file_get_contents( $path ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local seed file.
		preg_match_all( '#<(p|h2|h3)>(.*?)</\1>#s', $html, $parts, PREG_SET_ORDER );
		$out = '';
		foreach ( $parts as $el ) {
			$out .= 'p' === $el[1] ? ak_b_p( trim( $el[2] ) ) : ak_b_h( trim( $el[2] ), (int) substr( $el[1], 1 ) );
		}
		return $out;
	};

	$posts = array(
		array(
			'slug'     => 'london-fashion-day-season-8-porchester-hall',
			'title'    => 'London Fashion Day Season 8 comes to Porchester Hall',
			'date'     => '2026-10-23 10:00:00',
			'category' => 'News',
			'image'    => 'lfd-s8-campaign-profile.jpg',
			'excerpt'  => 'On 8 November London Fashion Day, the platform our studio manages, moves to a 1929 hall in Westminster, with twelve fashion voices and a campaign built around a student designer.',
			'content'  =>
				ak_b_p( 'The eighth season of <a href="/work/london-fashion-day/">London Fashion Day</a> takes place on Sunday 8 November 2026 at Porchester Hall in Westminster — six runway slots from 13:00 to 18:00, twelve fashion voices, and the first season in a venue that opened in 1929.' ) .
				ak_b_p( 'The theme is <em>PAST. PRESENT. NEXT.</em>: cultural memory and craftsmanship, the moment they meet technology, and the generation that decides what fashion keeps. The campaign follows the idea through — a look by Gabrielle Nitu, a student at the London College of Contemporary Arts, worn by a young model at the start of her career, filmed by Dinara Ostr and photographed by Mariana Zabramna.' ) .
				ak_b_p( 'The day opens with LCCA students, followed by Tiarael Fashion, STUDIO CATA, Heymers Education, Erzindjan’s, Tasha Ivy, NILDET, SHICHO, SULITUDE, Yanou Santangelo, NIKA and LEV3L UP.' ) .
				ak_b_quote( 'We cannot decide who will become the next important name in fashion. What we can do is make sure that talent has an opportunity to be seen.', 'Kostiantyn Lieontiev, London Fashion Day' ) .
				ak_b_p( 'Guests choose their shows and request tickets on the <a href="https://londonfashionday.co.uk/guest-registration/">official guest registration page</a>. Designers interested in future seasons can start with <a href="/show-in-london/">Show in London</a>. The full season is on the <a href="/work/london-fashion-day-season-8/">project page</a>.' ),
		),
		array(
			'slug'     => 'summer-weekend-by-ofd-2026-went-ahead',
			'title'    => 'SUMMER WEEKEND by OFD 2026: the second day went ahead',
			'date'     => '2026-08-25 10:00:00',
			'category' => 'News',
			'image'    => 'sw26-botanical-garden.jpg',
			'excerpt'  => 'After a night of missile and drone attacks on Odesa, the team behind Odessa Fashion Day decided together to carry on with the shows.',
			'content'  =>
				ak_b_p( 'SUMMER WEEKEND by OFD is the summer project of <a href="/work/odessa-fashion-day/">Odessa Fashion Day</a>, the runway platform our studio manages. First held in 2015, this year it opened on 8 August at MarAmax Academy with DeClassic, the label of designer Anastasiia Mykytyn.' ) .
				ak_b_p( 'During the night of 8 to 9 August, Odesa and the surrounding region came under a large-scale combined missile and drone attack. The team, led by OFD producer Oleh Shkilniuk, seriously considered cancelling the second day. The decision to continue was made collectively — and the next morning designers arrived at the Botanical Garden of Odesa I. I. Mechnikov National University with their collections, joined by models, make-up artists, stylists, photographers, partners and guests.' ) .
				ak_b_p( 'The summer and cruise collections came from PANACEA, LUMÉA, Yankovska Helen &amp; Lady Forever, Soul Swim, PODOLSKA, Nimble Scissors and KEOSMY by yesundress.' ) .
				ak_b_quote( 'After a night like that, the question was no longer about organising a fashion event. The question was whether we had the moral and physical strength to continue. And the answer came from the people themselves.', 'Kostiantyn Lieontiev, founder of Odessa Fashion Day' ) .
				ak_b_p( 'See the <a href="/work/summer-weekend-by-ofd-2026/">season’s photographs</a>.' ),
		),
		array(
			'slug'     => 'what-it-costs-to-show-at-a-fashion-week',
			'title'    => 'What it actually costs to show at a fashion week',
			'date'     => '2026-08-24 12:00:00',
			'category' => 'Notes',
			'image'    => 'lfd-s7-backstage-lacing.jpg',
			'excerpt'  => 'The honest breakdown a young designer cannot find anywhere else — what a runway slot really costs, where the money actually goes, and what a show is genuinely worth.',
			'content'  => $file( 'what-it-costs-to-show-at-a-fashion-week' ),
		),
		array(
			'slug'     => 'design-the-identity-at-ad-size-first',
			'title'    => 'Design the identity at ad size first',
			'date'     => '2026-08-17 12:00:00',
			'category' => 'Notes',
			'image'    => 'lfd-s8-campaign-close-up.jpg',
			'excerpt'  => 'An identity spends its real life at 1080 pixels wide, cropped to 4:5, behind a play button. Design it there first.',
			'content'  => $file( 'design-the-identity-at-ad-size-first' ),
		),
		array(
			'slug'     => 'your-company-is-not-your-personal-brand',
			'title'    => 'Your company is not your personal brand',
			'date'     => '2026-08-10 12:00:00',
			'category' => 'Notes',
			'image'    => 'lfd-s6-kostiantyn-lieontiev-backstage.jpg',
			'excerpt'  => 'The founder and the company are two media properties with two different jobs. Confusing them is how brands stall.',
			'content'  => $file( 'your-company-is-not-your-personal-brand' ),
		),
		array(
			'slug'     => 'fix-the-website-before-you-buy-the-traffic',
			'title'    => 'Fix the website before you buy the traffic',
			'date'     => '2026-08-03 12:00:00',
			'category' => 'Notes',
			'image'    => 'lfd-s7-campaign-on-set.jpg',
			'excerpt'  => 'Every pound of promotion lands on a page. If that page is slow or unclear, promotion just buys a faster exit.',
			'content'  => $file( 'fix-the-website-before-you-buy-the-traffic' ),
		),
	);

	return (array) apply_filters( 'ak_brand_seed_posts', $posts );
}
