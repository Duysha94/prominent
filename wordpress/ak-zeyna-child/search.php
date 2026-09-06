<?php
/**
 * Search results.
 *
 * The child had no search template, so /?s= fell through to Zeyna's — parent
 * markup, parent classes, parent chrome, on a site that has otherwise left
 * that vocabulary behind. A visitor who searches is the most motivated one
 * there is, and handing them the one page that looks like a different site is
 * a poor way to reward it.
 *
 * @package ak-zeyna-child
 */

get_header();

$ak_total = (int) $GLOBALS['wp_query']->found_posts;
?>
<main id="primary" class="site-main ak-scope" data-ak-container>

<div class="aks-wrap">
	<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Search', 'ak-zeyna-child' ); ?></span></div>
	<div class="aks-col-wide aks-section aks-section--open">
		<p class="aks-t-data">
			<?php
			printf(
				/* translators: %d: number of results */
				esc_html( _n( '%d result', '%d results', $ak_total, 'ak-zeyna-child' ) ),
				absint( $ak_total )
			);
			?>
		</p>
		<h1 class="aks-t-hero"><?php echo esc_html( get_search_query() ); ?></h1>
	</div>
</div>

<div class="aks-wrap">
	<div class="aks-rail"></div>
	<div class="aks-col-full aks-section">
		<?php if ( have_posts() ) : ?>
			<ul class="aks-index">
				<?php
				while ( have_posts() ) :
					the_post();
					$ak_type_obj = get_post_type_object( get_post_type() );
					$ak_kind     = $ak_type_obj ? $ak_type_obj->labels->singular_name : get_post_type();
					$ak_code     = AK_PROJECT_CPT === get_post_type() ? ak_project_code() : '';
					?>
					<li class="aks-index__row">
						<a class="aks-index__link" href="<?php the_permalink(); ?>">
							<span class="aks-index__code"><?php echo esc_html( $ak_code ); ?></span>
							<span class="aks-index__client"><?php echo esc_html( $ak_kind ); ?></span>
							<span class="aks-index__title"><?php the_title(); ?></span>
							<span class="aks-index__disc">
								<?php if ( has_excerpt() ) : ?>
									<span class="aks-index__tag"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 8, '…' ) ); ?></span>
								<?php endif; ?>
							</span>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => esc_html__( 'Previous', 'ak-zeyna-child' ),
					'next_text' => esc_html__( 'Next', 'ak-zeyna-child' ),
				)
			);
			?>

		<?php else : ?>
			<p class="aks-t-lead"><?php esc_html_e( 'Nothing matched that.', 'ak-zeyna-child' ); ?></p>
			<p class="aks-t-body" style="margin-top:1rem">
				<?php
				$ak_work_href = get_post_type_archive_link( AK_PROJECT_CPT );
				printf(
					/* translators: 1: work URL, 2: services URL */
					wp_kses(
						__( 'The published projects are in <a href="%1$s">the work</a>, and the full scope of the practice is on <a href="%2$s">Services</a>.', 'ak-zeyna-child' ),
						array( 'a' => array( 'href' => array() ) )
					),
					esc_url( $ak_work_href ? $ak_work_href : home_url( '/work/' ) ),
					esc_url( home_url( '/services/' ) )
				);
				?>
			</p>
			<div style="margin-top:2rem;max-width:32rem"><?php get_search_form(); ?></div>
		<?php endif; ?>
	</div>
</div>

</main><!-- #primary -->

<?php
get_footer();
