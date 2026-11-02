<?php
/**
 * Contact: the full brief, with what happens next beside it.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$need = isset( $_GET['need'] ) ? sanitize_key( wp_unslash( $_GET['need'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- preselects a checkbox only.

get_header();
?>
<main id="main">
	<?php
	while ( have_posts() ) :
		the_post();
		ak_studio_page_header( array( 'class' => 'page-head--compact' ) );
		?>
		<section class="section section--tight contact" id="brief">
			<div class="wrap contact__grid">
				<div class="contact__form">
					<?php
					if ( function_exists( 'ak_brand_brief_form' ) ) {
						echo ak_brand_brief_form( array( 'service' => $need, 'id' => 'brief', 'source' => __( 'Contact page', 'ak-studio' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped by the plugin.
					}
					?>
				</div>
				<aside class="contact__aside prose">
					<?php the_content(); ?>
				</aside>
			</div>
		</section>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
