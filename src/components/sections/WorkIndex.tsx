import { useState } from 'react'
import { Link } from 'react-router-dom'
import { motion, AnimatePresence } from 'motion/react'
import { WORK, laneLabel, type CaseStudy } from '../../data/work'
import { MeasureFrame } from '../primitives/MeasureFrame'
import { Plate } from '../primitives/Plate'
import { useHasHover } from '../../lib/usePointerKind'
import { useReducedMotion } from '../../lib/useReducedMotion'
import { EASE, D_MS } from '../../lib/motion'
import { cn } from '../../lib/cn'

/**
 * THE CONTENTS PAGE — how work is indexed here.
 *
 * Not a bento grid and not a horizontal-scroll gallery; both are exhausted,
 * and both bury the one thing a prospective client is scanning for, which is
 * *what happened*. This is a publication's contents page: folio, client,
 * result, lanes, year — readable in one pass, sortable by eye.
 *
 * The image is the reward for interest rather than the price of entry. A row
 * opens *in place* on hover or keyboard focus, showing the measured plate.
 * Crucially it opens in place rather than following the cursor, so it works
 * identically under keyboard, and on touch the newest row is simply left open.
 */
export function WorkIndex({
  items = WORK,
  heading,
}: {
  items?: CaseStudy[]
  heading?: React.ReactNode
}) {
  const hasHover = useHasHover()
  const reduced = useReducedMotion()
  const [active, setActive] = useState<string | null>(hasHover ? null : (items[0]?.slug ?? null))

  return (
    <section className="px-5 py-20 md:px-10 md:py-28">
      <div className="mx-auto max-w-[1440px]">
        {heading}

        <ul className="mt-12 border-t border-[var(--rule)]">
          {items.map((c) => {
            const open = active === c.slug
            return (
              <li key={c.slug} className="border-b border-[var(--rule)]">
                <Link
                  to={`/work/${c.slug}`}
                  className="group block py-6 md:py-7"
                  onMouseEnter={() => hasHover && setActive(c.slug)}
                  onMouseLeave={() => hasHover && setActive(null)}
                  onFocus={() => setActive(c.slug)}
                >
                  <div className="grid grid-cols-[auto_1fr] items-baseline gap-x-4 gap-y-2 md:grid-cols-[3.5rem_11rem_1fr_auto_4rem]">
                    <span className="font-mono text-[0.5625rem] tabular-nums text-[var(--text-faint)]">
                      {c.folio}
                    </span>
                    <span className="font-mono text-[0.6875rem] uppercase tracking-[0.14em] text-[var(--text)]">
                      {c.client}
                    </span>
                    <span
                      className={cn(
                        'col-span-2 font-display text-[clamp(1.25rem,2.6vw,2rem)] italic leading-tight transition-colors md:col-span-1',
                        open ? 'text-[var(--accent-text)]' : 'text-[var(--text)]',
                      )}
                    >
                      {c.headline}
                    </span>
                    <span className="col-span-2 flex gap-1.5 md:col-span-1">
                      {c.lanes.map((l) => (
                        <span
                          key={l}
                          className="border border-[var(--rule-strong)] px-1.5 py-0.5 font-mono text-[0.5rem] uppercase tracking-[0.12em] text-[var(--text-muted)]"
                        >
                          {laneLabel[l]}
                        </span>
                      ))}
                    </span>
                    <span className="hidden text-right font-mono text-[0.5625rem] tabular-nums text-[var(--text-faint)] md:block">
                      {c.year}
                    </span>
                  </div>

                  <AnimatePresence initial={false}>
                    {open && (
                      <motion.div
                        key="plate"
                        initial={reduced ? { opacity: 0 } : { height: 0, opacity: 0 }}
                        animate={reduced ? { opacity: 1 } : { height: 'auto', opacity: 1 }}
                        exit={reduced ? { opacity: 0 } : { height: 0, opacity: 0 }}
                        transition={{
                          duration: reduced ? 0.1 : D_MS.base / 1000,
                          ease: EASE.cut,
                        }}
                        className="overflow-hidden"
                      >
                        <div className="grid gap-6 pt-6 md:grid-cols-[minmax(0,26rem)_1fr] md:items-start">
                          <MeasureFrame measures={c.measures} label={`${c.category} — ${c.year}`} always>
                            <Plate
                              seed={c.slug}
                              tint={c.tint}
                              label={c.client}
                              className="aspect-[16/10] w-full overflow-hidden"
                            />
                          </MeasureFrame>
                          <p className="max-w-[52ch] text-[0.875rem] leading-relaxed text-[var(--text-muted)] md:pt-2">
                            {c.summary}
                          </p>
                        </div>
                      </motion.div>
                    )}
                  </AnimatePresence>
                </Link>
              </li>
            )
          })}
        </ul>
      </div>
    </section>
  )
}
