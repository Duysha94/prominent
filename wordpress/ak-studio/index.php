<?php
/**
 * Fallback template.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="page wrap">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'entry' ); ?>>
				<h1 class="entry__title"><?php the_title(); ?></h1>
				<div class="entry__body"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing here yet.', 'ak-studio' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
