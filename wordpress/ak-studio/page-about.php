<?php
/**
 * Studio: why AK, the founders, the record and the network.
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
		ak_studio_page_header( array( 'image' => ak_studio_seed_image( 'lfd-s7-kostiantyn-lieontiev-runway.jpg' ) ) );
		?>
		<div class="section section--tight">
			<div class="wrap prose">
				<?php the_content(); ?>
			</div>
		</div>
		<section class="section founders-section" id="founders">
			<div class="wrap">
				<?php ak_studio_section_head( __( 'The founders', 'ak-studio' ), __( 'A is for Andrii. *K is for Kostiantyn.*', 'ak-studio' ) ); ?>
				<?php get_template_part( 'template-parts/founders' ); ?>
			</div>
		</section>
		<?php
		get_template_part( 'template-parts/timeline' );
		get_template_part( 'template-parts/network' );
		ak_studio_cta( '', __( 'Work with *the studio.*', 'ak-studio' ) );
	endwhile;
	?>
</main>
<?php
get_footer();
