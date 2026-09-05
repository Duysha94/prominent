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

### Website / Digital is a real type — but never an inferred one

Two different statements, and only one of them is a rule:

| | |
|---|---|
| **`Website / Digital` is a project type** | True. Some engagements genuinely *are* primarily website design, website development, e-commerce or a digital ecosystem, and the model must be able to say so. A person selects it. |
| **A URL makes something a Website project** | False, and the thing the model exists to prevent. |

`Website / Digital` and `Retail / E-commerce` render in `digital` mode, which
places the Website module **first** — the engagement was the site, and burying
its one substantive section would misrepresent the work. Every other mode places
it last.

The type and the module stay separate in both directions:

```
Website / Digital   a kind of project      chosen by a person
Website module      a section of a record  available to every type
```

A Platform, a Fashion Brand, a Media / Editorial project or an Integrated
project may all display their live site through the module **without becoming
Website projects**. London Fashion Day carries a URL and a website module and
remains a Platform; the suite asserts exactly that.

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

## The service taxonomy and the public navigation are not the same thing

This distinction is load-bearing.

| | Size | Where it lives |
|---|---|---|
| **Service vocabulary** | 49 named services under six movements | The `ak_capability` taxonomy, Services, and **inside** each project |
| **Public Work navigation** | Six editorial filters plus All | The Work index |

Turning 49 services into 49 portfolio filters would be unusable, and would make
Work read as a capability list rather than a body of work. So the filters are:

**All · Brand · Image · Film · Digital · Experience · Fashion**

| Filter | Types mapped to it |
|---|---|
| **Brand** | Branding, Personal Branding |
| **Image** | Photography, Campaign |
| **Film** | Film |
| **Digital** | Website / Digital, Retail / E-commerce, Media / Editorial, Advertising |
| **Experience** | Event |
| **Fashion** | Platform, Fashion Brand, Fashion Production |

`ak_work_filters()` returns `all` plus one entry per filter that has at least one
published project. On the confirmed register that is **All 10 · Digital 3 ·
Fashion 4**. The mapping keeps the other rows so the first Photography or Event
project brings its filter with it, without a code change and without ever having
shown an empty one.

The counts do not sum to All. Untyped projects and `Integrated` projects appear
under All alone — an integrated project spans several of these and filing it
under one would misdescribe it. A count is a fact about published content, never
a target to fill.

**`ak_capability` is not publicly queryable.** A public taxonomy would have given
the site 49 capability archives at `/work/capability/<service>/` — a second,
accidental portfolio navigation an order of magnitude larger than the real one,
mostly empty, and indexable. Capabilities are recorded per project and displayed
inside it, grouped by movement, where they describe one piece of work rather than
trying to navigate all of it.

## Project codes are stored data

The Tech Pack language leans on these: a code appears in the margin rail, the
register, the card, the *next project* link and the spec block, and the owner is
expected to be able to say one out loud. So it is **stored**, in `ak_code`, and
the render path only ever reads it.

Two earlier versions were wrong the same way:

- `AK-O-YY-{post_id % 1000}` produced `AK-O-—-096`..`105` — a run of numbers
  saying nothing except how many rows the database held, and changing on any
  re-import.
- Deriving it from the project's position in its register is stable only until
  someone reorders the list or unpublishes a record, at which point every code
  after it silently shifts.

A code is minted **once** from the title — initials for a multi-word name, the
first four letters for a single word, with a numeric suffix on collision — and
kept. The confirmed register ships with explicit ones:

`AK-LFD` · `AK-OFD` · `AK-COOL` · `AK-PROM` · `AK-FF` · `AK-UTR` · `AK-KEKA` ·
`AK-WLX` · `AK-LB` · `AK-SMYN`

The field is owner-managed: retype it and no deployment will overwrite you. A
deployment backfills a code for any project that has none, so the front end never
writes during a GET.

## Fixtures are internal

`ak_fixture = 1` excludes a project from the Work index, the filters, filter
counts, related work, the homepage, feeds and the sitemap, and returns 404 to
the public. The flag is an **invariant** — restored on every deployment,
because a fixture that lost it would become public portfolio material, the one
thing fixtures must never do.

Three exist, proving the Image, Motion and Document modes. They are deleted, not
published, once the real project arrives.

## Field ownership

Three classes, and the boundary between them is the contract that makes it safe
to ship theme updates to a site the owner is actively editing. Declared in
`inc/projects/ownership.php`.

| Class | Who writes | When |
|---|---|---|
| **RELEASE-MANAGED** | The build | Enforced on **every** deployment |
| **OWNER-MANAGED** | The owner | Build may seed an empty field **once**, then never again |
| **DERIVED** | Computed | Cache only, never a source of truth |

**Release-managed:** seed key, managed marker, legacy marker, the fixture flag,
the confirmed relationship of a canonical seeded record, migration and version
state.

**Owner-managed:** title, excerpt, content, project type, capabilities, cover and
featured image, hero media, galleries, video, website URL, manual previews,
case-study depth, modules, credits, ordering, featured state, project code,
presentation choices — and **anything not listed at all**, because a field the
build does not name is not a field the build may write.

**Derived:** capture verification state, last-checked time, capture status.

### The rule

> Once an owner-managed value has been edited, a later deployment must not
> silently restore the seed value.

"Edited" includes **clearing** a field. An owner who deletes a seeded description
meant to delete it, and a deployment that helpfully puts it back is a bug that
looks like a haunting.

Enforcement is a recorded set of touched keys per project (`_ak_owner_edited`),
written on save from the edit screen and consulted by `ak_seed_field()`. It is
deliberately **not** a value comparison: comparing the current value to the seed
cannot distinguish *never touched* from *edited back to the same thing*, and gets
the clearing case wrong.

Presence in the POST is what counts, not a change in value. Opening the editor
and pressing Update is the owner asserting the current state of the record,
including the parts they chose to leave alone.

The suite edits nine representative owner-managed fields, runs **two**
deployments — a bug that survives one often fires on the next — and asserts every
one survived, including a deliberately cleared URL. It then breaks two
release-managed values and asserts both were restored.

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
