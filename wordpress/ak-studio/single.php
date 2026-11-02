<?php
/**
 * A journal entry.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="entry entry--article">
	<?php
	while ( have_posts() ) :
		the_post();
		$cats    = get_the_category();
		$journal = (int) get_option( 'page_for_posts' );
		$minutes = ak_studio_reading_time();
		$meta    = '<p class="page-head__meta"><time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date( 'j F Y' ) ) . '</time>'
			. ( $cats ? ' · <a href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>' : '' )
			/* translators: %d: minutes */
			. ' · ' . esc_html( sprintf( _n( '%d min read', '%d min read', $minutes, 'ak-studio' ), $minutes ) ) . '</p>';
		if ( ! ak_studio_opens_with_header() ) {
			ak_studio_title_header(
				array(
					'eyebrow' => $journal ? '<a href="' . esc_url( get_permalink( $journal ) ) . '">' . esc_html__( 'Journal', 'ak-studio' ) . '</a>' : esc_html__( 'Journal', 'ak-studio' ),
					'lede'    => has_excerpt() ? get_the_excerpt() : '',
					'meta'    => $meta,
					'size'    => 'article',
				)
			);
		}
		if ( has_post_thumbnail() ) :
			$cap = wp_get_attachment_caption( (int) get_post_thumbnail_id() );
			?>
			<figure class="article-cover alignwide" data-reveal>
				<?php echo ak_studio_img( (int) get_post_thumbnail_id(), '(max-width: 1520px) 92vw, 1400px', array( 'loading' => 'eager', 'fetchpriority' => 'high' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( $cap ) : ?><figcaption><?php echo esc_html( $cap ); ?></figcaption><?php endif; ?>
			</figure>
			<?php
		endif;
		the_content();

		$more = get_posts( array( 'posts_per_page' => 3, 'post__not_in' => array( get_the_ID() ), 'ignore_sticky_posts' => true ) );
		if ( $more ) :
			?>
			<section class="wp-block-group is-style-section more-reading">
				<p class="is-style-eyebrow"><?php esc_html_e( 'Keep reading', 'ak-studio' ); ?></p>
				<h2 class="wp-block-heading is-style-display"><?php echo ak_studio_heading( __( 'More from *the journal.*', 'ak-studio' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
				<div class="post-grid ak-wide">
					<?php
					foreach ( $more as $m ) {
						echo ak_studio_post_card( $m ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</div>
				<?php if ( $journal ) : ?>
					<div class="wp-block-buttons"><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_permalink( $journal ) ); ?>"><?php esc_html_e( 'All entries', 'ak-studio' ); ?></a></div></div>
				<?php endif; ?>
			</section>
			<?php
		endif;
		echo ak_studio_pattern( 'cta' ); // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks.
	endwhile;
	?>
</main>
<?php
get_footer();
