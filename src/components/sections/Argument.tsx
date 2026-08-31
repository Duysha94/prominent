import { CutText } from '../primitives/CutText'
import { Reveal } from '../primitives/Reveal'
import { STUDIO } from '../../data/studio'
import { LANES } from '../../data/services'

/**
 * THE ARGUMENT — the only full-bleed accent section on the home page.
 *
 * Almost every brand with a saturated accent spends it at 5-10% coverage:
 * grey page, orange buttons. Running a whole section at 100% #f37021 with warm
 * ink type on top is the move nobody makes, largely out of nerve — and it is
 * measurably the *safer* one, because ink on orange is 7.15:1 (AAA) while the
 * white-on-orange everybody defaults to is 2.94:1 and fails outright.
 *
 * It is used exactly once per page. Spent twice, it stops being an event.
 */
export function Argument() {
  return (
    <section className="ak-accent-field relative overflow-hidden py-24 md:py-32">
      <div className="mx-auto max-w-[1440px] px-5 md:px-10">
        <p className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] opacity-70">
          Why one studio
        </p>

        <CutText
          as="h2"
          blade={false}
          className="mt-6 max-w-[20ch] font-display text-[clamp(2.25rem,6.5vw,5.5rem)] italic leading-[0.92] tracking-[-0.025em]"
        >
          Three companies, and you pay for the seams between them.
        </CutText>

        <Reveal delay={0.1}>
          <p className="mt-8 max-w-[58ch] text-[clamp(0.9375rem,1.5vw,1.1875rem)] leading-relaxed opacity-80">
            {STUDIO.argument}
          </p>
        </Reveal>

        {/* The handovers stated explicitly — this is the proof of the claim,
            not a restatement of it. */}
        <ol className="mt-16 grid gap-px border border-[var(--rule-strong)] bg-[var(--rule-strong)] md:grid-cols-3">
          {LANES.map((lane, i) => (
            <Reveal key={lane.id} delay={0.08 * i} as="li">
              <div className="h-full bg-[var(--color-brand-500)] p-6">
                <span className="font-mono text-[0.5rem] uppercase tracking-[0.2em] opacity-60">
                  Handover {lane.index}
                </span>
                <h3 className="mt-3 font-display text-2xl italic">{lane.title}</h3>
                <p className="mt-3 text-[0.8125rem] leading-relaxed opacity-80">{lane.handover}</p>
              </div>
            </Reveal>
          ))}
        </ol>
      </div>
    </section>
  )
}
