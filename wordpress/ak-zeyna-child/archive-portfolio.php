<?php
/**
 * Work — the contents page.
 *
 * Overrides Zeyna's portfolio archive with the publication-index layout:
 * folio, client, result, movements, year — readable in one pass. The image is
 * the reward for interest on the case page, not the price of entry here.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<section class="ak-section">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><span class="ak-eyebrow__folio">01</span><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><?php esc_html_e( 'Index', 'ak-zeyna-child' ); ?></p>
			<h1 class="ak-display ak-display--hero" data-ak-cut><?php esc_html_e( 'Every project, and the movement it came through.', 'ak-zeyna-child' ); ?></h1>
			<p class="ak-lead"><?php esc_html_e( 'Read it like a contents page. Most projects ran through more than one movement — that is usually the point.', 'ak-zeyna-child' ); ?></p>

			<?php if ( have_posts() ) : ?>
				<ul class="ak-index">
					<?php
					$ak_folio = 0;
					while ( have_posts() ) :
						the_post();
						$ak_folio++;
						?>
						<li class="ak-index__row ak-rise">
							<a class="ak-index__link" href="<?php the_permalink(); ?>">
								<span class="ak-index__folio"><?php echo esc_html( str_pad( (string) $ak_folio, 3, '0', STR_PAD_LEFT ) ); ?></span>
								<span class="ak-index__client"><?php the_title(); ?></span>
								<span class="ak-index__title"><?php echo esc_html( ak_meta( 'ak_headline', get_the_title() ) ); ?></span>
								<span class="ak-index__tags">
									<?php foreach ( ak_case_movements() as $ak_tag ) : ?>
										<span class="ak-index__tag"><?php echo esc_html( $ak_tag ); ?></span>
									<?php endforeach; ?>
								</span>
								<span class="ak-index__year"><?php echo esc_html( ak_meta( 'ak_year' ) ); ?></span>
							</a>
						</li>
					<?php endwhile; ?>
				</ul>

				<?php the_posts_pagination(); ?>
			<?php else : ?>
				<p class="ak-lead"><?php esc_html_e( 'Case studies are being prepared. In the meantime, start a conversation — the work is happening either way.', 'ak-zeyna-child' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<?php get_template_part( 'template-parts/ak-cta' ); ?>

</main>

<?php
get_footer();
