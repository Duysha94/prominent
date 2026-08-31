import { Reveal } from '../primitives/Reveal'
import { Plate } from '../primitives/Plate'
import { PerformanceChart } from './PerformanceChart'
import type { CaseStudy } from '../../data/work'

/**
 * THE PRODUCTION SURFACE — the template for production-led work.
 *
 * This is the studio's crown jewel and the page most worth art-directing,
 * because it is the one thing a competitor cannot copy: the founders built
 * London Fashion Day and Odessa Fashion Day, so a client's collection can
 * actually be shown rather than merely strategised about.
 *
 * A show or a campaign is evidence of a specific kind — what was made, and
 * what happened afterwards. So the template gives equal weight to both: the
 * work at the ratios it was made for, and the eight weeks that followed it,
 * with reach and press picked out separately rather than blended into one
 * flattering number.
 */
export function CaseProduction({ study }: { study: CaseStudy }) {
  return (
    <div className="mx-auto max-w-[1440px] px-5 md:px-10">
      <div className="grid gap-12 border-t border-[var(--rule)] pt-12 lg:grid-cols-[1fr_26rem] lg:gap-16">
        <section>
          <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-faint)]">
            What was made
          </h2>

          <ul className="mt-6 grid grid-cols-2 gap-5">
            {(study.looks ?? []).map((look, i) => (
              <Reveal key={look.label} as="li" delay={i * 0.05}>
                <figure className="m-0">
                  {/* 4:5 — the ratio campaign and runway imagery actually
                      lives in once it reaches an audience. */}
                  <Plate
                    seed={`${study.slug}-${look.label}`}
                    tint={look.tint}
                    className="aspect-[4/5] w-full overflow-hidden border border-[var(--rule)]"
                  />
                  <figcaption className="mt-2 flex items-baseline justify-between gap-3">
                    <span className="font-mono text-[0.5rem] uppercase tracking-[0.16em] text-[var(--text)]">
                      {look.label}
                    </span>
                    <span className="font-mono text-[0.5rem] uppercase tracking-[0.14em] text-[var(--text-faint)]">
                      {look.note}
                    </span>
                  </figcaption>
                </figure>
              </Reveal>
            ))}
          </ul>

          <p className="mt-6 max-w-[54ch] text-[0.8125rem] leading-relaxed text-[var(--text-muted)]">
            Every asset is made for the crop it will be seen in. An image that only works at full
            bleed is an image the feed will crop, shrink and bury.
          </p>
        </section>

        <aside className="lg:sticky lg:top-28 lg:h-fit">
          <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-faint)]">
            The eight weeks after
          </h2>
          <div className="mt-6 border border-[var(--rule-strong)] p-5">
            <PerformanceChart series={study.series ?? []} />
          </div>
          <p className="mt-4 text-[0.75rem] leading-relaxed text-[var(--text-muted)]">
            Reach and press are reported separately, never combined into one number. They behave
            differently and they are worth different things.
          </p>
        </aside>
      </div>
    </div>
  )
}
