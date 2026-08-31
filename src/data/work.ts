/**
 * ─────────────────────────────────────────────────────────────────────────────
 * PLACEHOLDER CASE STUDIES.
 *
 * The founders will replace these with real projects. They are kept here so
 * the layouts can be judged with realistic content lengths, and they are
 * deliberately varied across all three case templates so the range is visible.
 *
 * Client names, figures and outcomes are invented. Nothing here should reach a
 * client-facing site.
 * ─────────────────────────────────────────────────────────────────────────────
 */
import type { Measure } from '../components/primitives/MeasureFrame'

export type CaseStudy = {
  slug: string
  folio: string
  client: string
  headline: string
  category: string
  /** Which movements ran. Drives the filter. */
  movements: Array<'strategy' | 'identity' | 'production' | 'presence'>
  /**
   * The movement that LED the work. This picks the case template — a
   * collection launch, an identity and a website are not the same kind of
   * evidence, and forcing them through one layout is what makes a mixed
   * portfolio read as unrelated departments.
   */
  lead: 'strategy' | 'identity' | 'production' | 'presence'
  year: string
  measures: Measure[]
  tint: number
  summary: string
  chapters: { title: string; body: string; measure?: { key: string; value: string } }[]
  /** production-led: the show or campaign, week by week. */
  series?: { week: string; reach: number; press: number }[]
  /** production-led: what was made. */
  looks?: { label: string; note: string; tint: number }[]
  /** presence-led: what shipped and what it measures. */
  vitals?: { key: string; before: string; after: string }[]
  /** strategy/identity-led: the position and what it beat. */
  position?: { statement: string; rejected: string[] }
}

export const WORK: CaseStudy[] = [
  {
    slug: 'placeholder-collection-launch',
    folio: '001',
    client: 'Client Name',
    headline: 'A first collection, shown in London.',
    category: 'Collection launch',
    movements: ['strategy', 'identity', 'production'],
    lead: 'production',
    year: '2026',
    tint: 34,
    measures: [
      { key: 'LOOKS', value: '24', edge: 'right', at: 0.3 },
      { key: 'PRESS', value: '38', edge: 'right', at: 0.62 },
      { key: 'RUNWAY', value: 'LDN', edge: 'left', at: 0.45 },
    ],
    summary:
      'Placeholder. A designer with a finished collection and no route to an audience. Position, identity and a runway slot inside one season — replace this text with the real project.',
    chapters: [
      {
        title: 'The position',
        body: 'Placeholder chapter. Describe what the brand was saying about itself before, and what it says now.',
        measure: { key: 'DECK', value: '1 page' },
      },
      {
        title: 'The show',
        body: 'Placeholder chapter. Describe the production — the venue, the casting, the running order, the press list.',
        measure: { key: 'LOOKS', value: '24' },
      },
      {
        title: 'The coverage',
        body: 'Placeholder chapter. Describe what the show produced afterwards: press, stockists, follow-on work.',
        measure: { key: 'PRESS', value: '38 titles' },
      },
    ],
    series: [
      { week: 'W1', reach: 12000, press: 0 },
      { week: 'W2', reach: 21000, press: 2 },
      { week: 'W3', reach: 38000, press: 5 },
      { week: 'W4', reach: 74000, press: 11 },
      { week: 'W5', reach: 143000, press: 19 },
      { week: 'W6', reach: 168000, press: 27 },
      { week: 'W7', reach: 181000, press: 34 },
      { week: 'W8', reach: 194000, press: 38 },
    ],
    looks: [
      { label: 'Opening look', note: 'Placeholder — replace with campaign imagery', tint: 34 },
      { label: 'Runway 12/24', note: 'Placeholder', tint: 40 },
      { label: 'Backstage', note: 'Placeholder', tint: 28 },
      { label: 'Front row', note: 'Placeholder', tint: 46 },
    ],
  },
  {
    slug: 'placeholder-brand-identity',
    folio: '002',
    client: 'Client Name',
    headline: 'An identity built to survive the feed.',
    category: 'Brand identity',
    movements: ['strategy', 'identity'],
    lead: 'identity',
    year: '2026',
    tint: 210,
    measures: [
      { key: 'MARKS', value: '3', edge: 'right', at: 0.35 },
      { key: 'SCALE', value: '4:5 → 6m', edge: 'right', at: 0.68 },
    ],
    summary:
      'Placeholder. An identity drawn at ad size first and at backdrop size second — replace with the real project.',
    chapters: [
      {
        title: 'Drawn at ad size',
        body: 'Placeholder chapter. Describe why the identity was designed for the crop it would actually live in.',
        measure: { key: 'CROP', value: '4:5 first' },
      },
      {
        title: 'The system',
        body: 'Placeholder chapter. Describe the guidelines, the type, the colour and how the team uses it without you.',
      },
    ],
    position: {
      statement: 'Placeholder positioning line — replace with the sentence this brand actually arrived at.',
      rejected: [
        'Placeholder alternative one — the safe version',
        'Placeholder alternative two — the category version',
        'Placeholder alternative three — the trend version',
      ],
    },
  },
  {
    slug: 'placeholder-personal-brand',
    folio: '003',
    client: 'Client Name',
    headline: 'A founder who stopped hiding behind the brand.',
    category: 'Personal brand',
    movements: ['strategy', 'presence'],
    lead: 'strategy',
    year: '2025',
    tint: 18,
    measures: [
      { key: 'AUDIENCE', value: '×4', edge: 'right', at: 0.4 },
      { key: 'SPEAKING', value: '6', edge: 'left', at: 0.55 },
    ],
    summary:
      'Placeholder. Personal brand strategy for a founder whose company was better known than they were.',
    chapters: [
      {
        title: 'The position',
        body: 'Placeholder chapter. Describe what the founder stands for and how it was found.',
      },
      {
        title: 'The channels',
        body: 'Placeholder chapter. Describe where that position was put to work.',
        measure: { key: 'AUDIENCE', value: '×4' },
      },
    ],
    position: {
      statement: 'Placeholder personal positioning line — replace with the real one.',
      rejected: [
        'Placeholder alternative one',
        'Placeholder alternative two',
        'Placeholder alternative three',
      ],
    },
  },
  {
    slug: 'placeholder-website',
    folio: '004',
    client: 'Client Name',
    headline: 'A store that finally looked like the clothes.',
    category: 'E-commerce',
    movements: ['identity', 'presence'],
    lead: 'presence',
    year: '2025',
    tint: 200,
    measures: [
      { key: 'LCP', value: '0.9s', edge: 'right', at: 0.32 },
      { key: 'CVR', value: '+2.4pp', edge: 'right', at: 0.66 },
    ],
    summary:
      'Placeholder. A website and online store rebuilt around the brand rather than around a template.',
    chapters: [
      {
        title: 'The rebuild',
        body: 'Placeholder chapter. Describe what the old site got wrong and what replaced it.',
        measure: { key: 'LCP', value: '3.8s → 0.9s' },
      },
      {
        title: 'The promotion',
        body: 'Placeholder chapter. Describe the digital campaigns that filled it.',
        measure: { key: 'CVR', value: '+2.4pp' },
      },
    ],
    vitals: [
      { key: 'LCP', before: '3.8s', after: '0.9s' },
      { key: 'INP', before: '410ms', after: '92ms' },
      { key: 'CLS', before: '0.24', after: '0.00' },
      { key: 'Steps to buy', before: '7', after: '3' },
    ],
  },
  {
    slug: 'placeholder-campaign',
    folio: '005',
    client: 'Client Name',
    headline: 'A campaign shot in three cities.',
    category: 'Photo & video',
    movements: ['identity', 'production', 'presence'],
    lead: 'production',
    year: '2026',
    tint: 46,
    measures: [
      { key: 'CITIES', value: '3', edge: 'right', at: 0.36 },
      { key: 'ASSETS', value: '120', edge: 'right', at: 0.7 },
    ],
    summary:
      'Placeholder. A seasonal campaign produced across London, Paris and Dubai — replace with the real project.',
    chapters: [
      {
        title: 'The direction',
        body: 'Placeholder chapter. Describe the creative direction and why it fit the position.',
      },
      {
        title: 'The shoot',
        body: 'Placeholder chapter. Describe the production itself.',
        measure: { key: 'ASSETS', value: '120' },
      },
    ],
    series: [
      { week: 'W1', reach: 8000, press: 0 },
      { week: 'W2', reach: 26000, press: 1 },
      { week: 'W3', reach: 51000, press: 4 },
      { week: 'W4', reach: 88000, press: 9 },
      { week: 'W5', reach: 112000, press: 14 },
      { week: 'W6', reach: 131000, press: 18 },
      { week: 'W7', reach: 146000, press: 21 },
      { week: 'W8', reach: 152000, press: 23 },
    ],
    looks: [
      { label: 'London', note: 'Placeholder', tint: 46 },
      { label: 'Paris', note: 'Placeholder', tint: 30 },
      { label: 'Dubai', note: 'Placeholder', tint: 52 },
      { label: 'Film still', note: 'Placeholder', tint: 38 },
    ],
  },
  {
    slug: 'placeholder-relaunch',
    folio: '006',
    client: 'Client Name',
    headline: 'A relaunch that kept the customers it had.',
    category: 'Repositioning',
    movements: ['strategy', 'identity', 'presence'],
    lead: 'strategy',
    year: '2025',
    tint: 226,
    measures: [
      { key: 'RANGE', value: '−60%', edge: 'right', at: 0.42 },
      { key: 'AOV', value: '+38%', edge: 'left', at: 0.6 },
    ],
    summary:
      'Placeholder. A brand relaunch and repositioning for a business that had grown by addition for nine years.',
    chapters: [
      {
        title: 'The cut',
        body: 'Placeholder chapter. Describe what was removed and how that decision was made.',
        measure: { key: 'RANGE', value: '−60%' },
      },
    ],
    position: {
      statement: 'Placeholder repositioning line — replace with the real one.',
      rejected: [
        'Placeholder alternative one',
        'Placeholder alternative two',
        'Placeholder alternative three',
      ],
    },
  },
]

export const movementTag = {
  strategy: 'Strategy',
  identity: 'Identity',
  production: 'Production',
  presence: 'Presence',
} as const
