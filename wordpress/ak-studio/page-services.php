<?php
/**
 * Services hub: the six disciplines, then how to work with us and the FAQ.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main">
	<?php
	while ( have_posts() ) :
		the_post();
		ak_studio_page_header();
		?>
		<section class="section section--tight">
			<div class="wrap">
				<?php get_template_part( 'template-parts/services-index' ); ?>
			</div>
		</section>
		<div class="section section--tight">
			<div class="wrap prose">
				<?php the_content(); ?>
			</div>
		</div>
		<?php
		ak_studio_cta();
	endwhile;
	?>
</main>
<?php
get_footer();
