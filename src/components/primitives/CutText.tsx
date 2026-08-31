import { useEffect, useRef, type ElementType } from 'react'
import { loadGsap } from '../../lib/gsap'
import { GSAP_EASE, STAGGER, D } from '../../lib/motion'
import { useReducedMotion } from '../../lib/useReducedMotion'
import { cn } from '../../lib/cn'

type Props = {
  children: string
  as?: ElementType
  className?: string
  /** Fire on scroll into view (default) or immediately on mount. */
  trigger?: 'scroll' | 'mount'
  delay?: number
  /** Show the accent blade sweep. Off below ~24px, where it reads as noise. */
  blade?: boolean
}

/**
 * CUT TEXT — the house headline reveal.
 *
 * The masked line reveal (SplitText, `mask:'lines'`, y:100%, stagger) became
 * free with GSAP in April 2025 and is now on a very large share of new agency
 * sites. This is the alternative: each line is split along its horizontal
 * midline into two halves that start displaced in opposite directions and
 * close together — the way cut cloth falls back into place — while a thin
 * accent blade sweeps the cut line just ahead of the close.
 *
 * Accessibility: SplitText's `aria: 'auto'` puts the original string on the
 * wrapper and hides the fragments, so a screen reader gets one sentence
 * rather than a stream of them. `autoSplit: true` re-splits after the webfont
 * swaps in and on resize, which is what keeps line boxes honest once the
 * self-hosted Fraunces replaces the fallback.
 *
 * Under `prefers-reduced-motion` nothing is split at all — the text is simply
 * present, which is the correct reduced state for content.
 */
export function CutText({
  children,
  as: Tag = 'span',
  className,
  trigger = 'scroll',
  delay = 0,
  blade = true,
}: Props) {
  const ref = useRef<HTMLElement>(null)
  const reduced = useReducedMotion()

  useEffect(() => {
    const host = ref.current
    if (!host || reduced) return

    // The headline is already painted and readable by now — this only ever
    // adds a reveal on top of finished text. If GSAP never arrives, nothing
    // is lost but the animation.
    let cancelled = false
    let cleanup: (() => void) | undefined
    let safety = 0

    loadGsap().then(({ gsap, SplitText, ScrollTrigger }) => {
      if (cancelled) return

      const ctx = gsap.context(() => {
        const inner = host.querySelector<HTMLElement>('[data-cut-inner]')
        if (!inner) return

        /** Rebuilt from scratch on every (re)split — see autoSplit below. */
        const build = (lines: HTMLElement[]) => {
          const blades: HTMLElement[] = []
          const halves: HTMLElement[][] = []

          lines.forEach((el) => {
            const html = el.innerHTML
            el.innerHTML = ''
            // Positioning and the padded clip box live in base.css on
            // .ak-cut-line — see the note there about descenders.

            const top = document.createElement('span')
            top.className = 'ak-cut-half ak-cut-half--top'
            top.innerHTML = html

            const bottom = document.createElement('span')
            bottom.className = 'ak-cut-half ak-cut-half--bottom'
            bottom.innerHTML = html

            el.append(top, bottom)
            halves.push([top, bottom])

            if (blade) {
              const b = document.createElement('span')
              b.className = 'ak-cut-blade'
              b.setAttribute('aria-hidden', 'true')
              el.append(b)
              blades.push(b)
            }
          })

          const tl = gsap.timeline({
            delay,
            defaults: { ease: GSAP_EASE.cut },
            scrollTrigger:
              trigger === 'scroll' ? { trigger: host, start: 'top 86%', once: true } : undefined,
            // Dismantle the split once it has served its purpose — see the
            // note on .ak-cut-line[data-cut-settled] in base.css.
            onComplete: () => {
              lines.forEach((el) => el.setAttribute('data-cut-settled', ''))
              halves.forEach(([top, bottom]) => {
                top.style.clipPath = 'none'
                bottom.style.display = 'none'
              })
              blades.forEach((b) => b.remove())
            },
          })

          halves.forEach(([top, bottom], i) => {
            const at = i * STAGGER.line
            tl.fromTo(
              top,
              { xPercent: 4, yPercent: -46, opacity: 0 },
              { xPercent: 0, yPercent: 0, opacity: 1, duration: D.reveal },
              at,
            ).fromTo(
              bottom,
              { xPercent: -4, yPercent: 46, opacity: 0 },
              { xPercent: 0, yPercent: 0, opacity: 1, duration: D.reveal },
              at,
            )
          })

          // The blade runs the cut a beat ahead of the halves closing.
          blades.forEach((b, i) => {
            const at = i * STAGGER.line
            tl.fromTo(
              b,
              { scaleX: 0, transformOrigin: 'left center', opacity: 1 },
              { scaleX: 1, duration: D.base, ease: GSAP_EASE.thread },
              at,
            ).to(b, { scaleX: 0, transformOrigin: 'right center', duration: D.quick }, at + D.base)
          })

          // SAFETY NET. This reveal starts its text at opacity 0, so a
          // ScrollTrigger that never fires does not degrade the animation —
          // it hides the content outright. That can happen for reasons
          // outside our control: a tool that repositions the page without
          // dispatching scroll events, a screenshot service, a print
          // stylesheet, an embedding context that suppresses scroll.
          //
          // So if the timeline has not begun shortly after it is built, play
          // it regardless. Anyone scrolling normally never reaches this;
          // anyone who would otherwise have seen a blank heading does.
          if (trigger === 'scroll') {
            safety = window.setTimeout(() => {
              if (tl.progress() === 0 && !tl.isActive()) tl.play()
            }, 2500)
          }

          // Returning the timeline lets SplitText revert it cleanly on
          // re-split instead of leaving orphaned tweens behind.
          return tl
        }

        SplitText.create(inner, {
          type: 'lines',
          linesClass: 'ak-cut-line',
          // Re-splits after the webfont swaps in and on resize, so line boxes
          // never go stale. Animations must be created inside onSplit and
          // returned, so GSAP can revert them before re-splitting.
          autoSplit: true,
          // Puts the original string back on the wrapper and hides the
          // fragments: a screen reader gets one sentence, not confetti.
          aria: 'auto',
          onSplit: (self) => build(self.lines as HTMLElement[]),
        })

        ScrollTrigger.refresh()
      }, host)

      cleanup = () => ctx.revert()
    })

    return () => {
      cancelled = true
      window.clearTimeout(safety)
      cleanup?.()
    }
  }, [children, reduced, trigger, delay, blade])

  return (
    <Tag ref={ref} className={cn('ak-cut', className)}>
      <span data-cut-inner className="ak-cut-inner">
        {children}
      </span>
    </Tag>
  )
}
