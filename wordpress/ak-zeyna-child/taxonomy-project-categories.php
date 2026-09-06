<?php
/**
 * Legacy portfolio taxonomy — kept inside the AK system.
 *
 * WHY THIS FILE EXISTS. `project-categories` is registered by Pe Core, the
 * plugin Zeyna ships with, for its own `portfolio` post type. The AK model
 * does not use either: work is `ak_project`, classified by ak_relationship,
 * ak_project_type and ak_capability.
 *
 * But the template hierarchy does not care which model is current. For a
 * term archive WordPress asks for `taxonomy-project-categories.php` BEFORE
 * it asks for `archive.php`, and it checks the child theme and then the
 * parent for each name in turn — so without this file, one URL on the site
 * still rendered through Zeyna's own template: a Redux read, a call to
 * `zeyna_barba()`, an Elementor builder render if a Redux key names one, and
 * a grid expecting a stylesheet that is no longer enqueued. Overriding
 * archive-portfolio.php and single-portfolio.php without this one left the
 * exit with a hole in exactly the place nobody visits during testing.
 *
 * It is deliberately the same index as archive-portfolio.php: a term archive
 * of legacy portfolio posts is a filtered version of the same list, and the
 * one thing it must not do is invent a second visual language for content
 * the studio is migrating away from.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ak_term = get_queried_object();
?>

<main id="primary" class="site-main ak-scope" data-ak-container>

	<section class="ak-section">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><span class="ak-eyebrow__folio">01</span><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><?php esc_html_e( 'Index', 'ak-zeyna-child' ); ?></p>
			<h1 class="ak-display ak-display--hero" data-ak-cut><?php echo esc_html( $ak_term && ! is_wp_error( $ak_term ) ? $ak_term->name : __( 'Work', 'ak-zeyna-child' ) ); ?></h1>

			<?php
			$ak_desc = $ak_term && ! is_wp_error( $ak_term ) ? term_description( $ak_term ) : '';
			if ( $ak_desc ) :
				?>
				<div class="ak-lead"><?php echo wp_kses_post( $ak_desc ); ?></div>
			<?php endif; ?>

			<?php if ( have_posts() ) : ?>
				<ul class="ak-index">
					<?php
					$ak_folio = 0;
					while ( have_posts() ) :
						the_post();
						$ak_folio++;
						?>
						<li class="ak-index__row ak-rise">
							<a class="ak-index__link" href="<?php the_permalink(); ?>">
								<span class="ak-index__folio"><?php echo esc_html( str_pad( (string) $ak_folio, 3, '0', STR_PAD_LEFT ) ); ?></span>
								<span class="ak-index__client"><?php the_title(); ?></span>
								<span class="ak-index__title"><?php echo esc_html( ak_meta( 'ak_headline', get_the_title() ) ); ?></span>
								<span class="ak-index__tags">
									<?php foreach ( ak_case_movements() as $ak_tag ) : ?>
										<span class="ak-index__tag"><?php echo esc_html( $ak_tag ); ?></span>
									<?php endforeach; ?>
								</span>
								<span class="ak-index__year"><?php echo esc_html( ak_meta( 'ak_year' ) ); ?></span>
							</a>
						</li>
					<?php endwhile; ?>
				</ul>

				<?php the_posts_pagination(); ?>
			<?php else : ?>
				<p class="ak-lead"><?php esc_html_e( 'Nothing is filed here.', 'ak-zeyna-child' ); ?></p>
			<?php endif; ?>

			<p class="ak-index__all"><a class="ak-link" href="<?php echo esc_url( get_post_type_archive_link( AK_PROJECT_CPT ) ? get_post_type_archive_link( AK_PROJECT_CPT ) : home_url( '/work/' ) ); ?>"><?php esc_html_e( 'See all work', 'ak-zeyna-child' ); ?></a></p>
		</div>
	</section>

	<?php get_template_part( 'template-parts/ak-cta' ); ?>

</main>

<?php
get_footer();
