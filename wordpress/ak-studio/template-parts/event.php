<?php
/**
 * The season the studio is producing next — or the latest one once it has happened.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$e = ak_studio_event();
if ( empty( $e['enabled'] ) ) {
	return;
}
$project = $e['project'] ? get_page_by_path( $e['project'], OBJECT, 'ak_project' ) : null;
$details = $project ? ak_studio_project( $project ) : array();
$image   = ! empty( $details['gallery'] ) ? (int) $details['gallery'][0] : ( $project ? (int) get_post_thumbnail_id( $project ) : 0 );
$ts      = strtotime( $e['date'] );
$show    = get_page_by_path( 'show-in-london' );
?>
<section class="event section" id="<?php echo esc_attr( $args['id'] ?? 'event' ); ?>">
	<div class="wrap event__grid">
		<div class="event__media" data-reveal>
			<?php echo ak_studio_img( $image, '(max-width: 900px) 92vw, 40vw', array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<div class="event__body">
			<p class="eyebrow" data-rise><?php echo esc_html( $e['upcoming'] ? __( 'Next on stage', 'ak-studio' ) : __( 'Latest season', 'ak-studio' ) ); ?> — <?php echo esc_html( $e['label'] ); ?></p>
			<h2 class="event__title display" data-split><?php echo esc_html( $e['title'] ); ?></h2>
			<div class="event__when" data-rise>
				<span class="event__day"><?php echo esc_html( wp_date( 'j', $ts ) ); ?></span>
				<span class="event__month"><?php echo esc_html( wp_date( 'F Y', $ts ) ); ?><br><?php echo esc_html( $e['venue'] ); ?></span>
			</div>
			<?php if ( $e['note'] ) : ?>
				<p class="lede" data-rise><?php echo esc_html( $e['note'] ); ?></p>
			<?php endif; ?>
			<div class="event__ctas" data-rise>
				<?php if ( $e['upcoming'] && $e['guest_url'] ) : ?>
					<a class="btn btn--accent" href="<?php echo esc_url( $e['guest_url'] ); ?>" target="_blank" rel="noopener" data-magnetic><?php esc_html_e( 'Register as a guest', 'ak-studio' ); ?><?php echo ak_studio_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php elseif ( $project ) : ?>
					<a class="btn btn--accent" href="<?php echo esc_url( get_permalink( $project ) ); ?>" data-magnetic><?php esc_html_e( 'See the season', 'ak-studio' ); ?><?php echo ak_studio_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php endif; ?>
				<?php if ( $show && ! is_page( $show->ID ) ) : ?>
					<a class="btn btn--ghost" href="<?php echo esc_url( get_permalink( $show ) ); ?>" data-magnetic><?php esc_html_e( 'Show at a future season', 'ak-studio' ); ?></a>
				<?php elseif ( $project && $e['upcoming'] ) : ?>
					<a class="btn btn--ghost" href="<?php echo esc_url( get_permalink( $project ) ); ?>" data-magnetic><?php esc_html_e( 'About the season', 'ak-studio' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
