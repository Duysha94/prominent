<?php
/**
 * Template Name: AK — Services
 *
 * Four movements, each stating what it hands to the next. The page is a chain
 * rather than a menu, so the argument for the whole practice is in the layout.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ak_movements = array(
	array(
		'id'       => 'strategy',
		'title'    => __( 'Strategy', 'ak-zeyna-child' ),
		'claim'    => __( 'Decide what the brand is, who it is for, and what it will not be.', 'ak-zeyna-child' ),
		'handover' => __( 'Leaves Identity a written position to design against, not a mood board.', 'ak-zeyna-child' ),
		'items'    => array(
			__( 'Brand concept development', 'ak-zeyna-child' ),
			__( 'Brand positioning and strategy', 'ak-zeyna-child' ),
			__( 'Brand relaunch and repositioning', 'ak-zeyna-child' ),
			__( 'Brand philosophy and identity foundations', 'ak-zeyna-child' ),
			__( 'Marketing and communication strategy', 'ak-zeyna-child' ),
			__( 'Personal brand strategy for founders and public figures', 'ak-zeyna-child' ),
			__( 'Strategic guidance for business growth', 'ak-zeyna-child' ),
		),
		'mkey'     => 'POSITION',
		'mval'     => __( '1 page', 'ak-zeyna-child' ),
		'mnote'    => __( 'A position that needs twelve slides is one nobody in your team will remember on a Tuesday.', 'ak-zeyna-child' ),
	),
	array(
		'id'       => 'identity',
		'title'    => __( 'Identity', 'ak-zeyna-child' ),
		'claim'    => __( 'Give the position a face that survives contact with the real world.', 'ak-zeyna-child' ),
		'handover' => __( 'Leaves Production a system that holds at campaign scale and at 4:5.', 'ak-zeyna-child' ),
		'items'    => array(
			__( 'Brand identity development', 'ak-zeyna-child' ),
			__( 'Logo and identity design', 'ak-zeyna-child' ),
			__( 'Creative and visual direction', 'ak-zeyna-child' ),
			__( 'Brand guidelines', 'ak-zeyna-child' ),
			__( 'Visual storytelling and content direction', 'ak-zeyna-child' ),
		),
		'mkey'     => 'SCALE',
		'mval'     => '4:5 → 6m',
		'mnote'    => __( 'Drawn at ad size first and at runway backdrop size second. Most identities are made the other way round.', 'ak-zeyna-child' ),
	),
	array(
		'id'       => 'production',
		'title'    => __( 'Production', 'ak-zeyna-child' ),
		'claim'    => __( 'Make it exist — the campaign, the event, the show, the room.', 'ak-zeyna-child' ),
		'handover' => __( 'Leaves Presence real assets and real coverage, not renders.', 'ak-zeyna-child' ),
		'items'    => array(
			__( 'Photo campaigns and creative direction', 'ak-zeyna-child' ),
			__( 'Promotional video production', 'ak-zeyna-child' ),
			__( 'Brand presentations and product launches', 'ak-zeyna-child' ),
			__( 'Creative events and brand experiences', 'ak-zeyna-child' ),
			__( 'Independent fashion shows', 'ak-zeyna-child' ),
			__( 'Fashion show production during international fashion weeks', 'ak-zeyna-child' ),
			__( 'Industry PR and press support', 'ak-zeyna-child' ),
		),
		'mkey'     => 'RUNWAY',
		'mval'     => __( 'Ours', 'ak-zeyna-child' ),
		'mnote'    => __( 'Our co-founder created London Fashion Day and Odessa Fashion Day. Most studios have to ask for a slot.', 'ak-zeyna-child' ),
	),
	array(
		'id'       => 'presence',
		'title'    => __( 'Presence', 'ak-zeyna-child' ),
		'claim'    => __( 'Put it where people already are, and make it findable.', 'ak-zeyna-child' ),
		'handover' => __( 'Feeds what the audience does back into Strategy — the position sharpens every season.', 'ak-zeyna-child' ),
		'items'    => array(
			__( 'Website creation and development', 'ak-zeyna-child' ),
			__( 'E-commerce and online stores', 'ak-zeyna-child' ),
			__( 'Social media presence and digital content', 'ak-zeyna-child' ),
			__( 'Online brand positioning', 'ak-zeyna-child' ),
			__( 'Digital advertising campaigns', 'ak-zeyna-child' ),
			__( 'Google, YouTube and Meta promotion', 'ak-zeyna-child' ),
			__( 'Audience growth and engagement', 'ak-zeyna-child' ),
		),
		'mkey'     => 'FOUND',
		'mval'     => __( 'Organic', 'ak-zeyna-child' ),
		'mnote'    => __( 'Paid reach you rent. Search and press you keep. We build for both, and report both.', 'ak-zeyna-child' ),
	),
);
?>

<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<section class="ak-section">
		<div class="ak-wrap">
			<p class="ak-eyebrow"><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><?php esc_html_e( 'Services', 'ak-zeyna-child' ); ?></p>
			<h1 class="ak-display ak-display--hero" data-ak-cut><?php esc_html_e( 'Four movements, and what each one hands over.', 'ak-zeyna-child' ); ?></h1>
			<p class="ak-lead"><?php esc_html_e( 'Nine services, one sequence: from the initial idea to international presence. Each movement states what it leaves the next, so nothing is lost in a handover between suppliers — because there is no handover between suppliers.', 'ak-zeyna-child' ); ?></p>
		</div>
	</section>

	<?php foreach ( $ak_movements as $ak_i => $ak_m ) : ?>
		<section class="ak-section ak-section--rule" id="<?php echo esc_attr( $ak_m['id'] ); ?>" style="scroll-margin-top:6rem">
			<div class="ak-wrap">
				<div class="ak-founder">
					<div>
						<p class="ak-eyebrow"><span class="ak-eyebrow__folio">0<?php echo (int) ( $ak_i + 1 ); ?></span><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><?php esc_html_e( 'Movement', 'ak-zeyna-child' ); ?></p>
						<h2 class="ak-founder__name ak-vf"><?php echo esc_html( $ak_m['title'] ); ?></h2>
						<p class="ak-lead" style="margin-top:1rem;max-width:34ch"><?php echo esc_html( $ak_m['claim'] ); ?></p>

						<div class="ak-chapter__measure ak-rise" style="margin-top:2rem;max-width:20rem">
							<span><?php esc_html_e( 'Judged on', 'ak-zeyna-child' ); ?> — <?php echo esc_html( $ak_m['mkey'] ); ?></span>
							<b><?php echo esc_html( $ak_m['mval'] ); ?></b>
							<p style="font-size:.75rem;line-height:1.6;color:var(--text-muted);margin:.6rem 0 0"><?php echo esc_html( $ak_m['mnote'] ); ?></p>
						</div>
					</div>

					<div>
						<ul style="list-style:none;margin:0;padding:0;border-top:1px solid var(--rule)">
							<?php foreach ( $ak_m['items'] as $ak_j => $ak_item ) : ?>
								<li class="ak-rise" style="display:flex;gap:1.25rem;align-items:baseline;padding:1.15rem 0;border-bottom:1px solid var(--rule)">
									<span class="ak-index__folio"><?php echo esc_html( str_pad( (string) ( $ak_j + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
									<span style="font-size:clamp(1rem,2vw,1.375rem);line-height:1.35;color:var(--text)"><?php echo esc_html( $ak_item ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>

						<p class="ak-rise" style="display:flex;gap:1rem;align-items:flex-start;background:var(--bg-sunk);padding:1.5rem;margin-top:2rem">
							<span aria-hidden="true" style="margin-top:.6rem;display:block;height:1px;width:2rem;flex:none;background:var(--accent-line)"></span>
							<span style="font-size:.875rem;line-height:1.6;color:var(--text-muted);max-width:52ch">
								<span style="font-family:var(--font-mono);font-size:.5625rem;letter-spacing:.16em;text-transform:uppercase;color:var(--accent-text)"><?php echo ( count( $ak_movements ) - 1 === $ak_i ) ? esc_html__( 'Loops back — ', 'ak-zeyna-child' ) : esc_html__( 'Hands over — ', 'ak-zeyna-child' ); ?></span>
								<?php echo esc_html( $ak_m['handover'] ); ?>
							</span>
						</p>

						<p class="ak-rise" style="margin-top:1.25rem">
							<a href="<?php echo esc_url( get_post_type_archive_link( 'portfolio' ) ? get_post_type_archive_link( 'portfolio' ) : home_url( '/work/' ) ); ?>" style="font-family:var(--font-mono);font-size:.625rem;letter-spacing:.14em;text-transform:uppercase;color:var(--accent-text);text-decoration:none"><?php
								/* translators: %s: movement title */
								printf( esc_html__( 'See %s in the work →', 'ak-zeyna-child' ), esc_html( $ak_m['title'] ) );
							?></a>
						</p>
					</div>
				</div>
			</div>
		</section>
	<?php endforeach; ?>

	<?php get_template_part( 'template-parts/ak-cta' ); ?>

</main>

<?php
get_footer();
