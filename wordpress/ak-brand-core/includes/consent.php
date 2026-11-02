<?php
/**
 * Cookie consent for analytics, built on Google Consent Mode.
 *
 * Analytics storage is denied by default before any Google tag loads (Site Kit
 * included); it is granted only when the visitor accepts. The choice lives in
 * one first-party cookie, ak_consent, as described in the cookie policy.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

function ak_brand_consent_active(): bool {
	$mode = ak_brand_studio( 'consent' );
	if ( 'off' === $mode ) {
		return false;
	}
	if ( 'on' === $mode ) {
		return true;
	}
	return defined( 'GOOGLESITEKIT_VERSION' ) || '' !== ak_brand_studio( 'ga_id' );
}

/* Defaults before any tag: priority 1, ahead of enqueued scripts. */
add_action(
	'wp_head',
	static function () {
		if ( ! ak_brand_consent_active() ) {
			return;
		}
		$ga = preg_replace( '/[^A-Z0-9-]/', '', strtoupper( ak_brand_studio( 'ga_id' ) ) );
		?>
<script id="ak-consent-defaults">
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
gtag('consent','default',{ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',analytics_storage:'denied',wait_for_update:500});
(function(){var m=document.cookie.match(/(?:^|; )ak_consent=([^;]+)/);window.akConsent=m?m[1]:'';if(window.akConsent==='granted'){gtag('consent','update',{analytics_storage:'granted'});}<?php if ( $ga ) : ?>window.akLoadTag=function(){if(window.akTagLoaded)return;window.akTagLoaded=1;var s=document.createElement('script');s.async=1;s.src='https://www.googletagmanager.com/gtag/js?id=<?php echo esc_js( $ga ); ?>';document.head.appendChild(s);gtag('js',new Date());gtag('config','<?php echo esc_js( $ga ); ?>');};if(window.akConsent==='granted'){window.akLoadTag();}<?php endif; ?>})();
</script>
		<?php
	},
	1
);

add_action(
	'wp_footer',
	static function () {
		if ( ! ak_brand_consent_active() ) {
			return;
		}
		$policy = get_page_by_path( 'cookie-policy' );
		?>
<div class="consent" role="region" aria-label="<?php esc_attr_e( 'Cookie choice', 'ak-brand-core' ); ?>" hidden data-consent>
	<p class="consent__text"><?php esc_html_e( 'We use analytics cookies to see which pages are useful. They are only set if you accept.', 'ak-brand-core' ); ?>
		<?php if ( $policy ) : ?><a href="<?php echo esc_url( get_permalink( $policy ) ); ?>"><?php esc_html_e( 'Cookie policy', 'ak-brand-core' ); ?></a><?php endif; ?></p>
	<div class="consent__actions">
		<button type="button" class="btn btn--sm btn--ghost" data-consent-choice="denied"><?php esc_html_e( 'No thanks', 'ak-brand-core' ); ?></button>
		<button type="button" class="btn btn--sm btn--accent" data-consent-choice="granted"><?php esc_html_e( 'Accept analytics', 'ak-brand-core' ); ?></button>
	</div>
</div>
<script>
(function(){var box=document.querySelector('[data-consent]');if(!box)return;
function show(){box.hidden=false;}
function set(v){var secure=location.protocol==='https:'?';Secure':'';document.cookie='ak_consent='+v+';max-age=15552000;path=/;SameSite=Lax'+secure;window.akConsent=v;
if(typeof gtag==='function'){gtag('consent','update',{analytics_storage:v==='granted'?'granted':'denied'});}
if(v==='granted'&&window.akLoadTag){window.akLoadTag();}
box.hidden=true;}
if(!window.akConsent){show();}
box.addEventListener('click',function(e){var b=e.target.closest('[data-consent-choice]');if(b)set(b.getAttribute('data-consent-choice'));});
document.addEventListener('click',function(e){var o=e.target.closest('[data-consent-open]');if(o){e.preventDefault();show();var f=box.querySelector('button');if(f)f.focus();}});
})();
</script>
		<?php
	},
	50
);

/**
 * A "Cookie settings" link for the footer (empty when no banner is active).
 */
function ak_brand_consent_link(): string {
	if ( ! ak_brand_consent_active() ) {
		return '';
	}
	return '<a href="#" data-consent-open>' . esc_html__( 'Cookie settings', 'ak-brand-core' ) . '</a>';
}

/* Lets the theme hide "Cookie settings" links when there is no banner to reopen. */
add_filter(
	'body_class',
	static function ( $classes ) {
		if ( ak_brand_consent_active() ) {
			$classes[] = 'has-consent';
		}
		return $classes;
	}
);
