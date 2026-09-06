<?php
/**
 * Journal — the posts index (assigned as the "posts page").
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main ak-scope" data-ak-container>

	<section class="ak-section">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><?php esc_html_e( 'Journal', 'ak-zeyna-child' ); ?></p>
			<h1 class="ak-display ak-display--hero" data-ak-cut><?php esc_html_e( 'Notes, and the odd disagreement.', 'ak-zeyna-child' ); ?></h1>
			<p class="ak-lead"><?php esc_html_e( 'Written notes on brand strategy, identity, fashion production and digital presence — from the studio that does the work rather than reports on it.', 'ak-zeyna-child' ); ?></p>

			<?php if ( have_posts() ) : ?>
				<ul class="ak-notes">
					<?php while ( have_posts() ) : the_post(); ?>
						<li class="ak-note ak-rise">
							<span class="ak-index__folio"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'M Y' ) ); ?></time></span>
							<div>
								<h2 class="ak-note__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p class="ak-note__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
							</div>
							<span class="ak-note__meta">
								<?php $ak_cat = get_the_category(); if ( $ak_cat ) : ?><span class="cat"><?php echo esc_html( $ak_cat[0]->name ); ?></span><?php endif; ?>
							</span>
						</li>
					<?php endwhile; ?>
				</ul>
				<?php the_posts_pagination(); ?>
			<?php else : ?>
				<p class="ak-lead"><?php esc_html_e( 'Nothing published yet.', 'ak-zeyna-child' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<?php get_template_part( 'template-parts/ak-cta' ); ?>

</main>

<?php
get_footer();
