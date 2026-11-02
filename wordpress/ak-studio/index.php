<?php
/**
 * Fallback for anything without its own template.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="entry">
	<?php
	if ( is_singular() ) {
		while ( have_posts() ) {
			the_post();
			if ( ! ak_studio_opens_with_header() ) {
				ak_studio_title_header();
			}
			the_content();
		}
	} else {
		ak_studio_title_header( array( 'heading' => esc_html( wp_strip_all_tags( get_the_archive_title() ) ) ) );
		get_template_part( 'template-parts/post-list' );
	}
	?>
</main>
<?php
get_footer();
