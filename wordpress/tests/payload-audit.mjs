/**
 * Frontend payload audit — what the browser actually transfers.
 *
 * Written to quantify the Zeyna exit. It measures RESPONSE BODIES from a real
 * page load rather than reading file sizes off disk, because what matters is
 * what a visitor downloads: conditional enqueues, handles that never fire, and
 * assets pulled in by other assets all differ from what `ls` suggests.
 *
 * Splits by origin — parent theme, child theme, WordPress core, external —
 * because "total JS went down" is not the claim being made. The claim is that
 * the parent theme's frontend architecture is gone.
 *
 * Usage:
 *   node payload-audit.mjs               all routes, summary
 *   node payload-audit.mjs --json        machine-readable, for before/after diffing
 */
import { chromium } from 'playwright'

const ROUTES = ['/', '/about/', '/services/', '/work/', '/work/london-fashion-day/', '/journal/', '/contact/']
const B = 'http://127.0.0.1:9410'
const asJson = process.argv.includes('--json')

const bucket = url =>
  /themes\/zeyna\//.test(url) ? 'parent'
  : /ak-zeyna-child/.test(url) ? 'child'
  : /wp-includes|wp-admin/.test(url) ? 'core'
  : /127\.0\.0\.1:9410/.test(url) ? 'site'
  : 'external'

const kind = url =>
  /\.js(\?|$)/.test(url) ? 'js'
  : /\.css(\?|$)/.test(url) ? 'css'
  : /\.(woff2?|ttf|otf|eot)(\?|$)/.test(url) ? 'font'
  : /\.(png|jpe?g|gif|webp|avif|svg|mp4|webm)(\?|$)/.test(url) ? 'media'
  : 'other'

const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' })
const report = { routes: {}, totals: {} }

for (const route of ROUTES) {
  const ctx = await b.newContext({ viewport: { width: 1440, height: 1000 } })
  const p = await ctx.newPage()
  const assets = []
  p.on('response', async r => {
    const url = r.url()
    const k = kind(url)
    if (k === 'other') return
    let size = 0
    try { size = (await r.body()).length } catch (e) { /* redirects, aborts */ }
    assets.push({ url, size, bucket: bucket(url), kind: k })
  })
  await p.goto(B + route, { waitUntil: 'networkidle' })
  await ctx.close()

  const agg = {}
  for (const a of assets) {
    const key = `${a.bucket}.${a.kind}`
    agg[key] = (agg[key] || 0) + a.size
  }
  report.routes[route] = {
    requests: assets.length,
    bytes: agg,
    parentAssets: assets.filter(a => a.bucket === 'parent')
      .map(a => ({ file: a.url.split('/themes/zeyna/')[1].split('?')[0], size: a.size })),
  }
}

for (const r of Object.values(report.routes)) {
  for (const [k, v] of Object.entries(r.bytes)) {
    report.totals[k] = (report.totals[k] || 0) + v
  }
}

await b.close()

if (asJson) {
  console.log(JSON.stringify(report, null, 1))
} else {
  const kb = n => (n / 1024).toFixed(1).padStart(7) + ' KB'
  const home = report.routes['/']
  console.log('HOME PAGE (/)')
  console.log('  requests            ', home.requests)
  for (const b2 of ['parent', 'child', 'core', 'site', 'external']) {
    const js = home.bytes[`${b2}.js`] || 0
    const css = home.bytes[`${b2}.css`] || 0
    const font = home.bytes[`${b2}.font`] || 0
    if (!js && !css && !font) continue
    console.log(`  ${b2.padEnd(9)} JS ${kb(js)}   CSS ${kb(css)}   fonts ${kb(font)}`)
  }
  if (home.parentAssets.length) {
    console.log('\n  PARENT THEME ASSETS STILL LOADING:')
    home.parentAssets.forEach(a => console.log(`     ${kb(a.size)}  ${a.file}`))
  } else {
    console.log('\n  PARENT THEME ASSETS STILL LOADING: none')
  }
  const totalParent = (report.totals['parent.js'] || 0) + (report.totals['parent.css'] || 0)
  console.log(`\nACROSS ${ROUTES.length} ROUTES`)
  console.log('  parent theme bytes  ', kb(totalParent))
  console.log('  child theme bytes   ', kb((report.totals['child.js'] || 0) + (report.totals['child.css'] || 0)))
}
