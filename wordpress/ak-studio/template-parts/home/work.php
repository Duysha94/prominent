<?php
/**
 * Selected work: a horizontal rail of featured projects (pinned on desktop).
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$projects = get_posts(
	array(
		'post_type'      => 'ak_project',
		'posts_per_page' => 6,
		'meta_key'       => 'ak_featured', // phpcs:ignore WordPress.DB.SlowDBQuery
		'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	)
);
if ( ! $projects ) {
	$projects = get_posts( array( 'post_type' => 'ak_project', 'posts_per_page' => 6, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
}
if ( ! $projects ) {
	return;
}
$archive = get_post_type_archive_link( 'ak_project' );
?>
<section class="work section" id="work" data-rail>
	<div class="wrap">
		<?php ak_studio_section_head( __( 'Selected work', 'ak-studio' ), __( 'Seasons, campaigns *and brands.*', 'ak-studio' ), $archive ? array( 'url' => $archive, 'label' => __( 'All work', 'ak-studio' ) ) : array() ); ?>
	</div>
	<div class="work__viewport">
		<div class="work__track" data-rail-track>
			<?php foreach ( $projects as $i => $project ) : ?>
				<?php ak_studio_project_card( $project, 'card--rail', '(max-width: 900px) 80vw, 34vw' ); ?>
			<?php endforeach; ?>
			<a class="work__more" href="<?php echo esc_url( $archive ); ?>" data-cursor="<?php esc_attr_e( 'All', 'ak-studio' ); ?>">
				<span class="display"><?php echo ak_studio_heading( __( 'All *work*', 'ak-studio' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php echo ak_studio_arrow( 'work__more-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		</div>
	</div>
</section>
