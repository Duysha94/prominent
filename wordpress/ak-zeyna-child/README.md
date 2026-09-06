# AK Brand Development Studio — the studio's theme

A complete, installable website: theme + content, with nothing to import.

It installs as a child of Zeyna and is no longer built on it. The header,
footer, navigation, page transitions, loader, motion layer, every stylesheet
and every script are this theme's own; no parent CSS, JavaScript, template or
setting is used at runtime. Zeyna is still required at install time for one
reason only — WordPress will not activate a child theme whose declared parent
is missing. See `docs/ZEYNA-EXIT-PLAN.md`.

After install, only three things are yours to swap: the **logo**, the
**photography**, and the **showreel video**. Everything else — pages, copy,
menu, contact form, placeholder case studies — arrives ready.

---

## Install — three steps, no import

1. **Zeyna** (declared parent): **Appearance → Themes → Add New → Upload
   Theme** → upload the Zeyna zip. Install only; do not activate it. Nothing
   from it is loaded — WordPress simply refuses to activate a child theme
   whose parent is not installed.
2. **Contact Form 7**: **Plugins → Add New** → install and activate.
3. **This theme**: upload `ak-zeyna-child.zip` → **Activate**.

That is the whole install. On activation the theme creates its own pages,
case studies, journal entries, the navigation menu and the contact form,
sets the front page and the posts page, and hangs the menu in the header.
There is nothing to import.

`import/ak-content.xml` still ships for anyone who prefers the classic
route, but it is no longer required and can be ignored.

### Updates

Every later version arrives through **Appearance → Themes** as a normal
one-click WordPress update. When it installs, the theme reconciles the site
with the content the new version ships:

| | |
|---|---|
| A page the new version adds | created |
| A page you have not edited | refreshed |
| A page you *have* edited | left exactly as you left it |
| A project or entry the new version drops | moved to the Trash |

It then tells you what it did in an admin notice. Nothing is ever deleted
outright — the Trash holds it for 30 days.

---

## Install — the long version

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

### Updates are automatic from here

This build (1.1.0) is the LAST one you install by hand. The theme checks
its published update channel twice a day; when a newer build exists,
WordPress shows the normal update notice under **Appearance → Themes**
and **Dashboard → Updates** — one click updates it. If the channel is
unreachable, nothing breaks and nothing is shown.

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
| **Showreel video** | Customizer → **AK Studio** | Already wired to the showreel in the media library (`OFD29COMP…mp4`); swap or add an AV1 version + poster frame there any time. |

And before launch:

- The studio address is **ak@akbrand.studio** everywhere (templates,
  JSON-LD, the form's Mail recipient and failure message). To change it
  later: the `ak_studio_email()` helper in `functions.php` + the Contact
  Form 7 **Mail** tab.
- Social links render in the footer with empty targets — paste the real
  profile URLs into the `$ak_socials` array in `footer.php` (the spot is
  marked `EDIT ME`).
- The six case studies and four journal entries are labelled placeholders —
  rewrite them with real projects at your pace; the layouts hold.
- The footer is a template file: edit columns/links in `footer.php`. If you
  ever assign an Elementor footer template in Zeyna's options, it takes
  precedence automatically.
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
| **A designed footer** | `footer.php` override: outline-type marquee, Studio/Movements/Contact columns, © bar — replaces Zeyna's "powered by" line |
| **A real mobile menu** | Side panel with the mode switch inside, instead of Zeyna's collapsed list |
| **Chrome that cannot break** | Zeyna's `--mainColor`/`--secondaryBackground`/… variables are defined from the AK tokens, so the header and menu never depend on Redux being configured |
| **Demo-proof** | The theme forces its own header/footer even after a Zeyna demo import (which switches both to Elementor demo templates) |
| **Fullscreen showreel** | The reel starts as a measured swatch and grows to fill the screen as you scroll, then the site continues |
| **Self-updates** | The theme checks its update channel twice a day; WordPress shows the standard one-click update notice when a new build ships |
| The name, explained | The A–K monogram on About and "The name" section on Home: A is Andrii, K is Kostiantyn |
| Contact Form 7 form + styling | Imported with the content; found by slug at render time |
| A bespoke-page template | `AK — Bespoke page`, the pattern all the others follow |

---

## The platform cards are live

The "What we built" grid does not use photos: each card renders the
platform's **current front page** as an image (via WordPress.com's mShots
capture service — cached on their CDN, re-captured periodically). When one
of the sites changes its homepage, the card updates itself. One lazy image
per card, no scripts, no cost to this site's PageSpeed. The list of sites
lives in `front-page.php`.

---

## Light/dark: how the two systems agree

Zeyna has no visitor-facing dark switch — what its demos show as "dark" is a
one-time Redux preset. What Zeyna DOES have is a per-page **"switched"
layout** (an ACF field, `page_layout`, that flips the parent palette for
that one page). The child bridges both worlds:

- The **AK toggle** in the menu is the visitor's control, and an explicit
  choice always wins (it is remembered).
- A page marked "switched" in Zeyna's options opens in RUNWAY (dark) by
  default — the bridge reads that field and sets the initial mode.
- Every Zeyna palette variable (`--mainColor`, `--secondaryBackground`, …)
  is re-defined from the AK tokens in both modes, so the parent chrome,
  submenus and any Elementor block follow the switch automatically.

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
