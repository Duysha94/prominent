<?php
/**
 * Search results.
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
			'eyebrow' => esc_html__( 'Search', 'ak-studio' ),
			/* translators: %s: search terms */
			'heading' => esc_html( sprintf( __( 'Results for “%s”', 'ak-studio' ), get_search_query() ) ),
			'extra'   => get_search_form( array( 'echo' => false ) ),
		)
	);
	?>
	<section class="wp-block-group is-style-section">
		<?php if ( have_posts() ) : ?>
			<ul class="results ak-wide">
				<?php
				while ( have_posts() ) :
					the_post();
					$type = get_post_type_object( (string) get_post_type() );
					?>
					<li><a href="<?php the_permalink(); ?>"><span class="results__type"><?php echo esc_html( $type ? $type->labels->singular_name : '' ); ?></span><span class="results__title"><?php the_title(); ?></span><span class="results__text"><?php echo esc_html( get_the_excerpt() ); ?></span></a></li>
				<?php endwhile; ?>
			</ul>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing matched. Try another word, or start from the work.', 'ak-studio' ); ?></p>
		<?php endif; ?>
	</section>
</main>
<?php
get_footer();
