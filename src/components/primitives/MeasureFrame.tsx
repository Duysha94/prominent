import { useRef, useState, type ReactNode } from 'react'
import { motion, useInView, AnimatePresence } from 'motion/react'
import { EASE, D_MS } from '../../lib/motion'
import { useHasHover } from '../../lib/usePointerKind'
import { useReducedMotion } from '../../lib/useReducedMotion'
import { cn } from '../../lib/cn'

export type Measure = {
  /** Short mono label, e.g. "LOOKS" / "PRESS" / "RUNWAY" / "LCP". */
  key: string
  /** The value as displayed, e.g. "4.8×" / "0.9s" / "240". */
  value: string
  /** Where the callout sits. */
  edge: 'top' | 'right' | 'bottom' | 'left'
  /** 0–1 position along that edge. */
  at?: number
}

/**
 * MEASURE FRAME — the studio's core visual device.
 *
 * A fashion tech pack annotates a garment with dimension lines and callouts.
 * This does the same to a piece of the website, except the numbers are the
 * ones this studio works in: looks in a collection, press picked up, the runway
 * it was shown on, the load time of the store. That is the practice expressed
 * as an interaction — the same measuring eye applied to a silhouette, to a
 * show, and to a page.
 *
 * Reachability: hover on fine pointers, `focus-within` for keyboard, and on
 * touch devices the annotations simply appear when the block scrolls into
 * view. No number is ever hover-only.
 */
export function MeasureFrame({
  children,
  measures = [],
  label,
  className,
  always = false,
}: {
  children: ReactNode
  measures?: Measure[]
  /** Optional caption on the top dimension line, e.g. "SS26 — CAMPAIGN". */
  label?: string
  className?: string
  /** Keep annotations permanently visible (used for hero/statement blocks). */
  always?: boolean
}) {
  const ref = useRef<HTMLDivElement>(null)
  const hasHover = useHasHover()
  const reduced = useReducedMotion()
  const inView = useInView(ref, { amount: 0.55, once: false })
  const [hovered, setHovered] = useState(false)
  const [focused, setFocused] = useState(false)

  const active = always || hovered || focused || (!hasHover && inView)
  const t = reduced
    ? { duration: 0 }
    : { duration: D_MS.base / 1000, ease: EASE.cut }

  return (
    <div
      ref={ref}
      className={cn('relative', className)}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
      onFocusCapture={() => setFocused(true)}
      onBlurCapture={() => setFocused(false)}
    >
      {children}

      {/* Annotations are decorative duplicates of text stated elsewhere in the
          card, so they are hidden from assistive tech rather than repeated. */}
      <div className="pointer-events-none absolute inset-0 z-20" aria-hidden="true">
        <AnimatePresence>
          {active && (
            <motion.div
              key="hud"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0, transition: { duration: D_MS.quick / 1000 } }}
              transition={t}
              className="absolute inset-0"
            >
              {/* Corner ticks — the drafting marks. */}
              {(['tl', 'tr', 'bl', 'br'] as const).map((c, i) => (
                <motion.span
                  key={c}
                  className={cn('ak-tick', `ak-tick--${c}`)}
                  initial={{ opacity: 0, scale: 0.4 }}
                  animate={{ opacity: 1, scale: 1 }}
                  transition={{ ...t, delay: reduced ? 0 : i * 0.03 }}
                />
              ))}

              {/* Top dimension line with its caption. */}
              <motion.span
                className="ak-dim ak-dim--top"
                initial={{ scaleX: 0 }}
                animate={{ scaleX: 1 }}
                transition={{ ...t, delay: reduced ? 0 : 0.06 }}
              />
              {label && (
                <motion.span
                  className="ak-dim-label"
                  initial={{ opacity: 0, y: reduced ? 0 : -6 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ ...t, delay: reduced ? 0 : 0.14 }}
                >
                  {label}
                </motion.span>
              )}

              {/* Callouts: leader line + value, snapping into place. */}
              {measures.map((m, i) => (
                <motion.span
                  key={m.key}
                  className={cn('ak-callout', `ak-callout--${m.edge}`)}
                  style={
                    m.edge === 'top' || m.edge === 'bottom'
                      ? { left: `${(m.at ?? 0.5) * 100}%` }
                      : { top: `${(m.at ?? 0.5) * 100}%` }
                  }
                  initial={{ opacity: 0, scale: reduced ? 1 : 0.82 }}
                  animate={{ opacity: 1, scale: 1 }}
                  transition={{
                    duration: reduced ? 0 : D_MS.base / 1000,
                    ease: EASE.snap,
                    delay: reduced ? 0 : 0.12 + i * 0.05,
                  }}
                >
                  <i className="ak-callout__leader" />
                  <b className="ak-callout__key">{m.key}</b>
                  <b className="ak-callout__value">{m.value}</b>
                </motion.span>
              ))}
            </motion.div>
          )}
        </AnimatePresence>
      </div>
    </div>
  )
}
