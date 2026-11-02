# WordPress deliverable — AK Brand Development Studio

The launch site is two pieces that install like any theme and plugin:

| Piece | What it is |
|---|---|
| `ak-brand-core/` → **AK Brand Core** plugin | Everything the site *knows*: studio details, the six services, the platforms, the project model behind `/work/`, the project brief form and its inbox, search and sharing data (meta, Open Graph, JSON-LD), cookie consent, and the **launch content installer** with all copy, photographs and films. |
| `ak-studio/` → **AK Studio** theme | Everything the site *looks like*: the kinetic hero reel, page templates for home, services, service, studio, contact, show in London, work, project, journal, article, search and 404, and the motion layer (GSAP, ScrollTrigger, Lenis — bundled, no CDN). |

Build the two ZIPs with `./build-zips.sh` (they land in `dist/`, which is not committed).

## Launch, step by step

1. **Back up the live site** (database and `wp-content/uploads`). The current site runs the Zeyna child theme with Elementor; nothing below deletes it, but a backup makes every step reversible.
2. **Plugins → Add New → Upload** `ak-brand-core.zip`, then **Activate**.
   The plugin is about 24 MB because it carries the launch photographs and films. If the host's upload limit is lower, copy the `ak-brand-core` folder to `wp-content/plugins/` over SFTP and activate it from the Plugins screen.
3. **Appearance → Themes → Add New → Upload** `ak-studio.zip`, then **Activate** (about 16 MB — the hero reel footage is bundled with the theme).
4. **Tools → AK launch content → Install.**
   It imports 68 media files, creates 14 pages, 10 projects and 6 journal entries, builds the three menus, sets the front page and the journal, and assigns the privacy policy page. It runs in small steps, so shared hosting does not time out.
   - On the live site, the pages **Home, About, Services, Journal and Contact already exist** (built with Elementor). The installer lists them and keeps them unless you tick **Replace them with the launch copy** — tick it for the launch. Old content stays in revisions.
   - Journal entries that already exist with the same address are kept as they are.
   - Running it again later only adds what is missing and refreshes items nobody has edited since.
5. **Settings → AK Studio** — check the studio email (`ak@akbrand.studio`), add a phone number, the studio's own social profiles and the legal entity details when you have them. Empty fields stay hidden on the site.
6. **Email delivery.** Every brief is saved under **Briefs** in the dashboard first, so no lead is ever lost. To also receive them by email, install an SMTP plugin (for example *WP Mail SMTP*) and send yourself a test brief from `/contact/`. Until mail works, the dashboard shows a warning.
7. **Analytics.** If Google Site Kit stays active, the cookie banner appears automatically and Google Analytics only stores cookies after a visitor accepts (Google Consent Mode). Nothing else to configure.
8. When everything looks right, the Zeyna parent theme, the old child theme and Elementor can be deactivated.

## After launch

- **The next event** (home page, Show in London) is driven by **Settings → AK Studio → Next event**. Until 8 November 2026 it shows London Fashion Day Season 8 as upcoming with guest registration; after the date it switches itself to “Latest season” and links to the Season 8 project. Enter Season 9 there when it is announced.
- **Projects** live under **Work** in the dashboard: title, excerpt (the lead), content, featured image (the cover) and the *Project details* box (date, venue, our part, credits, gallery, film, loop, “show on the home page”).
- **Pages** have a *Page header* box: eyebrow, display heading (`*word*` sets it in the orange italic) and, for service pages, the discipline.
- **Briefs** have a status (New, Replied, Won, Closed). Briefs that are not “Won” are deleted automatically after the retention period in **Settings → AK Studio** (12 months by default, as the privacy policy says). Data export and erasure requests under **Tools → Export/Erase Personal Data** include briefs.

## Before launch, confirm

Everything on the site comes from the studio's own record (akbrand.studio, londonfashionday.co.uk, ofd.org.ua). A few things only the founders can confirm:

- **Legal details** for the footer and the privacy policy: company name, number and registered address (Settings → AK Studio → Legal details).
- **The studio's own Instagram / LinkedIn** — the footer links to London Fashion Day, Odessa Fashion Day, KEKA and Cool'baba until they are added.
- **Budget brackets** in the brief (Under £3,000 · £3,000–£10,000 · £10,000–£30,000 · Over £30,000) — change them if they do not match how you quote.
- **Headline numbers**: 15+ years producing fashion events, 32 seasons of Odessa Fashion Day, 8 seasons of London Fashion Day (Season 8 is on 8 November 2026). OFD's own site says "since 2010" on the home page and "2009" on the About page; the studio site uses 2010.
- **Photographs and films** are the platforms' own campaign and runway material, credited in their captions. If any designer, model or photographer agreement limits use on the studio's commercial site, swap those images in the media library.

## Legacy

`ak-zeyna-child/` and `import/ak-content.xml` are the previous Zeyna-based build, kept for reference and for rolling back.
