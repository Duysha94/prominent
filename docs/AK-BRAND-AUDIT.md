# AK Brand Development Studio — production audit

Run against theme v1.3.1 on WordPress 7.1.0 / PHP 8.4.19, Chromium via
Playwright, at 320–1920 px. Every finding below was reproduced in a browser.

## Scope limits, stated first

Three parts of the brief could not be executed in this environment. They are
recorded here rather than quietly skipped.

| Blocked | Why | What it costs |
|---|---|---|
| **Phase 0** — install skills from ten GitHub repos | Egress proxy returns `403` for `github.com` and `api.github.com` | See `SKILLS-MANIFEST.md`. The installed suite covers most directions; GSAP-specific, Playwright-specific and Vercel design guidance are the real gaps |
| **Phase 1** — audit the live `akbrand.studio` | Same proxy denies the host (`connect_rejected`) | Everything below is audited on a local build of the **same theme**. Anything that lives only in the production **database** — demo pages, Elementor templates, Redux options, uploaded media — is invisible to me |
| **Phases 10–11, 31** — verify facts on the real project sites | No outbound HTTP at all | Case studies can only be written from facts already given in this project. Anything else is marked `NEEDS OWNER FACT` rather than invented |

The database limitation is the important one. Items the brief lists as visible
on the live site — `Main Hub, NYC`, Humana Studio links, `PeThemes`,
`ZEYNA CREATIVE`, ThemeForest placeholder clients — are **not in this
repository**. I searched the whole theme and import file:

```
$ grep -rniE "humana|pethemes|zeyna creative|main hub|themeforest" wordpress/
wordpress/ak-zeyna-child/README.md:49:   ... upload the Zeyna zip from your ThemeForest purchase.
wordpress/ak-zeyna-child/inc/setup.php:186: * demo content (the ZEYNA CREATIVE footer ...
```

Both hits are prose in a comment and an install instruction, not rendered
output. So those strings are coming from Zeyna's demo import sitting in the
production database. The theme cannot be patched to "not contain" them; it has
to **remove them from the database**. That is proposed as work item C-1.

## Findings

Severity: **S1** breaks the site or misleads a visitor · **S2** damages quality
or SEO · **S3** polish.

### Fixed in this pass

| # | Sev | Finding | Fix |
|---|---|---|---|
| F-1 | S1 | **Five dead social links on every page.** `footer.php` hardcoded `Instagram, Facebook, YouTube, LinkedIn, TikTok` with empty URLs and rendered each as `<a href="#" onclick="return false">`. Confirmed in-browser on all 8 routes | Networks moved to Customizer. A network with no URL now prints **no markup at all**. `inc/studio.php` |
| F-2 | S1 | **Founder names wrong throughout.** `Andrey Karakushan` / `Konstantin Lieontiev` in templates, footer, About, JSON-LD `Person` nodes and meta description | Corrected to **Andrii Karakushan** / **Kostiantyn Lieontiev**. Originally fixed in the 6 theme files; three files in the abandoned `src/` React prototype still carried the old spellings and were corrected later. `dist/` is untracked build output from that prototype and is not part of the deliverable |
| F-3 | S2 | **Business data hardcoded in four files.** Email in `functions.php`; `London, United Kingdom` and `London · Paris · Dubai` typed into `footer.php`, `template-contact.php` and `schema.php` independently. Changing the city meant four edits, and the schema could silently disagree with the footer | Single source of truth in `inc/studio.php`, read by footer, contact page and JSON-LD. Editable in Customizer |
| F-4 | S2 | **Zeyna's demo 404.** Parent template rendered "Oops! That page can't be found.", a search form and an Elementor template lookup | Own `404.php` in the studio's voice, routing to Work / Services / Journal / Contact |
| F-5 | S2 | **Empty `sameAs` in JSON-LD.** Organization schema would emit an empty array | `sameAs` now built from real profiles only, omitted when none are set |
| F-6 | S3 | **Primary `<nav>` had no accessible name.** Three `<nav>` landmarks per page; the two footer ones were labelled, the header one was not | `aria-label="Primary"` |
| F-7 | S3 | **Headings duplicated in the rendered DOM.** `data-ak-cut` clones each line into a second `<span>`; the clone is only `display:none`d when the animation *completes*. Screen readers were already correct (`aria-label` on the host, `aria-hidden` on the lines — verified in the accessibility tree), but anything reading `textContent` saw every heading twice, and a failed animation would leave the duplicate visible | Clone marked `aria-hidden="true"` at creation |

### Outstanding — needs the decisions in the redesign plan

| # | Sev | Finding |
|---|---|---|
| C-1 | S1 | **Zeyna demo content in the production database.** Not visible from here (see scope limits). Needs a theme-side purge that runs on update: retire demo pages, clear the Elementor header/footer/404 template IDs, blank the demo contact block and social fields in Redux |
| C-2 | S1 | **Six case studies are placeholders.** `Client Name` × 6, and every one carries **invented metrics** — `PRESS 38`, `PRESS 52`, `CVR +2.4pp`, `LCP 0.9s`. These are fabricated results on a live site and must go regardless of what replaces them |
| C-3 | S1 | Case study `<title>` is literally `Client Name – AK Brand Development Studio` — the post title is the client field |
| C-4 | S2 | **No case-study field architecture.** Phase 11 asks for Client / Year / Location / Disciplines / Challenge / Approach / Outcome / link / hero / gallery / quote / next-project. Current cases carry only headline, category, year, movements, summary and "measures" |
| C-5 | S2 | **Services page is four movements, not services.** Phase 13 lists ~13 real disciplines; the page states four and stops. No `/services/{movement}/` pages exist |
| C-6 | S2 | **No image slot system.** Case studies render no images at all (`img` count is 0 on `/work/` and every case page). Phase 12 needs defined ratios, focal points, fallbacks and Media Library fields |
| C-7 | S2 | Homepage platform cards use **WordPress.com mShots** to screenshot the ten platforms. Third-party request per card, and it fails closed in restricted networks (it fails here) |
| C-8 | S3 | Contact form is Contact Form 7 with a basic field set; Phase 18 asks for brand/budget/timeline/consent and proper states |

## What is already sound

Worth stating so the redesign does not spend effort re-solving it:

- **No horizontal overflow at any width**, 320→1920, across all six templates.
- **No JS errors** on any route.
- Every `<img>` has an `alt` attribute.
- Landmarks are correct: one `main`, one banner `header`, one `footer` per page.
- Titles and meta descriptions are per-page and distinct; breadcrumb JSON-LD present.
- Light/dark resolves before first paint from an inline head script — no flash.
- The mobile panel, the page-transition overlay and the dual logo were fixed in
  v1.3.0–1.3.1 and re-verified here.
- The parent theme is never edited. Every parent defect is overridden from the
  child by template override, filter or cascade — so a Zeyna update cannot
  destroy the work.
