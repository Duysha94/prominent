# Building this on WordPress + Zeyna

> ## ⚠️ Read this first
>
> **The Zeyna theme could not be inspected while writing this.**
> `themeforest.net` is blocked by this environment's egress proxy (`403 CONNECT
> tunnel failed`), and the theme does not appear in any public code index.
>
> So: there is **no verified demo URL, no confirmed page builder, no confirmed
> transition library** for Zeyna specifically. Everything below marked
> *(inference)* is drawn from how 2025-era ThemeForest "multipurpose creative"
> themes are built as a class, and from reading the source of two shipping
> Barba-based WordPress themes. It is a strong prior, not a fact.
>
> **Run [the day-one audit](#0-day-one-audit) before quoting or scoping.** One
> of its answers — whether transitions are Barba, swup, or a hand-rolled
> `fetch()` — is the difference between two days of work and two weeks.

---

## 0. Day-one audit

Unzip the theme package and run four commands. Twenty minutes, and it replaces
every inference in this document.

```bash
# 1. What drives the page transitions, and what is the wrapper contract?
grep -rl "data-barba\|barba.init\|swup\|@view-transition" .

# 2. Are header/footer Elementor theme-builder locations, or PHP templates?
grep -rn "elementor_theme_do_location\|register_locations" .

# 3. What does the dark-mode toggle actually set? (class? data-attribute?
#    localStorage key? does it run before first paint?)
grep -rn "dark\|theme-mode\|data-theme" --include=*.js --include=*.php . | head -40

# 4. What does the theme enqueue that we will need to dequeue?
grep -rn "wp_enqueue_script\|wp_enqueue_style" --include=*.php .
```

Record the answers in this file before writing any template.

---

## 1. What "we keep only the shell" actually means

*(inference — confirm with audit step 2)*

Keeping "the header, footer, menu, page transitions and light/dark mode" almost
certainly does **not** mean keeping `header.php` and `footer.php`. In this
theme class it means keeping:

| Kept | Why it cannot be removed |
|---|---|
| The theme + its companion/core plugin | The theme-builder templates and the mode toggle depend on it |
| Elementor + **Elementor Pro** | Header/footer are Pro theme-builder *locations* |
| Two Elementor templates (Header, Footer) | Unedited — this is the shell |
| The WP nav menu + its mobile panel markup | Rendered by the header template |
| The transition engine + its wrapper contract | See §2 |
| The mode toggle + its CSS custom properties | Our bespoke pages consume its variables |

**Consequence for the quote:** Elementor Pro licensing and its update path stay
in scope permanently, even though not one page layout uses Elementor.

---

## 2. The wrapper contract — the thing you must not break

*(inference — confirm with audit step 1)*

Expect Barba.js v2 driving GSAP timelines. The contract is two attributes:

```html
<body data-barba="wrapper">
  <div data-barba="container" data-barba-namespace="work">
    <!-- page content -->
  </div>
</body>
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

## 5. Barba config — ship these values, not the defaults

Barba's defaults are wrong for WordPress in four specific ways.

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

## 6. Dark mode across a soft navigation

Barba replaces body classes wholesale, so **the theme mode must be explicitly
preserved or it flashes back to light on every navigation.**

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
