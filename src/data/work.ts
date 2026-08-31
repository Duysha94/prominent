/** PLACEHOLDER CONTENT — see data/studio.ts. Metrics are invented. */
import type { Measure } from '../components/primitives/MeasureFrame'

export type CaseStudy = {
  slug: string
  folio: string
  client: string
  /** The one-line result. Reads as a sentence, not a badge. */
  headline: string
  category: string
  /** Which lanes ran. Drives the filter and the "one studio" argument. */
  lanes: Array<'advise' | 'build' | 'grow'>
  /**
   * The lane that LED the engagement. This picks the case-study template.
   *
   * Forcing three very different kinds of work through one layout is what
   * makes a mixed portfolio read as a bag of unrelated skills: a brand
   * identity wants to be seen as a spread, a store wants to be walked through
   * at device scale, and a media account wants to be read as numbers. So each
   * gets its own template. Ads work in particular is the service line most
   * agencies hide behind a testimonial — here it gets the most art direction.
   */
  lead: 'advise' | 'build' | 'grow'
  year: string
  /** Tech-pack callouts shown on hover / in view. */
  measures: Measure[]
  /** Hue used for the case's tint field (deg). Kept near-neutral by design. */
  tint: number
  summary: string
  chapters: { title: string; body: string; measure?: { key: string; value: string } }[]
  /** Present on grow-led cases: eight weeks of real account shape. */
  series?: { week: string; spend: number; roas: number }[]
  /** Present on grow-led cases: the creative that ran. */
  creative?: { platform: string; hook: string; ctr: string; tint: number }[]
  /** Present on build-led cases: what was measured on the shipped store. */
  vitals?: { key: string; before: string; after: string }[]
  /** Present on advise-led cases: the position, set as a spread. */
  position?: { statement: string; rejected: string[] }
}

export const WORK: CaseStudy[] = [
  {
    slug: 'maison-verre',
    folio: '001',
    client: 'Maison Verre',
    headline: 'A wholesale label that learned to sell direct.',
    category: 'Ready-to-wear',
    lanes: ['advise', 'build', 'grow'],
    lead: 'grow',
    year: '2025',
    tint: 34,
    measures: [
      { key: 'ROAS', value: '4.8×', edge: 'right', at: 0.3 },
      { key: 'AOV', value: '+38%', edge: 'right', at: 0.62 },
      { key: 'LCP', value: '0.9s', edge: 'left', at: 0.45 },
    ],
    summary:
      'Fourteen years of wholesale, no direct channel, and a founder who did not want to look like everyone else on the shelf. We rewrote the position, built the store, and ran the first four seasons of media against it.',
    chapters: [
      {
        title: 'The position',
        body: 'The brand was describing itself by fabric and by city. Neither is a position — both are facts. We moved it to what the customer actually buys: permission to wear one thing for a decade.',
        measure: { key: 'SKU', value: '240 → 96' },
      },
      {
        title: 'The store',
        body: 'Shopify Plus, custom front end, and a product page rebuilt around the single question the customer kept asking in wholesale: will this still fit me in five years.',
        measure: { key: 'LCP', value: '2.4s → 0.9s' },
      },
      {
        title: 'The media',
        body: 'Meta prospecting against the new position rather than the old catalogue, Shopping restructured around the reduced range. Blended ROAS reported monthly, in full.',
        measure: { key: 'ROAS', value: '4.8× blended' },
      },
    ],
    series: [
      { week: 'W1', spend: 4200, roas: 2.1 },
      { week: 'W2', spend: 5100, roas: 2.6 },
      { week: 'W3', spend: 6800, roas: 3.2 },
      { week: 'W4', spend: 8400, roas: 3.9 },
      { week: 'W5', spend: 9100, roas: 4.1 },
      { week: 'W6', spend: 11200, roas: 4.4 },
      { week: 'W7', spend: 12600, roas: 4.7 },
      { week: 'W8', spend: 14100, roas: 4.8 },
    ],
    creative: [
      { platform: 'Meta — Advantage+', hook: 'The coat you stop replacing', ctr: '2.4%', tint: 34 },
      { platform: 'Meta — Prospecting', hook: 'Fourteen years of wholesale. One shop.', ctr: '1.9%', tint: 30 },
      { platform: 'Google — Shopping', hook: 'Maison Verre — Wool Overcoat', ctr: '3.1%', tint: 40 },
    ]
  },
  {
    slug: 'atelier-nord',
    folio: '002',
    client: 'Atelier Nord',
    headline: 'Made-to-order, without the six-week email thread.',
    category: 'Tailoring',
    lanes: ['build', 'grow'],
    lead: 'build',
    year: '2025',
    tint: 210,
    measures: [
      { key: 'CVR', value: '+2.4pp', edge: 'right', at: 0.35 },
      { key: 'CPA', value: '−41%', edge: 'right', at: 0.68 },
    ],
    summary:
      'A tailoring house taking orders by email. We built the configurator and the measurement flow, then bought the traffic that could afford it.',
    chapters: [
      {
        title: 'The problem',
        body: 'Every order took eleven emails. The website was a brochure with a phone number, and the best customers were the most patient ones — not the most valuable.',
      },
      {
        title: 'The build',
        body: 'A made-to-order configurator with a measurement guide that assumes nothing, and a deposit flow that ends the thread at message two.',
        measure: { key: 'STEPS', value: '11 → 2' },
      },
      {
        title: 'The media',
        body: 'Search against intent, not category. We stopped bidding on "tailor" and started bidding on the eleven questions from the old email threads.',
        measure: { key: 'CPA', value: '−41%' },
      },
    ],
    vitals: [
      { key: 'LCP', before: '3.8s', after: '1.1s' },
      { key: 'INP', before: '410ms', after: '92ms' },
      { key: 'CLS', before: '0.24', after: '0.00' },
      { key: 'Steps to order', before: '11', after: '2' },
    ]
  },
  {
    slug: 'sable-studio',
    folio: '003',
    client: 'Sable',
    headline: 'One drop a season, sold out in nine hours.',
    category: 'Accessories',
    lanes: ['advise', 'grow'],
    lead: 'advise',
    year: '2024',
    tint: 18,
    measures: [
      { key: 'SELL-THRU', value: '100%', edge: 'right', at: 0.4 },
      { key: 'LIST', value: '12k', edge: 'left', at: 0.55 },
    ],
    summary:
      'Scarcity is a strategy, not an accident. We rebuilt the drop calendar and the waiting-list mechanic, and spent nothing on discounting.',
    chapters: [
      {
        title: 'The calendar',
        body: 'Four drops a year became two. Half the product, twice the anticipation, and a waiting list that finally meant something.',
        measure: { key: 'DROPS', value: '4 → 2' },
      },
      {
        title: 'The list',
        body: 'Paid social bought list signups, not sales. The list bought the sales. Cheaper, and it compounds season over season.',
        measure: { key: 'LIST', value: '1.4k → 12k' },
      },
    ],
    position: {
      statement: 'Sable does not sell more. It sells twice a year, and the rest of the year it is waiting.',
      rejected: [
        'Considered craftsmanship for the modern woman',
        'Accessible luxury, made to last',
        'Quiet luxury for the everyday',
      ],
    }
  },
  {
    slug: 'north-cove',
    folio: '004',
    client: 'North Cove',
    headline: 'A catalogue of 900 became a shop of 90.',
    category: 'Outerwear',
    lanes: ['advise', 'build'],
    lead: 'build',
    year: '2024',
    tint: 200,
    measures: [
      { key: 'SKU', value: '900 → 90', edge: 'right', at: 0.32 },
      { key: 'REV/SKU', value: '+610%', edge: 'right', at: 0.66 },
    ],
    summary:
      'The range had grown by addition for nine years. We cut it, restructured the feed around what was left, and rebuilt the site to show depth instead of breadth.',
    chapters: [
      {
        title: 'The cut',
        body: 'Nine years of "and also". We ranked every SKU by margin and by whether it made the next one easier to sell. Ninety survived.',
        measure: { key: 'SKU', value: '900 → 90' },
      },
      {
        title: 'The rebuild',
        body: 'A store built for ninety products looks nothing like a store built for nine hundred. Navigation became editorial. Search became almost unnecessary.',
        measure: { key: 'REV/SKU', value: '+610%' },
      },
    ],
    vitals: [
      { key: 'LCP', before: '4.2s', after: '0.9s' },
      { key: 'INP', before: '260ms', after: '78ms' },
      { key: 'CLS', before: '0.11', after: '0.00' },
      { key: 'SKUs in nav', before: '900', after: '90' },
    ]
  },
  {
    slug: 'lume',
    folio: '005',
    client: 'Lume',
    headline: 'Launched with no audience and no discount.',
    category: 'Footwear',
    lanes: ['advise', 'build', 'grow'],
    lead: 'grow',
    year: '2026',
    tint: 46,
    measures: [
      { key: 'D-30', value: '£84k', edge: 'right', at: 0.36 },
      { key: 'ROAS', value: '3.1×', edge: 'right', at: 0.7 },
      { key: 'CLS', value: '0.00', edge: 'left', at: 0.5 },
    ],
    summary:
      'A first collection, a founder, and nine months. Position, identity, store and launch media — one studio, one timeline.',
    chapters: [
      {
        title: 'From nothing',
        body: 'No name, no customers, no proof. We started with the one thing the founder would not compromise on and built the position outward from it.',
      },
      {
        title: 'The launch',
        body: 'Thirty days of media against a store that had been load-tested and a feed that had been merchandised. No launch discount — the position would not survive one.',
        measure: { key: 'D-30', value: '£84k' },
      },
    ],
    series: [
      { week: 'W1', spend: 1800, roas: 1.2 },
      { week: 'W2', spend: 2400, roas: 1.9 },
      { week: 'W3', spend: 3600, roas: 2.4 },
      { week: 'W4', spend: 4900, roas: 2.6 },
      { week: 'W5', spend: 6200, roas: 2.9 },
      { week: 'W6', spend: 7400, roas: 3.0 },
      { week: 'W7', spend: 8100, roas: 3.1 },
      { week: 'W8', spend: 9300, roas: 3.1 },
    ],
    creative: [
      { platform: 'Meta — Prospecting', hook: 'A first collection, no discount', ctr: '1.7%', tint: 46 },
      { platform: 'Meta — Retargeting', hook: 'Still thinking about the boot?', ctr: '3.8%', tint: 44 },
      { platform: 'Google — Search', hook: 'Lume — handmade leather boots', ctr: '4.2%', tint: 50 },
    ]
  },
  {
    slug: 'halden',
    folio: '006',
    client: 'Halden',
    headline: 'The rebrand that survived contact with the media budget.',
    category: 'Denim',
    lanes: ['advise', 'grow'],
    lead: 'advise',
    year: '2025',
    tint: 226,
    measures: [
      { key: 'CTR', value: '+2.9×', edge: 'right', at: 0.42 },
      { key: 'CPM', value: '−22%', edge: 'left', at: 0.6 },
    ],
    summary:
      'Most rebrands look best in the deck. We built this one to be tested — every element designed knowing it would end up as a 4:5 ad at 40% viewport.',
    chapters: [
      {
        title: 'Designed for the feed',
        body: 'The identity was drawn at ad size first and at billboard size second. It is the reverse of how most fashion identities are made, and it is why the creative test won.',
        measure: { key: 'CTR', value: '+2.9×' },
      },
    ],
    position: {
      statement: 'Denim designed at ad size. If it does not survive a 4:5 crop at 40% viewport, it does not ship.',
      rejected: [
        'Heritage denim, reimagined',
        'Authentic craft since 1994',
        'Denim for a new generation',
      ],
    }
  },
]

export const laneLabel = { advise: 'Advise', build: 'Build', grow: 'Grow' } as const
