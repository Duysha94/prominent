<?php
/**
 * Journal index (the posts page).
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
$page_id = (int) get_option( 'page_for_posts' );
$page    = $page_id ? get_post( $page_id ) : null;
?>
<main id="main">
	<?php
	if ( $page ) {
		setup_postdata( $GLOBALS['post'] = $page ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		ak_studio_page_header();
		wp_reset_postdata();
	} else {
		ak_studio_page_header( array( 'eyebrow' => '', 'heading' => __( 'Journal', 'ak-studio' ), 'lede' => '' ) );
	}
	get_template_part( 'template-parts/post-list' );
	?>
</main>
<?php
get_footer();
