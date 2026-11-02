<?php
/**
 * Category, tag and date archives of the journal.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="entry">
	<?php
	ak_studio_title_header(
		array(
			'eyebrow' => esc_html__( 'Journal', 'ak-studio' ),
			'heading' => esc_html( wp_strip_all_tags( get_the_archive_title() ) ),
			'lede'    => wp_strip_all_tags( get_the_archive_description() ),
		)
	);
	get_template_part( 'template-parts/post-list' );
	?>
</main>
<?php
get_footer();
