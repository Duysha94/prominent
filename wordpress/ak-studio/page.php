<?php
/**
 * Default page: header, then the page's own content.
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
		$legal = in_array( get_post_field( 'post_name' ), array( 'privacy-policy', 'cookie-policy' ), true ) || (int) get_option( 'wp_page_for_privacy_policy' ) === get_the_ID();
		ak_studio_page_header( array( 'class' => $legal ? 'page-head--compact' : '' ) );
		?>
		<div class="section section--tight">
			<div class="wrap prose<?php echo $legal ? ' prose--legal' : ''; ?>">
				<?php the_content(); ?>
			</div>
		</div>
		<?php
		if ( ! $legal ) {
			ak_studio_cta();
		}
	endwhile;
	?>
</main>
<?php
get_footer();
