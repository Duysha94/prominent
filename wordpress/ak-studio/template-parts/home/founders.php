<?php
/**
 * Founders teaser: A + K.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$about = get_page_by_path( 'about' );
?>
<section class="founders-teaser section" id="founders">
	<div class="wrap">
		<?php ak_studio_section_head( __( 'The founders', 'ak-studio' ), __( 'One letter *each.*', 'ak-studio' ), $about ? array( 'url' => get_permalink( $about ), 'label' => __( 'About the studio', 'ak-studio' ) ) : array() ); ?>
		<?php get_template_part( 'template-parts/founders', null, array( 'short' => true ) ); ?>
	</div>
</section>
