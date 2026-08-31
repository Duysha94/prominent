import { CutText } from '../components/primitives/CutText'
import { Reveal } from '../components/primitives/Reveal'
import { JOURNAL } from '../data/journal'
import { usePageMeta } from '../lib/usePageMeta'

export function Journal() {
  usePageMeta(
    'Journal — AK Brand Development Studio',
    'Notes on brand advisory, e-commerce and paid media for fashion brands.',
  )

  return (
    <div className="pt-28 md:pt-36">
      <header className="mx-auto max-w-[1440px] px-5 md:px-10">
        <p className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--accent-text)]">
          Journal
        </p>
        <CutText
          as="h1"
          trigger="mount"
          delay={0.15}
          className="mt-6 max-w-[16ch] font-display text-[clamp(2.5rem,8vw,6rem)] italic leading-[0.9] tracking-[-0.03em] text-[var(--text)]"
        >
          Notes, and the odd disagreement.
        </CutText>
      </header>

      <ul className="mx-auto mb-24 mt-16 max-w-[1440px] border-t border-[var(--rule)] px-5 md:px-10">
        {JOURNAL.map((note, i) => (
          <Reveal key={note.slug} as="li" delay={i * 0.05}>
            <article id={note.slug} className="scroll-mt-28 border-b border-[var(--rule)] py-9">
              <div className="grid gap-5 md:grid-cols-[4rem_1fr_9rem]">
                <span className="font-mono text-[0.5625rem] tabular-nums text-[var(--text-faint)]">
                  {note.folio}
                </span>
                <div>
                  <h2 className="max-w-[30ch] font-display text-[clamp(1.375rem,3.4vw,2.5rem)] italic leading-[1.05] text-[var(--text)]">
                    {note.title}
                  </h2>
                  <p className="mt-4 max-w-[62ch] text-[0.9375rem] leading-relaxed text-[var(--text-muted)]">
                    {note.standfirst}
                  </p>
                </div>
                <p className="flex flex-row gap-3 font-mono text-[0.5rem] uppercase tracking-[0.16em] text-[var(--text-faint)] md:flex-col md:text-right">
                  <span className="text-[var(--accent-text)]">{note.category}</span>
                  <span>{note.readTime}</span>
                  <time dateTime={note.date}>
                    {new Intl.DateTimeFormat('en-GB', { month: 'short', year: 'numeric' }).format(
                      new Date(note.date),
                    )}
                  </time>
                </p>
              </div>
            </article>
          </Reveal>
        ))}
      </ul>
    </div>
  )
}
