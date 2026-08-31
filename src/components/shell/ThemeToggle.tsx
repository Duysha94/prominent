import { motion } from 'motion/react'
import { useTheme } from '../../lib/useTheme'
import { EASE, D_MS } from '../../lib/motion'
import { useReducedMotion } from '../../lib/useReducedMotion'

/**
 * The mode switch, named rather than symbolised.
 *
 * A sun/moon icon says "there is a dark mode". Naming the two rooms —
 * ATELIER (the daylit workroom) and RUNWAY (the show) — says the studio
 * designed both, which is the actual claim. The switch is a physical throw:
 * the knob is the same accent thread that runs the page.
 */
export function ThemeToggle({ className }: { className?: string }) {
  const { theme, toggle } = useTheme()
  const reduced = useReducedMotion()
  const dark = theme === 'dark'

  return (
    <button
      type="button"
      onClick={toggle}
      className={`group flex items-center gap-2.5 ${className ?? ''}`}
      aria-pressed={dark}
      aria-label={`Switch to ${dark ? 'Atelier (light)' : 'Runway (dark)'} mode`}
    >
      <span className="hidden font-mono text-[0.5625rem] uppercase tracking-[0.18em] text-[var(--text-muted)] transition-colors group-hover:text-[var(--text)] sm:inline">
        {dark ? 'Runway' : 'Atelier'}
      </span>
      <span
        className="relative flex h-[18px] w-[34px] items-center border border-[var(--rule-strong)] px-[3px]"
        aria-hidden="true"
      >
        <motion.span
          className="block h-[10px] w-[10px] bg-[var(--accent-fill)]"
          animate={{ x: dark ? 14 : 0 }}
          transition={
            reduced
              ? { duration: 0 }
              : { duration: D_MS.base / 1000, ease: EASE.snap }
          }
        />
      </span>
    </button>
  )
}
