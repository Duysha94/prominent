# AK — concept v2: “One letter each”

A new concept for **AK Brand Development Studio**, built to replace the
tech-pack concept in `docs/CONCEPT.md`. Immersive, 3D, scroll-driven, with
video — and still about this studio and no other.

## The reading

The client wants a site that moves like the best current 3D sites, with video,
parallax and things to touch, but that still argues for this studio. Read as:
**one strong 3D idea taken from the studio itself, carried through every
section — not a reel of effects.**

## Inventory — what only this studio has

1. **The name.** AK is not an acronym. **A is Andrii, K is Kostiantyn** — two
   co-owners, one letter each (the founders’ own brief, in the About template).
2. **The split.** One side runs digital, identity and media (Andrii); the other
   runs strategy and fashion production (Kostiantyn). Both letters are load-bearing.
3. **Their sentence:** “From the initial idea to international presence, we
   support projects at every stage of their development.”
4. **London Fashion Day** — international platform for emerging designers,
   founded and produced by Kostiantyn.
5. **Odessa Fashion Day** — built to develop international creative communities,
   founded and produced by Kostiantyn.
6. **KEKA** — Kostiantyn’s fashion brand, in development for the international market.
7. **Cool’baba** — Andrii’s online magazine: fashion, lifestyle, creative industries.
8. **Nine years** running a regional branch of a major advertising holding (Kostiantyn).
9. **A multi-brand retail space** for emerging designers (Andrii).
10. **Six movements** — Strategy, Identity, Image, Experience, Digital,
    Visibility (`docs/CAPABILITIES-SYSTEM.md`, canonical). Named for the
    client’s questions: *who am I · what do I look like · who shoots this ·
    who produces the show · who builds the site · who gets me written about.*
11. **The studio is in London, United Kingdom.** Paris and Dubai are places
    Kostiantyn has collaborated — never a studio footprint.

## The argument

| Section | Claim | Proof |
|---|---|---|
| Hero | AK is a London studio that takes fashion brands from the first idea to international presence | The name, their own sentence |
| Between the letters | The two letters are two people, and each carries half the practice | A = Andrii, K = Kostiantyn, the split of the practice |
| Six movements | One studio answers every question a founder has, in order | All six movements, every capability |
| Our own | They do not write decks about runways — they founded them | London Fashion Day, Odessa Fashion Day, KEKA, Cool’baba |
| Studio | An independent practice with two complementary founders | The description and both bios, verbatim |
| Begin | Starting is one email | A prepared brief |

## Direction (taste-skill)

`name-as-monument`, seed `1714414601`, sector fashion, tone bold. Two slots redrawn:

- **Type → Epilogue** (from Erode). Erode is served only by Fontshare, which this
  environment cannot reach; Epilogue ships on npm, and a broad black grotesque
  extrudes into 3D letters that read as sculpture.
- **Palette → cool grey + cobalt** (from graphite + orange). Orange was the brand
  colour of the concept the client rejected; keeping it would make the new site
  read as the old one.

Motion: the sheet dealt a slow crossfade. The client asked explicitly for strong
3D motion, so the signature is the letters themselves; the crossfade survives on
the A’s screen.

## The idea

> On this page, **the two letters of the name — A for Andrii, K for Kostiantyn**
> become **two monuments the page happens between**: A in black glass with
> moving pictures on its face (digital, identity, media), K in cobalt silk
> (strategy and fashion production).

Competitor test: no other studio’s name is its two co-owners, one letter each,
with each letter carrying half the work. It does not transfer.

**The live thing:** choose a letter. Tap, click or focus **A** or **K** and that
letter steps forward and tells you whose it is and what that half of the studio
does. Works by thumb, by keyboard, and as plain text without script.

## Copy deck

**Title:** AK Brand Development Studio — from the initial idea to international presence

**Header:** AK · Practice · Our own · Studio · Contact · [Start a project]

**Hero**
- h1: **AK** Brand Development Studio
- Sentence: From the initial idea to international presence, we support projects at every stage of their development.
- Action: Start a project
- Letters: A — Andrii Karakushan · Digital, identity and media / K — Kostiantyn Lieontiev · Strategy and fashion production

**Between the letters**
- h2: A is Andrii. K is Kostiantyn.
- Statement: Two co-owners, one letter each. One side of the studio runs digital, identity and media; the other runs strategy and fashion production.
- Note: AK is not an acronym for anything corporate. Both letters are load-bearing.

**Six movements** (each line opens with its order word)
1. **First, who you are.** Strategy — what the brand is, and who it is for.
2. **Then, how you look.** Identity — what it looks and sounds like.
3. **Then, the pictures.** Image — the photographs and the films.
4. **Then, the show.** Experience — what happens in the room.
5. **Then, where you’re found.** Digital — where the brand lives online.
6. **Last, who hears of it.** Visibility — how it is seen and heard. Communication & PR and Promotion & paid media, with equal weight, never one list.

Every capability from `docs/CAPABILITIES-SYSTEM.md` sits under its line.

**Our own**
- h2 + names: Our own: London Fashion Day, Odessa Fashion Day, KEKA, Cool’baba.
- Line: Four platforms the two of us founded — two fashion weeks, a fashion brand and a magazine.
- Each, verbatim from the brief: founder and one-line note.

**Studio**
- Facts: Based in London, United Kingdom · Founders Andrii Karakushan, Kostiantyn Lieontiev · Practice: six movements
- Paragraph: the studio description, verbatim.
- Founders: both bios, verbatim.
- Between them: advertising, fashion production, retail, media, digital brand development.

**Begin** (one word is the button)
- Sentence: Tell us about the brand. The email opens with a short brief to fill in.

**Footer:** AK, full width, with each founder’s name under their letter.

## Placeholders — replace before launch

- **All video and imagery is rendered in code** (runway, silk, pages): no
  photograph or footage of the studio, the founders or the platforms was
  available, and the open image collections are unreachable from this
  environment. Replace with London Fashion Day / Odessa Fashion Day footage.
- `hello@akbrand.studio` is still unconfirmed (as in `src/data/studio.ts`).
- No social links until the real accounts are confirmed.

## Checks (taste-skill gate)

`lint.mjs`: 0 FAIL, 0 WARN. `inspect.mjs` on the production build at 1440 / 768 /
390: **0 FAIL, 3 WARN**, each defended:

- *blank-gap* — the stretch above the hero letters is filled by the 3D stage,
  which the inspector cannot see through; the footer's empty middle row is the
  blueprint's own deliberate rest.
- *pinned-over-picture* — the stage is a full-screen canvas, so any pinned text
  is geometrically "over a picture"; the letters are placed in the columns the
  text does not occupy (edges, lower-left), which the screenshots confirm.
- *long-pin* — the practice holds about five screens, and every step changes
  the active line, the 3D state and the detail panel.

Fixed along the way: a full-screen flash layer covered text (the flash now
lives in the 3D exposure, rate-limited to one per 350 ms for WCAG 2.3.1); the
hero action read as an eyebrow (moved to the bar); one near-empty screen before
the reel; capability lists sitting like eyebrows above the next line on tablet.

## What still falls short

1. The footage is rendered placeholder: the gowns read as CG. Real London
   Fashion Day / Odessa Fashion Day footage will lift every section it sits in.
2. No photographs of the founders exist yet; the studio section is type only.
3. The ivory gown in `runway-b` reads close to bridal — replace or re-render darker.
