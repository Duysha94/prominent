<?php
/**
 * The Work index.
 *
 * Two renderings, and the material chooses between them:
 *
 *   NO MEDIA     an editorial register — code, relationship, name, type,
 *                address. Dense, scannable, and honest about being a record.
 *   REAL MEDIA   a visual composition led by the cover.
 *
 * They coexist. A record with a cover becomes a card; a record without stays a
 * typographic entry. The index therefore becomes media-led on its own, one
 * project at a time, as material arrives — with no fake covers invented to
 * force a grid, and no wall of placeholder plates while it is still empty.
 *
 * Filters are generated from published content: a filter renders when a real
 * published project falls under it, and not before.
 *
 * @package ak-zeyna-child
 */

get_header();

?>
<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
<?php

$ak_layout   = ak_work_layout();
$ak_filters  = ak_work_filters();
$ak_projects = ak_published_projects();
$ak_current  = isset( $_GET['filter'] ) ? sanitize_key( wp_unslash( $_GET['filter'] ) ) : 'all';
if ( ! isset( $ak_filters[ $ak_current ] ) ) {
	$ak_current = 'all';
}
$ak_archive  = get_post_type_archive_link( AK_PROJECT_CPT );
$ak_untyped  = 0;
foreach ( $ak_projects as $ak_p ) {
	if ( ! ak_project_type( $ak_p->ID ) ) {
		$ak_untyped++;
	}
}
?>

<div class="aks-wrap">
	<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Index', 'ak-zeyna-child' ); ?></span></div>
	<div class="aks-col-wide aks-section aks-section--open">
		<p class="aks-t-data"><?php esc_html_e( 'Work', 'ak-zeyna-child' ); ?></p>
		<h1 class="aks-t-hero"><?php esc_html_e( 'Selected records, one practice behind them.', 'ak-zeyna-child' ); ?></h1>
		<p class="aks-t-lead">
			<?php
			printf(
				/* translators: %s: link to the Services page */
				esc_html__( 'Platforms the studio founded, media it publishes, brands it owns and engagements it was commissioned for. What the studio can do is on %s; this page is what it has published.', 'ak-zeyna-child' ),
				'<a href="' . esc_url( home_url( '/services/' ) ) . '">' . esc_html__( 'Services', 'ak-zeyna-child' ) . '</a>'
			);
			?>
		</p>
	</div>
</div>

<div class="aks-wrap">
	<div class="aks-rail"><span class="aks-rail__mark"><?php echo esc_html( $ak_filters[ $ak_current ]['label'] ); ?></span></div>
	<div class="aks-col-full aks-section">
		<h2 class="screen-reader-text"><?php esc_html_e( 'All projects', 'ak-zeyna-child' ); ?></h2>

		<?php if ( count( $ak_filters ) > 1 ) : ?>
			<ul class="aks-filters">
				<?php foreach ( $ak_filters as $ak_slug => $ak_filter ) : ?>
					<li>
						<a href="<?php echo esc_url( 'all' === $ak_slug ? $ak_archive : add_query_arg( 'filter', $ak_slug, $ak_archive ) ); ?>"
							<?php echo $ak_slug === $ak_current ? ' aria-current="true"' : ''; ?>>
							<?php echo esc_html( $ak_filter['label'] ); ?>
							<span class="aks-ct"><?php echo esc_html( $ak_filter['count'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( ! $ak_projects ) : ?>
			<p class="aks-t-body"><?php esc_html_e( 'Records are being prepared.', 'ak-zeyna-child' ); ?></p>

		<?php elseif ( 'register' === $ak_layout ) : ?>
			<ul class="aks-index">
				<?php
				foreach ( $ak_projects as $ak_project ) :
					if ( 'all' !== $ak_current && ak_project_filter( $ak_project->ID ) !== $ak_current ) {
						continue;
					}
					$ak_type = ak_project_type( $ak_project->ID );
					$ak_url  = ak_project_meta( 'ak_url', '', $ak_project->ID );
					?>
					<li class="aks-index__row">
						<a class="aks-index__link" href="<?php echo esc_url( get_permalink( $ak_project ) ); ?>">
							<span class="aks-index__code"><?php echo esc_html( ak_project_code( $ak_project->ID ) ); ?></span>
							<span class="aks-index__client"><?php echo esc_html( ak_relationship_label( $ak_project->ID ) ); ?></span>
							<span class="aks-index__title"><?php echo esc_html( get_the_title( $ak_project ) ); ?></span>
							<span class="aks-index__disc">
								<?php if ( $ak_type ) : ?>
									<span class="aks-index__tag"><?php echo esc_html( $ak_type->name ); ?></span>
								<?php endif; ?>
								<?php if ( $ak_url ) : ?>
									<span class="aks-index__tag"><?php echo esc_html( wp_parse_url( $ak_url, PHP_URL_HOST ) ); ?></span>
								<?php endif; ?>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

		<?php else : ?>
			<ul class="aks-cards">
				<?php
				foreach ( $ak_projects as $ak_project ) :
					if ( 'all' !== $ak_current && ak_project_filter( $ak_project->ID ) !== $ak_current ) {
						continue;
					}
					$ak_type  = ak_project_type( $ak_project->ID );
					$ak_cover = ak_project_cover( $ak_project->ID );
					$ak_url   = ak_project_meta( 'ak_url', '', $ak_project->ID );
					?>
					<li class="aks-card<?php echo $ak_cover ? '' : ' aks-card--plain'; ?> ak-rise">
						<a href="<?php echo esc_url( get_permalink( $ak_project ) ); ?>">
							<?php if ( $ak_cover ) : ?>
								<div class="aks-card__media">
									<?php
									if ( is_int( $ak_cover ) ) {
										echo wp_get_attachment_image( $ak_cover, 'large', false, array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title( $ak_project ) ) ) );
									} else {
										printf(
											'<img src="%s" loading="lazy" decoding="async" referrerpolicy="no-referrer" width="1200" height="900" alt="%s">',
											esc_url( $ak_cover ),
											/* translators: %s: project title */
											esc_attr( sprintf( __( '%s — current front page', 'ak-zeyna-child' ), get_the_title( $ak_project ) ) )
										);
									}
									?>
								</div>
							<?php endif; ?>
							<div class="aks-card__meta">
								<?php if ( $ak_type ) : ?>
									<span class="aks-card__type"><?php echo esc_html( $ak_type->name ); ?></span>
								<?php endif; ?>
								<span class="aks-card__rel"><?php echo esc_html( ak_relationship_label( $ak_project->ID ) ); ?></span>
							</div>
							<h3 class="aks-card__name"><?php echo esc_html( get_the_title( $ak_project ) ); ?></h3>
							<?php if ( $ak_url ) : ?>
								<p class="aks-card__caps"><?php echo esc_html( wp_parse_url( $ak_url, PHP_URL_HOST ) ); ?></p>
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $ak_untyped && 'all' === $ak_current ) : ?>
			<p class="aks-t-small aks-quiet">
				<?php
				printf(
					/* translators: %d: number of records with no established type */
					esc_html( _n( '%d record carries no type yet: a live address and a client relationship establish that an engagement happened, not what it was, and the studio does not publish a classification it cannot stand behind.', '%d records carry no type yet: a live address and a client relationship establish that an engagement happened, not what it was, and the studio does not publish a classification it cannot stand behind.', $ak_untyped, 'ak-zeyna-child' ) ),
					absint( $ak_untyped )
				);
				?>
			</p>
		<?php endif; ?>
	</div>
</div>

</main><!-- #primary -->

<?php
get_template_part( 'template-parts/ak-cta' );
get_footer();
