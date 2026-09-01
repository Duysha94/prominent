<?php
/**
 * A case study.
 *
 * Opens the same way every time — measured plate, then numbered chapters,
 * each with its measurement in the margin — and the featured image replaces
 * the drawn plate the moment one is set.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php
	while ( have_posts() ) :
		the_post();
		$ak_measures = ak_meta_json( 'ak_measures' );
		$ak_chapters = ak_meta_json( 'ak_chapters' );
		$ak_position = ak_meta_json( 'ak_position' );
		?>

		<article <?php post_class(); ?>>
			<section class="ak-section">
				<div class="ak-wrap">
					<p class="ak-eyebrow">
						<span style="color:var(--accent-text)"><?php the_title(); ?></span>
						<?php if ( ak_meta( 'ak_category' ) ) : ?><span aria-hidden="true">—</span><?php echo esc_html( ak_meta( 'ak_category' ) ); ?><?php endif; ?>
						<?php if ( ak_meta( 'ak_year' ) ) : ?><span aria-hidden="true">—</span><?php echo esc_html( ak_meta( 'ak_year' ) ); ?><?php endif; ?>
					</p>

					<h1 class="ak-display ak-display--hero" data-ak-cut><?php echo esc_html( ak_meta( 'ak_headline', get_the_title() ) ); ?></h1>

					<?php if ( ak_meta( 'ak_summary' ) ) : ?>
						<p class="ak-lead"><?php echo esc_html( ak_meta( 'ak_summary' ) ); ?></p>
					<?php endif; ?>

					<?php $ak_mv = ak_case_movements(); ?>
					<?php if ( $ak_mv ) : ?>
						<p class="ak-chips" aria-label="<?php esc_attr_e( 'Movements this project ran through', 'ak-zeyna-child' ); ?>">
							<span class="ak-chips__label"><?php esc_html_e( 'Ran through', 'ak-zeyna-child' ); ?></span>
							<?php foreach ( $ak_mv as $ak_tag ) : ?>
								<?php $ak_mv_url = ak_movement_url( $ak_tag ); ?>
								<?php if ( $ak_mv_url ) : ?>
									<a class="ak-chip" href="<?php echo esc_url( $ak_mv_url ); ?>"><?php echo esc_html( $ak_tag ); ?></a>
								<?php else : ?>
									<span class="ak-chip"><?php echo esc_html( $ak_tag ); ?></span>
								<?php endif; ?>
							<?php endforeach; ?>
						</p>
					<?php endif; ?>

					<div class="ak-rise" style="margin-top:3.5rem">
						<div data-ak-measure data-always data-ak-tilt style="position:relative">
							<div class="ak-plate ak-plate--band ak-r-165">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'full' ); ?>
								<?php else : ?>
									<span class="ak-plate__note"><?php esc_html_e( 'Image slot — set a featured image', 'ak-zeyna-child' ); ?></span>
								<?php endif; ?>
							</div>
							<?php ak_measure_hud( $ak_measures, get_the_title() . ( ak_meta( 'ak_year' ) ? ' — ' . ak_meta( 'ak_year' ) : '' ) ); ?>
						</div>
					</div>

					<?php if ( $ak_chapters ) : ?>
						<ol class="ak-chapters">
							<?php foreach ( $ak_chapters as $ak_i => $ak_ch ) : ?>
								<li class="ak-chapter ak-rise">
									<span class="ak-chapter__no"><?php echo esc_html( str_pad( (string) ( $ak_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
									<div>
										<h2 class="ak-chapter__title"><?php echo esc_html( isset( $ak_ch['title'] ) ? $ak_ch['title'] : '' ); ?></h2>
										<p class="ak-chapter__body"><?php echo esc_html( isset( $ak_ch['body'] ) ? $ak_ch['body'] : '' ); ?></p>
									</div>
									<?php if ( ! empty( $ak_ch['mkey'] ) && ! empty( $ak_ch['mval'] ) ) : ?>
										<div class="ak-chapter__measure">
											<span><?php echo esc_html( $ak_ch['mkey'] ); ?></span>
											<b><?php echo esc_html( $ak_ch['mval'] ); ?></b>
										</div>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ol>
					<?php endif; ?>

					<?php if ( ! empty( $ak_position['statement'] ) ) : ?>
						<section class="ak-section--rule" style="margin-top:4rem;padding-top:3rem">
							<p class="ak-eyebrow"><?php esc_html_e( 'The position', 'ak-zeyna-child' ); ?></p>
							<p class="ak-display ak-vf" data-ak-cut style="max-width:24ch;font-size:clamp(1.75rem,5vw,4rem);line-height:1.02"><?php echo esc_html( $ak_position['statement'] ); ?></p>
							<?php if ( ! empty( $ak_position['rejected'] ) && is_array( $ak_position['rejected'] ) ) : ?>
								<p class="ak-eyebrow" style="margin-top:3rem"><?php esc_html_e( 'What it beat', 'ak-zeyna-child' ); ?></p>
								<ul style="list-style:none;margin:1.25rem 0 0;padding:0;max-width:52ch">
									<?php foreach ( $ak_position['rejected'] as $ak_r ) : ?>
										<li class="ak-rise" style="padding:.9rem 0;border-bottom:1px solid var(--rule)">
											<s style="color:var(--text-faint);text-decoration-color:var(--accent-line);font-size:clamp(.9375rem,1.8vw,1.125rem)"><?php echo esc_html( $ak_r ); ?></s>
										</li>
									<?php endforeach; ?>
								</ul>
								<p style="margin-top:1.5rem;max-width:44ch;font-size:.8125rem;line-height:1.6;color:var(--text-muted)"><?php esc_html_e( 'Each of these would have passed a board meeting. That is what is wrong with them: a position nobody could object to is a position nobody will remember.', 'ak-zeyna-child' ); ?></p>
							<?php endif; ?>
						</section>
					<?php endif; ?>

					<?php if ( trim( get_the_content() ) ) : ?>
						<div class="ak-prose" style="margin-top:3.5rem"><?php the_content(); ?></div>
					<?php endif; ?>
				</div>
			</section>
		</article>

		<?php
		// Next case, so the index is never a dead end.
		$ak_next = get_next_post();
		if ( ! $ak_next ) {
			$ak_prev = get_previous_post();
			$ak_next = $ak_prev ? $ak_prev : null;
		}
		if ( $ak_next ) :
			?>
			<section class="ak-section ak-section--rule">
				<div class="ak-wrap">
					<a href="<?php echo esc_url( get_permalink( $ak_next ) ); ?>" style="text-decoration:none;display:block" class="ak-rise">
						<span class="ak-eyebrow"><?php esc_html_e( 'Next', 'ak-zeyna-child' ); ?></span>
						<span class="ak-display" style="display:block;margin-top:1rem;color:var(--text)"><?php echo esc_html( ak_meta( 'ak_headline', $ak_next->post_title, $ak_next->ID ) ); ?></span>
					</a>
				</div>
			</section>
		<?php endif; ?>

	<?php endwhile; ?>

	<?php get_template_part( 'template-parts/ak-cta' ); ?>

</main>

<?php
get_footer();
