import { useRef, type ReactNode } from 'react'
import { motion, useMotionValue, useSpring } from 'motion/react'
import { SPRING } from '../../lib/motion'
import { useHasHover } from '../../lib/usePointerKind'
import { useReducedMotion } from '../../lib/useReducedMotion'
import { cn } from '../../lib/cn'

/**
 * Magnetic pull, deliberately restrained.
 *
 * The 2023-25 version of this dragged the whole button 20-30px toward the
 * cursor, which is why it now reads as a template tell. Here the body barely
 * moves (6px ceiling) and the expressive part is a thin accent tick that
 * tracks the cursor's x across the control — the pointer is *measuring* the
 * button, which is the same language as the Measure Frame.
 */
export function Magnetic({
  children,
  className,
  strength = 6,
}: {
  children: ReactNode
  className?: string
  strength?: number
}) {
  const ref = useRef<HTMLSpanElement>(null)
  const hasHover = useHasHover()
  const reduced = useReducedMotion()

  const x = useSpring(useMotionValue(0), SPRING.light)
  const y = useSpring(useMotionValue(0), SPRING.light)
  const tick = useSpring(useMotionValue(0), SPRING.light)
  const tickOpacity = useSpring(useMotionValue(0), SPRING.light)

  const enabled = hasHover && !reduced

  return (
    <motion.span
      ref={ref}
      className={cn('relative inline-flex', className)}
      style={enabled ? { x, y } : undefined}
      onPointerMove={(e) => {
        if (!enabled || !ref.current) return
        const r = ref.current.getBoundingClientRect()
        const dx = e.clientX - (r.left + r.width / 2)
        const dy = e.clientY - (r.top + r.height / 2)
        x.set((dx / (r.width / 2)) * strength)
        y.set((dy / (r.height / 2)) * strength * 0.6)
        tick.set(e.clientX - r.left)
        tickOpacity.set(1)
      }}
      onPointerLeave={() => {
        x.set(0)
        y.set(0)
        tickOpacity.set(0)
      }}
    >
      {children}
      {enabled && (
        <motion.i
          aria-hidden="true"
          className="pointer-events-none absolute -bottom-1.5 h-2 w-px bg-[var(--accent-line)]"
          style={{ left: tick, opacity: tickOpacity }}
        />
      )}
    </motion.span>
  )
}
