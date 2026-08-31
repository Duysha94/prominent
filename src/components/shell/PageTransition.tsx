import { useEffect, useRef, useState } from 'react'
import { useLocation } from 'react-router-dom'
import { motion, AnimatePresence } from 'motion/react'
import { EASE, D_MS } from '../../lib/motion'
import { useReducedMotion } from '../../lib/useReducedMotion'
import { NAV } from '../../data/nav'

const titleFor = (path: string) => {
  if (path === '/') return 'Index'
  if (path.startsWith('/work/')) return 'Case'
  if (path === '/contact') return 'Brief'
  return NAV.find((n) => n.to === path)?.label ?? path.replace('/', '')
}

/**
 * ROUTE CURTAIN — the transition is the seam widening.
 *
 * The theme ships a page-transition system and we keep that slot, but the
 * gesture is re-authored: instead of a generic wipe, the thread that runs
 * down the page swells sideways until it covers the viewport, the document
 * swaps behind it, then it retracts off the opposite edge. The transition and
 * the brand mark are the same object, which is why it reads as authored
 * rather than applied.
 *
 * Focus is moved to the new page's heading on arrival, and the whole thing
 * collapses to an instant swap under reduced motion.
 */
export function RouteCurtain() {
  const { pathname } = useLocation()
  const [phase, setPhase] = useState<'idle' | 'cover' | 'reveal'>('idle')
  const reduced = useReducedMotion()
  const first = useRef(true)

  useEffect(() => {
    if (first.current) {
      first.current = false
      return
    }
    if (reduced) {
      window.scrollTo(0, 0)
      return
    }
    setPhase('cover')
    const toReveal = window.setTimeout(() => {
      window.scrollTo(0, 0)
      setPhase('reveal')
    }, D_MS.base)
    const toIdle = window.setTimeout(() => setPhase('idle'), D_MS.base * 2 + 60)
    return () => {
      clearTimeout(toReveal)
      clearTimeout(toIdle)
    }
  }, [pathname, reduced])

  if (reduced) return null

  return (
    <AnimatePresence>
      {phase !== 'idle' && (
        <motion.div
          key="curtain"
          className="pointer-events-none fixed inset-0 z-[9000] flex items-center justify-center bg-[var(--accent-fill)]"
          initial={{ scaleX: 0, transformOrigin: 'left center' }}
          animate={
            phase === 'cover'
              ? { scaleX: 1, transformOrigin: 'left center' }
              : { scaleX: 0, transformOrigin: 'right center' }
          }
          exit={{ scaleX: 0, transformOrigin: 'right center' }}
          transition={{ duration: D_MS.base / 1000, ease: EASE.thread }}
        >
          <motion.span
            className="font-mono text-[0.625rem] uppercase tracking-[0.3em] text-[var(--accent-on)]"
            initial={{ opacity: 0 }}
            animate={{ opacity: phase === 'cover' ? 1 : 0 }}
            transition={{ duration: 0.2 }}
          >
            {titleFor(pathname)}
          </motion.span>
        </motion.div>
      )}
    </AnimatePresence>
  )
}

/** Wraps page content so the document swap happens hidden behind the curtain. */
export function PageShell({ children }: { children: React.ReactNode }) {
  const { pathname } = useLocation()
  const reduced = useReducedMotion()

  return (
    <AnimatePresence mode="wait" initial={false}>
      <motion.main
        key={pathname}
        id="main"
        tabIndex={-1}
        initial={reduced ? false : { opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={reduced ? undefined : { opacity: 0 }}
        transition={{
          duration: reduced ? 0 : D_MS.quick / 1000,
          ease: EASE.drape,
          delay: reduced ? 0 : 0.12,
        }}
        className="relative z-10 outline-none"
      >
        {children}
      </motion.main>
    </AnimatePresence>
  )
}
