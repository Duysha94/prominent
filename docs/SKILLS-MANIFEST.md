# Skills manifest — AK Brand Development Studio

Status of Phase 0 of the production brief, recorded honestly.

## Installed project skills — `.claude/skills/`

Installed 2026-11-02 from the ten repositories the brief names. GitHub is now
reachable over git from this environment, so the blocker recorded in the
previous version of this file is gone.

**Selection, not a dump.** The ten repositories hold ~620 skills. Every
installed skill's description is loaded into the agent's context, so
installing all of them would dilute routing and crowd out project context.
59 were kept: everything WordPress, GSAP and Playwright, plus the design,
animation, accessibility, performance and SEO skills that apply to this
project. Skipped: React Native / Expo / Swift, Vercel deploy tooling,
blockchain, Python, LLM, Figma/Stitch-specific and duplicate skills.

| Repository | Commit | Count | Skills |
|---|---|---|---|
| `WordPress/agent-skills` | `eff3785` | 19 | `blueprint`, `wordpress-router`, `wp-abilities-api`, `wp-abilities-audit`, `wp-abilities-verify`, `wp-block-development`, `wp-block-themes`, `wp-env`, `wp-interactivity-api`, `wp-patterns`, `wp-performance`, `wp-phpstan`, `wp-playground`, `wp-plugin-development`, `wp-plugin-directory-guidelines`, `wp-project-triage`, `wp-rest-api`, `wp-wpcli-and-ops`, `wpds` |
| `greensock/gsap-skills` | `aed9cfd` | 8 | `gsap-core`, `gsap-frameworks`, `gsap-performance`, `gsap-plugins`, `gsap-react`, `gsap-scrolltrigger`, `gsap-timeline`, `gsap-utils` |
| `lackeyjb/playwright-skill` | `dd47a6a` | 1 | `playwright-skill` |
| `vercel-labs/agent-skills` | `1a2c79d` | 4 | `composition-patterns`, `react-best-practices`, `react-view-transitions`, `web-design-guidelines` |
| `emilkowalski/skills` | `a81dbca` | 12 | `animate`, `animation-performance`, `animation-vocabulary`, `apple-design`, `break-ui`, `emil-design-eng`, `find-animation-opportunities`, `improve-animations`, `pick-ui-library`, `prototype`, `remove-animations`, `review-animations` |
| `Leonxlnx/taste-skill` | `12af471` | 1 | `taste-skill` |
| `PracticalSwan/agent-skills` | `6cf6351` | 3 | `core-web-vitals`, `php-development`, `seo` |
| `JPeetz/agent-skills` | `e711966` | 2 | `accessibility-compliance-audit`, `design-to-code` |
| `mthines/agent-skills` | `ab1b41c` | 3 | `ui-verify`, `ux`, `visual-design` |
| `wshobson/agents` | `7e2fb55` | 6 | `design-system-patterns`, `interaction-design`, `responsive-design`, `tailwind-design-system`, `visual-design-foundations`, `wcag-audit-patterns` |

Exact per-skill source paths: [`.claude/skills/SOURCES.tsv`](../.claude/skills/SOURCES.tsv).
Licences: [`.claude/skills/_licenses/`](../.claude/skills/_licenses/) — all MIT
or GPL-compatible except `vercel-labs/agent-skills` and `JPeetz/agent-skills`,
which ship no licence file at the repository root.

**Review before install:** bundled scripts were read for network, `eval` and
`child_process` use. Only local use was found (`wp-cli` inspection, the
Playwright runner, the taste-skill page inspector). No prompt-injection text.

**To update:** re-clone the repository, diff the skill directory against
`.claude/skills/<name>`, and bump the commit in `SOURCES.tsv`.

### Coverage gaps closed

- **GSAP** — was *Partial*; now `gsap-core`, `gsap-timeline`,
  `gsap-scrolltrigger`, `gsap-plugins`, `gsap-react`, `gsap-performance`,
  `gsap-utils`, `gsap-frameworks` (official GreenSock).
- **Playwright** — was *Partial*; now `playwright-skill`.
- **Visual regression** — was *Missing*; now `ui-verify` (screenshot-based UI
  verification). Still no pixel-diff baseline in CI.
- **WordPress official rules** — `WordPress/agent-skills` is installed in
  full; it is the brief's stated priority for architecture and security.

### Not installed: CLI tooling

WP-CLI, PHPCS + WordPress Coding Standards and PHPStan + WordPress stubs (which
`wp-wpcli-and-ops`, `wp-phpstan` and `wp-performance` call) were **not**
installed: Composer cannot authenticate to GitHub through the proxy, and
installing binaries from cloned repositories needs explicit approval.

## The stack actually in use

`design-director-suite` is installed and active at the user scope. It already
covers most of the directions the brief asks for. This is a mapping, not a
substitute claim — where the coverage is weaker than a named repository would
have been, the table says so.

| Direction required | Skill used | Coverage |
|---|---|---|
| WordPress development | `wordpress-router`, `wp-modern-baseline` | Full — router classifies the repo and selects specialists; baseline enforces WP 7.1+/PHP 8.3+ |
| Theme architecture | `wp-theme-development` | Full — classic/child theme, template hierarchy, enqueueing, parent-theme separation |
| Plugin development | `wp-plugin-development`, `wp-plugin-directory-guidelines` | Full — not currently needed; no plugin in this project |
| Security | `wp-modern-baseline`, `wp-quality-gate`, `/security-review` | Full — escaping, sanitisation, nonces, capabilities |
| Modern WordPress APIs | `wp-rest-api`, `wp-interactivity-api`, `wp-block-development`, `wp-abilities-api` | Full |
| Frontend / visual design | `impeccable`, `design-taste-frontend` | Full |
| Design systems | `impeccable` (`extract`), `artifact-design` | Full |
| Responsive design | `impeccable` (`adapt`), `ui-quality-gate` | Full |
| Mobile UX | `impeccable` (`adapt`) | Full for web |
| Design-to-code | `image-to-code`, `imagegen-frontend-web` | Full |
| Accessibility | `impeccable` (`audit`), `ui-quality-gate` | Full — WCAG 2.2 AA |
| Interaction design | `emil-design-eng`, `apple-design` | Full |
| Animations | `animate`, `review-animations`, `improve-animations`, `find-animation-opportunities` | Full |
| GSAP | `animate` | **Partial** — covers GSAP as one tool among several; the dedicated `greensock/gsap-skills` would go deeper on plugins and edge cases |
| Performance / Core Web Vitals | `wp-performance`, `impeccable` (`optimize`) | Full |
| Browser QA | `wp-environments`, `ui-quality-gate` | Full |
| Playwright | `wp-environments` | **Partial** — establishes the headless-capture approach; no Playwright-specific authoring skill. Playwright itself is installed and used directly |
| Visual regression | — | **Missing** — no skill. Screenshot comparison is done by hand against captures in the scratchpad |

## Routing decisions

Resolved in advance, per the brief's precedence rules:

1. **WordPress architecture and security** — `wp-modern-baseline` and
   `wp-quality-gate` win over any design skill. Where a design idea would need
   an unsafe query, an unescaped output or a parent-theme edit, the design idea
   changes.
2. **Brand brief over generic taste** — `docs/CONCEPT.md` and the client's
   stated direction (fashion editorial, not agency template) win over
   `impeccable`'s default preferences and over any "safe" pattern library.
   `minimalist-ui` and `industrial-brutalist-ui` are **not** used: both would
   impose a look the brand has not asked for.
3. **Parent-theme separation** — the child theme never edits Zeyna's files.
   Anything the parent does wrongly is overridden from the child, by template
   override, filter or cascade.
4. **`ui-quality-gate` and `wp-quality-gate` both run** before any completion
   claim, since every change here is both WordPress and UI.

## Where they are used

| Skill | Used for |
|---|---|
| `wp-environments` | Establishing that no WordPress runtime was reachable, then building the local WP 7.1.0 + SQLite + PHP 8.4 stack used for every check in this project |
| `wp-theme-development` | `header.php`, `footer.php`, `404.php`, page templates, the parent-override strategy |
| `impeccable` | The phone type scale and hierarchy rebuild (v1.3.1) |
| `wp-quality-gate` / `ui-quality-gate` | Pre-release verification of each version |

## Verification environment

Not a skill, but the thing that actually catches defects. Recorded here because
three rounds of static review missed what one real install found immediately:

- WordPress **7.1.0** from Packagist (`johnpbloch/wordpress-core`)
- SQLite drop-in from `@wp-playground/wordpress-builds`
- PHP **8.4.19** built-in server, `WP_DEBUG` on
- Chromium via Playwright for every rendered check

`https://akbrand.studio` itself is **not reachable** from this environment — the
same proxy denies it — so every audit in this repository is run against this
local build of the same theme, never against the production site.
