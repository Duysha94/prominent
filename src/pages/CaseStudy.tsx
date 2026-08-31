import { Link, useParams } from 'react-router-dom'
import { WORK, laneLabel } from '../data/work'
import { CutText } from '../components/primitives/CutText'
import { MeasureFrame } from '../components/primitives/MeasureFrame'
import { Plate } from '../components/primitives/Plate'
import { Reveal } from '../components/primitives/Reveal'
import { CaseSpread } from '../components/case/CaseSpread'
import { CaseWalkthrough } from '../components/case/CaseWalkthrough'
import { CasePerformance } from '../components/case/CasePerformance'
import { NotFound } from './NotFound'
import { usePageMeta } from '../lib/usePageMeta'

/**
 * A case study opens the same way every time — measured plate, chapters, each
 * with its measurement in the margin — and then branches. The lane that LED
 * the engagement picks the body template: a spread for advisory, a walkthrough
 * for a build, a performance surface for media.
 *
 * Forcing three different kinds of work through one layout is what makes a
 * mixed portfolio read as three unrelated departments; giving each its own
 * shape is what makes it read as one practice with three tools.
 *
 * The plate carries a `view-transition-name` matched to the index, so on
 * browsers with View Transitions the thumbnail morphs into the hero rather
 * than cross-fading. On the WordPress build the identical names work
 * cross-document via `@view-transition { navigation: auto }`.
 */
export function CaseStudyPage() {
  const { slug } = useParams()
  const study = WORK.find((c) => c.slug === slug)

  usePageMeta(
    study ? `${study.client} — AK Brand Development Studio` : 'Not found',
    study?.summary,
  )

  if (!study) return <NotFound />

  const next = WORK[(WORK.findIndex((c) => c.slug === study.slug) + 1) % WORK.length]

  return (
    <article className="pt-28 md:pt-36">
      <header className="mx-auto max-w-[1440px] px-5 md:px-10">
        <p className="flex flex-wrap items-center gap-x-4 gap-y-1 font-mono text-[0.5625rem] uppercase tracking-[0.2em] text-[var(--text-muted)]">
          <span className="tabular-nums text-[var(--text-faint)]">{study.folio}</span>
          <span className="text-[var(--accent-text)]">{study.client}</span>
          <span>{study.category}</span>
          <span>{study.year}</span>
          <span className="flex gap-1.5">
            {study.lanes.map((l) => (
              <span key={l} className="border border-[var(--rule-strong)] px-1.5 py-0.5">
                {laneLabel[l]}
              </span>
            ))}
          </span>
        </p>

        <CutText
          as="h1"
          trigger="mount"
          delay={0.15}
          className="mt-7 max-w-[20ch] font-display text-[clamp(2.25rem,7vw,5.5rem)] italic leading-[0.92] tracking-[-0.025em] text-[var(--text)]"
        >
          {study.headline}
        </CutText>

        <p className="mt-8 max-w-[58ch] text-[clamp(0.9375rem,1.5vw,1.125rem)] leading-relaxed text-[var(--text-muted)]">
          {study.summary}
        </p>
      </header>

      <div className="mx-auto mt-14 max-w-[1440px] px-5 md:px-10">
        <MeasureFrame always measures={study.measures} label={`${study.client} — ${study.year}`}>
          <Plate
            seed={study.slug}
            tint={study.tint}
            label={study.client}
            className="aspect-[21/8] w-full overflow-hidden"
            /* matched to the index thumbnail for the shared-element morph */
          />
        </MeasureFrame>
      </div>

      <div className="mx-auto mt-20 max-w-[1440px] px-5 md:mt-28 md:px-10">
        <ol className="border-t border-[var(--rule)]">
          {study.chapters.map((ch, i) => (
            <Reveal key={ch.title} as="li" delay={i * 0.05}>
              <div className="grid gap-4 border-b border-[var(--rule)] py-10 md:grid-cols-[4rem_1fr_14rem] md:gap-10">
                <span className="font-mono text-[0.5625rem] tabular-nums text-[var(--text-faint)]">
                  {String(i + 1).padStart(2, '0')}
                </span>
                <div>
                  <h2 className="font-display text-[clamp(1.375rem,3vw,2.25rem)] italic leading-tight text-[var(--text)]">
                    {ch.title}
                  </h2>
                  <p className="mt-4 max-w-[56ch] text-[0.9375rem] leading-relaxed text-[var(--text-muted)]">
                    {ch.body}
                  </p>
                </div>
                {ch.measure && (
                  <div className="self-start border border-[var(--rule-strong)] p-4">
                    <p className="font-mono text-[0.5rem] uppercase tracking-[0.18em] text-[var(--text-faint)]">
                      {ch.measure.key}
                    </p>
                    <p className="mt-2 font-mono text-[1.375rem] leading-none text-[var(--accent-text)]">
                      {ch.measure.value}
                    </p>
                  </div>
                )}
              </div>
            </Reveal>
          ))}
        </ol>

      </div>

      {/* The lane that led decides the shape of the evidence. */}
      <div className="mt-20 md:mt-28">
        {study.lead === 'advise' && <CaseSpread study={study} />}
        {study.lead === 'build' && <CaseWalkthrough study={study} />}
        {study.lead === 'grow' && <CasePerformance study={study} />}
      </div>

      <div className="mx-auto max-w-[1440px] px-5 md:px-10">
        <Link
          to={`/work/${next.slug}`}
          className="group mt-16 mb-24 block border-t border-[var(--rule)] pt-8"
        >
          <span className="font-mono text-[0.5rem] uppercase tracking-[0.2em] text-[var(--text-faint)]">
            Next — {next.folio}
          </span>
          <span className="mt-3 block font-display text-[clamp(1.5rem,4vw,3rem)] italic leading-tight text-[var(--text)] transition-colors group-hover:text-[var(--accent-text)]">
            {next.headline}
          </span>
        </Link>
      </div>
    </article>
  )
}
