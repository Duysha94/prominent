<?php
/**
 * Search results.
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
			'eyebrow' => __( 'Search', 'ak-studio' ),
			/* translators: %s: search terms */
			'heading' => sprintf( __( 'Results for “%s”', 'ak-studio' ), get_search_query() ),
			'lede'    => '',
			'class'   => 'page-head--compact',
			'extra'   => get_search_form( array( 'echo' => false ) ),
		)
	);
	?>
	<section class="section section--tight">
		<div class="wrap">
			<?php if ( have_posts() ) : ?>
				<ul class="results">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<li><a href="<?php the_permalink(); ?>"><span class="results__type"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span><span class="results__title"><?php the_title(); ?></span><span class="results__text"><?php echo esc_html( get_the_excerpt() ); ?></span></a></li>
					<?php endwhile; ?>
				</ul>
				<?php the_posts_pagination(); ?>
			<?php else : ?>
				<p class="lede"><?php esc_html_e( 'Nothing matched. Try another word, or start from the work.', 'ak-studio' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php
get_footer();
