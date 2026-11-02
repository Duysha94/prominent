<?php
/**
 * Show in London: for designers — London Fashion Day, how selection works,
 * and how the studio prepares a collection for the runway.
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
		ak_studio_page_header( array( 'image' => ak_studio_seed_image( 'still-lfd-westminster-9.jpg' ) ) );
		get_template_part( 'template-parts/event', null, array( 'id' => 'season' ) );
		?>
		<div class="section section--tight">
			<div class="wrap prose">
				<?php the_content(); ?>
			</div>
		</div>
		<?php
		$ids = array_filter( array_map( 'ak_studio_seed_image', array( 'lfd-s7-gorsset.jpg', 'lfd-s6-lcca-showcase.jpg', 'lfd-s5-elen-ost.jpg', 'lfd-s6-sara-k-couture.jpg', 'lfd-s7-riri-couture.jpg', 'lfd-s6-mogardi.jpg' ) ) );
		get_template_part( 'template-parts/gallery', null, array( 'ids' => $ids ) );
		ak_studio_cta( 'runway', __( 'Arrive *ready.*', 'ak-studio' ) );
	endwhile;
	?>
</main>
<?php
get_footer();
