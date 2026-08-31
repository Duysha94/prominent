import { useEffect, useRef, useState, type ReactNode } from 'react'
import { loadGsap } from '../../lib/gsap'
import type { GsapBundle } from '../../lib/gsap'
import { useReducedMotion } from '../../lib/useReducedMotion'
import { cn } from '../../lib/cn'

/**
 * Infinite marquee whose speed and direction are driven by how you scroll.
 *
 * A constant-speed logo strip is wallpaper. Tying velocity to scroll makes the
 * strip a readout of your own movement — and reversing it when you scroll up
 * gives the page a sense of being wound backwards.
 *
 * Under reduced motion it stops entirely and becomes a static, wrapping row,
 * because a permanently moving band is exactly what that preference is for.
 */
export function Marquee({
  children,
  speed = 60,
  reverse = false,
  className,
  repeat = 4,
  label = 'Scrolling banner',
}: {
  children: ReactNode
  /** Base px/second at rest. */
  speed?: number
  reverse?: boolean
  className?: string
  repeat?: number
  label?: string
}) {
  const track = useRef<HTMLDivElement>(null)
  const tweenRef = useRef<{ pause(): void; play(): void } | null>(null)
  const reduced = useReducedMotion()
  const [paused, setPaused] = useState(false)

  useEffect(() => {
    const el = track.current
    if (!el || reduced) return

    let cancelled = false
    let dispose: (() => void) | undefined

    loadGsap().then(({ gsap, ScrollTrigger }: GsapBundle) => {
      if (cancelled || !el) return

      const half = el.scrollWidth / 2
      const tween = gsap.to(el, {
        x: reverse ? 0 : -half,
        duration: half / speed,
        ease: 'none',
        repeat: -1,
        modifiers: { x: (v) => `${gsap.utils.wrap(-half, 0, parseFloat(v))}px` },
      })
      gsap.set(el, { x: reverse ? -half : 0 })

      const st = ScrollTrigger.create({
        onUpdate: (self) => {
          const v = self.getVelocity()
          // Scroll direction flips the strip; speed scales with it, capped so
          // a fling never turns the band into a blur.
          const boost = gsap.utils.clamp(-8, 8, v / 260)
          const dir = self.direction * (reverse ? -1 : 1)
          tween.timeScale(dir * Math.min(9, 1 + Math.abs(boost)))
        },
      })

      tweenRef.current = tween
      dispose = () => {
        tween.kill()
        st.kill()
        tweenRef.current = null
      }
    })

    return () => {
      cancelled = true
      dispose?.()
    }
  }, [reduced, reverse, speed])

  // WCAG 2.2.2: any motion that starts automatically and runs beyond five
  // seconds must be pausable. This is not optional politeness — under the
  // European Accessibility Act, in force since June 2025, it is the law for
  // anyone selling into the EU. Hovering or tab-focusing the band stops it,
  // and there is an explicit control for people who use neither.
  useEffect(() => {
    const tween = tweenRef.current
    if (!tween) return
    if (paused) tween.pause()
    else tween.play()
  }, [paused])

  return (
    <div
      className={cn('group/marquee relative w-full overflow-hidden', className)}
      role="marquee"
      aria-label={label}
      onMouseEnter={() => setPaused(true)}
      onMouseLeave={() => setPaused(false)}
      onFocusCapture={() => setPaused(true)}
      onBlurCapture={() => setPaused(false)}
    >
      {!reduced && (
        // WCAG 2.2.2 is Level A and it is not satisfied by hover. Revealing
        // this control on hover meant it did not exist on touch — where most
        // of the traffic is — so it is now always visible, just quiet, and it
        // gains contrast on hover and focus like any other control.
        <button
          type="button"
          onClick={() => setPaused((p) => !p)}
          aria-pressed={paused}
          className="absolute right-2 top-1/2 z-10 -translate-y-1/2 border border-[var(--rule)] bg-[var(--bg)] px-2 py-1 font-mono text-[0.5rem] uppercase tracking-[0.14em] text-[var(--text-faint)] transition-colors hover:border-[var(--accent-line)] hover:text-[var(--text)] focus-visible:text-[var(--text)]"
        >
          {paused ? 'Play' : 'Pause'}
        </button>
      )}
      <div ref={track} className="flex w-max">
        {Array.from({ length: reduced ? 1 : repeat * 2 }, (_, i) => (
          <div key={i} className="flex shrink-0 items-center" aria-hidden={i > 0}>
            {children}
          </div>
        ))}
      </div>
    </div>
  )
}
