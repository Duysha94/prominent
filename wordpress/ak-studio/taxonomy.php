<?php
/**
 * Project groupings (/work/type/…, /work/service/…, /work/relationship/…).
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
$term = get_queried_object();
$work = get_page_by_path( 'work' );
?>
<main id="main" class="entry">
	<?php
	ak_studio_title_header(
		array(
			'eyebrow' => $work ? '<a href="' . esc_url( get_permalink( $work ) ) . '">' . esc_html__( 'Work', 'ak-studio' ) . '</a>' : esc_html__( 'Work', 'ak-studio' ),
			'heading' => esc_html( $term instanceof WP_Term ? $term->name : wp_strip_all_tags( get_the_archive_title() ) ),
			'lede'    => $term instanceof WP_Term ? wp_strip_all_tags( $term->description ) : '',
		)
	);
	?>
	<section class="wp-block-group is-style-section">
		<?php if ( have_posts() ) : ?>
			<div class="ak-work ak-wide">
				<div class="card-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						echo 'ak_project' === get_post_type() ? ak_studio_card( get_post() ) : ak_studio_post_card( get_post() ); // phpcs:ignore WordPress.Security.EscapeOutput
					endwhile;
					?>
				</div>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing here yet.', 'ak-studio' ); ?></p>
		<?php endif; ?>
		<?php if ( $work ) : ?>
			<div class="wp-block-buttons"><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_permalink( $work ) ); ?>"><?php esc_html_e( 'All work', 'ak-studio' ); ?></a></div></div>
		<?php endif; ?>
	</section>
	<?php echo ak_studio_pattern( 'cta' ); // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks. ?>
</main>
<?php
get_footer();
