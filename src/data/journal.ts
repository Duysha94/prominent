/** PLACEHOLDER CONTENT — see data/studio.ts. */

export type Note = {
  slug: string
  folio: string
  title: string
  standfirst: string
  category: 'Advisory' | 'Commerce' | 'Media'
  readTime: string
  date: string
}

export const JOURNAL: Note[] = [
  {
    slug: 'blended-roas',
    folio: '024',
    title: 'Stop reporting last-click ROAS to fashion founders',
    standfirst:
      'It flatters the channel that closes and hides the one that created the demand. Here is the report we send instead, and why founders prefer it even when the number is smaller.',
    category: 'Media',
    readTime: '6 min',
    date: '2026-07-14',
  },
  {
    slug: 'cutting-the-range',
    folio: '023',
    title: 'Every range is 40% too large, and you already know which 40%',
    standfirst:
      'Ranges grow by addition because cutting requires someone to be wrong in public. A method for doing it with numbers instead of opinions.',
    category: 'Advisory',
    readTime: '9 min',
    date: '2026-06-02',
  },
  {
    slug: 'pdp-questions',
    folio: '022',
    title: 'Your product page should answer the emails you keep getting',
    standfirst:
      'The best PDP copy is already written — it is in your inbox, in the replies you send twice a week. A short exercise for finding it.',
    category: 'Commerce',
    readTime: '5 min',
    date: '2026-04-28',
  },
  {
    slug: 'identity-at-ad-size',
    folio: '021',
    title: 'Design the identity at ad size first',
    standfirst:
      'A logo that only works at billboard scale is a logo that will be cropped, shrunk and ignored by the algorithm that decides whether anyone sees it.',
    category: 'Advisory',
    readTime: '7 min',
    date: '2026-03-11',
  },
]
