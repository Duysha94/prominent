# Content deployment

How an AK Brand Studio release brings a WordPress install to its expected
state.

## The contract

Installing or updating the theme makes the database match the manifest of that
release. Content in the manifest exists; content the build used to own and no
longer ships is gone; nothing is ever created twice.

## Version detection

| Thing | Where |
|---|---|
| Installed release | `AK_CHILD_VERSION`, read from the `Version:` header of `style.css` at load |
| Last successful deployment | `akbrand_content_version` option |
| Completed one-time migrations | `akbrand_migrations` option — `id => ISO timestamp` |
| Run history | `akbrand_deploy_log` option — last 10 runs |
| Concurrency guard | `akbrand_deploy_lock` transient, 5 minutes |

The gate is the first thing in `ak_maybe_deploy()`:

```php
if ( get_option( AK_VERSION_OPTION ) === AK_CHILD_VERSION ) {
    return;   // the common path — one option read, then out
}
```

Hooked on **`admin_init`** *and* `after_switch_theme`. `admin_init` is the
load-bearing one: updating an already-active theme fires no switch hook at all,
so a deployment bound only to `after_switch_theme` would never run on an
update — the exact bug this system replaces.

## Manifest architecture

`inc/deployment/manifest.php` is the single canonical description of managed
state. Nothing else in the theme may define content; two files describing the
same page is how a build ends up creating it twice.

```
ak_manifest()
├── posts   ← ak_manifest_pages() + ak_manifest_journal() + ak_manifest_projects()
├── menus   ← ak_manifest_menus()   (menus and their items)
└── terms   ← ak_manifest_terms()
```

Every entry carries a `key` — `ak_home`, `ak_about`, `ak_nav_work`,
`ak_post_ad_size_first`. Filterable via `ak_manifest` and
`ak_manifest_projects`.

## Ownership markers

| Marker | On | Meaning |
|---|---|---|
| `_ak_managed` = `1` | post meta / term meta | This object belongs to the AK build |
| `_ak_seed_key` = `ak_about` | post meta / term meta | *Which* manifest entry it is |

**The seed key is the identity.** Titles and slugs are not: a title is editable
in wp-admin, and a slug silently becomes `about-2` the moment anything else
claims `about` — so a deployment keyed on either creates a duplicate the first
time an editor renames a page. The key never changes and is never displayed.

Changing a key means "delete the old object, create a new one". Reusing a key
for a different object corrupts the mapping. Do neither by accident.

## Reconciliation

Order is deliberate; each step depends on the one before.

| # | Step | Why here |
|---|---|---|
| 1 | **Migrations** | Ownership changes the rest depends on. Adopting old markers *before* reconciling is what stops previously-created pages being seen as unmanaged and duplicated |
| 2 | **Terms** | Posts reference them |
| 3 | **Posts** | Menus reference their IDs |
| 4 | **Menus** | Need the IDs from step 3 |
| 5 | **Retire** | Only once the manifest side is fully built, so a mid-run failure never deletes something before its replacement exists |
| 6 | **Invariants** | After adoption has claimed anything worth keeping, so what is left unmanaged really is residue |
| 7 | **Contact form** | Create-once; CF7 owns it thereafter |
| 8 | **Wiring** | Front page, posts page, privacy page, menu location |

Per object:

```
seed key found          → update in place, keep the ID
seed key found twice+   → keep the OLDEST, delete the rest      (self-heal)
not found, slug/alias   → adopt the existing post               (no duplicate)
not found at all        → create
managed, key not in manifest → delete
```

**Update in place, never delete-and-recreate.** The ID, the permalink, and
every inbound link survive the release.

## Migrations vs invariants

The distinction that testing forced, and the most important idea in the system.

|  | Migrations | Invariants |
|---|---|---|
| Run | Once per site, ever | Every deployment |
| Recorded in | `akbrand_migrations` | Nothing — they are re-checked |
| For | Transitions: "the old marker scheme becomes the new one" | Properties: "no unmanaged content exists in a managed build" |
| Example | `001_adopt_legacy_markers` | `ak_purge_unmanaged`, `ak_drop_placeholders` |

The legacy purge was written as a run-once migration first. Testing broke it
immediately: **activate the AK theme, then import a Zeyna demo afterwards**, and
the migration is already recorded as done — so the demo pages, the demo menu
and `Main Hub, NYC` all survive untouched. A demo can arrive at any time. "No
unmanaged content" is a property of the site, not an event in its history, so
it is re-enforced on every release.

## Deletion scope

**The engine may delete only what it can name.** Every object on the site falls
into exactly one of three namespaces, and only the first two are ever touched.

| Namespace | Identified by | Treatment |
|---|---|---|
| **AK** | `_ak_managed` | Reconciled strictly against the manifest. Obsolete AK objects are deleted |
| **LEGACY** | Positive evidence of the Zeyna / PeThemes / OCDI import | Purged on every deployment, including residue that reappears after one |
| **SYSTEM** | Everything else | Never deleted. Never even queried for deletion |

### The five rules

1. **AK-managed public content** — reconciled strictly against the manifest;
   objects whose seed key has left it are deleted.
2. **Confirmed Zeyna / PeThemes / OCDI legacy** — purged automatically, as an
   invariant re-checked on every release, because a demo can be imported after
   a deployment has already run.
3. **An existing object occupying a canonical AK slug** — adopted and
   reconciled, never duplicated.
4. **WordPress, system and plugin-owned objects** — *not* deleted merely for
   being absent from the AK manifest.
5. **Unknown objects outside the managed scope** — no blanket global delete
   query. They are reported to the owner and left alone.

### What counts as evidence

Legacy is proven, not inferred. **"Not in the manifest" is not evidence of
anything.** `ak_legacy_evidence()` accepts only:

| Signal | Why it is proof |
|---|---|
| **Vendor host in the `guid`** — `pethemes.com`, `themes.pethemes.com`, `zeyna.pethemes.com` | WordPress's WXR importer copies `<guid>` verbatim from the source site, so it survives editing, renaming and re-saving |
| **Vendor asset URL** in `post_content`, `_elementor_data` or `_elementor_page_settings` | Elementor stores absolute URLs; a demo page carries the vendor's domain in its own JSON |
| **Named by the demo's Redux config** as the header, footer, 404 or transition template | Captured by `ak_capture_redux_templates()` *before* those fields are cleared — otherwise clearing them would destroy the only proof that an `elementor_library` post is the demo's and not the owner's |

Evidence is **sticky**: once found it is written to `_ak_legacy`, because
Elementor rewrites its stored JSON on save and would otherwise launder it away.

A **menu** carries no provenance of its own, so it is judged by its contents: a
menu is legacy when it is not ours *and* at least one item points at a vendor
host or at a post already identified as legacy. A hand-built menu of ordinary
links is left alone.

### Hard limits

| Never touched | Why |
|---|---|
| Post types outside `ak_purgeable_post_types()` | A type not on the list is never queried, so an unknown plugin's content cannot be deleted even by accident |
| `attachment`, `wpcf7_contact_form`, `product`, `shop_order`, `wp_block`, `wp_template`, `nav_menu_item`, … | Owned by WordPress or another plugin, which manages their lifecycle |
| Front page, posts page, privacy page, WooCommerce pages | Referenced by a site setting — deleting one breaks the site. Applies **even inside the AK namespace**: an obsolete managed page that has since been wired to a setting is kept and reported, not removed |
| Attachments / media | A demo image and an owner's upload are indistinguishable. Deleting the owner's media is unrecoverable |
| Users, core settings, whole tables | No `TRUNCATE`, no `DROP`, no bulk `wp_options` deletion |

### Widgets are deactivated, not deleted

This build renders no widget areas, so a widget sitting in one is unreachable —
but *unreachable* is not *disposable*, and a widget instance is core-owned data
that may hold text nobody has another copy of. Placements are moved to
**Inactive Widgets**, which hides them and leaves them fully recoverable from
Appearance → Widgets.

### `pe-redux` is gated on evidence

The demo branding fields inside `pe-redux` are cleared **only when the site
actually shows demo residue elsewhere**. The option belongs to Redux, and the
child theme reads none of those fields — the footer, contact details and social
profiles all come from `inc/studio.php` — so clearing them is hygiene, not a
requirement, and hygiene is not a reason to overwrite something an owner may
have typed.

### Content outside every namespace is reported

`ak_observe_unclaimed()` lists pages and projects that are neither ours nor
identifiably legacy. Nothing there is ever deleted; it appears in the admin
notice as *"items outside the AK manifest — left untouched"*, so the owner
knows it exists and can decide for themselves. Silence would be its own kind of
dishonesty on a build that advertises itself as managed.

## Failure handling

```php
$report = ak_deploy();            // wrapped in try/catch — a fatal here
                                  // must never take wp-admin down
ak_deploy_log( $report );
if ( empty( $report['errors'] ) ) {
    update_option( AK_VERSION_OPTION, AK_CHILD_VERSION );
}
```

**Fail closed.** The version marker advances only after a clean run, so a
failed deployment retries on the next admin request rather than declaring a
broken state deployed. Migrations record individually and the chain stops at
the first failure, because a later migration may assume an earlier one
completed.

## Logging

- `akbrand_deploy_log` — last 10 runs: from, to, ok, counts, full detail.
  Bounded, so it cannot grow without limit; `autoload = false`.
- `error_log()` on failure, because a site whose deployment is failing should
  say so where a developer already looks.
- An admin notice after each run, listing what was created, updated and
  **deleted** — a system with delete rights owes the owner that account.

## Idempotency

Guaranteed by: seed-key identity, update-in-place, duplicate self-heal, and
adoption before creation. Verified, not assumed — `T1` in the suite runs three
deployments and asserts the content state is byte-identical.

One real churn bug was found this way: the purge cleared `pe-redux` fields that
`inc/setup.php` *filters* at runtime, so the filtered read reported them set
again and every deployment rewrote the option forever. Fixed by reading the
option raw, and by removing the filter-governed keys from the purge list.

## Theme update behaviour

1. Owner uploads the new theme zip (or the self-updater pulls it).
2. Next wp-admin request: `admin_init` sees the version mismatch.
3. Lock taken — concurrent requests during the same update do not race.
4. Migrations → reconcile → retire → invariants → wiring.
5. Rewrite rules flushed.
6. On success only: marker advances, notice shown.

## Preventing duplicates — every mechanism

| Mechanism | Prevents |
|---|---|
| Seed-key lookup | A renamed page being re-created |
| Duplicate collapse | Copies a buggy earlier release left behind |
| Slug/alias adoption | `about-2` when something already holds `about` |
| Menu items keyed too | Re-ordering in wp-admin reading as "missing" |
| Term adoption by name | A second "Strategy" category |
| Deploy lock | Two concurrent requests both deploying |
| Version gate | Any work at all on a normal page load |

## Responsibility

Everything above currently lives in the **theme**, under
`inc/deployment/`. That is the wrong long-term home — see
`THEME-PLUGIN-ARCHITECTURE.md`. Deployment infrastructure and the project data
it manages should outlive a theme switch, and today they would not.

## File map

| File | Role |
|---|---|
| `inc/deployment/registry.php` | Ownership markers, seeded lookups, duplicate detection |
| `inc/deployment/manifest.php` | The canonical desired state |
| `inc/deployment/migrations.php` | One-time migrations **and** the invariants |
| `inc/deployment/deploy.php` | The engine, the gate, reconcile, retire, logging |
| `inc/deployment/notice.php` | The admin report |
| `wordpress/tests/deployment-test.php` | 14 assertions against a real install |
