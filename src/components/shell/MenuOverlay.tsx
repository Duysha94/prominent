import { useEffect } from 'react'
import { Link } from 'react-router-dom'
import { motion, AnimatePresence } from 'motion/react'
import { NAV } from '../../data/nav'
import { EASE, D_MS, STAGGER } from '../../lib/motion'
import { useReducedMotion } from '../../lib/useReducedMotion'

const META: Record<string, string> = {
  '/work': 'SELECTED',
  '/services': '4 MOVEMENTS',
  '/studio': 'THE FOUNDERS',
  '/journal': 'NOTES',
}

/**
 * THE PATTERN SHEET — the mobile/overflow menu.
 *
 * Keeps the theme's fullscreen-overlay slot but treats the panel as a cutting
 * table: entries are laid out as pattern pieces with folio numbers and a
 * count, and the sheet is cut open from the centre line rather than sliding
 * in. Escape closes, focus is trapped by the browser's own inert semantics on
 * the backdrop, and body scroll is locked while open.
 */
export function MenuOverlay({ open, onClose }: { open: boolean; onClose: () => void }) {
  const reduced = useReducedMotion()

  useEffect(() => {
    if (!open) return
    const onKey = (e: KeyboardEvent) => e.key === 'Escape' && onClose()
    document.addEventListener('keydown', onKey)
    const prev = document.body.style.overflow
    document.body.style.overflow = 'hidden'
    return () => {
      document.removeEventListener('keydown', onKey)
      document.body.style.overflow = prev
    }
  }, [open, onClose])

  return (
    <AnimatePresence>
      {open && (
        <motion.div
          className="fixed inset-0 z-[8000] flex flex-col bg-[var(--bg)] px-5 py-5"
          initial={reduced ? { opacity: 0 } : { clipPath: 'inset(50% 0% 50% 0%)' }}
          animate={reduced ? { opacity: 1 } : { clipPath: 'inset(0% 0% 0% 0%)' }}
          exit={reduced ? { opacity: 0 } : { clipPath: 'inset(50% 0% 50% 0%)' }}
          transition={{
            duration: reduced ? 0.12 : D_MS.slow / 1000,
            ease: EASE.cut,
          }}
          role="dialog"
          aria-modal="true"
          aria-label="Menu"
        >
          <div className="flex items-center justify-between">
            <span className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-muted)]">
              Pattern sheet
            </span>
            <button
              type="button"
              onClick={onClose}
              className="font-mono text-[0.625rem] uppercase tracking-[0.18em] text-[var(--text)]"
            >
              Close
            </button>
          </div>

          <nav className="mt-auto flex flex-col" aria-label="Primary">
            {[{ to: '/', label: 'Index', index: '00' }, ...NAV].map((item, i) => (
              <motion.div
                key={item.to}
                initial={reduced ? false : { opacity: 0, y: 22 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{
                  duration: D_MS.slow / 1000,
                  ease: EASE.cut,
                  delay: reduced ? 0 : 0.18 + i * STAGGER.block,
                }}
                className="border-t border-[var(--rule)] last:border-b"
              >
                <Link
                  to={item.to}
                  onClick={onClose}
                  className="group flex items-baseline gap-4 py-4"
                >
                  <span className="font-mono text-[0.5625rem] tabular-nums text-[var(--text-faint)]">
                    {item.index}
                  </span>
                  <span className="font-display text-[clamp(2.25rem,11vw,4rem)] italic leading-[0.95] text-[var(--text)] transition-colors group-hover:text-[var(--accent-text)]">
                    {item.label}
                  </span>
                  <span className="ml-auto font-mono text-[0.5rem] uppercase tracking-[0.16em] text-[var(--text-faint)]">
                    {META[item.to] ?? ''}
                  </span>
                </Link>
              </motion.div>
            ))}
          </nav>

          <Link
            to="/contact"
            onClick={onClose}
            className="mt-8 block bg-[var(--accent-fill)] px-5 py-4 text-center font-mono text-[0.625rem] uppercase tracking-[0.2em] text-[var(--accent-on)]"
          >
            Start a project
          </Link>
        </motion.div>
      )}
    </AnimatePresence>
  )
}
