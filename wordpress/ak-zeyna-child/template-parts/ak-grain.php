<?php
/**
 * The material layer: paper fibre for ATELIER, film grain for RUNWAY.
 *
 * Two static feTurbulence fields; ak.css decides which one shows per mode
 * and holds the blend/opacity tokens. Printed from wp_footer beside the
 * Seam — outside `[data-ak-container]` — so it renders once and persists.
 * The filters are generated once and composited; nothing here animates.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ak-grain" aria-hidden="true">
	<svg class="ak-grain__fibre" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
		<filter id="ak-grain-fibre"><feTurbulence type="fractalNoise" baseFrequency="0.62" numOctaves="2" seed="7" stitchTiles="stitch" /></filter>
		<rect width="100%" height="100%" filter="url(#ak-grain-fibre)" />
	</svg>
	<svg class="ak-grain__film" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
		<filter id="ak-grain-film"><feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="2" seed="3" stitchTiles="stitch" /></filter>
		<rect width="100%" height="100%" filter="url(#ak-grain-film)" />
	</svg>
</div>
