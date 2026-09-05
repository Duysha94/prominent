# The AK Core Project model

How the approved v1.7 architecture is implemented in the theme. This is the
reference for the code in `wordpress/ak-zeyna-child/inc/projects/`.

Everything here exists to keep one promise: **the site never claims more than
the studio has established.**

## Files

| File | Owns |
|---|---|
| `capabilities.php` | The factual layer — six movements, eight practice areas, 49 services |
| `model.php` | The `ak_project` post type and the three taxonomies |
| `meta.php` | Every field, through `register_post_meta` |
| `preview.php` | The Website module's four states and the capture verification |
| `query.php` | Public queries, content-driven filters, register-or-grid |
| `modules.php` | The module renderer |
| `admin.php` | The conditional project editor |
| `seed.php` | Terms as invariants, the confirmed register as a one-time seed |

## Two layers, and the rule between them

```
PRESENTATION   Strategy · Identity · Image · Experience · Digital · Visibility
                      │ every movement is a PARENT TERM
FACTUAL        49 services, each a CHILD TERM beneath the movement that carries it
```

**Creative naming must never hide what the agency does.** The mechanism is the
parent/child taxonomy: naming a group IMAGE cannot conceal *editorial
photography* when that exact phrase is a term beneath it, a line on Services
with its own anchor, and a filter a project can be tagged with.
`project-model-test.php` asserts this — every service must resolve to a term
whose parent is its movement, or the suite fails.

## Three taxonomies, deliberately separate

| Taxonomy | Answers | Always known? |
|---|---|---|
| `ak_relationship` | How the project came to exist | **Yes** |
| `ak_project_type` | What the project *is* | Often not |
| `ak_capability` | What AK actually delivered | Often not |

Collapsing any two of these is what produced a portfolio biased toward
websites.

### Type may be unset, and unset is a publishable state

`ak_project_type` is a radio list whose first option is **Not established yet**,
and the editor carries the rule in the interface itself:

> Leave unset until the nature of the project is established. Do not infer it
> from the domain name, the website, the project name or who owns it — the
> record publishes perfectly well without a type.

A project with no type renders in `record` mode: title, relationship, address,
whatever media exists. Wolax, Lenie Boya and Show Me Your Nails ship this way.

### The seed does not lock a classification in

Relationship is confirmed, so it is enforced on **every** deployment. Type is
written **once**, behind a `_ak_type_seeded` marker, and never re-imposed — a
deployment that reasserted a seeded type would silently overwrite the owner's
own correction the next time the theme updated. The suite proves it: change
COOLBABA to Film, redeploy, and it is still Film.

## The Website module

A module available to any project. **It is not a project type, and a URL does
not create one.** For an owned platform its website is one surface of a larger
ecosystem, so the module sits last in the default order — leading with it would
reduce the platform to its web build.

### Four states

| State | When | What the visitor sees |
|---|---|---|
| **AUTO** | A generated capture exists **and has been verified** | The current front page |
| **MANUAL** | The owner supplied a screenshot, image set or recording | That media |
| **LIVE** | An embed of the real site | The live site |
| **UNAVAILABLE** | Anything else | **Nothing. No frame, no plate, no message.** |

UNAVAILABLE returns before printing a single tag. "Capture pending", "capture
failed" and "preview unavailable" are administrative status, shown on the edit
screen and nowhere else. A public portfolio that renders its own broken tooling
is worse than one that renders less.

### Verification, not optimism

The capture service answers a not-yet-ready capture with a small grey
placeholder — HTTP 200, `image/*`, so the status code cannot tell "captured"
from "still working". `ak_verify_capture()` therefore checks the payload size:
anything under 8 KB is the placeholder, and the project stays UNAVAILABLE.

This is not hypothetical. The homepage previously called the service directly
and printed whatever came back, and rendered a row of grey plates.

### Manual override is mandatory, not a fallback

From the admin, per project: upload a desktop preview, a mobile preview or a
screen recording; disable automatic capture; request a refresh; switch back to
AUTO later. **Owner-supplied media outranks anything automatic**, because the
owner is the authority on their own project and the capture service is the part
that fails.

The automated capture is an enhancement. It must never become a dependency that
prevents a project from being published — which is why every resolution path
can end in "no module" without anything else degrading.

## Work: the layout follows the material

```
NO MEDIA      → editorial register: code · relationship · name · type · address
REAL MEDIA    → visual composition led by the cover
```

The two coexist, per record. A record with a cover becomes a card; a record
without stays a typographic entry. The index becomes media-led on its own, one
project at a time, as material arrives.

The register is a **fallback state, not the intended final portfolio.** No fake
cover is ever invented to force a grid, and no wall of *capture pending* plates
is ever published to fake one.

`ak_project_cover()` decides, in order: featured image → owner's manual desktop
preview → **verified** automatic capture → first gallery image → nothing. An
unverified capture is not a cover, which is what keeps the index honest.

## Filters are generated from published content

`ak_work_filters()` returns `all` plus one entry per editorial filter that has
at least one published project. On the confirmed register that is five chips.
The mapping table in `model.php` keeps the other rows so the first Photography
or Event project brings its filter with it, without a code change and without
ever having shown an empty one.

The counts do not sum to All: untyped projects appear only under All. A count is
a fact about published content, never a target to fill.

## Fixtures are internal

`ak_fixture = 1` excludes a project from the Work index, the filters, filter
counts, related work, the homepage, feeds and the sitemap, and returns 404 to
the public. The flag is an **invariant** — restored on every deployment,
because a fixture that lost it would become public portfolio material, the one
thing fixtures must never do.

Three exist, proving the Image, Motion and Document modes. They are deleted, not
published, once the real project arrives.

## Projects are seeded, never reconciled

The deployment engine reconciles the pages and journal posts it owns as
structure. **It does not reconcile projects.** They carry the managed marker so
relationship terms and fixture flags can be repaired, but they are absent from
the manifest and the retire pass skips the post type outright.

Without that guard the engine created the confirmed register and deleted it
again three lines later — and worse, would have removed the owner's own
editorial work on every release. A project is created once and then belongs to
the owner.

## Adding a new kind of work needs no code

Photo shoots, editorial photography, campaigns, promotional video, fashion
films, event production, event curation, brand presentations, product launches,
fashion shows, fashion-week production, branding, personal branding, PR and
communication projects, advertising campaigns and integrated projects are all
expressible today:

- pick the **project type** → it selects the presentation mode and the editor
  panels
- tag the **capabilities delivered** → from the 49 already seeded
- add **modules** in the order the project actually needs

Adding a service to `ak_movements()` makes it a taxonomy term, a Services entry
and a Work filter with no further work.
