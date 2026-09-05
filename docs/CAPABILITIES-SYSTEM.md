# Capabilities system

The canonical description of what AK Brand Development Studio does, and how
that maps onto taxonomies, the project editor and the front end.

**Source:** the confirmed practice areas supplied in the scope correction.
Nothing here is invented; nothing confirmed is dropped.

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

Branding · Personal Branding · Photography · Film · Event · Fashion Production ·
Campaign · Website / Digital · Advertising · Integrated

**One term per project.** The type decides the presentation mode and which
admin panel opens. A project that genuinely spans disciplines is **Integrated**
— not seven separate entries.

### `ak_capability` — what AK actually delivered

Many-to-many, from the list above. Filterable without changing the type. This
is how a Website project can also be credited with Photography and Strategy
without becoming a photography project.

## Front-end Work filters

Editorial labels, derived from project type. Raw taxonomy names are never
exposed.

| Filter | Includes |
|---|---|
| **All** | everything |
| **Brand** | Branding, Personal Branding |
| **Image** | Photography, Campaign |
| **Film** | Film |
| **Experience** | Event, Fashion Production |
| **Digital** | Website / Digital, Advertising |
| **Integrated** | Integrated |

Six filters plus All. The point is that a visitor moves naturally from a
website to a fashion show to a photo campaign to an identity and still feels
one studio behind all of it.

## Presentation modes

The project type selects the mode. **A mode is a rendering strategy, not a
different design system** — the grid, type, colour and motion are constant.

| Type | Mode | Leads with |
|---|---|---|
| Website / Digital | **Preview** | An interactive capture of the live site — see `WEBSITE-PREVIEW-SYSTEM.md` |
| Photography, Campaign | **Image** | Full-bleed stills, sequences, portrait/landscape pairs. Can be almost wordless |
| Film | **Motion** | Hero film, poster, cuts. Motion before text |
| Event, Fashion Production | **Document** | Date, city, venue, role, programme, gallery, event film |
| Branding, Personal Branding | **Narrative** | Context, positioning, strategy, identity, applications |
| Advertising | **Campaign** | Channels, creative assets, period. **No Results module unless verified figures are supplied** |
| Integrated | **Assembled** | The owner enables the modules the project actually has |

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
| Website / Digital | Live URL, capture settings, viewport mode, scroll behaviour, live-site toggle |
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

| Project | Relationship | Type | URL |
|---|---|---|---|
| London Fashion Day | AK Owned | Fashion Production | londonfashionday.co.uk |
| Odessa Fashion Day | AK Owned | Fashion Production | ofd.org.ua |
| COOLBABA | AK Owned | Website / Digital | coolbaba.in.ua |
| Prominent Magazine | AK Owned | Website / Digital | prominentmagazine.co.uk |
| Fashion Frontier | AK Owned | Website / Digital | fashionfrontier.uk |
| Utrend Store | AK Owned | Website / Digital | utrendstore.co.uk |
| KEKA | AK Owned | Branding | keka.design |
| Wolax | Client | Website / Digital | wolax.co.uk |
| Lenie Boya | Client | Website / Digital | lenieboya.com |
| Show Me Your Nails | Client | Website / Digital | showmeyournails.com |

This resolves the KEKA conflict flagged in the previous checkpoint: **KEKA is
AK Owned.**

**What is still absent, and must stay absent:** descriptions, dates,
deliverables, results, KPIs. Only existence and relationship are confirmed, so
only existence and relationship are published. Each entry seeds as a card with
its live preview and no invented narrative.

### The bias this seed data creates, and the fix

Seven of ten confirmed projects are websites. Seeded as-is, the Work index
would once again read as a web studio — the exact problem this correction
exists to solve.

Three structural responses:

1. **Work does not default to a website-first order.** The index opens on
   `All`, ordered so that types alternate rather than clustering.
2. **The homepage leads with the platforms**, not the sites. London Fashion Day
   and Odessa Fashion Day are Fashion Production and they come first.
3. **The empty categories are shown, not hidden.** Image, Film and Experience
   filters exist and state that projects are being prepared. An honest empty
   shelf communicates breadth better than a full shelf of one thing —
   and it is the owner's cue about which material to supply next.
