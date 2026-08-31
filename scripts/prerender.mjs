/**
 * Bake per-route HTML after the Vite build.
 *
 * This solves two problems with one pass:
 *
 *  1. SEO. A React SPA ships one index.html with one title. Every route then
 *     rewrites the title in JavaScript after paint — which a crawler may never
 *     execute and a social scraper certainly will not. Writing a real HTML file
 *     per route with its own title, description, canonical, Open Graph tags and
 *     JSON-LD is the difference between being indexable and hoping.
 *
 *  2. Deep links on static hosting. GitHub Pages has no server-side rewrite, so
 *     /work/some-case would 404 without a real file at that path.
 *
 *  The production build is WordPress, where all of this is server-rendered
 *  natively; this script is what makes the prototype honest in the meantime.
 */
import { mkdirSync, readFileSync, writeFileSync, copyFileSync, existsSync } from 'node:fs'
import { join, dirname } from 'node:path'

const DIST = 'dist'
const BASE = process.env.BASE_PATH || '/'

const { PAGES, siteGraph, breadcrumbFor, STUDIO } = await import('../.seo-build/seo.js')

const shell = readFileSync(join(DIST, 'index.html'), 'utf8')
const graph = JSON.stringify(siteGraph())

/**
 * A preview build is a staging copy on someone else's domain. Letting it be
 * indexed would split ranking signals with the real site and put placeholder
 * copy into search results, so previews are excluded explicitly rather than
 * relying on nobody finding them.
 */
const PREVIEW = process.env.PREVIEW === '1'

const esc = (s) =>
  String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')

let count = 0
for (const page of PAGES) {
  const canonical = `${STUDIO.url}${page.path === '/' ? '' : page.path}`
  const head = [
    `<title>${esc(page.title)}</title>`,
    `<meta name="description" content="${esc(page.description)}" />`,
    `<link rel="canonical" href="${canonical}" />`,
    `<meta property="og:type" content="${page.path === '/' ? 'website' : 'article'}" />`,
    `<meta property="og:site_name" content="${esc(STUDIO.name)}" />`,
    `<meta property="og:title" content="${esc(page.title)}" />`,
    `<meta property="og:description" content="${esc(page.description)}" />`,
    `<meta property="og:url" content="${canonical}" />`,
    `<meta property="og:locale" content="en_GB" />`,
    `<meta name="twitter:card" content="summary_large_image" />`,
    `<meta name="twitter:title" content="${esc(page.title)}" />`,
    `<meta name="twitter:description" content="${esc(page.description)}" />`,
    PREVIEW ? '<meta name="robots" content="noindex, nofollow" />' : '',
    `<script type="application/ld+json">${graph}</script>`,
    `<script type="application/ld+json">${JSON.stringify(breadcrumbFor(page))}</script>`,
    page.schema ? `<script type="application/ld+json">${JSON.stringify(page.schema)}</script>` : '',
  ]
    .filter(Boolean)
    .join('\n    ')

  // Replace the shell's placeholder title and description rather than
  // appending, so no page ends up with two of either.
  const html = shell
    .replace(/<title>[\s\S]*?<\/title>/, '')
    .replace(/<meta name="description"[^>]*>/, '')
    .replace('</head>', `  ${head}\n  </head>`)

  const out = page.path === '/' ? join(DIST, 'index.html') : join(DIST, page.path, 'index.html')
  mkdirSync(dirname(out), { recursive: true })
  writeFileSync(out, html)
  count++
}

// SPA fallback for any path we did not prerender.
copyFileSync(join(DIST, 'index.html'), join(DIST, '404.html'))

// Sitemap. Static hosting will not generate one, and a sitemap is the cheapest
// way to tell a crawler the route list exists at all.
const sitemap = `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${PAGES.map(
  (p) => `  <url>
    <loc>${STUDIO.url}${p.path === '/' ? '' : p.path}</loc>
    <changefreq>${p.path === '/' ? 'weekly' : 'monthly'}</changefreq>
    <priority>${p.path === '/' ? '1.0' : p.path.startsWith('/work/') ? '0.6' : '0.8'}</priority>
  </url>`,
).join('\n')}
</urlset>
`
writeFileSync(join(DIST, 'sitemap.xml'), sitemap)

writeFileSync(
  join(DIST, 'robots.txt'),
  PREVIEW
    ? 'User-agent: *\nDisallow: /\n'
    : `User-agent: *\nAllow: /\n\nSitemap: ${STUDIO.url}/sitemap.xml\n`,
)

if (BASE !== '/' && existsSync(join(DIST, 'index.html'))) {
  console.log(`  base path: ${BASE}`)
}
console.log(
  `Prerendered ${count} routes + 404 fallback, sitemap.xml and robots.txt` +
    (PREVIEW ? ' (PREVIEW: noindex)' : ''),
)
