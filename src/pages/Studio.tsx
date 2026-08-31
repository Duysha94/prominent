import { CutText } from '../components/primitives/CutText'
import { Reveal } from '../components/primitives/Reveal'
import { Marquee } from '../components/primitives/Marquee'
import { MeasureFrame } from '../components/primitives/MeasureFrame'
import { Plate } from '../components/primitives/Plate'
import { STUDIO, CREDENTIALS } from '../data/studio'
import { usePageMeta } from '../lib/usePageMeta'

/** PLACEHOLDER CONTENT — see data/studio.ts. */
const PRINCIPLES = [
  {
    title: 'We say the number',
    body: 'Blended, not last-click. With the window and the channel mix attached. If it is down, it is in the report in the same size type as when it is up.',
  },
  {
    title: 'One page or it is not finished',
    body: 'A position that needs twelve slides to explain is a position nobody in your team will remember on a Tuesday. We write it until it fits on one page.',
  },
  {
    title: 'We do not take competing brands',
    body: 'One label per category at a time. It costs us work and it is the only way the advice stays worth having.',
  },
  {
    title: 'The build is not a handover',
    body: 'The people who wrote the position build the store and run the media. Nothing is explained twice, and nothing is lost explaining it.',
  },
]

export function Studio() {
  usePageMeta(
    'Studio — AK Brand Development Studio',
    'How the studio works, what it refuses, and who it is for.',
  )

  return (
    <div className="pt-28 md:pt-36">
      <header className="mx-auto max-w-[1440px] px-5 md:px-10">
        <p className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--accent-text)]">
          Studio — est. {STUDIO.founded}
        </p>
        <CutText
          as="h1"
          trigger="mount"
          delay={0.15}
          className="mt-6 max-w-[15ch] font-display text-[clamp(2.5rem,8vw,6rem)] italic leading-[0.9] tracking-[-0.03em] text-[var(--text)]"
        >
          A small studio that holds the whole line.
        </CutText>
        <p className="mt-8 max-w-[58ch] text-[clamp(0.9375rem,1.5vw,1.125rem)] leading-relaxed text-[var(--text-muted)]">
          {STUDIO.promise}
        </p>
      </header>

      <div className="mx-auto mt-16 max-w-[1440px] px-5 md:px-10">
        <MeasureFrame
          always
          label="The workroom"
          measures={[
            { key: 'FOUNDED', value: String(STUDIO.founded), edge: 'right', at: 0.3 },
            { key: 'BRANDS', value: '40+', edge: 'right', at: 0.66 },
          ]}
        >
          <Plate
            seed="studio-portrait"
            tint={40}
            label="Studio"
            className="aspect-[16/6] w-full overflow-hidden"
          />
        </MeasureFrame>
        {/* Honest about the placeholder rather than passing a generated image
            off as a photograph — see the note in the concept documentation. */}
        <p className="mt-3 font-mono text-[0.5rem] uppercase tracking-[0.16em] text-[var(--text-faint)]">
          Placeholder — replace with commissioned studio photography, dithered to two brand colours
        </p>
      </div>

      <section className="mx-auto mt-24 max-w-[1440px] px-5 md:px-10">
        <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-muted)]">
          How we work
        </h2>
        <ul className="mt-8 grid gap-px border border-[var(--rule)] bg-[var(--rule)] md:grid-cols-2">
          {PRINCIPLES.map((p, i) => (
            <Reveal key={p.title} as="li" delay={i * 0.05}>
              <div className="h-full bg-[var(--bg)] p-7">
                <span className="font-mono text-[0.5rem] tabular-nums text-[var(--text-faint)]">
                  {String(i + 1).padStart(2, '0')}
                </span>
                <h3 className="mt-3 font-display text-[clamp(1.25rem,2.6vw,1.875rem)] italic leading-tight text-[var(--text)]">
                  {p.title}
                </h3>
                <p className="mt-3 max-w-[46ch] text-[0.875rem] leading-relaxed text-[var(--text-muted)]">
                  {p.body}
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
          label="Studio credentials"
        >
          {CREDENTIALS.map((c) => (
            <span
              key={c.label}
              className="flex items-baseline gap-3 px-8 font-mono text-[0.6875rem] uppercase tracking-[0.16em]"
            >
              {c.label}
              <span className="opacity-60">{c.meta}</span>
            </span>
          ))}
        </Marquee>
      </div>

      <section className="mx-auto mb-24 mt-24 max-w-[1440px] px-5 md:px-10">
        <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-muted)]">
          What we will not do
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
