# Website preview system

How a Website / Digital project presents the live site — and, just as
importantly, where that presentation is not allowed to go.

## The rule that comes first

**The preview is one rendering mode for one project type.** It must not
influence how Photography, Film, Event, Fashion Production or Branding projects
look. The studio is not a web studio, and a preview mechanism that leaked into
every project page would say otherwise louder than any copy could.

## What it replaces

The current homepage renders platform cards through **WordPress.com mShots** —
a third-party screenshot service, one request per card. Three problems:

1. It is a third-party dependency on the critical path of the homepage.
2. It fails closed on restricted networks. It fails in this environment.
3. Screenshots of websites, presented as a studio's work, make a fashion
   practice look like a link directory.

## Capture, not embed

Ten live iframes on one index page is not an option: ten third-party documents,
their scripts, their fonts, their trackers, their layout shifts. The Work index
uses **captures**; only a single case study may go live, and only on request.

| Stage | Behaviour |
|---|---|
| **Capture** | A tall screenshot of the real homepage — full page, not one viewport — stored in the media library as an ordinary attachment |
| **Refresh** | Re-captured on a schedule via WP-Cron, and on demand from the project editor. Stale captures are the failure mode this system exists to avoid |
| **Store** | Two per project: desktop (1440 wide) and mobile (390 wide) |
| **Serve** | `srcset` from the media library. Same pipeline as every other image on the site |
| **Fallback** | Capture missing or failed → the typographic plate: project code and name on the raised ground. **Never a broken image, never a grey box** |

Capture runs server-side through a headless browser where the host allows it,
and otherwise through a configured capture endpoint. Which is available is a
deployment concern; the front end only ever sees an attachment ID.

## Making a capture feel alive

A tall still, cropped to a frame, scrolled by the reader.

| Interaction | Behaviour |
|---|---|
| **Scroll-through** | The capture is taller than its frame; scrolling the page pans the image inside the frame on a scroll-driven timeline. The reader moves through the real page without loading it |
| **Viewport switch** | A desktop/mobile control swaps the capture and re-shapes the frame — 16:10 to 9:19.5 — with the pan position preserved proportionally |
| **Browser chrome** | A single hairline bar carrying the real domain in Data type. No fake traffic lights, no skeuomorphic browser |
| **Hover** | The frame's rule turns accent. Nothing moves |
| **Live mode** | An explicit *Open live site* control. Loads the real site in an iframe **only after a click**, sandboxed, with a visible close. Never on load, never on hover |
| **Reduced motion** | No pan. The capture sits at its top, and the frame scrolls natively |

The pan is CSS scroll-driven (`animation-timeline: view()`) — no scroll
listener, no JavaScript on the critical path, and it degrades to a static
capture where unsupported.

## Fields

Held under `ak_site_*`, shown only when Project Type is Website / Digital.

| Field | Type | Notes |
|---|---|---|
| `ak_site_url` | url | The live site. Required for this type |
| `ak_site_capture_desktop` | attachment | Tall capture, 1440 wide |
| `ak_site_capture_mobile` | attachment | Tall capture, 390 wide |
| `ak_site_captured_at` | datetime | Derived. Shown in the spec block as *Captured* |
| `ak_site_refresh` | select | `manual` · `weekly` · `monthly` |
| `ak_site_live_enabled` | bool | Whether *Open live site* is offered. Default off |
| `ak_site_scroll` | select | `pan` (default) · `static` |

## Performance budget

| Rule | Reason |
|---|---|
| Index shows the **cover crop only**, never the tall capture | A tall capture per row would be megabytes of index |
| Tall capture loads on the **case study only**, lazily, below the fold | It is not the LCP element |
| Captures are WebP with a JPEG fallback, `srcset` at 2 widths | Same as every other image |
| **No iframe until a click**, one at a time, and never on the index | Ten live sites on one page is a page nobody can use |
| Live mode is `sandbox`ed and `loading="lazy"` | It is someone else's document |

## Honesty

A capture is a picture of a website at a moment. The spec block states when it
was taken, and the domain shown is the real one. The preview never implies AK
built something it did not: the **relationship** taxonomy sits in the same spec
block, so an `AK Owned` platform and a `Client` commission are never confusable.

## What this does not do

It does not add a "web design" flavour to the site. It appears on Website /
Digital projects and nowhere else — not on the homepage, not in the About page,
not as a decorative motif. Seven of the ten confirmed projects are websites,
which is exactly why the preview has to stay in its lane: if the mechanism
spread, the site would read as a web studio again through pure repetition.
