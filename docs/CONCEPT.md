# AK Brand Development Studio — website concept

**Status:** design concept + working prototype. Content is placeholder (see
[Open questions](#open-questions)).
**Accent:** `#f37021` · **Language:** English · **Production target:** WordPress
on the Zeyna theme shell.

---

## 1. The problem this design solves

AK sells three things that normally come from three different companies:

1. fashion & brand advisory,
2. website and e-commerce development,
3. Meta and Google Ads.

Presented as a service list, that reads as a bag of unrelated skills — and the
default agency layout (a three-column "Branding / Web / Ads" grid with line
icons) actively makes it worse. It says nothing, and it invites the reader to
buy one item from the menu.

**The concept's whole job is to make those three read as one practice.** Every
structural decision below is downstream of that.

---

## 2. The idea: the tech pack

In fashion, a **tech pack** is the document that turns a creative idea into a
manufacturable, sellable product: measurements, callouts, materials,
construction notes. It is the artefact where craft and commerce meet, and it is
exactly what AK does for a brand.

So the site *is* a tech pack. That gives us one visual language — annotation,
measurement, specification — that applies equally to a silhouette, a checkout
funnel and an ad account. The claim "we measure everything, including the
things agencies usually hand-wave" stops being a sentence in an About page and
becomes the interface.

Four devices carry it:

### The Measure Frame
Hover, focus, or simply scroll to any significant block and thin annotation
lines snap around it with real figures attached — `ROAS 4.8×`, `LCP 0.9s`,
`SKU 900 → 90`. It is drawn like a garment spec drawing and populated with
business numbers. This single mechanism is what unifies the three lanes: the
same measuring eye applied to a coat and to a conversion rate.
→ `src/components/primitives/MeasureFrame.tsx`

### The Seam
One orange thread runs the full length of every page. It is not decoration: it
is scroll position made physical. Scroll fast and it bows and slackens the way
real thread does when pulled through cloth; stop, and an underdamped spring
lets it wobble taut. The knot riding it is where you are in the document. The
page transition is the same thread swelling sideways to cover the viewport —
**the brand mark and the transition are the same object.**
→ `src/components/shell/Seam.tsx`, `src/components/shell/PageTransition.tsx`

### Cut text
Headlines are not revealed word-by-word. Each line is split along its
horizontal midline into two halves that start displaced in opposite directions
and close together, the way cut cloth falls back into place, while a thin
accent blade sweeps the cut just ahead of the close.
→ `src/components/primitives/CutText.tsx`

### Two designed rooms
Light and dark are **ATELIER** (the daylit workroom) and **RUNWAY** (the show)
— named on the switch, not symbolised with a sun and a moon. They differ in
*material*, not only in hex values: ATELIER carries a low-frequency paper
fibre, RUNWAY a high-frequency film grain. Most dual-mode sites swap colours;
swapping substrate is what makes both feel authored.

---

## 3. What we deliberately did **not** do

The brief asked for something not already on the market, so the patterns we
rejected matter as much as the ones we chose. Each of these is now
template-tier:

| Rejected | Why | What we did instead |
|---|---|---|
| Full-bleed autoplay video hero | The most-repeated agency opening of 2024–26; it fights every headline placed on it and makes the largest element the slowest | The video became a **swatch** — a narrow measured band of material. LCP is the headline (text), not a video |
| 000→100 percentage preloader | Pure ritual, taxes LCP, ships in every creative WordPress theme | Kept the *form*, changed the meaning and the cost: a **tape measure**, bounded by real font-readiness with a hard 900ms ceiling |
| Word-by-word / masked-line SplitText reveal | Free in GSAP since April 2025, therefore everywhere | Cut text (above), plus scroll-driven variable-font weight |
| Sticky list with a cursor-trailing thumbnail | Available as a free Framer component; the visual equivalent of a stock photo | Rows expand **in place**, so it works identically under keyboard and touch |
| Bento grid | The 2026 default, not a choice | A print editorial grid on CSS subgrid |
| Horizontal-scroll pinned case studies | Breaks reflow at 400% zoom, fights the scrollbar | A publication **contents page** |
| Client-logo / service-word marquee | Communicates nothing, continuous compositor cost | The marquee carries **live studio figures** — spend managed, campaigns live, builds shipping |
| Magnetic pull on every CTA | A few clicks from any no-code builder | Exactly one magnetic control per region, and the expressive part is a tick that *measures* the button |
| Instrument Serif / PP Editorial New | The 2026 shorthand for "no type budget" | Fraunces (below) |
| Inter / Geist | Appear in ~90% of design output; read as undesigned | Bricolage Grotesque (below) |
| A cold "data" accent colour | Would quietly reintroduce the greyscale-plus-one-accent look every AI-era product site shares | **Three colours, held hard.** Data is separated by typeface, not hue |
| AI-generated or stock imagery | An active brand liability for a creative studio in 2026 | Duotone halftone plates, drawn procedurally (below) |

---

## 4. Typography

Three variable families, self-hosted, subset, axis-pruned. **737KB of
Google-served files became 187KB** with every animated axis intact.

| Role | Face | Axes kept | Why this one |
|---|---|---|---|
| Display | **Fraunces** italic | `opsz 9–144`, `wght 100–900` | Real optical-size and WONK axes that Instrument Serif does not have; the WONK setting gives the italic its singular slightly-wrong leg. `SOFT`/`WONK` pinned at 0/1 |
| Text / UI | **Bricolage Grotesque** | `opsz 12–96`, `wght 200–800` | An irregular grotesque with genuine letterform character, not the neo-grotesk default. `wdth` pinned |
| Data | **Martian Mono** | `wght 100–800`, `wdth 75–112.5` | One of very few monos with *two* variable axes — which is what lets the figures widen as they scroll |

`text-box: trim-both cap alphabetic` is applied to all display type so a 10vw
headline's cap height sits exactly on the rule beside it instead of floating on
invisible half-leading. Metric-matched fallback faces (`size-adjust`,
`ascent-override`) keep the webfont swap from shifting layout.

---

## 5. Colour

Everything is derived from `#f37021` = `oklch(0.6924 0.1812 46.21)`, with the
full 50–950 ramp chroma-clamped to sRGB so it never clips.

**The measured facts that shaped the system:**

Measured against this site's own surfaces — paper `#f9f6f2` and warm ink
`#100d0a` — not against generic white and black:

| Pair | Contrast | Verdict |
|---|---|---|
| brand-500 on paper | **2.73:1** | **Fails WCAG 1.4.11 non-text (3:1)** — so the brand orange is never a mark, rule or focus ring on the light theme |
| brand-600 on paper | **3.49:1** | Passes 1.4.11 → accent **marks** in light mode |
| brand-700 on paper | **4.80:1** | Passes AA text → accent **text** in light mode |
| brand-500 on ink | **6.60:1** | Passes AA → accent marks and text on dark |
| brand-400 on ink | **8.43:1** | Small accent labels on dark (see below) |
| Ink on brand-500 | **6.60:1** | Passes AA → orange fills take **ink** type |

Two of those rows are the ones almost everyone gets wrong. **White on orange
measures 2.94:1 and fails three thresholds at once** — AA text, AA large text,
and non-text contrast — and it is what nearly every orange brand ships.

And brand-500 as *small* text on dark technically passes WCAG at 6.60:1, but
APCA scores it Lc −46, which wants roughly 39px at weight 400. WCAG 2.x
systematically over-rewards saturated mid-luminance colours on dark grounds,
and small orange labels genuinely glare. So small accent text on dark steps up
to brand-400.

The ramp also **drifts in hue** — tints toward yellow-cream (H 62), shades
toward rust-red (H 27) — because a ramp holding one hue at every step is the
signature of an auto-generated palette.

**On a P3 display the accent gets hotter.** The brand orange is chroma-limited
by sRGB, not by choice: at L 0.692 / H 46.21, sRGB tops out at C 0.194 while
Display-P3 reaches 0.221, so roughly 20% of the colour is being left on the
table on any modern laptop or phone. Inside `@media (color-gamut: p3)` the
accent steps up to C 0.217 with **lightness and hue held to four decimals** —
which is what makes it safe, since every contrast figure above is a function of
lightness alone. Guarded with both `@supports` and the gamut media query,
because a wide-gamut-capable browser on an sRGB monitor will otherwise accept
the declaration and clip it, ending up flatter than the value it replaced.

Which produces the rule the whole palette hangs on:

> **The brand orange is always a surface, never light-mode ink.**
> As a fill it takes near-black type. As text or as a mark on paper it steps
> down to 700 or 600 respectively; only on the dark theme does the true brand
> value carry type.

Three colours, held hard: **warm ink**, **warm paper**, **`#f37021`**. The
neutrals sit at hue 60 with chroma 0.005–0.008 — warm enough to belong with the
orange, cold enough never to read as brown. The ground is `#100d0a`, never
`#000` (which halates on OLED and dates a build immediately).

**One full-bleed accent section per page, no more.** Running a whole section at
100% orange with ink type is confident, measurably *more* accessible than the
usual white-on-orange, and almost unoccupied. Spent twice, it stops being an
event.

---

## 6. Motion

Full spec in **[MOTION.md](./MOTION.md)**. The architecture in one line:

> **Tier 1 is CSS, tier 2 is GSAP, and tier 2 is not allowed in the entry
> chunk.**

- **Tier 1 — CSS scroll-driven animation** (`animation-timeline: view()`).
  Every reveal, the drawn rules, the lane diagrams, and the signature move:
  headline weight and optical size as a function of scroll position. Runs on
  the compositor, costs 0KB of JavaScript, cannot contribute to INP. All of it
  wrapped in `@supports` so the finished state renders where unsupported.
- **Tier 2 — GSAP**, lazily loaded, only for what the CSS spec deliberately
  excludes: split-text choreography and programmatic scrub.

Springs are sampled from real physics into CSS `linear()` easings **and**
exported as Motion configs from the same source, so a CSS transition and a JS
animation on the same element are physically identical.

Reduced motion is a **designed mode, not a kill switch**: travel is replaced by
instant state change, marquees stop, the loader does not run, Lenis is disabled
entirely — but no information ever disappears.

---

## 7. Structure

```
/                 Hero (the swatch) → the argument (full-bleed accent)
                  → work contents → the readout → journal
/work             Contents page, filterable by lane
/work/:slug       Case study — branches by the lane that LED the work
/services         Three lanes, each stating what it HANDS OVER to the next
/studio           How the studio works, and what it refuses
/journal          Notes
/contact          The Brief — a measuring instrument, not a form
```

Two structural decisions do the heavy lifting:

**Services states handovers, not features.** Each lane declares what it leaves
the next one — advisory leaves a written position the build can lay out; the
build leaves a store that is fast and tracked enough to receive paid traffic;
media feeds what it learns back into advisory. The page is a chain, not a menu,
so the argument for buying all three is built into the layout rather than
asserted in a paragraph.

**Case studies branch by lane.** A brand identity, a shipped store and a media
account are not the same kind of evidence, and forcing them through one
template is what makes a mixed portfolio read as three departments.

- `advise` → **The Spread.** The position set at full display size, with the
  three rejected lines struck through beneath it. Showing the rejects is the
  point: any studio can present the line it arrived at.
- `build` → **The Walkthrough.** The store at real device proportions, with
  before/after field data as a table.
- `grow` → **The Performance Surface.** The actual ad creative at the ratio it
  ran at with CTR attached, next to eight weeks of spend and blended ROAS.

That last one is the site's one deliberately strange page. Paid advertising is
the service line agencies hide behind a testimonial, which is precisely why
art-directing it is unoccupied ground — and it is the work a fashion founder is
most sceptical about.

**The Brief** replaces "tell us about your project" with four questions a
founder can answer without thinking, and returns a spec sheet that recalculates
live: which lanes they need (including ones they did not ask for, and why),
a realistic shape of engagement, and what we would need from them to start. The
visitor leaves with something even if they never send it.

---

## 8. Charts

Spend and ROAS are different scales, so they are **two charts sharing an x
axis**, never a dual-axis plot. A dual axis lets you place the two series'
crossings anywhere by choosing the scales, which is indefensible on a page
whose argument is that the numbers are honest. One series per panel means no
legend is needed; only the final value is directly labelled; the grid is
recessive; and the accessible representation is a real `<table>` that is always
in the DOM.

---

## 9. Performance & accessibility contract

| Budget | Target | Current |
|---|---|---|
| Initial JS (gzip) | < 150KB | **~140KB** |
| Fonts | < 200KB | **187KB** (3 variable families) |
| CSS (gzip) | < 15KB | **~10KB** |
| LCP element | Text, never media | Hero headline |

- **hls.js was removed.** The brief specified an HLS hero stream; measured in
  this build, hls.js cost **176KB gzip** — 3.5x the GSAP chunk and nearly 2x
  the entire React app — to adaptively stream a silent loop that has one
  bitrate and never adapts. The hero is now a plain `<video>` with an AV1
  source and an H.264 fallback, `preload="none"`, started after `load` and
  only when it is on screen, not under Save-Data, and not on a 2G connection.
- GSAP (51KB gz) is dynamically imported and not in the entry chunk. Routes are
  split; only Home ships eagerly.
- Exactly **one** font is preloaded — the face the H1 is set in. Preloading
  several at highest priority ahead of the CSS is a common own goal.
- **Known cost, stated rather than hidden: this ships two animation engines.**
  GSAP (50.8KB) + Motion (47.1KB) = ~98KB gzip, and they overlap. GSAP is here
  for SplitText and ScrollTrigger; Motion is here because the React surface is
  declarative (`AnimatePresence`, `useScroll`, springs). The honest options are
  (a) `LazyMotion` + `m` with the `domAnimation` feature set, worth roughly
  19KB, or (b) consolidating onto GSAP outright. Both are real rearchitecture
  decisions rather than a tidy-up, so they belong to the team that owns the
  build, not to a late unverifiable refactor.
- Reveals animate `transform`/`opacity` only — every revealed element occupies
  its final box from first paint, so no reveal can cause CLS.
- Marquees carry a pause control that is **always present, not revealed on
  hover** — hover does not exist on touch, so a hover-revealed control fails
  WCAG 2.2.2 (Level A) for most of the traffic. The QA harness asserts this.
- **Lenis's own `respectReducedMotion` is not trusted.** In the shipped 1.3.26
  source it only makes programmatic `scrollTo()` instant; wheel hijacking
  continues at full lerp. The constructor is guarded instead.
- **A focus bridge is built by hand.** Lenis ships no keyboard or focus
  handling, so tabbing to anything below the fold makes the browser scroll it
  into view while Lenis fights back, and the page snaps. A `focusin` listener
  hands the scroll to Lenis instead.
- `allowNestedScroll` is deliberately **off**: it walks the composed path
  calling `getComputedStyle` and reading `scrollHeight` on every ancestor — a
  forced synchronous layout inside a non-passive wheel listener. Scrollable
  panels opt out with `data-lenis-prevent` instead, which is a plain attribute
  check.
- No `will-change` in any static selector. It permanently promotes every match
  to its own compositor layer; GSAP's `force3D: 'auto'` promotes for the tween
  and un-promotes after.
- Every number reachable by hover is also reachable by keyboard focus, and
  appears automatically on touch. **Nothing is hover-only.**
- The theme is resolved synchronously in `<head>` before first paint, so a dark
  visitor never gets a white flash.
- `prefers-reduced-motion`, `prefers-reduced-transparency` and `forced-colors`
  are all honoured.

**Verified in this repo** — `npm run qa` walks all 10 routes at 320 / 390 /
768 / 1440 in both themes (80 combinations) and asserts: no horizontal
overflow, no console errors, exactly one `<h1>` per page, a `#main` skip
target, a non-trivial `<title>`, and no headline left hidden after scrolling.
Plus a reduced-motion pass (no loader, content at full opacity) and a keyboard
pass (skip link is the first tab stop). Typecheck, lint and production build
pass.

Two things that harness had to learn, both of which are easy to get wrong:

- **It must scroll with real wheel events.** Lenis overrides
  `window.scrollTo`, so a synthetic `scrollTo` loop never drives ScrollTrigger
  — every scroll-revealed heading reads as permanently hidden and you chase a
  bug that is not there.
- **A reveal that starts at `opacity: 0` must not be able to strand content.**
  `CutText` plays regardless after 2.5s if its trigger never fired, because a
  tool that repositions the page without dispatching scroll events would
  otherwise leave headings blank.

**Not yet verified:** real-device Core Web Vitals, screen-reader passes with
NVDA/VoiceOver, and 400% zoom reflow. Those need a staging URL.

One thing to watch once there is one: with `web-vitals` v6, **soft navigations
are measured**, so the route curtain is now visible to field data where it
previously was not. The budget is that the incoming page's LCP element paints
and the curtain lifts inside 1.2s — the current sequence is 480ms cover +
480ms reveal with the swap at 480ms, which fits, but it is the first thing to
re-measure if transitions get more elaborate.

---

## 10. Open questions

1. **The agency brief never arrived in this session.** Every string in
   `src/data/*` is placeholder — structurally correct (right length, tone and
   shape for the layouts) and factually invented, including all metrics, client
   names and the `studio@akbrand.dev` address. Nothing in the design depends on
   these particular words. **Replace before anything goes near a client.**
2. **Photography.** The plates are procedural duotone halftones. They are a
   deliberate placeholder, not a proposal to ship a site with no photography —
   budget one shoot and put it through the same halftone treatment.
3. **The hero video** currently points at a third-party demo HLS stream from
   the original brief. Replace with the studio's own footage.
4. **Is `AK` the wordmark, or is there an existing logotype?** The header
   currently sets it in Fraunces italic.
