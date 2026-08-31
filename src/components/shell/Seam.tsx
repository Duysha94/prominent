import { motion, useScroll, useSpring, useTransform, useVelocity, useMotionTemplate } from 'motion/react'
import { SPRING } from '../../lib/motion'
import { useReducedMotion } from '../../lib/useReducedMotion'

/**
 * THE SEAM — the site's spine and its single strongest brand device.
 *
 * One orange thread runs the entire length of every page. It is not a
 * decoration: it is the scroll position made physical. Scroll fast and the
 * thread bows and slackens the way real thread does when you pull it through
 * cloth; stop, and an underdamped spring lets it wobble taut again. The knot
 * riding it is where you are in the document.
 *
 * The curve is a quadratic Bézier whose control point is driven by scroll
 * velocity. Because the y control sits at the exact midpoint, y(t) reduces to
 * a straight `1000t`, which is what lets the knot be placed analytically
 * instead of walking the path with getPointAtLength every frame.
 */
const VB_H = 1000 // viewBox height; x stays in real px via preserveAspectRatio="none"
const X = 30 // thread's resting x inside the 60-wide viewBox

export function Seam() {
  const reduced = useReducedMotion()
  const { scrollYProgress, scrollY } = useScroll()

  const velocity = useVelocity(scrollY)
  const slack = useSpring(velocity, SPRING.thread)
  // Bow *against* the direction of travel — the thread trails the motion.
  const bow = useTransform(slack, [-3500, 0, 3500], [34, 0, -34], { clamp: true })

  const progress = useSpring(scrollYProgress, { stiffness: 180, damping: 34, mass: 0.5 })

  const controlX = useTransform(bow, (b) => X + (reduced ? 0 : b))
  const d = useMotionTemplate`M ${X} 0 Q ${controlX} ${VB_H / 2} ${X} ${VB_H}`

  // Knot position on the curve: x(t) = X + 2(1-t)t·bow, y(t) = 1000t.
  const knotX = useTransform([bow, progress], ([b, p]) =>
    X + (reduced ? 0 : 2 * (1 - (p as number)) * (p as number) * (b as number)),
  )
  const knotY = useTransform(progress, (p) => p * VB_H)

  return (
    <div className="ak-seam" aria-hidden="true">
      <svg
        width="60"
        height="100%"
        viewBox={`0 0 60 ${VB_H}`}
        preserveAspectRatio="none"
        fill="none"
      >
        {/* The unstitched run — where you have not been yet. */}
        <motion.path
          d={d}
          stroke="var(--rule-strong)"
          strokeWidth={1}
          vectorEffect="non-scaling-stroke"
        />
        {/* The stitched run — accent, drawn to your scroll position. */}
        <motion.path
          d={d}
          stroke="var(--accent-line)"
          strokeWidth={1.5}
          strokeDasharray="5 4"
          vectorEffect="non-scaling-stroke"
          style={{ pathLength: progress }}
        />
        <motion.circle cx={knotX} cy={knotY} r={3.5} fill="var(--accent-fill)" />
        <motion.circle
          cx={knotX}
          cy={knotY}
          r={7}
          fill="none"
          stroke="var(--accent-line)"
          strokeWidth={1}
          opacity={0.4}
          vectorEffect="non-scaling-stroke"
        />
      </svg>
    </div>
  )
}
