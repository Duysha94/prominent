import { Reveal } from '../primitives/Reveal'
import { SectionHead } from '../primitives/SectionHead'
import { STUDIO } from '../../data/studio'

/**
 * THE PROOF — platforms, not statistics.
 *
 * The obvious move here is a row of counters: projects delivered, years in
 * business, happy clients. Those numbers are unverifiable, every agency site
 * has them, and a reader discounts them on sight.
 *
 * This studio has something better and rarer: its founders built the things.
 * London Fashion Day and Odessa Fashion Day are checkable facts, and they
 * answer the only question that matters — can you actually get my collection
 * in front of people, or can you only advise me about it?
 *
 * So the section states what was founded, by whom, and leaves it there. No
 * counters, because a fact does not need to be animated to be believed.
 */
export function Proof() {
  return (
    <section className="border-y border-[var(--rule)] px-5 py-20 md:px-10 md:py-28">
      <div className="mx-auto max-w-[1440px]">
        <SectionHead
          folio="03"
          eyebrow="What we built"
          title="We do not rent the platform. We founded it."
          lead="Most studios advising a young designer have to ask someone else for a slot. These are ours — which is why we can put a first collection on an international runway rather than write a deck about one."
        />

        <ul className="mt-14 grid gap-px border border-[var(--rule)] bg-[var(--rule)] sm:grid-cols-2">
          {STUDIO.platforms.map((platform, i) => (
            <Reveal key={platform.name} delay={i * 0.06} as="li">
              <article className="flex h-full flex-col bg-[var(--bg)] p-7">
                <span className="font-mono text-[0.5rem] tabular-nums text-[var(--text-faint)]">
                  {String(i + 1).padStart(2, '0')}
                </span>
                <h3 className="ak-vf mt-3 font-display text-[clamp(1.5rem,3.2vw,2.25rem)] italic leading-tight text-[var(--text)]">
                  {platform.name}
                </h3>
                <p className="mt-3 font-mono text-[0.5625rem] uppercase leading-relaxed tracking-[0.12em] text-[var(--accent-text)]">
                  {platform.role}
                </p>
                <p className="mt-auto pt-4 text-[0.8125rem] leading-relaxed text-[var(--text-muted)]">
                  {platform.note}
                </p>
              </article>
            </Reveal>
          ))}
        </ul>
      </div>
    </section>
  )
}
