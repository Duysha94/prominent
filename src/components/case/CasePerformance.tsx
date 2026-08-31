import { Reveal } from '../primitives/Reveal'
import { Plate } from '../primitives/Plate'
import { PerformanceChart } from './PerformanceChart'
import type { CaseStudy } from '../../data/work'

/**
 * THE PERFORMANCE SURFACE — the template for media-led work.
 *
 * Paid advertising is the service line agencies hide. It gets a testimonial
 * and a percentage, because there is no hero image and the actual artefacts —
 * ad creative and an account — are considered unshowable. That is precisely
 * why it is the most valuable page on this site to art-direct: nobody else is
 * doing it, and it is the work a fashion founder is most sceptical about.
 *
 * So the ads case study shows the account: the creative that ran, at the
 * proportions it ran at, with its click-through rate attached, next to eight
 * weeks of spend and blended ROAS. It is the one page on the site allowed to
 * be strange; everything else stays disciplined.
 */
export function CasePerformance({ study }: { study: CaseStudy }) {
  return (
    <div className="mx-auto max-w-[1440px] px-5 md:px-10">
      <div className="grid gap-12 border-t border-[var(--rule)] pt-12 lg:grid-cols-[1fr_26rem] lg:gap-16">
        {/* The creative, in a feed. */}
        <section>
          <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-faint)]">
            The creative that ran
          </h2>

          <ul className="mt-6 space-y-5">
            {(study.creative ?? []).map((c, i) => (
              <Reveal key={c.hook} as="li" delay={i * 0.06}>
                <article className="flex gap-5 border border-[var(--rule)] p-4">
                  {/* 4:5 — the ratio the work was actually made for. */}
                  <Plate
                    seed={`${study.slug}-${i}`}
                    tint={c.tint}
                    className="aspect-[4/5] w-28 shrink-0 overflow-hidden md:w-36"
                  />
                  <div className="flex min-w-0 flex-col">
                    <span className="font-mono text-[0.5rem] uppercase tracking-[0.18em] text-[var(--accent-text)]">
                      {c.platform}
                    </span>
                    <p className="mt-3 font-display text-[clamp(1.125rem,2.4vw,1.75rem)] italic leading-tight text-[var(--text)]">
                      {c.hook}
                    </p>
                    <dl className="mt-auto flex gap-6 pt-5">
                      <div>
                        <dt className="font-mono text-[0.5rem] uppercase tracking-[0.16em] text-[var(--text-faint)]">
                          CTR
                        </dt>
                        <dd className="mt-1 font-mono text-[0.9375rem] text-[var(--text)]">{c.ctr}</dd>
                      </div>
                      <div>
                        <dt className="font-mono text-[0.5rem] uppercase tracking-[0.16em] text-[var(--text-faint)]">
                          Ratio
                        </dt>
                        <dd className="mt-1 font-mono text-[0.9375rem] text-[var(--text)]">4:5</dd>
                      </div>
                    </dl>
                  </div>
                </article>
              </Reveal>
            ))}
          </ul>

          <p className="mt-6 max-w-[54ch] text-[0.8125rem] leading-relaxed text-[var(--text-muted)]">
            Every asset was drawn at 4:5 first and at campaign scale second. An identity that only
            works at billboard size is an identity the algorithm will crop, shrink and ignore.
          </p>
        </section>

        {/* The account. */}
        <aside className="lg:sticky lg:top-28 lg:h-fit">
          <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-faint)]">
            The account, eight weeks
          </h2>
          <div className="mt-6 border border-[var(--rule-strong)] p-5">
            <PerformanceChart series={study.series ?? []} />
          </div>
          <p className="mt-4 text-[0.75rem] leading-relaxed text-[var(--text-muted)]">
            Blended, not last-click: total revenue over total spend across every channel. It is a
            smaller number than the platform reports, and it is the one the bank agrees with.
          </p>
        </aside>
      </div>
    </div>
  )
}
