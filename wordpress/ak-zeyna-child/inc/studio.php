<?php
/**
 * The studio's own facts, in one place.
 *
 * Every piece of business data the site states about itself — the address it
 * gives, the email it answers on, the cities it names, the profiles it links
 * to — is read from here and only from here. Before this file the email was a
 * function in functions.php, the location was typed into footer.php, the
 * cities were typed into footer.php AND front-page.php, and the social
 * networks were a hardcoded array of empty strings that rendered five dead
 * `href="#"` links on every page of the site.
 *
 * Each value is a theme mod with a sensible default, so the founders change
 * any of it in Customizer → AK Studio without touching a file, and nothing
 * has to be edited twice.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The networks the studio can list, in the order they are shown.
 *
 * A network with no URL saved is simply not printed. That is the whole point:
 * an empty profile field must produce no markup at all, never a placeholder
 * link that goes nowhere.
 *
 * @return array<string,string> Setting key => display label.
 */
function ak_social_networks() {
	return array(
		'ak_social_instagram' => 'Instagram',
		'ak_social_linkedin'  => 'LinkedIn',
		'ak_social_facebook'  => 'Facebook',
		'ak_social_youtube'   => 'YouTube',
		'ak_social_tiktok'    => 'TikTok',
		'ak_social_pinterest' => 'Pinterest',
	);
}

/**
 * The social profiles that actually exist, ready to print.
 *
 * @return array<string,string> Label => URL. Empty when nothing is set.
 */
function ak_socials() {
	$out = array();
	foreach ( ak_social_networks() as $key => $label ) {
		$url = trim( (string) get_theme_mod( $key, '' ) );
		if ( $url ) {
			$out[ $label ] = $url;
		}
	}
	return $out;
}

/**
 * One studio fact.
 *
 * @param string $key One of: email, phone, city, country, address, cities, name, tagline.
 * @return string
 */
function ak_studio( $key ) {
	$defaults = array(
		'name'    => 'AK Brand Development Studio',
		'tagline' => 'Fashion & Brand Advisory',
		'email'   => 'ak@akbrand.studio',
		'phone'   => '',
		'city'    => 'London',
		'country' => 'United Kingdom',
		'cities'  => 'London · Paris · Dubai',
	);

	$default = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$value   = (string) get_theme_mod( 'ak_studio_' . $key, $default );

	/**
	 * Filter a single studio fact.
	 *
	 * @param string $value Resolved value.
	 * @param string $key   Fact key.
	 */
	return apply_filters( 'ak_studio_fact', $value, $key );
}

/**
 * The postal line, assembled from the parts rather than typed out again.
 *
 * @return string
 */
function ak_studio_location() {
	return trim( ak_studio( 'city' ) . ', ' . ak_studio( 'country' ), ', ' );
}

/**
 * Back-compat: the email used to come from here.
 *
 * @return string
 */
function ak_studio_email() {
	return apply_filters( 'ak_studio_email', ak_studio( 'email' ) );
}

/**
 * Register every studio fact in the Customizer.
 *
 * These sit in their own section rather than beside the logo and video
 * uploads, because they are the facts an owner edits after launch and the
 * uploads are the assets they set once.
 */
add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'ak_studio_facts',
			array(
				'title'       => __( 'AK Studio — contact & profiles', 'ak-zeyna-child' ),
				'priority'    => 31,
				'description' => __( 'The single source of truth for everything the site says about the studio. The footer, the contact page, the schema and the social profiles all read from here. Leave a profile empty and it is not shown at all.', 'ak-zeyna-child' ),
			)
		);

		$fields = array(
			'ak_studio_name'    => array( __( 'Studio name', 'ak-zeyna-child' ), 'AK Brand Development Studio', 'sanitize_text_field' ),
			'ak_studio_tagline' => array( __( 'Tagline', 'ak-zeyna-child' ), 'Fashion & Brand Advisory', 'sanitize_text_field' ),
			'ak_studio_email'   => array( __( 'Email', 'ak-zeyna-child' ), 'ak@akbrand.studio', 'sanitize_email' ),
			'ak_studio_phone'   => array( __( 'Phone (optional — hidden when empty)', 'ak-zeyna-child' ), '', 'sanitize_text_field' ),
			'ak_studio_city'    => array( __( 'City', 'ak-zeyna-child' ), 'London', 'sanitize_text_field' ),
			'ak_studio_country' => array( __( 'Country', 'ak-zeyna-child' ), 'United Kingdom', 'sanitize_text_field' ),
			'ak_studio_cities'  => array( __( 'Cities line', 'ak-zeyna-child' ), 'London · Paris · Dubai', 'sanitize_text_field' ),
		);

		foreach ( $fields as $key => $spec ) {
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => $spec[1],
					'sanitize_callback' => $spec[2],
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				$key,
				array(
					'label'   => $spec[0],
					'section' => 'ak_studio_facts',
					'type'    => 'text',
				)
			);
		}

		foreach ( ak_social_networks() as $key => $label ) {
			$wp_customize->add_setting(
				$key,
				array(
					'default'           => '',
					'sanitize_callback' => 'esc_url_raw',
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				$key,
				array(
					/* translators: %s: social network name. */
					'label'       => sprintf( __( '%s profile URL', 'ak-zeyna-child' ), $label ),
					'section'     => 'ak_studio_facts',
					'type'        => 'url',
					'input_attrs' => array( 'placeholder' => 'https://' ),
				)
			);
		}
	}
);
