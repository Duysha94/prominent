<?php
/**
 * Project groupings (/work/type/…, /work/relationship/…, disciplines).
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
$term = get_queried_object();
?>
<main id="main">
	<?php
	ak_studio_page_header(
		array(
			'eyebrow' => __( 'Work', 'ak-studio' ),
			'heading' => $term ? $term->name : wp_strip_all_tags( get_the_archive_title() ),
			'lede'    => $term && $term->description ? wp_strip_all_tags( $term->description ) : '',
			'class'   => 'page-head--compact',
		)
	);
	?>
	<section class="section section--tight">
		<div class="wrap">
			<?php if ( have_posts() ) : ?>
				<div class="card-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						if ( 'ak_project' === get_post_type() ) {
							ak_studio_project_card( get_post() );
						} else {
							ak_studio_post_card( get_post() );
						}
					endwhile;
					?>
				</div>
				<?php the_posts_pagination(); ?>
			<?php else : ?>
				<p class="lede"><?php esc_html_e( 'Nothing here yet.', 'ak-studio' ); ?></p>
			<?php endif; ?>
			<p style="margin-top:48px"><a class="link-arrow" href="<?php echo esc_url( get_post_type_archive_link( 'ak_project' ) ); ?>"><?php esc_html_e( 'All work', 'ak-studio' ); ?><?php echo ak_studio_arrow( 'link-arrow__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
		</div>
	</section>
	<?php ak_studio_cta(); ?>
</main>
<?php
get_footer();
