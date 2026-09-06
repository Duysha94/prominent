/**
 * Progressive enhancement — does it actually degrade?
 *
 * Every enhancement in this theme is supposed to fail safely. That claim is
 * cheap to make and only meaningful if something breaks it on purpose, so
 * this deliberately breaks each one and asserts that the content and the
 * navigation still work.
 *
 * The bar is not "it still looks nice". The bar is: a visitor can read the
 * page and get to another page.
 *
 * Usage:  node failure-modes-test.mjs      (expects WordPress on :9410)
 */
import { chromium } from 'playwright'

const B = 'http://127.0.0.1:9410'
const CHROME = '/opt/pw-browsers/chromium-1194/chrome-linux/chrome'
let pass = 0, fail = 0
const ok = (n, c, extra) => { c ? pass++ : fail++; console.log(`${c ? 'PASS' : 'FAIL'}  ${n}${!c && extra ? '  — ' + extra : ''}`) }

const b = await chromium.launch({ executablePath: CHROME })

/* ── 1. JavaScript disabled entirely ─────────────────────────────────────
   The most important case. Navigation must not depend on JS at all. */
{
  const ctx = await b.newContext({ javaScriptEnabled: false, viewport: { width: 1440, height: 1000 } })
  const p = await ctx.newPage()
  await p.goto(B + '/', { waitUntil: 'domcontentloaded' })
  ok('JS off: home renders content', (await p.locator('main h1').count()) > 0)
  ok('JS off: navigation links are real hrefs', await p.evaluate(
    () => [...document.querySelectorAll('#primary-menu a')].every(a => a.getAttribute('href') && !a.getAttribute('href').startsWith('#'))))
  // .first() and force: the header runs a CSS entrance animation, so
  // Playwright's stability wait never settles, and strict mode sees the same
  // href in the header, the panel and the footer.
  await p.locator('a[href$="/work/"]').first().click({ force: true })
  await p.waitForLoadState('domcontentloaded')
  ok('JS off: clicking a link navigates', p.url().includes('/work/'))
  ok('JS off: the new page has content', (await p.locator('main').innerText()).length > 200)
  ok('JS off: no transition overlay is covering the page', await p.evaluate(() => {
    const el = document.querySelector('.ak-transition')
    if (!el) return true
    const cs = getComputedStyle(el)
    return cs.display === 'none' || +cs.opacity === 0
  }))
  // The loader must not be able to strand the page behind a curtain.
  ok('JS off: body is scrollable (loader did not lock it)', await p.evaluate(
    () => getComputedStyle(document.body).overflow !== 'hidden' &&
          getComputedStyle(document.documentElement).overflow !== 'hidden'))
  await ctx.close()
}

/* ── 2. The navigation runtime throws before it can initialise ──────────── */
{
  const ctx = await b.newContext({ viewport: { width: 1440, height: 1000 } })
  const p = await ctx.newPage()
  await p.addInitScript(() => {
    // Break the API ak-nav.js needs, before it runs.
    Object.defineProperty(window, 'fetch', { get() { throw new Error('fetch is broken') } })
  })
  const errs = []
  p.on('pageerror', e => errs.push(e.message))
  await p.goto(B + '/', { waitUntil: 'networkidle' })
  // Let the first-load loader dismiss itself; force-clicking through it would
  // land the click on the overlay and prove nothing.
  await p.waitForTimeout(1200)
  ok('nav throws: page still renders', (await p.locator('main h1').count()) > 0)
  ok('nav throws: ak-nav correctly declined to take over', await p.evaluate(
    () => !!(window.AKNav && window.AKNav.supported === false)))
  await p.locator('a[href$="/about/"]').first().click({ force: true }).catch(() => {})
  await p.waitForLoadState('domcontentloaded')
  ok('nav throws: the browser navigated anyway', p.url().includes('/about/'), p.url())
  await ctx.close()
}

/* ── 3. GSAP unavailable ─────────────────────────────────────────────────
   Every motion module guards on window.gsap. Blocking the library must cost
   the animation and nothing else. */
{
  const ctx = await b.newContext({ viewport: { width: 1440, height: 1000 } })
  const p = await ctx.newPage()
  await p.route('**/assets/vendor/*.js', r => r.abort())
  const errs = []
  p.on('pageerror', e => errs.push(e.message))
  await p.goto(B + '/', { waitUntil: 'networkidle' })
  await p.waitForTimeout(1200)
  ok('no GSAP: no uncaught errors', errs.length === 0, errs[0])
  ok('no GSAP: headline is present and visible', await p.evaluate(() => {
    const h = document.querySelector('main h1')
    if (!h) return false
    const cs = getComputedStyle(h)
    return h.getBoundingClientRect().height > 10 && cs.visibility !== 'hidden'
  }))
  ok('no GSAP: content is readable, not stuck at opacity 0', await p.evaluate(() => {
    const els = [...document.querySelectorAll('main p')].slice(0, 8)
    return els.length > 0 && els.some(e => +getComputedStyle(e).opacity > 0.5)
  }))
  ok('no GSAP: soft navigation still works', await (async () => {
    await p.locator('a[href$="/services/"]').first().click({ force: true }); await p.waitForTimeout(1800)
    return p.url().includes('/services/')
  })())
  await ctx.close()
}

/* ── 4. Reduced motion ───────────────────────────────────────────────────── */
{
  const ctx = await b.newContext({ viewport: { width: 1440, height: 1000 }, reducedMotion: 'reduce' })
  const p = await ctx.newPage()
  await p.goto(B + '/', { waitUntil: 'networkidle' })
  ok('reduced motion: transition overlay is display:none', await p.evaluate(
    () => getComputedStyle(document.querySelector('.ak-transition')).display === 'none'))
  ok('reduced motion: content is visible without scrolling into it', await p.evaluate(() => {
    const els = [...document.querySelectorAll('main .ak-rise')].slice(0, 6)
    return els.length === 0 || els.every(e => +getComputedStyle(e).opacity > 0.5)
  }))
  await p.locator('a[href$="/work/"]').first().click({ force: true }); await p.waitForTimeout(1800)
  ok('reduced motion: navigation still works', p.url().includes('/work/'))
  await ctx.close()
}

/* ── 5. Slow / failing media ─────────────────────────────────────────────
   The hero video is remote. A stalled or blocked one must not hold the page. */
{
  const ctx = await b.newContext({ viewport: { width: 1440, height: 1000 } })
  const p = await ctx.newPage()
  await p.route('**/*.{mp4,webm}', r => r.abort())
  await p.route('**/*.{png,jpg,jpeg,webp,avif}', r => r.abort())
  await p.goto(B + '/', { waitUntil: 'domcontentloaded' })
  await p.waitForTimeout(2500)
  ok('media blocked: page is not stuck behind a loader', await p.evaluate(
    () => !document.querySelector('.ak-loader') ||
          getComputedStyle(document.querySelector('.ak-loader')).opacity === '0'))
  ok('media blocked: body scrolls', await p.evaluate(
    () => getComputedStyle(document.body).overflow !== 'hidden' &&
          getComputedStyle(document.documentElement).overflow !== 'hidden'))
  ok('media blocked: headline still readable', (await p.locator('main h1').first().innerText()).length > 5)
  await ctx.close()
}

/* ── 6. A project with no media at all ───────────────────────────────────
   The website preview is UNAVAILABLE for every project in this install,
   which is the state the record must publish cleanly in. */
{
  const ctx = await b.newContext({ viewport: { width: 1440, height: 1000 } })
  const p = await ctx.newPage()
  await p.goto(B + '/work/london-fashion-day/', { waitUntil: 'networkidle' })
  const text = await p.locator('main').innerText()
  ok('no media: the record still renders a title and spec', text.includes('London Fashion Day') && text.includes('Platform'))
  ok('no media: no capture status wording reaches the visitor',
    !/capture pending|capture failed|preview unavailable/i.test(text))
  ok('no media: no empty preview frame is drawn', (await p.locator('.aks-wp').count()) === 0)
  await ctx.close()
}

await b.close()
console.log(`\n${pass} passed, ${fail} failed`)
process.exit(fail ? 1 : 0)
