# AK Brand Development Studio — website concept

**Status:** design concept + working prototype.
Studio, founders and services copy is **real**, from the founders' brief. Case
studies, imagery and the hero video are **placeholders** — see §11.
**Live preview:** `https://duysha94.github.io/prominent/` (noindex).
**Accent:** `#f37021` · **Language:** English · **Production target:** WordPress
on the Zeyna theme shell.

---

## 1. The problem this design solves

AK is an independent creative and strategic practice. It does brand
development and positioning, personal brand strategy, identity and creative
direction, marketing and communication, photo and video campaign production,
events and fashion show production, industry PR, websites and digital
promotion.

That is nine services. Presented as a list, nine services read as a **menu** —
and a menu invites a client to buy one item and go elsewhere for the rest,
which is the opposite of what this studio is for.

The founders' own sentence gives the better structure:

> **From the initial idea to international presence.**

That is a sequence, not a list. So the site is built as one.

**And there is a second, larger problem the design has to solve: the studio's
single strongest asset is buried.** Konstantin Lieontiev founded and produces
**London Fashion Day** and **Odessa Fashion Day**. Most studios advising a
young designer have to *ask someone else* for a runway slot. These founders own
the platform. That is not a claim a competitor can copy — it is a fact about
who they are, and it is the answer to the only question a designer actually
has, which is "can you get my collection in front of people, or can you only
advise me about it?"

So it leads. It is in the hero, it is the one full-bleed section, and it has a
section of its own.


## 2. The idea: the tech pack

In fashion, a **tech pack** is the document that turns a creative idea into a
manufacturable, sellable product: measurements, callouts, materials,
construction notes. It is the artefact where craft and commerce meet, and it is
exactly what AK does for a brand.

So the site *is* a tech pack. That gives one visual language — annotation,
measurement, specification — that applies equally to a silhouette, a runway
running order and a product page. For a practice whose centre of gravity is
*production*, this is not a borrowed metaphor: it is the actual document the
work passes through.

Four devices carry it:

### The Measure Frame
Hover, focus, or simply scroll to any significant block and thin annotation
lines snap around it with figures attached — `LOOKS 24`, `PRESS 38`,
`RUNWAY LDN`, `LCP 0.9s`. It is drawn like a garment spec drawing and populated
with the numbers this studio actually works in. This single mechanism is what
unifies four movements: the same measuring eye applied to a silhouette, to a
show, and to a page.
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
| Client-logo / service-word marquee | Communicates nothing, continuous compositor cost | The band carries **checkable facts** — the platforms the founders built, and the cities they work across |
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
/                 Hero → the argument (full-bleed accent) → selected work
                  → what we founded → journal
/work             Contents page, filterable by movement
/work/:slug       Case study — branches by the movement that LED the work
/services         Four movements, each stating what it HANDS OVER to the next
/studio           The two founders, in full, then what they founded
/journal          Notes
/contact          The Brief — a measuring instrument, not a form
```

Four structural decisions do the heavy lifting:

**Nine services become four movements.** Strategy → Identity → Production →
Presence. Every service from the brief appears exactly once, in the movement
where it belongs. Nothing was dropped and nothing was invented.

**Each movement states its handover.** Strategy leaves Identity a written
position to design against; Identity leaves Production a system that holds at
campaign scale; Production leaves Presence real assets and real coverage;
Presence feeds what the audience does back into Strategy. The page is a chain,
so the argument for the whole practice is built into the layout rather than
asserted in a paragraph.

**The founders get the About page, in full.** A studio's credibility is not its
adjectives — it is who is going to do the work and what they have already done.
Nine years running a regional branch of an advertising holding, two
international fashion platforms founded, a multi-brand retail space, a media
title. Those are checkable, and they are the page.

**Case studies branch by movement.** A position, a campaign and a website are
not the same kind of evidence.

- `strategy` / `identity` → **The Spread.** The position at full display size,
  with the rejected lines struck through beneath it. Showing the rejects is the
  point: any studio can present the line it arrived at.
- `production` → **The Production Surface.** The work at the ratios it was made
  for, next to the eight weeks that followed — reach and press reported
  separately, never blended into one flattering number. This is the studio's
  crown jewel and the page most worth art-directing.
- `presence` → **The Walkthrough.** The site at real device proportions with
  before/after field data.

**The Brief** replaces "tell us about your project" with four questions a
founder can answer without thinking, and returns a spec sheet that recalculates
live — including movements they did not ask for, and why. The visitor leaves
with something even if they never send it.

## 8. SEO

SEO here is content and markup, not a plugin.

**Every route is prerendered to a real HTML file** by `scripts/prerender.mjs`
after the Vite build, each with its own `<title>`, meta description, canonical,
Open Graph and Twitter tags, and JSON-LD. A React SPA otherwise ships one
`index.html` and rewrites the title in JavaScript after paint — which a crawler
may never execute and a social scraper certainly will not. That single fact is
the biggest SEO failure mode of a React marketing site.

The same pass writes `sitemap.xml`, `robots.txt`, and a `404.html` fallback,
which is also what makes deep links work on static hosting.

**Structured data** is a linked graph rather than a lone Organization blob:

- `ProfessionalService` + `Organization` with address, `areaServed`
  (London / Paris / Dubai), `knowsAbout`, and an `OfferCatalog` generated from
  the four movements and every service in them;
- a `Person` node per founder, referenced by `@id` from the organisation's
  `founder`, each with `founderOf` pointing at London Fashion Day, Odessa
  Fashion Day, KEKA and Cool'baba — so a search engine can connect Konstantin
  to the platforms instead of treating them as unrelated strings;
- `BreadcrumbList` per page, `WebSite` for the site itself.

**Titles and descriptions** are hand-written in `src/data/seo.ts`, one intent
per page, under ~60 and ~155 characters so neither is truncated into nonsense.
Every claim in a description is one the page actually makes.

**On-page**: exactly one `<h1>` per route (asserted by the QA harness), a real
heading hierarchy, `<time datetime>` on entries, tables as tables, and the
split-text headline reveal carrying its full string on the wrapper's
`aria-label` — so a crawler and a screen reader both read one sentence rather
than fragments.

**The preview build is `noindex`** with a disallowing `robots.txt`, so the
staging copy on `github.io` cannot compete with `akbrand.studio` or put
placeholder copy into search results.

**In production this gets better, not worse.** WordPress renders all of it
server-side natively; the prerender script exists to make the prototype honest
in the meantime.


## 9. Charts

Campaign reach is in the hundreds of thousands and press mentions are in the
tens, so they are **two charts sharing an x axis**, never a dual-axis plot. A
dual axis lets you place the two series' crossings anywhere by choosing the
scales, which is indefensible on a page whose argument is that the numbers are
honest. One series per panel means no
legend is needed; only the final value is directly labelled; the grid is
recessive; and the accessible representation is a real `<table>` that is always
in the DOM.

---

## 10. Performance & accessibility contract

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

## 11. Open questions

1. **What is real and what is not.**
   *Real, from the founders' brief:* the studio description, the nine services,
   both founder biographies, London Fashion Day, Odessa Fashion Day, KEKA,
   Cool'baba, London / Paris / Dubai, and the `akbrand.studio` domain.
   *Placeholder, to be replaced:* every case study in `src/data/work.ts`, every
   journal entry, the `hello@akbrand.studio` address, and the Instagram and
   LinkedIn URLs in the footer.
2. **Photography.** The plates are procedural duotone halftones — a deliberate
   placeholder, not a proposal to ship without photography. The founders'
   portraits in particular are labelled as placeholders on the page itself.
   Budget one shoot and put it through the same halftone treatment.
3. **The hero video.** Drop two files into `public/media/` — see the README
   there for the spec. Until they exist the hero shows its drawn plate, which
   is the intended fallback rather than a broken state.
4. **The logotype.** The header currently sets `AK` in Fraunces italic as a
   placeholder. Supply an SVG and it swaps in one component.
5. **Zeyna has no light/dark mode.** The brief asked to keep it; the theme does
   not have one. This prototype's two-mode system is the answer — see
   `docs/WORDPRESS-ZEYNA.md` §6.
