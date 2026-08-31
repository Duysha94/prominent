# AK Brand Development Studio — website concept

A design concept and working prototype for **AK Brand Development Studio** —
an independent creative and strategic practice in London specialising in brand
development, fashion consulting and creative production.

**Live preview: <https://duysha94.github.io/prominent/>** (noindex — see below)

**→ Read [`docs/CONCEPT.md`](docs/CONCEPT.md) first.** It explains the idea, what
was rejected and why, and the open questions.

| | |
|---|---|
| [`docs/CONCEPT.md`](docs/CONCEPT.md) | The design concept, colour science, typography, structure |
| [`docs/MOTION.md`](docs/MOTION.md) | The motion system — tokens, doctrine, reduced-motion strategy |
| [`docs/WORDPRESS-ZEYNA.md`](docs/WORDPRESS-ZEYNA.md) | How this maps onto the WordPress/Zeyna build |

---

## Run it

```bash
npm install
npm run dev      # http://localhost:5173
npm run build    # typecheck + production build
```

Node 22+. No environment variables, no services.

---

## The idea in one paragraph

The studio does nine things. As a list, nine services read as a menu — so the
site is built on the founders' own sentence instead: *from the initial idea to
international presence*. Four movements, each stating what it hands to the
next: **Strategy → Identity → Production → Presence**.

The concept is a **tech pack** — the fashion document that turns a creative
idea into a manufacturable, sellable product. Annotation, measurement and
specification become one visual language covering a silhouette, a runway and a
product page. An orange thread (the Seam) stitches every page together and *is*
the page transition; headlines are cut open and closed rather than faded in.

And the studio's strongest asset leads: the founders built **London Fashion
Day** and **Odessa Fashion Day**, so they can put a first collection on an
international runway rather than write a deck about one.

---

## Stack

React 19 · Vite 8 · TypeScript · Tailwind v4 (CSS-first) · GSAP 3.15 ·
Motion 13 · Lenis 1.3

Motion runs in two tiers: **CSS scroll-driven animation** for everything the
platform can do on the compositor (0KB of JS, cannot affect INP), and **GSAP**,
lazily loaded, only for what the CSS spec deliberately excludes.

| Budget | Target | Actual |
|---|---|---|
| Initial JS (gzip) | < 150KB | ~140KB |
| Fonts | < 200KB | 187KB — 3 variable families, subset and axis-pruned from 737KB |
| CSS (gzip) | < 15KB | ~10KB |

The brief asked for an HLS hero. hls.js measured 176KB gzip in this build — 3.5x
the GSAP chunk — to adaptively stream a silent loop that never adapts, so it was
removed in favour of a plain gated `<video>`. See `docs/CONCEPT.md` §9.

---

## What is real and what is placeholder

**Real** (from the founders' brief): the studio description, all nine services,
both founder biographies, London Fashion Day, Odessa Fashion Day, KEKA,
Cool'baba, the cities, and the `akbrand.studio` domain.

**Placeholder** (replace before launch): every case study in `src/data/work.ts`,
every journal entry, the email address, the social URLs, all imagery, and the
hero video (drop two files into `public/media/` — see the README there).

The preview is served `noindex` with a disallowing `robots.txt` so it cannot
compete with the real domain or put placeholder copy into search results.

## SEO

Every route is prerendered to a real HTML file with its own title, description,
canonical, Open Graph tags and JSON-LD — because a title written by JavaScript
after paint is one a crawler may never see. Structured data is a linked graph:
`ProfessionalService` with a full service catalogue, a `Person` node per
founder with `founderOf` pointing at the platforms they built, and breadcrumbs
per page. `sitemap.xml` and `robots.txt` are generated. See `docs/CONCEPT.md` §8.

---

## Layout

```
src/
  components/
    shell/        Header, MenuOverlay, Footer, Seam, Loader, PageTransition, ThemeToggle
    primitives/   CutText, MeasureFrame, Magnetic, Marquee, Plate, Reveal, Counter, Grain
    sections/     Hero, Argument, WorkIndex, Proof, JournalPreview, LaneDiagram
    case/         CaseSpread · CaseProduction · CaseWalkthrough — one per movement
  pages/          Home, Work, CaseStudy, Services, Studio, Journal, Contact, NotFound
  lib/            motion tokens, GSAP loader, Lenis, theme, reduced-motion, HLS
  styles/         tokens.css (design tokens) → base.css (semantics, tier-1 motion, fonts)
  data/           studio + founders (real) · services (real) · work + journal (placeholder) · seo
scripts/
  prerender.mjs   bakes per-route HTML, JSON-LD, sitemap, robots
  visual-qa.mjs   80-combination QA sweep
```
