# Theme / AK Brand Core — the proposed boundary

**Status: proposal. No code has been moved.** Everything described here as
"Core" currently lives in the theme.

## The test

One question decides every line: **would this need to survive switching to a
different theme?**

- Presentation dies with the theme. That is what a theme is.
- The studio's projects, its business data and the record of which release is
  deployed must not.

## Where things sit today

| Concern | Today | Belongs in | Severity if left |
|---|---|---|---|
| Templates, CSS, motion, design system | Theme | **Theme** | — correct |
| Header, footer, 404, page templates | Theme | **Theme** | — correct |
| Light/dark mode | Theme | **Theme** | — correct; it is a presentation concern |
| `portfolio` CPT + `project-categories` | Theme (fallback) *and* Pe Core plugin | **Core** | **High** — switch theme and the projects become unreachable |
| Case-study fields (`ak_headline`, `ak_year`, measures) | Theme (`inc/case-meta.php`) | **Core** | **High** — the data survives, the meaning does not |
| Business data (email, city, socials) | Theme (`inc/studio.php`, theme mods) | **Core** | **High** — theme mods are *per theme*. Switch theme and every value silently reverts to its default |
| JSON-LD / structured data | Theme (`inc/schema.php`) | **Core** | Medium |
| Content manifest | Theme (`inc/deployment/manifest.php`) | **Split** — see below | Medium |
| Deployment engine | Theme (`inc/deployment/`) | **Core** | **High** — deactivate the theme and the site can never be brought to a known state again |
| Self-updater | Theme (`inc/updates.php`) | **Theme** | — correct; it updates the theme |

### The sharpest problem: `get_theme_mod()`

`inc/studio.php` stores the studio's email, city, country, cities line and six
social URLs as **theme mods**. Theme mods are namespaced per theme
(`theme_mods_ak-zeyna-child`). Switch or rename the theme and every one of them
reverts to its hardcoded default — the site would go on claiming
`ak@akbrand.studio` and London whether or not that is still true.

It is a genuine single source of truth *within the theme*, which is what was
asked for and what was built. It is not durable business data. Moving these to
`wp_options` under an `akbrand_*` prefix, owned by Core, fixes it.

## Value classification

Requirement 8 asked for every value in `inc/studio.php` to be classified.

| Value | Class | Editable in admin | Storage today | Storage proposed |
|---|---|---|---|---|
| `name`, `tagline` | Editable setting | ✅ Customizer | theme mod | `akbrand_studio` option |
| `email` | Editable setting | ✅ Customizer | theme mod | `akbrand_studio` option |
| `phone` | Editable setting | ✅ Customizer | theme mod | `akbrand_studio` option |
| `city`, `country` | Editable setting | ✅ Customizer | theme mod | `akbrand_studio` option |
| `cities` | Editable setting | ✅ Customizer | theme mod | `akbrand_studio` option |
| 6 social URLs | Editable setting | ✅ Customizer | theme mod | `akbrand_studio` option |
| `ak_logo_light` / `ak_logo_dark` | Editable setting (asset) | ✅ Customizer | theme mod | stays a theme mod — a logo *is* presentation |
| Hero video / poster | Editable setting (asset) | ✅ Customizer | theme mod | stays a theme mod |
| Location string | **Derived** (`city` + `country`) | n/a | computed | computed |
| Social list order | **Immutable build config** | ❌ | `ak_social_networks()` | code |
| Manifest seed keys | **Immutable build config** | ❌ | code | code |
| Page titles, slugs, journal bodies | **Managed seeded content** | Editable, then overwritten next release | manifest | manifest |
| Case studies, project metadata | **Project content** | ✅ wp-admin | posts + meta | posts + meta, Core-owned |

Every value that can change without a code release is already editable from
wp-admin. The remaining gap is **durability**, not editability.

## Where the deployment engine belongs

**AK Brand Core**, with one part staying in the theme.

The engine manages the CPT, the project data and the business data — all of
which are Core's. It must also keep working when the theme is switched, and it
cannot if it lives in the theme's `functions.php` chain.

The split:

| Part | Owner | Reason |
|---|---|---|
| Engine, registry, migrations, invariants, logging | **Core** | Must outlive any theme |
| Version gate | **Core**, watching both versions | A release is a theme+plugin pair; either changing should deploy |
| Manifest: pages, menus, business data, projects | **Core** | The content is Core's |
| Manifest: page **templates** (`template-about.php`) | **Theme**, contributed via the `ak_manifest` filter | Only the theme knows its own template files exist |

The `ak_manifest` filter is already in place, so the theme can contribute its
presentation-specific entries to a Core-owned manifest without Core knowing
anything about templates. That is the seam to build along.

### Version strategy across two packages

Today: one version, `AK_CHILD_VERSION` from `style.css`.

Proposed: Core owns `akbrand_content_version`, and the gate compares a
composite — `core_version . ':' . theme_version` — so a release that changes
either package deploys. The user installs one "AK Brand Studio" package
containing both; the versions move together.

## Migration path

Deliberately staged. Each step is independently shippable and reversible.

| Step | Move | Risk |
|---|---|---|
| 1 | Create the plugin shell, no behaviour | None |
| 2 | CPT + taxonomy registration → Core (theme keeps its `post_type_exists()` fallback for one release) | Low |
| 3 | Business data → `akbrand_studio` option, with a one-time migration reading the existing theme mods so nothing is lost | Low — needs the migration to be right |
| 4 | Deployment engine → Core; theme contributes template entries through `ak_manifest` | Medium — this is the load-bearing move |
| 5 | Case-study fields + schema → Core | Low |

**Not started, per the brief.** Step 3 is the one worth doing soonest: it is
small, and it is the difference between the studio's contact details being
durable data and being a theme setting.

## What must not move

- Templates, CSS, JS, fonts, the motion layer, the design system.
- Light/dark mode. It is presentation, and it is correct in the theme.
- The self-updater — it updates the theme, so it belongs to the theme.
- Zeyna parent-theme overrides. They only make sense while this theme is active.
