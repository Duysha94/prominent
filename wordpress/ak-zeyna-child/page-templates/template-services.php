<?php
/**
 * Template Name: AK — Services
 *
 * Six movements, and every service under them, named in full.
 *
 * The movements are the PRESENTATION layer. The services are the FACTUAL
 * layer, and they come from inc/projects/capabilities.php — one source, which
 * also seeds the `ak_capability` taxonomy, so the page cannot drift out of
 * step with what a project can actually be credited with.
 *
 * The rule this template exists to keep: creative naming must never hide what
 * the agency does. A movement is a heading over a list, never a summary that
 * absorbs it. Nothing here is collapsed into "and more", nothing is truncated
 * behind a toggle, and every service has an anchor of its own so it can be
 * linked to directly.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

?>
<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
<?php

$ak_movements = ak_movements();
$ak_count     = ak_service_count();
?>

<div class="aks-wrap">
	<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Practice', 'ak-zeyna-child' ); ?></span></div>
	<div class="aks-col-wide aks-section aks-section--open">
		<p class="aks-t-data"><?php esc_html_e( 'Services', 'ak-zeyna-child' ); ?></p>
		<h1 class="aks-t-hero"><?php esc_html_e( 'A brand is built across six movements — and we work in all of them.', 'ak-zeyna-child' ); ?></h1>
		<p class="aks-t-lead">
			<?php
			printf(
				/* translators: %d: number of individual services */
				esc_html__( 'Six movements, %d services. Most studios take one of these and hand you on: we produce the campaign, we produce the show, and we own the runways our clients walk on.', 'ak-zeyna-child' ),
				absint( $ak_count )
			);
			?>
		</p>
	</div>
</div>

<div class="aks-wrap">
	<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Contents', 'ak-zeyna-child' ); ?></span></div>
	<div class="aks-col-full aks-section">
		<h2 class="screen-reader-text"><?php esc_html_e( 'The six movements', 'ak-zeyna-child' ); ?></h2>
		<ul class="aks-breadth">
			<?php foreach ( $ak_movements as $ak_slug => $ak_movement ) : ?>
				<li>
					<a href="#<?php echo esc_attr( $ak_slug ); ?>">
						<b><?php echo esc_html( $ak_movement['number'] . ' ' . $ak_movement['name'] ); ?></b>
						<span><?php echo esc_html( implode( ' · ', $ak_movement['areas'] ) ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<?php foreach ( $ak_movements as $ak_slug => $ak_movement ) : ?>
	<div class="aks-wrap" id="<?php echo esc_attr( $ak_slug ); ?>">
		<div class="aks-rail"><span class="aks-rail__mark"><?php echo esc_html( $ak_movement['number'] ); ?></span></div>
		<div class="aks-col-full aks-section">
			<div class="aks-mv__head">
				<h2 class="aks-t-display"><?php echo esc_html( $ak_movement['name'] ); ?></h2>
				<p class="aks-mv__summary"><?php echo esc_html( $ak_movement['summary'] ); ?></p>
				<p class="aks-mv__areas"><?php echo esc_html( implode( ' · ', $ak_movement['areas'] ) ); ?></p>
			</div>

			<?php
			$ak_clusters = ak_movement_clusters( $ak_movement );
			if ( $ak_clusters ) :
				/*
				 * VISIBILITY carries two clusters, and they are presented with
				 * equal weight and never merged into one list: PR must not be
				 * swallowed by advertising, and advertising must not be hidden.
				 * Paid media is the amplification side of a brand system the
				 * other five movements built — never the practice itself.
				 */
				?>
				<div class="aks-mv__clusters">
					<?php foreach ( $ak_clusters as $ak_cluster ) : ?>
						<div class="aks-mv__cluster">
							<h3 class="aks-mv__cluster-name"><?php echo esc_html( $ak_cluster['label'] ); ?></h3>
							<ul class="aks-services">
								<?php foreach ( $ak_cluster['services'] as $ak_service_slug => $ak_label ) : ?>
									<li id="service-<?php echo esc_attr( $ak_service_slug ); ?>"><?php echo esc_html( $ak_label ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<ul class="aks-services">
					<?php foreach ( ak_movement_services( $ak_movement ) as $ak_service_slug => $ak_label ) : ?>
						<li id="service-<?php echo esc_attr( $ak_service_slug ); ?>"><?php echo esc_html( $ak_label ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php
			// Real evidence, when there is any. Never a placeholder shelf.
			$ak_evidence = get_posts(
				array(
					'post_type'      => AK_PROJECT_CPT,
					'post_status'    => 'publish',
					'posts_per_page' => 3,
					'tax_query'      => array(
						array(
							'taxonomy'         => 'ak_capability',
							'field'            => 'slug',
							'terms'            => $ak_slug,
							'include_children' => true,
						),
					),
					'meta_query'     => array(
						'relation' => 'OR',
						array( 'key' => 'ak_fixture', 'compare' => 'NOT EXISTS' ),
						array( 'key' => 'ak_fixture', 'value' => '1', 'compare' => '!=' ),
					),
				)
			);
			if ( $ak_evidence ) :
				?>
				<p class="aks-mv__evidence">
					<span class="aks-t-quiet"><?php esc_html_e( 'In the work', 'ak-zeyna-child' ); ?></span>
					<?php foreach ( $ak_evidence as $ak_i => $ak_project ) : ?>
						<a href="<?php echo esc_url( get_permalink( $ak_project ) ); ?>"><?php echo esc_html( get_the_title( $ak_project ) ); ?></a><?php echo $ak_i < count( $ak_evidence ) - 1 ? ' · ' : ''; ?>
					<?php endforeach; ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>

<div class="aks-wrap">
	<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Note', 'ak-zeyna-child' ); ?></span></div>
	<div class="aks-col-text aks-section">
		<p class="aks-t-body">
			<?php
			printf(
				/* translators: %s: link to the Work index */
				esc_html__( 'This page is the whole scope of the practice, complete and independent of what has been published. %s is the selected evidence, and it is not padded to match: a service is listed here because the studio does it, not because a case study exists for it yet.', 'ak-zeyna-child' ),
				'<a href="' . esc_url( get_post_type_archive_link( AK_PROJECT_CPT ) ) . '">' . esc_html__( 'Work', 'ak-zeyna-child' ) . '</a>'
			);
			?>
		</p>
	</div>
</div>
</main><!-- #primary -->

<?php
get_template_part( 'template-parts/ak-cta' );
get_footer();
