<?php
/**
 * Template Name: AK — Bespoke page
 *
 * The pattern for every bespoke page.
 *
 * Two rules this template exists to demonstrate:
 *
 * 1. The Barba container MUST be emitted, with Zeyna's exact contract, or the
 *    page transition silently degrades to a full browser navigation and nobody
 *    notices until launch. Zeyna puts it on <main id="primary"> via
 *    zeyna_barba(false) — we call the same function rather than hard-coding
 *    the attribute, so the theme's own "page transitions" option still governs
 *    it.
 *
 * 2. get_footer() is called AFTER </main>, so the footer sits outside the
 *    container and persists across navigations. Do not move it inside.
 *
 * Everything else is plain markup using the design-system classes in
 * assets/css/ak.css — no page builder, no utility framework, no build step.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme helper returns a fixed attribute. ?>>

	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<section class="ak-section">
			<div class="ak-wrap">
				<p class="ak-eyebrow">
					<span class="ak-eyebrow__folio">01</span>
					<span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span>
					<?php echo esc_html( get_the_title() ); ?>
				</p>

				<h1 class="ak-display ak-display--hero ak-vf" data-ak-cut>
					<?php echo esc_html( get_post_meta( get_the_ID(), 'ak_headline', true ) ?: get_the_title() ); ?>
				</h1>

				<div class="ak-lead"><?php the_content(); ?></div>
			</div>
		</section>

	<?php endwhile; ?>

</main>

<?php
get_footer();
