import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import Lenis from 'lenis'
import { initBar } from './ui/bar'
import { initPick } from './ui/pick'
import { initVideos } from './ui/videos'
import { initMotion } from './motion/sections'
import type { Stage } from './gl/stage'
import { state } from './state'

function hasWebGL2() {
  try {
    const c = document.createElement('canvas')
    return !!c.getContext('webgl2')
  } catch {
    return false
  }
}

export async function boot() {
  gsap.registerPlugin(ScrollTrigger)
  const root = document.documentElement
  const params = new URLSearchParams(location.search)
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches || params.has('still')
  const gl = !params.has('nogl') && hasWebGL2()

  if (gl) root.classList.add('gl')
  if (!reduced) root.classList.add('motion')

  initBar()
  initVideos(reduced)

  // The display face must be measured before the stage lays letters over it.
  try {
    await Promise.race([
      document.fonts.load('900 200px "Epilogue Variable"'),
      new Promise((r) => setTimeout(r, 2500)),
    ])
  } catch {
    /* fall back to whatever loaded */
  }

  let stage: Stage | null = null
  if (gl) {
    try {
      const { Stage } = await import('./gl/stage')
      stage = new Stage(document.querySelector<HTMLCanvasElement>('canvas.stage')!, { reduced })
    } catch (err) {
      console.error('Stage failed, continuing without it', err)
      root.classList.remove('gl')
      stage = null
    }
  }

  let lenis: Lenis | null = null
  if (!reduced) {
    lenis = new Lenis({ lerp: 0.11, smoothWheel: true })
    lenis.on('scroll', (l: Lenis) => {
      ScrollTrigger.update()
      state.velocity = l.velocity
    })
    gsap.ticker.add((time) => lenis!.raf(time * 1000))
    gsap.ticker.lagSmoothing(0)
    // Anchor links travel through the smooth scroll, not around it.
    document.addEventListener('click', (e) => {
      const a = (e.target as HTMLElement).closest<HTMLAnchorElement>('a[href^="#"]')
      if (!a) return
      const id = a.getAttribute('href')!
      if (id.length < 2) return
      const el = document.querySelector(id)
      if (!el) return
      e.preventDefault()
      lenis!.scrollTo(el as HTMLElement, { offset: 0, duration: 1.4 })
      history.replaceState(null, '', id)
    })
  }

  initPick(stage)
  initMotion({ stage, reduced, lenis })

  if (stage) {
    const s = stage
    gsap.ticker.add((_t, deltaMs) => s.render(deltaMs / 1000))
  }
  ;(window as unknown as { __ak: unknown }).__ak = { stage, lenis, ScrollTrigger }
}
