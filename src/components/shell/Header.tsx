import { useEffect, useState } from 'react'
import { Link, NavLink, useLocation } from 'react-router-dom'
import { motion, useScroll, useMotionValueEvent } from 'motion/react'
import { ThemeToggle } from './ThemeToggle'
import { Magnetic } from '../primitives/Magnetic'
import { NAV } from '../../data/nav'
import { cn } from '../../lib/cn'
import { EASE, D_MS } from '../../lib/motion'

/**
 * THE RULE — the header is a ruler, not a pill.
 *
 * The floating rounded capsule (Linear/Framer lineage) is the single most
 * copied header of the last two years. This keeps the same structural slots
 * the WordPress theme provides — brand left, primary nav, mode switch, one
 * CTA, burger below the breakpoint — but draws them along a hairline with
 * measurement ticks, so the navigation reads as part of the tech-pack
 * language rather than as a floating app chrome.
 */
export function Header({ onOpenMenu }: { onOpenMenu: () => void }) {
  const [scrolled, setScrolled] = useState(false)
  const { scrollY } = useScroll()
  const { pathname } = useLocation()

  useMotionValueEvent(scrollY, 'change', (v) => setScrolled(v > 24))
  useEffect(() => setScrolled(window.scrollY > 24), [pathname])

  return (
    <motion.header
      className={cn(
        'fixed inset-x-0 top-0 z-50 transition-colors duration-500',
        scrolled && 'bg-[var(--bg)]/80 backdrop-blur-xl',
      )}
      initial={{ y: -24, opacity: 0 }}
      animate={{ y: 0, opacity: 1 }}
      transition={{ duration: D_MS.slow / 1000, ease: EASE.cut, delay: 0.1 }}
    >
      <div className="mx-auto flex max-w-[1440px] items-center gap-6 px-5 py-4 md:px-10">
        <Link to="/" className="group flex items-baseline gap-2" aria-label="AK Brand Development Studio — home">
          <span className="font-display text-xl italic leading-none text-[var(--text)]">AK</span>
          <span className="hidden font-mono text-[0.5625rem] uppercase leading-none tracking-[0.18em] text-[var(--text-muted)] transition-colors group-hover:text-[var(--accent-text)] lg:inline">
            Brand Development Studio
          </span>
        </Link>

        <nav className="ml-auto hidden items-center gap-7 md:flex" aria-label="Primary">
          {NAV.map((item) => (
            <NavLink key={item.to} to={item.to} className="group relative">
              {({ isActive }) => (
                <span className="flex items-baseline gap-1.5">
                  <span className="font-mono text-[0.5rem] tabular-nums text-[var(--text-faint)]">
                    {item.index}
                  </span>
                  <span
                    className={cn(
                      'text-[0.8125rem] tracking-tight transition-colors',
                      isActive
                        ? 'text-[var(--accent-text)]'
                        : 'text-[var(--text-muted)] group-hover:text-[var(--text)]',
                    )}
                  >
                    {item.label}
                  </span>
                  {/* The active mark is a stitch, not an underline. */}
                  <span
                    aria-hidden="true"
                    className={cn(
                      'absolute -bottom-2 left-0 right-0 h-px origin-left scale-x-0 bg-[var(--accent-line)] transition-transform duration-300 group-hover:scale-x-100',
                      isActive && 'scale-x-100',
                    )}
                    style={{ backgroundImage: 'repeating-linear-gradient(90deg,currentColor 0 4px,transparent 4px 7px)' }}
                  />
                </span>
              )}
            </NavLink>
          ))}
        </nav>

        <div className="ml-auto flex items-center gap-4 md:ml-0 md:gap-5">
          <ThemeToggle />
          <Magnetic className="hidden sm:inline-flex">
            {/* The only magnetic control in the header — see Magnetic.tsx. */}
            <Link
              to="/contact"
              className="border border-[var(--rule-strong)] px-4 py-2 font-mono text-[0.5625rem] uppercase tracking-[0.16em] text-[var(--text)] transition-colors hover:border-transparent hover:bg-[var(--accent-fill)] hover:text-[var(--accent-on)]"
            >
              Start a brief
            </Link>
          </Magnetic>
          <button
            type="button"
            onClick={onOpenMenu}
            className="flex h-8 w-8 flex-col items-center justify-center gap-[5px] md:hidden"
            aria-label="Open menu"
          >
            <span className="h-px w-5 bg-[var(--text)]" />
            <span className="h-px w-5 bg-[var(--text)]" />
          </button>
        </div>
      </div>

      {/* The rule itself, with ticks. This is the header's whole graphic idea. */}
      <div className="relative mx-auto max-w-[1440px] px-5 md:px-10">
        <div className="h-px w-full bg-[var(--rule)]" />
        <div className="absolute inset-x-5 top-0 flex justify-between md:inset-x-10" aria-hidden="true">
          {Array.from({ length: 41 }, (_, i) => (
            <span
              key={i}
              className="w-px bg-[var(--rule)]"
              style={{ height: i % 10 === 0 ? 5 : 2 }}
            />
          ))}
        </div>
      </div>
    </motion.header>
  )
}
