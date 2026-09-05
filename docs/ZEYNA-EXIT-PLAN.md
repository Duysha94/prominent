# Zeyna exit plan

What still depends on the parent theme, and what happens to each dependency.

Classification: **REMOVE** · **REPLACE** · **RETAIN (temporary)** ·
**INFRASTRUCTURE (keep)**.

## Where things stand

The child already owns `header.php`, `footer.php`, `404.php`, every page
template, all CSS and all JS. Zeyna contributes no visual language to the AK
site — its stylesheet is loaded and then almost entirely overridden.

That is also the problem: **97 KB of parent CSS is downloaded to be
overridden**, and 410 KB of parent JS loads on every page for two features.

## Dependency register

### PHP — nine `zeyna_*` calls

| Function | Uses | Class | Notes |
|---|---|---|---|
| `zeyna_barba()` | 23 | **INFRASTRUCTURE** | Prints Barba's `data-barba` attributes. Only reason soft navigation works. Cheap, stable |
| `zeyna_page_transitions()` | 2 | **REPLACE** | Prints `.page--transitions`. `barba.init()` only runs when that element exists, so it cannot simply be dropped — the child must print its own before removing this |
| `zeyna_header_classes()` | 2 | **REMOVE** | Returns Redux-derived classes the child's CSS ignores |
| `zeyna_popups()` | 2 | **REMOVE** | Renders nothing without Redux popups configured. Dead call |
| `zeyna_mouse_cursor()` | 2 | **REMOVE** | Custom cursor. Explicitly banned by the motion system |
| `zeyna_grid_layout_bg()` | 2 | **REMOVE** | Decorative grid overlay from a demo option |
| `zeyna_page_loader()` | 2 | **REMOVE** | Already unused — the child prints its own loader and forces `page_loader = false` |
| `zeyna_footer_template()` | 3 | **REMOVE** | Elementor footer lookup; forced to `default` |
| `zeyna_wp_nav_menu_objects` | 1 | **RETAIN (temporary)** | A parent filter, not a call. Calls ACF unguarded and fatals without it — the child's `get_field()` shim exists purely for this. Goes when the parent goes |

Every one is `function_exists`-guarded, so removing the parent breaks nothing
at the PHP level.

### Assets

| Asset | Size | Class | Notes |
|---|---|---|---|
| `zeyna/style.css` | 97 KB | **REPLACE** | Loaded only so the bridge can override it. The bridge maps 10 Redux variables to AK tokens; once the chrome is fully child-owned, both go |
| `js/scripts.js` | 132 KB | **REPLACE** | Contains the Barba init and transition timeline. The only part the site uses |
| `js/plugins.min.js` | 278 KB | **REMOVE** | Sliders, lightbox, isotope. None used |
| `js/lenis.min.js` | 16 KB | **RETAIN** | Smooth scroll. Scroll-driven animation must agree with it |
| `js/gsap.min.js` + plugins | 71 KB + | **RETAIN** | Split-text and ScrollTrigger. The child uses both |
| `js/three.min.js` | 490 KB | **REMOVE** | WebGL demos. Not loaded here; would load on a site with the WebGL Redux options set |
| `js/dotlottie-player.js` | 1.8 MB | **REMOVE** | Lottie player. Never used |
| `assets/fonts/fonts.css` | — | **REMOVE** | General Sans. The AK system uses Fraunces, Bricolage, Martian Mono |
| `css/plugins.css` | — | **REMOVE** | Styles for the unused plugins |

Measured on the current build: `plugins.min.js`, `scripts.js`, `gsap`,
`gsap-plugins`, `lenis`, `plugins.css` and `fonts.css` all load on the front
page. **410 KB of JS for Barba and GSAP.**

### CSS coupling

| Hook | Uses | Class |
|---|---|---|
| `--mainColor`, `--secondaryBackground`, … | 10 | **REPLACE** — the bridge exists only because Redux may leave them undefined |
| `.pe-col-6`, `.pe-items-right` | 7 | **REPLACE** — parent header markup the child restyles |
| `.layout--switched` | 6 | **RETAIN** — the per-page dark bridge into `data-theme` |
| `.page--transitions` | 6 | **REPLACE** — with the child's own overlay |
| `.first--load` | 4 | **RETAIN** — until the loader is fully child-owned |

### Plugins

| Plugin | Class | Notes |
|---|---|---|
| Pe Core | **RETAIN (temporary)** | Registers the `portfolio` CPT. The child already has a fallback; AK Brand Core takes this over |
| Redux | **RETAIN (temporary)** | Required by the parent. Goes with it |
| Elementor | **REMOVE** | The AK site uses no Elementor layouts. Only demo templates need it, and those are purged |
| ACF | **REMOVE** | Only the parent's menu filter calls it, and the shim covers that |
| OCDI | **REMOVE** | Demo importer. Its bookkeeping is already purged |
| Contact Form 7 | **INFRASTRUCTURE** | Unrelated to Zeyna. Stays |

## Sequence

Ordered so nothing breaks. Each step is shippable alone.

| Step | Do | Unblocks | Risk |
|---|---|---|---|
| **1** | ✅ **DONE in 1.4.2.** Dequeue `plugins.min.js`, `plugins.css`, `fonts.css`, `three`, `dotlottie` | 278 KB actual, ~2.5 MB potential | Low — **but see the two traps below** |
| **2** | ✅ **DONE in 1.4.2.** Remove the dead calls: `zeyna_popups`, `zeyna_mouse_cursor`, `zeyna_grid_layout_bg`, `zeyna_header_classes` | Header stops referencing parent behaviour | Low — all rendered nothing |
| **3** | Child prints its own `.page--transitions` and initialises Barba itself | Drops `scripts.js` (132 KB) and `zeyna_page_transitions()` | **Medium** — the transition timeline must be reimplemented. The motion system already specifies it |
| **4** | Own the loader end-to-end; drop `.first--load` coupling | Removes the last parent class from the boot path | Low |
| **5** | Stop enqueuing `zeyna/style.css`; delete the variable bridge | 97 KB, and the last visual coupling | **Medium** — needs a full-width sweep to confirm nothing depended on a parent rule |
| **6** | AK Brand Core registers the CPT and taxonomies | Pe Core no longer required | Medium — see `THEME-PLUGIN-ARCHITECTURE.md` |
| **7** | Convert to a standalone theme: `Template:` header removed, `get_template_directory()` replaced | Zeyna uninstalled | **High** — do last, behind a full regression pass |

### Two traps found while doing steps 1 and 2

This plan originally called step 1 "pure deletion of things that already do
nothing". That was too confident, and checking rather than assuming found two
reasons it is not:

1. **`scripts.js` does reference a library from `plugins.min.js`.** Exactly
   once — `new Swiper(...)` — reached only through
   `if (mainQuery.querySelector('.product--archive--gallery'))`, a WooCommerce
   product archive. So `plugins` is dequeued **only when WooCommerce is
   inactive**. With WooCommerce present the branch can fire and the file stays.
   A blanket dequeue would have thrown a ReferenceError on a shop archive.

2. **A dequeued handle comes back if anything still depends on it.** The
   child's `zeyna-parent` stylesheet declared `array( 'plugins' )` as its
   dependency, and WordPress re-adds a dequeued style when a queued style
   depends on it — so the dequeue would have silently done nothing. The
   dependency had to be dropped first.

**Result, measured on WordPress 7.1.0:** the front page now loads
`gsap`, `gsap-plugins`, `lenis` and `scripts` and nothing else from the
parent — `plugins.min.js`, `plugins.css` and the parent's font CSS are gone.
GSAP, SplitText and the child's own runtime all still initialise; the loader
still releases; no JS errors; no overflow.

*(Unrelated observation recorded so it is not mistaken for a regression:
`window.zeynaLenis` is undefined on a site without Redux, because the parent
initialises Lenis only behind a Redux-driven `body.smooth-scroll` class. That
was true before this change too.)*

**Steps 3 onward still need their replacements built first.**

## Why anything is retained

- **Barba + the transition element.** Soft navigation is part of the identity.
  Removing it before the replacement exists would make every navigation a full
  reload, which is a visible regression to fix a invisible one.
- **Lenis and GSAP.** Genuinely used. They would be re-added under the child's
  own handles anyway; the exit is about ownership, not about deleting
  libraries that do work.
- **Redux and Pe Core.** Required by the parent while the parent exists.
- **The ACF shim.** Three lines that prevent a fatal in a parent filter. It
  costs nothing and it stays until the parent goes.

## Standing rule

**Do not break working functionality to achieve ideological purity.** A
dependency is removed when the AK equivalent is built and verified, not when it
is planned. Every step above is gated on its replacement existing first — which
is why step 7 is last and not first.
