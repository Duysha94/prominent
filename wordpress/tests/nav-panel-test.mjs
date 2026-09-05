/**
 * Prototype mobile navigation panel — behaviour test.
 *
 * The prototype shipped a Menu button with no script behind it. A screenshot
 * cannot show that: the button renders, it just does nothing. This file is the
 * check that would have caught it, and it asserts behaviour rather than paint —
 * open, focus entry, trap wrap, Escape, focus return, scroll lock, and the
 * resize case that would otherwise strand the lock over a desktop layout.
 *
 * Usage:  node nav-panel-test.mjs        (expects a server on :9500 at repo root)
 */
import { chromium } from 'playwright'
const B = 'http://127.0.0.1:9500/prototype/'
const PAGES = ['home.html','services.html','work.html','case-platform.html',
               'case-photo.html','case-film.html','case-event.html','internal.html']
const CHROME = '/opt/pw-browsers/chromium-1194/chrome-linux/chrome'
const b = await chromium.launch({ executablePath: CHROME })
let pass = 0, fail = 0
const ok  = (n, c) => { c ? pass++ : fail++; console.log(`${c ? 'PASS' : 'FAIL'}  ${n}`) }

for (const path of PAGES) {
  const ctx = await b.newContext({ viewport: { width: 390, height: 844 } })
  const p = await ctx.newPage()
  const errs = []
  p.on('pageerror', e => errs.push(e.message))
  await p.goto(B + path, { waitUntil: 'networkidle' })
  const tag = path.replace('.html', '')

  ok(`${tag}: no page errors`, errs.length === 0)

  const toggle = p.locator('[data-nav-toggle]')
  ok(`${tag}: toggle is visible on a phone`, await toggle.isVisible())
  ok(`${tag}: panel starts hidden`, await p.locator('#navp').isHidden())
  ok(`${tag}: aria-expanded starts false`, await toggle.getAttribute('aria-expanded') === 'false')

  await toggle.click()
  await p.waitForTimeout(450)
  ok(`${tag}: panel opens`, await p.locator('#navp').isVisible())
  ok(`${tag}: aria-expanded flips true`, await toggle.getAttribute('aria-expanded') === 'true')
  ok(`${tag}: panel is on screen`, await p.evaluate(() => {
    const r = document.querySelector('.navp__panel').getBoundingClientRect()
    return r.right <= innerWidth + 1 && r.left >= -1 && r.width > 200
  }))
  ok(`${tag}: focus moved into the panel`, await p.evaluate(
    () => document.querySelector('#navp').contains(document.activeElement)))
  ok(`${tag}: body scroll locked`, await p.evaluate(
    () => getComputedStyle(document.documentElement).overflow === 'hidden'))

  // The audit's focus scan cannot see inside a closed panel, so the ring on the
  // panel's own controls is asserted here, under real keyboard interaction.
  // The panel was opened by a click, and Chromium correctly withholds
  // :focus-visible while the last interaction was a pointer — one real Tab
  // switches the modality, which is exactly what a keyboard user does.
  await p.keyboard.press('Tab')
  ok(`${tag}: focus ring is visible inside the panel`, await p.evaluate(() => {
    const cs = getComputedStyle(document.activeElement)
    return document.querySelector('#navp').contains(document.activeElement) &&
           cs.outlineStyle !== 'none' && parseFloat(cs.outlineWidth) > 0
  }))

  // Tabbing back off the first item must wrap to the last, not escape the panel.
  await p.keyboard.press('Shift+Tab')
  await p.keyboard.press('Shift+Tab')
  ok(`${tag}: focus trap wraps backwards to the last control`, await p.evaluate(() => {
    const panel = document.querySelector('#navp')
    const items = [...panel.querySelectorAll('a[href],button')].filter(e => e.getClientRects().length)
    return panel.contains(document.activeElement) &&
           document.activeElement === items[items.length - 1]
  }))

  await p.keyboard.press('Escape')
  await p.waitForTimeout(450)
  ok(`${tag}: Escape closes`, await p.locator('#navp').isHidden())
  ok(`${tag}: focus returns to the toggle`, await p.evaluate(
    () => document.activeElement === document.querySelector('[data-nav-toggle]')))
  ok(`${tag}: scroll lock released`, await p.evaluate(
    () => getComputedStyle(document.documentElement).overflow !== 'hidden'))

  // Scrim click closes.
  await toggle.click(); await p.waitForTimeout(450)
  await p.locator('.navp__scrim').click({ position: { x: 10, y: 400 } })
  await p.waitForTimeout(450)
  ok(`${tag}: scrim click closes`, await p.locator('#navp').isHidden())

  // Resizing past the breakpoint with the panel open must not strand the lock.
  await toggle.click(); await p.waitForTimeout(450)
  await p.setViewportSize({ width: 1280, height: 900 })
  await p.waitForTimeout(500)
  ok(`${tag}: resize to desktop releases the lock`, await p.evaluate(
    () => getComputedStyle(document.documentElement).overflow !== 'hidden'))
  ok(`${tag}: toggle hidden on desktop`, !(await toggle.isVisible()))

  await ctx.close()
}

// Reduced motion must not strand the panel in the DOM after close.
{
  const ctx = await b.newContext({ viewport: { width: 390, height: 844 }, reducedMotion: 'reduce' })
  const p = await ctx.newPage()
  await p.goto(B + 'work.html', { waitUntil: 'networkidle' })
  await p.locator('[data-nav-toggle]').click(); await p.waitForTimeout(200)
  await p.keyboard.press('Escape'); await p.waitForTimeout(100)
  ok('reduced motion: panel hides immediately', await p.locator('#navp').isHidden())
  await ctx.close()
}

await b.close()
console.log(`\n${pass} passed, ${fail} failed`)
process.exit(fail ? 1 : 0)
