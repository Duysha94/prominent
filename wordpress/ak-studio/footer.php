<?php
/**
 * Site footer. Everything above the base line is the "Site footer" pattern,
 * edited in Appearance → Patterns; the base line names the business.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$holder = ak_studio_get( 'legal_name' ) ?: ak_studio_get( 'name' );
$legal  = array_filter(
	array(
		ak_studio_get( 'company_number' ) ? sprintf( /* translators: %s: company number */ __( 'Company no. %s', 'ak-studio' ), ak_studio_get( 'company_number' ) ) : '',
		ak_studio_get( 'registered_addr' ) ? sprintf( /* translators: %s: registered office */ __( 'Registered office: %s', 'ak-studio' ), preg_replace( '/\s*\n\s*/', ', ', trim( ak_studio_get( 'registered_addr' ) ) ) ) : '',
	)
);
$pattern = ak_studio_pattern( 'footer' );
?>
<footer class="site-foot">
	<div class="wrap site-foot__body">
		<?php if ( $pattern ) : ?>
			<?php echo $pattern; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks. ?>
		<?php else : ?>
			<p class="is-style-eyebrow"><?php esc_html_e( 'Start a project', 'ak-studio' ); ?></p>
			<h2 class="wp-block-heading is-style-display-xl"><a href="<?php echo esc_url( ak_studio_contact_url() ); ?>"><?php echo ak_studio_heading( __( 'Have a brand *in mind?*', 'ak-studio' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></h2>
			<p><a href="mailto:<?php echo esc_attr( antispambot( ak_studio_get( 'email' ) ) ); ?>"><?php echo esc_html( antispambot( ak_studio_get( 'email' ) ) ); ?></a></p>
		<?php endif; ?>
	</div>

	<div class="site-foot__word" aria-hidden="true"><span>A<i>K</i> <?php esc_html_e( 'Brand Development Studio', 'ak-studio' ); ?></span></div>

	<div class="wrap site-foot__base">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( $holder ); ?><?php echo $legal ? ' · ' . esc_html( implode( ' · ', $legal ) ) : ''; ?></p>
		<p><?php echo esc_html( ak_studio_get( 'promise' ) ); ?></p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
