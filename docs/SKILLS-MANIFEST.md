# Skills manifest — AK Brand Development Studio

Status of Phase 0 of the production brief, recorded honestly.

## The ten named repositories could not be installed

The brief asks for a skill stack assembled from ten GitHub repositories. **None
of them could be cloned in this session.** Outbound HTTPS runs through an egress
proxy that denies `github.com` and `api.github.com` at the organisation-policy
level:

```
$ curl -o /dev/null -w '%{http_code}' https://github.com/WordPress/agent-skills
403
$ curl -o /dev/null -w '%{http_code}' https://api.github.com/repos/WordPress/agent-skills
403
```

The proxy's own status endpoint confirms it: only `registry.npmjs.org`,
`pypi.org`, `files.pythonhosted.org`, `index.crates.io`, `proxy.golang.org` and
the Anthropic APIs bypass the gateway. Everything else needs an allow-list entry.

Not installed, and what each would have added over the stack below:

| Repository | Would have added |
|---|---|
| `WordPress/agent-skills` | Official WordPress rules — the brief's stated priority for architecture and security |
| `greensock/gsap-skills` | GSAP-specific idioms and plugin guidance |
| `lackeyjb/playwright-skill` | Playwright-specific test authoring patterns |
| `vercel-labs/agent-skills` | Vercel web design guidelines, named in Phase 5 |
| `PracticalSwan/agent-skills`, `JPeetz/agent-skills`, `mthines/agent-skills`, `wshobson/agents`, `emilkowalski/skills`, `Leonxlnx/taste-skill` | Unknown until readable — several are already mirrored in the suite below |

**To unblock:** allow `github.com` and `api.github.com` (or
`codeload.github.com` for tarballs) for this environment, and I will install the
selected skills into `.claude/skills/` and update this file with commits and
versions. Until then no `.claude/skills/` directory is created, because
committing an empty or fabricated one would misrepresent the project's state.

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
