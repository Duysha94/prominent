import { useRef, type ReactNode } from 'react'
import { motion, useInView } from 'motion/react'
import { EASE, D_MS, TRAVEL, STAGGER } from '../../lib/motion'
import { useReducedMotion } from '../../lib/useReducedMotion'

/**
 * The workhorse in-view reveal. Short travel, long easing tail — arrival, not
 * a slideshow. Under reduced motion the element is simply already there:
 * opacity is preserved because a fade-in that never completes would hide
 * content, and content is never the thing we animate away.
 */
export function Reveal({
  children,
  delay = 0,
  y = TRAVEL.short,
  className,
  as: Tag = 'div',
}: {
  children: ReactNode
  delay?: number
  y?: number
  className?: string
  as?: 'div' | 'li' | 'section' | 'span'
}) {
  const ref = useRef<HTMLDivElement>(null)
  const inView = useInView(ref, { once: true, margin: '0px 0px -12% 0px' })
  const reduced = useReducedMotion()
  const M = motion[Tag] as typeof motion.div

  return (
    <M
      ref={ref}
      className={className}
      initial={reduced ? false : { opacity: 0, y }}
      animate={inView || reduced ? { opacity: 1, y: 0 } : undefined}
      transition={{
        duration: D_MS.slow / 1000,
        ease: EASE.cut,
        delay: reduced ? 0 : delay,
      }}
    >
      {children}
    </M>
  )
}

/** Reveal a list with the house card stagger. */
export function RevealList({
  children,
  className,
  as: Tag = 'div',
}: {
  children: ReactNode[]
  className?: string
  as?: 'div' | 'ul'
}) {
  return (
    <Tag className={className}>
      {children.map((child, i) => (
        <Reveal key={i} delay={i * STAGGER.card} as={Tag === 'ul' ? 'li' : 'div'}>
          {child}
        </Reveal>
      ))}
    </Tag>
  )
}
