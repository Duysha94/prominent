<?php
/**
 * The brief, right on the front page.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$email = ak_studio_get( 'email' );
?>
<section class="brief-section section" id="brief">
	<div class="wrap brief-section__grid">
		<div class="brief-section__intro">
			<p class="eyebrow" data-rise><?php esc_html_e( 'Start a project', 'ak-studio' ); ?></p>
			<h2 class="display" data-split><?php echo ak_studio_heading( __( 'Tell us what *you’re building.*', 'ak-studio' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<p class="lede" data-rise><?php esc_html_e( 'A few lines are enough to start. We read every brief ourselves and reply with questions, a time to talk, or a proposal.', 'ak-studio' ); ?></p>
			<p class="brief-section__mail" data-rise><?php esc_html_e( 'Or write to', 'ak-studio' ); ?> <a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
		</div>
		<div class="brief-section__form" data-rise>
			<?php
			if ( function_exists( 'ak_brand_brief_form' ) ) {
				echo ak_brand_brief_form( array( 'compact' => true, 'id' => 'brief-home', 'source' => __( 'Home page', 'ak-studio' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped by the plugin.
			} else {
				echo '<p><a class="btn btn--accent" href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html__( 'Email the studio', 'ak-studio' ) . '</a></p>';
			}
			?>
		</div>
	</div>
</section>
