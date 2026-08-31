import { Reveal } from '../primitives/Reveal'
import { Plate } from '../primitives/Plate'
import { CutText } from '../primitives/CutText'
import type { CaseStudy } from '../../data/work'

/**
 * THE SPREAD — the template for advisory-led work.
 *
 * The output of the Advise lane is a sentence, so this template is built
 * around one: the position set at full display size, with the lines that were
 * rejected underneath it, struck through.
 *
 * Showing the rejects is the whole point. Any studio can present the line it
 * arrived at; showing the three plausible, comfortable, thoroughly average
 * alternatives it beat is the only way to demonstrate that a choice was
 * actually made — and it is the part of the process every agency deletes
 * before the client sees it.
 */
export function CaseSpread({ study }: { study: CaseStudy }) {
  return (
    <div className="mx-auto max-w-[1440px] px-5 md:px-10">
      <section className="border-t border-[var(--rule)] pt-12">
        <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-faint)]">
          The position
        </h2>

        <CutText
          as="p"
          className="ak-vf mt-8 max-w-[24ch] font-display text-[clamp(1.75rem,5vw,4rem)] italic leading-[1.02] tracking-[-0.02em] text-[var(--text)]"
        >
          {study.position?.statement ?? ''}
        </CutText>

        <div className="mt-16 grid gap-10 md:grid-cols-[1fr_1.1fr] md:gap-16">
          <div>
            <h3 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-faint)]">
              What it beat
            </h3>
            <ul className="mt-6">
              {(study.position?.rejected ?? []).map((r, i) => (
                <Reveal key={r} as="li" delay={i * 0.06}>
                  <p className="flex items-baseline gap-4 border-b border-[var(--rule)] py-4">
                    <span className="font-mono text-[0.5rem] tabular-nums text-[var(--text-faint)]">
                      {String(i + 1).padStart(2, '0')}
                    </span>
                    <span className="text-[clamp(0.9375rem,1.8vw,1.125rem)] leading-snug text-[var(--text-faint)] line-through decoration-[var(--accent-line)] decoration-1">
                      {r}
                    </span>
                  </p>
                </Reveal>
              ))}
            </ul>
            <p className="mt-6 max-w-[44ch] text-[0.8125rem] leading-relaxed text-[var(--text-muted)]">
              Each of these would have passed a board meeting. That is what is wrong with them: a
              position nobody could object to is a position nobody will remember.
            </p>
          </div>

          {/* The identity, shown as plates rather than a mockup on a desk. */}
          <div className="grid grid-cols-2 gap-4">
            {['mark', 'palette', 'campaign', 'packaging'].map((piece, i) => (
              <Reveal key={piece} delay={i * 0.05}>
                <figure className="m-0">
                  <Plate
                    seed={`${study.slug}-${piece}`}
                    tint={study.tint + i * 6}
                    className={`w-full overflow-hidden border border-[var(--rule)] ${
                      i % 3 === 0 ? 'aspect-[4/5]' : 'aspect-square'
                    }`}
                  />
                  <figcaption className="mt-2 font-mono text-[0.5rem] uppercase tracking-[0.16em] text-[var(--text-faint)]">
                    {piece}
                  </figcaption>
                </figure>
              </Reveal>
            ))}
          </div>
        </div>
      </section>
    </div>
  )
}
