<?php
/**
 * Template Name: AK — About
 *
 * Two named people, not a "we". The founders' bios are the studio's
 * credibility — verbatim from their own brief — and they get the page.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ak_founders = array(
	array(
		'id'    => 'andrey-karakushan',
		'name'  => 'Andrii Karakushan',
		'role'  => __( 'Creative entrepreneur, digital & identity', 'ak-zeyna-child' ),
		'plate' => 'ak-plate--band',
		'bio'   => array(
			__( 'Andrii Karakushan is a creative entrepreneur specialising in brand development, digital presence and visual communication.', 'ak-zeyna-child' ),
			__( 'He has experience managing a multi-brand retail space for emerging designers, supporting young fashion brands through retail presentation and brand promotion.', 'ak-zeyna-child' ),
			__( "He is the founder of the online magazine Cool'baba, a media platform focused on fashion, lifestyle and creative industries.", 'ak-zeyna-child' ),
			__( 'His expertise includes website development, brand identity, digital communication and building online ecosystems for businesses and creative projects.', 'ak-zeyna-child' ),
		),
		'facts' => array(
			array( 'key' => 'RETAIL', 'value' => 'MULTI-BRAND' ),
			array( 'key' => 'MEDIA', 'value' => "COOL'BABA" ),
		),
	),
	array(
		'id'    => 'konstantin-lieontiev',
		'name'  => 'Kostiantyn Lieontiev',
		'role'  => __( 'Fashion producer, brand strategist', 'ak-zeyna-child' ),
		'plate' => 'ak-plate--disc',
		'bio'   => array(
			__( 'Kostiantyn Lieontiev is a fashion producer, brand strategist and media professional with extensive experience in brand development and creative industries.', 'ak-zeyna-child' ),
			__( 'Before focusing on international fashion projects, he spent nine years managing a regional branch of a major advertising holding, where he was responsible for business development, advertising campaigns and strategic client development.', 'ak-zeyna-child' ),
			__( 'He is the founder and producer of the international fashion platforms London Fashion Day and Odessa Fashion Day, created to support emerging designers and develop international creative communities. He is also the founder of the fashion brand KEKA, currently being developed for the international market.', 'ak-zeyna-child' ),
			__( 'Through his work he has collaborated with designers, brands and creative teams across London, Paris and Dubai.', 'ak-zeyna-child' ),
		),
		'facts' => array(
			array( 'key' => 'ADVERTISING', 'value' => '9 YRS' ),
			array( 'key' => 'PLATFORMS', 'value' => '2 FOUNDED' ),
		),
	),
);
?>

<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<section class="ak-section">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><?php esc_html_e( 'The studio — London, United Kingdom', 'ak-zeyna-child' ); ?></p>
			<h1 class="ak-display ak-display--hero" data-ak-cut><?php esc_html_e( 'An independent practice at the intersection of strategy and production.', 'ak-zeyna-child' ); ?></h1>
			<p class="ak-lead"><?php esc_html_e( 'AK Brand Development Studio works at the intersection of brand strategy, fashion production and creative industries. The studio supports founders, designers and businesses in building strong brand identities, developing strategic positioning and creating meaningful visibility through creative campaigns, events and media presence.', 'ak-zeyna-child' ); ?></p>
		</div>
	</section>

	<!-- ── The name, spelled out ────────────────────────────────────────── -->
	<section class="ak-section ak-section--rule">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><?php esc_html_e( 'The name', 'ak-zeyna-child' ); ?></p>
			<div class="ak-monogram">
				<a class="ak-monogram__letter" href="#andrey-karakushan">
					<span class="ak-monogram__glyph ak-vf" aria-hidden="true">A</span>
					<span class="ak-monogram__who">Andrii<br>Karakushan</span>
				</a>
				<span class="ak-monogram__thread ak-draw" aria-hidden="true"></span>
				<a class="ak-monogram__letter" href="#konstantin-lieontiev">
					<span class="ak-monogram__glyph ak-vf" aria-hidden="true">K</span>
					<span class="ak-monogram__who">Kostiantyn<br>Lieontiev</span>
				</a>
			</div>
			<p class="ak-lead" style="margin-top:2.5rem"><?php esc_html_e( 'AK is not an acronym for anything corporate. A is Andrii, K is Kostiantyn — two co-owners, one letter each. One side of the studio runs digital, identity and media; the other runs strategy and fashion production. Both letters are load-bearing: every project below carries the two crafts at once.', 'ak-zeyna-child' ); ?></p>
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

	<section class="ak-section">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><?php esc_html_e( 'How we work', 'ak-zeyna-child' ); ?></p>
			<ul style="list-style:none;margin:2rem 0 0;padding:0;max-width:62ch">
				<?php
				$ak_principles = array(
					__( 'Independent practice, not an agency roster', 'ak-zeyna-child' ),
					__( 'Strategy, production and digital under one roof', 'ak-zeyna-child' ),
					__( 'We produce the show, not just the deck', 'ak-zeyna-child' ),
					__( 'London · Paris · Dubai', 'ak-zeyna-child' ),
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
				$ak_services_page = get_page_by_path( 'services' );
				$ak_services_href = $ak_services_page ? get_permalink( $ak_services_page ) : home_url( '/services/' );
				$ak_work_href     = get_post_type_archive_link( 'portfolio' );
				$ak_work_href     = $ak_work_href ? $ak_work_href : home_url( '/work/' );
				printf(
					/* translators: 1: services URL, 2: work URL */
					wp_kses(
						__( 'The four movements are set out in full on the <a href="%1$s">Services page</a>; the proof lives in <a href="%2$s">the work</a>.', 'ak-zeyna-child' ),
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
