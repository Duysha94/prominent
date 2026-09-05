<?php
/**
 * One project.
 *
 * The project type selects a presentation mode; the mode is a rendering
 * strategy, not a different design system. With no type the mode is `record`:
 * title, relationship, address, whatever media exists — nothing claimed
 * beyond what is known.
 *
 * The Website module is one section among many, wherever the owner placed it.
 * When it resolves to UNAVAILABLE it renders nothing at all: no frame, no
 * plate, no status message. The project stands on its own media.
 *
 * @package ak-zeyna-child
 */

get_header();

?>
<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
<?php

while ( have_posts() ) :
	the_post();

	$ak_id      = get_the_ID();
	$ak_mode    = ak_project_mode( $ak_id );
	$ak_type    = ak_project_type( $ak_id );
	$ak_rel     = ak_relationship_label( $ak_id );
	$ak_code    = ak_project_code( $ak_id );
	$ak_url     = ak_project_meta( 'ak_url', '', $ak_id );
	$ak_owner   = ak_project_meta( 'ak_owner', '', $ak_id );
	$ak_year    = ak_project_meta( 'ak_year', '', $ak_id );
	$ak_place   = ak_project_meta( 'ak_location', '', $ak_id );
	$ak_module  = ak_website_module( $ak_id );
	$ak_cover   = ak_project_cover( $ak_id );
	$ak_archive = get_post_type_archive_link( AK_PROJECT_CPT );
	?>

	<?php if ( ak_project_meta( 'ak_fixture', false, $ak_id ) ) : ?>
		<div class="aks-wrap"><div class="aks-rail"></div><div class="aks-col-full">
			<div class="aks-fixture" data-label="<?php esc_attr_e( 'Internal · not published', 'ak-zeyna-child' ); ?>">
				<?php esc_html_e( 'A structural fixture, visible to you because you are logged in. It is excluded from the Work index, the filters, filter counts, related work, the homepage, feeds and the sitemap, and returns 404 to the public. Delete it once the real project exists.', 'ak-zeyna-child' ); ?>
			</div>
		</div></div>
	<?php endif; ?>

	<div class="aks-wrap">
		<div class="aks-rail"><span class="aks-rail__mark"><?php echo esc_html( $ak_code ); ?></span></div>
		<div class="aks-col-wide aks-section aks-section--open">
			<p class="aks-t-data">
				<a href="<?php echo esc_url( $ak_archive ); ?>"><?php esc_html_e( 'Work', 'ak-zeyna-child' ); ?></a>
				<?php if ( $ak_type ) : ?> · <?php echo esc_html( $ak_type->name ); ?><?php endif; ?>
				<?php if ( $ak_rel ) : ?> · <?php echo esc_html( $ak_rel ); ?><?php endif; ?>
			</p>
			<h1 class="aks-t-hero"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="aks-t-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>

		<div class="aks-col-full aks-spec-wrap">
			<div class="aks-spec">
				<?php
				$ak_cells = array_filter(
					array(
						__( 'Owner', 'ak-zeyna-child' )        => $ak_owner,
						__( 'Relationship', 'ak-zeyna-child' ) => $ak_rel,
						__( 'Type', 'ak-zeyna-child' )         => $ak_type ? $ak_type->name : '',
						__( 'Year', 'ak-zeyna-child' )         => $ak_year,
						__( 'Location', 'ak-zeyna-child' )     => $ak_place,
						__( 'Code', 'ak-zeyna-child' )         => $ak_code,
					)
				);
				foreach ( $ak_cells as $ak_key => $ak_value ) :
					?>
					<div class="aks-spec__cell">
						<span class="aks-spec__k"><?php echo esc_html( $ak_key ); ?></span>
						<span class="aks-spec__v"><?php echo esc_html( $ak_value ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php if ( $ak_cover && is_int( $ak_cover ) ) : ?>
			<div class="aks-col-full">
				<div class="aks-case__hero"><?php echo wp_get_attachment_image( $ak_cover, 'full', false, array( 'alt' => esc_attr( get_the_title() ) ) ); ?></div>
			</div>
		<?php endif; ?>
	</div>

	<?php
	/*
	 * The capabilities AK actually delivered, when any have been recorded.
	 * Grouped by movement so the editorial layer is visible without hiding a
	 * single service name beneath it.
	 */
	$ak_caps = get_the_terms( $ak_id, 'ak_capability' );
	if ( $ak_caps && ! is_wp_error( $ak_caps ) ) :
		?>
		<div class="aks-wrap">
			<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Delivered', 'ak-zeyna-child' ); ?></span></div>
			<div class="aks-col-wide aks-section">
				<h2 class="aks-t-display"><?php esc_html_e( 'What we delivered', 'ak-zeyna-child' ); ?></h2>
				<ul class="aks-caps">
					<?php foreach ( $ak_caps as $ak_cap ) : ?>
						<?php if ( $ak_cap->parent ) : ?>
							<li><?php echo esc_html( $ak_cap->name ); ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<?php ak_render_modules( $ak_id, $ak_mode, $ak_module ); ?>

	<?php
	// Next project: the following published record, wrapping to the first.
	$ak_all  = ak_published_projects();
	$ak_next = null;
	foreach ( $ak_all as $ak_i => $ak_p ) {
		if ( $ak_p->ID === $ak_id ) {
			$ak_next = isset( $ak_all[ $ak_i + 1 ] ) ? $ak_all[ $ak_i + 1 ] : ( isset( $ak_all[0] ) && $ak_all[0]->ID !== $ak_id ? $ak_all[0] : null );
			break;
		}
	}
	if ( $ak_next ) :
		?>
		<div class="aks-wrap"><div class="aks-rail"></div><div class="aks-col-full aks-section">
			<a class="aks-next" href="<?php echo esc_url( get_permalink( $ak_next ) ); ?>">
				<div>
					<span class="aks-t-quiet"><?php echo esc_html( sprintf( /* translators: %s: relationship label */ __( 'Next · %s', 'ak-zeyna-child' ), ak_relationship_label( $ak_next->ID ) ) ); ?></span>
					<h2 class="aks-next__name"><?php echo esc_html( get_the_title( $ak_next ) ); ?></h2>
				</div>
				<span class="aks-t-data"><?php echo esc_html( ak_project_code( $ak_next->ID ) ); ?> →</span>
			</a>
		</div></div>
	<?php endif; ?>

	<?php
endwhile;
?>
</main><!-- #primary -->
<?php

get_template_part( 'template-parts/ak-cta' );
get_footer();
