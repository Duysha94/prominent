<?php
/**
 * The final fallback.
 *
 * WordPress requires index.php, and every template it cannot resolve lands
 * here — a date archive, an author archive, a taxonomy the site does not
 * style, anything a future plugin registers. Without a child copy all of
 * those rendered through the parent theme.
 *
 * Deliberately plain: this is the route nobody designed, so it states what it
 * is and lists what is there rather than pretending to be a designed page.
 *
 * @package ak-zeyna-child
 */

get_header();
?>
<main id="primary" class="site-main ak-scope" data-ak-container>

<div class="aks-wrap">
	<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Index', 'ak-zeyna-child' ); ?></span></div>
	<div class="aks-col-wide aks-section aks-section--open">
		<?php if ( is_archive() || is_home() ) : ?>
			<p class="aks-t-data"><?php echo esc_html( get_the_archive_title() ); ?></p>
			<h1 class="aks-t-hero"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
			<?php
			$ak_description = get_the_archive_description();
			if ( $ak_description ) :
				?>
				<div class="aks-t-lead"><?php echo wp_kses_post( $ak_description ); ?></div>
			<?php endif; ?>
		<?php else : ?>
			<h1 class="aks-t-hero"><?php esc_html_e( 'Everything else', 'ak-zeyna-child' ); ?></h1>
		<?php endif; ?>
	</div>
</div>

<div class="aks-wrap">
	<div class="aks-rail"></div>
	<div class="aks-col-full aks-section">
		<?php if ( have_posts() ) : ?>
			<ul class="aks-index">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li class="aks-index__row">
						<a class="aks-index__link" href="<?php the_permalink(); ?>">
							<span class="aks-index__code"><?php echo esc_html( get_the_date( 'Y·m·d' ) ); ?></span>
							<span class="aks-index__client"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span>
							<span class="aks-index__title"><?php the_title(); ?></span>
							<span class="aks-index__disc">
								<?php if ( has_excerpt() ) : ?>
									<span class="aks-index__tag"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 9, '…' ) ); ?></span>
								<?php endif; ?>
							</span>
						</a>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => esc_html__( 'Previous', 'ak-zeyna-child' ),
					'next_text' => esc_html__( 'Next', 'ak-zeyna-child' ),
				)
			);
			?>
		<?php else : ?>
			<p class="aks-t-lead"><?php esc_html_e( 'Nothing here yet.', 'ak-zeyna-child' ); ?></p>
		<?php endif; ?>
	</div>
</div>

</main><!-- #primary -->

<?php
get_template_part( 'template-parts/ak-cta' );
get_footer();
