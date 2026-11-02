<?php
/**
 * Services: six disciplines as an index, each row opening its page.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$hub = get_page_by_path( 'services' );
?>
<section class="services section" id="services">
	<div class="wrap">
		<?php
		ak_studio_section_head(
			__( 'What we do', 'ak-studio' ),
			__( 'Six disciplines. *One studio.*', 'ak-studio' ),
			$hub ? array( 'url' => get_permalink( $hub ), 'label' => __( 'All services', 'ak-studio' ) ) : array()
		);
		get_template_part( 'template-parts/services-index' );
		?>
	</div>
</section>
