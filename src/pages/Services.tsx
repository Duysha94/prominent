import { Link } from 'react-router-dom'
import { CutText } from '../components/primitives/CutText'
import { Reveal } from '../components/primitives/Reveal'
import { MovementDiagram } from '../components/sections/MovementDiagram'
import { MOVEMENTS } from '../data/services'
import { STUDIO } from '../data/studio'
import { usePageMeta } from '../lib/usePageMeta'

/**
 * SERVICES — four movements, one method.
 *
 * The brief lists nine services. As a list, nine services read as a menu, and
 * a menu invites a client to buy one item and go elsewhere for the rest —
 * which is the opposite of what this studio is for. Two structural decisions
 * fix it:
 *
 *  1. Each movement states its HANDOVER — what it leaves the next one. The
 *     page is a chain rather than a menu, so the argument for the whole
 *     practice is built into the layout instead of asserted in a paragraph.
 *  2. Each movement is judged by a single measure, in the same technical
 *     register. Strategy gets held to one too, which is the part clients do
 *     not expect.
 */
export function Services() {
  usePageMeta(
    'Services — AK Brand Development Studio',
    'Advisory, e-commerce build and paid media. Three lanes, one studio, stated handovers.',
  )

  return (
    <div className="pt-28 md:pt-36">
      <header className="mx-auto max-w-[1440px] px-5 md:px-10">
        <p className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--accent-text)]">
          Services
        </p>
        <CutText
          as="h1"
          trigger="mount"
          delay={0.15}
          className="mt-6 max-w-[16ch] font-display text-[clamp(2.5rem,8vw,6rem)] italic leading-[0.9] tracking-[-0.03em] text-[var(--text)]"
        >
          Four movements, and what each one hands over.
        </CutText>
        <p className="mt-8 max-w-[56ch] text-[clamp(0.9375rem,1.5vw,1.125rem)] leading-relaxed text-[var(--text-muted)]">
          {STUDIO.argument}
        </p>
      </header>

      {MOVEMENTS.map((movement, i) => (
        <section
          key={movement.id}
          id={movement.id}
          className="mx-auto mt-20 max-w-[1440px] scroll-mt-28 border-t border-[var(--rule)] px-5 pt-12 md:mt-28 md:px-10"
        >
          <div className="grid gap-10 md:grid-cols-[22rem_1fr] md:gap-16">
            {/* Left column holds position while the deliverables scroll past —
                the lane's identity stays on screen with its evidence. */}
            <div className="md:sticky md:top-28 md:self-start">
              <p className="font-mono text-[0.5625rem] uppercase tracking-[0.2em] text-[var(--text-faint)]">
                Movement {movement.index}
              </p>
              <h2 className="ak-vf mt-4 font-display text-[clamp(2.5rem,7vw,4.5rem)] italic leading-[0.9] text-[var(--text)]">
                {movement.title}
              </h2>
              <p className="mt-5 max-w-[34ch] text-[0.9375rem] leading-relaxed text-[var(--text-muted)]">
                {movement.claim}
              </p>

              <div className="mt-8 border border-[var(--rule-strong)] p-5">
                <p className="font-mono text-[0.5rem] uppercase tracking-[0.2em] text-[var(--text-faint)]">
                  Judged on — {movement.measure.key}
                </p>
                <p className="mt-2 font-mono text-[1.75rem] leading-none text-[var(--accent-text)]">
                  {movement.measure.value}
                </p>
                <p className="mt-3 text-[0.75rem] leading-relaxed text-[var(--text-muted)]">
                  {movement.measure.note}
                </p>
              </div>

              <p className="mt-6 font-mono text-[0.5625rem] uppercase tracking-[0.16em] text-[var(--text-muted)]">
                Typical engagement — {movement.duration}
              </p>
            </div>

            <div>
              <div className="mb-12 flex justify-end">
                <MovementDiagram movement={movement.id} />
              </div>

              <ul className="border-t border-[var(--rule)]">
                {movement.deliverables.map((d, j) => (
                  <Reveal key={d} as="li" delay={j * 0.04}>
                    <div className="flex items-baseline gap-5 border-b border-[var(--rule)] py-5">
                      <span className="font-mono text-[0.5rem] tabular-nums text-[var(--text-faint)]">
                        {String(j + 1).padStart(2, '0')}
                      </span>
                      <span className="text-[clamp(1rem,2vw,1.375rem)] leading-snug text-[var(--text)]">
                        {d}
                      </span>
                    </div>
                  </Reveal>
                ))}
              </ul>

              {/* The handover — the whole reason this is one page. */}
              <div className="mt-8 flex items-start gap-4 bg-[var(--bg-sunk)] p-6">
                <span
                  aria-hidden="true"
                  className="mt-1.5 h-px w-8 shrink-0 bg-[var(--accent-line)]"
                />
                <p className="max-w-[52ch] text-[0.875rem] leading-relaxed text-[var(--text-muted)]">
                  <span className="font-mono text-[0.5625rem] uppercase tracking-[0.16em] text-[var(--accent-text)]">
                    {i === MOVEMENTS.length - 1 ? 'Loops back — ' : 'Hands over — '}
                  </span>
                  {movement.handover}
                </p>
              </div>
            </div>
          </div>
        </section>
      ))}

      <div className="mx-auto mt-24 max-w-[1440px] px-5 pb-24 md:px-10">
        <Link
          to="/contact"
          className="inline-block bg-[var(--accent-fill)] px-7 py-4 font-mono text-[0.625rem] uppercase tracking-[0.2em] text-[var(--accent-on)]"
        >
          Work out what you actually need
        </Link>
      </div>
    </div>
  )
}
