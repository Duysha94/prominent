# Visual system

Specific enough to build from without re-deciding anything per section.

## The direction

**Tech pack as structure, not decoration.**

A garment tech pack is a production document: a spec block at the top, numbered
construction steps, measurements in the margin, a credits panel at the foot.
Its grammar is *labelled data beside large imagery* — which is exactly what a
case study is. So the grammar becomes the page's skeleton, and the fashion
editorial supplies the flesh: large imagery, display italics, generous
whitespace, asymmetry.

What this is **not**: a page that looks like a spec sheet. No blueprint
crosshatch, no faux-CAD frames, no monospace body copy, no dark "engineering
dashboard". The tech-pack language appears as **codes, rules, margin notes and
numbering** — never as a skin.

The one-line test: *fashion editorial that happens to be rigorously
documented*, never *a document with fashion pictures in it*.

## Grid

A 12-column grid with a **margin rail** — the single most identity-carrying
structural decision.

```
│ rail │ 12 columns                                             │
│ 5rem │ max 1440px, gutter 24px                                │
```

- **The rail** (left, ~5 rem on desktop) carries what a tech pack puts in the
  margin: project codes, section numbers, figure references, the seam. It is
  never used for prose.
- **Content** sits in the 12 columns. Text runs 6–7 columns (≈65ch); imagery
  takes 8, 10 or 12.
- **Asymmetry is the default.** Full-width text blocks are reserved for a
  single statement per page.
- Below 900 px the rail collapses to a 1 px line at the left edge and its
  contents move inline above their sections.

Named spans, used consistently: `--span-text` (6), `--span-wide` (8),
`--span-full` (12), `--span-bleed` (100vw).

## Typography

Three faces, three jobs. Never a fourth.

| Role | Face | Size (desktop / phone) | Use |
|---|---|---|---|
| **Display** | Fraunces italic 400 | `clamp(2rem, 5.5vw, 4.25rem)` / 30 px | Statements, case headlines, project names. One per section |
| **Hero display** | Fraunces italic 400 | `clamp(2.5rem, 8vw, 6rem)` / 38 px | One per page |
| **Lead** | Bricolage Grotesque 400 | 1.125 rem / 17 px, full ink | The paragraph under a statement. Never the same size as body |
| **Body** | Bricolage Grotesque 400 | 1 rem / 15 px, muted | Prose |
| **Body small** | Bricolage Grotesque 400 | 0.875 rem / 14 px | Card bodies, captions |
| **Data** | Martian Mono 400 | 0.6875 rem / 11 px, 0.1em | Codes, labels, metadata, years, disciplines, nav |
| **Data quiet** | Martian Mono 400 | 0.625 rem / 10 px, 0.12em | Folios, figure numbers |

Leading: display 1.02–1.06 (never below 1.0 — italic descenders collide);
lead 1.55; body 1.65.

**Martian Mono is the tech-pack voice.** Every piece of structured data is
mono; no prose ever is. That single rule does most of the identity work.

## Spacing

Three intervals and no others:

| Token | Desktop | Phone | Between |
|---|---|---|---|
| `--gap-unit` | 0.6 rem | 0.6 rem | Parts of one thing |
| `--gap-block` | 1.5 rem | 1.5 rem | Things in a list |
| `--gap-section` | `clamp(4rem, 9vw, 7rem)` | 3.5 rem | Sections |

Every section boundary on a phone also carries a hairline. Whitespace alone
does not survive a single column.

## Colour

Unchanged from the shipped system; it works.

| Token | Light (ATELIER) | Dark (RUNWAY) |
|---|---|---|
| `--bg` | `#FAF8F6` | `#141210` |
| `--text` | `#1A1613` | `#EDE9E4` |
| `--accent-line` | `#F37021` | `#F37021` |
| `--accent-text` | `#C4531A` | `#F58A45` |

**The contrast rule that must not be relaxed:** brand orange on paper is
2.73:1 and fails. Orange is a *line and fill* colour in light mode; orange
**text** on paper is always `--accent-text`. Ink on orange is 6.60:1 and is the
only text permitted on an orange field — never white.

## Light / dark

Still relevant, and now more so: a case study can declare its own mode
(`ak_mode`), so a dark project presents dark. Resolution order: explicit
visitor choice → the case's declared mode → OS preference. Resolved by an
inline head script before first paint, on `<html data-theme>` so a Barba
navigation cannot reset it.

## Imagery

| | |
|---|---|
| Ratios | Cover 4:5 · hero 16:9 desktop / 4:5 phone · gallery 1:1, 4:5 or 3:2 by `span` |
| Cropping | `object-fit: cover` with `object-position` from `ak_focal` |
| Loading | `srcset` always; `loading="lazy"` except the hero; `fetchpriority="high"` on the hero and logo |
| CLS | Intrinsic width/height on every image, always |
| Fallback | No image → a typographic plate: the project code and name set large on the raised surface. **Never a broken-image icon, never a grey box** |
| Treatment | `filter: saturate(0.92)` at rest, 1.0 on hover. A whole-site duotone would fight the client's own art direction |

## Video

Video is composition, not an embed.

- **Never in a bordered box.** It is either full-bleed, or it is a measured
  frame that *becomes* full-bleed on scroll.
- Always `muted playsinline autoplay loop preload="metadata"` with a poster.
- Two sources: AV1-in-MP4 first, H.264 fallback.
- No third-party player. No YouTube chrome.
- Phone: the same film, portrait-cropped via `ak_hero_mobile`, or the poster
  alone on a slow connection.
- Paused off-screen. Never plays behind an open menu.

## Metadata style

The tech-pack signature. A spec block is a bordered table of `LABEL` / `value`
pairs:

```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ CLIENT       │ YEAR         │ LOCATION     │ REGISTER     │
│ London …     │ 2019 →       │ London       │ Founded      │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

Label in Data-quiet, value in Body-small semibold. Four columns desktop, two
tablet, two phone. Hairline rules, no fills, no radius. **An empty field is
omitted, not shown blank** — the block reflows to the fields that exist.

## Project indexing

Every project carries a code: `AK·F·19·001` — studio, register initial, year,
sequence. It appears in the rail on a case study, in the index rows, and in the
`<title>`. It is the thing that makes the archive read as an archive.

Index rows: `code · client · title · disciplines · year`, hairline-separated,
whole row a link, 8 px spring shift on hover. Two registers are separated by a
full section boundary and their own heading — never interleaved.

## Navigation

- **Header:** wordmark left; mode switch and menu right. Always visible, never
  hides on scroll. Hairline bottom rule.
- **Desktop:** five items, Data type, current item underlined in accent.
- **Phone (≤900 px):** a right-hand panel over a dimmed scrim, focus trapped,
  Escape and outside-click close, body scroll locked including Lenis. The panel
  sits above the header's stacking context.
- No mega-menu, no dropdowns. A five-item nav does not need them.

## Footer

Four zones: an outline-type marquee of the studio name; three columns (Studio ·
Movements · Contact); social profiles **only when set**; a © bar with the
cities line. All business data from `inc/studio.php`. No widget areas.

## Composition by width

| Width | Behaviour |
|---|---|
| **1440+** | Rail visible. 12 columns. Asymmetric spreads: text 6 / image 5 with a 1-column void. Hero display at full scale |
| **1024–1440** | Rail narrows to 3 rem. Spreads compress to 7 / 5, void removed |
| **900–1024** | Rail becomes a 1 px line. Two-column spreads become stacked with the image full-width. Nav still horizontal, gaps tightened |
| **640–900** | Panel navigation. Grids to two columns. Spec block two columns |
| **≤640** | Single column. Grid frames and card borders drop to one hairline per row; card text aligns to the column. Rail contents move inline above their sections. Phone type scale |
| **≤390** | No further breakpoint. The 640 rules hold; type is already fluid |

Verified 320 → 1920 with no horizontal overflow.

## Rules that are not negotiable

1. Prose is never monospace; data is always monospace.
2. No white text on orange.
3. No image without intrinsic dimensions.
4. No empty metadata field rendered.
5. One display statement per section, one hero display per page.
6. Every hairline is 1 px `--rule`; there is no second border weight.
7. No shadows. Depth comes from rules and ground, not blur.
8. No border radius anywhere. The system is drawn with straight lines.
