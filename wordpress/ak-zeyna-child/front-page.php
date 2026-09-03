<?php
/**
 * The front page.
 *
 * Sequence answers the visitor's questions in order: what is this → why this
 * studio → what has it done → who is behind it → what to do next. The one
 * full-bleed accent section is spent exactly once.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ak_video_av1  = get_theme_mod( 'ak_hero_video_av1', '' );
// Defaults to the showreel already in the media library; swap it any
// time in Customizer → AK Studio.
$ak_video_h264 = get_theme_mod( 'ak_hero_video_h264', 'https://akbrand.studio/wp-content/uploads/2026/03/OFD29COMP-online-video-cutter.com_-1.mp4' );
$ak_poster     = get_theme_mod( 'ak_hero_poster', '' );
$ak_work_page  = get_post_type_archive_link( 'portfolio' );
$ak_contact    = get_page_by_path( 'contact' );
$ak_services   = get_page_by_path( 'services' );
$ak_journal    = get_page_by_path( 'journal' );
?>

<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<!-- ── Hero ─────────────────────────────────────────────────────────── -->
	<section class="ak-section">
		<div class="ak-wrap">
			<p class="ak-eyebrow">
				<span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span>
				<span style="color:var(--accent-text)"><?php esc_html_e( 'Fashion & Brand Advisory', 'ak-zeyna-child' ); ?></span>
				<span aria-hidden="true">—</span>
				<?php esc_html_e( 'London · Paris · Dubai', 'ak-zeyna-child' ); ?>
			</p>

			<h1 class="ak-display ak-display--hero" data-ak-cut><?php esc_html_e( 'From an idea to an international presence.', 'ak-zeyna-child' ); ?></h1>

			<p class="ak-lead">
				<?php esc_html_e( 'AK Brand Development Studio is an independent creative and strategic practice specialising in brand development, fashion consulting and creative production.', 'ak-zeyna-child' ); ?>
				<strong style="display:block;margin-top:1rem;color:var(--text)"><?php esc_html_e( 'Our co-founder created London Fashion Day and Odessa Fashion Day — so the runway our clients walk on is one of our own.', 'ak-zeyna-child' ); ?></strong>
			</p>

			<p class="ak-cta__row">
				<a class="ak-btn ak-btn--fill" href="<?php echo esc_url( $ak_work_page ? $ak_work_page : home_url( '/work/' ) ); ?>"><?php esc_html_e( 'See the work', 'ak-zeyna-child' ); ?></a>
				<a class="ak-btn ak-btn--line" href="<?php echo esc_url( $ak_contact ? get_permalink( $ak_contact ) : home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Start a project', 'ak-zeyna-child' ); ?></a>
			</p>

		</div>
	</section>

	<!-- ── The showreel: a framed swatch that takes the whole screen ────── -->
	<!-- The section is tall; a sticky child pins the frame while the named
	     view-timeline (on the section, which DOES move) drives the growth
	     from a measured swatch to full-bleed. Without scroll-driven support
	     the reel is simply one full screen of video — it still fills the
	     screen, it just skips the transition. -->
	<section class="ak-reel" id="showreel" aria-label="<?php esc_attr_e( 'Showreel', 'ak-zeyna-child' ); ?>">
		<div class="ak-reel__sticky">
			<div class="ak-reel__frame" data-ak-measure data-always>
				<div class="ak-plate ak-plate--disc ak-video ak-reel__plate">
						<?php if ( $ak_video_av1 || $ak_video_h264 ) : ?>
							<video autoplay muted loop playsinline preload="metadata" disableremoteplayback aria-hidden="true" tabindex="-1" data-ak-video
								<?php echo $ak_poster ? 'poster="' . esc_url( $ak_poster ) . '"' : ''; ?>>
								<?php if ( $ak_video_av1 ) : ?><source src="<?php echo esc_url( $ak_video_av1 ); ?>" type='video/mp4; codecs="av01.0.05M.08"' /><?php endif; ?>
								<?php if ( $ak_video_h264 ) : ?><source src="<?php echo esc_url( $ak_video_h264 ); ?>" type="video/mp4" /><?php endif; ?>
							</video>
						<?php else : ?>
							<span class="ak-video-slot">
								<span class="ak-video-slot__play" aria-hidden="true"><svg width="13" height="15" viewBox="0 0 13 15" fill="currentColor" aria-hidden="true"><path d="M0 0 L13 7.5 L0 15 Z" /></svg></span>
								<span class="ak-video-slot__label"><?php esc_html_e( 'Video — showreel slot', 'ak-zeyna-child' ); ?></span>
								<span class="ak-video-slot__note"><?php esc_html_e( 'Upload in Customizer → AK Studio · AV1 + H.264 .mp4 · 8–12 s · silent', 'ak-zeyna-child' ); ?></span>
							</span>
						<?php endif; ?>
				</div>
				<?php ak_measure_hud( array(
					array( 'key' => 'MOVEMENTS', 'value' => '04' ),
					array( 'key' => 'CITIES', 'value' => 'LDN·PAR·DXB' ),
				), __( 'Showreel', 'ak-zeyna-child' ) ); ?>
			</div>
		</div>
	</section>

	<!-- ── The figures and the four movements ───────────────────────────── -->
	<section class="ak-section">
		<div class="ak-wrap">
			<!-- The claim, stated once. The counting animation stays where a
			     figure IS the claim — the tech-pack readouts inside case
			     studies — rather than on a row of tiles that restate the
			     sections around them. -->
			<p class="ak-claim ak-rise">
				<?php
				echo wp_kses(
					__( 'Two international fashion platforms <em>founded, not hired</em>. Nine services across four movements, run by <em>the two owners</em> — the same two who answer your email.', 'ak-zeyna-child' ),
					array( 'em' => array() )
				);
				?>
			</p>

			<!-- The four movements, stated immediately. -->
			<ol class="ak-grid ak-grid--2 ak-grid--4" style="list-style:none;padding:0;margin:3.5rem 0 0">
				<?php
				$ak_movements = array(
					array( 'strategy', __( 'Strategy', 'ak-zeyna-child' ), __( 'Decide what the brand is, who it is for, and what it will not be.', 'ak-zeyna-child' ) ),
					array( 'identity', __( 'Identity', 'ak-zeyna-child' ), __( 'Give the position a face that survives contact with the real world.', 'ak-zeyna-child' ) ),
					array( 'production', __( 'Production', 'ak-zeyna-child' ), __( 'Make it exist — the campaign, the event, the show, the room.', 'ak-zeyna-child' ) ),
					array( 'presence', __( 'Presence', 'ak-zeyna-child' ), __( 'Put it where people already are, and make it findable.', 'ak-zeyna-child' ) ),
				);
				$ak_services_url = $ak_services ? get_permalink( $ak_services ) : home_url( '/services/' );
				foreach ( $ak_movements as $ak_i => $ak_m ) :
					?>
					<li class="ak-rise">
						<a class="ak-card" data-ak-tilt href="<?php echo esc_url( $ak_services_url . '#' . $ak_m[0] ); ?>">
							<span class="ak-eyebrow" style="gap:.5rem"><span class="ak-eyebrow__folio">0<?php echo (int) ( $ak_i + 1 ); ?></span></span>
							<h2 style="font-family:var(--font-display);font-style:italic;font-size:1.5rem;margin:.75rem 0 0"><?php echo esc_html( $ak_m[1] ); ?></h2>
							<p style="font-size:.8125rem;line-height:1.6;color:var(--text-muted);margin:.6rem 0 0"><?php echo esc_html( $ak_m[2] ); ?></p>
						</a>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<!-- ── The argument: the one full-bleed accent section ──────────────── -->
	<section class="ak-accent-field">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><?php esc_html_e( 'Why this studio', 'ak-zeyna-child' ); ?></p>
			<h2 class="ak-display" data-ak-cut><?php esc_html_e( 'Strategy that never leaves the deck is not strategy.', 'ak-zeyna-child' ); ?></h2>
			<p class="ak-lead" style="color:inherit;opacity:.82">
				<?php esc_html_e( 'Most advisories hand you a deck and wish you luck. We produce the campaign, we produce the show, and we own the runways our clients walk on. From the initial idea to international presence, we support projects at every stage of their development.', 'ak-zeyna-child' ); ?>
			</p>
		</div>
	</section>

	<!-- ── Selected work ────────────────────────────────────────────────── -->
	<?php
	$ak_cases = new WP_Query(
		array(
			'post_type'      => 'portfolio',
			'posts_per_page' => 4,
			'no_found_rows'  => true,
		)
	);
	if ( $ak_cases->have_posts() ) :
		?>
		<section class="ak-section ak-section--rule">
			<div class="ak-wrap">
				<p class="ak-eyebrow">
					<span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span>
					<?php esc_html_e( 'Selected work', 'ak-zeyna-child' ); ?>
				</p>
				<h2 class="ak-display ak-vf" data-ak-cut><?php esc_html_e( 'Selected work, and what it moved.', 'ak-zeyna-child' ); ?></h2>

				<ul class="ak-index">
					<?php
					$ak_folio = 0;
					while ( $ak_cases->have_posts() ) :
						$ak_cases->the_post();
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
					<?php endwhile; wp_reset_postdata(); ?>
				</ul>

				<p style="margin-top:2.5rem">
					<a class="ak-btn ak-btn--line" href="<?php echo esc_url( $ak_work_page ? $ak_work_page : home_url( '/work/' ) ); ?>"><?php esc_html_e( 'All work', 'ak-zeyna-child' ); ?></a>
				</p>
			</div>
		</section>
	<?php endif; ?>

	<!-- ── What we founded ──────────────────────────────────────────────── -->
	<section class="ak-section ak-section--rule">
		<div class="ak-wrap">
			<p class="ak-eyebrow">
				<span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span>
				<?php esc_html_e( 'What we built', 'ak-zeyna-child' ); ?>
			</p>
			<h2 class="ak-display ak-vf" data-ak-cut><?php esc_html_e( 'We do not rent the platform. Our founders built it.', 'ak-zeyna-child' ); ?></h2>
			<p class="ak-lead"><?php esc_html_e( 'Most studios advising a young designer have to ask someone else for a slot. These are ours — which is why we can put a first collection on an international runway rather than write a deck about one.', 'ak-zeyna-child' ); ?></p>

			<?php
			// Founded platforms — live: the cards show each site's CURRENT
			// front page (auto-refreshing capture), not a stale photo.
			$ak_founded = array(
				array( 'London Fashion Day', 'https://londonfashionday.co.uk/', __( 'Founded and produced by Konstantin Lieontiev', 'ak-zeyna-child' ), __( 'An international platform created to support emerging designers.', 'ak-zeyna-child' ) ),
				array( 'Odessa Fashion Day', 'https://ofd.org.ua/', __( 'Founded and produced by Konstantin Lieontiev', 'ak-zeyna-child' ), __( 'Built to develop international creative communities.', 'ak-zeyna-child' ) ),
				array( 'KEKA', 'https://keka.design/', __( 'Fashion brand founded by Konstantin Lieontiev', 'ak-zeyna-child' ), __( 'Currently being developed for the international market.', 'ak-zeyna-child' ) ),
				array( "Cool'baba", 'https://coolbaba.in.ua/', __( 'Online magazine founded by Andrey Karakushan', 'ak-zeyna-child' ), __( 'A media platform covering fashion, lifestyle and creative industries.', 'ak-zeyna-child' ) ),
			);
			?>
			<ul class="ak-grid ak-grid--2" style="list-style:none;padding:0;margin:3rem 0 0">
				<?php foreach ( $ak_founded as $ak_p ) : ?>
					<li class="ak-rise">
						<a class="ak-site-card" data-ak-tilt href="<?php echo esc_url( $ak_p[1] ); ?>" target="_blank" rel="noopener">
							<span class="ak-plate ak-plate--fine ak-r-1610"><?php ak_live_preview( $ak_p[1], $ak_p[0] ); ?></span>
							<span class="ak-site-card__row">
								<span class="ak-site-card__name"><?php echo esc_html( $ak_p[0] ); ?></span>
								<span class="ak-site-card__domain"><?php echo esc_html( wp_parse_url( $ak_p[1], PHP_URL_HOST ) ); ?></span>
							</span>
							<span class="ak-site-card__by"><?php echo esc_html( $ak_p[2] ); ?></span>
							<span class="ak-site-card__desc"><?php echo esc_html( $ak_p[3] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<p class="ak-eyebrow" style="margin-top:4rem"><?php esc_html_e( 'Built and run by the studio', 'ak-zeyna-child' ); ?></p>
			<?php
			$ak_built = array(
				array( 'Fashion Frontier', 'https://fashionfrontier.uk/' ),
				array( 'Prominent Magazine', 'https://prominentmagazine.co.uk/' ),
				array( 'Lenie Boya', 'https://www.lenieboya.com/' ),
				array( 'Wolax', 'https://wolax.co.uk/' ),
				array( 'UTREND Store', 'https://utrendstore.co.uk/' ),
				array( 'Show Me Your Nails', 'https://showmeyournails.com/' ),
			);
			?>
			<ul class="ak-grid ak-grid--3" style="list-style:none;padding:0;margin:1.75rem 0 0">
				<?php foreach ( $ak_built as $ak_p ) : ?>
					<li class="ak-rise">
						<a class="ak-site-card ak-site-card--small" data-ak-tilt href="<?php echo esc_url( $ak_p[1] ); ?>" target="_blank" rel="noopener">
							<span class="ak-plate ak-plate--fine ak-r-1610"><?php ak_live_preview( $ak_p[1], $ak_p[0] ); ?></span>
							<span class="ak-site-card__row">
								<span class="ak-site-card__name"><?php echo esc_html( $ak_p[0] ); ?></span>
								<span class="ak-site-card__domain"><?php echo esc_html( wp_parse_url( $ak_p[1], PHP_URL_HOST ) ); ?></span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<!-- ── The name ─────────────────────────────────────────────────────── -->
	<section class="ak-section ak-section--rule">
		<div class="ak-wrap">
			<p class="ak-eyebrow">
				<span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span>
				<?php esc_html_e( 'The name', 'ak-zeyna-child' ); ?>
			</p>
			<h2 class="ak-display ak-vf" data-ak-cut><?php esc_html_e( 'A is Andrey. K is Konstantin.', 'ak-zeyna-child' ); ?></h2>
			<p class="ak-lead"><?php esc_html_e( 'AK is the two co-owners, one letter each. Andrey Karakushan runs digital, identity and media; Konstantin Lieontiev runs strategy and fashion production. Every project gets both letters — which is the point of the studio.', 'ak-zeyna-child' ); ?></p>
			<p style="margin-top:2rem">
				<?php $ak_about_page = get_page_by_path( 'about' ); ?>
				<a class="ak-btn ak-btn--line" href="<?php echo esc_url( $ak_about_page ? get_permalink( $ak_about_page ) : home_url( '/about/' ) ); ?>"><?php esc_html_e( 'Meet the founders', 'ak-zeyna-child' ); ?></a>
			</p>
		</div>
	</section>

	<?php get_template_part( 'template-parts/ak-band' ); ?>

	<!-- ── Journal preview ──────────────────────────────────────────────── -->
	<?php
	$ak_notes = new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 4,
			'no_found_rows'  => true,
		)
	);
	if ( $ak_notes->have_posts() ) :
		?>
		<section class="ak-section">
			<div class="ak-wrap">
				<p class="ak-eyebrow">
					<span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span>
					<?php esc_html_e( 'Journal', 'ak-zeyna-child' ); ?>
				</p>
				<h2 class="ak-display ak-vf" data-ak-cut><?php esc_html_e( 'What we have argued about lately.', 'ak-zeyna-child' ); ?></h2>

				<ul class="ak-notes">
					<?php while ( $ak_notes->have_posts() ) : $ak_notes->the_post(); ?>
						<li class="ak-note ak-rise">
							<span class="ak-index__folio"><?php echo esc_html( get_the_date( 'M Y' ) ); ?></span>
							<div>
								<h3 class="ak-note__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p class="ak-note__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
							</div>
							<span class="ak-note__meta">
								<?php $ak_cat = get_the_category(); if ( $ak_cat ) : ?><span class="cat"><?php echo esc_html( $ak_cat[0]->name ); ?></span><?php endif; ?>
							</span>
						</li>
					<?php endwhile; wp_reset_postdata(); ?>
				</ul>

				<p style="margin-top:2.5rem">
					<a class="ak-btn ak-btn--line" href="<?php echo esc_url( $ak_journal ? get_permalink( $ak_journal ) : home_url( '/journal/' ) ); ?>"><?php esc_html_e( 'All notes', 'ak-zeyna-child' ); ?></a>
				</p>
			</div>
		</section>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/ak-cta' ); ?>

</main>

<?php
get_footer();
