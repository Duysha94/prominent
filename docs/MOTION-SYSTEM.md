# Motion system

Motion is part of the identity, so it gets roles before it gets timelines. The
test for any animation: **remove it — does the page lose meaning, or just
lose movement?** If the latter, it does not ship.

## Principles

1. **Motion carries structure.** A reveal says "this is a new section". A
   transition says "you moved". Movement that says nothing is noise.
2. **One idea per screen.** If two things animate at once, one of them is
   decoration.
3. **The reader is never blocked.** No scroll hijacking, no waiting for an
   animation before content is readable, no motion that delays the LCP element.
4. **Cheapest tool that works.** CSS first, Web Animations second, GSAP only
   where neither can do it.
5. **Reduced motion is a real mode**, not a switch that disables everything —
   see below.

## Tooling ladder

| Tier | Tool | Used for |
|---|---|---|
| 1 | CSS transitions / `@keyframes` | Hover, focus, colour, small entrances |
| 2 | CSS scroll-driven animation — `animation-timeline: view()` | Section reveals, the scroll rail, media scale. Behind `@supports`; without it, content is simply visible |
| 3 | Web Animations API | Menu choreography, staggered lists |
| 4 | **GSAP** | Split-text only, and pinning where a section must hold. Nothing else earns 71 KB |

Today's cost: GSAP + plugins are loaded by the parent theme on every page.
`ZEYNA-EXIT-PLAN.md` covers reducing that.

## The five roles

### 1. Navigation motion

| Event | Behaviour | Duration | Easing |
|---|---|---|---|
| Route change | A single sheet of the current mode's paper wipes up, content swaps behind it, wipes away | 1250 ms total | `expo.inOut` |
| Menu open (phone) | Panel slides from the right; links arrive in reading order, 45 ms apart | 320 ms panel, 380 ms links | `--ease-cut` |
| Menu close | Panel leaves, no link stagger — leaving should be faster than arriving | 240 ms | `--ease-cut` |
| First load | Loader: thread draws, monogram sets, released on `DOMContentLoaded` with a 700 ms floor and a 2500 ms ceiling | ≤ 2500 ms | — |

The transition sheet is **blank**. No logo, no caption, no percentage. The
parent theme's version put a logo and a caption in it, which turns a 1.2-second
transition into a 1.2-second advertisement.

### 2. Typography motion

Reserved for **one headline per page** — the page's thesis. Applying it to
every heading makes none of them the thesis.

| | |
|---|---|
| Mechanism | Split by line; each line cut along its midline into two halves that arrive from opposite directions and close |
| Timing | 1200 ms per line, 28 ms stagger |
| Trigger | `top 86%`, once |
| Teardown | Clip released and the duplicate half removed on completion — display type at 0.9 leading puts descenders outside the line box, so clipping exists only while something moves |
| Accessibility | `aria-label` on the host, `aria-hidden` on the split lines, duplicate half `aria-hidden` at creation |

Everything else: a 12 px rise with opacity, 700 ms, no stagger.

### 3. Media motion

| Element | Behaviour |
|---|---|
| Showreel | Grows from a measured frame to full bleed on scroll, driven by `view()` on the section. Without scroll-driven support it is simply one full screen of film |
| Case hero | Scales 1.06 → 1.00 over the first viewport. Never the reverse — starting small and growing reads as a loading state |
| Gallery images | Fade and rise 16 px, 60 ms apart, once |
| Video | Autoplay muted inline, `preload="metadata"`, poster always set. Paused when off-screen via `IntersectionObserver`; never plays behind a modal or an open menu |

**Media never animates before it has loaded.** A poster is shown, then the
video fades in — a black rectangle that scales is worse than a still that does
not.

### 4. Scroll behaviour

What reacts to scroll:

- The seam — the orange thread down the left edge. Fill = document progress.
- Section reveals, once each.
- The showreel expansion.
- The case hero scale.

What deliberately does **not**:

- Body copy. It is there to be read.
- Navigation. A header that hides and reappears costs more than it saves at
  this page length.
- The spec header on a case study. It is reference data; it stays put.
- Anything in the footer.

**There is no smooth-scroll library.** Lenis came from the parent and was not
re-adopted on the way out — a decision taken on its own terms, not a
consequence of the exit. This site is read, not flown through: its long routes
are the case study and the Journal post, and interpolated scrolling on a
reading surface fights the reader's pacing while taking ownership of scroll
position away from the browser (anchors, back/forward restoration, assistive
scrolling all then have to be rebuilt, and none of it survives the script
failing).

The motion here is scroll-**triggered**, not scroll-**driven**: it needs to
know the scroll position, not to control it, so nothing above changes.
`scroll-behavior: smooth` provides the eased anchor jumps natively, honours
`prefers-reduced-motion`, and costs nothing with JavaScript off. The full
reasoning is in `ZEYNA-EXIT-PLAN.md` §4 and in `ak.css` where the three Lenis
rules used to be.

**Libraries: GSAP, ScrollTrigger, SplitText — 123 KB, vendored from
GreenSock's own npm package** (`assets/vendor/`), not carried over from the
parent's bundle. Needing GSAP is a library dependency; the parent's 133 KB
runtime and 192 KB plugin set was a parent-theme dependency, and those are
different things. Every use is guarded with `if (!window.gsap) return`.

### 5. Micro-interaction

| Element | Response | Duration |
|---|---|---|
| Index row hover | Row shifts 8 px right on a spring; folio turns accent | 420 ms spring |
| Link hover | Colour to accent, 1 px underline draws from the left | 250 ms |
| Button hover | Border tightens, no scale. Scaling a button on a text-led site reads as a different design language | 250 ms |
| Metadata hover | None. Data does not respond to the pointer |
| Focus-visible | 2 px accent outline, 3 px offset. Never suppressed, never animated |
| Form field focus | Border to accent, label rises. 200 ms |

Hover is never the only route to information. Every project's code, client,
year and disciplines are visible at rest.

## Reduced motion

`prefers-reduced-motion: reduce` is a **different design**, not a disabled one:

| Role | Reduced behaviour |
|---|---|
| Route change | Instant swap, 120 ms cross-fade |
| Typography | No split. The headline is simply present |
| Media | No scale, no parallax. Video still plays — it is content — but never autoplays |
| Scroll | No reveals; everything visible at rest |
| Micro | Colour changes only |
| Loader | Minimum duration drops to 0 |

Implemented at the token level (`--duration-*` → `1ms`) *and* by branch in
`ak.js` (`reduced.matches` gates registration), so a component cannot animate
by forgetting a media query.

## What is banned

- Animation on every element.
- Decorative GSAP timelines — GSAP is for split-text and pinning.
- Scroll hijacking, scroll-jacked carousels, forced section snapping.
- A custom cursor that replaces the system cursor over text or controls.
- Parallax on more than one element per screen.
- Infinite motion outside the marquee band, which is the one deliberate
  exception because it is a typographic device, not an effect.
- Entrance animation on anything above the fold at first paint. It delays the
  LCP and it makes a fast site feel slow.
