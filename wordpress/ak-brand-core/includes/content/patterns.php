<?php
/**
 * Synced patterns: blocks shared by many pages, edited once under
 * Appearance → Patterns (or from any page that uses them).
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string,array{title:string,content:string}>
 */
function ak_brand_seed_patterns(): array {
	$link = static fn( string $label, string $url ): string => '<a href="' . esc_url( $url ) . '">' . $label . '</a>';
	return array(
		'cta'    => array(
			'title'   => 'Call to action',
			'content' => ak_b_styled_group(
				ak_b_display( 'Ready when <em>you are.</em>' ) .
				ak_b_lead( 'Send a brief — a few lines are enough. We read every one ourselves and reply with questions, a time to talk, or a proposal.' ) .
				ak_b_buttons( array( array( 'Start a project', '/contact/' ) ) ),
				'cta'
			),
		),
		'footer' => array(
			'title'   => 'Site footer',
			'content' =>
				ak_b_eyebrow( 'Start a project' ) .
				ak_b_display( '<a href="/contact/">Have a brand <em>in mind?</em></a>', 2, 'display-xl' ) .
				ak_b_columns(
					array(
						ak_b_eyebrow( 'Studio' ) .
						ak_b_p( '<strong>[ak_studio_email]</strong>' ) .
						ak_b_p( 'London, United Kingdom' ) .
						ak_b_list( array( 'Instagram', 'Facebook', 'LinkedIn' ) ),
						ak_b_eyebrow( 'Services' ) .
						ak_b_list(
							array(
								$link( 'Brand strategy', '/services/brand-strategy/' ),
								$link( 'Identity &amp; creative direction', '/services/brand-identity/' ),
								$link( 'Websites &amp; digital', '/services/website-development/' ),
								$link( 'Photo &amp; film', '/services/photo-video-production/' ),
								$link( 'Shows &amp; events', '/services/fashion-show-production/' ),
								$link( 'PR &amp; promotion', '/services/pr-and-promotion/' ),
							)
						),
						ak_b_eyebrow( 'Our platforms' ) .
						ak_b_list(
							array(
								$link( 'London Fashion Day', 'https://londonfashionday.co.uk/' ),
								$link( 'Odessa Fashion Day', 'https://ofd.org.ua/' ),
								$link( 'KEKA', 'https://keka.design/' ),
								$link( 'COOL’BABA', 'https://coolbaba.in.ua/' ),
							)
						),
						ak_b_eyebrow( 'Site' ) .
						ak_b_list(
							array(
								$link( 'Work', '/work/' ),
								$link( 'Journal', '/journal/' ),
								$link( 'Privacy Policy', '/privacy-policy/' ),
								$link( 'Cookie Policy', '/cookie-policy/' ),
								'<a href="#cookie-settings" data-consent-open>Cookie settings</a>',
							)
						),
					)
				),
		),
	);
}

/**
 * A synced pattern's id by key.
 */
function ak_brand_pattern_id( string $key ): int {
	$ids = get_option( 'akbrand_patterns', array() );
	$id  = is_array( $ids ) ? (int) ( $ids[ $key ] ?? 0 ) : 0;
	return $id && 'wp_block' === get_post_type( $id ) ? $id : 0;
}

/**
 * Rendered HTML of a synced pattern (blocks and shortcodes), for templates.
 */
function ak_brand_render_pattern( string $key ): string {
	$id = ak_brand_pattern_id( $key );
	if ( ! $id || 'publish' !== get_post_status( $id ) ) {
		return '';
	}
	$html = do_shortcode( do_blocks( (string) get_post_field( 'post_content', $id ) ) );
	if ( 'footer' === $key ) {
		// "Instagram", "Facebook", "LinkedIn" and "TikTok" lines link to the studio's profiles once they are set in Settings → AK Studio.
		foreach ( array( 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'tiktok' => 'TikTok' ) as $field => $label ) {
			$url = ak_brand_studio( $field );
			if ( $url ) {
				$html = preg_replace( '#<li>\s*' . preg_quote( $label, '#' ) . '\s*</li>#i', '<li><a href="' . esc_url( $url ) . '" rel="me noopener" target="_blank">' . esc_html( $label ) . '</a></li>', $html );
			}
		}
	}
	return $html;
}
