<?php
/**
 * A page: its blocks, exactly as built in the page editor.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="entry">
	<?php
	while ( have_posts() ) :
		the_post();
		if ( ! ak_studio_opens_with_header() ) {
			ak_studio_title_header( array( 'lede' => has_excerpt() ? get_the_excerpt() : '' ) );
		}
		the_content();
		wp_link_pages();
	endwhile;
	?>
</main>
<?php
get_footer();
