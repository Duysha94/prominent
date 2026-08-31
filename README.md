# AK Brand Development Studio — website concept

A design concept and working prototype for **AK Brand Development Studio**
(fashion & brand advisory + website/e-commerce development + Meta & Google Ads).

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

AK sells three things that normally come from three companies, so the site's
job is to make them read as one practice. The concept is a **tech pack** — the
fashion document that turns a creative idea into a manufacturable, sellable
product. Annotation, measurement and specification become one visual language
that applies equally to a silhouette, a checkout funnel and an ad account. An
orange thread (the Seam) stitches every page together and *is* the page
transition; headlines are cut open and closed rather than faded in; and case
studies branch into three different templates depending on which lane led the
work — because a brand identity, a shipped store and a media account are not
the same kind of evidence.

---

## Stack

React 19 · Vite 8 · TypeScript · Tailwind v4 (CSS-first) · GSAP 3.15 ·
Motion 13 · Lenis 1.3 · hls.js

Motion runs in two tiers: **CSS scroll-driven animation** for everything the
platform can do on the compositor (0KB of JS, cannot affect INP), and **GSAP**,
lazily loaded, only for what the CSS spec deliberately excludes.

| Budget | Target | Actual |
|---|---|---|
| Initial JS (gzip) | < 150KB | ~138KB |
| Fonts | < 200KB | 187KB — 3 variable families, subset and axis-pruned from 737KB |
| CSS (gzip) | < 15KB | ~9KB |

---

## ⚠️ The content is placeholder

**The agency's own brief never reached the session that built this.** Every
string in `src/data/*` — client names, metrics, the email address, the case
studies — is invented. It is written to be *structurally* right (correct length
and tone for the layouts) and is factually fiction.

Nothing in the design depends on those particular words. **Replace before this
goes anywhere near a client.** See `docs/CONCEPT.md` §10.

---

## Layout

```
src/
  components/
    shell/        Header, MenuOverlay, Footer, Seam, Loader, PageTransition, ThemeToggle
    primitives/   CutText, MeasureFrame, Magnetic, Marquee, Plate, Reveal, Counter, Grain
    sections/     Hero, Argument, WorkIndex, Proof, JournalPreview, LaneDiagram
    case/         CaseSpread · CaseWalkthrough · CasePerformance — one per service lane
  pages/          Home, Work, CaseStudy, Services, Studio, Journal, Contact, NotFound
  lib/            motion tokens, GSAP loader, Lenis, theme, reduced-motion, HLS
  styles/         tokens.css (design tokens) → base.css (semantics, tier-1 motion, fonts)
  data/           ← all placeholder content lives here
```
