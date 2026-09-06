# Zeyna exit — the final record

**Status: complete.** Nothing in the parent theme renders, executes, or is
read at runtime. The theme is still installed as a child of Zeyna, for one
reason given in full at the end.

This document was a plan. It is now the record: what depended on the parent,
what each dependency was classified as, what replaced it, and what is
deliberately still here.

---

## 1. Final dependency inventory

Classified by what actually happens at runtime, not by what is enqueued. The
distinction matters because **every Zeyna template helper is gated on
`class_exists("Redux")`** — `zeyna_barba()`, `zeyna_page_transitions()`,
`zeyna_footer_template()`, `zeyna_page_loader()`. On a test install with no
Redux they return early and look harmless. On the studio's real site, with
Redux and Pe Core active, they run. **"Unused" can never be inferred from how
this build behaves on the test install**, and several items below were
classified from reading the parent's source, not from watching it.

### Already replaced (earlier releases)

| Dependency | Replaced by |
|---|---|
| `zeyna_popups()`, `zeyna_mouse_cursor()`, `zeyna_grid_layout_bg()`, `zeyna_header_classes()` | Removed. Each rendered nothing without Redux demo options, and the cursor is banned by the motion system |
| `js/plugins.min.js` (278 KB) — Swiper, lightbox, isotope | Nothing. No selector or call site in this theme |
| `css/plugins.css` (52 KB) | Nothing |
| `assets/fonts/fonts.css` — General Sans | Fraunces / Bricolage Grotesque / Martian Mono, self-hosted |
| `js/three.min.js` (491 KB) | Nothing. WebGL demos behind Redux options |
| `js/dotlottie-player.js` (1.8 MB) | Nothing |
| `zeyna_page_loader()` | `ak_page_loader()` in `inc/chrome.php`, with a CSS failsafe |
| Elementor footer template lookup (`zeyna_footer_template()`) | `footer.php`, drawing from `inc/studio.php` and the capability layer |

### Replaced in this release

| Dependency | Replaced by | Section |
|---|---|---|
| `js/barba.min.js` (31 KB) + `barba.init()` inside `js/scripts.js` | `assets/js/ak-nav.js` | [2](#2-the-transition-system) |
| `js/scripts.js` (133 KB) — the parent's entire runtime | Nothing is carried over; the child's own modules already existed | [8](#8-parent-javascript-exit) |
| `zeyna_page_transitions()` and `.page--transitions` | `.ak-transition`, printed by `header.php` | [2](#2-the-transition-system) |
| `js/gsap.min.js` + `js/gsap-plugins.min.js` (263 KB) | `assets/vendor/` — gsap, ScrollTrigger, SplitText only (123 KB) | [3](#3-motion-ownership) |
| `js/lenis.min.js` (16 KB) | Nothing. Native `scroll-behavior` | [4](#4-lenis) |
| `zeyna/style.css` (98 KB) and `style-rtl.css` (96 KB) | `assets/css/ak-wordpress.css` (9 KB) for what WordPress itself generates; the design system for everything else | [7](#7-parent-css-exit) |
| The Zeyna variable bridge — 10 custom properties over 9 selectors | Deleted. Nothing read them | [7](#7-parent-css-exit) |
| `zeyna_barba()` on `<main>`, `.pe-*` grid classes in the header | `data-ak-container`, `.ak-chrome__*` | [5](#5-header-and-navigation) |
| `add_filter('option_pe-redux', 'ak_force_redux_chrome')` | Deleted. Nothing on the frontend reads Redux | [9](#9-redux-exit) |
| The `page_layout` ACF read in `inc/theme-mode.php` | Deleted. Mode is the visitor's choice, else `prefers-color-scheme` | [9](#9-redux-exit) |
| `<span hidden class="layout--colors">` — a palette probe for `scripts.js` | Deleted | [5](#5-header-and-navigation) |
| `first--load` on `<html>` | `ak-booting` — same mechanism, this theme's name | [5](#5-header-and-navigation) |
| Parent frontend hooks: the `wp_footer@100` `js_editor` injection, `zeyna_body_classes`, `zeyna_wp_nav_menu_objects`, the `nav_menu_link_attributes@100` class assignment | `inc/zeyna-exit.php` detaches each on `wp` priority 1 | [8](#8-parent-javascript-exit) |
| Parent `taxonomy-project-categories.php` | A child template of the same name | [10](#10-templates-and-helpers) |
| Parent `woocommerce/` template set | `woocommerce_locate_template` filter refusing any parent path | [11](#11-woocommerce) |

### Still required — and why

| Item | Why |
|---|---|
| `Template: zeyna` in `style.css` | WordPress will not activate a child theme whose declared parent is missing. This is the *only* remaining tie, and it is an installation-time requirement, not a runtime one. See [15](#15-standalone-verdict) |
| GSAP, ScrollTrigger, SplitText | Genuinely used. Vendored from GreenSock's own npm package — a **library** dependency, not a parent-theme one. See [3](#3-motion-ownership) |
| Pe Core | Registers the legacy `portfolio` CPT and `project-categories` taxonomy. AK's own model (`ak_project`) does not need it; it stays only while legacy content does. `THEME-PLUGIN-ARCHITECTURE.md` |
| Redux | Required by Zeyna while Zeyna is installed. Nothing in this theme's frontend reads it |
| The `get_field()` shim | Three lines. It existed for `zeyna_wp_nav_menu_objects`, which is now detached, so it is no longer load-bearing — but ACF absence is a fatal, not a warning, and the shim costs nothing. Removed when the parent is |

### Retained temporarily

| Item | Until |
|---|---|
| `inc/deployment/` Redux **recognition** — `ak_capture_redux_templates()`, `ak_redux_raw()`, the demo-branding sweep | The studio's real site has been migrated and the legacy purge has run once. This is cleanup that knows how to read Zeyna data; it is not a rendering dependency. See [9](#9-redux-exit) |
| Child overrides of `archive-portfolio.php`, `single-portfolio.php`, `taxonomy-project-categories.php` | Legacy `portfolio` posts are migrated to `ak_project` |

### WooCommerce-only

| Item | Handling |
|---|---|
| `woo-styles`, `woo-rtl-styles`, `woocommerce-blocks` | Dequeued and deregistered unconditionally |
| `shop_archive_css`, `zeyna_woo_styles_footer`, `zeyna_ajax_add_to_cart_redirect` | Detached in `inc/zeyna-exit.php` |
| The parent's `woocommerce/` templates | Refused. See [11](#11-woocommerce) |

### Unused — verified, not assumed

`js/navigation.js` (registered by the parent but never enqueued by it),
`comments.php` and `sidebar.php` (only ever loaded by `comments_template()`
and `get_sidebar()`, neither of which this theme calls — asserted in the
regression suite rather than shipped as two unused overrides), and
`zeyna_pingback_header()` (WordPress's own pingback link, nothing to do with
the theme — deliberately left attached).

---

## 2. The transition system

Replaced first, because everything else depended on it.

**The rule, written at the top of `assets/js/ak-nav.js`:**

> Navigation must never depend on JavaScript. Every failure path ends in
> `location.assign()`. There is no path that ends in "nothing happened."

Every link on the site is a real `<a href>` to a real URL. `ak-nav.js` is
progressive enhancement over that, and it is the only script on the page with
no dependencies at all — fetch, History and DOMParser.

| Requirement | Behaviour |
|---|---|
| Internal navigation | Intercepted, fetched, container swapped, head adopted |
| Back / forward | `popstate` handled; content re-fetched and matched to the URL |
| Direct load / refresh | Ordinary server render. The runtime is not involved |
| External links | Refused: cross-origin, non-http(s), `rel=external`, `target`, `download` |
| Hash links | Refused when same-page; the browser's own anchor behaviour is better |
| Form submissions | Never intercepted |
| Admin bar | `/wp-admin/`, `/wp-login.php`, `/wp-json/` refused outright; the admin bar's **Edit** link is re-pointed on every swap |
| Loading state | `.ak-transition` sheet, coloured from `data-theme`, CSS-animated |
| Focus | Moved to the new container (`tabindex="-1"`, outline suppressed, `:focus-visible` preserved) |
| Title / metadata | `<title>`, `lang`, meta description, canonical and every `og:*` adopted |
| History | `pushState` on navigation, never on a refusal |
| Reduced motion | The sheet is `display: none`; the swap is instant |
| Failure | Any throw, any non-OK response, any asset mismatch → `location.assign()` |

Three refusal rules worth naming, because each was a bug first:

- **Capability probing by property name is not probing the API.** `'fetch' in
  window` is `true` even when reading `window.fetch` throws. The probe reads
  each API and catches.
- **A throw after `preventDefault()` strands the visitor.** The browser's own
  navigation has already been cancelled, so a synchronous throw leaves them on
  a page whose link did nothing. `go()` wraps everything in try/catch.
- **Asset parity.** If the incoming document needs a script the current one
  does not have, a swap would run new markup against old code. Missing
  stylesheets are injected; a missing script is a hard navigation.

---

## 3. Motion ownership

The exit separates two things that are easy to conflate:

- **A library dependency.** The theme uses GSAP. That is a dependency on
  GreenSock, and it is fine.
- **A parent-theme implementation dependency.** Zeyna's 133 KB `scripts.js`,
  its Redux-configured Barba setup, its Lenis instance, its 192 KB plugin
  bundle. That is a dependency on Zeyna, and it is what the exit removes.

Needing the first is not a reason to keep the second. Measured against what
this theme actually calls — `gsap.quickTo`, `gsap.set`, `gsap.timeline`,
`gsap.context`, `gsap.ticker`, `gsap.registerEase`, `ScrollTrigger.refresh`,
`SplitText` — three files are needed, not the parent's ten:

| | Parent | AK |
|---|---|---|
| gsap core | 71 KB | 72 KB (`assets/vendor/gsap.min.js`) |
| plugins | 192 KB (Flip, Observer, Draggable, MotionPath, ScrollTo, TextPlugin, EasePack, ScrollTrigger, SplitText) | 51 KB (ScrollTrigger + SplitText only) |
| **total** | **263 KB** | **123 KB** |

Vendored from npm `gsap@3.13.0`, **not** copied out of Zeyna: Zeyna bundles
its copies under the licence that came with Zeyna, and carrying those forward
into a theme that no longer depends on Zeyna would be redistributing someone
else's files. SplitText has been in GreenSock's public package since 3.13.0.
`assets/vendor/README.md` records this and flags the licence question as the
studio owner's to answer before launch.

Every use is guarded with `if (!window.gsap) return`. A library that fails to
load costs the animation and nothing else.

---

## 4. Lenis

**Removed, as a design decision — not because it came from Zeyna.**

Inheriting a library is not a reason to keep it, and it is not a reason to
drop it either. The question was asked on its own terms:

- This site is **read**, not flown through. Its long routes are the case study
  and the Journal post, both reading surfaces. Interpolated scrolling on a
  reading surface fights the reader's pacing: a trackpad flick overshoots, a
  find-in-page jump arrives late, the scrollbar stops matching the viewport.
- It takes ownership of scroll position away from the browser, so anchor
  links, back/forward scroll restoration and assistive-technology scrolling
  all have to be re-implemented on top of it — and none of that survives the
  script failing to load.
- The motion here is scroll-**triggered**, not scroll-**driven**. It needs to
  know the scroll position, not to control it.
- `scroll-behavior: smooth` gives the one thing Lenis genuinely provided —
  eased anchor jumps — natively, honours `prefers-reduced-motion`, and costs
  nothing when JavaScript is absent.

If the studio later wants inertial scrolling, it comes back as an AK decision
with those costs answered. The reasoning is written into `ak.css` where the
three Lenis rules used to be, so it cannot be silently reversed.

---

## 5. Header and navigation

`header.php` emits no parent markup and no parent-generated hidden element.

| Removed | Note |
|---|---|
| `zeyna_barba(true)` on `<body>` | Replaced by `data-ak-container` on `<main>` |
| `.pe-section header--default`, `.pe-wrapper pe-items-center`, `.pe-col-6` | `.ak-chrome`, `.ak-chrome__bar`, `.ak-chrome__side` |
| `class="ajax--first"` | Meaningless without the parent's runtime |
| `<span hidden class="layout--colors">` | A **probe**, not content: `scripts.js` read its computed styles to discover the Redux palette. An empty hidden element carrying a parent theme's class name is exactly what this exit was meant to remove |
| `first--load` | Renamed `ak-booting`. The class is printed by this theme's `header.php`, cleared by this theme's `ak.js`, and now carries this theme's name |
| `.ak-booting .site-header { opacity: 1 }` | A defence against Zeyna holding the header at `opacity: 0` during boot. That stylesheet is gone; the declared opacity is the initial `1` |

The menu is `wp_nav_menu()` with this theme's own fallback, this theme's own
mobile panel (scrim, focus trap, Escape, scroll lock), and — since the parent
filter that **assigned** `class="pe--styled--object text--anim--inner
menu--link"` to every link is detached — this theme's own classes survive.

The one heading defect worth recording: the site title was an `<h5>`, making
the first heading on every page an h5. It is a `<p>`.

---

## 6. Footer

`footer.php` had a branch: if `zeyna_footer_template()` returned anything, the
child rendered *that* instead — an Elementor template chosen through a Redux
key. On a site where the Zeyna demo had been imported, that key points at the
demo's own footer, and the studio's footer would silently have been replaced
by "ZEYNA CREATIVE" with a demo address and a demo email.

The child used to fight that by forcing the Redux value. **The branch is now
gone entirely, which is a stronger guarantee than a forced setting.**

Business data comes from the AK model:

- studio location, email, phone, social profiles — `inc/studio.php`
  (Customizer → AK Studio). Unset profiles print nothing; the earlier version
  rendered every network as an inert `href="#"`, putting five dead links on
  every page.
- the six movements — `inc/projects/capabilities.php`. This was a hard-coded
  four, so every page closed with a footer stating a practice two thirds the
  size of the real one, with photography, film, shows and PR nowhere in it.

---

## 7. Parent CSS exit

`zeyna/style.css` (98 KB) and `style-rtl.css` (96 KB) are dequeued **and
deregistered**. `style-rtl` mattered: it is a second full copy of the parent
stylesheet, dormant only because the site is LTR — adding a right-to-left
language would have reintroduced the whole thing.

**The part that is easy to get wrong.** A parent stylesheet is not only the
parent's visual language; it is also everything WordPress itself generates and
expects a theme to style. Removing it silently removed all of that.
`assets/css/ak-wordpress.css` (9 KB) reimplements exactly that layer and
nothing else:

reset and box-sizing · `.screen-reader-text` · `.alignleft` / `.alignright` /
`.aligncenter` / `.alignwide` / `.alignfull`, with a float reset at phone
width · `.wp-caption`, `.wp-caption-text`, `figcaption` · `.gallery` ·
post-content rhythm · form controls · pagination · the container focus rule.

Two defects the audit found in it, both invisible without checking:

- the post-content rules were scoped to `.entry-content`, and `single.php`
  wraps content in `.ak-prose` — **they matched nothing**. Both are covered.
- `.search-form .search-field { flex: 1 1 auto }` did nothing, because
  WordPress wraps the field in a `<label>`; the label is the flex item.

Also deleted: the **Zeyna variable bridge** — `--mainColor`,
`--secondaryBackground`, `--linesColor` and their `--custom*` twins, defined
across nine selectors so the parent chrome would render even when Redux had
never printed them. A grep of every stylesheet this theme ships returns zero
uses of any of the ten. A block of definitions with no consumer is not free:
it is the shape of a dependency, and it invites the next person to write
against it. The regression suite now asserts they resolve to nothing, and
asserts an AK token in the same breath so a broken stylesheet cannot make that
pass vacuously.

**One defect this caused, recorded because it is the exact failure mode the
exit had to guard against:** the menu's `display: flex` came from the parent.
The child set `gap` and `align-items`, both meaningless without it. Dropping
the parent stylesheet stacked six menu items vertically and made the header
201 px tall. Nothing about the child's CSS looked wrong.

---

## 8. Parent JavaScript exit

Every parent script handle is dequeued and deregistered: `scripts`, `barba`,
`lenis`, `zeyna-gsap`, `gsap-plugins`, `three`, `dotlottie`, `navigation`,
`plugins`, `wishlist`, `compare`.

**Deregistered as well as dequeued, deliberately.** WordPress re-adds a
dequeued handle the moment anything still declares it as a dependency — the
child's own `zeyna-parent` stylesheet once declared `array('plugins')`, so an
earlier dequeue silently undid itself.

Dequeuing is also not the whole story, because **not every parent frontend
behaviour is an enqueue**. `inc/zeyna-exit.php` detaches four things that fire
regardless of which templates this theme overrides:

1. **`wp_footer` @100** — echoes `$option['js_editor']` from `pe-redux` raw
   into a `<script>` tag. On a demo-imported site that is arbitrary JavaScript
   from the demo running on every page of the studio's website. **The single
   most important removal in the exit.** It is a closure, so it is matched by
   the file it was declared in.
2. **`zeyna_body_classes`** — adds `page--loader--active`,
   `page--transitions--active`, `loader__*`, `body--grained`, `smooth-scroll`,
   `show--footer` / `hide--footer`. They describe a runtime this theme no
   longer has, and `hide--footer` would hide a footer this theme renders. A
   `body_class` filter at 99 strips them; the callback is not removed wholesale
   because it also adds `hfeed` and `no-sidebar`.
3. **`zeyna_wp_nav_menu_objects`** — a *filter* that **echoes** a
   `<span class="sub--wrap--overlay">` and an Elementor template into the
   middle of the menu.
4. **`nav_menu_link_attributes` @100** — does not merge, it **assigns**
   `$atts['class']`, discarding whatever this theme put there on every link.

Plus the custom-header `wp-head-callback`, swapped to `__return_false` so
`zeyna_header_style` stops printing a `<style>` block into every page's head.

`inc/zeyna-exit.php` now returns early when `get_template_directory() ===
get_stylesheet_directory()`. Two of those removals identify a callback by the
file it was declared in; in a standalone theme those paths are the same, and
the module would have started unhooking *its own theme's* closures. A detacher
that can detach its own theme is a trap laid for whoever performs step 15.

---

## 9. Redux exit

**Frontend: nothing reads Redux. Verified by removal, not by inspection.**

`add_filter('option_pe-redux', 'ak_force_redux_chrome')` pinned eight keys —
`header_type` and `footer_template` back to `default`, six `transition_*` keys,
and `page_loader` off. Every one of those was a way of taming a parent that is
now gone: `header.php` and `footer.php` are this theme's own and read no Redux
key; the transition is `.ak-transition`; the loader is `ak_page_loader()`.

A filter that pins settings nothing reads is not neutral. It made every read of
a plugin-owned option report values that are not in the database — which is why
the deployment engine had to detach it for raw reads, and why clearing those
keys once made every deployment rewrite the option forever.

Removed with it: the `get_field('page_layout')` read in `inc/theme-mode.php`,
which let Zeyna's per-page "switched" layout set a page's default mode. It made
a page's opening appearance depend on parent configuration, it behaved
differently depending on which plugins were active, and AK has no per-page mode
design. The mode is the visitor's: a stored choice, else `prefers-color-scheme`.

**Legacy recognition deliberately remains, and is a different thing.**
`ak_capture_redux_templates()` records which `elementor_library` posts the demo
config pointed at — the only proof that a given post is the demo's header
rather than one the owner built — and the migration clears demo branding out of
`pe-redux` when it finds import evidence. That is cleanup that knows how to
*read* Zeyna data on a site that once ran the demo. Nothing on the frontend
consults it.

One correctness fix went with this. Those reads used
`remove_all_filters('option_pe-redux')` and restored only the child's own
callback — which, on a site where Redux is genuinely active, silently destroyed
**every other** filter on that option for the rest of the request. Removing the
child's filter would have left that hazard in place with nothing to restore at
all. `ak_redux_raw()` reads the options table directly instead: a true raw read,
with no restoration step and no side effect.

---

## 10. Templates and helpers

`archive-portfolio.php`, `single-portfolio.php` and
`taxonomy-project-categories.php` were the audit's real finding here, together
with `page.php`, `index.php` and `archive.php` — six routes that fell through
to the **parent**.

`taxonomy-project-categories.php` is the subtlest. The hierarchy asks for
`taxonomy-{taxonomy}.php` **before** `archive.php`, and checks the child and
then the parent for each name in turn. Overriding `archive-portfolio.php` and
`single-portfolio.php` without this one left the exit with a hole in exactly
the place nobody visits during testing — a URL that still rendered a Redux
read, a `zeyna_barba()` call, and an Elementor render if a Redux key named one.

Every parent template now has a child template that outranks it, except two,
and those are asserted rather than shipped: `comments.php` and `sidebar.php`
are only ever loaded by `comments_template()` and `get_sidebar()`, and this
theme calls neither. The regression suite checks that no file in the theme
calls either, so shipping two unused overrides would be worse than the
assertion.

**Zero live calls to any `zeyna_*` helper remain.** The suite scans every PHP
file, strips comment lines (the exit is documented in place, so the names
appear in prose throughout) and fails on any remaining call.

---

## 11. WooCommerce

The strict conditional, and a real defect it fixed.

`inc/enqueue.php` prints an admin notice saying that with WooCommerce active,
"WooCommerce pages will render with WooCommerce's own default appearance."
**That was not true.** WooCommerce resolves every template through
`wc_get_template()`, which looks in the child theme's `woocommerce/` folder,
then the **parent's**, then the plugin's. Zeyna ships a complete override set —
`archive-product.php`, `single-product.php`, `cart/`, `checkout/`,
`myaccount/`, `loop/`, `notices/` — so a shop would have rendered entirely
through parent templates calling removed helpers and expecting a stylesheet
that is no longer enqueued. Not WooCommerce's default appearance, and not the
studio's either.

A `woocommerce_locate_template` filter now rejects any path resolving inside
the parent theme directory in favour of WooCommerce's own default, so a shop is
unstyled-but-whole rather than broken. **The child's own `woocommerce/` folder
is untouched**: when AK builds shop templates they take precedence exactly as
they should — which is the point, since the studio should own the integration
rather than reintroduce the parent bundle.

The admin notice stays, and now tells the truth: a shop on this site is a
design decision that needs AK templates.

`plugins.min.js` and `scripts.js` are dequeued unconditionally. The parent's
only WooCommerce-gated caller is `if (mainQuery.querySelector('.product--archive--gallery'))
new Swiper(...)`, and reintroducing 400 KB of parent runtime to serve it would
undo the exit through the back door.

---

## 12. Failure behaviour

Tested deliberately, not assumed. `wordpress/tests/failure-modes-test.mjs`, 22
assertions.

| Failure | Behaviour |
|---|---|
| **All AK JavaScript disabled** | Every link works (real `<a href>`). A `<noscript>` block in `<head>` hides the loader, releases the scroll lock, and forces every reveal animation to its finished state |
| **`ak-nav.js` throws before init** | Nothing is intercepted; every click is an ordinary browser navigation |
| **A throw *after* interception** | `go()` catches and calls `location.assign()`. The visitor arrives |
| **GSAP unavailable** | Every module returns early. Content is visible; navigation works |
| **`prefers-reduced-motion`** | Transition sheet `display: none`; content visible without scrolling into it; navigation works |
| **Slow / blocked video** | Poster frame; the page is not held behind the loader; the body scrolls |
| **Missing project media** | The editorial register renders — title, spec, code. No empty frame, no plate |
| **Failed website capture** | **Nothing** renders. No frame, no message. "Capture pending / failed / unavailable" are admin-only strings, asserted absent from public HTML |

The JS-off case was a genuine bug found by this suite, not a check that passed
first time: the loader was a full-screen overlay with a 3-second CSS failsafe,
and for those three seconds it locked scrolling **and swallowed every click**.
Three seconds of a frozen page is not a degraded experience, and it is
invisible to anyone testing with JavaScript on.

---

## 13. Performance

Home page, WordPress 7.1.0, measured with `wordpress/tests/payload-audit.mjs`.

| | Before | After |
|---|---|---|
| Requests | 16 | **13** |
| JS + CSS | 1030.6 KB | **357.1 KB** (−65%) |
| Parent theme bytes | ~740 KB | **0** |
| WordPress core JS | 354.8 KB | **44.9 KB** |

Core JS fell by 310 KB because jQuery was on the page **only** for Zeyna.

Corroborating the removal from the parent's own files on disk: `style.css`
97.6 KB · `style-rtl.css` 96.4 KB · `plugins.css` 51.8 KB · `scripts.js`
132.9 KB · `plugins.min.js` 278.4 KB · `gsap-plugins.min.js` 192.1 KB ·
`gsap.min.js` 71.0 KB · `barba.min.js` 31.0 KB · `lenis.min.js` 16.3 KB ·
`three.min.js` 490.8 KB · `navigation.js` 2.9 KB. Against that, this theme
adds 123 KB of vendored GSAP and 9 KB of `ak-wordpress.css`.

Across seven routes: **parent theme bytes 0.0 KB.**

---

## 14. Regression suite

| Suite | Result |
|---|---|
| Project model (`project-model-test.php`) | 89 / 89 |
| Deployment engine (`deployment-test.php`) | 27 / 27 |
| Content consistency (`content-consistency-test.php`) | 73 / 73 |
| Navigation panel (`wp-nav-panel-test.mjs`) | 129 / 129 |
| Failure modes (`failure-modes-test.mjs`) | 22 / 22 |
| **Zeyna exit (`zeyna-exit-test.mjs`)** | **99 / 99** |
| Accessibility (`wp-accessibility-audit.mjs`) | 0 findings |
| Positioning (`positioning-audit.mjs`) | 0 findings |
| Horizontal overflow (`width-sweep.mjs`) | none — 11 routes × 14 widths |

The exit suite asserts, per route: no parent theme asset loads; no Zeyna
transition markup; no parent menu or grid classes; `data-ak-container` present;
no parent boot vocabulary. Then, once: no `barba`, `zeynaLenis`, `Lenis`,
`jQuery`, `THREE` or any `zeyna*` global; GSAP and both AK modules present; no
parent body classes; no parent header `<style>` block; the variable bridge
resolves to nothing while AK tokens do resolve; the mode script reads no parent
configuration; and back/forward across a soft navigation lands on content
matching the URL.

Six of its assertions are **source** assertions, because no browser check can
reach them on this install — WooCommerce and Pe Core are not here, and every
parent helper is Redux-gated. "It renders fine here" has been the wrong answer
to this question at every stage of the build.

---

## 15. Standalone verdict

**B — a small parent dependency still exists, and it is exactly one:**

```
style.css:  Template: zeyna
```

WordPress will not activate a child theme whose declared parent is not
installed. That is the whole of it. No parent CSS, JavaScript, template,
helper, hook or configuration key is used at runtime; the only file that still
names the parent is `inc/zeyna-exit.php`, whose entire job is detaching it, and
which now no-ops when there is no parent to detach.

**Removing the parent is not done here, deliberately.** Converting to a
standalone theme is a one-line change in `style.css` plus an uninstall, and it
carries two risks this session cannot discharge:

1. **The studio's real site is not this test install.** Every Zeyna helper is
   Redux-gated; the real site has Redux and Pe Core active and may have had the
   demo imported. The exit was written against the parent's *source* for
   exactly that reason, but the first run on the real site is the first time
   any of it executes in that configuration. Removing the parent at the same
   time would remove the ability to compare.
2. **Pe Core still registers the legacy `portfolio` CPT and its taxonomy.**
   Three child templates exist to render that content in AK's language. Until
   legacy content is migrated to `ak_project`, uninstalling Zeyna and its
   plugin stack removes content routes, not just a theme.

**The order to finish it in:**

1. Deploy this release to the real site. Confirm the exit suite's runtime
   assertions there, with Redux and Pe Core active.
2. Run the legacy purge once; confirm `ak_capture_redux_templates()` recorded
   the demo's template IDs before anything was cleared.
3. Migrate legacy `portfolio` posts to `ak_project`.
4. Drop `Template: zeyna`, move the theme's own files to a standalone
   directory, and re-run the full suite — the source assertions in
   `zeyna-exit-test.mjs` will need their parent-template list retired at that
   point, which is the correct moment to do it.
5. Delete `inc/zeyna-exit.php`, uninstall Zeyna, Redux and Pe Core.

---

## Standing rule

**Do not break working functionality to achieve ideological purity.** A
dependency is removed when the AK equivalent is built and verified, not when it
is planned. Every step above was gated on its replacement existing first —
which is why the last one is still the last one.
