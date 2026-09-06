/**
 * Capture the whole site, at the three widths that decide the layout.
 *
 * `capture.mjs` is the library — it knows how to neutralise the scroll-driven
 * reveals so a full-page screenshot shows the finished page rather than
 * everything below the fold in its start state. It had no runner, so every
 * capture pass was written from scratch and each one flattened slightly
 * differently. This is the runner.
 *
 * Usage:  node site-capture.mjs <output-directory>
 *         (expects WordPress on :9410)
 *
 * Nine routes, because they are the nine distinct templates: the front page,
 * a bespoke page, the services page, the project archive, a single project,
 * the blog index, a search result, the contact template and the 404.
 */
import { chromium } from 'playwright'
import { flatten } from './capture.mjs'

const B = 'http://127.0.0.1:9410'
const OUT = process.argv[2]
const ROUTES = [
  ['home', '/'],
  ['about', '/about/'],
  ['services', '/services/'],
  ['work', '/work/'],
  ['case', '/work/london-fashion-day/'],
  ['journal', '/journal/'],
  ['search', '/?s=fashion'],
  ['contact', '/contact/'],
  ['404', '/nope-404/'],
]
const SIZES = [
  ['desktop', 1440, 900],
  ['tablet', 834, 1112],
  ['mobile', 390, 844],
]

const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' })
for (const [sname, width, height] of SIZES) {
  const ctx = await b.newContext({ viewport: { width, height }, deviceScaleFactor: 1 })
  const p = await ctx.newPage()
  for (const [rname, route] of ROUTES) {
    await p.goto(B + route, { waitUntil: 'networkidle' })
    await flatten(p)
    await p.screenshot({ path: `${OUT}/${sname}-${rname}.png`, fullPage: true })
    console.log(`${sname}/${rname}`)
  }
  await ctx.close()
}
await b.close()
