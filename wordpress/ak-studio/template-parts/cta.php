<?php
/**
 * Closing call to action: a short line and the way in.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$need  = (string) ( $args['need'] ?? '' );
$title = (string) ( $args['title'] ?? '' ) ?: __( 'Ready when *you are.*', 'ak-studio' );
?>
<section class="cta section">
	<div class="wrap cta__inner">
		<h2 class="display cta__title" data-split><?php echo ak_studio_heading( $title ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<div class="cta__side" data-rise>
			<p class="lede"><?php esc_html_e( 'Send a brief — a few lines are enough. We read every one ourselves and reply with questions, a time to talk, or a proposal.', 'ak-studio' ); ?></p>
			<a class="btn btn--accent" href="<?php echo esc_url( ak_studio_contact_url( $need ) ); ?>" data-magnetic><?php esc_html_e( 'Start a project', 'ak-studio' ); ?><?php echo ak_studio_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
	</div>
</section>
