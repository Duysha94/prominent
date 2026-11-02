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
			'discipline'  => array( 'Discipline line', 'Brand & fashion development studio', 'text' ),
			'promise'     => array( 'Promise', 'From the initial idea to international presence.', 'text' ),
			'description' => array( 'Description', 'AK Brand Development Studio is an independent creative and strategic practice in London, working with fashion and lifestyle brands from the initial idea to international presence: strategy, identity, photo and film, fashion shows and events, websites, PR and promotion.', 'textarea', 'Used for search engines and social previews when a page has no excerpt.' ),
			'email'       => array( 'Studio email', 'ak@akbrand.studio', 'email' ),
			'phone'       => array( 'Phone', '', 'text', 'Hidden on the site while empty.' ),
			'city'        => array( 'City', 'London', 'text' ),
			'country'     => array( 'Country', 'United Kingdom', 'text' ),
			'booking'     => array( 'Availability line', 'Taking on projects for 2027', 'text', 'Shown in the hero when there is no upcoming event.' ),
		),
		'legal'   => array(
			'legal_name'      => array( 'Legal entity name', '', 'text', 'Add “AK Brand Development Studio Ltd” here once the company is registered — not before. It then appears in the footer and as the data controller in the privacy policy. Hidden while empty.' ),
			'company_number'  => array( 'Company number', '', 'text', 'Hidden while empty.' ),
			'registered_addr' => array( 'Registered address', '', 'textarea', 'Hidden while empty.' ),
		),
		'social'  => array(
			'instagram' => array( 'Studio Instagram URL', '', 'url', 'The footer always names Instagram; it becomes a link once this is filled.' ),
			'facebook'  => array( 'Studio Facebook URL', '', 'url', 'The footer always names Facebook; it becomes a link once this is filled.' ),
			'linkedin'  => array( 'Studio LinkedIn URL', '', 'url', 'The footer always names LinkedIn; it becomes a link once this is filled.' ),
			'tiktok'    => array( 'Studio TikTok URL', '', 'url', 'Hidden while empty.' ),
		),
		'brief'   => array(
			'brief_to'     => array( 'Send briefs to', '', 'email', 'Leave empty to use the studio email. Every brief is also saved under Briefs in the dashboard.' ),
			'brief_retain' => array( 'Delete unconverted briefs after (months)', '12', 'text', 'Matches the privacy policy. Briefs marked “Won” are kept.' ),
		),
		'event'   => array(
			'event_label'      => array( 'Event label', 'London Fashion Day · Season 8', 'text' ),
			'event_title'      => array( 'Event title', 'PAST. PRESENT. NEXT.', 'text' ),
			'event_date'       => array( 'Event date', '2026-11-08', 'date', 'The site shows the event as upcoming until this date, then as the latest season.' ),
			'event_venue'      => array( 'Venue', 'Porchester Hall, Westminster', 'text' ),
			'event_note'       => array( 'Event note', 'Twelve fashion voices, one London runway: six runway slots from 13:00 to 18:00, opening with students of the London College of Contemporary Arts.', 'textarea' ),
			'event_guest_url'  => array( 'Guest registration URL', 'https://londonfashionday.co.uk/guest-registration/', 'url' ),
			'event_apply_url'  => array( 'Designer application URL', 'https://londonfashionday.co.uk/designers-reg/', 'url' ),
			'event_project'    => array( 'Project slug for the event', 'london-fashion-day-season-8', 'text', 'The project page the event links to once it has happened.' ),
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
 * The upcoming (or latest) event, with its state worked out from the date.
 *
 * @return array<string,mixed>
 */
function ak_brand_event(): array {
	$date = ak_brand_studio( 'event_date' );
	$ts   = $date ? strtotime( $date . ' 23:59:59 ' . wp_timezone_string() ) : 0;
	$ok   = $ts && ak_brand_studio( 'event_title' );
	return array(
		'enabled'   => (bool) $ok,
		'upcoming'  => $ok && $ts >= time(),
		'label'     => ak_brand_studio( 'event_label' ),
		'title'     => ak_brand_studio( 'event_title' ),
		'date'      => $date,
		'date_h'    => $ts ? wp_date( 'j F Y', $ts ) : '',
		'venue'     => ak_brand_studio( 'event_venue' ),
		'note'      => ak_brand_studio( 'event_note' ),
		'guest_url' => ak_brand_studio( 'event_guest_url' ),
		'apply_url' => ak_brand_studio( 'event_apply_url' ),
		'project'   => ak_brand_studio( 'event_project' ),
	);
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
		'event'   => __( 'Next event', 'ak-brand-core' ),
		'privacy' => __( 'Cookies & analytics', 'ak-brand-core' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'AK Studio', 'ak-brand-core' ); ?></h1>
		<p><?php esc_html_e( 'The studio’s details, used by every page, the footer, the brief form and search results. Empty fields use the default shown in grey.', 'ak-brand-core' ); ?>
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
									<textarea class="large-text" rows="3" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" placeholder="<?php echo esc_attr( $field[1] ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
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
