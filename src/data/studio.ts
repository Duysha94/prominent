/**
 * Studio facts, from the founders' own brief.
 *
 * Copy here is either verbatim from that brief or written to sit alongside it.
 * Anything invented is marked. Nothing about a founder, a platform or a
 * credential is invented — those are the load-bearing claims on this site.
 */

export const STUDIO = {
  name: 'AK Brand Development Studio',
  legalName: 'AK Brand Development Studio',
  discipline: 'Fashion & Brand Advisory',
  domain: 'akbrand.studio',
  url: 'https://akbrand.studio',
  email: 'hello@akbrand.studio', // PLACEHOLDER — confirm the real address
  city: 'London',
  country: 'United Kingdom',
  timeZone: 'Europe/London',
  availability: 'Open for projects',

  /** Their own line. It is the spine of the whole site. */
  promise:
    'From the initial idea to international presence, we support projects at every stage of their development.',

  /** The one-paragraph description, from the brief. */
  description:
    'AK Brand Development Studio is an independent creative and strategic practice specialising in brand development, fashion consulting and creative production. We work with designers, entrepreneurs and growing businesses to build strong brands, develop clear positioning and create meaningful visibility.',

  /**
   * The differentiator, stated plainly.
   *
   * Most brand consultancies stop at the deck. This studio produces the
   * campaign, the show and the room — and it owns the platforms its clients
   * appear on. That is not a claim a competitor can copy; it is a fact about
   * who the founders are, which is why it leads.
   */
  argument:
    'Most advisories hand you a deck and wish you luck. We produce the campaign, we produce the show, and we own the runways our clients walk on. Strategy that never leaves the document is not strategy — it is a document.',

  /** Platforms the founders built. The site's strongest proof. */
  platforms: [
    {
      name: 'London Fashion Day',
      role: 'Founded and produced by Kostiantyn Lieontiev',
      note: 'An international platform created to support emerging designers.',
    },
    {
      name: 'Odessa Fashion Day',
      role: 'Founded and produced by Kostiantyn Lieontiev',
      note: 'Built to develop international creative communities.',
    },
    {
      name: 'KEKA',
      role: 'Fashion brand founded by Kostiantyn Lieontiev',
      note: 'Currently being developed for the international market.',
    },
    {
      name: "Cool'baba",
      role: 'Online magazine founded by Andrii Karakushan',
      note: 'A media platform covering fashion, lifestyle and creative industries.',
    },
  ],

  /** Cities the studio has worked across, from the brief. */
  cities: ['London', 'Paris', 'Dubai'],

  care: [
    'Independent practice, not an agency roster',
    'Strategy, production and digital under one roof',
    'We produce the show, not just the deck',
    'London · Paris · Dubai',
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
      title: 'Practice',
      links: [
        { label: 'Strategy', to: '/services#strategy' },
        { label: 'Identity', to: '/services#identity' },
        { label: 'Production', to: '/services#production' },
        { label: 'Presence', to: '/services#presence' },
      ],
    },
    {
      title: 'Elsewhere',
      links: [
        { label: 'Instagram', to: 'https://instagram.com' }, // PLACEHOLDER
        { label: 'LinkedIn', to: 'https://linkedin.com' }, // PLACEHOLDER
        { label: 'Start a project', to: '/contact' },
        { label: 'hello@akbrand.studio', to: 'mailto:hello@akbrand.studio' },
      ],
    },
  ],
} as const

/**
 * The band that runs across the footer.
 *
 * A marquee of service words is decoration and reads as one. These are facts
 * about the studio that a reader can check, which is the only reason a moving
 * band earns its place.
 */
export const LIVE = [
  { key: 'Founded', value: 'London Fashion Day' },
  { key: 'Founded', value: 'Odessa Fashion Day' },
  { key: 'Working across', value: 'London · Paris · Dubai' },
  { key: 'Practice', value: 'Strategy · Identity · Production · Presence' },
] as const

/**
 * The founders. Verbatim from the brief — these bios are the studio's
 * credibility and must not be embellished.
 *
 * Pronouns are as written by the founders themselves in the source brief.
 */
export const FOUNDERS = [
  {
    id: 'kostiantyn-lieontiev',
    name: 'Kostiantyn Lieontiev',
    role: 'Fashion producer, brand strategist',
    bio: [
      'Kostiantyn Lieontiev is a fashion producer, brand strategist and media professional with extensive experience in brand development and creative industries.',
      'Before focusing on international fashion projects, he spent nine years managing a regional branch of a major advertising holding, where he was responsible for business development, advertising campaigns and strategic client development.',
      'He is the founder and producer of the international fashion platforms London Fashion Day and Odessa Fashion Day, created to support emerging designers and develop international creative communities. He is also the founder of the fashion brand KEKA, currently being developed for the international market.',
      'Through his work he has collaborated with designers, brands and creative teams across London, Paris and Dubai.',
    ],
    facts: [
      { key: 'Advertising', value: '9 yrs' },
      { key: 'Platforms', value: '2 founded' },
      { key: 'Cities', value: 'LDN · PAR · DXB' },
    ],
  },
  {
    id: 'andrii-karakushan',
    name: 'Andrii Karakushan',
    role: 'Creative entrepreneur, digital & identity',
    bio: [
      'Andrii Karakushan is a creative entrepreneur specialising in brand development, digital presence and visual communication.',
      'He has experience managing a multi-brand retail space for emerging designers, supporting young fashion brands through retail presentation and brand promotion.',
      "He is the founder of the online magazine Cool'baba, a media platform focused on fashion, lifestyle and creative industries.",
      'His expertise includes website development, brand identity, digital communication and building online ecosystems for businesses and creative projects.',
    ],
    facts: [
      { key: 'Retail', value: 'Multi-brand' },
      { key: 'Media', value: "Cool'baba" },
      { key: 'Focus', value: 'Digital' },
    ],
  },
] as const
