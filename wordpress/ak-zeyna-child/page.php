<?php
/**
 * The default page template.
 *
 * The child had none, so every page without an explicit AK template — the
 * privacy policy, and anything the owner adds from the admin without picking
 * one — rendered through the PARENT'S page.php: parent markup, parent
 * classes, parent chrome, on a site that has otherwise left that vocabulary
 * behind. Independence means the fallbacks too, not only the designed routes.
 *
 * @package ak-zeyna-child
 */

get_header();
?>
<main id="primary" class="site-main ak-scope" data-ak-container>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<div class="aks-wrap">
		<div class="aks-rail"><span class="aks-rail__mark"><?php echo esc_html( get_the_title() ); ?></span></div>
		<div class="aks-col-wide aks-section aks-section--open">
			<h1 class="aks-t-hero"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?>
				<p class="aks-t-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="aks-wrap">
		<div class="aks-rail"></div>
		<div class="aks-col-text aks-section entry-content">
			<?php
			the_content();
			wp_link_pages(
				array(
					'before' => '<nav class="pagination">',
					'after'  => '</nav>',
				)
			);
			?>
		</div>
	</div>
	<?php
endwhile;
?>

</main><!-- #primary -->

<?php
get_template_part( 'template-parts/ak-cta' );
get_footer();
