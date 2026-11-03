# WordPress deliverable — AK Brand Development Studio

The site is two pieces that install like any theme and plugin. Every page, project and journal entry is built from **blocks in the page editor**: all texts, headings, films, photographs, captions, buttons and links are edited there — nothing a visitor reads is written into the theme.

| Piece | What it is |
|---|---|
| `ak-brand-core/` → **AK Brand Core** plugin | What the site *knows and does*: the AK blocks (Hero reel, Page header, Moving band, Services list, Work, Facts, Event, Project brief form, Latest journal entries), the block styles, the project model behind `/work/`, the brief form and its inbox, search and sharing data (meta, Open Graph, JSON-LD), cookie consent, and the **launch content installer** with all copy, photographs, website screenshots and films. |
| `ak-studio/` → **AK Studio** theme | How the site *looks and moves*: header with the AK logo, footer, the design of every block on the site and in the editor, starter patterns for new pages and projects, and the motion layer (GSAP, ScrollTrigger, Lenis — bundled, no CDN). |

Build the two ZIPs with `./build-zips.sh` (they land in `dist/`, which is not committed).

## Launch, step by step

1. **Back up the live site** (database and `wp-content/uploads`). Nothing below deletes the current theme or Elementor, but a backup makes every step reversible.
2. **Plugins → Add New → Upload** `ak-brand-core.zip`, then **Activate**. It is about 115 MB because it carries the photographs (originals, up to 2560 px) and the films (1080p); if the host's upload limit is lower, copy the `ak-brand-core` folder to `wp-content/plugins/` with the hosting file manager or SFTP and activate it from the Plugins screen.
3. **Appearance → Themes → Add New → Upload** `ak-studio.zip`, then **Activate** (about 48 MB — the hero reel films are bundled with the theme in 1080p, with lighter 720p versions for phones).
4. **Tools → AK launch content → Install.** It imports the media, creates 15 pages, 21 projects (10 websites, seasons, shoots and films, the platforms and KEKA) and 6 journal entries, the shared *Call to action* and *Site footer* patterns, the two menus, the front page, the journal, the site icon and the privacy page. It runs in small steps, so shared hosting does not time out.
   - Pages that already exist at the same address (on the live site: Home, About, Services, Journal, Contact) are kept unless you tick **Replace them with the launch copy** — tick it for the launch. Old content stays in revisions.
   - Running it again later only adds what is missing and refreshes items nobody has edited since.
5. **Settings → AK Studio** — check the studio email (`ak@akbrand.studio`); add a phone number and the Instagram, Facebook and LinkedIn addresses when you have them (until then the footer names them as plain text; each becomes a link once its address is saved). Legal details wait for the company registration; empty fields stay hidden.
6. **Email delivery.** Every brief is saved under **Briefs** in the dashboard first, so no lead is lost. Then two emails go out: the brief to the studio (*Send briefs to*, or the studio email) and an automatic reply to the client with a copy of what they sent. Send a test brief from `/contact/` with your own address in it and check that both arrive (and not in spam). If they do not, install an SMTP plugin (for example *WP Mail SMTP*) with the studio mailbox. Until mail works, the dashboard shows a warning.
7. **Spam protection (Cloudflare Turnstile).** The brief form only accepts a brief after Cloudflare's check. If the *Simple Cloudflare Turnstile* plugin is set up (site key and secret key in its settings), the form uses it automatically — nothing to paste: the `[cf7_simple_turnstile]` code is only for Contact Form 7 forms, which this site does not use. Set its **Theme** to *Dark* to match the site. Without that plugin, put the two keys from Cloudflare (Turnstile → your site) in **Settings → AK Studio → Project briefs**.
8. **Analytics.** If Google Site Kit stays active, the cookie banner appears automatically and Google Analytics only stores cookies after a visitor accepts (Google Consent Mode).
9. When everything looks right, the old theme and Elementor can be deactivated.

## Updating a site that already runs an earlier version

Upload the new `ak-brand-core.zip` and `ak-studio.zip` (Plugins / Themes → Add New → Upload → **Replace current with uploaded**), then run **Tools → AK launch content → Install** again. It swaps the photographs and films for the original-quality files — same items in the Media Library, so every page that uses them stays as it is — sets the focus points, and refreshes the pages, projects and entries nobody has edited since the last install. Anything you have edited is kept and listed.

## Editing the site

**Pages → (page) → Edit.** The editor shows the page as it looks on the site.

- **Type straight into the blocks.** Headings, paragraphs, button labels, the hero lines, the page header — click and type. In large headings, *italic* words turn into the orange serif accent.
- **Films, photographs, links and numbers** of the AK blocks are in the right-hand sidebar (**Block** tab): the hero reel's films and posters, captions, numbers and button links; a page header's photo or silent film; the event's date, image and buttons; which projects a Work block shows.
- **Looks** are chosen under **Block → Styles**: paragraphs (Eyebrow, Lead, Letter plate), headings (Display, Display XL), lists (Steps, Steps in a row, Cards, Timeline, Marquee, Credits), groups (Section, Statement, Panel, Call to action, Founder), columns (Split), galleries (Masonry with lightbox) and videos (Film with play button).
- **Add a section:** the **+** inserter → **Patterns → AK Studio** (heading and text, projects, steps, questions, photographs, project brief).
- **Shared blocks:** *Call to action* and *Site footer* are synced patterns — edit them once under **Appearance → Patterns** (or from any page that uses them) and every page updates.
- **Menus:** **Appearance → Menus** — *AK Primary* is the main menu, *AK Call to action* is the orange header button (its first item's label and link).
- **Logo:** the header shows the full AK logo with the name in white (it is built into the theme, so it always reads on the dark site); the sign-in screen uses the dark-text version. Both versions, as SVG and transparent PNG, are in `wordpress/brand/` for print, social media and documents. The browser icon is set in **Appearance → Customize → Site Identity**.
- **What stays in frame:** every photograph has a focus point (set at launch on the main face), used wherever the image is cropped — cards, headers, the event. Change it in **Media → (image) → Focus point**, or click the subject in the Page header block's sidebar.

### A new service

**Pages → Add New** → choose **Service page** → set **Parent: Services** and an **Order** (Page panel). The service joins the Services list on the home page and the Services page, and the brief form's checkboxes, automatically. Its name in those lists is the **Short title** in the sidebar. At the foot of the page, the Work block shows related projects: pick the project type or service in its sidebar.

### A new project

**Work → Add New** → choose **Website project** or **Shoot, show or brand project**, then in the sidebar:

- **Featured image** — the card picture;
- **Project card** — card title, label, city, year or status (e.g. “In development”), event date (a future date shows “Upcoming”) and *Show on the home page*;
- **Project type** — Websites, Photo & film, Shows & events, Brands, Platforms;
- **Disciplines** — the services involved.

The project then appears by itself on **Work** (with its filter, e.g. `/work/?type=website`), on the service pages whose Work block shows that type or service, and on the home page.

### Website screenshots

Each website project shows the site in a browser window and on a phone. The frames are drawn by the site — you only upload plain screenshots, in the **Website screens** block on the project page:

- click the browser window to add or replace the **computer** screenshot — the first screen of the site in a wide browser window (about 1440 px wide, without the browser's own toolbar; on a Mac ⌘⇧4 then drag over the page, on Windows Win+Shift+S);
- click the phone to add or replace the **phone** screenshot — an ordinary screenshot taken on the phone;
- in the block's sidebar: the address shown in the browser bar, the colour of the glow behind, and **More pages** (more computer and phone screenshots shown below).

The project's card on Work, on the home page and on the service pages uses the same two screenshots, so a new screenshot updates everywhere at once.

### Events

The **Event** block (home page, Show in London) shows the event as upcoming until its date, then as the latest season. To announce the next season, edit the block on both pages: date, title, venue, image and buttons.

### Briefs

Briefs arrive under **Briefs** with a status (New, Replied, Won, Closed). The client gets an automatic reply at once: its subject and text are in **Settings → AK Studio → Project briefs** (with placeholders such as `{name}` and `{summary}`), and it can be switched off there. It comes from the studio email, and the client's answer to it goes to the studio. Briefs not marked “Won” are deleted automatically after the retention period in **Settings → AK Studio** (12 months by default, as the privacy policy says). Data export and erasure requests under **Tools → Export/Erase Personal Data** include briefs.

## Agreed with the founders

- **One name everywhere: AK Brand Development Studio.** The company is being registered as *AK Brand Development Studio Ltd*. Add the legal name, company number and registered office in **Settings → AK Studio → Legal details** only once the company exists — using “Ltd” before incorporation is not allowed in the UK. Then the footer and the privacy policy name the company automatically.
- **Instagram, Facebook and LinkedIn** are named in the footer as plain text until their addresses are added.
- **No prices or budget brackets on the site.** Budgets are discussed on request: every enquiry gets a written proposal.
- **Rights:** the studio holds the rights and model agreements for every photograph and film used.
- **The studio's role:** the studio **manages** London Fashion Day and Odessa Fashion Day (it did not create them); it **designed and built** the websites; it **directed** the shoots, campaigns and films.
- **Seasons:** Odessa Fashion Day — 33 seasons since 2010 (the 33rd on 6–8 November 2026) and SUMMER WEEKEND by OFD since 2015; London Fashion Day — 8 seasons since 2023 (the 8th on 8 November 2026).
- **Websites in the portfolio:** the nine sites from the previous build plus **showmeyournails.co.uk** (the Marylebone salon) — ten in all; the `.com` address is not shown. When the salon's full site goes live, replace its screenshots (they show the current holding page).

Still worth a look: the hero numbers (15+ years, 10 websites, 40+ runway seasons) and Odessa Fashion Day's founding year (2010 on the site; OFD's own pages say 2010 and 2009).

## Legacy

`ak-zeyna-child/` and `import/ak-content.xml` are the previous Zeyna-based build, kept for reference and for rolling back.
