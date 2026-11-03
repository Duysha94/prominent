<?php
/**
 * The project brief: the form, its handler, and where briefs live afterwards.
 *
 * Every brief is saved in the dashboard (Briefs) before any email is sent, so
 * a mail-server problem never loses a lead. Spam is kept out without a
 * captcha: a honeypot, a signed timestamp and a per-visitor rate limit.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

const AK_BRIEF_STATUSES = array(
	'new'     => 'New',
	'replied' => 'Replied',
	'won'     => 'Won',
	'closed'  => 'Closed',
);

add_action( 'init', 'ak_brand_register_brief' );

function ak_brand_register_brief(): void {
	register_post_type(
		'ak_brief',
		array(
			'labels'          => array(
				'name'          => __( 'Briefs', 'ak-brand-core' ),
				'singular_name' => __( 'Brief', 'ak-brand-core' ),
				'edit_item'     => __( 'Brief', 'ak-brand-core' ),
				'all_items'     => __( 'All briefs', 'ak-brand-core' ),
				'search_items'  => __( 'Search briefs', 'ak-brand-core' ),
				'not_found'     => __( 'No briefs yet. They arrive here from the form on the site.', 'ak-brand-core' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => false,
			'menu_position'   => 22,
			'menu_icon'       => 'dashicons-email-alt',
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array(
				'create_posts'       => 'do_not_allow',
				'edit_posts'         => 'edit_others_posts',
				'edit_others_posts'  => 'edit_others_posts',
				'delete_posts'       => 'edit_others_posts',
				'publish_posts'      => 'edit_others_posts',
				'read_private_posts' => 'edit_others_posts',
			),
			'map_meta_cap'    => true,
		)
	);
}

/* ── Rendering ─────────────────────────────────────────────────────── */

/**
 * Signed timestamp: proves the form was rendered by this site, and when.
 */
function ak_brand_brief_token(): string {
	$t = (string) time();
	return $t . '.' . substr( hash_hmac( 'sha256', $t, wp_salt( 'nonce' ) ), 0, 20 );
}

/* ── Spam protection: Cloudflare Turnstile ─────────────────────────────
   Uses the Simple Cloudflare Turnstile plugin when it is set up (the same
   keys and settings as its [cf7_simple_turnstile] tag for Contact Form 7),
   otherwise the keys in Settings → AK Studio. Without keys the form relies
   on its honeypot, signed timestamp and rate limit. */

/**
 * 'plugin', 'own' or '' when Turnstile is not configured.
 */
function ak_brand_turnstile_mode(): string {
	if ( function_exists( 'cfturnstile_field_show' ) && function_exists( 'cfturnstile_check' ) && get_option( 'cfturnstile_key' ) && get_option( 'cfturnstile_secret' ) ) {
		return 'plugin';
	}
	return ak_brand_studio( 'turnstile_site' ) && ak_brand_studio( 'turnstile_secret' ) ? 'own' : '';
}

/**
 * The Turnstile widget for one form.
 */
function ak_brand_turnstile_field( string $form_id ): string {
	$mode = ak_brand_turnstile_mode();
	if ( 'plugin' === $mode ) {
		ob_start();
		cfturnstile_field_show( '', '', 'ak-brief', '-' . sanitize_html_class( $form_id ) );
		return '<div class="brief__human">' . ob_get_clean() . '</div>';
	}
	if ( 'own' === $mode ) {
		wp_enqueue_script( 'ak-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, array( 'in_footer' => true, 'strategy' => 'defer' ) ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Cloudflare serves the current version.
		return '<div class="brief__human"><div class="cf-turnstile" data-sitekey="' . esc_attr( ak_brand_studio( 'turnstile_site' ) ) . '" data-theme="dark" data-action="ak-brief" data-refresh-expired="auto"></div></div>';
	}
	return '';
}

/**
 * Whether the submitted Turnstile token is good (true when Turnstile is off).
 */
function ak_brand_turnstile_passed(): bool {
	$mode = ak_brand_turnstile_mode();
	if ( 'plugin' === $mode ) {
		$check = cfturnstile_check();
		return ! empty( $check['success'] );
	}
	if ( 'own' === $mode ) {
		$token = isset( $_POST['cf-turnstile-response'] ) && is_string( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified with Cloudflare below.
		if ( '' === $token || strlen( $token ) > 2048 ) {
			return false;
		}
		$res = wp_remote_post(
			'https://challenges.cloudflare.com/turnstile/v0/siteverify',
			array(
				'timeout' => 10,
				'body'    => array(
					'secret'   => ak_brand_studio( 'turnstile_secret' ),
					'response' => $token,
					'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
				),
			)
		);
		$data = is_wp_error( $res ) ? null : json_decode( (string) wp_remote_retrieve_body( $res ), true );
		return is_array( $data ) && ! empty( $data['success'] );
	}
	return true;
}

/**
 * The brief form.
 *
 * @param array<string,mixed> $args compact (bool), service (preselected value), source (label), id.
 */
function ak_brand_brief_form( array $args = array() ): string {
	$args     = wp_parse_args( $args, array( 'compact' => false, 'service' => '', 'id' => 'brief', 'source' => '' ) );
	$id       = sanitize_html_class( (string) $args['id'] );
	$compact  = (bool) $args['compact'];
	$services = ak_brand_brief_services();
	$privacy  = get_privacy_policy_url();
	$sent     = isset( $_GET['brief'] ) && 'sent' === $_GET['brief']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display flag only.
	$f        = static fn( string $name ): string => $id . '-' . $name;

	wp_enqueue_script( 'ak-brief' );

	ob_start();
	?>
	<form class="brief<?php echo $compact ? ' brief--compact' : ''; ?>" id="<?php echo esc_attr( $id ); ?>-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-brief>
		<div class="brief__done" role="status" tabindex="-1" <?php echo $sent ? '' : 'hidden'; ?> data-brief-done>
			<p class="brief__done-title"><?php esc_html_e( 'Thank you — your brief is with us.', 'ak-brand-core' ); ?></p>
			<p><?php esc_html_e( 'We read every brief ourselves and will reply by email. If something is urgent, write to', 'ak-brand-core' ); ?> <a href="mailto:<?php echo esc_attr( antispambot( ak_brand_studio( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( ak_brand_studio( 'email' ) ) ); ?></a>.</p>
		</div>
		<div class="brief__body" <?php echo $sent ? 'hidden' : ''; ?> data-brief-body>
			<input type="hidden" name="action" value="ak_brief">
			<input type="hidden" name="ak_t" value="<?php echo esc_attr( ak_brand_brief_token() ); ?>">
			<input type="hidden" name="ak_source" value="<?php echo esc_attr( $args['source'] ? (string) $args['source'] : ( is_singular() ? get_the_title() : wp_get_document_title() ) ); ?>">
			<div class="brief__trap" aria-hidden="true"><label for="<?php echo esc_attr( $f( 'website2' ) ); ?>">Leave empty</label><input type="text" id="<?php echo esc_attr( $f( 'website2' ) ); ?>" name="ak_website2" tabindex="-1" autocomplete="off"></div>

			<div class="brief__error" role="alert" tabindex="-1" hidden data-brief-error></div>

			<div class="brief__grid">
				<p class="field">
					<label for="<?php echo esc_attr( $f( 'name' ) ); ?>"><?php esc_html_e( 'Your name', 'ak-brand-core' ); ?> <span aria-hidden="true">*</span></label>
					<input type="text" id="<?php echo esc_attr( $f( 'name' ) ); ?>" name="ak_name" autocomplete="name" required maxlength="120">
				</p>
				<p class="field">
					<label for="<?php echo esc_attr( $f( 'email' ) ); ?>"><?php esc_html_e( 'Email', 'ak-brand-core' ); ?> <span aria-hidden="true">*</span></label>
					<input type="email" id="<?php echo esc_attr( $f( 'email' ) ); ?>" name="ak_email" autocomplete="email" required maxlength="190" inputmode="email">
				</p>
				<?php if ( ! $compact ) : ?>
					<p class="field">
						<label for="<?php echo esc_attr( $f( 'brand' ) ); ?>"><?php esc_html_e( 'Brand or company', 'ak-brand-core' ); ?></label>
						<input type="text" id="<?php echo esc_attr( $f( 'brand' ) ); ?>" name="ak_brand" autocomplete="organization" maxlength="160">
					</p>
					<p class="field">
						<label for="<?php echo esc_attr( $f( 'link' ) ); ?>"><?php esc_html_e( 'Website or Instagram', 'ak-brand-core' ); ?></label>
						<input type="text" id="<?php echo esc_attr( $f( 'link' ) ); ?>" name="ak_link" maxlength="300" placeholder="@yourbrand">
					</p>
					<p class="field">
						<label for="<?php echo esc_attr( $f( 'phone' ) ); ?>"><?php esc_html_e( 'Phone or WhatsApp', 'ak-brand-core' ); ?></label>
						<input type="tel" id="<?php echo esc_attr( $f( 'phone' ) ); ?>" name="ak_phone" autocomplete="tel" maxlength="40">
					</p>
					<p class="field">
						<label for="<?php echo esc_attr( $f( 'timeline' ) ); ?>"><?php esc_html_e( 'Key date or deadline', 'ak-brand-core' ); ?></label>
						<input type="text" id="<?php echo esc_attr( $f( 'timeline' ) ); ?>" name="ak_timeline" maxlength="160" placeholder="<?php esc_attr_e( 'e.g. a show in February', 'ak-brand-core' ); ?>">
					</p>
				<?php endif; ?>
			</div>

			<fieldset class="brief__chips">
				<legend><?php esc_html_e( 'What do you need?', 'ak-brand-core' ); ?></legend>
				<?php foreach ( $services as $value => $label ) : ?>
					<label class="chip"><input type="checkbox" name="ak_services[]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $args['service'], $value ); ?>><span><?php echo esc_html( $label ); ?></span></label>
				<?php endforeach; ?>
			</fieldset>


			<p class="field field--wide">
				<label for="<?php echo esc_attr( $f( 'message' ) ); ?>"><?php esc_html_e( 'Tell us about the brand and what you need', 'ak-brand-core' ); ?> <span aria-hidden="true">*</span></label>
				<textarea id="<?php echo esc_attr( $f( 'message' ) ); ?>" name="ak_message" rows="<?php echo $compact ? 4 : 6; ?>" required maxlength="5000"></textarea>
			</p>

			<p class="field field--check">
				<label><input type="checkbox" name="ak_consent" value="1" required>
					<span>
					<?php
					if ( $privacy ) {
						printf(
							/* translators: %s: privacy policy link */
							esc_html__( 'I agree that the studio may use these details to reply to my enquiry, as described in the %s.', 'ak-brand-core' ),
							'<a href="' . esc_url( $privacy ) . '">' . esc_html__( 'privacy policy', 'ak-brand-core' ) . '</a>'
						);
					} else {
						esc_html_e( 'I agree that the studio may use these details to reply to my enquiry.', 'ak-brand-core' );
					}
					?>
					</span></label>
			</p>

			<?php echo ak_brand_turnstile_field( (string) $args['id'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- widget markup from the Turnstile plugin or escaped above. ?>

			<div class="brief__submit">
				<button type="submit" class="btn btn--accent" data-magnetic><span data-brief-label><?php esc_html_e( 'Send the brief', 'ak-brand-core' ); ?></span><svg class="btn__arrow" viewBox="0 0 16 16" aria-hidden="true"><path d="M4 12 12 4M5.5 4H12v6.5" fill="none" stroke="currentColor" stroke-width="1.6"/></svg></button>
				<p class="brief__note"><?php esc_html_e( 'We reply by email. No newsletters, no sharing.', 'ak-brand-core' ); ?></p>
			</div>
		</div>
	</form>
	<?php
	return (string) ob_get_clean();
}

add_shortcode(
	'ak_brief',
	static function ( $atts ) {
		$atts = shortcode_atts( array( 'compact' => '', 'service' => '', 'id' => 'brief' ), $atts, 'ak_brief' );
		return ak_brand_brief_form(
			array(
				'compact' => '1' === (string) $atts['compact'],
				'service' => sanitize_key( $atts['service'] ),
				'id'      => sanitize_html_class( $atts['id'] ),
			)
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_register_script( 'ak-brief', plugins_url( 'assets/brief.js', AK_BRAND_CORE_FILE ), array(), AK_BRAND_CORE_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_localize_script(
			'ak-brief',
			'akBrief',
			array(
				'sending' => __( 'Sending…', 'ak-brand-core' ),
				'invalid' => __( 'Please check the highlighted fields.', 'ak-brand-core' ),
				'failed'  => __( 'Something went wrong and the brief was not sent. Please try again, or email us directly.', 'ak-brand-core' ),
			)
		);
	}
);

/* ── Handling ──────────────────────────────────────────────────────── */

add_action( 'admin_post_nopriv_ak_brief', 'ak_brand_handle_brief' );
add_action( 'admin_post_ak_brief', 'ak_brand_handle_brief' );

/**
 * Validate, save, notify, respond.
 */
function ak_brand_handle_brief(): void {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- public form; protected by a signed timestamp, honeypot and rate limit instead of a nonce so page caching cannot break it.
	$ajax   = ! empty( $_POST['ak_ajax'] );
	$fail   = static function ( string $message, array $fields = array(), int $code = 400 ) use ( $ajax ) {
		if ( $ajax ) {
			wp_send_json( array( 'ok' => false, 'message' => $message, 'fields' => $fields ), $code );
		}
		wp_die( esc_html( $message ), esc_html__( 'Brief not sent', 'ak-brand-core' ), array( 'response' => $code, 'back_link' => true ) );
	};
	$get    = static fn( string $k, int $max = 300 ): string => isset( $_POST[ $k ] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST[ $k ] ) ), 0, $max ) : '';

	// Bots fill the hidden field; humans never see it. Pretend success.
	if ( ! empty( $_POST['ak_website2'] ) ) {
		$ajax ? wp_send_json( array( 'ok' => true ) ) : wp_safe_redirect( add_query_arg( 'brief', 'sent', wp_get_referer() ?: home_url( '/' ) ) . '#brief' );
		exit;
	}

	// Signed timestamp: rendered here, at least 3 seconds ago, within 30 days.
	$token = isset( $_POST['ak_t'] ) ? sanitize_text_field( wp_unslash( $_POST['ak_t'] ) ) : '';
	$parts = explode( '.', $token );
	$valid = 2 === count( $parts ) && hash_equals( substr( hash_hmac( 'sha256', $parts[0], wp_salt( 'nonce' ) ), 0, 20 ), $parts[1] );
	$age   = $valid ? time() - (int) $parts[0] : -1;
	if ( ! $valid || $age < 3 || $age > 30 * DAY_IN_SECONDS ) {
		$fail( __( 'This form has expired. Please reload the page and send it again.', 'ak-brand-core' ) );
	}

	// Rate limit: 5 briefs per visitor per hour.
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rkey  = 'ak_brief_rl_' . substr( hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) ), 0, 16 );
	$count = (int) get_transient( $rkey );
	if ( $count >= 5 ) {
		$fail( __( 'Too many briefs from this connection. Please try again in an hour, or email us directly.', 'ak-brand-core' ), array(), 429 );
	}

	$name     = $get( 'ak_name', 120 );
	$email    = isset( $_POST['ak_email'] ) ? sanitize_email( wp_unslash( $_POST['ak_email'] ) ) : '';
	$brand    = $get( 'ak_brand', 160 );
	$link     = $get( 'ak_link', 300 );
	$phone    = $get( 'ak_phone', 40 );
	$timeline = $get( 'ak_timeline', 160 );
	$source   = $get( 'ak_source', 200 );
	$message  = isset( $_POST['ak_message'] ) ? mb_substr( sanitize_textarea_field( wp_unslash( $_POST['ak_message'] ) ), 0, 5000 ) : '';
	$allowed  = ak_brand_brief_services();
	$services = isset( $_POST['ak_services'] ) && is_array( $_POST['ak_services'] ) ? array_values( array_intersect( array_map( 'sanitize_key', wp_unslash( $_POST['ak_services'] ) ), array_keys( $allowed ) ) ) : array();
	$consent  = ! empty( $_POST['ak_consent'] );
	// phpcs:enable

	$errors = array();
	if ( '' === $name ) {
		$errors['ak_name'] = __( 'Please tell us your name.', 'ak-brand-core' );
	}
	if ( ! is_email( $email ) ) {
		$errors['ak_email'] = __( 'Please enter a valid email address.', 'ak-brand-core' );
	}
	if ( mb_strlen( trim( $message ) ) < 10 ) {
		$errors['ak_message'] = __( 'Please tell us a little about the brand and what you need.', 'ak-brand-core' );
	}
	if ( ! $consent ) {
		$errors['ak_consent'] = __( 'Please confirm we may use your details to reply.', 'ak-brand-core' );
	}
	if ( preg_match_all( '#https?://#i', $message ) > 5 ) {
		$errors['ak_message'] = __( 'Please include no more than five links.', 'ak-brand-core' );
	}
	if ( $errors ) {
		$fail( __( 'Please check the highlighted fields.', 'ak-brand-core' ), $errors, 422 );
	}
	if ( ! ak_brand_turnstile_passed() ) {
		$fail( __( 'The spam check did not go through. Please wait a moment for it to finish and send again.', 'ak-brand-core' ), array(), 403 );
	}

	set_transient( $rkey, $count + 1, HOUR_IN_SECONDS );

	$labels = array_map( static fn( $s ) => $allowed[ $s ], $services );
	$title  = $name . ( $brand ? ' — ' . $brand : '' );
	$id     = wp_insert_post(
		array(
			'post_type'    => 'ak_brief',
			'post_status'  => 'private',
			'post_title'   => $title,
			'post_content' => $message,
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		$fail( __( 'Something went wrong and the brief was not sent. Please try again, or email us directly.', 'ak-brand-core' ), array(), 500 );
	}
	$meta = array(
		'_ak_name'     => $name,
		'_ak_email'    => $email,
		'_ak_brand'    => $brand,
		'_ak_link'     => $link,
		'_ak_phone'    => $phone,
		'_ak_timeline' => $timeline,
		'_ak_services' => implode( ',', $services ),
		'_ak_source'   => $source,
		'_ak_status'   => 'new',
		'_ak_consent'  => gmdate( 'c' ),
	);
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, $k, $v );
	}

	// Notify the studio. Reply goes straight to the client.
	$to      = ak_brand_studio( 'brief_to' ) ?: ak_brand_studio( 'email' );
	$subject = sprintf( /* translators: 1: name/brand, 2: services */ __( 'New brief: %1$s%2$s', 'ak-brand-core' ), $title, $labels ? ' (' . implode( ', ', $labels ) . ')' : '' );
	$lines   = array(
		__( 'Name', 'ak-brand-core' )        => $name,
		__( 'Email', 'ak-brand-core' )       => $email,
		__( 'Brand', 'ak-brand-core' )       => $brand,
		__( 'Link', 'ak-brand-core' )        => $link,
		__( 'Phone', 'ak-brand-core' )       => $phone,
		__( 'Needs', 'ak-brand-core' )       => implode( ', ', $labels ),
		__( 'Key date', 'ak-brand-core' )    => $timeline,
		__( 'Sent from', 'ak-brand-core' )   => $source,
	);
	$body = '';
	foreach ( $lines as $k => $v ) {
		if ( '' !== $v ) {
			$body .= $k . ': ' . $v . "\n";
		}
	}
	$body .= "\n" . $message . "\n\n— " . admin_url( 'post.php?post=' . $id . '&action=edit' ) . "\n";
	$sent  = wp_mail( $to, $subject, $body, array( 'Reply-To: ' . str_replace( array( "\r", "\n", '"' ), '', $name ) . ' <' . $email . '>' ) );
	update_post_meta( $id, '_ak_mailed', $sent ? 1 : 0 );

	// Automatic reply to the client, from the studio.
	if ( 'off' !== ak_brand_studio( 'reply_on' ) ) {
		$studio  = ak_brand_studio( 'name' );
		$from    = ak_brand_studio( 'email' );
		$facts   = array_filter(
			array(
				$brand ? __( 'Brand', 'ak-brand-core' ) . ': ' . $brand : '',
				$labels ? __( 'Needs', 'ak-brand-core' ) . ': ' . implode( ', ', $labels ) : '',
				$timeline ? __( 'Key date', 'ak-brand-core' ) . ': ' . $timeline : '',
			)
		);
		$summary = trim( implode( "\n", $facts ) . "\n\n" . $message );
		$vars    = array(
			'{name}'    => $name,
			'{brand}'   => $brand,
			'{needs}'   => implode( ', ', $labels ),
			'{date}'    => $timeline,
			'{message}' => $message,
			'{summary}' => $summary,
			'{studio}'  => $studio,
			'{email}'   => $from,
		);
		$headers = array( 'Reply-To: ' . $studio . ' <' . $to . '>' );
		$host    = (string) wp_parse_url( home_url(), PHP_URL_HOST );
		if ( $from && $host && str_ends_with( strtolower( $from ), '@' . preg_replace( '/^www\./', '', strtolower( $host ) ) ) ) {
			$headers[] = 'From: ' . $studio . ' <' . $from . '>';
		}
		$replied = wp_mail( $email, strtr( ak_brand_studio( 'reply_subject' ), $vars ), strtr( ak_brand_studio( 'reply_body' ), $vars ), $headers );
		update_post_meta( $id, '_ak_replied', $replied ? 1 : 0 );
	}

	/**
	 * Fires after a brief is saved (e.g. to push it to a CRM).
	 *
	 * @param int                 $id   Brief post ID.
	 * @param array<string,mixed> $meta Saved fields.
	 */
	do_action( 'ak_brand_brief_received', $id, $meta );

	if ( $ajax ) {
		wp_send_json( array( 'ok' => true ) );
	}
	wp_safe_redirect( add_query_arg( 'brief', 'sent', wp_get_referer() ?: home_url( '/' ) ) . '#brief' );
	exit;
}

/* ── Dashboard ─────────────────────────────────────────────────────── */

add_filter(
	'manage_ak_brief_posts_columns',
	static fn() => array(
		'cb'        => '<input type="checkbox">',
		'title'     => __( 'Brief', 'ak-brand-core' ),
		'ak_email'  => __( 'Email', 'ak-brand-core' ),
		'ak_needs'  => __( 'Needs', 'ak-brand-core' ),
		'ak_status' => __( 'Status', 'ak-brand-core' ),
		'date'      => __( 'Received', 'ak-brand-core' ),
	)
);

add_action(
	'manage_ak_brief_posts_custom_column',
	static function ( $column, $post_id ) {
		switch ( $column ) {
			case 'ak_email':
				$e = (string) get_post_meta( $post_id, '_ak_email', true );
				echo '<a href="mailto:' . esc_attr( $e ) . '">' . esc_html( $e ) . '</a>';
				if ( '0' === (string) get_post_meta( $post_id, '_ak_mailed', true ) ) {
					echo '<br><span style="color:#b32d2e">' . esc_html__( 'Email notification failed', 'ak-brand-core' ) . '</span>';
				}
				break;
			case 'ak_needs':
				$all = ak_brand_brief_services();
				$s   = array_filter( explode( ',', (string) get_post_meta( $post_id, '_ak_services', true ) ) );
				echo esc_html( implode( ', ', array_map( static fn( $k ) => $all[ $k ] ?? $k, $s ) ) ?: '—' );
				break;
			case 'ak_status':
				$st = (string) get_post_meta( $post_id, '_ak_status', true ) ?: 'new';
				echo '<strong>' . esc_html( AK_BRIEF_STATUSES[ $st ] ?? $st ) . '</strong>';
				break;
		}
	},
	10,
	2
);

/* Count of new briefs next to the menu item. */
add_action(
	'admin_menu',
	static function () {
		global $menu;
		$new = get_posts(
			array(
				'post_type'      => 'ak_brief',
				'post_status'    => 'private',
				'meta_key'       => '_ak_status', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'     => 'new', // phpcs:ignore WordPress.DB.SlowDBQuery
				'fields'         => 'ids',
				'posts_per_page' => 50,
				'no_found_rows'  => true,
			)
		);
		if ( ! $new || ! is_array( $menu ) ) {
			return;
		}
		foreach ( $menu as $i => $item ) {
			if ( isset( $item[2] ) && 'edit.php?post_type=ak_brief' === $item[2] ) {
				$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . count( $new ) . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			}
		}
	},
	99
);

add_action(
	'add_meta_boxes_ak_brief',
	static function () {
		remove_meta_box( 'submitdiv', 'ak_brief', 'side' );
		add_meta_box( 'ak-brief-details', __( 'Brief', 'ak-brand-core' ), 'ak_brand_render_brief_box', 'ak_brief', 'normal', 'high' );
		add_meta_box( 'ak-brief-status', __( 'Status', 'ak-brand-core' ), 'ak_brand_render_brief_status', 'ak_brief', 'side', 'high' );
	}
);

function ak_brand_render_brief_box( WP_Post $post ): void {
	$all     = ak_brand_brief_services();
	$rows    = array(
		__( 'Name', 'ak-brand-core' )      => get_post_meta( $post->ID, '_ak_name', true ),
		__( 'Email', 'ak-brand-core' )     => get_post_meta( $post->ID, '_ak_email', true ),
		__( 'Brand', 'ak-brand-core' )     => get_post_meta( $post->ID, '_ak_brand', true ),
		__( 'Link', 'ak-brand-core' )      => get_post_meta( $post->ID, '_ak_link', true ),
		__( 'Phone', 'ak-brand-core' )     => get_post_meta( $post->ID, '_ak_phone', true ),
		__( 'Needs', 'ak-brand-core' )     => implode( ', ', array_map( static fn( $k ) => $all[ $k ] ?? $k, array_filter( explode( ',', (string) get_post_meta( $post->ID, '_ak_services', true ) ) ) ) ),
		__( 'Key date', 'ak-brand-core' )  => get_post_meta( $post->ID, '_ak_timeline', true ),
		__( 'Sent from', 'ak-brand-core' ) => get_post_meta( $post->ID, '_ak_source', true ),
		__( 'Consent', 'ak-brand-core' )   => get_post_meta( $post->ID, '_ak_consent', true ),
	);
	echo '<table class="widefat striped"><tbody>';
	foreach ( $rows as $label => $value ) {
		if ( '' === (string) $value ) {
			continue;
		}
		echo '<tr><th style="width:9em">' . esc_html( $label ) . '</th><td>';
		if ( is_email( (string) $value ) ) {
			echo '<a href="mailto:' . esc_attr( $value ) . '?subject=' . rawurlencode( 'Re: your brief to ' . ak_brand_studio( 'name' ) ) . '">' . esc_html( $value ) . '</a>';
		} else {
			echo esc_html( (string) $value );
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
	echo '<h3>' . esc_html__( 'Message', 'ak-brand-core' ) . '</h3><div style="white-space:pre-wrap;font-size:14px;line-height:1.6">' . esc_html( $post->post_content ) . '</div>';
}

function ak_brand_render_brief_status( WP_Post $post ): void {
	wp_nonce_field( 'ak_brief_status', 'ak_brief_nonce' );
	$current = (string) get_post_meta( $post->ID, '_ak_status', true ) ?: 'new';
	echo '<select name="ak_status" class="widefat">';
	foreach ( AK_BRIEF_STATUSES as $value => $label ) {
		echo '<option value="' . esc_attr( $value ) . '" ' . selected( $current, $value, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select><p class="description">' . esc_html__( 'Briefs not marked “Won” are deleted automatically after the retention period set in Settings → AK Studio.', 'ak-brand-core' ) . '</p>';
	echo '<p><button type="submit" class="button button-primary">' . esc_html__( 'Save status', 'ak-brand-core' ) . '</button> ';
	echo '<a class="submitdelete deletion" href="' . esc_url( get_delete_post_link( $post->ID, '', true ) ) . '">' . esc_html__( 'Delete permanently', 'ak-brand-core' ) . '</a></p>';
	echo '<input type="hidden" name="post_status" value="private">';
}

add_action(
	'save_post_ak_brief',
	static function ( $post_id ) {
		if ( ! isset( $_POST['ak_brief_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ak_brief_nonce'] ) ), 'ak_brief_status' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$status = isset( $_POST['ak_status'] ) ? sanitize_key( wp_unslash( $_POST['ak_status'] ) ) : 'new';
		update_post_meta( $post_id, '_ak_status', array_key_exists( $status, AK_BRIEF_STATUSES ) ? $status : 'new' );
	}
);

add_filter(
	'display_post_states',
	static fn( $states, $post ) => 'ak_brief' === $post->post_type ? array() : $states,
	10,
	2
);

/* Keep briefs private whatever the edit screen sends. */
add_filter(
	'wp_insert_post_data',
	static function ( $data ) {
		if ( 'ak_brief' === $data['post_type'] && ! in_array( $data['post_status'], array( 'private', 'trash', 'auto-draft' ), true ) ) {
			$data['post_status'] = 'private';
		}
		return $data;
	}
);

/* A visible warning when the mail server is not delivering notifications. */
add_action(
	'admin_notices',
	static function () {
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			return;
		}
		$failed = get_posts(
			array(
				'post_type'      => 'ak_brief',
				'post_status'    => 'private',
				'meta_key'       => '_ak_mailed', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'     => '0', // phpcs:ignore WordPress.DB.SlowDBQuery
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'date_query'     => array( array( 'after' => '14 days ago' ) ),
			)
		);
		if ( $failed ) {
			echo '<div class="notice notice-warning"><p>' . wp_kses_post( sprintf( /* translators: %s: briefs admin URL */ __( 'A recent brief was saved but its email notification could not be sent. Read it under <a href="%s">Briefs</a>, and set up an SMTP plugin so notifications reach your inbox.', 'ak-brand-core' ), esc_url( admin_url( 'edit.php?post_type=ak_brief' ) ) ) ) . '</p></div>';
		}
	}
);

/* ── Retention and personal data ───────────────────────────────────── */

add_action( 'ak_brand_daily', 'ak_brand_prune_briefs' );

function ak_brand_prune_briefs(): void {
	$months = max( 1, (int) ak_brand_studio( 'brief_retain' ) );
	$old    = get_posts(
		array(
			'post_type'      => 'ak_brief',
			'post_status'    => array( 'private', 'trash' ),
			'fields'         => 'ids',
			'posts_per_page' => 200,
			'date_query'     => array( array( 'before' => $months . ' months ago' ) ),
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				'relation' => 'OR',
				array( 'key' => '_ak_status', 'value' => 'won', 'compare' => '!=' ),
				array( 'key' => '_ak_status', 'compare' => 'NOT EXISTS' ),
			),
		)
	);
	foreach ( $old as $id ) {
		wp_delete_post( $id, true );
	}
}

/**
 * @param string $email Address to look up.
 * @return int[]
 */
function ak_brand_briefs_by_email( string $email ): array {
	return get_posts(
		array(
			'post_type'      => 'ak_brief',
			'post_status'    => array( 'private', 'trash' ),
			'fields'         => 'ids',
			'posts_per_page' => 100,
			'meta_key'       => '_ak_email', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $email, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
}

add_filter(
	'wp_privacy_personal_data_exporters',
	static function ( $exporters ) {
		$exporters['ak-brief'] = array(
			'exporter_friendly_name' => __( 'Project briefs', 'ak-brand-core' ),
			'callback'               => static function ( $email ) {
				$items = array();
				foreach ( ak_brand_briefs_by_email( $email ) as $id ) {
					$data = array( array( 'name' => __( 'Message', 'ak-brand-core' ), 'value' => get_post_field( 'post_content', $id ) ) );
					foreach ( array( '_ak_name' => 'Name', '_ak_email' => 'Email', '_ak_brand' => 'Brand', '_ak_link' => 'Link', '_ak_phone' => 'Phone', '_ak_timeline' => 'Key date', '_ak_services' => 'Needs' ) as $k => $label ) {
						$data[] = array( 'name' => $label, 'value' => (string) get_post_meta( $id, $k, true ) );
					}
					$items[] = array( 'group_id' => 'ak-briefs', 'group_label' => __( 'Project briefs', 'ak-brand-core' ), 'item_id' => 'brief-' . $id, 'data' => $data );
				}
				return array( 'data' => $items, 'done' => true );
			},
		);
		return $exporters;
	}
);

add_filter(
	'wp_privacy_personal_data_erasers',
	static function ( $erasers ) {
		$erasers['ak-brief'] = array(
			'eraser_friendly_name' => __( 'Project briefs', 'ak-brand-core' ),
			'callback'             => static function ( $email ) {
				$ids = ak_brand_briefs_by_email( $email );
				foreach ( $ids as $id ) {
					wp_delete_post( $id, true );
				}
				return array( 'items_removed' => count( $ids ) > 0, 'items_retained' => false, 'messages' => array(), 'done' => true );
			},
		);
		return $erasers;
	}
);
