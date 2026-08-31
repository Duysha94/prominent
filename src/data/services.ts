/** PLACEHOLDER CONTENT — see data/studio.ts. */

export type Lane = {
  id: 'advise' | 'build' | 'grow'
  index: string
  title: string
  /** What the lane changes about the brand, in one line. */
  claim: string
  /** The unifying sentence — how this lane hands over to the next. */
  handover: string
  deliverables: string[]
  /** The measurement this lane is judged on. */
  measure: { key: string; value: string; note: string }
  duration: string
}

export const LANES: Lane[] = [
  {
    id: 'advise',
    index: '01',
    title: 'Advise',
    claim: 'Decide what the brand is, who it is for, and what it refuses to be.',
    handover: 'Leaves the Build lane a written point of view it can lay out, not a mood board.',
    deliverables: [
      'Brand positioning & architecture',
      'Category and competitor mapping',
      'Collection & drop strategy',
      'Naming and verbal identity',
      'Visual identity and art direction',
      'Wholesale vs DTC route to market',
    ],
    measure: { key: 'CLARITY', value: '1 page', note: 'The whole position, on one page, or it is not finished.' },
    duration: '4–8 weeks',
  },
  {
    id: 'build',
    index: '02',
    title: 'Build',
    claim: 'Put the brand somewhere it can actually be bought.',
    handover: 'Leaves the Grow lane a store that is fast, tracked and ready to receive paid traffic.',
    deliverables: [
      'Websites and brand sites',
      'E-commerce — Shopify, Shopify Plus, WooCommerce',
      'Headless and custom front ends',
      'Design systems and component libraries',
      'Product feed and catalogue structure',
      'Analytics, consent and server-side tracking',
    ],
    measure: { key: 'LCP', value: '< 1.2s', note: 'Every store we ship, on a mid-range phone, on 4G.' },
    duration: '6–14 weeks',
  },
  {
    id: 'grow',
    index: '03',
    title: 'Grow',
    claim: 'Fill it with the right people, and prove which ones paid.',
    handover: 'Feeds what the media learns back into the Advise lane — the position gets sharper every quarter.',
    deliverables: [
      'Meta Ads — prospecting, retargeting, Advantage+',
      'Google Ads — Search, Shopping, Performance Max',
      'Creative testing and iteration',
      'Feed optimisation and merchandising',
      'Incrementality and measurement',
      'Monthly reporting, in full, no rounding up',
    ],
    measure: { key: 'ROAS', value: 'Blended', note: 'We report blended, not last-click. It is the only honest number.' },
    duration: 'Ongoing, min. 3 months',
  },
]
