import { chromium } from 'playwright'
const OUT = process.argv[2]
const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' })
const PAGES = ['front-page', 'archive-portfolio', 'single-portfolio', 'template-services', 'template-about', 'template-contact', 'home', 'single']
const issues = []
for (const theme of ['dark', 'light']) {
  for (const w of [390, 1366]) {
    const c = await b.newContext({ viewport: { width: w, height: 940 } })
    const p = await c.newPage()
    p.on('pageerror', (e) => issues.push(`[${theme}/${w}] PAGEERROR ${e.message}`))
    p.on('console', (m) => { if (m.type() === 'error' && !/favicon|ERR_TUNNEL_CONNECTION_FAILED/.test(m.text())) issues.push(`[${theme}/${w}] ${m.text().slice(0, 110)}`) })
    await p.addInitScript((t) => { try { localStorage.setItem('ak-theme', t) } catch {} }, theme)
    for (const t of PAGES) {
      await p.goto(`http://127.0.0.1:4180/index-${t}.html`, { waitUntil: 'networkidle' })
      await p.waitForTimeout(700)
      const h = await p.evaluate(() => document.body.scrollHeight)
      for (let i = 0; i < Math.min(16, Math.ceil(h / 700)); i++) { await p.mouse.wheel(0, 700); await p.waitForTimeout(45) }
      await p.waitForTimeout(1200)
      const r = await p.evaluate(() => ({
        overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
        h1: document.querySelectorAll('h1').length,
        hidden: [...document.querySelectorAll('.ak-cut-half')].filter((el) => getComputedStyle(el).opacity === '0').length,
        fonts: document.fonts.status,
      }))
      if (r.overflow > 1) issues.push(`[${theme}/${w}] ${t} OVERFLOW ${r.overflow}px`)
      if (r.h1 !== 1) issues.push(`[${theme}/${w}] ${t} has ${r.h1} h1`)
      if (r.hidden > 0) issues.push(`[${theme}/${w}] ${t} ${r.hidden} headline halves stuck at 0`)
      if (r.fonts !== 'loaded') issues.push(`[${theme}/${w}] ${t} fonts ${r.fonts}`)
      if (theme === 'dark' && w === 1366) {
        await p.evaluate(() => window.scrollTo(0, 0)); await p.waitForTimeout(600)
        await p.screenshot({ path: `${OUT}/wp-${t}.png`, fullPage: true })
      }
    }
    await c.close()
  }
}
await b.close()
console.log(issues.length ? `ISSUES (${issues.length}):\n` + issues.join('\n') : `Clean: ${PAGES.length} templates x 2 widths x 2 themes.`)
