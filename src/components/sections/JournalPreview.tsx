import { Link } from 'react-router-dom'
import { SectionHead } from '../primitives/SectionHead'
import { Reveal } from '../primitives/Reveal'
import { JOURNAL } from '../../data/journal'

/**
 * Notes, set as a publication's stub column rather than as blog cards.
 * The standfirst does the selling; a thumbnail would only add weight.
 */
export function JournalPreview() {
  return (
    <section className="px-5 py-20 md:px-10 md:py-28">
      <div className="mx-auto max-w-[1440px]">
        <SectionHead
          folio="04"
          eyebrow="Journal"
          title="What we have argued about lately."
          aside={
            <Link
              to="/journal"
              className="hidden shrink-0 border border-[var(--rule-strong)] px-5 py-3 font-mono text-[0.5625rem] uppercase tracking-[0.16em] text-[var(--text)] transition-colors hover:border-[var(--accent-line)] hover:text-[var(--accent-text)] md:inline-block"
            >
              All notes
            </Link>
          }
        />

        <ul className="mt-12 grid gap-px border-t border-[var(--rule)] md:grid-cols-2">
          {JOURNAL.slice(0, 4).map((note, i) => (
            <Reveal key={note.slug} delay={i * 0.05} as="li">
              <article className="h-full border-b border-[var(--rule)] py-7 md:pr-10">
                <p className="flex items-center gap-3 font-mono text-[0.5rem] uppercase tracking-[0.18em] text-[var(--text-faint)]">
                  <span className="tabular-nums">{note.folio}</span>
                  <span className="text-[var(--accent-text)]">{note.category}</span>
                  <span>{note.readTime}</span>
                </p>
                <h3 className="mt-3 max-w-[28ch] font-display text-[clamp(1.125rem,2.2vw,1.6rem)] italic leading-snug text-[var(--text)]">
                  <Link to={`/journal#${note.slug}`} className="transition-colors hover:text-[var(--accent-text)]">
                    {note.title}
                  </Link>
                </h3>
                <p className="mt-3 max-w-[46ch] text-[0.8125rem] leading-relaxed text-[var(--text-muted)]">
                  {note.standfirst}
                </p>
              </article>
            </Reveal>
          ))}
        </ul>
      </div>
    </section>
  )
}
