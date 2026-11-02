<?php
/**
 * Category, tag and date archives of the journal.
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
			'eyebrow' => __( 'Journal', 'ak-studio' ),
			'heading' => wp_strip_all_tags( get_the_archive_title() ),
			'lede'    => wp_strip_all_tags( get_the_archive_description() ),
			'class'   => 'page-head--compact',
		)
	);
	get_template_part( 'template-parts/post-list' );
	?>
</main>
<?php
get_footer();
