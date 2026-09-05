/**
 * Horizontal-overflow sweep.
 *
 * A page that scrolls sideways on a phone is the defect users report as "the
 * site is broken", and it is invisible in a screenshot taken at one width. So
 * every route is measured at every breakpoint, and the check is the document's
 * own scrollWidth — not whether some element looks wide, because an element
 * wider than the viewport inside a clipping ancestor is fine and one outside
 * it is not.
 *
 * Usage:
 *   node width-sweep.mjs                     WordPress on :9410
 *   node width-sweep.mjs --prototype         static prototype on :9500
 */
import { chromium } from 'playwright'

const proto = process.argv.includes('--prototype')
const B = proto ? 'http://127.0.0.1:9500/prototype/' : 'http://127.0.0.1:9410'
const PAGES = proto
  ? ['home.html', 'services.html', 'work.html', 'case-platform.html',
     'case-photo.html', 'case-film.html', 'case-event.html', 'internal.html']
  : ['/', '/work/', '/services/', '/work/london-fashion-day/', '/about/', '/contact/', '/journal/']
const W = [320, 360, 390, 414, 480, 600, 768, 834, 900, 1024, 1180, 1280, 1440, 1728]

const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' })
const ctx = await b.newContext()
const p = await ctx.newPage()
let bad = 0

for (const page of PAGES) {
  for (const w of W) {
    await p.setViewportSize({ width: w, height: 900 })
    await p.goto(B + page, { waitUntil: 'networkidle' })
    const r = await p.evaluate(() => {
      const over = [...document.querySelectorAll('body *')].filter(e => {
        const cs = getComputedStyle(e)
        if (cs.position === 'fixed' || cs.visibility === 'hidden' || cs.display === 'none') return false
        return e.getBoundingClientRect().right > innerWidth + 1
      }).map(e => e.tagName + '.' + (typeof e.className === 'string' ? e.className : '').split(' ').slice(0, 2).join('.'))
      return { doc: document.documentElement.scrollWidth, win: innerWidth, over: [...new Set(over)].slice(0, 4) }
    })
    if (r.doc > r.win + 1) {
      bad++
      console.log(`OVERFLOW ${page} @${w}: ${r.doc} vs ${r.win} (+${r.doc - r.win})  ${r.over.join(', ')}`)
    }
  }
}

await b.close()
console.log(bad ? `\n${bad} overflow cases` : `\nno horizontal overflow at any tested width (${PAGES.length} routes x ${W.length} widths)`)
process.exit(bad ? 1 : 0)
