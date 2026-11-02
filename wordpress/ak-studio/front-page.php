<?php
/**
 * Front page: built from template parts so each section can move into its own
 * block pattern later.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main">
	<?php
	get_template_part( 'template-parts/home/hero' );
	get_template_part( 'template-parts/home/band' );
	?>
</main>
<?php
get_footer();
