// A scroll-through film of the site: frame-by-frame screenshots → ffmpeg.
// usage: node scripts/tour.cjs <url> <out.mp4> [width] [height]
const { chromium } = require('playwright')
const { execFileSync } = require('child_process')
const fs = require('fs')
const os = require('os')
const path = require('path')

const [url = 'http://localhost:4173/', out = 'tour.mp4', W = '1280', H = '800'] = process.argv.slice(2)
const dir = fs.mkdtempSync(path.join(process.env.TOUR_TMP || os.tmpdir(), 'tour-'))
let n = 0

;(async () => {
  const browser = await chromium.launch({ args: ['--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] })
  const page = await browser.newPage({ viewport: { width: +W, height: +H }, deviceScaleFactor: 1 })
  await page.goto(url, { waitUntil: 'load' })
  const snap = async () => {
    await page.screenshot({ path: path.join(dir, `${String(n++).padStart(5, '0')}.jpg`), type: 'jpeg', quality: 80 })
  }
  const hold = async (frames, wait = 30) => { for (let i = 0; i < frames; i++) { await page.waitForTimeout(wait); await snap() } }
  const at = (y) => page.evaluate((y) => window.__ak.lenis.scrollTo(y, { immediate: true, force: true }), y)
  const ease = (t) => (t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2)
  const glide = async (from, to, frames) => {
    for (let i = 1; i <= frames; i++) { await at(from + (to - from) * ease(i / frames)); await page.waitForTimeout(25); await snap() }
  }
  const top = (sel) => page.$eval(sel, (e) => e.getBoundingClientRect().top + scrollY)
  const pinOf = (cls) => page.evaluate((cls) => {
    const t = window.__ak.ScrollTrigger.getAll().find((s) => s.pin && s.trigger && s.trigger.classList.contains(cls))
    return t ? [t.start, t.end] : null
  }, cls)

  const vh = +H
  // Arrival: the letters are raised.
  await page.mouse.move(+W * 0.3, +H * 0.4)
  await hold(40, 40)
  // The hand moves the light.
  for (let i = 0; i <= 30; i++) { await page.mouse.move(+W * (0.3 + 0.5 * i / 30), +H * (0.4 - 0.2 * Math.sin((i / 30) * Math.PI))); await page.waitForTimeout(20); await snap() }
  // Split.
  const [lStart] = (await pinOf('letters')) || [vh]
  await glide(0, lStart, 50)
  await hold(8)
  await page.click('.pick__btn[data-pick="A"]'); await hold(22, 40)
  await page.click('.pick__btn[data-pick="K"]'); await hold(22, 40)
  await page.click('.pick__btn[data-pick="K"]'); await hold(6)
  // Practice: the six movements.
  const stairs = await pinOf('stairs')
  const pStart = stairs ? stairs[0] : await top('#practice')
  const pEnd = stairs ? stairs[1] : pStart + vh * 5
  await glide(lStart, pStart, 40)
  await glide(pStart, pEnd, 260)
  // Our own and the reel.
  const reel = await pinOf('reel')
  const ownTop = await top('#own')
  await glide(pEnd, ownTop - vh * 0.05, 40)
  await hold(10)
  if (reel) { await glide(ownTop - vh * 0.05, reel[1], 70); await hold(12) }
  const studio = await top('#studio')
  await glide(reel ? reel[1] : ownTop, studio, 30)
  await hold(10)
  const begin = await top('#begin')
  await glide(studio, begin - vh * 0.2, 40)
  const end = await page.evaluate(() => document.documentElement.scrollHeight - innerHeight)
  await glide(begin - vh * 0.2, end, 40)
  await hold(20)
  await browser.close()

  execFileSync('ffmpeg', ['-loglevel', 'error', '-y', '-framerate', '30', '-i', path.join(dir, '%05d.jpg'),
    '-c:v', 'libx264', '-preset', 'slow', '-crf', '24', '-pix_fmt', 'yuv420p', '-movflags', '+faststart', out])
  fs.rmSync(dir, { recursive: true, force: true })
  console.log('tour', out, n, 'frames', (fs.statSync(out).size / 1e6).toFixed(2), 'MB')
})()
