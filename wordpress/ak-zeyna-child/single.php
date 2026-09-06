<?php
/**
 * A journal entry.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main ak-scope" data-ak-container>

	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class(); ?>>
			<section class="ak-section">
				<div class="ak-wrap">
					<p class="ak-eyebrow">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<?php $ak_cat = get_the_category(); if ( $ak_cat ) : ?>
							<span aria-hidden="true">—</span><span style="color:var(--accent-text)"><?php echo esc_html( $ak_cat[0]->name ); ?></span>
						<?php endif; ?>
					</p>
					<h1 class="ak-display" data-ak-cut style="max-width:24ch;font-size:clamp(2rem,6vw,4.5rem)"><?php the_title(); ?></h1>
					<div class="ak-prose" style="margin-top:2.5rem"><?php the_content(); ?></div>

					<nav style="margin-top:4rem;display:flex;gap:1rem;flex-wrap:wrap" aria-label="<?php esc_attr_e( 'More notes', 'ak-zeyna-child' ); ?>">
						<?php previous_post_link( '%link', '← %title' ); ?>
						<?php next_post_link( '%link', '%title →' ); ?>
					</nav>
				</div>
			</section>
		</article>
	<?php endwhile; ?>

	<?php get_template_part( 'template-parts/ak-cta' ); ?>

</main>

<?php
get_footer();
