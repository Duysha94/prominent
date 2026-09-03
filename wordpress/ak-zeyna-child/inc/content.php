<?php
/**
 * The canonical content set.
 *
 * This is the list the theme considers "the site". `inc/sync.php` compares
 * the database against it after every theme update: anything here that is
 * missing gets created, anything here that exists and has not been edited by
 * hand gets refreshed, and anything the theme previously created that has
 * since been dropped from this list gets moved to the Trash.
 *
 * Adding a page, retiring a case study or renaming a journal entry is
 * therefore a matter of editing this file — the next theme update carries
 * the change onto the live site with no import and no manual tidying.
 *
 * Editing safety: a post the founders have changed in wp-admin is never
 * overwritten. Sync stores a hash of exactly what it wrote; if the stored
 * hash no longer matches the post's current content, the post is treated as
 * theirs and left alone.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Every page, case study and journal entry the theme ships.
 *
 * @return array[] Each entry: type, slug, title, content, template, meta, terms, order.
 */
function ak_content_manifest() {
	$pages = array(
		array(
			'type'  => 'page',
			'slug'  => 'home',
			'title' => __( 'Home', 'ak-zeyna-child' ),
			'order' => 1,
		),
		array(
			'type'     => 'page',
			'slug'     => 'work',
			'title'    => __( 'Work', 'ak-zeyna-child' ),
			'order'    => 2,
			'template' => 'page-templates/template-ak-page.php',
			'content'  => '<!-- Fallback: /work/ is rendered by the portfolio archive. -->',
		),
		array(
			'type'     => 'page',
			'slug'     => 'services',
			'title'    => __( 'Services', 'ak-zeyna-child' ),
			'order'    => 3,
			'template' => 'page-templates/template-services.php',
		),
		array(
			'type'  => 'page',
			'slug'  => 'journal',
			'title' => __( 'Journal', 'ak-zeyna-child' ),
			'order' => 4,
		),
		array(
			'type'     => 'page',
			'slug'     => 'about',
			'title'    => __( 'About', 'ak-zeyna-child' ),
			'order'    => 5,
			'template' => 'page-templates/template-about.php',
		),
		array(
			'type'     => 'page',
			'slug'     => 'contact',
			'title'    => __( 'Contact', 'ak-zeyna-child' ),
			'order'    => 6,
			'template' => 'page-templates/template-contact.php',
		),
	);

	return array_merge( $pages, ak_content_cases(), ak_content_journal() );
}

/**
 * Placeholder case studies. Replace these with real projects — or delete an
 * entry here and the next update retires it from the site.
 *
 * @return array[]
 */
function ak_content_cases() {
	$cases = array(
		array(
			'slug'      => 'placeholder-collection-launch',
			'client'    => 'Client Name',
			'headline'  => 'A first collection, shown in London.',
			'category'  => 'Collection launch',
			'year'      => '2026',
			'movements' => 'Strategy, Identity, Production',
			'summary'   => 'Placeholder. A designer with a finished collection and no route to an audience. Position, identity and a runway slot inside one season — replace this text with the real project.',
			'measures'  => array(
				array( 'key' => 'LOOKS', 'value' => '24' ),
				array( 'key' => 'PRESS', 'value' => '38' ),
				array( 'key' => 'RUNWAY', 'value' => 'LDN' ),
			),
		),
		array(
			'slug'      => 'placeholder-brand-identity',
			'client'    => 'Client Name',
			'headline'  => 'An identity built to survive the feed.',
			'category'  => 'Brand identity',
			'year'      => '2026',
			'movements' => 'Strategy, Identity',
			'summary'   => 'Placeholder. An identity drawn at ad size first and at backdrop size second — replace with the real project.',
			'measures'  => array(
				array( 'key' => 'MARKS', 'value' => '3' ),
				array( 'key' => 'SCALE', 'value' => '4:5 → 6m' ),
			),
		),
		array(
			'slug'      => 'placeholder-personal-brand',
			'client'    => 'Client Name',
			'headline'  => 'A founder who stopped hiding behind the brand.',
			'category'  => 'Personal brand',
			'year'      => '2025',
			'movements' => 'Strategy, Presence',
			'summary'   => 'Placeholder. Personal brand strategy for a founder whose company was better known than they were.',
			'measures'  => array(
				array( 'key' => 'SPEAKING', 'value' => '6' ),
			),
		),
		array(
			'slug'      => 'placeholder-campaign',
			'client'    => 'Client Name',
			'headline'  => 'A campaign shot in three cities.',
			'category'  => 'Photo & video',
			'year'      => '2025',
			'movements' => 'Identity, Production',
			'summary'   => 'Placeholder. A photo and video campaign produced across three cities in one season.',
			'measures'  => array(
				array( 'key' => 'CITIES', 'value' => '3' ),
				array( 'key' => 'ASSETS', 'value' => '84' ),
			),
		),
		array(
			'slug'      => 'placeholder-website',
			'client'    => 'Client Name',
			'headline'  => 'A store that finally looked like the clothes.',
			'category'  => 'E-commerce',
			'year'      => '2025',
			'movements' => 'Identity, Presence',
			'summary'   => 'Placeholder. A website and online store rebuilt around the brand rather than around a template.',
			'measures'  => array(
				array( 'key' => 'LCP', 'value' => '0.9s' ),
				array( 'key' => 'CVR', 'value' => '+2.4pp' ),
			),
		),
		array(
			'slug'      => 'placeholder-fashion-week',
			'client'    => 'Client Name',
			'headline'  => 'A show inside an international fashion week.',
			'category'  => 'Fashion show',
			'year'      => '2026',
			'movements' => 'Production, Presence',
			'summary'   => 'Placeholder. Production and industry PR for a show staged during an international fashion week.',
			'measures'  => array(
				array( 'key' => 'LOOKS', 'value' => '18' ),
				array( 'key' => 'PRESS', 'value' => '52' ),
			),
		),
	);

	$out = array();
	foreach ( $cases as $c ) {
		$movements = implode(
			', ',
			array_map(
				function ( $m ) {
					$m   = trim( $m );
					$key = strtolower( $m );
					return in_array( $key, array( 'strategy', 'identity', 'production', 'presence' ), true )
						? '<a href="/services/#' . $key . '">' . $m . '</a>'
						: $m;
				},
				explode( ',', $c['movements'] )
			)
		);

		$out[] = array(
			'type'    => 'portfolio',
			'slug'    => $c['slug'],
			'title'   => $c['client'],
			'excerpt' => $c['summary'],
			'content' => '<p>Placeholder case narrative — replace with the project story. The chapters, measures and position blocks are driven from the case fields below.</p>'
				. "\n\n" . '<p>This project ran through the ' . $movements . ' movements. '
				. 'Browse <a href="/work/">all work</a>, or <a href="/contact/">start a project</a>.</p>',
			'terms'   => array( 'project-categories' => $c['category'] ),
			'meta'    => array(
				'ak_headline'  => $c['headline'],
				'ak_category'  => $c['category'],
				'ak_year'      => $c['year'],
				'ak_movements' => $c['movements'],
				'ak_summary'   => $c['summary'],
				'ak_measures'  => wp_json_encode( $c['measures'] ),
			),
		);
	}
	return $out;
}

/**
 * Journal entries.
 *
 * @return array[]
 */
function ak_content_journal() {
	$dir   = get_stylesheet_directory() . '/content/journal';
	$posts = array(
		array(
			'slug'  => 'what-it-costs-to-show-at-a-fashion-week',
			'title' => 'What it actually costs to show at a fashion week',
			'cat'   => 'Production',
			'excerpt' => 'The honest breakdown a young designer cannot find anywhere else — what a runway slot really costs, where the money actually goes, and what a show is genuinely worth.',
		),
		array(
			'slug'  => 'design-the-identity-at-ad-size-first',
			'title' => 'Design the identity at ad size first',
			'cat'   => 'Identity',
			'excerpt' => 'A logo that only works at billboard scale is a logo that will be cropped, shrunk and ignored by the algorithm that decides whether anyone sees it.',
		),
		array(
			'slug'  => 'your-company-is-not-your-personal-brand',
			'title' => 'Your company is not your personal brand',
			'cat'   => 'Strategy',
			'excerpt' => 'Founders in fashion are usually better known than their labels — or invisible behind them. Both are strategy problems, and both are fixable.',
		),
		array(
			'slug'  => 'fix-the-website-before-you-buy-the-traffic',
			'title' => 'Fix the website before you buy the traffic',
			'cat'   => 'Presence',
			'excerpt' => 'Every pound of promotion lands on a page. If that page is slow or unclear, promotion just buys a faster exit.',
		),
	);

	$out = array();
	foreach ( $posts as $p ) {
		$file = $dir . '/' . $p['slug'] . '.html';
		$body = is_readable( $file ) ? file_get_contents( $file ) : '<p>' . esc_html( $p['excerpt'] ) . '</p>';
		$out[] = array(
			'type'    => 'post',
			'slug'    => $p['slug'],
			'title'   => $p['title'],
			'excerpt' => $p['excerpt'],
			'content' => $body,
			'terms'   => array( 'category' => $p['cat'] ),
		);
	}
	return $out;
}

/**
 * The primary menu, in order.
 *
 * @return array<string,string> slug => label. 'work' resolves to the archive.
 */
function ak_content_menu() {
	return array(
		'home'     => __( 'Home', 'ak-zeyna-child' ),
		'work'     => __( 'Work', 'ak-zeyna-child' ),
		'services' => __( 'Services', 'ak-zeyna-child' ),
		'journal'  => __( 'Journal', 'ak-zeyna-child' ),
		'about'    => __( 'About', 'ak-zeyna-child' ),
		'contact'  => __( 'Contact', 'ak-zeyna-child' ),
	);
}

/**
 * The Contact Form 7 project brief, as data.
 *
 * Shipped with the theme so the form is created on activation rather than
 * imported. The form is found by name at render time, so it keeps working
 * whatever ID the database gives it.
 *
 * @return array{title:string,slug:string,meta:array}
 */
function ak_content_form() {
	$email = ak_studio_email();
	$file  = get_stylesheet_directory() . '/content/cf7-form.txt';
	$body  = is_readable( $file ) ? file_get_contents( $file ) : '';

	$mail = array(
		'subject'            => 'Project brief — [your-name]',
		'sender'             => 'AK Studio <wordpress@' . wp_parse_url( home_url(), PHP_URL_HOST ) . '>',
		'recipient'          => $email,
		'body'               => "From: [your-name] <[your-email]>\nBrand: [your-brand]\nLink: [your-link]\n\nStage: [brand-stage]\nNeeds: [project-need]\n\n[your-message]\n\n--\nSent from [_site_title] ([_site_url])",
		'additional_headers' => 'Reply-To: [your-email]',
		'attachments'        => '',
		'use_html'           => false,
		'exclude_blank'      => false,
	);

	$mail_2 = array(
		'active'             => true,
		'subject'            => 'We have your brief — AK Brand Development Studio',
		'sender'             => 'AK Brand Development Studio <' . $email . '>',
		'recipient'          => '[your-email]',
		'body'               => "[your-name],\n\nThank you — your brief has arrived and one of us has read it.\n\nWe reply within two working days with an honest read: which movements your project actually needs, and which it does not.\n\nAK Brand Development Studio\nFashion & Brand Advisory — London\n" . $email,
		'additional_headers' => 'Reply-To: ' . $email,
		'attachments'        => '',
		'use_html'           => false,
		'exclude_blank'      => false,
	);

	$messages = array(
		'mail_sent_ok'             => 'Thank you. Your brief has arrived — we reply within two working days.',
		'mail_sent_ng'             => 'Something failed on our side. Email ' . $email . ' directly.',
		'validation_error'         => 'A field or two needs attention below.',
		'accept_terms'             => 'Please accept the terms before sending.',
		'invalid_required'         => 'This field is required.',
		'invalid_too_long'         => 'This entry is too long.',
		'invalid_too_short'        => 'This entry is too short.',
		'upload_failed'            => 'That upload failed. Try again.',
		'upload_file_type_invalid' => 'That file type is not allowed.',
		'upload_file_too_large'    => 'That file is too large.',
		'upload_failed_php_error'  => 'Something went wrong with the upload.',
		'invalid_date'             => 'This date seems invalid.',
		'date_too_early'           => 'This date is too early.',
		'date_too_late'            => 'This date is too late.',
		'invalid_number'           => 'This number seems invalid.',
		'number_too_small'         => 'This number is too small.',
		'number_too_large'         => 'This number is too large.',
		'quiz_answer_not_correct'  => 'The answer to the quiz is incorrect.',
		'invalid_email'            => 'This email address seems invalid.',
		'invalid_url'              => 'This URL seems invalid.',
		'invalid_tel'              => 'This telephone number seems invalid.',
	);

	return array(
		'title' => 'AK — Project brief',
		'slug'  => 'ak-project-brief',
		'meta'  => array(
			'_form'                => $body,
			'_mail'                => $mail,
			'_mail_2'              => $mail_2,
			'_messages'            => $messages,
			'_additional_settings' => '',
			'_locale'              => 'en_GB',
		),
	);
}
