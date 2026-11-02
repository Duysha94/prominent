<?php
/**
 * Front page: built from template parts so each section can move or be
 * reordered without touching the others.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main">
	<?php
	foreach ( array( 'hero', 'band', 'statement', 'services', 'platforms', 'work', 'event', 'process', 'founders', 'journal', 'brief' ) as $part ) {
		get_template_part( 'template-parts/home/' . $part );
	}
	?>
</main>
<?php
get_footer();
