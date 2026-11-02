<?php
/**
 * Site footer: one last invitation, then everything a visitor looks for.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$email    = ak_studio_get( 'email' );
$profiles = ak_studio_profiles();
$legal    = array_filter( array( ak_studio_get( 'legal_name' ), ak_studio_get( 'company_number' ) ? sprintf( /* translators: %s: company number */ __( 'Company no. %s', 'ak-studio' ), ak_studio_get( 'company_number' ) ) : '', ak_studio_get( 'registered_addr' ) ) );
?>
<footer class="site-foot">
	<div class="wrap site-foot__cta">
		<p class="eyebrow"><?php esc_html_e( 'Next season starts with a brief', 'ak-studio' ); ?></p>
		<a class="site-foot__mail" href="<?php echo esc_url( ak_studio_contact_url() ); ?>" data-cursor="<?php esc_attr_e( 'Brief', 'ak-studio' ); ?>">
			<span class="site-foot__mail-line" data-split><?php echo ak_studio_heading( __( 'Have a brand *in mind?*', 'ak-studio' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<?php echo ak_studio_arrow( 'site-foot__mail-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
	</div>

	<div class="wrap site-foot__grid">
		<div class="site-foot__col">
			<p class="site-foot__label"><?php esc_html_e( 'Studio', 'ak-studio' ); ?></p>
			<p><a class="site-foot__email" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p>
			<?php if ( ak_studio_get( 'phone' ) ) : ?>
				<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', ak_studio_get( 'phone' ) ) ); ?>"><?php echo esc_html( ak_studio_get( 'phone' ) ); ?></a></p>
			<?php endif; ?>
			<p><?php echo esc_html( implode( ', ', array_filter( array( ak_studio_get( 'city' ), ak_studio_get( 'country' ) ) ) ) ); ?></p>
			<?php if ( $profiles ) : ?>
				<ul class="site-foot__list">
					<?php foreach ( $profiles as $label => $url ) : ?>
						<li><a href="<?php echo esc_url( $url ); ?>" rel="me noopener" target="_blank"><?php echo esc_html( $label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<div class="site-foot__col">
			<p class="site-foot__label"><?php esc_html_e( 'Services', 'ak-studio' ); ?></p>
			<?php
			if ( has_nav_menu( 'services' ) ) {
				wp_nav_menu( array( 'theme_location' => 'services', 'container' => false, 'menu_class' => 'site-foot__list', 'depth' => 1 ) );
			} else {
				echo '<ul class="site-foot__list">';
				foreach ( ak_studio_movements() as $m ) {
					$page = ak_studio_service_page( $m['slug'] );
					if ( $page ) {
						echo '<li><a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( $m['name'] ) . '</a></li>';
					}
				}
				echo '</ul>';
			}
			?>
		</div>
		<div class="site-foot__col">
			<p class="site-foot__label"><?php esc_html_e( 'Our platforms', 'ak-studio' ); ?></p>
			<ul class="site-foot__list">
				<?php foreach ( ak_studio_platforms() as $pl ) : ?>
					<?php $link = $pl['url'] ?: $pl['social']; ?>
					<li><?php if ( $link ) : ?><a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $pl['name'] ); ?></a><?php else : ?><?php echo esc_html( $pl['name'] ); ?><?php endif; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="site-foot__col">
			<p class="site-foot__label"><?php esc_html_e( 'Site', 'ak-studio' ); ?></p>
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'site-foot__list', 'depth' => 1 ) );
			}
			if ( function_exists( 'ak_brand_consent_link' ) && ak_brand_consent_link() ) {
				echo '<ul class="site-foot__list"><li>' . ak_brand_consent_link() . '</li></ul>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped by the plugin.
			}
			?>
		</div>
	</div>

	<div class="site-foot__word" aria-hidden="true"><span>A<i>K</i> <?php esc_html_e( 'Brand Development Studio', 'ak-studio' ); ?></span></div>

	<div class="wrap site-foot__base">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( ak_studio_get( 'name' ) ); ?><?php echo $legal ? ' · ' . esc_html( implode( ' · ', $legal ) ) : ''; ?></p>
		<p><?php echo esc_html( ak_studio_get( 'promise' ) ); ?></p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
