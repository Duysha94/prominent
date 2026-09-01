# AK Brand Development Studio — child theme for Zeyna

A complete, installable website: child theme + content import. It keeps
Zeyna's header, footer, menu and Barba page transitions, and layers the
studio's design system, motion and finished page layouts on top.

After install, only three things are yours to swap: the **logo**, the
**photography**, and the **showreel video**. Everything else — pages, copy,
menu, contact form, placeholder case studies — arrives ready.

---

## Install — in this exact order

1. **Zeyna** (parent theme): admin → **Appearance → Themes → Add New →
   Upload Theme** → upload the Zeyna zip from your ThemeForest purchase.
   Install only — no need to activate it.
2. **Contact Form 7**: **Plugins → Add New** → search "Contact Form 7" →
   Install → Activate. (Free, by Takayuki Miyoshi.)
3. **This child theme**: **Appearance → Themes → Add New → Upload Theme** →
   upload `ak-zeyna-child.zip` → **Activate**. Activating the child first
   matters: it registers the portfolio content type and page templates the
   import is about to reference.
4. **The content**: **Tools → Import → WordPress** (install the importer
   when prompted) → upload `ak-content.xml` → assign posts to your own
   user → run. There are no attachments to download, so that checkbox does
   not matter.
5. Done. The theme wires the rest itself the moment the import finishes:
   front page → *Home*, posts page → *Journal*, the *Primary* menu onto
   Zeyna's `menu-1` location, permalinks flushed so `/work` resolves.

### Installing over an existing site

The import **adopts the site**. When it finishes, the theme automatically:

- reclaims its page addresses — if an old page already held `home` or
  `services`, the old page is moved to the Trash and the imported page
  takes the clean URL (no `home-2` leftovers);
- points the front page, the posts page and the header menu at the
  imported content, replacing whatever was set before;
- moves every published page that is **not** part of this import to the
  Trash, plus the default "Hello world!" post. Nothing is hard-deleted —
  anything can be restored from Pages → Trash for 30 days. The privacy
  policy page is deliberately left alone.

Then two settings worth checking:

- **Zeyna theme options (Redux) → Page transitions: ON.** Zeyna gates the
  whole Barba system on that switch; the child theme follows it.
- **Settings → Permalinks** — any pretty-permalink structure ("Post name"
  is fine). Just visiting the screen re-saves rewrite rules if a URL ever 404s.

### Import ran before the theme was active, or pages look unassigned?

Re-activate the child theme (switch to another theme and back, or just
re-run the import — both are safe and idempotent). The wiring never
overwrites a choice you made manually.

---

## After install: the three swaps

| What | Where | Notes |
|---|---|---|
| **Logo** | Customizer → Site Identity → Logo | SVG preferred. Until then the header shows the site title set in Fraunces italic. |
| **Photos** | Each case study → Featured image; founders' portraits on the About page slots | Every image slot is labelled "replace via featured image" on the page itself. |
| **Showreel video** | Customizer → **AK Studio** | Two uploads — AV1 `.mp4` preferred + H.264 `.mp4` fallback — and a poster frame. 8–12 s, silent, loopable. The hero slot stays a labelled placeholder until you do. |

And before launch:

- Replace `hello@akbrand.studio` with the real address in **two** places:
  the `ak_studio_email()` helper at the top of `functions.php` (every
  template and the JSON-LD read it from there), and the Contact Form 7
  form — its **Mail** tab recipient plus the "failed to send" line under
  **Messages**.
- The six case studies and four journal entries are labelled placeholders —
  rewrite them with real projects at your pace; the layouts hold.
- Search snippets are built in: every page emits a hand-written meta
  description (see `inc/seo.php`), and a page's own **excerpt** overrides it
  — no SEO plugin required. If you later install one (Yoast, Rank Math,
  AIOSEO, SEOPress), the theme detects it and steps aside automatically.

---

## The contact form

The import creates a Contact Form 7 form named **ak-project-brief** — the
project brief: name, email, brand, link, two selects ("Where is the brand
now?", "What do you think you need?") and the message. The contact page
finds it **by that name**, not by ID (IDs change on import), so never rename
it. Mail 1 goes to the studio with Reply-To set to the sender; Mail 2 is a
short auto-reply in the house voice. All validation messages are restyled
and rewritten.

---

## What is in the box

| Included | Notes |
|---|---|
| **Every page, finished** | Home, Work index, six case studies, Services, About, Journal + four entries, Contact — templates and imported content |
| The full colour system | `#f37021` with the measured contrast rules, both modes, wide-gamut P3 |
| Three variable fonts, self-hosted | Fraunces, Bricolage Grotesque, Martian Mono — subset and axis-pruned to 187KB total |
| **ATELIER / RUNWAY** light-dark mode | Zeyna has none — see below |
| The Seam | The orange thread, scroll-velocity reactive, in the footer so it survives navigations |
| Cut-text headline reveal | GSAP SplitText, dismantled after it settles so descenders are never clipped |
| Measure frames | Tech-pack annotations; hover, focus **and** touch |
| Tier-1 CSS motion | Scroll-driven reveals and variable-font weight, zero JavaScript |
| A Barba bridge | Teardown and rebuild around the container swap, plus focus and announcements |
| JSON-LD | Linked graph: the studio, both founders, the platforms they founded — plus BreadcrumbList on every inner page |
| Cinema layer | Perspective plate entrances, Ken Burns drift, pointer 3D tilt, the grain — all guarded |
| The name, explained | The A–K monogram on About and "The name" section on Home: A is Andrey, K is Konstantin |
| Contact Form 7 form + styling | Imported with the content; found by slug at render time |
| A bespoke-page template | `AK — Bespoke page`, the pattern all the others follow |

---

## Zeyna has no light/dark mode

Worth stating plainly because the brief assumed otherwise. Verified against
the theme source: there is not one occurrence of `data-theme`, `.dark`,
`.light-mode` or `prefers-color-scheme` in Zeyna's CSS, and no toggle in its
JavaScript. What looks like a dark mode in the demos is a **demo variant** —
a Redux colour preset you import once — not a switch a visitor can operate.

So the child theme brings its own, and it is better than a hex-swap: ATELIER
(the daylit workroom) and RUNWAY (the show) are two designed modes that
differ in *material* — paper fibre against film grain — not only in colour.
The switch is appended to the primary menu automatically; it is also
available as `<?php ak_mode_toggle(); ?>` or the `[ak_mode_toggle]`
shortcode if you want it elsewhere.

The mode is resolved in a blocking inline script in `<head>` before first
paint, so a dark visitor never sees a white flash, and it is stored on
`<html data-theme>` rather than on `<body>` — because Barba replaces body
classes wholesale on every soft navigation and a mode kept there would reset
each time.

---

## Building a bespoke page

Copy `page-templates/template-ak-page.php`. Two rules it exists to demonstrate:

**Emit the Barba container.** Zeyna puts `data-barba="container"` on
`<main id="primary">` through `zeyna_barba(false)`. Call that same function
rather than hard-coding the attribute, so the theme's own transition option
still governs it. Miss it and transitions silently degrade to full browser
navigations.

**Leave `get_footer()` after `</main>`.** The footer sits outside the
container and persists across navigations — which is exactly why the Seam
lives there.

Markup uses plain classes from `assets/css/ak.css`: `.ak-wrap`,
`.ak-section`, `.ak-eyebrow`, `.ak-display`, `.ak-lead`, `.ak-grid`,
`.ak-btn`, `.ak-accent-field`, `.ak-plate`, `.ak-band`, `.ak-form`. No page
builder, no utility framework, no build step.

**Hooks for the motion devices** — add these attributes to any element:

| Attribute | Effect |
|---|---|
| `data-ak-cut` | Cut-text reveal on a headline |
| `data-ak-measure` | Tech-pack annotation frame (add `data-always` to pin it open) |
| `data-ak-tilt` | Pointer-tracking 3D tilt with a travelling sheen (fine pointers only) |
| `class="ak-vf"` | Scroll-driven variable-font weight (CSS only) |
| `class="ak-draw"` | A rule that draws itself as the section scrolls through |
| `class="ak-rise"` | Short-travel arrival (CSS only) |
| `class="ak-plate"` | Arrives like a set piece (perspective entrance) and gives any `<img>`/`<video>` inside a slow Ken Burns drift |
| `class="ak-lead"` | Arrives line by line from behind a mask, staggered (SplitText; the split is reverted afterwards) |
| `class="ak-btn"` | Magnetic — leans toward the cursor and springs home (fine pointers only) |
| Folio numbers & tech-pack figures | Decode into place with ~600ms of monospace noise as they enter the viewport |
| The facts band | Skews against scroll velocity, like tape being pulled |
| Index rows & journal notes | Step aside for the cursor on a spring |

All of it degrades honestly: no scroll-driven support → finished state;
`prefers-reduced-motion` → still pages; touch → no tilt.

---

## The one design rule that is not negotiable

**The brand orange is a surface, never light-mode ink.**

Measured against this site's own surfaces:

| Pair | Contrast | Verdict |
|---|---|---|
| brand-500 on paper | 2.73:1 | **Fails** WCAG 1.4.11 — never a mark, rule or focus ring in light mode |
| brand-600 on paper | 3.49:1 | Marks in light mode |
| brand-700 on paper | 4.80:1 | Text in light mode |
| brand-500 on ink | 6.60:1 | Marks and text on dark |
| ink on brand-500 | 6.60:1 | Orange fills take **ink** type |

White on orange measures **2.94:1** and fails three thresholds at once. It
is also what nearly every orange brand ships. Do not add it back.

Use `var(--accent-fill)` for surfaces, `var(--accent-text)` for type and
`var(--accent-line)` for marks — each already resolves correctly per mode.

---

## What Zeyna already loads, and why this theme does not

Zeyna enqueues GSAP (`zeyna-gsap`), its plugin bundle (`gsap-plugins` —
SplitText, ScrollTrigger, Flip, Observer), Lenis and Barba. This child theme
declares dependencies on those handles instead of shipping duplicates: two
GSAP instances would fight over the same ticker, and it would be ~130KB of
duplicate JavaScript.

One consequence worth knowing: Zeyna's bundle does **not** include
CustomEase, so the four house easing curves are registered as plain
functions via `gsap.registerEase` in `assets/js/ak.js`. They are the same
curves as the CSS tokens, so a GSAP tween and a CSS transition on one
element move identically.

---

## Reference

The React prototype in the repository is the design reference for anything
not covered here — exact spacing, motion timing, and the page compositions.
See `docs/CONCEPT.md`, `docs/MOTION.md` and `docs/WORDPRESS-ZEYNA.md`.
