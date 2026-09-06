<?php
/**
 * ATELIER / RUNWAY — the two modes.
 *
 * The mode is the VISITOR'S, and it is decided in one place: an explicit
 * stored choice if there is one, otherwise the operating system's own
 * `prefers-color-scheme`.
 *
 * It used to be decided in two. Zeyna has a per-page "switched" layout — an
 * ACF field (`page_layout`) that puts `layout--switched` on <body> and flips
 * the parent's palette variables for that one page — and this file read it
 * server-side to set the page's default. That read is gone with the rest of
 * the parent's frontend:
 *
 *   · it made a page's opening appearance depend on Zeyna/Pe Core
 *     configuration, which is precisely the dependency this exit removed;
 *   · the field is registered by the parent's ACF group, so on a site where
 *     that group is absent it silently resolved to false anyway, meaning the
 *     "bridge" behaved differently depending on which plugins were active;
 *   · none of the AK routes set it, and AK has no per-page mode design —
 *     the two rooms are a visitor's choice, not an editorial one.
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
 * The attribute goes on <html>, not <body>: assets/js/ak-nav.js adopts the
 * incoming body class on every soft navigation, so a mode kept there would
 * reset on each one. <html> is never swapped.
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
