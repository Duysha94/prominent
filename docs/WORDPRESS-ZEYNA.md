# Building this on WordPress + Zeyna

> ## ✅ This document is now verified
>
> The theme package was supplied and audited directly. **Zeyna 1.5.0 by Pe
> Themes**, requires PHP 8.0. Everything below is read from its source unless
> explicitly marked otherwise.
>
> Three findings changed the plan, and one of them changes the brief:
>
> 1. **Barba.js confirmed** — and the wrapper contract is not where you would
>    guess. See §2.
> 2. **Header and footer are plain PHP, not Elementor theme-builder
>    locations.** `elementor_theme_do_location` appears nowhere. This removes
>    the single most expensive risk in the original estimate — see §3.
> 3. **Zeyna has no light/dark mode.** Not a toggle, not a class, not a media
>    query. See §6. This matters because keeping it was part of the brief.

---

## 0. What the audit found

| Question | Answer |
|---|---|
| Theme | **Zeyna 1.5.0**, Pe Themes, requires PHP 8.0 |
| Transition engine | **Barba.js** (`js/barba.min.js`), driving GSAP |
| Wrapper | `data-barba="wrapper"` on `<body>`, `data-barba="container"` on **`<main id="primary">`** |
| Namespace | **None emitted.** No `data-barba-namespace` anywhere |
| Footer | **Outside** the container — it persists across navigations |
| Header / footer | **Classic `header.php` / `footer.php`.** Not Elementor locations |
| Options framework | **Redux** (`pe-redux`), transitions gated on a `page_transitions` option |
| Bundled JS | barba, gsap, gsap-plugins, **lenis**, **three**, dotlottie-player, plugins, scripts |
| Light / dark mode | **Does not exist.** Zero `data-theme`, `.dark` or `prefers-color-scheme` in the theme CSS |
| Commerce | Full WooCommerce template overrides included |
| Content types | `portfolio` post type + `project-categories` taxonomy |
| Barba timeout | Already `15000` — the usual 2s footgun is not present |

**The stack alignment is the good news.** Zeyna already bundles GSAP, Lenis,
Barba and three.js — the same engines this prototype is built on. Porting the
motion system is therefore a translation, not a rewrite.

---

## 1. What "we keep only the shell" actually means

Better than expected. The shell is **plain PHP**, so keeping it means keeping:

| Kept | Notes |
|---|---|
| `header.php` / `footer.php` | Classic templates — edit or extend in a child theme |
| The WP nav menu + mobile panel | Rendered by `header.php` |
| Barba + the theme's transition JS | See §2 |
| Redux theme options | `page_transitions` gates the whole transition system |
| WooCommerce overrides | Keep — the studio builds online stores |
| `portfolio` post type + taxonomy | This is where case studies live |

**No Elementor Pro dependency for the shell.** That removes a recurring licence
cost and, more importantly, removes the entire `elementorFrontend.init()`
re-initialisation problem for content pages. Elementor appears in 18 PHP files
as optional widget compatibility, not as the header/footer mechanism — so if
the site is authored with ACF and custom templates, Elementor need not be
installed at all.

**One item in the brief cannot be kept, because it does not exist: see §6.**

---

## 2. The wrapper contract — the thing you must not break

Verified. `inc/template-tags.php` defines `zeyna_barba($body)`, which returns
the attribute **only when the Redux option `page_transitions` is on**:

```php
// header.php line 32
<body <?php body_class(); ?> <?php echo zeyna_barba(true) ?>>   // data-barba="wrapper"

// index.php / page.php / single.php / archive.php / 404.php
<main id="primary" class="site-main" <?php echo zeyna_barba(false) ?>>  // data-barba="container"
```

Two consequences worth knowing before writing a template:

**The container is `<main>`, not a wrapper div.** Anything a bespoke page
renders must live inside `<main id="primary">` or it will not be swapped.

**`get_footer()` is called after `</main>`, so the footer is OUTSIDE the
container and persists across navigations.** That is the configuration we
wanted: it means the Seam can live in the footer and survive every navigation
instead of being rebuilt, and the footer CTA never re-animates.

**Zeyna emits no `data-barba-namespace`.** Per-template transitions are
therefore not available out of the box — if we want the case-study transition
to differ from the contact transition, we add the namespace ourselves:

```php
// child theme: template-parts/barba-open.php
$ns = is_front_page() ? 'home'
  : ( is_singular('portfolio') ? 'case'
  : ( is_post_type_archive('portfolio') ? 'work' : 'page' ) );
```

**Every bespoke page template must emit that container with a meaningful
namespace**, or the transition silently degrades to a full browser navigation
and nobody notices until launch. Emit it from one shared partial, never by hand:

```php
<?php /* template-parts/barba-open.php */
$namespace = is_front_page() ? 'home'
  : ( is_singular('case_study') ? 'case-study'
  : ( is_post_type_archive('case_study') ? 'work'
  : ( is_page('contact') ? 'contact' : 'page' ) ) );
?>
<div data-barba="container" data-barba-namespace="<?php echo esc_attr( $namespace ); ?>">
```

**Decide deliberately whether the footer sits inside or outside the container.**
Inside → it is torn down and rebuilt per navigation, so it can be bespoke per
page. Outside → it persists across navigations. For this site the footer CTA is
the same everywhere, so **put it outside**: it is a one-line change and a free
performance win.

---

## 3. How to author bespoke pages inside that shell

**Recommendation: ACF Pro flexible content + custom page templates in a child
theme. Do not use Elementor for page bodies.**

This is not a stylistic preference — it removes two entire classes of bug:

**Elementor must be manually re-initialised after every container swap.**
Because Barba replaces the container, Elementor's JS stays bound to destroyed
DOM. The production fix is to clone-and-re-execute a hard-coded allowlist of
script nodes from the incoming document (`#elementor-frontend-js`,
`#elementor-pro-js`, `#elementor-frontend-js-before`, `…-after`,
`…-before-vendors`, `#swiper-js`, `#imagesloaded-js`, and more), then call
`elementorFrontend.init()`, then re-fire per-widget ready triggers. Every
Elementor widget on a bespoke page is one more thing that must survive
`elementorFrontend.init()` running twice on a mutated document.

**If your bespoke pages contain zero Elementor widgets, that entire step
disappears** for content pages. You only need it for header/footer — which live
*outside* the swapped container and therefore never re-init at all.

**Elementor writes per-page stylesheets.** Barba only swaps the container, so
the `<head>` keeps the previous page's assets and bespoke pages render
unstyled. The fix is a full DOMParser head-diff (styles by id, in server
order, then links, then scripts). **Ship one compiled global stylesheet
instead and head-sync becomes a no-op.**

So:

```
child-theme/
  functions.php               enqueue one CSS + one JS bundle; dequeue theme animation libs
  template-parts/
    barba-open.php            the container contract, one place
    sections/                 one partial per ACF flexible-content layout
  assets/
    dist/site.css             compiled — the design tokens live here
    dist/site.js              IIFE/UMD, depends on jQuery only where it must
```

---

## 4. The re-init registry — write this before the second template

The single biggest maintainability win, and about 60 lines.

Instead of the transition system hard-coding every component that needs
teardown, **components register themselves**:

```js
window.ak.pageTransitions.register({
  id: 'page:work',
  cleanup(container) { ctx.revert() },   // kills timelines AND their ScrollTriggers
  reinit(container)  { ScrollTrigger.refresh() },
})
```

Backed by a `Map` so ids are unique, each phase run in registration order inside
`try/catch` so one throwing handler cannot break navigation. Mirror it with four
`CustomEvent`s carrying `{ detail: { container } }`:
`ak:page-transition-start`, `ak:before-swap`, `ak:after-swap`,
`ak:page-transition-complete`.

**GSAP teardown pattern** — build every page's animations inside a context
scoped to the container:

```js
const ctx = gsap.context(() => { /* all ScrollTriggers, timelines, observers */ }, container)
// cleanup:
ctx.revert()  // kills timelines + their ScrollTriggers + reverts inline styles, in one call
```

Without this you accumulate dead ScrollTriggers per navigation and scroll
positions drift; without `ScrollTrigger.refresh()` on enter, pinned sections
compute against the wrong document height and the whole scroll story is offset.

---

## 5. Barba config — what Zeyna already gets right

Zeyna already sets `timeout: 15000`, so the classic 2-second footgun (an
uncached WordPress page exceeding the default and silently falling back to a
full navigation) is **not** present. It also already marks the WooCommerce
cart block with `data-barba-prevent="all"`.

What is still worth adding in the child theme:

```js
const IGNORED = [
  '.pdf','.doc','.eps','.png','.jpg','.jpeg','.zip',
  'wp-admin','wp-login','wp-','feed','#',
  '&add-to-cart=','?add-to-cart=','?remove_item',
]

barba.init({
  // Default is 2000ms. An uncached WordPress page routinely exceeds it, and
  // Barba then hard-falls-back to a full navigation — which is why transitions
  // "randomly don't work" on staging.
  timeout: 10000,
  prefetchIgnore: true,
  prevent: ({ el, href }) =>
    el.target === '_blank' ||
    el.hasAttribute('data-no-transition') ||
    IGNORED.some((p) => href.includes(p)),
  // Failures degrade to a real navigation instead of a dead page.
  requestError: (trigger, action, url) => { window.location.href = url },
  transitions: [/* … */],
})
```

Also skip init entirely inside the Customizer preview
(`body.is--customizer-preview`) or the editor breaks.

**For this client specifically:** add the agency's own exclusions for
UTM-laden and `?gclid=` inbound URLs so the router cannot mangle paid-media
landing pages.

---

## 6. Dark mode — it does not exist in this theme

**This is the one place the brief and the theme disagree.**

The brief said to keep Zeyna's light/dark mode. The theme has none. Verified:
zero occurrences of `data-theme`, `.dark`, `.light-mode` or
`prefers-color-scheme` anywhere in its CSS, and no toggle in its JS. What
looks like a dark mode in the demos is a *demo variant* — a different Redux
colour preset you import once (`digital-agency-dark.xml`), not a runtime
switcher a visitor can operate.

So the two-mode system has to be built, and this prototype already contains it:
ATELIER and RUNWAY as two designed rooms rather than one palette inverted, with
different grain material per mode. Port `src/styles/tokens.css` and
`src/styles/base.css`, and add the toggle to `header.php`.

Because Barba replaces body classes wholesale, **the mode must be explicitly
preserved or it flashes back on every navigation.**

1. Sync body classes from the incoming document, then re-add a preserve list
   that **must include the dark-mode class** (whatever the audit finds Zeyna
   calls it), plus `admin-bar` and any JS-set loaded flags.
2. Strip the server-side `is-loading` class — meaningless after an AJAX nav.
3. Set the mode from `localStorage` in a **blocking inline `<script>` in
   `<head>`**, before first paint. A toggle that runs on `DOMContentLoaded`
   flashes white on every hard load, which on an orange-accented dark design is
   extremely visible.

The prototype already does step 3 — see `index.html`. Port it verbatim into
`header.php` / the Elementor header template's before-render hook.

---

## 7. Lenis wiring

```js
const lenis = new Lenis({ anchors: true, autoRaf: false, lerp: 0.1 })
lenis.on('scroll', ScrollTrigger.update)
gsap.ticker.add((time) => lenis.raf(time * 1000))
gsap.ticker.lagSmoothing(0)
```

- **Never** set `autoRaf: true` alongside the ticker integration — two rAF loops.
- **`anchors` is off by default**, so without it every in-page jump link
  silently does nothing.
- Put `data-lenis-prevent` on the mobile menu panel and any modal, or the
  overlay scroll fights the page.
- Call `lenis.destroy()` in the registry `cleanup`, or set
  `stopInertiaOnNavigate: true` and keep one instance across swaps.
- Keep `lerp` at 0.1. ThemeForest demos favour 0.04–0.06, which is where smooth
  scroll starts to feel like lag rather than polish.

---

## 8. Do not replace Barba with cross-document View Transitions

It is tempting in 2026 — it is native, and scripts/forms/analytics work by
construction. **It will silently delete the loader.**

View Transitions removes script execution between documents and the snapshot is
static, so you cannot run an animation while the next page is fetching and you
cannot hold the cover phase until the page is ready. A motion-led agency site
with a dynamic-duration loader needs exactly that.

**The defensible position:** Barba for routing and the cover phase; native View
Transitions as a progressive enhancement on *same-document* state changes —
filter changes on the work index, tab switches — where there is no fetch to
wait on. `view-transition-name: match-element` is well supported and gets you
the work-thumbnail → case-hero morph for almost nothing.

---

## 9. Analytics — non-negotiable for a paid-media client

Soft navigations do not fire page views. On a site that sells Meta and Google
Ads, getting this wrong is a credibility problem, not just a data problem.

In `barba.hooks.after`:

```js
document.title = /* title from the incoming document */
gtag('event', 'page_view', { page_location: location.href, page_title: document.title })
fbq('track', 'PageView')
```

Then **verify by hand** that Google Ads conversion tags and Meta CAPI browser
events still fire on the thank-you route when reached by soft navigation. If
WordPress Speculation Rules / prerendering is enabled, guard every tag with
`document.prerendering` and the `prerenderingchange` event or the Meta Pixel
double-counts.

---

## 10. Accessibility across the swap

Every reference implementation reviewed skips this. Barba ships nothing for it.

After the container swap:

```js
container.setAttribute('tabindex', '-1')
container.focus({ preventScroll: true })
// announce the new page title in an aria-live="polite" region
// restore scroll to top unless this is a back/forward navigation
```

And gate every transition timeline behind `prefers-reduced-motion` — the
prototype's approach in `src/lib/useReducedMotion.ts` and
`src/components/shell/PageTransition.tsx` ports directly.

---

## 11. What ports from this prototype, and how

| Prototype | WordPress equivalent |
|---|---|
| `src/styles/tokens.css` | Compiled into `assets/dist/site.css`. The `@theme` block becomes plain `:root` custom properties — Tailwind is optional here |
| `src/styles/base.css` tier-1 motion | **Ports verbatim.** All of it is plain CSS with `@supports` guards — no build step, no JS |
| `CutText` | GSAP `SplitText` with `autoSplit: true, aria: 'auto'`, built inside a `gsap.context()` registered with the registry |
| `MeasureFrame` | A PHP section partial + one CSS file; the show/hide is `:hover`, `:focus-within` and an `IntersectionObserver` for touch |
| `Seam` | One inline SVG in the footer partial (outside the Barba container so it persists), driven by a small GSAP/Lenis velocity subscriber |
| `Loader` | The theme's own overlay, restyled. Keep the ≤900ms ceiling and the `document.fonts.ready` gate |
| `RouteCurtain` | Becomes the Barba transition itself — same gesture, same easing token |
| `PerformanceChart` | Plain SVG + CSS in a section partial; the `<table>` is the source of truth and the chart is generated from it |
| Fonts | Copy `public/fonts/*` into the child theme, `wp_enqueue_style` the face declarations, preload the two above-the-fold faces |
| `public/llms.txt` | Drop at web root; exclude from the transition router |

**Dequeue the theme's own smooth-scroll and animation libraries explicitly**
(`wp_dequeue_script`) or you will ship two scroll implementations fighting each
other. Enqueue the bundle with `['strategy' => 'defer', 'in_footer' => true]`
and a `jquery` dependency, registered *after* the theme's handle.

---

## 12. Pinned versions

Pin exactly and bundle locally. Do not load GSAP from a CDN on a client site
whose transitions break if the version bumps.

```
gsap        3.15.0     (all plugins now free — SplitText, MorphSVG, Flip, ScrollSmoother)
@barba/core 2.10.3
lenis       1.3.26
motion      13.1.1     (vanilla DOM API only — see below)
```

**Do not mount React inside WordPress to get Motion.** That means a second
render tree fighting Elementor's DOM and a second teardown problem across every
Barba swap. Use GSAP as the primary engine and Motion's framework-agnostic
`animate()` / `inView()` only for small spring interactions. Two engines is
defensible; two rendering paradigms is not.
