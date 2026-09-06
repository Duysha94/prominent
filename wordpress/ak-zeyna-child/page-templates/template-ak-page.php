<?php
/**
 * Template Name: AK — Bespoke page
 *
 * The pattern for every bespoke page.
 *
 * Two rules this template exists to demonstrate:
 *
 * 1. The swap container MUST be marked, with `data-ak-container` on
 *    <main id="primary">. assets/js/ak-nav.js reads that attribute to decide
 *    what to replace; a template that omits it still WORKS — the link falls
 *    through to an ordinary browser navigation, because navigation never
 *    depends on the runtime — but it loses the transition silently, and
 *    nobody notices until launch.
 *
 *    This used to be `zeyna_barba(false)`, calling the parent so its own
 *    "page transitions" option governed the attribute. The parent no longer
 *    renders here and the attribute is the child's own.
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

<main id="primary" class="site-main ak-scope" data-ak-container>

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

	<?php get_template_part( 'template-parts/ak-cta' ); ?>

</main>

<?php
get_footer();
