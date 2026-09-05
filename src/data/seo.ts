import { STUDIO, FOUNDERS } from './studio'
import { MOVEMENTS } from './services'
import { WORK } from './work'

/**
 * SEO is content, not a plugin.
 *
 * The rules this file follows:
 *  - one intent per page, stated in the title, with the city where it matters;
 *  - descriptions written to be read by a human in a result list, not stuffed;
 *  - every claim in a description is one the page actually makes;
 *  - titles under ~60 characters and descriptions under ~155, so neither is
 *    truncated into nonsense.
 *
 * These are baked into static HTML at build time by scripts/prerender.mjs. A
 * title written by JavaScript after paint is a title a crawler may never see —
 * which is the single biggest SEO failure mode of a React site, and the reason
 * the production WordPress build renders all of this server-side.
 */

export type PageSeo = {
  path: string
  title: string
  description: string
  /** Where this page sits in the breadcrumb trail. */
  breadcrumb: string
  /** Additional JSON-LD for this page, beyond the site-wide graph. */
  schema?: Record<string, unknown>
}

const SITE = STUDIO.url

export const PAGES: PageSeo[] = [
  {
    path: '/',
    title: 'AK Brand Development Studio — Fashion & Brand Advisory, London',
    description:
      'Independent brand and fashion practice in London. Brand strategy, identity, campaign and fashion show production, and digital presence — from the first idea to international visibility.',
    breadcrumb: 'Home',
  },
  {
    path: '/work',
    title: 'Work — Brand, Identity and Fashion Production Projects',
    description:
      'Selected projects across brand strategy, identity, campaign and fashion show production, and websites. Filter by the movement each project came through.',
    breadcrumb: 'Work',
  },
  {
    path: '/services',
    title: 'Services — Brand Strategy, Identity, Production & Presence',
    description:
      'Brand development and positioning, personal brand strategy, identity and creative direction, photo and video campaigns, fashion show production, PR, websites and digital promotion.',
    breadcrumb: 'Services',
  },
  {
    path: '/studio',
    title: 'About the Studio — Founded by Kostiantyn Lieontiev & Andrii Karakushan',
    description:
      'An independent creative and strategic practice in London. Founded by Kostiantyn Lieontiev, producer of London Fashion Day and Odessa Fashion Day, and Andrii Karakushan.',
    breadcrumb: 'About',
  },
  {
    path: '/journal',
    title: 'Journal — Notes on Brand Strategy and Fashion Production',
    description:
      'Written notes on brand positioning, identity design, fashion show production and digital presence, from the studio that does the work.',
    breadcrumb: 'Journal',
  },
  {
    path: '/contact',
    title: 'Start a Project — AK Brand Development Studio, London',
    description:
      'Four questions and a spec sheet back. Work out which parts of the practice your brand actually needs before you talk to anyone.',
    breadcrumb: 'Contact',
  },
  ...WORK.map((c) => ({
    path: `/work/${c.slug}`,
    title: `${c.headline.replace(/\.$/, '')} — ${c.category}`,
    description: c.summary.slice(0, 154),
    breadcrumb: c.client,
  })),
]

export const seoFor = (path: string) => PAGES.find((p) => p.path === path)

/**
 * The site-wide JSON-LD graph.
 *
 * ProfessionalService rather than a bare Organization, because that is what
 * this is and it carries address, area served and the service catalogue.
 * The founders are separate Person nodes referenced by @id, so a search engine
 * can connect Kostiantyn to London Fashion Day rather than treating the two as
 * unrelated strings on one page.
 */
export function siteGraph() {
  return {
    '@context': 'https://schema.org',
    '@graph': [
      {
        '@type': 'WebSite',
        '@id': `${SITE}/#website`,
        url: SITE,
        name: STUDIO.name,
        publisher: { '@id': `${SITE}/#studio` },
        inLanguage: 'en-GB',
      },
      {
        '@type': ['ProfessionalService', 'Organization'],
        '@id': `${SITE}/#studio`,
        name: STUDIO.name,
        alternateName: 'AK Studio',
        description: STUDIO.description,
        url: SITE,
        email: STUDIO.email,
        slogan: STUDIO.discipline,
        address: {
          '@type': 'PostalAddress',
          addressLocality: STUDIO.city,
          addressCountry: 'GB',
        },
        areaServed: STUDIO.cities.map((c) => ({ '@type': 'City', name: c })),
        knowsAbout: [
          'Brand development',
          'Brand positioning',
          'Personal brand strategy',
          'Brand identity',
          'Creative direction',
          'Fashion consulting',
          'Fashion show production',
          'Photo and video campaign production',
          'Event production',
          'Public relations',
          'Website development',
          'Digital promotion',
        ],
        founder: FOUNDERS.map((f) => ({ '@id': `${SITE}/studio#${f.id}` })),
        hasOfferCatalog: {
          '@type': 'OfferCatalog',
          name: 'Services',
          itemListElement: MOVEMENTS.map((m) => ({
            '@type': 'OfferCatalog',
            name: m.title,
            itemListElement: m.deliverables.map((d) => ({
              '@type': 'Offer',
              itemOffered: { '@type': 'Service', name: d },
            })),
          })),
        },
      },
      ...FOUNDERS.map((f) => ({
        '@type': 'Person',
        '@id': `${SITE}/studio#${f.id}`,
        name: f.name,
        jobTitle: f.role,
        description: f.bio[0],
        worksFor: { '@id': `${SITE}/#studio` },
        ...(f.id === 'kostiantyn-lieontiev'
          ? {
              founderOf: [
                { '@type': 'Event', name: 'London Fashion Day' },
                { '@type': 'Event', name: 'Odessa Fashion Day' },
                { '@type': 'Brand', name: 'KEKA' },
              ],
            }
          : {
              founderOf: [{ '@type': 'Periodical', name: "Cool'baba" }],
            }),
      })),
    ],
  }
}

/** Breadcrumbs for a single page. */
export function breadcrumbFor(page: PageSeo) {
  const trail =
    page.path === '/'
      ? [{ name: 'Home', item: SITE }]
      : page.path.startsWith('/work/')
        ? [
            { name: 'Home', item: SITE },
            { name: 'Work', item: `${SITE}/work` },
            { name: page.breadcrumb, item: `${SITE}${page.path}` },
          ]
        : [
            { name: 'Home', item: SITE },
            { name: page.breadcrumb, item: `${SITE}${page.path}` },
          ]

  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: trail.map((t, i) => ({
      '@type': 'ListItem',
      position: i + 1,
      name: t.name,
      item: t.item,
    })),
  }
}
