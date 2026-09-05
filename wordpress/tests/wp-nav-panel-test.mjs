/**
 * The SHIPPED theme's mobile navigation panel — the same contract the
 * prototype is held to, asserted against the real WordPress render.
 *
 * The prototype suite (nav-panel-test.mjs) proves the interaction design. This
 * one proves the theme actually implements it, because those are different
 * claims: the prototype's panel is `#navp` driven by proto.js, the theme's is
 * Zeyna's `#primary-menu` markup driven by ak.js, and a passing prototype says
 * nothing about the theme.
 *
 * Usage:  node wp-nav-panel-test.mjs        (expects WordPress on :9410)
 */
import { chromium } from 'playwright'
const B = 'http://127.0.0.1:9410'
const PAGES = ['/', '/work/', '/services/', '/work/london-fashion-day/', '/about/', '/contact/',
               '/journal/', '/?s=fashion']
const CHROME = '/opt/pw-browsers/chromium-1194/chrome-linux/chrome'
const b = await chromium.launch({ executablePath: CHROME })
let pass = 0, fail = 0
const ok = (n, c) => { c ? pass++ : fail++; console.log(`${c ? 'PASS' : 'FAIL'}  ${n}`) }

for (const path of PAGES) {
  const ctx = await b.newContext({ viewport: { width: 390, height: 844 } })
  const p = await ctx.newPage()
  const errs = []
  p.on('pageerror', e => errs.push(e.message))
  await p.goto(B + path, { waitUntil: 'networkidle' })
  const tag = path === '/' ? 'home' : path.replace(/\//g, ' ').trim()

  ok(`${tag}: no page errors`, errs.length === 0)

  const toggle = p.locator('.menu-toggle')
  const panel = p.locator('#primary-menu')
  ok(`${tag}: toggle visible on a phone`, await toggle.isVisible())
  ok(`${tag}: aria-expanded starts false`, await toggle.getAttribute('aria-expanded') === 'false')

  await toggle.click()
  await p.waitForTimeout(500)
  ok(`${tag}: panel opens`, await panel.isVisible())
  ok(`${tag}: aria-expanded flips true`, await toggle.getAttribute('aria-expanded') === 'true')
  ok(`${tag}: panel is on screen`, await p.evaluate(() => {
    const el = document.querySelector('#primary-menu')
    const r = el.getBoundingClientRect()
    return r.right <= innerWidth + 1 && r.width > 150 && r.height > 100
  }))
  ok(`${tag}: panel paints above the header`, await p.evaluate(() => {
    const el = document.elementFromPoint(innerWidth - 20, 200)
    return !!(el && el.closest('#primary-menu'))
  }))
  ok(`${tag}: focus moved into the panel`, await p.evaluate(
    () => document.querySelector('#primary-menu').contains(document.activeElement)))
  ok(`${tag}: scroll locked`, await p.evaluate(
    () => getComputedStyle(document.documentElement).overflow === 'hidden' ||
          getComputedStyle(document.body).overflow === 'hidden'))

  await p.keyboard.press('Tab')
  ok(`${tag}: focus ring visible in the panel`, await p.evaluate(() => {
    const cs = getComputedStyle(document.activeElement)
    return document.querySelector('#primary-menu').contains(document.activeElement) &&
           cs.outlineStyle !== 'none' && parseFloat(cs.outlineWidth) > 0
  }))

  await p.keyboard.press('Shift+Tab')
  await p.keyboard.press('Shift+Tab')
  ok(`${tag}: focus trap holds`, await p.evaluate(
    () => document.querySelector('#primary-menu').contains(document.activeElement)))

  await p.keyboard.press('Escape')
  await p.waitForTimeout(500)
  ok(`${tag}: Escape closes`, await toggle.getAttribute('aria-expanded') === 'false')
  ok(`${tag}: focus returns to the toggle`, await p.evaluate(
    () => document.activeElement === document.querySelector('.menu-toggle')))
  ok(`${tag}: scroll lock released`, await p.evaluate(
    () => getComputedStyle(document.documentElement).overflow !== 'hidden' &&
          getComputedStyle(document.body).overflow !== 'hidden'))

  // Scrim dismissal.
  await toggle.click(); await p.waitForTimeout(500)
  const scrim = p.locator('.ak-nav-scrim')
  if (await scrim.count()) {
    await scrim.click({ position: { x: 10, y: 400 }, force: true })
    await p.waitForTimeout(500)
    ok(`${tag}: scrim click closes`, await toggle.getAttribute('aria-expanded') === 'false')
  } else {
    ok(`${tag}: scrim exists while open`, false)
  }

  // Resize past the breakpoint must not strand the lock.
  await toggle.click(); await p.waitForTimeout(500)
  await p.setViewportSize({ width: 1280, height: 900 })
  await p.waitForTimeout(600)
  ok(`${tag}: resize to desktop releases the lock`, await p.evaluate(
    () => getComputedStyle(document.documentElement).overflow !== 'hidden' &&
          getComputedStyle(document.body).overflow !== 'hidden'))

  await ctx.close()
}

// Reduced motion: the panel must still close, not stay in the DOM over the page.
{
  const ctx = await b.newContext({ viewport: { width: 390, height: 844 }, reducedMotion: 'reduce' })
  const p = await ctx.newPage()
  await p.goto(B + '/work/', { waitUntil: 'networkidle' })
  await p.locator('.menu-toggle').click(); await p.waitForTimeout(300)
  await p.keyboard.press('Escape'); await p.waitForTimeout(300)
  ok('reduced motion: panel closes', await p.locator('.menu-toggle').getAttribute('aria-expanded') === 'false')
  await ctx.close()
}

await b.close()
console.log(`\n${pass} passed, ${fail} failed`)
process.exit(fail ? 1 : 0)
