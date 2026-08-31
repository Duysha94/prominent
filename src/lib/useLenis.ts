import { useEffect } from 'react'
import Lenis from 'lenis'
import { loadGsap } from './gsap'
import { prefersReducedMotion } from './useReducedMotion'

/** Height of the fixed header, so anchored jumps clear it. */
const HEADER_OFFSET = 88

let lenis: Lenis | null = null
export const getLenis = () => lenis

/**
 * Smooth scroll — scoped, not global dogma.
 *
 * Deliberately NOT scroll-hijacking. Lenis wraps `scrollTo` rather than faking
 * a transformed fixed container, so the scrollbar, arrow keys, Home/End, find-
 * in-page and assistive technology all keep working — that is the only reason
 * a smooth-scroll library is defensible at all. The transform-hijack pattern
 * is an accessibility failure and is not used here.
 *
 * It is switched off entirely under `prefers-reduced-motion`, and it must stay
 * off any section using CSS `scroll-snap`, which it is documented to fight.
 * If a future section needs snapping, opt it out with `data-lenis-prevent`
 * rather than tuning Lenis around it.
 *
 * Deliberately NOT scroll-hijacking: `lerp` is high enough that a flick still
 * lands roughly where the OS would put it, wheel multiplier stays at 1, and the
 * whole thing is switched off entirely under prefers-reduced-motion so keyboard
 * and assistive-tech scrolling behave natively.
 */
export function useLenis() {
  useEffect(() => {
    if (prefersReducedMotion()) return

    const instance = new Lenis({
      lerp: 0.09,
      wheelMultiplier: 1,
      // Touch devices keep native momentum — emulating it costs INP and never
      // feels as good as the platform's own.
      syncTouch: false,
      autoRaf: false,
      // Anchor jumps land below the fixed header instead of underneath it.
      anchors: { offset: -HEADER_OFFSET },
      // Any scrollable panel inside the page (the figures table, a code block)
      // keeps its own native scrolling instead of the page eating the gesture.
      allowNestedScroll: true,
    })
    lenis = instance

    let cancelled = false
    let dispose: (() => void) | undefined

    // Lenis is driven off GSAP's ticker so the two share one rAF loop; two
    // loops is the classic source of pinned-section jitter.
    loadGsap().then(({ gsap, ScrollTrigger }) => {
      if (cancelled) return
      instance.on('scroll', ScrollTrigger.update)
      const tick = (time: number) => instance.raf(time * 1000)
      gsap.ticker.add(tick)
      gsap.ticker.lagSmoothing(0)
      dispose = () => gsap.ticker.remove(tick)
    })

    return () => {
      cancelled = true
      dispose?.()
      instance.destroy()
      lenis = null
    }
  }, [])
}

/** Anchor + route-change scrolling that respects whichever engine is active. */
export function scrollTo(target: string | number | HTMLElement, offset = 0) {
  if (lenis) lenis.scrollTo(target, { offset, duration: 1.1 })
  else if (typeof target === 'number') window.scrollTo({ top: target, behavior: 'auto' })
  else {
    const el = typeof target === 'string' ? document.querySelector(target) : target
    el?.scrollIntoView({ block: 'start' })
  }
}
