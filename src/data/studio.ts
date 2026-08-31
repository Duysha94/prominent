/**
 * ─────────────────────────────────────────────────────────────────────────────
 * PLACEHOLDER CONTENT.
 *
 * The agency's own brief did not reach this session, so every string below is
 * written to be *structurally* right — correct length, tone and shape for the
 * layouts — and factually invented. Replace before anything goes live. Nothing
 * in the design depends on these particular words; the components are sized
 * from the copy, not the other way round.
 * ─────────────────────────────────────────────────────────────────────────────
 */

export const STUDIO = {
  name: 'AK Brand Development Studio',
  legalName: 'AK Brand Development Studio',
  discipline: 'Fashion & Brand Advisory',
  email: 'studio@akbrand.dev',
  city: 'London',
  timeZone: 'Europe/London',
  availability: 'Taking work',
  nextIntake: 'Q4 2026 — two places',
  founded: 2017,

  /** The positioning sentence. Everything else on the site is downstream of it. */
  promise:
    'We take fashion brands from a point of view to a paying customer — the strategy, the identity, the store that sells it, and the media that fills it.',

  /** Why the three lanes belong to one studio. This is the site's core argument. */
  argument:
    'Most brands buy these three things from three companies, and pay for the seams between them. A positioning deck that the site never expresses. A beautiful store the media team cannot make convert. We hold all three, so nothing is lost in the handover.',

  care: [
    'One studio, three lanes',
    'No retainers under three months',
    'Numbers reported monthly, in full',
    'We do not take competing brands',
    'Founded 2017 — 40+ brands',
  ],

  footerColumns: [
    {
      title: 'Studio',
      links: [
        { label: 'Work', to: '/work' },
        { label: 'Services', to: '/services' },
        { label: 'About', to: '/studio' },
        { label: 'Journal', to: '/journal' },
      ],
    },
    {
      title: 'Lanes',
      links: [
        { label: 'Advise', to: '/services#advise' },
        { label: 'Build', to: '/services#build' },
        { label: 'Grow', to: '/services#grow' },
        { label: 'Start a brief', to: '/contact' },
      ],
    },
    {
      title: 'Elsewhere',
      links: [
        { label: 'Instagram', to: 'https://instagram.com' },
        { label: 'LinkedIn', to: 'https://linkedin.com' },
        { label: 'Behance', to: 'https://behance.net' },
        { label: 'Email', to: 'mailto:studio@akbrand.dev' },
      ],
    },
  ],
} as const

/**
 * The live band in the footer.
 *
 * A marquee of service words or client logos is decoration and reads as one.
 * These are figures the studio actually knows and that change week to week, so
 * the band is a readout of the business rather than a moving ornament — and it
 * is the one thing on the site a competitor cannot copy from a template.
 *
 * PLACEHOLDER VALUES: in production these come from the studio's own endpoint
 * (ad platform spend, project tracker, calendar) and are cached server-side.
 */
export const LIVE = [
  { key: 'Spend managed, month to date', value: '£248,400' },
  { key: 'Campaigns live', value: '14' },
  { key: 'Builds shipping this quarter', value: '3' },
  { key: 'Next intake', value: 'Q4 2026 — two places' },
  { key: 'Brands since 2017', value: '40+' },
] as const

/** Credentials that answer "why should a performance budget sit with you". */
export const CREDENTIALS = [
  { label: 'Meta Business Partner', meta: 'Verified 2024' },
  { label: 'Google Partner', meta: 'Search & Shopping' },
  { label: 'Shopify Plus', meta: 'Build partner' },
  { label: 'Klaviyo', meta: 'Certified' },
] as const
