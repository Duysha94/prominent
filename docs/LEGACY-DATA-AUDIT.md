# Legacy data audit

Where the Zeyna/demo artefacts actually come from, established from the parent
theme's own source rather than inferred.

## Correcting the previous claim

The earlier audit said the demo strings "are not in this repository, therefore
they are in the database". That inference was not proof, and the pushback was
right. This document replaces it with the mechanism, read out of
`zeyna/inc/demo-import.php`.

## The mechanism

Zeyna ships a **One Click Demo Import** configuration. `ocdi_import_files()`
registers 39 demos on the `pt-ocdi/import_files` filter, each pointing at two
remote files:

```php
'import_file_url' => 'https://themes.pethemes.com/zeyna/demos/xml/agency.xml',
'import_redux'    => array( array(
    'file_url'    => 'https://themes.pethemes.com/zeyna/demos/redux/agency.json',
    'option_name' => 'pe-redux',
) ),
```

Running an import therefore writes to five places:

| # | Vector | What lands in the database |
|---|---|---|
| L1 | **WXR import** (`<demo>.xml`) | `wp_posts` — pages, posts, portfolio entries, attachments; `wp_postmeta` — every custom field including Elementor's `_elementor_data`; `wp_terms`/`wp_term_taxonomy` — categories, project categories, **nav_menu terms**; `wp_posts` again — `nav_menu_item` rows |
| L2 | **Elementor library** (inside the WXR) | `elementor_library` posts: the demo header, footer, 404 and page-transition templates that `pe-redux` then points at by ID |
| L3 | **Redux import** (`<demo>.json`) | The `pe-redux` option, **overwritten wholesale**. Holds `header_type`, `footer_template`, `loader_logo`, `transition_logo`, `transition_caption`, the contact block and the copyright line |
| L4 | **`ocdi_after_import_setup()`** | `theme_mods_zeyna[nav_menu_locations]` → the demo's "Menu 1"; `show_on_front`; `page_on_front` → the demo's "Homepage" |
| L5 | **OCDI's own bookkeeping** | `ocdi_importer_data` / `pt-ocdi_importer_data` options; widget placements in `sidebars_widgets` |

Companion plugins installed through TGMPA alongside it: `redux-framework`,
`pe-core`, `elementor`, `advanced-custom-fields`, `one-click-demo-import`,
`revslider`, `woocommerce`, `material-design-icons-for-elementor`,
`woocommerce-product-tabs`.

## Mapping the reported strings

| Reported | Most likely vector | Confidence |
|---|---|---|
| `Main Hub, NYC` | L3 (Redux contact block) or L2 (Elementor footer template) | **Vector certain, exact row not verifiable from here** |
| `ZEYNA CREATIVE` | L3 (`footer_copyright`) or L2 | Same |
| `Humana` | L1 — a demo project/client inside the WXR | Same |
| `PeThemes` | L1 or L3 — vendor branding | Same |
| Demo pages, menu items | L1 + L4 | Certain |
| ThemeForest placeholder clients | L1 | Certain |

**Why "exact row not verifiable":** `themes.pethemes.com` is denied by this
environment's egress proxy, so the demo XML and JSON cannot be downloaded and
read; and there is no access to the production database. The *vectors* are
certain because they are read from the parent theme's source. The specific rows
are not, and this document does not pretend otherwise.

## What that means for the cleanup

The purge is written against **provenance classes, not string matches**. A
string list only removes the phrases somebody happened to notice; a class
removes the artefact whatever it happens to say — including the demo strings
nobody has reported yet, and including the ones inside a demo that has not been
imported yet.

`ak_purge_unmanaged()` in `inc/deployment/migrations.php` removes, on every
deployment:

| Target | Rule |
|---|---|
| `page`, `post`, `portfolio`, `elementor_library`, `e-landing-page` | Only rows carrying **positive legacy evidence**: a vendor host in the `guid`, a vendor asset URL in the body or in `_elementor_data`, or a template the demo's Redux config names |
| Navigation menus | Only menus that are not ours **and** contain an item pointing at a vendor host or at an identified legacy post |
| `pe-redux` demo branding | `404_page_template`, `loader_logo`, `transition_logo`, `transition_caption`, `transition_repeater_captions`, `footer_copyright`, `footer_text`, `contact_address`, `contact_phone`, `contact_email`, `social_links` cleared. **The option itself is kept** — deleting it would reset every legitimate Zeyna setting |
| OCDI bookkeeping | `ocdi_importer_data`, `pt-ocdi_importer_data`, and the `current_` variants |
| Widget placements | Moved to **Inactive Widgets**, not deleted. This build renders no widget areas, but a widget instance is core-owned data that may hold text nobody has another copy of |

### Deliberately NOT touched

| Not touched | Why |
|---|---|
| **Attachments / media** | A demo image and an owner's upload are indistinguishable without provenance markers. Deleting the owner's media is unrecoverable, so the risk is not taken. Orphaned demo files stay on disk; they cost storage, not correctness |
| **Users** | Never |
| **Core options** other than the named ones | Never |
| **Whole tables** | No `TRUNCATE`, no `DROP`, no bulk `wp_options` deletion |
| **The privacy policy page** | Spared by the purge, then *adopted* as `ak_privacy` so the build owns it rather than adding a second one |

### Scope — corrected in v1.4.1

An earlier version of this document said "any page, post or project not in the
manifest is deleted". **That was too broad and has been fixed.** It made the
engine a database-wide garbage collector: a WooCommerce page, another plugin's
landing page or a page an editor wrote yesterday would all have been removed,
purely for not appearing in a manifest that never claimed them.

Deletion now has an explicit namespace — see the *Deletion scope* section of
`CONTENT-DEPLOYMENT.md`. In short:

- **AK-managed** content is reconciled against the manifest and deleted when
  its seed key leaves it.
- **Confirmed legacy** content is purged — where "confirmed" means positive
  evidence of the demo import, not absence from the manifest.
- **Everything else** is left alone and reported to the owner.

Hand-made pages therefore survive a release. They are listed in the deployment
notice so you can remove them yourself if they do not belong.

## Verification performed

On WordPress 7.1.0 / PHP 8.4.19, against a fresh install seeded with fixtures
that reproduce every vector above (demo page, demo Elementor footer template,
demo menu with a `Humana Studio` item, `pe-redux` carrying `Main Hub, NYC` and
`ZEYNA CREATIVE`, OCDI bookkeeping option, demo widget placements, old-scheme
AK content, and a placeholder case study):

```
migrations: 001_adopt_legacy_markers — 1 adopted
created: 0  updated: 10  deleted: 6  healed: 1
  DEL unkeyed — Client Name (#26)
  DEL legacy page — Homepage (#22)
  DEL legacy page — Our Team (#21)
  DEL legacy menu — Menu 1
  DEL demo branding cleared from pe-redux
  DEL 2 demo widget placements
  HEAL ak_about (duplicate #25)
```

Final state: 6 pages, 4 posts, 1 menu, 6 nav items, every one seed-keyed;
`pe-redux` demo fields empty with the unrelated key preserved; OCDI option
gone; sidebars empty.

Full suite: `wordpress/tests/deployment-test.php` — **27 assertions, all
passing**, including nine that specifically prove the deletion scope: a
hand-written page, a plugin-referenced page, a plugin-owned post type and a
hand-built menu all survive, while a page with a vendor GUID, a page with a
vendor URL in `_elementor_data` and a menu linking to a vendor host are all
purged.
