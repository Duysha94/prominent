import { chromium } from 'playwright'
const B = 'http://127.0.0.1:9410'
const ROUTES = ['/', '/work/', '/work/placeholder-fashion-week/', '/services/', '/about/', '/journal/', '/contact/', '/nope-404/']
const browser = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' })
const report = []
for (const r of ROUTES) {
  const row = { route: r, errors: [], overflow: {}, }
  for (const w of [390, 768, 1440]) {
    const ctx = await browser.newContext({ viewport: { width: w, height: 900 } })
    const page = await ctx.newPage()
    page.on('pageerror', e => row.errors.push('JS: ' + e.message))
    const resp = await page.goto(B + r, { waitUntil: 'domcontentloaded' }).catch(e => null)
    await page.waitForTimeout(1500)
    if (w === 1440) {
      row.status = resp ? resp.status() : 'ERR'
      row.title = await page.title()
      row.h1 = await page.evaluate(() => {
        const hs = [...document.querySelectorAll('h1')].map(h => h.textContent.trim())
        return hs
      })
      row.headings = await page.evaluate(() => [...document.querySelectorAll('main h1,main h2,main h3')].map(h => h.tagName + ' ' + h.textContent.trim().slice(0,60)).slice(0,30))
      row.imgNoAlt = await page.evaluate(() => [...document.querySelectorAll('img')].filter(i => !i.hasAttribute('alt')).length)
      row.imgTotal = await page.evaluate(() => document.querySelectorAll('img').length)
      row.landmarks = await page.evaluate(() => ({ main: document.querySelectorAll('main').length, nav: document.querySelectorAll('nav').length, header: document.querySelectorAll('header').length, footer: document.querySelectorAll('footer').length }))
      row.demo = await page.evaluate(() => {
        const t = document.body.innerText
        const hits = []
        for (const s of ['Client Name','Zeyna','PeThemes','Humana','Main Hub','Lorem','ThemeForest','Placeholder','placeholder'])
          if (t.includes(s)) hits.push(s)
        return hits
      })
      row.emptyLinks = await page.evaluate(() => [...document.querySelectorAll('a[href="#"], a[href=""]')].map(a => a.textContent.trim().slice(0,25)))
    }
    const ov = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth)
    row.overflow[w] = ov
    await ctx.close()
  }
  report.push(row)
}
await browser.close()
console.log(JSON.stringify(report, null, 1))
