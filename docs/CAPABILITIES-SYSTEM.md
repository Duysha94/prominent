# Capabilities system

The canonical description of what AK Brand Development Studio does, and how
that maps onto taxonomies, the project editor and the front end.

**Source:** the AK Brand Development Studio source document, supplied in full
by the owner. Every practice area and every service below appears there.
Nothing here is invented; nothing confirmed is dropped.

Three plausible-sounding services that were *not* in the document — *editorial
photography*, *fashion film* and *event curation* — were removed once it
arrived. A service list is a claim about what the studio sells, and inventing a
line item is inventing a claim however reasonable it sounds. The count is 46.

## The correction this document makes

The site read as *websites and advertising*. Those are two of eight confirmed
areas. The studio works across the whole development and visibility cycle —
idea, positioning, strategy, identity, creative direction, content, photo and
film, digital, communication, advertising, event and fashion production, market
visibility — and the architecture has to say so structurally, not by adding
adjectives.

## Six movements

Eight confirmed areas grouped into six movements. The grouping is the editorial
layer; the taxonomies below are the machine layer, and they are not the same
shape on purpose.

| № | Movement | Covers | Confirmed areas |
|---|---|---|---|
| **01** | **STRATEGY** | What the brand is, and who it is for | Brand Strategy & Development · Personal Brand Development |
| **02** | **IDENTITY** | What it looks and sounds like | Brand Identity & Creative Direction |
| **03** | **IMAGE** | The pictures and the films | Photo & Video Production |
| **04** | **EXPERIENCE** | What happens in a room | Events & Fashion Production |
| **05** | **DIGITAL** | Where it lives online | Digital Presence |
| **06** | **VISIBILITY** | How it is seen and heard | Marketing & Communication · Digital Promotion |

### Why these six

- **Six, not four.** The old *Strategy / Identity / Production / Presence* put
  photography, film, fashion shows and product launches into one bucket called
  Production, and websites, PR and paid media into one called Presence. That is
  precisely how the site came to read as a web-and-ads shop with a sideline:
  the two largest parts of the practice were invisible inside other people's
  headings.
- **Six, not eight.** Personal branding is a strategy discipline, not a
  separate department, and grouping it under STRATEGY where it is *named* keeps
  it visible without inventing a ninth movement. Marketing & Communication and
  Digital Promotion are the same job — getting the work seen — approached
  editorially and commercially.
- **Named for the client's question, not the studio's org chart.** A founder
  thinks *who am I* · *what do I look like* · *who shoots this* · *who produces
  the show* · *who builds the site* · *who gets me written about*. The six
  movements answer those in that order.

### 06 VISIBILITY carries a constraint

It contains two clusters that are **presented with equal weight and never
merged into one list**:

- **Communication & PR** — marketing strategy, brand communication, PR
  support, campaign development
- **Promotion & Paid Media** — digital advertising campaigns, Google, YouTube,
  Meta/Facebook/Instagram, audience growth, digital visibility, engagement

PR must not be swallowed by advertising, and advertising must not be hidden.
Paid media is framed as the amplification side of a brand system that the other
five movements built — never as the practice itself.

## Capabilities, in full

The `ak_capability` taxonomy. Many-to-many: a project carries every capability
AK actually delivered on it. This is the filterable layer and it does **not**
change a project's type.

### 01 STRATEGY
Brand concept development · Brand positioning · Brand strategy · Brand
relaunch · Repositioning · Brand philosophy · Identity foundations · Strategic
guidance for business growth · **Personal brand strategy** · Personal
positioning · Personal identity · Personal communication strategy · Personal
visual direction · Personal content direction

### 02 IDENTITY
Brand identity development · Visual direction · Logo and identity design ·
Brand guidelines · Visual storytelling · **Creative direction**

*Creative direction is a studio-level capability, not a sub-service.* It
connects identity, campaigns, photography, film, fashion, events and digital,
and it appears in the credits of projects across every other movement.

### 03 IMAGE
Creative direction · Photo campaigns · Promotional video production · Visual
storytelling · Campaign production

### 04 EXPERIENCE
Brand presentations · Product launches · Creative events · Independent fashion
shows · Fashion show production · Fashion-week-related production

### 05 DIGITAL
Website creation · Website development · Social media presence · Digital
content production · Online brand positioning

### 06 VISIBILITY
Marketing strategy · Brand communication · PR support · Campaign development ·
Digital advertising campaigns · Google promotion · YouTube promotion ·
Meta / Facebook / Instagram advertising · Audience growth strategies · Digital
visibility · Engagement

## Implemented

The factual layer above is now `ak_movements()` in
`wordpress/ak-zeyna-child/inc/projects/capabilities.php` — one source that
builds the Services page, seeds the `ak_capability` taxonomy, and fills the
homepage and footer. Every one of the 46 services exists as a **child term
beneath its movement**, which is the mechanism that stops the editorial layer
hiding the factual one, and `project-model-test.php` fails if any service is
missing or misparented.

See [PROJECT-MODEL.md](PROJECT-MODEL.md) for the implementation.

## Taxonomies

Three, deliberately separate. Collapsing any two of them is what produced a
portfolio biased toward websites.

### `ak_relationship` — how the project came to exist

| Term | Meaning |
|---|---|
| **AK Owned** | Created, owned, developed or operated by the studio |
| **Client** | Work performed for an external client |
| **Collaboration** | Developed jointly; authorship shared |

Front-end labels: **FOUNDED**, **COMMISSIONED**, **IN COLLABORATION**.

Three terms, not two — the previous model forced every project into founded or
commissioned, and an event AK creative-directed with a partner is honestly
neither.

### `ak_project_type` — what the project *is*

Platform · Media / Editorial · Fashion Brand · Retail / E-commerce · Branding ·
Personal Branding · Photography · Film · Event · Fashion Production · Campaign ·
Advertising · Integrated

**One term per project.** It describes the **nature of the entity**, not the
outputs AK produced for it — those are capabilities and modules.

**Website / Digital is on this list**, and this document previously said it was
not. That over-corrected. The instruction was *a URL does not make something a
website project*; it was never *website projects do not exist*. Some engagements
genuinely are primarily website design, website development, e-commerce or a
digital ecosystem, and the model has to be able to say so — a person selects the
type. What must never happen is a URL selecting it. See the rule below.

A project that genuinely spans disciplines is **Integrated** — not seven
separate entries.

### The rule: a URL is not a project type

> **A supplied URL identifies a project and gives us real digital material to
> preview. It says nothing about what the project is.**

The rule is about *inference*, not existence. A person may select
`Website / Digital`. A URL may not select it for them.

Inferring `URL exists → Website / Digital` collapses an owned fashion platform
into a web build. London Fashion Day is a platform involving fashion
production, events, industry activity, PR, media *and* digital presence; its
website is one surface of it. Classifying it by the one artefact that happens
to have an address would be the single most damaging misstatement the site
could make about the practice.

Type comes from established fact about the entity. Where the full work is not
yet established, the type is **left unset** and the project publishes as a
minimal record — title, relationship, address, preview — until it can be
classified truthfully.

### `ak_capability` — what AK actually delivered

Many-to-many, from the list above. Filterable without changing the type. This
is how a Website project can also be credited with Photography and Strategy
without becoming a photography project.

## Front-end Work filters

Editorial labels, derived from project type. Raw taxonomy names are never
exposed.

The service taxonomy and the public portfolio navigation are **not the same
thing**. 46 services is the right size for a factual record of the practice and
the wrong size for a portfolio menu, so the public filters stay concise and the
detail is shown inside each project.

| Filter | Includes |
|---|---|
| **All** | everything |
| **Brand** | Branding, Personal Branding |
| **Image** | Photography, Campaign |
| **Film** | Film |
| **Experience** | Event |
| **Fashion** | Platform, Fashion Brand, Fashion Production |
| **Digital** | Website / Digital, Retail / E-commerce, Media / Editorial, Advertising |

**The table is the mapping, not the menu.** A filter renders only when at least
one published project falls under it. On the confirmed register that is *All 10
· Fashion 4 · Digital 3* — three chips, not seven. The other rows exist so that
the first Photography or Event project brings its filter with it, without a code
change and without ever having shown an empty one.

The counts do not sum to All, and that is correct: the three client records carry
no type, and an `Integrated` project spans several filters, so all of them appear
under All alone. A count is a fact about published content, never a target to
fill.

**Capabilities are not a public taxonomy.** `ak_capability` is registered
`publicly_queryable => false` with no rewrite, so the site does not grow 46
capability archives at `/work/capability/<service>/` — a second, accidental
portfolio navigation an order of magnitude larger than the real one, mostly
empty, and indexable. The detailed capabilities delivered on a project are shown
**inside** that project, grouped by movement.

## Presentation modes

The project type selects the mode. **A mode is a rendering strategy, not a
different design system** — the grid, type, colour and motion are constant.

| Type | Mode | Leads with |
|---|---|---|
| Platform, Media / Editorial, Fashion Brand, Retail / E-commerce | **Assembled** | Whatever the platform actually has. These are large entities; the layout follows the material |
| Photography, Campaign | **Image** | Full-bleed stills, sequences, portrait/landscape pairs. Can be almost wordless |
| Film | **Motion** | Hero film, poster, cuts. Motion before text |
| Event, Fashion Production | **Document** | Date, city, venue, role, programme, gallery, event film |
| Branding, Personal Branding | **Narrative** | Context, positioning, strategy, identity, applications |
| Advertising | **Campaign** | Channels, creative assets, period. **No Results module unless verified figures are supplied** |
| Integrated | **Assembled** | The owner enables the modules the project actually has |
| *unset* | **Record** | Title, relationship, address, preview. Nothing claimed beyond what is known |

**No mode is selected by the presence of a URL.** The website preview is a
module (below), available to every mode.

### Photography is not Film

They share a movement and separate everything else. Photography leads with a
still and can carry a whole project through sequence and scale alone; its
credits are photographer, styling, hair and make-up, talent. Film leads with
motion — poster, then playback — and its credits are director, cinematography,
editing. Forcing a photo project into a film layout wastes the image; forcing a
film into a photo layout kills it.

### Owned platforms are not events

London Fashion Day and Odessa Fashion Day are **platforms**, not one-off
productions. They carry `AK Owned` + `Fashion Production`, and their record
describes an ongoing operation — founded, produced, developed — rather than a
single date and venue. A one-night show AK produced for a client is
`Client` + `Fashion Production` and *does* lead with a date and a venue.
Reducing an owned platform to an ordinary event case would understate the
single strongest thing the studio has.

### Integrated projects

The case study assembles itself from the modules the project actually has. A
brand launch might run Strategy → Identity → Campaign → Photography → Film →
Digital → Launch → Promotion → Outcome. Another project runs Creative Direction
→ Photography and stops. **Nothing is forced through a fixed sequence**, and a
missing module is absent rather than empty.

## Website Preview is a module, not a project type

Any project may carry a **Website / Digital module**. It holds the live URL,
the desktop and mobile captures, the live-preview toggle, launch information
and a short digital narrative.

```
Project
├── relationship          AK Owned · Client · Collaboration
├── project type          the nature of the entity (may be unset)
├── capabilities          what AK actually delivered (may be unset)
└── modules
    ├── Website / Digital   ← url, captures, live toggle, launch, narrative
    ├── Photography
    ├── Film
    ├── Event Information
    ├── Strategy · Identity · Campaign · Credits · Results · …
    └── …
```

Consequences, and they are the point:

- An **Integrated** project can contain Strategy, Identity, Photography, Film,
  Website, Event and Promotion **without becoming a Website project**.
- An **owned platform** can display its current website while remaining a
  Platform. The preview is one section of the case study, not its subject.
- A **website-focused client engagement** can make the module dominant — that
  is an editorial decision from the material, not a consequence of a URL
  existing.

The module's weight is set per project by where the owner places it in the
module order. First position makes it the lead; last makes it a footnote.

## Modular case-study builder

**Recommended approach: a fixed set of AK modules, ordered by the owner.**
Not a page builder, and not a rigid template.

Stored as one meta key holding an ordered JSON array of typed blocks:

```json
[ {"type":"statement","text":"…"},
  {"type":"image_full","id":128,"caption":"…"},
  {"type":"credits","rows":[{"role":"Photography","name":"…"}]} ]
```

Approved modules: Text Statement · Project Data · Full Image · Image Pair ·
Gallery · Film · Website Preview · Strategy · Identity · Credits ·
Deliverables · Event Information · Results · Quote · Related Projects.

Why this rather than the alternatives:

| Option | Verdict |
|---|---|
| Fixed template per type | Too rigid — an integrated project cannot express itself |
| Gutenberg blocks | Viable, but exposes the whole core block library and the art direction leaks immediately |
| Elementor | Rejected. The theme exists partly to escape it |
| **Ordered JSON modules** | **Chosen.** Deliberately limited to the AK visual system, trivially migratable, readable in the database, and Core-ownable |

Each module renders through one template partial, so the visual system governs
every project no matter how it was assembled.

## Admin model

**Conditional panels. Never one form with every field.**

The Project Type select is the first control on the screen. Choosing it reveals
that type's panel and hides the rest:

| Type chosen | Panel shown |
|---|---|
| Photography | Hero image, gallery, sequence, photographer, styling, hair/make-up, talent, location, BTS, related film |
| Film | Hero film, poster, trailer, cuts, director, cinematography, editing, stills, BTS |
| Event / Fashion Production | Date, city, venue, event type, AK role, concept, programme, gallery, event film, collaborators, partners |
| *(any type)* | The Website module is available to every type: live URL, capture settings, viewport mode, scroll behaviour, live-site toggle |
| Branding / Personal Branding | Context, challenge, positioning, strategy, concept, naming, philosophy, identity, logo, typography, colour, guidelines, applications, launch |
| Advertising | Campaign, channels, audience, creative assets, strategy, period, results |
| Integrated | Module enable list, then the modules themselves |

Always visible regardless of type: title, short title, relationship, capabilities,
client/owner, year, location, cover image, featured, URL.

Implemented with `register_post_meta` plus a small vanilla-JS panel switcher.
No ACF dependency — the theme already ships a `get_field()` shim only because
the parent calls ACF unguarded, and the project data should not inherit a
plugin requirement it does not need.

## Confirmed seed data

Registers now established. **These are facts supplied by the owner** — nothing
is assumed.

Type below is the **owner's own characterisation of the entity**, supplied
directly. Capabilities are unset everywhere: what AK delivered on each project
has not been established, and inventing it is out of the question.

| Project | Relationship | Type | Capabilities | URL |
|---|---|---|---|---|
| London Fashion Day | AK Owned | Platform | *unset* | londonfashionday.co.uk |
| Odessa Fashion Day | AK Owned | Platform | *unset* | ofd.org.ua |
| Fashion Frontier | AK Owned | Platform | *unset* | fashionfrontier.uk |
| COOLBABA | AK Owned | Media / Editorial | *unset* | coolbaba.in.ua |
| Prominent Magazine | AK Owned | Media / Editorial | *unset* | prominentmagazine.co.uk |
| KEKA | AK Owned | Fashion Brand | *unset* | keka.design |
| Utrend Store | AK Owned | Retail / E-commerce | *unset* | utrendstore.co.uk |
| Wolax | Client | *unset* | *unset* | wolax.co.uk |
| Lenie Boya | Client | *unset* | *unset* | lenieboya.com |
| Show Me Your Nails | Client | *unset* | *unset* | showmeyournails.com |

The three client projects carry **no type at all**. A URL and a client
relationship do not establish what the engagement was, and guessing would put a
claim on the site the studio cannot stand behind.

This resolves the KEKA conflict flagged in the previous checkpoint: **KEKA is
AK Owned.**

**What is still absent, and must stay absent:** descriptions, dates,
deliverables, results, KPIs. Only existence and relationship are confirmed, so
only existence and relationship are published. Each entry seeds as a card with
its live preview and no invented narrative.

### Capabilities and Work do different jobs

This is the correction that makes the seed data safe.

| | Answers | Source |
|---|---|---|
| **Capabilities** | *What can AK do?* | The full confirmed practice. Complete from day one, independent of what has been published |
| **Work** | *What has AK chosen to publish?* | Real published projects only |

Work is not required to visually match Capabilities, and **must not be padded
to make it look as though it does.**

**Public Work filters are generated from published content.** A filter appears
when at least one real published project would fall under it, and not before.
No `IMAGE (0)`, no `FILM (—)`, no empty shelves.

An earlier version of this document argued the opposite — that showing empty
categories communicated breadth. **That was wrong and is withdrawn.** An empty
shelf on a portfolio does not read as *breadth pending*; it reads as a studio
that has not done the work. Capabilities already states the scope, in words,
without needing the portfolio to carry that argument.

The seed data is still website-heavy in its *material*, so three responses
remain:

1. **Work does not default to a website-first order.** The index opens on
   `All`, ordered so types alternate rather than cluster.
2. **The homepage leads with the platforms.** London Fashion Day and Odessa
   Fashion Day are owned platforms and they come first — not because they have
   the best websites, but because they are the largest things the studio has.
3. **The index renders as a register until covers exist.** Same content-driven
   rule as the filters, applied to layout: with no cover on any record, a card
   grid is ten identical *capture pending* plates, which is a wall of nothing
   pretending to be a portfolio. The register — code, relationship, name, type,
   address — is dense, honest and reads as a studio that keeps records. The
   index switches to the card grid once covers exist, per record: a record with
   a cover shows it, a record without stays a row.

## Fixtures are internal

The Photography, Film and Event presentation modes are proven with **structural
fixtures**. They exist to demonstrate that the layout works before real
material arrives.

They are **never public portfolio material**:

- Fixture projects carry `ak_fixture = 1` and are excluded from every public
  query — the Work index, filters, related work, the homepage, sitemaps and
  feeds.
- They are reachable only by direct URL while logged in, and carry a visible
  internal marker.
- They contribute nothing to filter counts, so they cannot cause an empty
  category to appear populated.
- They are deleted, not published, once the real project exists.
