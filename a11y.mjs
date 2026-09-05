import { chromium } from 'playwright'
const browser = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' })
const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } })
const page = await ctx.newPage()
await page.goto('http://127.0.0.1:9410/', { waitUntil: 'domcontentloaded' })
await page.waitForTimeout(3000)
console.log(JSON.stringify(await page.evaluate(() => {
  const h1 = document.querySelector('h1')
  return {
    outerStart: h1.outerHTML.slice(0, 320),
    ariaLabel: h1.getAttribute('aria-label'),
    lineAriaHidden: [...h1.querySelectorAll('.ak-cut-line')].map(l => l.getAttribute('aria-hidden')),
    halfAriaHidden: [...h1.querySelectorAll('.ak-cut-half')].map(l => l.getAttribute('aria-hidden')),
    bottomDisplay: [...h1.querySelectorAll('.ak-cut-half--bottom')].map(l => getComputedStyle(l).display),
  }
}, null), null, 1))
// accessible name from the real a11y tree
const snap = await page.accessibility.snapshot({ root: await page.$('h1') })
console.log('A11Y NAME:', JSON.stringify(snap && snap.name))
await browser.close()
