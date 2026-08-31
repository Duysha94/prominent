/** PLACEHOLDER — replace with the studio's own writing. See data/work.ts. */

export type Note = {
  slug: string
  folio: string
  title: string
  standfirst: string
  category: 'Strategy' | 'Identity' | 'Production' | 'Presence'
  readTime: string
  date: string
}

export const JOURNAL: Note[] = [
  {
    slug: 'showing-at-fashion-week',
    folio: '004',
    title: 'What it actually costs to show at a fashion week',
    standfirst:
      'Placeholder standfirst. The real version should be the honest breakdown a young designer cannot find anywhere else — venue, casting, production, PR, and what a slot is genuinely worth.',
    category: 'Production',
    readTime: '9 min',
    date: '2026-07-14',
  },
  {
    slug: 'identity-at-ad-size',
    folio: '003',
    title: 'Design the identity at ad size first',
    standfirst:
      'Placeholder standfirst. A logo that only works at billboard scale is a logo that will be cropped, shrunk and ignored by the algorithm that decides whether anyone sees it.',
    category: 'Identity',
    readTime: '6 min',
    date: '2026-05-02',
  },
  {
    slug: 'personal-brand-founders',
    folio: '002',
    title: 'Your company is not your personal brand',
    standfirst:
      'Placeholder standfirst. Why founders in fashion are usually better known than their labels — and what to do when it is the other way round.',
    category: 'Strategy',
    readTime: '7 min',
    date: '2026-03-11',
  },
  {
    slug: 'website-before-the-campaign',
    folio: '001',
    title: 'Fix the website before you buy the traffic',
    standfirst:
      'Placeholder standfirst. Every pound of promotion lands on a page. If that page is slow or unclear, promotion just buys a faster exit.',
    category: 'Presence',
    readTime: '5 min',
    date: '2026-01-28',
  },
]
