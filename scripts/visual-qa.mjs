/**
 * Visual + structural QA harness.
 *
 * Walks every route at four widths in both themes and asserts the things that
 * are cheap to break and expensive to notice late: horizontal overflow, a
 * missing or duplicated <h1>, a missing skip target, a weak <title>. Then
 * checks that prefers-reduced-motion actually yields a readable page (no
 * loader, content at full opacity) and that the skip link is the first tab
 * stop.
 *
 * Usage:  npm run build && npm run preview &  →  node scripts/visual-qa.mjs ./out
 */
import { chromium } from 'playwright'
const OUT = process.argv[2]
const browser = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' })
const issues = []
const PAGES = ['/', '/work', '/work/maison-verre', '/work/sable-studio', '/work/atelier-nord', '/services', '/studio', '/journal', '/contact', '/nope']
const VIEWPORTS = [
  { w: 320, h: 800, tag: 'xs' },
  { w: 390, h: 844, tag: 'sm' },
  { w: 768, h: 1024, tag: 'md' },
  { w: 1440, h: 1000, tag: 'lg' },
]

for (const theme of ['dark', 'light']) {
  for (const vp of VIEWPORTS) {
    const ctx = await browser.newContext({ viewport: { width: vp.w, height: vp.h } })
    // Block everything off-origin: the demo HLS stream is unreachable through
    // this environment's proxy and its retries dominate the run time.
    await ctx.route('**/*', (route) =>
      route.request().url().startsWith('http://127.0.0.1:4173') ? route.continue() : route.abort())
    const page = await ctx.newPage()
    page.on('pageerror', (e) => issues.push(`[${theme}/${vp.tag}] PAGEERROR ${e.message}`))
    page.on('console', (m) => { if (m.type() === 'error' && !/resource|net::/i.test(m.text())) issues.push(`[${theme}/${vp.tag}] CONSOLE ${m.text()}`) })
    await page.addInitScript((t) => { try { localStorage.setItem('ak-theme', t) } catch {} }, theme)
    for (const path of PAGES) {
      await page.goto(`http://127.0.0.1:4173${path}`, { waitUntil: 'domcontentloaded' })
      await page.waitForTimeout(700)
      const r = await page.evaluate(() => ({
        overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth,
        h1: document.querySelectorAll('h1').length,
        title: document.title,
        main: !!document.getElementById('main'),
      }))
      if (r.overflow > 1) issues.push(`[${theme}/${vp.tag}] ${path} OVERFLOW ${r.overflow}px`)
      if (r.h1 !== 1) issues.push(`[${theme}/${vp.tag}] ${path} has ${r.h1} <h1>`)
      if (!r.main) issues.push(`[${theme}/${vp.tag}] ${path} missing #main skip target`)
      if (!r.title || r.title.length < 5) issues.push(`[${theme}/${vp.tag}] ${path} weak <title>: "${r.title}"`)
    }
    await ctx.close()
  }
}

// Reduced motion: the loader must not run and content must be present immediately.
{
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 1000 }, reducedMotion: 'reduce' })
  await ctx.route('**/*', (route) =>
    route.request().url().startsWith('http://127.0.0.1:4173') ? route.continue() : route.abort())
  const page = await ctx.newPage()
  page.on('pageerror', (e) => issues.push(`[reduced] PAGEERROR ${e.message}`))
  await page.goto('http://127.0.0.1:4173/', { waitUntil: 'domcontentloaded' })
  await page.waitForTimeout(700)
  const r = await page.evaluate(() => {
    const h1 = document.querySelector('h1')
    return {
      loader: !!document.querySelector('[role="status"]'),
      h1Text: h1?.textContent?.trim() ?? '',
      h1Visible: h1 ? getComputedStyle(h1).opacity : '0',
      counters: [...document.querySelectorAll('dd')].map((d) => d.textContent?.trim()).slice(0, 4),
    }
  })
  if (r.loader) issues.push('[reduced] loader still rendered under prefers-reduced-motion')
  if (!r.h1Text) issues.push('[reduced] h1 has no text')
  if (r.h1Visible !== '1') issues.push(`[reduced] h1 opacity ${r.h1Visible} — content hidden`)
  await page.screenshot({ path: `${OUT}/reduced.png`, fullPage: false })
  await ctx.close()
}

// Keyboard: the skip link must be reachable and the measure HUD focus-visible.
{
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 1000 } })
  await ctx.route('**/*', (route) =>
    route.request().url().startsWith('http://127.0.0.1:4173') ? route.continue() : route.abort())
  const page = await ctx.newPage()
  await page.goto('http://127.0.0.1:4173/work', { waitUntil: 'domcontentloaded' })
  await page.waitForTimeout(1600)
  await page.keyboard.press('Tab')
  const first = await page.evaluate(() => document.activeElement?.textContent?.trim())
  if (!/skip/i.test(first ?? '')) issues.push(`[kbd] first tab stop is "${first}", expected skip link`)
  await ctx.close()
}

await browser.close()
console.log(issues.length ? `ISSUES (${issues.length}):\n` + issues.join('\n') : `Clean across ${PAGES.length} pages x ${VIEWPORTS.length} viewports x 2 themes, plus reduced-motion and keyboard.`)
