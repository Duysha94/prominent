<?php
/**
 * Pre-footer CTA — every page closes on the same invitation.
 *
 * Lives INSIDE `[data-ak-container]` (unlike the Seam), so it animates in
 * with each page rather than sitting static.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ak_contact = get_page_by_path( 'contact' );
$ak_contact_url = $ak_contact ? get_permalink( $ak_contact ) : home_url( '/contact/' );
?>
<section class="ak-section ak-cta">
	<div class="ak-wrap">
		<p class="ak-eyebrow">
			<span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span>
			<?php esc_html_e( 'Fashion & Brand Advisory — London', 'ak-zeyna-child' ); ?>
		</p>
		<h2 class="ak-display ak-vf" data-ak-cut><?php esc_html_e( 'Let us take your measurements.', 'ak-zeyna-child' ); ?></h2>
		<div class="ak-cta__row">
			<a class="ak-btn ak-btn--fill" href="<?php echo esc_url( $ak_contact_url ); ?>"><?php esc_html_e( 'Start a project', 'ak-zeyna-child' ); ?></a>
			<a class="ak-btn ak-btn--line" href="mailto:<?php echo esc_attr( ak_studio_email() ); ?>"><?php echo esc_html( ak_studio_email() ); ?></a>
		</div>
	</div>
</section>
