<?php
/**
 * One service: what it is, what you get, how it runs, the proof, the way in.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main">
	<?php
	while ( have_posts() ) :
		the_post();
		$slug     = (string) get_post_meta( get_the_ID(), '_ak_movement', true );
		$movement = array();
		foreach ( ak_studio_movements() as $i => $m ) {
			if ( $m['slug'] === $slug ) {
				$movement = $m + array( 'index' => $i );
			}
		}
		$image = ! empty( $movement['image'] ) ? ak_studio_seed_image( $movement['image'] ) : 0;
		$meta  = $movement ? '<p class="page-head__q"><span>' . esc_html__( 'The question', 'ak-studio' ) . '</span>' . esc_html( $movement['question'] ) . '</p>' : '';
		ak_studio_page_header( array( 'image' => $image, 'meta' => $meta, 'class' => 'page-head--service' ) );
		?>
		<div class="section section--tight">
			<div class="wrap service__grid">
				<div class="prose service__content">
					<?php the_content(); ?>
				</div>
				<aside class="service__aside" aria-label="<?php esc_attr_e( 'Start this service', 'ak-studio' ); ?>">
					<div class="service__card">
						<p class="eyebrow"><?php echo esc_html( $movement['name'] ?? get_the_title() ); ?></p>
						<p><?php esc_html_e( 'Tell us what you are working on. You will get a written proposal — scope, timeline and fee — before any work begins.', 'ak-studio' ); ?></p>
						<a class="btn btn--accent" href="<?php echo esc_url( ak_studio_contact_url( $slug ) ); ?>" data-magnetic><?php esc_html_e( 'Start a project', 'ak-studio' ); ?><?php echo ak_studio_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					</div>
				</aside>
			</div>
		</div>
		<?php
		$related = $slug ? get_posts(
			array(
				'post_type'      => 'ak_project',
				'posts_per_page' => 3,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'tax_query'      => array( array( 'taxonomy' => 'ak_capability', 'field' => 'slug', 'terms' => $slug ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		) : array();
		if ( $related ) :
			?>
			<section class="section related">
				<div class="wrap">
					<?php ak_studio_section_head( __( 'In practice', 'ak-studio' ), __( 'Where we did *this work.*', 'ak-studio' ), array( 'url' => get_post_type_archive_link( 'ak_project' ), 'label' => __( 'All work', 'ak-studio' ) ) ); ?>
					<div class="card-grid card-grid--3">
						<?php foreach ( $related as $p ) : ?>
							<?php ak_studio_project_card( $p, '', '(max-width: 900px) 92vw, 30vw' ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
		<section class="section section--tight others">
			<div class="wrap">
				<p class="eyebrow"><?php esc_html_e( 'Other services', 'ak-studio' ); ?></p>
				<ul class="others__list">
					<?php foreach ( ak_studio_movements() as $i => $m ) : ?>
						<?php
						if ( $m['slug'] === $slug ) {
							continue;
						}
						$page = ak_studio_service_page( $m['slug'] );
						if ( ! $page ) {
							continue;
						}
						?>
						<li><a href="<?php echo esc_url( get_permalink( $page ) ); ?>"><span><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><?php echo esc_html( $m['name'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php
		ak_studio_cta( $slug );
	endwhile;
	?>
</main>
<?php
get_footer();
