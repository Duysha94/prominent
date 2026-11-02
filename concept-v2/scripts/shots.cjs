// Screenshots of the concept at its key scroll moments, for visual QA.
// usage: node scripts/shots.cjs <url> <outdir> [width] [height] [only]
const { chromium } = require('playwright')
const fs = require('fs')

const [url = 'http://localhost:5179/', out = '.shots', W = '1440', H = '900', only = ''] = process.argv.slice(2)
fs.mkdirSync(out, { recursive: true })

;(async () => {
  const browser = await chromium.launch({ args: ['--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] })
  const page = await browser.newPage({ viewport: { width: +W, height: +H }, deviceScaleFactor: 1 })
  const logs = []
  page.on('console', (m) => { if (m.type() === 'error' || m.type() === 'warning') logs.push(`${m.type()}: ${m.text()}`) })
  page.on('pageerror', (e) => logs.push(`pageerror: ${e.message}`))
  await page.goto(url, { waitUntil: 'load' })
  await page.waitForTimeout(4200)

  const scrollTo = async (y) => {
    await page.evaluate((y) => {
      const ak = window.__ak
      if (ak && ak.lenis) ak.lenis.scrollTo(y, { immediate: true, force: true })
      else window.scrollTo(0, y)
    }, y)
    await page.waitForTimeout(1300)
  }
  const shot = async (name) => {
    if (only && !name.includes(only)) return
    await page.screenshot({ path: `${out}/${name}.jpg`, type: 'jpeg', quality: 82 })
    console.log('shot', name)
  }
  const vh = +H
  await shot('01-hero')
  await scrollTo(vh * 0.5); await shot('02-split-mid')
  await scrollTo(vh * 1.3); await shot('03-letters')
  await page.click('.pick__btn[data-pick="A"]').catch(() => {}); await page.waitForTimeout(1300); await shot('04-pick-a')
  await page.click('.pick__btn[data-pick="K"]').catch(() => {}); await page.waitForTimeout(1300); await shot('05-pick-k')
  await page.click('.pick__btn[data-pick="K"]').catch(() => {})

  const st = await page.evaluate(() => {
    const t = window.__ak && window.__ak.ScrollTrigger.getAll().find((s) => s.pin && s.trigger && s.trigger.classList.contains('stairs'))
    return t ? { start: t.start, end: t.end } : null
  })
  if (st) {
    const at = (p) => st.start + ((st.end - st.start) * p) / 6
    await scrollTo(st.start - vh * 0.4); await shot('06-stairs-arrive')
    for (const [p, n] of [[0.6, '07-strategy'], [1.35, '08-identity-build'], [1.8, '09-identity'], [2.3, '10-image-shot'], [2.7, '11-image-shot2'], [3.5, '12-experience'], [4.6, '13-digital'], [5.8, '14-visibility']]) {
      await scrollTo(at(p)); await shot(n)
    }
  } else {
    const steps = await page.$$eval('.step', (els) => els.map((e) => e.getBoundingClientRect().top + scrollY))
    for (let i = 0; i < steps.length; i++) { await scrollTo(steps[i] - vh * 0.1); await shot(`07-step-${i}`) }
  }
  const top = async (sel) => page.$eval(sel, (e) => e.getBoundingClientRect().top + scrollY)
  await scrollTo((await top('#own')) - vh * 0.1); await shot('15-own')
  const reel = await page.evaluate(() => {
    const t = window.__ak && window.__ak.ScrollTrigger.getAll().find((s) => s.pin && s.trigger && s.trigger.classList.contains('reel'))
    return t ? { start: t.start, end: t.end } : null
  })
  if (reel) { await scrollTo(reel.start + (reel.end - reel.start) * 0.35); await shot('16-reel-open'); await scrollTo(reel.end - 10); await shot('17-reel-full') }
  await scrollTo((await top('.platforms')) - vh * 0.3); await shot('18-platforms')
  await scrollTo(await top('#studio')); await shot('19-studio')
  await scrollTo((await top('.studio__row--people')) - 40); await shot('20-founders')
  await scrollTo((await top('#begin')) - vh * 0.2); await shot('21-begin')
  await scrollTo(await page.evaluate(() => document.documentElement.scrollHeight)); await shot('22-footer')
  fs.writeFileSync(`${out}/console.txt`, logs.join('\n'))
  console.log('console messages:', logs.length)
  console.log(logs.slice(0, 15).join('\n'))
  await browser.close()
})()
