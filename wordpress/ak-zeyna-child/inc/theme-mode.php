<?php
/**
 * ATELIER / RUNWAY — the two modes.
 *
 * Zeyna has no light/dark mode. Verified against the theme source: not one
 * occurrence of `data-theme`, `.dark`, `.light-mode` or `prefers-color-scheme`
 * in its CSS, and no toggle in its JavaScript. What looks like a dark mode in
 * the demos is a *demo variant* — a Redux colour preset imported once — not a
 * switch a visitor can operate. So the studio's own two-mode system fills that
 * gap.
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
		?>
<script>
(function(){try{var t=localStorage.getItem('ak-theme');if(!t){t=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-theme',t);}catch(e){document.documentElement.setAttribute('data-theme','light');}})();
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
function ak_mode_toggle() {
	?>
	<button type="button" class="ak-mode" data-ak-mode aria-pressed="false">
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
