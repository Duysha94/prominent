<?php
/**
 * Studio details: one option, owned by the plugin, so the values survive a
 * theme switch (theme mods do not — see docs/THEME-PLUGIN-ARCHITECTURE.md).
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults are the founders' own facts. Anything marked PLACEHOLDER must be
 * confirmed before launch.
 *
 * @return array<string,string>
 */
function ak_brand_studio_defaults(): array {
	return array(
		'name'        => 'AK Brand Development Studio',
		'short'       => 'AK',
		'discipline'  => 'Brand & fashion development',
		'promise'     => 'From the initial idea to international presence, we support projects at every stage of their development.',
		'description' => 'AK Brand Development Studio is an independent creative and strategic practice specialising in brand development, fashion consulting and creative production. We work with designers, entrepreneurs and growing businesses to build strong brands, develop clear positioning and create meaningful visibility.',
		'argument'    => 'Most advisories hand you a deck and wish you luck. We produce the campaign, we produce the show, and we own the runways our clients walk on.',
		'email'       => 'hello@akbrand.studio', // PLACEHOLDER — confirm the real address.
		'city'        => 'London',
		'country'     => 'United Kingdom',
		'booking'     => 'Booking SS27 projects',
		'instagram'   => '',
		'linkedin'    => '',
	);
}

/**
 * One studio value, filtered so a theme or site can override it.
 *
 * @param string $key Key from ak_brand_studio_defaults().
 */
function ak_brand_studio( string $key ): string {
	$defaults = ak_brand_studio_defaults();
	$saved    = get_option( 'akbrand_studio', array() );
	$value    = is_array( $saved ) && isset( $saved[ $key ] ) && '' !== $saved[ $key ] ? $saved[ $key ] : ( $defaults[ $key ] ?? '' );
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
	foreach ( array_keys( ak_brand_studio_defaults() ) as $key ) {
		$raw = isset( $input[ $key ] ) ? wp_unslash( $input[ $key ] ) : '';
		if ( 'email' === $key ) {
			$clean[ $key ] = sanitize_email( $raw );
		} elseif ( in_array( $key, array( 'instagram', 'linkedin' ), true ) ) {
			$clean[ $key ] = esc_url_raw( $raw );
		} elseif ( in_array( $key, array( 'promise', 'description', 'argument' ), true ) ) {
			$clean[ $key ] = sanitize_textarea_field( $raw );
		} else {
			$clean[ $key ] = sanitize_text_field( $raw );
		}
	}
	return $clean;
}

function ak_brand_render_settings(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$defaults = ak_brand_studio_defaults();
	$saved    = get_option( 'akbrand_studio', array() );
	$long     = array( 'promise', 'description', 'argument' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'AK Studio', 'ak-brand-core' ); ?></h1>
		<p><?php esc_html_e( 'The studio’s details. Empty fields fall back to the defaults shown as placeholders.', 'ak-brand-core' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'ak_studio' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( $defaults as $key => $default ) : ?>
					<tr>
						<th scope="row"><label for="akbrand-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( ucfirst( $key ) ); ?></label></th>
						<td>
							<?php if ( in_array( $key, $long, true ) ) : ?>
								<textarea class="large-text" rows="3" id="akbrand-<?php echo esc_attr( $key ); ?>" name="akbrand_studio[<?php echo esc_attr( $key ); ?>]" placeholder="<?php echo esc_attr( $default ); ?>"><?php echo esc_textarea( $saved[ $key ] ?? '' ); ?></textarea>
							<?php else : ?>
								<input class="regular-text" type="text" id="akbrand-<?php echo esc_attr( $key ); ?>" name="akbrand_studio[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $saved[ $key ] ?? '' ); ?>" placeholder="<?php echo esc_attr( $default ); ?>">
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
