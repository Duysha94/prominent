# Information architecture

The site is a **portfolio for a two-person studio**, not a corporate agency
site. Every route has to earn its place; the default answer to "should we add a
page?" is no.

## Sitemap

| Route | Exists? | Why |
|---|---|---|
| `/` | **Yes** | The one page most visitors see |
| `/work/` | **Yes** | The index. Two registers, one page |
| `/work/{slug}/` | **Yes** | The case study. The thing the studio is judged on |
| `/services/` | **Yes** | One page. Not four |
| `/about/` | **Yes** | Studio and founders |
| `/contact/` | **Yes** | The conversion |
| `/journal/`, `/journal/{slug}/` | **Yes, with a limit** | See below |
| `/privacy/` | **Yes** | Legal |

Eight routes plus two archives. That is the whole site.

### Reversal: no service detail pages

An earlier plan proposed `/services/strategy/`, `/services/identity/`,
`/services/production/`, `/services/presence/`. **That was wrong and is
withdrawn.** Four pages for a two-person studio would be four thin pages saying
what one page says better, and thin pages are exactly the agency-template
boilerplate this phase exists to avoid. Depth goes *into* `/services/`, and
the disciplines get anchor targets (`/services/#production`) so links from case
studies still land precisely.

### Rejected outright

| Page | Why not |
|---|---|
| Our Team | Two people. They are the About page |
| Testimonials | Would need quotes that do not exist. Fabricating them is out of the question, and a testimonials page with two entries reads as weakness |
| FAQ | An FAQ answers objections a sales page failed to. Fix the page |
| Careers | No hiring |
| Process | A separate process page is a case study nobody committed to writing. The four movements carry it |
| Clients / Logo wall | No confirmed permission to display client marks. And a sparse logo wall is worse than none |

### Journal — kept, deliberately capped

Not a blog. It is the studio's written position, and for an advisory the
written position **is** the credential — it is the cheapest proof that the
people giving the advice have done the work. Four pieces exist and are
genuinely good.

The condition: **it must not scale.** Four to eight pieces, each substantial.
A journal that becomes a content-marketing feed turns the site into the thing
we are avoiding. If it cannot be maintained at that quality, it should be
removed rather than allowed to go stale.

## Route specifications

### `/` — Home

| | |
|---|---|
| **Purpose** | Make one argument: this studio produces what it advises, and owns the platforms it puts clients on |
| **Audience** | A founder or brand owner deciding within ~20 seconds whether this is a real practice |
| **Primary action** | Enter the work |
| **Required** | Opening statement over film · the *founded, not hired* claim · FOUNDED register · COMMISSIONED register · capabilities in context · the two founders · closing contact |
| **Relationships** | Feeds `/work/`, `/services/`, `/about/`, `/contact/` |
| **SEO intent** | Brand + category: "fashion brand consultancy London", "fashion brand development studio" |

### `/work/` — Work

| | |
|---|---|
| **Purpose** | Show the whole body of work with the ownership distinction intact |
| **Audience** | Someone already interested, assessing range and seriousness |
| **Primary action** | Open a case study |
| **Required** | Two registers, visually distinct · project code, client, year, disciplines per row · filter by discipline |
| **Relationships** | Parent of every case study; links to `/services/` anchors |
| **SEO intent** | "fashion brand identity portfolio", "fashion show production London" |

### `/work/{slug}/` — Case study

| | |
|---|---|
| **Purpose** | Prove one project in depth, and make its shape legible in five seconds |
| **Audience** | Evaluating fit for a specific need |
| **Primary action** | Next project, or contact |
| **Required** | Spec header (code, client, year, location, register, disciplines) · hero media · challenge · role · approach · outcome **only when verifiable** · next project |
| **Relationships** | Back to `/work/`, out to `/services/` anchors, on to the next case |
| **SEO intent** | Long tail: "London Fashion Day production", "emerging designer runway platform" |

### `/services/` — Capabilities

| | |
|---|---|
| **Purpose** | Answer "can you do this?" without becoming a menu |
| **Audience** | Someone with a specific need, checking coverage |
| **Primary action** | See the work that proves it |
| **Required** | Four movements, disciplines grouped under them by weight, each linking to real work |
| **Relationships** | Every discipline links to the case studies that used it |
| **SEO intent** | The service long tail — the site's widest keyword surface |

### `/about/` — Studio

| | |
|---|---|
| **Purpose** | Explain why fashion, brand, digital and production sit in one practice, and who makes that true |
| **Audience** | Someone close to enquiring, checking the people are real |
| **Primary action** | Contact |
| **Required** | What the studio is · why it exists · the intersection · the two founders editorially · the relationship between the studio and its own platforms |
| **Relationships** | Links to FOUNDED work; the founders' platforms link to their case studies |
| **SEO intent** | "Andrii Karakushan", "Kostiantyn Lieontiev", "AK Brand Development Studio" — name searches, which convert highest |

### `/contact/` — Contact

| | |
|---|---|
| **Purpose** | Collect a brief good enough to reply to properly |
| **Audience** | Ready to talk |
| **Primary action** | Submit, or email |
| **Required** | Short form (name, email, brand, need, budget band, timeline, description, consent) · email fallback · honest response-time claim |
| **SEO intent** | Low. Navigational only |

### `/journal/` and `/journal/{slug}/`

| | |
|---|---|
| **Purpose** | Demonstrate the thinking that justifies the fee |
| **Audience** | Researching the problem, not yet the supplier |
| **Primary action** | Read, then look at the work |
| **Required** | Date, discipline, headline, excerpt; article + related work |
| **SEO intent** | The widest top-of-funnel surface. "what it costs to show at a fashion week" is a real query with real intent |

### `/privacy/`

Legal. Deployed and adopted as `ak_privacy`; WordPress's own privacy page is
claimed rather than duplicated. No SEO intent; not in the primary nav.

## Navigation

Primary nav is five items: **Work · Services · About · Journal · Contact**.
Home is the wordmark. Privacy lives in the footer.

Five is the ceiling. A sixth item would mean a page that has not justified
itself.
