<?php
/**
 * THE SEAM.
 *
 * One orange thread running the length of every page — scroll position made
 * physical. Straight and taut: the orange fill is scroll progress and the
 * knot riding it is where you are in the document.
 *
 * Printed from wp_footer, which is outside `[data-ak-container]`, so this
 * markup survives every soft navigation and is initialised exactly once.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ak-seam" data-ak-seam aria-hidden="true">
	<svg width="60" height="100%" viewBox="0 0 60 1000" preserveAspectRatio="none" fill="none">
		<path d="M 30 0 L 30 1000" stroke="var(--rule-strong)" stroke-width="1" vector-effect="non-scaling-stroke" />
		<path d="M 30 0 L 30 1000" stroke="var(--accent-line)" stroke-width="1.5" vector-effect="non-scaling-stroke" />
		<circle cx="30" cy="0" r="3.5" fill="var(--accent-fill)" />
	</svg>
</div>
