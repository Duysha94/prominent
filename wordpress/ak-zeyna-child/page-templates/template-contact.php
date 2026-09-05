<?php
/**
 * Template Name: AK — Contact
 *
 * The brief, powered by Contact Form 7.
 *
 * The form is found BY SLUG rather than by the ID baked into a shortcode,
 * because post IDs change on import and a hard-coded [contact-form-7 id="123"]
 * silently renders nothing on the destination site. Looking the form up at
 * render time survives any import.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/**
 * Locate the imported CF7 form by its slug.
 *
 * @return WP_Post|null
 */
function ak_find_brief_form() {
	if ( ! post_type_exists( 'wpcf7_contact_form' ) ) {
		return null;
	}
	$found = get_posts(
		array(
			'post_type'      => 'wpcf7_contact_form',
			'name'           => 'ak-project-brief',
			'posts_per_page' => 1,
		)
	);
	return $found ? $found[0] : null;
}

$ak_form = ak_find_brief_form();
?>

<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<section class="ak-section">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><?php printf( esc_html__( 'Start a project — %s', 'ak-zeyna-child' ), esc_html( ak_studio( 'city' ) ) ); ?></p>
			<h1 class="ak-display ak-display--hero" data-ak-cut><?php esc_html_e( 'Tell us where the brand is. We will tell you what it needs.', 'ak-zeyna-child' ); ?></h1>
			<p class="ak-lead"><?php esc_html_e( 'A few questions a founder can answer without preparation. We reply within two working days with an honest read: which movements your project actually needs, and which it does not.', 'ak-zeyna-child' ); ?></p>
		</div>
	</section>

	<section class="ak-section ak-section--rule">
		<div class="ak-wrap" style="display:grid;gap:3rem;grid-template-columns:1fr;align-items:start">
			<div class="ak-form" style="max-width:46rem">
				<?php if ( $ak_form ) : ?>
					<?php echo do_shortcode( '[contact-form-7 id="' . (int) $ak_form->ID . '" title="' . esc_attr( $ak_form->post_title ) . '"]' ); ?>
				<?php else : ?>
					<?php
					/*
					 Contact Form 7 is not active (or the form is not imported
					 yet): degrade to a mailto so the page is never a dead end.
					 */
					?>
					<p class="ak-lead"><?php esc_html_e( 'The project brief form appears here once the Contact Form 7 plugin is active and the studio content has been imported.', 'ak-zeyna-child' ); ?></p>
					<p style="margin-top:1.5rem"><a class="ak-btn ak-btn--fill" href="mailto:<?php echo esc_attr( ak_studio_email() ); ?>"><?php esc_html_e( 'Email the studio instead', 'ak-zeyna-child' ); ?></a></p>
				<?php endif; ?>
			</div>

			<aside style="border:1px solid var(--rule-strong);padding:1.5rem;max-width:24rem">
				<p class="ak-eyebrow" style="margin:0"><?php esc_html_e( 'Direct', 'ak-zeyna-child' ); ?></p>
				<p style="margin:.9rem 0 0"><a href="mailto:<?php echo esc_attr( ak_studio_email() ); ?>" style="color:var(--accent-text);text-decoration:none;font-family:var(--font-mono);font-size:.8125rem"><?php echo esc_html( ak_studio_email() ); ?></a></p>
				<p style="margin:.4rem 0 0;font-family:var(--font-mono);font-size:.625rem;letter-spacing:.1em;color:var(--text-muted)">akbrand.studio</p>
				<p style="margin:1.25rem 0 0;font-size:.8125rem;line-height:1.6;color:var(--text-muted)"><?php
					/* translators: 1: postal location, 2: the cities line. */
					printf( esc_html__( '%1$s. Working across %2$s.', 'ak-zeyna-child' ), esc_html( ak_studio_location() ), esc_html( str_replace( ' · ', ', ', ak_studio( 'cities' ) ) ) );
					?></p>
			</aside>
		</div>
	</section>

</main>

<?php
get_footer();
