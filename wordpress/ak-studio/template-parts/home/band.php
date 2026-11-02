<?php
/**
 * The capability band: every discipline, on one tilted accent strip.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$words = array( 'Brand strategy', 'Identity', 'Creative direction', 'Photo & film', 'Fashion shows', 'Launches & events', 'Websites', 'PR', 'Paid media' );
$items = '';
foreach ( $words as $word ) {
	$items .= '<span class="band__item">' . esc_html( $word ) . '</span><span class="band__dot" aria-hidden="true">✦</span>';
}
?>
<section class="band" id="services" aria-label="<?php esc_attr_e( 'What we do', 'ak-studio' ); ?>">
	<div class="band__strip">
		<div class="band__track"><?php echo $items . $items; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?></div>
	</div>
</section>
