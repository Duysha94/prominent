<?php
/**
 * Statement: the argument for the studio, from the Home page content.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$front   = (int) get_option( 'page_on_front' );
$heading = $front ? (string) get_post_meta( $front, '_ak_heading', true ) : '';
$heading = $heading ?: __( 'Most agencies hand you a deck. *We hand you the show.*', 'ak-studio' );
$content = $front ? (string) get_post_field( 'post_content', $front ) : '';
$about   = get_page_by_path( 'about' );
?>
<section class="statement section" id="studio">
	<div class="wrap statement__grid">
		<p class="eyebrow" data-rise><?php esc_html_e( 'The studio', 'ak-studio' ); ?></p>
		<h2 class="statement__title display" data-split><?php echo ak_studio_heading( $heading ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
		<div class="statement__body prose" data-rise>
			<?php echo $content ? apply_filters( 'the_content', $content ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput -- post content. ?>
			<?php if ( $about ) : ?>
				<p><a class="link-arrow" href="<?php echo esc_url( get_permalink( $about ) ); ?>"><?php esc_html_e( 'Meet the studio', 'ak-studio' ); ?><?php echo ak_studio_arrow( 'link-arrow__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a></p>
			<?php endif; ?>
		</div>
		<div class="statement__letters" aria-hidden="true" data-parallax="-10"><span>A</span><span>K</span></div>
	</div>
</section>
