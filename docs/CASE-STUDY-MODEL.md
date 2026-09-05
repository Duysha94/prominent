# Case Study content model

A structured record, not a long page. The point of a model is that a
three-sentence project and a full editorial case study come out of the **same
fields** and both look deliberate.

> **Superseded in part by `CAPABILITIES-SYSTEM.md`.** That document is now
> canonical for the taxonomies, the presentation modes, the conditional admin
> model and the modular builder. This file remains the field-level reference.
> Two registers became three; one flat field set became seven type-specific
> panels.

## The three relationships

Backend names are administrative; front-end names are editorial.

| Backend | Front end | Meaning |
|---|---|---|
| **AK Owned** | **FOUNDED** | Created, owned, developed or operated by the studio |
| **Client** | **COMMISSIONED** | Work performed for an external client |
| **Collaboration** | **IN COLLABORATION** | Developed jointly; authorship shared |

Three, not two. An event AK creative-directed with a partner is honestly
neither founded nor commissioned, and forcing it into either would misstate
the authorship.

**Why FOUNDED / COMMISSIONED rather than "The House / Selected Work".** "The
House" is a metaphor the reader has to decode; "Founded" is the fact itself,
and it is the studio's actual differentiator — the homepage already says
*founded, not hired*. It is also honest in a way a metaphor is not: nobody can
mistake what it claims.

Implemented as the taxonomy `ak_register` with exactly two terms, so it filters,
archives and stays queryable. Not a post meta field, and **not two post types** —
one project is one project; only its relationship differs.

### Register assignments

| Project | Register | Basis |
|---|---|---|
| London Fashion Day | FOUNDED | Founded and produced by Kostiantyn Lieontiev — stated in the brief |
| Odessa Fashion Day | FOUNDED | Same |
| Cool'baba | FOUNDED | Founded by Andrii Karakushan — stated in the brief |
| KEKA | **AK Owned** | ✅ Confirmed in the scope correction. The earlier conflict is resolved |
| Fashion Frontier | **AK Owned** | ✅ Confirmed |
| Prominent Magazine | **AK Owned** | ✅ Confirmed |
| Utrend Store | **AK Owned** | ✅ Confirmed |
| Wolax | **Client** | ✅ Confirmed |
| Lenie Boya | **Client** | ✅ Confirmed |
| Show Me Your Nails | **Client** | ✅ Confirmed |

All ten relationships are now established. What remains absent — descriptions,
dates, deliverables, results — stays absent until supplied.

Nothing is assigned on assumption. An unassigned project simply does not
publish.

## Fields

Legend: **R** required · **O** optional · **T** taxonomy · **RPT** repeater ·
**D** derived · Owner: **Core** (data, survives a theme change) ·
**Theme** (presentation only).

### Identity

| Field | Key | Type | Req | Owner | Notes |
|---|---|---|---|---|---|
| Project title | `post_title` | text | **R** | Core | The real name |
| Short title | `ak_short_title` | text | O | Core | For the index and nav when the full name is long. Falls back to the title |
| Slug | `post_name` | slug | **R** | Core | |
| Project code | `ak_code` | text | **D** | Core | `AK·F·19·001` — register initial, year, sequence. Generated, overridable. This is the tech-pack spine of the whole system |
| Relationship | `ak_relationship` | **T** | **R** | Core | AK Owned \| Client \| Collaboration |
| Project type | `ak_project_type` | **T** | **R** | Core | Branding · Personal Branding · Photography · Film · Event · Fashion Production · Campaign · Website / Digital · Advertising · Integrated. **One per project** — it selects the presentation mode and the admin panel |
| Capabilities | `ak_capability` | **T** | O | Core | Many-to-many. What AK actually delivered. Filterable without changing the type |
| Client / owner | `ak_client` | text | O | Core | For FOUNDED this is the studio or a founder |
| Location | `ak_location` | text | O | Core | |
| Year | `ak_year` | text | **R** | Core | `2019` or `2019 →` for something ongoing |
| Season | `ak_season` | text | O | Core | `SS26`. Fashion-native, empty for non-seasonal work |
| Services | `ak_service` | **T** | O | Core | Shared vocabulary with `/services/` — the link between a project and a capability |
| URL | `ak_url` | url | O | Core | |
| Featured | `ak_featured` | bool | O | Core | Surfaces on the homepage |

### Narrative

Every one optional. A case with only `challenge` is a valid short case; a case
with all six is a full editorial one. **Absent sections do not render** — no
empty headings, no "coming soon".

| Field | Key | Type | Owner |
|---|---|---|---|
| Challenge | `ak_challenge` | rich text | Core |
| Brief | `ak_brief` | rich text | Core |
| Strategy | `ak_strategy` | rich text | Core |
| Concept | `ak_concept` | rich text | Core |
| Creative direction | `ak_direction` | rich text | Core |
| Identity / design system | `ak_identity` | rich text | Core |
| Production | `ak_production` | rich text | Core |
| Digital | `ak_digital` | rich text | Core |
| Campaign | `ak_campaign` | rich text | Core |
| Launch | `ak_launch` | rich text | Core |
| Outcome | `ak_outcome` | rich text | Core |

**`ak_outcome` carries a hard rule.** It renders only when populated, and it
must state something verifiable. No invented press counts, no invented
conversion figures. An absent outcome is honest; a fabricated one is a
liability. The field description in wp-admin says exactly this.

### Repeaters

| Field | Key | Row shape | Owner |
|---|---|---|---|
| Deliverables | `ak_deliverables` | `label` | Core |
| Outputs | `ak_outputs` | `label`, `count`, `unit` | Core |
| Collaborators | `ak_collaborators` | `name`, `role`, `url` | Core |
| Credits | `ak_credits` | `role`, `name`, `url` | Core |
| Gallery | `ak_gallery` | `image_id`, `caption`, `span` | Core (ids) / Theme (`span`) |

### Media

| Field | Key | Type | Owner | Notes |
|---|---|---|---|---|
| Cover | `_thumbnail_id` | image | Core | Index and cards. **Required to publish** — a case study with no cover damages the grid |
| Hero media | `ak_hero` | image or video | Core | Falls back to the cover |
| Video | `ak_video_h264` / `ak_video_av1` | url | Core | Two sources, AV1 preferred |
| Poster | `ak_video_poster` | image | Core | |
| Mobile media | `ak_hero_mobile` | image | Core | Portrait crop. Falls back to the hero |
| Focal point | `ak_focal` | `x%,y%` | Theme | Where the crop holds |
| Visual mode | `ak_mode` | select | **Theme** | `atelier` \| `runway` \| `auto`. Which mode this case opens in — a dark project can present dark. Pure presentation |

### Derived — never stored twice

| Value | From |
|---|---|
| `ak_code` | register + year + sequence |
| Next / previous project | Register order, wrapping within the register |
| Related work | Shared `ak_service` terms |
| Reading depth | Count of populated narrative fields → chooses the short or full layout |
| Discipline links | `ak_service` term → `/services/#slug` |

## Layout selection

The model picks the layout; the editor does not.

| Populated narrative fields | Layout |
|---|---|
| 0 | **Card only.** Appears in the index, has no page. For a project worth listing but not writing up |
| 1–2 | **Short.** Spec header, hero, the sections that exist, next project |
| 3+ | **Full.** Spec header, hero, numbered sections, gallery, credits, outcome, next project |

This is why a sparse project cannot look broken: it is never given a layout it
cannot fill.

## Ownership

| Belongs to Core | Belongs to the theme |
|---|---|
| The `portfolio` post type and both taxonomies | Every template |
| Every field above marked Core | `ak_mode`, `ak_focal`, gallery `span` |
| The admin UI for editing them | The tech-pack rendering |
| REST exposure | Motion, type, spacing |
| Code generation, next/prev, related | — |

Today all of it sits in the theme. Switch theme and the projects become
unreachable and their meaning is lost. That is the load-bearing move in
`THEME-PLUGIN-ARCHITECTURE.md`, and this model is the specification for it.

## Storage

Post meta with an `ak_` prefix, registered through `register_post_meta()` with
types and `show_in_rest`. No ACF dependency: the theme already ships a
`get_field()` shim because Zeyna calls ACF unguarded, and the project data
should not inherit a plugin requirement it does not need.

Repeaters are stored as JSON in a single meta key each, not as
`field_0_label`-style rows. Simpler to migrate, and readable in the database.
