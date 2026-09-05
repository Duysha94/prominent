<?php
/**
 * 404 — the studio's own, not Zeyna's.
 *
 * The parent's 404 renders "Oops! That page can't be found.", a search form
 * and an Elementor template lookup: template furniture on a page that is, in
 * practice, one of the most-seen pages on any site with a history of moved
 * URLs. This one is written in the studio's voice and does the one useful
 * thing a 404 can do — put the four places a visitor was probably heading
 * within a single tap.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$ak_work = get_post_type_archive_link( AK_PROJECT_CPT );
$ak_dest = array(
	array( __( 'Work', 'ak-zeyna-child' ), $ak_work ? $ak_work : home_url( '/work/' ), __( 'Platforms, brands and commissioned engagements', 'ak-zeyna-child' ) ),
	array( __( 'Services', 'ak-zeyna-child' ), home_url( '/services/' ), __( 'Strategy, Identity, Image, Experience, Digital, Visibility', 'ak-zeyna-child' ) ),
	array( __( 'Journal', 'ak-zeyna-child' ), home_url( '/journal/' ), __( 'Notes on brand, fashion and presence', 'ak-zeyna-child' ) ),
	array( __( 'Contact', 'ak-zeyna-child' ), home_url( '/contact/' ), __( 'Tell us where the brand is', 'ak-zeyna-child' ) ),
);
?>

<main id="primary" class="site-main ak-scope" <?php echo function_exists( 'zeyna_barba' ) ? zeyna_barba( false ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

	<section class="ak-section">
		<div class="ak-wrap">

			<p class="ak-eyebrow"><span class="ak-eyebrow__rule ak-draw" aria-hidden="true"></span><span class="ak-eyebrow__folio">404</span><?php esc_html_e( 'Page not found', 'ak-zeyna-child' ); ?></p>

			<h1 class="ak-display ak-display--hero" data-ak-cut><?php esc_html_e( 'This page is not part of the collection.', 'ak-zeyna-child' ); ?></h1>

			<p class="ak-lead"><?php esc_html_e( 'The address does not match anything we publish. It may have moved, or it may never have existed. Either way, here is everything that does.', 'ak-zeyna-child' ); ?></p>

			<ul class="ak-index" style="list-style:none;padding:0">
				<?php foreach ( $ak_dest as $ak_i => $ak_d ) : ?>
					<li class="ak-index__row ak-rise">
						<a class="ak-index__link" href="<?php echo esc_url( $ak_d[1] ); ?>">
							<span class="ak-index__folio"><?php echo esc_html( str_pad( (string) ( $ak_i + 1 ), 3, '0', STR_PAD_LEFT ) ); ?></span>
							<span class="ak-index__client"><?php echo esc_html( $ak_d[0] ); ?></span>
							<span class="ak-index__title"><?php echo esc_html( $ak_d[2] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<p style="margin-top:2.5rem">
				<a class="ak-btn ak-btn--fill" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the studio', 'ak-zeyna-child' ); ?></a>
			</p>

		</div>
	</section>

</main><!-- #primary -->

<?php
get_footer();
