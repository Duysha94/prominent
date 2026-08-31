import { CutText } from '../components/primitives/CutText'
import { Reveal } from '../components/primitives/Reveal'
import { Marquee } from '../components/primitives/Marquee'
import { MeasureFrame } from '../components/primitives/MeasureFrame'
import { Plate } from '../components/primitives/Plate'
import { STUDIO, FOUNDERS, LIVE } from '../data/studio'
import { usePageMeta } from '../lib/usePageMeta'

/**
 * ABOUT — two named people, not a "we".
 *
 * This is the highest-value page on the site and it is the one most agencies
 * waste. A studio's credibility is not its adjectives; it is who is actually
 * going to do the work and what they have already done. Konstantin founded
 * two international fashion platforms and ran a regional branch of an
 * advertising holding for nine years; Andrey has run retail for emerging
 * designers and founded a media title. Those are checkable facts and they
 * answer a client's real question, which is "why you".
 *
 * So the founders get the page, in full, with their own words — and the
 * studio's philosophy sits underneath them rather than above.
 */
export function Studio() {
  usePageMeta(
    'About the studio — AK Brand Development Studio',
    'An independent creative and strategic practice in London, founded by Konstantin Lieontiev and Andrey Karakushan. Brand development, fashion consulting and creative production.',
  )

  return (
    <div className="pt-28 md:pt-36">
      <header className="mx-auto max-w-[1440px] px-5 md:px-10">
        <p className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--accent-text)]">
          The studio — {STUDIO.city}, {STUDIO.country}
        </p>
        <CutText
          as="h1"
          trigger="mount"
          delay={0.15}
          className="mt-6 max-w-[17ch] font-display text-[clamp(2.5rem,8vw,6rem)] italic leading-[0.9] tracking-[-0.03em] text-[var(--text)]"
        >
          An independent practice at the intersection of strategy and production.
        </CutText>
        <p className="mt-8 max-w-[62ch] text-[clamp(0.9375rem,1.5vw,1.125rem)] leading-relaxed text-[var(--text-muted)]">
          {STUDIO.description} The studio supports founders, designers and businesses in building
          strong brand identities, developing strategic positioning and creating meaningful
          visibility through creative campaigns, events and media presence.
        </p>
      </header>

      {/* ── The founders ────────────────────────────────────────────────── */}
      <section className="mx-auto mt-20 max-w-[1440px] px-5 md:mt-28 md:px-10">
        <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-muted)]">
          Founded by
        </h2>

        <div className="mt-10 space-y-20 md:space-y-28">
          {FOUNDERS.map((person, i) => (
            <article
              key={person.id}
              id={person.id}
              className="grid scroll-mt-28 gap-8 border-t border-[var(--rule)] pt-10 md:grid-cols-[minmax(0,22rem)_1fr] md:gap-14"
            >
              <div className="md:sticky md:top-28 md:self-start">
                <MeasureFrame
                  always
                  label={person.name}
                  measures={person.facts.map((f, j) => ({
                    key: f.key,
                    value: f.value,
                    edge: j === 1 ? 'left' : 'right',
                    at: 0.28 + j * 0.24,
                  }))}
                >
                  <Plate
                    seed={person.id}
                    tint={i === 0 ? 34 : 210}
                    className="aspect-[4/5] w-full overflow-hidden border border-[var(--rule)]"
                  />
                </MeasureFrame>
                <p className="mt-2 font-mono text-[0.5rem] uppercase tracking-[0.16em] text-[var(--text-faint)]">
                  Placeholder — replace with a portrait
                </p>
              </div>

              <div>
                <span className="font-mono text-[0.5rem] tabular-nums text-[var(--text-faint)]">
                  {String(i + 1).padStart(2, '0')}
                </span>
                <h3 className="ak-vf mt-3 font-display text-[clamp(2rem,5vw,3.5rem)] italic leading-[0.95] text-[var(--text)]">
                  {person.name}
                </h3>
                <p className="mt-3 font-mono text-[0.5625rem] uppercase tracking-[0.16em] text-[var(--accent-text)]">
                  {person.role}
                </p>
                <div className="mt-7 space-y-5">
                  {person.bio.map((para) => (
                    <p
                      key={para.slice(0, 24)}
                      className="max-w-[62ch] text-[0.9375rem] leading-relaxed text-[var(--text-muted)]"
                    >
                      {para}
                    </p>
                  ))}
                </div>
              </div>
            </article>
          ))}
        </div>
      </section>

      {/* ── What the founders built ─────────────────────────────────────── */}
      <section className="mx-auto mt-24 max-w-[1440px] px-5 md:px-10">
        <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-muted)]">
          Platforms and brands we founded
        </h2>
        <ul className="mt-8 grid gap-px border border-[var(--rule)] bg-[var(--rule)] md:grid-cols-2">
          {STUDIO.platforms.map((platform, i) => (
            <Reveal key={platform.name} as="li" delay={i * 0.05}>
              <div className="h-full bg-[var(--bg)] p-7">
                <h3 className="font-display text-[clamp(1.25rem,2.6vw,1.875rem)] italic leading-tight text-[var(--text)]">
                  {platform.name}
                </h3>
                <p className="mt-3 font-mono text-[0.5rem] uppercase tracking-[0.14em] text-[var(--accent-text)]">
                  {platform.role}
                </p>
                <p className="mt-3 max-w-[46ch] text-[0.875rem] leading-relaxed text-[var(--text-muted)]">
                  {platform.note}
                </p>
              </div>
            </Reveal>
          ))}
        </ul>
      </section>

      <div className="mt-24">
        <Marquee
          className="ak-accent-field border-y border-[var(--rule-strong)] py-5"
          speed={48}
          label="Studio facts"
        >
          {LIVE.map((item) => (
            <span
              key={item.value}
              className="flex items-baseline gap-3 whitespace-nowrap px-8 font-mono text-[0.6875rem] uppercase tracking-[0.16em]"
            >
              <span className="opacity-60">{item.key}</span>
              {item.value}
            </span>
          ))}
        </Marquee>
      </div>

      {/* ── How we work ─────────────────────────────────────────────────── */}
      <section className="mx-auto mb-24 mt-24 max-w-[1440px] px-5 md:px-10">
        <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-muted)]">
          How we work
        </h2>
        <ul className="mt-8 max-w-[62ch] space-y-4">
          {STUDIO.care.map((line) => (
            <li key={line} className="flex gap-4 border-b border-[var(--rule)] pb-4">
              <span aria-hidden="true" className="mt-2.5 h-px w-6 shrink-0 bg-[var(--accent-line)]" />
              <span className="text-[clamp(1rem,2vw,1.25rem)] leading-snug text-[var(--text)]">
                {line}
              </span>
            </li>
          ))}
        </ul>
      </section>
    </div>
  )
}
