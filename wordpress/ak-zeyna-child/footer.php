<?php
/**
 * The AK footer — a designed close, not a copyright line.
 *
 * Overrides Zeyna's footer.php. The closing structure is kept identical to
 * the parent (`</div><!-- #page -->`, wp_footer(), </body></html>) so
 * Barba, the Seam and everything hung on wp_footer keep working, and an
 * Elementor footer template — if one is ever set in Zeyna's options —
 * still takes precedence, exactly as in the parent.
 *
 * @package ak-zeyna-child
 */

$ak_footer_pages = array(
	'home'     => __( 'Home', 'ak-zeyna-child' ),
	'work'     => __( 'Work', 'ak-zeyna-child' ),
	'services' => __( 'Services', 'ak-zeyna-child' ),
	'journal'  => __( 'Journal', 'ak-zeyna-child' ),
	'about'    => __( 'About', 'ak-zeyna-child' ),
	'contact'  => __( 'Contact', 'ak-zeyna-child' ),
);

/*
 * The six movements, read from the factual layer rather than listed here.
 * This was a hard-coded four — Strategy, Identity, Production, Presence — so
 * every page on the site closed with a footer stating a practice two thirds
 * the size of the real one, with photography, film, shows and PR nowhere in
 * it. One source now: inc/projects/capabilities.php.
 */
$ak_movements_nav = array();
foreach ( ak_movements() as $ak_mv_slug => $ak_mv ) {
	$ak_movements_nav[ $ak_mv_slug ] = $ak_mv['name'];
}

$ak_services_page = get_page_by_path( 'services' );
$ak_services_base = $ak_services_page ? get_permalink( $ak_services_page ) : home_url( '/services/' );
$ak_marquee_id    = 'ak-fmq-' . wp_unique_id();
?>

<?php if ( function_exists( 'zeyna_footer_template' ) && zeyna_footer_template() ) : ?>

	<footer id="colophon" class="site-footer footer--overlay">
		<?php echo zeyna_footer_template(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- parent template output. ?>
	</footer><!-- #colophon -->

<?php else : ?>

	<footer id="colophon" class="site-footer ak-footer ak-scope">

		<div class="ak-band ak-band--type" role="marquee" aria-label="<?php esc_attr_e( 'Studio line', 'ak-zeyna-child' ); ?>">
			<input type="checkbox" class="ak-band__pause" id="<?php echo esc_attr( $ak_marquee_id ); ?>" />
			<div class="ak-band__track">
				<?php for ( $ak_copy = 0; $ak_copy < 2; $ak_copy++ ) : ?>
					<div <?php echo $ak_copy ? 'class="ak-band__copy" aria-hidden="true"' : 'class="ak-band__half"'; ?>>
						<span class="ak-band__item"><?php esc_html_e( 'From an idea to an international presence —', 'ak-zeyna-child' ); ?></span>
						<span class="ak-band__item"><?php esc_html_e( 'From an idea to an international presence —', 'ak-zeyna-child' ); ?></span>
					</div>
				<?php endfor; ?>
			</div>
			<label class="ak-band__toggle" for="<?php echo esc_attr( $ak_marquee_id ); ?>"><span class="screen-reader-text"><?php esc_html_e( 'Pause or play the moving line', 'ak-zeyna-child' ); ?></span></label>
		</div>

		<div class="ak-wrap">
			<div class="ak-footer__grid">

				<div>
					<p class="ak-footer__mark"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">AK</a></p>
					<p class="ak-footer__blurb"><?php esc_html_e( 'AK Brand Development Studio — Fashion & Brand Advisory. A is Andrii, K is Kostiantyn: strategy, identity, image, experience, digital and visibility under one roof in London.', 'ak-zeyna-child' ); ?></p>
				</div>

				<nav aria-label="<?php esc_attr_e( 'Footer', 'ak-zeyna-child' ); ?>">
					<p class="ak-footer__head"><?php esc_html_e( 'Studio', 'ak-zeyna-child' ); ?></p>
					<ul>
						<?php foreach ( $ak_footer_pages as $ak_slug => $ak_label ) : ?>
							<?php
							if ( 'work' === $ak_slug ) {
								$ak_href = get_post_type_archive_link( AK_PROJECT_CPT );
								$ak_href = $ak_href ? $ak_href : home_url( '/work/' );
							} else {
								$ak_page = get_page_by_path( $ak_slug );
								$ak_href = $ak_page ? get_permalink( $ak_page ) : home_url( '/' . $ak_slug . '/' );
							}
							?>
							<li><a href="<?php echo esc_url( $ak_href ); ?>"><?php echo esc_html( $ak_label ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>

				<nav aria-label="<?php esc_attr_e( 'Movements', 'ak-zeyna-child' ); ?>">
					<p class="ak-footer__head"><?php esc_html_e( 'Movements', 'ak-zeyna-child' ); ?></p>
					<ul>
						<?php foreach ( $ak_movements_nav as $ak_anchor => $ak_label ) : ?>
							<li><a href="<?php echo esc_url( $ak_services_base . '#' . $ak_anchor ); ?>"><?php echo esc_html( $ak_label ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>

				<div>
					<p class="ak-footer__head"><?php esc_html_e( 'Contact', 'ak-zeyna-child' ); ?></p>
					<address>
						<a href="mailto:<?php echo esc_attr( ak_studio( 'email' ) ); ?>"><?php echo esc_html( ak_studio( 'email' ) ); ?></a><br>
						<?php if ( ak_studio( 'phone' ) ) : ?>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', ak_studio( 'phone' ) ) ); ?>"><?php echo esc_html( ak_studio( 'phone' ) ); ?></a><br>
						<?php endif; ?>
						<?php echo esc_html( ak_studio_location() ); ?>
					</address>

					<?php
					// Only profiles that exist. An unset network prints
					// nothing at all — the previous version rendered every
					// network as an inert href="#", which put five dead links
					// on every page of the site. Set them in
					// Customizer → AK Studio — contact & profiles.
					$ak_socials = ak_socials();
					if ( $ak_socials ) :
						?>
						<ul class="ak-footer__social">
							<?php foreach ( $ak_socials as $ak_net => $ak_url ) : ?>
								<li><a href="<?php echo esc_url( $ak_url ); ?>" target="_blank" rel="noopener me"><?php echo esc_html( $ak_net ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

			</div>

			<div class="ak-footer__bottom">
				<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'AK Brand Development Studio', 'ak-zeyna-child' ); ?></span>
				<span><?php esc_html_e( 'Fashion & Brand Advisory — London', 'ak-zeyna-child' ); ?></span>
			</div>
		</div>

	</footer><!-- #colophon -->

<?php endif; ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>
