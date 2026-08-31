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
 * It is switched off entirely under `prefers-reduced-motion` — by guarding the
 * constructor, NOT by trusting Lenis's own `respectReducedMotion` option,
 * which only makes programmatic `scrollTo()` instant and leaves wheel
 * hijacking running at full lerp. It must also stay off any section using CSS
 * `scroll-snap`, which it is documented to fight.
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
      // NOT allowNestedScroll. That option walks the composed path from the
      // wheel target to the scroller calling getComputedStyle and reading
      // scrollHeight on each ancestor — a forced synchronous layout, inside a
      // non-passive listener, refreshed every two seconds. Marking the two or
      // three genuinely scrollable panels with `data-lenis-prevent` is a plain
      // attribute check with no layout read.
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

    // Lenis ships no keyboard or focus handling at all. When a keyboard user
    // Tabs to something below the fold the browser scrolls it into view
    // natively, Lenis immediately fights that, and the page snaps back — so
    // the focus bridge has to be built by hand.
    const onFocusIn = (e: FocusEvent) => {
      const el = e.target as HTMLElement | null
      if (!el || el.closest('[data-lenis-prevent]')) return
      instance.scrollTo(el, { immediate: true, offset: -HEADER_OFFSET })
    }
    document.addEventListener('focusin', onFocusIn)

    return () => {
      cancelled = true
      document.removeEventListener('focusin', onFocusIn)
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
