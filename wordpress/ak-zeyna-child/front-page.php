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
$ak_work_page  = get_post_type_archive_link( AK_PROJECT_CPT );
$ak_contact    = get_page_by_path( 'contact' );
$ak_services   = get_page_by_path( 'services' );
$ak_journal    = get_page_by_path( 'journal' );
?>

<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<?php
	/*
	 ── Hero ───────────────────────────────────────────────────────────
	 */
	?>
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

	<?php
	/*
	 ── The showreel: a framed swatch that takes the whole screen ──────
	 */
	?>
	<?php
	/*
	 The section is tall; a sticky child pins the frame while the named
	 view-timeline (on the section, which DOES move) drives the growth
	 from a measured swatch to full-bleed. Without scroll-driven support
	 the reel is simply one full screen of video — it still fills the
	 screen, it just skips the transition.
	 */
	?>
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

	<?php
	/*
	 ── The figures and the four movements ─────────────────────────────
	 */
	?>
	<section class="ak-section">
		<div class="ak-wrap">
			<?php
			/*
			 The claim, stated once. The counting animation stays where a
			 figure IS the claim — the tech-pack readouts inside case
			 studies — rather than on a row of tiles that restate the
			 sections around them.
			 */
			?>
			<p class="ak-claim ak-rise">
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %d: number of individual services */
						__( 'Two international fashion platforms <em>founded, not hired</em>. %d services across six movements, run by <em>the two owners</em> — the same two who answer your email.', 'ak-zeyna-child' ),
						absint( ak_service_count() )
					),
					array( 'em' => array() )
				);
				?>
			</p>

			<?php
			/*
			 The six movements, read from inc/projects/capabilities.php — the
			 same source that builds Services and seeds the capability
			 taxonomy. It used to be four, hard-coded here: Production
			 swallowed photography, film, shows and launches, and Presence
			 swallowed websites, PR and paid media, so the two largest parts
			 of the practice were invisible inside other people's headings and
			 the studio read as a web-and-ads shop.
			 
			 Each card names the confirmed practice areas beneath it, so the
			 editorial layer never hides the factual one — and links straight
			 to that movement's full service list.
			 */
			?>
			<ol class="ak-grid ak-grid--2 ak-grid--3" style="list-style:none;padding:0;margin:3.5rem 0 0">
				<?php
				$ak_services_url = $ak_services ? get_permalink( $ak_services ) : home_url( '/services/' );
				foreach ( ak_movements() as $ak_slug => $ak_m ) :
					?>
					<li class="ak-rise">
						<a class="ak-card" data-ak-tilt href="<?php echo esc_url( $ak_services_url . '#' . $ak_slug ); ?>">
							<span class="ak-eyebrow" style="gap:.5rem"><span class="ak-eyebrow__folio"><?php echo esc_html( $ak_m['number'] ); ?></span></span>
							<h2 class="ak-card__title"><?php echo esc_html( $ak_m['name'] ); ?></h2>
							<p class="ak-card__body"><?php echo esc_html( $ak_m['summary'] ); ?></p>
							<p class="ak-card__areas"><?php echo esc_html( implode( ' · ', $ak_m['areas'] ) ); ?></p>
						</a>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<?php
	/*
	 ── The argument: the one full-bleed accent section ────────────────
	 */
	?>
	<section class="ak-accent-field">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><?php esc_html_e( 'Why this studio', 'ak-zeyna-child' ); ?></p>
			<h2 class="ak-display" data-ak-cut><?php esc_html_e( 'Strategy that never leaves the deck is not strategy.', 'ak-zeyna-child' ); ?></h2>
			<p class="ak-lead" style="color:inherit;opacity:.82">
				<?php esc_html_e( 'Most advisories hand you a deck and wish you luck. We produce the campaign, we produce the show, and we own the runways our clients walk on. From the initial idea to international presence, we support projects at every stage of their development.', 'ak-zeyna-child' ); ?>
			</p>
		</div>
	</section>

	<?php
	/*
	 ── Selected work ──────────────────────────────────────────────────
	 */
	?>
	<?php
	// Featured first, then the rest. Fixtures are excluded by the public
	// query filter in inc/projects/query.php, so they can never reach here.
	$ak_projects = ak_published_projects();
	usort(
		$ak_projects,
		function ( $a, $b ) {
			return (int) ak_project_meta( 'ak_featured', 0, $b->ID ) <=> (int) ak_project_meta( 'ak_featured', 0, $a->ID );
		}
	);
	$ak_projects = array_slice( $ak_projects, 0, 4 );
	if ( $ak_projects ) :
		?>
		<section class="ak-section ak-section--rule">
			<div class="ak-wrap">
				<p class="ak-eyebrow">
					<span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span>
					<?php esc_html_e( 'Selected work', 'ak-zeyna-child' ); ?>
				</p>
				<?php
				/*
				 "and what it moved" promised outcomes. No result, metric or
				 uplift has been supplied for any project, and a heading that
				 implies otherwise is a claim the studio cannot stand behind.
				 */
				?>
				<h2 class="ak-display ak-vf" data-ak-cut><?php esc_html_e( 'Selected work.', 'ak-zeyna-child' ); ?></h2>

				<ul class="ak-index">
					<?php
					$ak_folio = 0;
					foreach ( $ak_projects as $ak_project ) :
						$ak_folio++;
						$ak_p_type = ak_project_type( $ak_project->ID );
						?>
						<li class="ak-index__row ak-rise">
							<a class="ak-index__link" href="<?php echo esc_url( get_permalink( $ak_project ) ); ?>">
								<span class="ak-index__folio"><?php echo esc_html( str_pad( (string) $ak_folio, 3, '0', STR_PAD_LEFT ) ); ?></span>
								<span class="ak-index__client"><?php echo esc_html( ak_relationship_label( $ak_project->ID ) ); ?></span>
								<span class="ak-index__title"><?php echo esc_html( get_the_title( $ak_project ) ); ?></span>
								<span class="ak-index__tags">
									<?php if ( $ak_p_type ) : ?>
										<span class="ak-index__tag"><?php echo esc_html( $ak_p_type->name ); ?></span>
									<?php endif; ?>
								</span>
								<?php
								/*
								 * The year, not the address. This slot is a
								 * fixed 4rem column in the homepage index, and
								 * a host name like prominentmagazine.co.uk
								 * needs 139px — it pushed the whole document
								 * 35px wide at every desktop width. Addresses
								 * belong on the Work register, which has a
								 * column that sizes to them.
								 */
								?>
								<span class="ak-index__year"><?php echo esc_html( ak_project_meta( 'ak_year', '', $ak_project->ID ) ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>

				<p style="margin-top:2.5rem">
					<a class="ak-btn ak-btn--line" href="<?php echo esc_url( $ak_work_page ? $ak_work_page : home_url( '/work/' ) ); ?>"><?php esc_html_e( 'All work', 'ak-zeyna-child' ); ?></a>
				</p>
			</div>
		</section>
	<?php endif; ?>

	<?php
	/*
	 ── The registers ─────────────────────────────────────────────────────
	 Two registers, and they are NOT the same claim. This section used to
	 print one list headed "Built and run by the studio" containing Fashion
	 Frontier, Prominent Magazine and Utrend Store — which the studio owns —
	 alongside Lenie Boya, Wolax and Show Me Your Nails, which are client
	 engagements. That heading asserted ownership of three other people's
	 businesses. Relationship comes from the taxonomy now, so the two
	 registers cannot be merged by accident again.

	 Every card also used to call the capture service directly and print
	 whatever came back. When the service is not ready it answers with a grey
	 placeholder, so the homepage showed a row of grey plates — capture
	 failure rendered as portfolio. Cards now go through ak_project_cover(),
	 which returns media only when there is verified media, and a project
	 without any renders as a typographic entry instead.
	 */
	?>
	<?php
	$ak_registers = array(
		'ak-owned' => array(
			'eyebrow' => __( 'What we built', 'ak-zeyna-child' ),
			'title'   => __( 'We do not rent the platform. Our founders built it.', 'ak-zeyna-child' ),
			'lead'    => __( 'Most studios advising a young designer have to ask someone else for a slot. These are ours — which is why we can put a first collection on an international runway rather than write a deck about one.', 'ak-zeyna-child' ),
		),
		'client'   => array(
			'eyebrow' => __( 'Commissioned', 'ak-zeyna-child' ),
			'title'   => __( 'And the work we were asked to do.', 'ak-zeyna-child' ),
			'lead'    => '',
		),
	);
	foreach ( $ak_registers as $ak_rel_slug => $ak_reg ) :
		$ak_in_register = array_values(
			array_filter(
				ak_published_projects(),
				function ( $p ) use ( $ak_rel_slug ) {
					$term = ak_project_relationship( $p->ID );
					return $term && $term->slug === $ak_rel_slug;
				}
			)
		);
		if ( ! $ak_in_register ) {
			continue;
		}
		?>
		<section class="ak-section ak-section--rule">
			<div class="ak-wrap">
				<p class="ak-eyebrow">
					<span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span>
					<?php echo esc_html( $ak_reg['eyebrow'] ); ?>
				</p>
				<h2 class="ak-display ak-vf" data-ak-cut><?php echo esc_html( $ak_reg['title'] ); ?></h2>
				<?php if ( $ak_reg['lead'] ) : ?>
					<p class="ak-lead"><?php echo esc_html( $ak_reg['lead'] ); ?></p>
				<?php endif; ?>

				<ul class="ak-grid ak-grid--3" style="list-style:none;padding:0;margin:3rem 0 0">
					<?php
					foreach ( $ak_in_register as $ak_project ) :
						$ak_cover  = ak_project_cover( $ak_project->ID );
						$ak_p_type = ak_project_type( $ak_project->ID );
						$ak_p_url  = ak_project_meta( 'ak_url', '', $ak_project->ID );
						?>
						<li class="ak-rise">
							<a class="ak-site-card<?php echo $ak_cover ? '' : ' ak-site-card--plain'; ?>" data-ak-tilt href="<?php echo esc_url( get_permalink( $ak_project ) ); ?>">
								<?php if ( $ak_cover ) : ?>
									<span class="ak-plate ak-plate--fine ak-r-1610">
										<?php
										if ( is_int( $ak_cover ) ) {
											echo wp_get_attachment_image( $ak_cover, 'large', false, array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title( $ak_project ) ) ) );
										} else {
											printf(
												'<img loading="lazy" decoding="async" referrerpolicy="no-referrer" width="760" height="570" src="%s" alt="%s">',
												esc_url( $ak_cover ),
												/* translators: %s: project title */
												esc_attr( sprintf( __( '%s — current front page', 'ak-zeyna-child' ), get_the_title( $ak_project ) ) )
											);
										}
										?>
									</span>
								<?php endif; ?>
								<span class="ak-site-card__row">
									<span class="ak-site-card__name"><?php echo esc_html( get_the_title( $ak_project ) ); ?></span>
									<?php if ( $ak_p_url ) : ?>
										<span class="ak-site-card__domain"><?php echo esc_html( wp_parse_url( $ak_p_url, PHP_URL_HOST ) ); ?></span>
									<?php endif; ?>
								</span>
								<?php if ( $ak_p_type ) : ?>
									<span class="ak-site-card__by"><?php echo esc_html( $ak_p_type->name ); ?></span>
								<?php endif; ?>
								<?php if ( has_excerpt( $ak_project ) ) : ?>
									<span class="ak-site-card__desc"><?php echo esc_html( get_the_excerpt( $ak_project ) ); ?></span>
								<?php endif; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
	<?php endforeach; ?>

	<?php
	/*
	 ── The name ───────────────────────────────────────────────────────
	 */
	?>
	<section class="ak-section ak-section--rule">
		<div class="ak-wrap">
			<p class="ak-eyebrow">
				<span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span>
				<?php esc_html_e( 'The name', 'ak-zeyna-child' ); ?>
			</p>
			<h2 class="ak-display ak-vf" data-ak-cut><?php esc_html_e( 'A is Andrii. K is Kostiantyn.', 'ak-zeyna-child' ); ?></h2>
			<p class="ak-lead"><?php esc_html_e( 'AK is the two co-owners, one letter each. Andrii Karakushan runs digital, identity and media; Kostiantyn Lieontiev runs strategy and fashion production. Every project gets both letters — which is the point of the studio.', 'ak-zeyna-child' ); ?></p>
			<p style="margin-top:2rem">
				<?php $ak_about_page = get_page_by_path( 'about' ); ?>
				<a class="ak-btn ak-btn--line" href="<?php echo esc_url( $ak_about_page ? get_permalink( $ak_about_page ) : home_url( '/about/' ) ); ?>"><?php esc_html_e( 'Meet the founders', 'ak-zeyna-child' ); ?></a>
			</p>
		</div>
	</section>

	<?php get_template_part( 'template-parts/ak-band' ); ?>

	<?php
	/*
	 ── Journal preview ────────────────────────────────────────────────
	 */
	?>
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
