// Renders the film studio's clips frame by frame and encodes them with ffmpeg.
// usage: node scripts/render-clips.cjs <dev-url> [clip ...]
const { chromium } = require('playwright')
const { execFileSync } = require('child_process')
const fs = require('fs')
const path = require('path')

const base = process.argv[2] || 'http://localhost:5179/'
const SPECS = {
  'runway-a': { w: 1600, h: 900, poster: 0.62 },
  'runway-b': { w: 1280, h: 720, poster: 0.5 },
  silk: { w: 1280, h: 720, poster: 0.3 },
  pages: { w: 1280, h: 720, poster: 0.4 },
}
const only = process.argv.slice(3)
const outDir = path.resolve(__dirname, '../public/media')
const work = process.env.FILM_TMP || path.resolve(__dirname, '../.film')
fs.mkdirSync(outDir, { recursive: true })

;(async () => {
  const browser = await chromium.launch({ args: ['--enable-unsafe-swiftshader', '--ignore-gpu-blocklist'] })
  for (const [name, spec] of Object.entries(SPECS)) {
    if (only.length && !only.includes(name)) continue
    const dir = path.join(work, name)
    fs.rmSync(dir, { recursive: true, force: true })
    fs.mkdirSync(dir, { recursive: true })
    const page = await browser.newPage({ viewport: { width: spec.w, height: spec.h } })
    page.on('pageerror', (e) => console.error(name, 'pageerror', e.message))
    await page.goto(`${base}film.html?clip=${name}&w=${spec.w}&h=${spec.h}`, { waitUntil: 'load' })
    await page.waitForFunction(() => window.film && window.film.ready, null, { timeout: 60000 })
    const frames = await page.evaluate(() => window.film.frames)
    const t0 = Date.now()
    for (let i = 0; i < frames; i++) {
      const url = await page.evaluate((i) => window.film.frame(i), i)
      fs.writeFileSync(path.join(dir, `${String(i).padStart(4, '0')}.jpg`), Buffer.from(url.split(',')[1], 'base64'))
      if (i % 30 === 0) console.log(name, `frame ${i}/${frames}`, `${((Date.now() - t0) / 1000).toFixed(0)}s`)
    }
    await page.close()
    const mp4 = path.join(outDir, `${name}.mp4`)
    execFileSync('ffmpeg', ['-loglevel', 'error', '-y', '-framerate', '30', '-i', path.join(dir, '%04d.jpg'),
      '-c:v', 'libx264', '-preset', 'slow', '-crf', name === 'runway-a' ? '22' : '24', '-pix_fmt', 'yuv420p',
      '-movflags', '+faststart', '-an', mp4])
    const pf = String(Math.round(frames * spec.poster)).padStart(4, '0')
    execFileSync('ffmpeg', ['-loglevel', 'error', '-y', '-i', path.join(dir, `${pf}.jpg`), '-vf', 'scale=1280:-2', '-q:v', '4', path.join(outDir, `${name}.jpg`)])
    console.log(name, 'done', (fs.statSync(mp4).size / 1e6).toFixed(2), 'MB')
  }
  await browser.close()
})()
