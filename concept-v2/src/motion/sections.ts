import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import type Lenis from 'lenis'
import { state } from '../state'
import type { Stage } from '../gl/stage'

const ramp = (p: number, a: number, b: number) => Math.min(1, Math.max(0, (p - a) / (b - a)))

/** A pose for each photograph in the Image movement (radians). */
const SHOTS = [
  { poseX: 0, poseY: 0, poseZ: 0 },
  { poseX: -0.12, poseY: 0.62, poseZ: 0.05 },
  { poseX: 0.2, poseY: -0.42, poseZ: -0.07 },
  { poseX: -0.06, poseY: 1.05, poseZ: 0.0 },
  { poseX: 0.12, poseY: -0.8, poseZ: 0.09 },
  { poseX: 0.02, poseY: 0.28, poseZ: -0.03 },
]
const SHOT_AT = [0.1, 0.28, 0.46, 0.64, 0.82]

interface Opts { stage: Stage | null; reduced: boolean; lenis: Lenis | null }

export function initMotion({ stage, reduced }: Opts) {
  const root = document.documentElement

  // ── Arrival: the letters are raised, then the words around them ────────
  if (reduced || !stage) state.intro = 1
  else gsap.to(state, { intro: 1, duration: 2.4, ease: 'expo.out', delay: 0.1 })

  if (reduced) {
    // Still page: the letters stand in the hero and leave when it does.
    if (stage) {
      ScrollTrigger.create({
        trigger: '#letters', start: 'top 70%', end: 'top 10%',
        onUpdate: (s) => { state.exit = s.progress },
      })
    }
    return
  }

  gsap.to('.hero [data-rise]', { opacity: 1, y: 0, duration: 1.1, ease: 'expo.out', stagger: 0.09, delay: stage ? 1.0 : 0.2 })
  ScrollTrigger.batch('[data-rise]:not(.hero [data-rise])', {
    start: 'top 88%',
    once: true,
    onEnter: (els) => gsap.to(els, { opacity: 1, y: 0, duration: 0.95, ease: 'expo.out', stagger: 0.075, overwrite: true }),
  })

  const mm = gsap.matchMedia()

  // ── Parallax: the words around the monument travel at their own pace ──
  mm.add('(min-width: 700px)', () => gsap.utils.toArray<HTMLElement>('[data-speed]').forEach((el) => {
    const k = 1 - parseFloat(el.dataset.speed || '1')
    gsap.fromTo(el, { y: () => -k * innerHeight * 0.3 }, {
      y: () => k * innerHeight * 0.3, ease: 'none',
      scrollTrigger: { trigger: el, start: 'top bottom', end: 'bottom top', scrub: true, invalidateOnRefresh: true },
    })
  }))
  gsap.utils.toArray<HTMLElement>('.frame video, .shot__frame video').forEach((v) => {
    gsap.fromTo(v, { yPercent: -10, scale: 1.25 }, {
      yPercent: 10, scale: 1.25, ease: 'none',
      scrollTrigger: { trigger: v, start: 'top bottom', end: 'bottom top', scrub: true },
    })
  })

  // ── Between the letters: split to opposite edges, hold while chosen ────
  if (stage) {
    gsap.to(state, {
      split: 1, ease: 'none',
      scrollTrigger: { trigger: '#letters', start: 'top bottom', end: 'top top', scrub: true },
    })
    ScrollTrigger.create({
      trigger: '#letters', start: 'top top', end: '+=100%', pin: true,
      onToggle: (s) => root.classList.toggle('letters-live', s.isActive),
    })
    gsap.to(state, {
      stairs: 1, ease: 'none',
      scrollTrigger: { trigger: '#practice', start: 'top bottom', end: 'top top', scrub: true },
    })
  }

  // ── Practice ───────────────────────────────────────────────────────────
  const steps = gsap.utils.toArray<HTMLElement>('.step')
  const lines = steps.map((s) => s.querySelector<HTMLElement>('.step__line')!)
  let shot = 0
  let lastFlash = 0
  const takeShot = (n: number) => {
    if (n === shot) return
    shot = n
    gsap.to(state, { ...SHOTS[n], duration: 0.5, ease: 'expo.out', overwrite: 'auto' })
    // At most one flash every 350 ms, however fast the page is scrolled (WCAG 2.3.1).
    const now = performance.now()
    if (n > 0 && now - lastFlash > 350) {
      lastFlash = now
      state.flash = 1
    }
  }
  const setActive = (i: number) => steps.forEach((s, j) => s.classList.toggle('is-active', j === i))

  mm.add('(min-width: 900px) and (min-height: 600px)', () => {
    if (!stage) return
    // One frame for six movements: lines appear in order, the letters perform each.
    // The whole staircase is set at once; the active line takes the ink.
    gsap.from(lines, {
      opacity: 0, y: 40, duration: 1, ease: 'expo.out', stagger: 0.08,
      scrollTrigger: { trigger: '#practice', start: 'top 92%', once: true },
    })
    const N = steps.length
    const apply = (p: number) => {
      const i = Math.min(N - 1, Math.max(0, Math.floor(p)))
      setActive(i)
      state.wire = ramp(p, 0, 0.3) * (1 - ramp(p, 1.55, 1.95))
      state.build = Math.min(1, Math.max(0, 1 - ramp(p, 0.02, 0.32) + ramp(p, 1.05, 1.6)))
      state.dark = ramp(p, 3.0, 3.18) * (1 - ramp(p, 3.84, 4.0))
      root.classList.toggle('is-dark', p >= 3.02 && p < 3.9)
      state.vox = ramp(p, 4.05, 4.4) * (1 - ramp(p, 4.86, 5.04))
      state.echo = ramp(p, 5.04, 5.5)
      let n = 0
      if (i === 2) {
        const local = p - 2
        n = SHOT_AT.filter((a) => local >= a).length
      }
      takeShot(n)
    }
    const st = ScrollTrigger.create({
      trigger: '.stairs', start: 'top top', end: `+=${N * 65}%`, pin: true, scrub: true,
      onUpdate: (s) => apply(s.progress * N),
      onLeaveBack: () => { apply(0); setActive(0) },
    })
    apply(0)
    return () => {
      st.kill()
      gsap.set(lines, { clearProps: 'opacity,transform' })
      root.classList.remove('is-dark')
    }
  })

  mm.add('(max-width: 899px), (max-height: 599px)', () => {
    // Each movement is its own screen; the letters play it as it arrives.
    const presets = [
      { wire: 1, build: 0, dark: 0, vox: 0, echo: 0 },
      { wire: 0, build: 1, dark: 0, vox: 0, echo: 0 },
      { wire: 0, build: 1, dark: 0, vox: 0, echo: 0 },
      { wire: 0, build: 1, dark: 1, vox: 0, echo: 0 },
      { wire: 0, build: 1, dark: 0, vox: 1, echo: 0 },
      { wire: 0, build: 1, dark: 0, vox: 0, echo: 1 },
    ]
    let shooting: gsap.core.Timeline | null = null
    const activate = (i: number) => {
      setActive(i)
      root.classList.toggle('is-dark', i === 3)
      shooting?.kill()
      if (i === 1) {
        gsap.fromTo(state, { build: 0, wire: 1 }, { build: 1, duration: 1.4, ease: 'power2.inOut', overwrite: 'auto' })
        gsap.to(state, { wire: 0, duration: 0.6, delay: 1.3 })
        gsap.to(state, { dark: 0, vox: 0, echo: 0, duration: 0.6 })
      } else {
        gsap.to(state, { ...presets[i], duration: 0.9, ease: 'power2.inOut', overwrite: 'auto' })
      }
      if (i === 2) {
        shooting = gsap.timeline()
        for (let k = 1; k < SHOTS.length; k++) shooting.call(() => takeShot(k), [], k * 0.75)
      } else {
        takeShot(0)
      }
    }
    const triggers = steps.map((s, i) =>
      ScrollTrigger.create({
        trigger: s, start: 'top 55%', end: 'bottom 55%',
        onToggle: (self) => { if (self.isActive) activate(i) },
      }),
    )
    return () => {
      triggers.forEach((t) => t.kill())
      shooting?.kill()
      root.classList.remove('is-dark')
    }
  })

  // Created after the practice pin so its positions include that pin's spacing.
  if (stage) {
    gsap.to(state, {
      exit: 1, ease: 'none',
      scrollTrigger: { trigger: '#own', start: 'top 92%', end: 'top 30%', scrub: true },
    })
  }

  // ── Our own: the reel opens to the whole screen ─────────────────────────
  const reel = document.querySelector<HTMLElement>('.reel')
  if (reel) {
    gsap.timeline({
      scrollTrigger: { trigger: reel, start: 'top top', end: '+=130%', pin: true, scrub: true },
    })
      .fromTo('.reel__frame', { clipPath: 'inset(20% 24% 20% 24%)' }, { clipPath: 'inset(0% 0% 0% 0%)', ease: 'none', duration: 1 })
      .fromTo('.reel__video', { scale: 1.3 }, { scale: 1, ease: 'none', duration: 1 }, 0)
      .fromTo('.reel__caption', { opacity: 0, y: 24 }, { opacity: 1, y: 0, duration: 0.25 }, 0.8)
  }

  // ── Begin: the word arrives from the right edge ─────────────────────────
  gsap.fromTo('.begin__word', { xPercent: 12 }, {
    xPercent: 0, ease: 'none',
    scrollTrigger: { trigger: '.begin', start: 'top bottom', end: 'top 30%', scrub: true },
  })

  // ── Footer: the name rises back into place ──────────────────────────────
  gsap.fromTo('.foot__l', { yPercent: 55 }, {
    yPercent: 0, ease: 'none', stagger: 0.12,
    scrollTrigger: { trigger: '.foot', start: 'top bottom', end: 'bottom bottom', scrub: true },
  })

  // Fonts and posters change heights; measure again once they settle.
  document.fonts?.ready.then(() => ScrollTrigger.refresh())
  addEventListener('load', () => ScrollTrigger.refresh())
}
