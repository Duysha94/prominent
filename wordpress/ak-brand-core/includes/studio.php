<?php
/**
 * Studio details: one option, owned by the plugin, so the values survive a
 * theme switch (theme mods do not — see docs/THEME-PLUGIN-ARCHITECTURE.md).
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Every editable studio value, grouped for the settings screen.
 * Each field: label, default, type (text|email|url|textarea|date|select), help.
 *
 * @return array<string,array<string,array<string,mixed>>>
 */
function ak_brand_studio_fields(): array {
	return array(
		'studio'  => array(
			'name'        => array( 'Studio name', 'AK Brand Development Studio', 'text' ),
			'short'       => array( 'Short name', 'AK', 'text' ),
			'promise'     => array( 'Promise', 'From the initial idea to international presence.', 'text' ),
			'description' => array( 'Description', 'AK Brand Development Studio is an independent London studio that helps people and businesses build brands that get noticed: strategy and identity, websites and online stores, photo and film, shows and events, PR and promotion.', 'textarea', 'Used for search engines and social previews when a page has no excerpt.' ),
			'email'       => array( 'Studio email', 'ak@akbrand.studio', 'email' ),
			'phone'       => array( 'Phone', '', 'text', 'Hidden on the site while empty.' ),
			'city'        => array( 'City', 'London', 'text' ),
			'country'     => array( 'Country', 'United Kingdom', 'text' ),
		),
		'legal'   => array(
			'legal_name'      => array( 'Legal entity name', '', 'text', 'Add “AK Brand Development Studio Ltd” here once the company is registered — not before. It then appears in the footer and as the data controller in the privacy policy. Hidden while empty.' ),
			'company_number'  => array( 'Company number', '', 'text', 'Hidden while empty.' ),
			'registered_addr' => array( 'Registered address', '', 'textarea', 'Hidden while empty.' ),
		),
		'social'  => array(
			'instagram' => array( 'Studio Instagram URL', '', 'url', 'The “Instagram” line in the Site footer pattern becomes a link once this is filled; search engines also get the address.' ),
			'facebook'  => array( 'Studio Facebook URL', '', 'url', 'The “Facebook” line in the Site footer pattern becomes a link once this is filled; search engines also get the address.' ),
			'linkedin'  => array( 'Studio LinkedIn URL', '', 'url', 'The “LinkedIn” line in the Site footer pattern becomes a link once this is filled; search engines also get the address.' ),
			'tiktok'    => array( 'Studio TikTok URL', '', 'url', 'Add a “TikTok” line to the Site footer pattern and it links here.' ),
		),
		'brief'   => array(
			'brief_to'     => array( 'Send briefs to', '', 'email', 'Leave empty to use the studio email. Every brief is also saved under Briefs in the dashboard.' ),
			'brief_retain' => array( 'Delete unconverted briefs after (months)', '12', 'text', 'Matches the privacy policy. Briefs marked “Won” are kept.' ),
			'reply_on'      => array( 'Automatic reply to the client', 'on', 'select', 'Sent by email right after a brief arrives, from the studio email. Replies to it come to the address above.', array( 'on' => 'Send', 'off' => 'Do not send' ) ),
			'reply_subject' => array( 'Automatic reply — subject', 'We have your brief — AK Brand Development Studio', 'text' ),
			'reply_body'    => array( 'Automatic reply — text', "Hello {name},\n\nThank you for your brief — it has reached AK Brand Development Studio. We read every brief ourselves and will come back to you personally with questions, a time to talk, or a proposal.\n\nFor reference, this is what you sent us:\n\n{summary}\n\nIf you would like to add anything, simply reply to this email.\n\nAK Brand Development Studio\nLondon · {email}", 'textarea', 'You can use {name}, {brand}, {needs}, {date}, {message}, {summary} (everything the client sent), {studio} and {email} (the studio email).' ),
			'turnstile_site'   => array( 'Cloudflare Turnstile site key', '', 'text', 'Spam protection for the brief form. Not needed when the Simple Cloudflare Turnstile plugin is set up: the form uses its keys and settings automatically.' ),
			'turnstile_secret' => array( 'Cloudflare Turnstile secret key', '', 'text', 'Only together with the site key above.' ),
		),
		'privacy' => array(
			'consent' => array( 'Cookie banner', 'auto', 'select', 'Auto shows the banner only when Google Site Kit or a tag ID is active.', array( 'auto' => 'Auto', 'on' => 'Always', 'off' => 'Never' ) ),
			'ga_id'   => array( 'Google tag ID (optional)', '', 'text', 'Only if you are not using Site Kit. Loaded after consent.' ),
		),
	);
}

/**
 * Flat defaults.
 *
 * @return array<string,string>
 */
function ak_brand_studio_defaults(): array {
	$out = array();
	foreach ( ak_brand_studio_fields() as $group ) {
		foreach ( $group as $key => $field ) {
			$out[ $key ] = (string) $field[1];
		}
	}
	return $out;
}

/**
 * One studio value, filtered so a theme or site can override it.
 *
 * @param string $key Key from ak_brand_studio_defaults().
 */
function ak_brand_studio( string $key ): string {
	static $saved = null;
	if ( null === $saved ) {
		$saved = get_option( 'akbrand_studio', array() );
		$saved = is_array( $saved ) ? $saved : array();
	}
	$defaults = ak_brand_studio_defaults();
	$value    = isset( $saved[ $key ] ) && '' !== $saved[ $key ] ? $saved[ $key ] : ( $defaults[ $key ] ?? '' );
	return (string) apply_filters( 'ak_brand_studio_' . $key, $value );
}

/**
 * Settings → AK Studio.
 */
add_action(
	'admin_menu',
	static function () {
		add_options_page(
			__( 'AK Studio', 'ak-brand-core' ),
			__( 'AK Studio', 'ak-brand-core' ),
			'manage_options',
			'ak-studio',
			'ak_brand_render_settings'
		);
	}
);

add_action(
	'admin_init',
	static function () {
		register_setting(
			'ak_studio',
			'akbrand_studio',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'ak_brand_sanitize_studio',
				'default'           => array(),
			)
		);
	}
);

/**
 * @param mixed $input Raw form input.
 * @return array<string,string>
 */
function ak_brand_sanitize_studio( $input ): array {
	$input = is_array( $input ) ? $input : array();
	$clean = array();
	foreach ( ak_brand_studio_fields() as $group ) {
		foreach ( $group as $key => $field ) {
			$raw = isset( $input[ $key ] ) ? (string) wp_unslash( $input[ $key ] ) : '';
			switch ( $field[2] ) {
				case 'email':
					$clean[ $key ] = sanitize_email( $raw );
					break;
				case 'url':
					$clean[ $key ] = esc_url_raw( $raw );
					break;
				case 'textarea':
					$clean[ $key ] = sanitize_textarea_field( $raw );
					break;
				case 'date':
					$clean[ $key ] = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ? $raw : '';
					break;
				case 'select':
					$clean[ $key ] = array_key_exists( $raw, $field[4] ?? array() ) ? $raw : '';
					break;
				default:
					$clean[ $key ] = sanitize_text_field( $raw );
			}
		}
	}
	return $clean;
}

function ak_brand_render_settings(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$saved  = get_option( 'akbrand_studio', array() );
	$titles = array(
		'studio'  => __( 'Studio', 'ak-brand-core' ),
		'legal'   => __( 'Legal details', 'ak-brand-core' ),
		'social'  => __( 'Studio profiles', 'ak-brand-core' ),
		'brief'   => __( 'Project briefs', 'ak-brand-core' ),
		'privacy' => __( 'Cookies & analytics', 'ak-brand-core' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'AK Studio', 'ak-brand-core' ); ?></h1>
		<p><?php esc_html_e( 'The few details used across the whole site: how the studio is reached, its legal details, where briefs go and the cookie banner. Everything you see on a page — texts, films, images, buttons — is edited in that page’s editor.', 'ak-brand-core' ); ?>
			<a href="<?php echo esc_url( admin_url( 'tools.php?page=ak-install' ) ); ?>"><?php esc_html_e( 'Install or refresh the launch content →', 'ak-brand-core' ); ?></a></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'ak_studio' ); ?>
			<?php foreach ( ak_brand_studio_fields() as $group => $fields ) : ?>
				<h2 class="title"><?php echo esc_html( $titles[ $group ] ?? $group ); ?></h2>
				<table class="form-table" role="presentation">
					<?php
					foreach ( $fields as $key => $field ) :
						$id    = 'akbrand-' . $key;
						$name  = 'akbrand_studio[' . $key . ']';
						$value = (string) ( $saved[ $key ] ?? '' );
						?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
							<td>
								<?php if ( 'textarea' === $field[2] ) : ?>
									<textarea class="large-text" rows="<?php echo 'reply_body' === $key ? 14 : 3; ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" placeholder="<?php echo esc_attr( $field[1] ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
								<?php elseif ( 'select' === $field[2] ) : ?>
									<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>">
										<option value=""><?php echo esc_html( sprintf( /* translators: %s: default option */ __( 'Default (%s)', 'ak-brand-core' ), $field[4][ $field[1] ] ?? $field[1] ) ); ?></option>
										<?php foreach ( $field[4] as $opt => $label ) : ?>
											<option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $value, $opt ); ?>><?php echo esc_html( $label ); ?></option>
										<?php endforeach; ?>
									</select>
								<?php else : ?>
									<input class="regular-text" type="<?php echo esc_attr( 'date' === $field[2] ? 'date' : ( 'email' === $field[2] ? 'email' : ( 'url' === $field[2] ? 'url' : 'text' ) ) ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $field[1] ); ?>">
								<?php endif; ?>
								<?php if ( ! empty( $field[3] ) ) : ?>
									<p class="description"><?php echo esc_html( $field[3] ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * [ak_studio_email] — the studio address as a protected mailto link.
 */
add_shortcode(
	'ak_studio_email',
	static function () {
		$email = antispambot( ak_brand_studio( 'email' ) );
		return '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
	}
);

/**
 * [ak_controller] — who the data controller is: the trading name until the
 * company is registered, then the legal name, number and registered office.
 */
add_shortcode(
	'ak_controller',
	static function () {
		$parts = array( ak_brand_studio( 'legal_name' ) ?: ak_brand_studio( 'name' ) );
		if ( ak_brand_studio( 'company_number' ) ) {
			/* translators: %s: company number */
			$parts[] = sprintf( __( 'company number %s', 'ak-brand-core' ), ak_brand_studio( 'company_number' ) );
		}
		if ( ak_brand_studio( 'registered_addr' ) ) {
			/* translators: %s: registered office address */
			$parts[] = sprintf( __( 'registered office: %s', 'ak-brand-core' ), preg_replace( '/\s*\n\s*/', ', ', ak_brand_studio( 'registered_addr' ) ) );
		}
		return esc_html( implode( ', ', $parts ) );
	}
);
