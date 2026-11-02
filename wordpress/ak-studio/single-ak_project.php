<?php
/**
 * One project: the cover, the facts, the story, the film and the photographs.
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
		$p     = ak_studio_project();
		$cover = (int) get_post_thumbnail_id();
		$loop  = ! empty( $p['loop'] ) ? wp_get_attachment_url( (int) $p['loop'] ) : '';
		$facts = array_filter(
			array(
				__( 'When', 'ak-studio' )  => $p['date_h'] ?: $p['year'],
				__( 'Where', 'ak-studio' ) => implode( ', ', array_filter( array( $p['venue'], $p['location'] ) ) ),
				__( 'Our part', 'ak-studio' ) => $p['role'],
			)
		);
		?>
		<article class="project">
			<header class="project-hero">
				<div class="project-hero__media" aria-hidden="true" data-parallax="10">
					<?php if ( $loop ) : ?>
						<video muted loop playsinline preload="none" data-autoplay data-src="<?php echo esc_url( $loop ); ?>" poster="<?php echo esc_url( (string) get_the_post_thumbnail_url( null, 'full' ) ); ?>"></video>
					<?php else : ?>
						<?php echo ak_studio_img( $cover, '100vw', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => '' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endif; ?>
					<div class="project-hero__scrim"></div>
				</div>
				<div class="wrap project-hero__inner">
					<p class="eyebrow" data-rise>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'ak_project' ) ); ?>"><?php esc_html_e( 'Work', 'ak-studio' ); ?></a>
						<?php if ( $p['type'] ) : ?> / <?php echo esc_html( $p['type'] ); ?><?php endif; ?>
						<?php if ( $p['upcoming'] ) : ?> / <span class="tag-live"><?php esc_html_e( 'Upcoming', 'ak-studio' ); ?></span><?php endif; ?>
					</p>
					<h1 class="display project-hero__title" data-split><?php the_title(); ?></h1>
				</div>
			</header>

			<div class="section section--tight">
				<div class="wrap project__grid">
					<div class="project__intro">
						<?php if ( has_excerpt() ) : ?>
							<p class="lede lede--xl" data-rise><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<div class="prose" data-rise><?php the_content(); ?></div>
					</div>
					<aside class="project__facts" data-rise>
						<dl>
							<?php foreach ( $facts as $k => $v ) : ?>
								<div><dt><?php echo esc_html( $k ); ?></dt><dd><?php echo esc_html( $v ); ?></dd></div>
							<?php endforeach; ?>
							<?php if ( $p['disciplines'] ) : ?>
								<div><dt><?php esc_html_e( 'Disciplines', 'ak-studio' ); ?></dt>
									<dd class="chips">
										<?php foreach ( $p['disciplines'] as $slug => $name ) : ?>
											<?php $sp = ak_studio_service_page( $slug ); ?>
											<?php if ( $sp ) : ?><a class="chip-link" href="<?php echo esc_url( get_permalink( $sp ) ); ?>"><?php echo esc_html( $name ); ?></a><?php else : ?><span class="chip-link"><?php echo esc_html( $name ); ?></span><?php endif; ?>
										<?php endforeach; ?>
									</dd></div>
							<?php endif; ?>
							<?php foreach ( $p['credits'] as $k => $v ) : ?>
								<div><dt><?php echo esc_html( $k ); ?></dt><dd><?php echo esc_html( $v ); ?></dd></div>
							<?php endforeach; ?>
						</dl>
						<?php if ( $p['url'] ) : ?>
							<a class="link-arrow" href="<?php echo esc_url( $p['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( wp_parse_url( $p['url'], PHP_URL_HOST ) ); ?><?php echo ak_studio_arrow( 'link-arrow__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						<?php endif; ?>
					</aside>
				</div>
			</div>

			<?php
			get_template_part( 'template-parts/film', null, array( 'id' => (int) $p['film'] ) );
			get_template_part( 'template-parts/gallery', null, array( 'ids' => $p['gallery'] ) );

			// Projects are ordered by menu order, not date: the next one in that order, wrapping round.
			$next    = null;
			$ordered = get_posts( array( 'post_type' => 'ak_project', 'posts_per_page' => 100, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ), 'fields' => 'ids' ) );
			$pos     = array_search( get_the_ID(), $ordered, true );
			if ( false !== $pos && count( $ordered ) > 1 ) {
				$next = get_post( $ordered[ ( $pos + 1 ) % count( $ordered ) ] );
			}
			if ( $next ) :
				?>
				<nav class="next-project" aria-label="<?php esc_attr_e( 'Next project', 'ak-studio' ); ?>">
					<a href="<?php echo esc_url( get_permalink( $next ) ); ?>" data-cursor="<?php esc_attr_e( 'Next', 'ak-studio' ); ?>">
						<div class="next-project__media" aria-hidden="true"><?php echo ak_studio_img( (int) get_post_thumbnail_id( $next ), '100vw', array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<div class="wrap next-project__inner">
							<p class="eyebrow"><?php esc_html_e( 'Next project', 'ak-studio' ); ?></p>
							<p class="display next-project__title"><?php echo esc_html( get_the_title( $next ) ); ?></p>
						</div>
					</a>
				</nav>
			<?php endif; ?>
		</article>
		<?php
	endwhile;
	ak_studio_cta();
	?>
</main>
<?php
get_footer();
