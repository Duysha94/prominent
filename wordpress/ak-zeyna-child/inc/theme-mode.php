<?php
/**
 * ATELIER / RUNWAY — the two modes.
 *
 * Zeyna itself has no visitor-facing dark switch. What it DOES have is a
 * per-page "switched" layout: an ACF field (`page_layout`) that puts
 * `layout--switched` on <body> and flips the parent's palette variables for
 * that one page. The child bridges both worlds: that Zeyna field sets the
 * page's DEFAULT mode here, the AK toggle remains the visitor's control,
 * and an explicit visitor choice always wins. The parent's palette
 * variables are re-mapped onto the AK tokens in ak.css, so whichever way
 * the mode is set, every part of the chrome follows.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the mode BEFORE first paint.
 *
 * This runs synchronously in <head>, ahead of any stylesheet paint. A mode
 * applied on DOMContentLoaded flashes white on every load, which on an
 * orange-on-ink design is glaring. It is deliberately inline and unminified so
 * it is auditable.
 *
 * The attribute goes on <html>, not <body>: Barba replaces body classes
 * wholesale on every soft navigation, and a mode kept there would reset each
 * time.
 */
add_action(
	'wp_head',
	function () {
		// Zeyna's per-page "switched" (dark) layout, read server-side. With
		// ACF absent the child's get_field() shim returns null and this is
		// simply false.
		$ak_page_dark = function_exists( 'get_field' ) && 'layout--switched' === get_field( 'page_layout' );
		?>
<script>
(function(){var d=<?php echo $ak_page_dark ? 'true' : 'false'; ?>;try{var t=localStorage.getItem('ak-theme');if(!t){t=d||window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-theme',t);}catch(e){document.documentElement.setAttribute('data-theme',d?'dark':'light');}})();
</script>
		<?php
	},
	0
);

/**
 * The switch, named rather than symbolised.
 *
 * A sun/moon icon says "there is a dark mode". Naming the two rooms says the
 * studio designed both, which is the actual claim.
 *
 * Drop `<?php ak_mode_toggle(); ?>` into the header, or use the
 * [ak_mode_toggle] shortcode from a builder.
 */
function ak_mode_toggle( $extra_class = '' ) {
	?>
	<button type="button" class="ak-mode <?php echo esc_attr( $extra_class ); ?>" data-ak-mode aria-pressed="false">
		<span class="ak-mode__label">Atelier</span>
		<span class="ak-mode__track" aria-hidden="true">
			<span class="ak-mode__knob"></span>
		</span>
	</button>
	<?php
}

add_shortcode(
	'ak_mode_toggle',
	function () {
		ob_start();
		ak_mode_toggle();
		return ob_get_clean();
	}
);
