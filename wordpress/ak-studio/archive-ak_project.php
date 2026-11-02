<?php
/**
 * Work: every project, filterable by platform.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main">
	<?php
	ak_studio_page_header(
		array(
			'eyebrow' => __( 'Work', 'ak-studio' ),
			'heading' => __( 'Seasons, campaigns *and brands.*', 'ak-studio' ),
			'lede'    => __( 'The runways we organise and the work we produce on them: concepts, campaign films, show days, partnerships and press — from London and Odesa.', 'ak-studio' ),
		)
	);
	?>
	<section class="section section--tight">
		<div class="wrap">
			<div class="filters" role="group" aria-label="<?php esc_attr_e( 'Filter projects', 'ak-studio' ); ?>" data-filters>
				<button type="button" class="filter is-on" aria-pressed="true" data-filter="*"><?php esc_html_e( 'All', 'ak-studio' ); ?></button>
				<button type="button" class="filter" aria-pressed="false" data-filter="lfd"><?php esc_html_e( 'London Fashion Day', 'ak-studio' ); ?></button>
				<button type="button" class="filter" aria-pressed="false" data-filter="ofd"><?php esc_html_e( 'Odessa Fashion Day', 'ak-studio' ); ?></button>
				<button type="button" class="filter" aria-pressed="false" data-filter="brand"><?php esc_html_e( 'Brands', 'ak-studio' ); ?></button>
			</div>
			<?php if ( have_posts() ) : ?>
				<div class="card-grid" data-filter-grid>
					<?php
					$i = 0;
					while ( have_posts() ) :
						the_post();
						ak_studio_project_card( get_post(), 0 === $i % 3 ? 'card--tall' : '' );
						++$i;
					endwhile;
					?>
				</div>
				<p class="filters__empty" hidden data-filter-empty><?php esc_html_e( 'Nothing here yet.', 'ak-studio' ); ?></p>
			<?php else : ?>
				<p class="lede"><?php esc_html_e( 'Projects will appear here as soon as they are published.', 'ak-studio' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php ak_studio_cta(); ?>
</main>
<?php
get_footer();
