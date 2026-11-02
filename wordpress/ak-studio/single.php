<?php
/**
 * A journal entry.
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
		$cats    = get_the_category();
		$journal = (int) get_option( 'page_for_posts' );
		$meta    = '<p class="page-head__meta"><time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date( 'j F Y' ) ) . '</time>' . ( $cats ? ' · <a href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>' : '' ) . ' · ' . esc_html( sprintf( /* translators: %d: minutes */ _n( '%d min read', '%d min read', max( 1, (int) round( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) ), 'ak-studio' ), max( 1, (int) round( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) ) ) ) . '</p>';
		ak_studio_page_header(
			array(
				'eyebrow' => __( 'Journal', 'ak-studio' ),
				'heading' => get_the_title(),
				'lede'    => has_excerpt() ? get_the_excerpt() : '',
				'meta'    => $meta,
				'class'   => 'page-head--article',
			)
		);
		if ( has_post_thumbnail() ) :
			?>
			<figure class="article-cover wrap" data-reveal>
				<?php echo ak_studio_img( (int) get_post_thumbnail_id(), '(max-width: 1200px) 92vw, 1100px', array( 'loading' => 'eager' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( wp_get_attachment_caption( (int) get_post_thumbnail_id() ) ) : ?>
					<figcaption><?php echo esc_html( wp_get_attachment_caption( (int) get_post_thumbnail_id() ) ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>
		<div class="section section--tight">
			<div class="wrap prose prose--article">
				<?php the_content(); ?>
			</div>
		</div>
		<?php
		$more = get_posts( array( 'posts_per_page' => 3, 'post__not_in' => array( get_the_ID() ), 'ignore_sticky_posts' => true ) );
		if ( $more ) :
			?>
			<section class="section journal">
				<div class="wrap">
					<?php ak_studio_section_head( __( 'Keep reading', 'ak-studio' ), __( 'More from *the journal.*', 'ak-studio' ), $journal ? array( 'url' => get_permalink( $journal ), 'label' => __( 'All entries', 'ak-studio' ) ) : array() ); ?>
					<div class="post-grid">
						<?php foreach ( $more as $m ) : ?>
							<?php ak_studio_post_card( $m ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
			<?php
		endif;
		ak_studio_cta();
	endwhile;
	?>
</main>
<?php
get_footer();
