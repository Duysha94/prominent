/**
 * GSAP, loaded on demand.
 *
 * The site runs a two-tier motion architecture: tier 1 is CSS scroll-driven
 * animation (compositor thread, zero JS), tier 2 is GSAP for the handful of
 * things the CSS spec deliberately excludes — split-text choreography, pinning,
 * programmatic scrub.
 *
 * Tier 2 therefore has no business being in the entry chunk. Everything that
 * uses GSAP renders its finished, readable state first and enhances once this
 * resolves, which keeps ~50KB gzip off the critical path and means a failed or
 * blocked animation bundle costs the visitor nothing but the animation.
 *
 * All plugins here are free as of GSAP 3.13 — Webflow released the former Club
 * plugins (SplitText, ScrollSmoother, MorphSVG, DrawSVG, Inertia) for everyone
 * in April 2025. Verified against the installed gsap 3.15.0.
 */
import type { gsap as GsapType } from 'gsap'
import type { ScrollTrigger as ScrollTriggerType } from 'gsap/ScrollTrigger'
import type { SplitText as SplitTextType } from 'gsap/SplitText'
import { EASE } from './motion'

export type GsapBundle = {
  gsap: typeof GsapType
  ScrollTrigger: typeof ScrollTriggerType
  SplitText: typeof SplitTextType
}

let pending: Promise<GsapBundle> | null = null

export function loadGsap(): Promise<GsapBundle> {
  if (pending) return pending

  pending = (async () => {
    const [core, st, split, ease] = await Promise.all([
      import('gsap'),
      import('gsap/ScrollTrigger'),
      import('gsap/SplitText'),
      import('gsap/CustomEase'),
    ])
    const { gsap } = core
    const { ScrollTrigger } = st
    const { SplitText } = split
    const { CustomEase } = ease

    gsap.registerPlugin(ScrollTrigger, SplitText, CustomEase)

    // The same four curves the CSS tokens use, so a GSAP tween and a CSS
    // transition on the same element are physically identical.
    const bez = (e: readonly number[]) => `M0,0 C${e[0]},${e[1]} ${e[2]},${e[3]} 1,1`
    CustomEase.create('ak.cut', bez(EASE.cut))
    CustomEase.create('ak.thread', bez(EASE.thread))
    CustomEase.create('ak.snap', bez(EASE.snap))
    CustomEase.create('ak.drape', bez(EASE.drape))

    // Reveals fire slightly before the element is fully on screen, so the page
    // never feels like it is waiting for you.
    ScrollTrigger.defaults({ start: 'top 88%', toggleActions: 'play none none none' })

    // Mobile browsers show and hide the URL bar constantly. Refreshing every
    // trigger on each of those resizes is a forced-layout pass mid-scroll and
    // a major source of mobile CLS. ScrollTrigger infers this from touch
    // support; say it outright instead.
    ScrollTrigger.config({ ignoreMobileResize: true })
    gsap.defaults({ ease: 'ak.cut', duration: 0.82 })

    return { gsap, ScrollTrigger, SplitText }
  })()

  return pending
}
