<?php
/**
 * The journal loop with category links and pagination.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$cats = get_categories( array( 'hide_empty' => true ) );
?>
<section class="section section--tight">
	<div class="wrap">
		<?php if ( count( $cats ) > 1 ) : ?>
			<nav class="filters" aria-label="<?php esc_attr_e( 'Journal categories', 'ak-studio' ); ?>">
				<a class="filter<?php echo is_home() ? ' is-on' : ''; ?>" href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>"<?php echo is_home() ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All', 'ak-studio' ); ?></a>
				<?php foreach ( $cats as $c ) : ?>
					<a class="filter<?php echo is_category( $c->term_id ) ? ' is-on' : ''; ?>" href="<?php echo esc_url( get_category_link( $c ) ); ?>"<?php echo is_category( $c->term_id ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $c->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
		<?php if ( have_posts() ) : ?>
			<div class="post-grid post-grid--list">
				<?php
				while ( have_posts() ) :
					the_post();
					ak_studio_post_card( get_post() );
				endwhile;
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __( 'Newer', 'ak-studio' ),
					'next_text' => __( 'Older', 'ak-studio' ),
				)
			);
			?>
		<?php else : ?>
			<p class="lede"><?php esc_html_e( 'Nothing published here yet.', 'ak-studio' ); ?></p>
		<?php endif; ?>
	</div>
</section>
