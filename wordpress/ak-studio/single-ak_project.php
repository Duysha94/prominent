<?php
/**
 * One project: its blocks (header, facts, story, film, photographs), then the next project.
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
			$work = get_page_by_path( 'work' );
			ak_studio_title_header(
				array(
					'eyebrow' => $work ? '<a href="' . esc_url( get_permalink( $work ) ) . '">' . esc_html__( 'Work', 'ak-studio' ) . '</a>' : esc_html__( 'Work', 'ak-studio' ),
					'lede'    => has_excerpt() ? get_the_excerpt() : '',
					'size'    => 'large',
				)
			);
		}
		the_content();
		ak_studio_next_project();
	endwhile;
	?>
</main>
<?php
get_footer();
