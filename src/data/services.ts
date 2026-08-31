/**
 * The practice, in four movements.
 *
 * The founders' brief lists nine services. Presented as a list, nine services
 * read as a menu — and a menu invites a client to buy one item and go
 * elsewhere for the rest, which is precisely the opposite of what this studio
 * is for. Their own sentence gives the better structure:
 *
 *   "From the initial idea to international presence."
 *
 * That is a sequence, so the site is built as one. Every service from the
 * brief appears exactly once, in the movement where it actually belongs, and
 * each movement states what it hands to the next — so the argument for the
 * whole practice is built into the layout rather than asserted in a paragraph.
 */

export type Movement = {
  id: 'strategy' | 'identity' | 'production' | 'presence'
  index: string
  title: string
  /** What this movement decides or makes, in one line. */
  claim: string
  /** What it leaves the next movement. This is the spine of the page. */
  handover: string
  deliverables: string[]
  /** The thing this movement is judged on. */
  measure: { key: string; value: string; note: string }
  duration: string
}

export const MOVEMENTS: Movement[] = [
  {
    id: 'strategy',
    index: '01',
    title: 'Strategy',
    claim: 'Decide what the brand is, who it is for, and what it will not be.',
    handover: 'Leaves Identity a written position to design against, not a mood board.',
    deliverables: [
      'Brand concept development',
      'Brand positioning and strategy',
      'Brand relaunch and repositioning',
      'Brand philosophy and identity foundations',
      'Personal brand strategy for founders and public figures',
      'Strategic guidance for business growth',
    ],
    measure: {
      key: 'POSITION',
      value: '1 page',
      note: 'A position that needs twelve slides is one nobody in your team will remember on a Tuesday.',
    },
    duration: '4–8 weeks',
  },
  {
    id: 'identity',
    index: '02',
    title: 'Identity',
    claim: 'Give the position a face that survives contact with the real world.',
    handover: 'Leaves Production a system that holds at campaign scale and at 4:5.',
    deliverables: [
      'Brand identity development',
      'Logo and identity design',
      'Creative and visual direction',
      'Brand guidelines',
      'Visual storytelling',
      'Content direction',
    ],
    measure: {
      key: 'SCALE',
      value: '4:5 → 6m',
      note: 'Drawn at ad size first and at runway backdrop size second. Most identities are made the other way round.',
    },
    duration: '4–10 weeks',
  },
  {
    id: 'production',
    index: '03',
    title: 'Production',
    claim: 'Make it exist — the campaign, the event, the show, the room.',
    handover: 'Leaves Presence real assets and real coverage, not renders.',
    deliverables: [
      'Photo campaigns and creative direction',
      'Promotional video production',
      'Brand presentations and product launches',
      'Creative events and brand experiences',
      'Independent fashion shows',
      'Fashion show production during international fashion weeks',
      'Industry PR and press support',
    ],
    measure: {
      key: 'RUNWAY',
      value: 'Ours',
      note: 'We founded London Fashion Day and Odessa Fashion Day. Most studios have to ask for a slot.',
    },
    duration: 'Per production',
  },
  {
    id: 'presence',
    index: '04',
    title: 'Presence',
    claim: 'Put it where people already are, and make it findable.',
    handover: 'Feeds what the audience does back into Strategy — the position sharpens every season.',
    deliverables: [
      'Website creation and development',
      'E-commerce and online stores',
      'Social media presence and digital content',
      'Online brand positioning',
      'Digital advertising campaigns',
      'Google, YouTube and Meta promotion',
      'Audience growth and engagement',
    ],
    measure: {
      key: 'FOUND',
      value: 'Organic',
      note: 'Paid reach you rent. Search and press you keep. We build for both, and report both.',
    },
    duration: 'Ongoing',
  },
]

/** Kept as a short alias — several components read the movement titles. */
export const movementLabel = {
  strategy: 'Strategy',
  identity: 'Identity',
  production: 'Production',
  presence: 'Presence',
} as const
