/**
 * Positioning audit.
 *
 * The studio is a brand x fashion x creative x production x digital practice.
 * It must not read as a web agency or an ad agency with a sideline, and the
 * way that misreading happens is rarely a false sentence — it is ORDER and
 * PROPORTION. A page that names websites and advertising first, or names them
 * far more often than fashion production and image, says "web and ads" no
 * matter what the words claim.
 *
 * So this measures three things per route:
 *   1. banned framings   phrases that state the wrong identity outright
 *   2. first discipline  which practice area the page names FIRST
 *   3. balance           how often each of the six movements is named
 *
 * Website development and digital promotion are GENUINE capabilities and must
 * keep appearing. The failure is prominence, not presence.
 *
 * Usage:  node positioning-audit.mjs      (expects WordPress on :9410)
 */
import { chromium } from 'playwright'

const B = 'http://127.0.0.1:9410'
const PAGES = ['/', '/about/', '/services/', '/work/', '/contact/', '/journal/', '/work/london-fashion-day/']

// The studio's own name contains "Development Studio"; it is not a claim to be
// a development studio.
const BRAND = /AK Brand Development Studio/g

const BANNED = [
  /\bweb (?:design|development) (?:agency|studio)\b/i,
  /\bdevelopment studio\b/i,
  /\bweb agency\b/i,
  /\badvertising agency\b/i,
  /\bwe build websites\b/i,
  /\bgoogle ads\b/i,
  /\bmeta ads\b/i,
  /\bdigital agency\b/i,
]

const MOVEMENTS = {
  Strategy:   /\b(strateg|positioning|brand concept|philosophy)/gi,
  Identity:   /\b(identity|creative direction|logo|guidelines)/gi,
  Image:      /\b(photo|film|video|campaign|imagery|visual storytelling)/gi,
  Experience: /\b(fashion show|runway|event|launch|presentation|production)/gi,
  Digital:    /\b(website|digital|e-?commerce|online)/gi,
  Visibility: /\b(pr\b|public relations|communication|advertis|promotion|audience)/gi,
}

const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' })
const findings = []

for (const path of PAGES) {
  const ctx = await b.newContext({ viewport: { width: 1440, height: 1000 } })
  const p = await ctx.newPage()
  await p.goto(B + path, { waitUntil: 'networkidle' })
  // Visible copy from <main> only: the header, footer and nav repeat on every
  // route and would swamp the measurement.
  const text = (await p.evaluate(() => (document.querySelector('main')?.innerText || '')))
    .replace(/\s+/g, ' ')
  await ctx.close()

  const scrubbed = text.replace(BRAND, 'AK')

  for (const rx of BANNED) {
    const m = scrubbed.match(rx)
    if (m) findings.push(`${path}: banned framing "${m[0]}"`)
  }

  // Which movement is named first, and how the six balance.
  const first = Object.entries(MOVEMENTS)
    .map(([name, rx]) => { rx.lastIndex = 0; const m = rx.exec(scrubbed); return [name, m ? m.index : Infinity] })
    .sort((a, b2) => a[1] - b2[1])
  const counts = Object.fromEntries(Object.entries(MOVEMENTS)
    .map(([name, rx]) => [name, (scrubbed.match(rx) || []).length]))

  const digital = counts.Digital + counts.Visibility
  const craft = counts.Strategy + counts.Identity + counts.Image + counts.Experience

  console.log(`${path}`)
  console.log(`   first named: ${first[0][0]}`)
  console.log(`   ${Object.entries(counts).map(([k, v]) => `${k} ${v}`).join('  ')}`)

  // Digital + Visibility must not dominate the other four combined.
  if (craft > 0 && digital > craft) {
    findings.push(`${path}: digital+visibility (${digital}) outweighs strategy+identity+image+experience (${craft})`)
  }
  if (['/', '/about/', '/services/'].includes(path) && ['Digital', 'Visibility'].includes(first[0][0])) {
    findings.push(`${path}: opens on ${first[0][0]} — this page should lead with the practice, not its digital layer`)
  }
}

await b.close()
console.log('')
findings.forEach(f => console.log('FINDING  ' + f))
console.log(findings.length ? `\n${findings.length} positioning findings` : '\nno positioning findings')
process.exit(findings.length ? 1 : 0)
