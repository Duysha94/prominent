<?php
/**
 * Fallback for anything without its own template.
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
			'eyebrow' => '',
			'heading' => is_singular() ? get_the_title() : wp_strip_all_tags( get_the_archive_title() ),
			'lede'    => '',
			'class'   => 'page-head--compact',
		)
	);
	?>
	<div class="section section--tight">
		<div class="wrap prose">
			<?php
			while ( have_posts() ) :
				the_post();
				if ( is_singular() ) {
					the_content();
				} else {
					echo '<h2><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h2>';
					the_excerpt();
				}
			endwhile;
			the_posts_pagination();
			?>
		</div>
	</div>
</main>
<?php
get_footer();
