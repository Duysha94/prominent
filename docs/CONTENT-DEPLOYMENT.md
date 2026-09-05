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
