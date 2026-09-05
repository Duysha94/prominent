<?php
/**
 * Contact Form 7 definition.
 *
 * Everything else that used to live in this file — the page list, the case
 * studies, the journal entries and the menu — moved to
 * inc/deployment/manifest.php, which is now the single canonical description
 * of managed content. Two files describing the same pages is exactly how a
 * build ends up creating each of them twice.
 *
 * The form stays here because it is not manifest-managed content: CF7 owns its
 * own post type and the founders edit the recipient and copy in CF7's screens,
 * so the deployment engine deliberately does not reconcile it.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
