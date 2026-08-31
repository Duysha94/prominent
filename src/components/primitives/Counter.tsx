import { useLayoutEffect, useRef, useState } from 'react'
import { useInView } from 'motion/react'
import { useReducedMotion } from '../../lib/useReducedMotion'
import { cn } from '../../lib/cn'

/**
 * Count-up for measured results. Tabular figures so the number does not shuffle
 * its own width while counting — the commonest reason counters look cheap.
 */
export function Counter({
  to,
  from = 0,
  decimals = 0,
  prefix = '',
  suffix = '',
  duration = 1400,
  className,
}: {
  to: number
  from?: number
  decimals?: number
  prefix?: string
  suffix?: string
  duration?: number
  className?: string
}) {
  const ref = useRef<HTMLSpanElement>(null)
  const inView = useInView(ref, { once: true, amount: 0.6 })
  const reduced = useReducedMotion()
  // Seeded with the TRUE value, not the start value. If the observer never
  // fires — element never scrolled into view, IntersectionObserver blocked,
  // the page captured as a static render — the figure on screen is still
  // correct. A counter that reads 0.0x because nobody scrolled past it is
  // worse than one that never animates.
  const [value, setValue] = useState(to)

  useLayoutEffect(() => {
    if (!inView) return
    if (reduced) {
      setValue(to)
      return
    }
    // Reset to the start value inside a layout effect so the jump back is
    // committed in the same frame as the first animated value — never painted.
    setValue(from)
    let raf = 0
    let start: number | null = null
    const step = (ts: number) => {
      if (start === null) start = ts
      const p = Math.min(1, (ts - start) / duration)
      // expo-out, matching --ease-cut so counters feel like the rest of the site
      const eased = 1 - Math.pow(2, -10 * p)
      setValue(from + (to - from) * (p === 1 ? 1 : eased))
      if (p < 1) raf = requestAnimationFrame(step)
    }
    raf = requestAnimationFrame(step)
    return () => cancelAnimationFrame(raf)
  }, [inView, reduced, to, from, duration])

  return (
    <span ref={ref} className={cn('tabular-nums', className)}>
      {prefix}
      {value.toFixed(decimals)}
      {suffix}
    </span>
  )
}
