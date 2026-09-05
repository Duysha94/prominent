<?php
/**
 * Template Name: AK — About
 *
 * Two named people, not a "we". The founders' backgrounds are the studio's
 * credibility, and they get the page.
 *
 * Every factual sentence below comes from the AK Brand Development Studio
 * source document: what the studio does, what each founder has done, and what
 * they founded. Nothing is added to it. There are no company names beyond the
 * ones supplied, no dates, no awards, no client lists and no numbers other
 * than the nine years the document states — because a biography is the one
 * place where an invented detail is indistinguishable from a lie.
 *
 * Deliberately NOT a Mission / Vision / Values page.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ak_founders = array(
	array(
		'id'    => 'andrii-karakushan',
		'name'  => 'Andrii Karakushan',
		'role'  => __( 'Creative entrepreneur — brand development, digital presence, visual communication', 'ak-zeyna-child' ),
		'plate' => 'ak-plate--band',
		'bio'   => array(
			__( 'Andrii Karakushan is a creative entrepreneur specialising in brand development, digital presence and visual communication.', 'ak-zeyna-child' ),
			__( 'He has experience managing a multi-brand retail space for emerging designers — work that meant supporting young fashion brands through retail presentation and brand promotion, which is a different discipline from designing for them.', 'ak-zeyna-child' ),
			__( 'He is the founder of COOLBABA, an online media platform focused on fashion, lifestyle and the creative industries.', 'ak-zeyna-child' ),
			__( 'His expertise covers website development, brand identity, digital communication, and building online ecosystems for businesses and creative projects.', 'ak-zeyna-child' ),
		),
		'facts' => array(
			array( 'key' => 'RETAIL', 'value' => 'MULTI-BRAND' ),
			array( 'key' => 'MEDIA', 'value' => 'COOLBABA' ),
		),
	),
	array(
		'id'    => 'kostiantyn-lieontiev',
		'name'  => 'Kostiantyn Lieontiev',
		'role'  => __( 'Fashion producer, brand strategist, media professional', 'ak-zeyna-child' ),
		'plate' => 'ak-plate--disc',
		'bio'   => array(
			__( 'Kostiantyn Lieontiev is a fashion producer, brand strategist and media professional with experience in brand development and the creative industries.', 'ak-zeyna-child' ),
			__( 'Before focusing on international fashion projects, he spent nine years managing a regional branch of a major advertising holding, responsible for business development, advertising campaigns and strategic client development.', 'ak-zeyna-child' ),
			__( 'He is the founder and producer of London Fashion Day and Odessa Fashion Day — platforms created to support emerging designers and develop international creative communities. He is also the founder of the fashion brand KEKA, developed for the international market.', 'ak-zeyna-child' ),
			__( 'Through this work he has collaborated with designers, brands and creative teams across London, Paris and Dubai, and taken part in international projects and industry initiatives.', 'ak-zeyna-child' ),
		),
		'facts' => array(
			array( 'key' => 'ADVERTISING', 'value' => '9 YRS' ),
			array( 'key' => 'PLATFORMS', 'value' => '2 FOUNDED' ),
		),
	),
);

$ak_services_page = get_page_by_path( 'services' );
$ak_services_href = $ak_services_page ? get_permalink( $ak_services_page ) : home_url( '/services/' );
$ak_work_href     = get_post_type_archive_link( AK_PROJECT_CPT );
$ak_work_href     = $ak_work_href ? $ak_work_href : home_url( '/work/' );
?>

<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<section class="ak-section">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><?php esc_html_e( 'The studio — London, United Kingdom', 'ak-zeyna-child' ); ?></p>
			<h1 class="ak-display ak-display--hero" data-ak-cut><?php esc_html_e( 'An independent practice at the intersection of strategy and production.', 'ak-zeyna-child' ); ?></h1>
			<p class="ak-lead"><?php esc_html_e( 'AK Brand Development Studio works at the intersection of brand strategy, fashion production and the creative industries. We support founders, designers and businesses in building strong brand identities, developing strategic positioning, and creating visibility through creative campaigns, events, media presence and digital brand development.', 'ak-zeyna-child' ); ?></p>
			<p class="ak-lead" style="margin-top:1.25rem"><?php esc_html_e( 'The studio works with projects at every stage of their development — from the initial idea through to international presence.', 'ak-zeyna-child' ); ?></p>
		</div>
	</section>

	<?php
	/*
	 ── The name, spelled out ──────────────────────────────────────────────
	 The anchors used to read andrey-karakushan and konstantin-lieontiev, so
	 the old spellings survived in the URL fragment and in the page source
	 long after the visible text was corrected.
	 */
	?>
	<section class="ak-section ak-section--rule">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><?php esc_html_e( 'The name', 'ak-zeyna-child' ); ?></p>
			<div class="ak-monogram">
				<a class="ak-monogram__letter" href="#andrii-karakushan">
					<span class="ak-monogram__glyph ak-vf" aria-hidden="true">A</span>
					<span class="ak-monogram__who">Andrii<br>Karakushan</span>
				</a>
				<span class="ak-monogram__thread ak-draw" aria-hidden="true"></span>
				<a class="ak-monogram__letter" href="#kostiantyn-lieontiev">
					<span class="ak-monogram__glyph ak-vf" aria-hidden="true">K</span>
					<span class="ak-monogram__who">Kostiantyn<br>Lieontiev</span>
				</a>
			</div>
			<p class="ak-lead" style="margin-top:2.5rem"><?php esc_html_e( 'AK is not an acronym for anything corporate. A is Andrii, K is Kostiantyn — two co-owners, one letter each. One side of the studio runs digital, identity and media; the other runs strategy and fashion production. Both letters are load-bearing.', 'ak-zeyna-child' ); ?></p>
		</div>
	</section>

	<?php
	/*
	 ── What the two of them bring together ────────────────────────────────
	 Straight from the source: the founders bring experience across
	 advertising, fashion production, retail, media and digital brand
	 development. Stated as five fields rather than a paragraph, because the
	 breadth IS the argument — and because it is the fact that stops the
	 studio reading as a web-and-ads shop.
	 */
	?>
	<section class="ak-section ak-section--rule">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><?php esc_html_e( 'What we bring together', 'ak-zeyna-child' ); ?></p>
			<h2 class="ak-display ak-vf" data-ak-cut><?php esc_html_e( 'Five backgrounds, one practice.', 'ak-zeyna-child' ); ?></h2>
			<ul class="ak-grid ak-grid--2 ak-grid--3" style="list-style:none;padding:0;margin:3rem 0 0">
				<?php
				$ak_backgrounds = array(
					array( __( 'Advertising', 'ak-zeyna-child' ), __( 'Nine years running a regional branch of a major advertising holding — business development, campaigns, strategic client work.', 'ak-zeyna-child' ) ),
					array( __( 'Fashion production', 'ak-zeyna-child' ), __( 'Two international fashion platforms, founded and produced rather than hired.', 'ak-zeyna-child' ) ),
					array( __( 'Retail', 'ak-zeyna-child' ), __( 'A multi-brand retail space for emerging designers, and the work of getting young brands presented and promoted.', 'ak-zeyna-child' ) ),
					array( __( 'Media', 'ak-zeyna-child' ), __( 'An online platform covering fashion, lifestyle and the creative industries.', 'ak-zeyna-child' ) ),
					array( __( 'Digital brand development', 'ak-zeyna-child' ), __( 'Websites, identity and the online ecosystems that carry a brand between them.', 'ak-zeyna-child' ) ),
				);
				foreach ( $ak_backgrounds as $ak_i => $ak_bg ) :
					?>
					<li class="ak-rise">
						<span class="ak-eyebrow" style="gap:.5rem"><span class="ak-eyebrow__folio"><?php echo esc_html( str_pad( (string) ( $ak_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span></span>
						<h3 class="ak-card__title"><?php echo esc_html( $ak_bg[0] ); ?></h3>
						<p class="ak-card__body"><?php echo esc_html( $ak_bg[1] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<section class="ak-section ak-section--rule">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><?php esc_html_e( 'Founded by', 'ak-zeyna-child' ); ?></p>

			<?php foreach ( $ak_founders as $ak_i => $ak_f ) : ?>
				<article id="<?php echo esc_attr( $ak_f['id'] ); ?>" class="ak-founder" style="margin-top:<?php echo $ak_i ? '5rem' : '2.5rem'; ?>;scroll-margin-top:6rem">
					<div>
						<div data-ak-measure data-always data-ak-tilt style="position:relative">
							<div class="ak-plate <?php echo esc_attr( $ak_f['plate'] ); ?> ak-r-45">
								<span class="ak-plate__note"><?php esc_html_e( 'Portrait slot — replace via featured image', 'ak-zeyna-child' ); ?></span>
							</div>
							<?php ak_measure_hud( $ak_f['facts'], $ak_f['name'] ); ?>
						</div>
					</div>
					<div>
						<span class="ak-index__folio">0<?php echo (int) ( $ak_i + 1 ); ?></span>
						<h2 class="ak-founder__name ak-vf" data-ak-cut><?php echo esc_html( $ak_f['name'] ); ?></h2>
						<p class="ak-founder__role"><?php echo esc_html( $ak_f['role'] ); ?></p>
						<div class="ak-founder__bio" style="margin-top:1.5rem">
							<?php foreach ( $ak_f['bio'] as $ak_para ) : ?>
								<p><?php echo esc_html( $ak_para ); ?></p>
							<?php endforeach; ?>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<?php get_template_part( 'template-parts/ak-band' ); ?>

	<?php
	/*
	 ── How the disciplines connect ────────────────────────────────────────
	 The source describes a conceptual journey — idea, strategy, positioning,
	 identity, creative direction, content, digital, communication, event and
	 production, promotion — and says explicitly not to render it literally:
	 it describes how the disciplines connect, not a process a client is put
	 through. So it is set as one continuous sentence rather than a numbered
	 pipeline, and it is the last thing on the page rather than the offer.
	 */
	?>
	<section class="ak-section">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><?php esc_html_e( 'How it connects', 'ak-zeyna-child' ); ?></p>
			<p class="ak-claim ak-rise" style="margin-top:1.5rem">
				<?php
				echo wp_kses(
					__( 'An idea becomes a <em>position</em>. A position becomes an <em>identity</em>. An identity becomes <em>pictures and film</em>, a <em>website</em>, a <em>room full of people</em> — and then something worth <em>writing about</em>. We work at every one of those joins, which is why we do not hand you on at the third one.', 'ak-zeyna-child' ),
					array( 'em' => array() )
				);
				?>
			</p>

			<ul style="list-style:none;margin:3rem 0 0;padding:0;max-width:62ch">
				<?php
				$ak_principles = array(
					__( 'An independent practice, run by its two owners', 'ak-zeyna-child' ),
					__( 'Strategy, production and digital under one roof', 'ak-zeyna-child' ),
					__( 'We produce the show, not just the deck', 'ak-zeyna-child' ),
					__( 'Working with designers, brands and creative teams across London, Paris and Dubai', 'ak-zeyna-child' ),
				);
				foreach ( $ak_principles as $ak_line ) :
					?>
					<li class="ak-rise" style="display:flex;gap:1rem;align-items:baseline;padding:1rem 0;border-bottom:1px solid var(--rule)">
						<span aria-hidden="true" style="display:block;height:1px;width:1.5rem;flex:none;background:var(--accent-line);transform:translateY(-4px)"></span>
						<span style="font-size:clamp(1rem,2vw,1.25rem);color:var(--text)"><?php echo esc_html( $ak_line ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

			<p style="margin-top:2.5rem;font-size:.9375rem;line-height:1.7;color:var(--text-muted);max-width:62ch">
				<?php
				printf(
					/* translators: 1: services URL, 2: work URL */
					wp_kses(
						__( 'The six movements, and every service inside them, are set out in full on <a href="%1$s">Services</a>; the published evidence is in <a href="%2$s">the work</a>.', 'ak-zeyna-child' ),
						array( 'a' => array( 'href' => array() ) )
					),
					esc_url( $ak_services_href ),
					esc_url( $ak_work_href )
				);
				?>
			</p>
		</div>
	</section>

	<?php get_template_part( 'template-parts/ak-cta' ); ?>

</main>

<?php
get_footer();
