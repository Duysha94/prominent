<?php
/**
 * The journal (posts page): the Journal page's own blocks, then the entries.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
$page_id = (int) get_option( 'page_for_posts' );
$page    = $page_id ? get_post( $page_id ) : null;
?>
<main id="main" class="entry">
	<?php
	if ( $page && '' !== trim( $page->post_content ) ) {
		if ( ! ak_studio_opens_with_header( $page_id ) ) {
			ak_studio_title_header( array( 'heading' => esc_html( get_the_title( $page ) ) ) );
		}
		echo apply_filters( 'the_content', $page->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput -- core filter.
	} else {
		ak_studio_title_header( array( 'heading' => $page ? esc_html( get_the_title( $page ) ) : esc_html__( 'Journal', 'ak-studio' ) ) );
	}
	get_template_part( 'template-parts/post-list' );
	?>
</main>
<?php
get_footer();
