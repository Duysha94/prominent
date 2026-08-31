import { Link } from 'react-router-dom'
import { Hero } from '../components/sections/Hero'
import { Argument } from '../components/sections/Argument'
import { WorkIndex } from '../components/sections/WorkIndex'
import { Proof } from '../components/sections/Proof'
import { JournalPreview } from '../components/sections/JournalPreview'
import { SectionHead } from '../components/primitives/SectionHead'
import { WORK } from '../data/work'
import { usePageMeta } from '../lib/usePageMeta'

export function Home() {
  usePageMeta(
    'AK Brand Development Studio — Fashion & Brand Advisory',
    'Advisory, identity, e-commerce build and paid media for fashion brands. One studio, three lanes, no handover losses.',
  )

  return (
    <>
      <Hero />
      <Argument />
      <WorkIndex
        items={WORK.slice(0, 4)}
        heading={
          <SectionHead
            folio="02"
            eyebrow="Selected work"
            title="Selected work, and what it moved."
            lead="Read it like a contents page. Open a line to see the work."
            aside={
              <Link
                to="/work"
                className="hidden shrink-0 border border-[var(--rule-strong)] px-5 py-3 font-mono text-[0.5625rem] uppercase tracking-[0.16em] text-[var(--text)] transition-colors hover:border-[var(--accent-line)] hover:text-[var(--accent-text)] md:inline-block"
              >
                All work
              </Link>
            }
          />
        }
      />
      <Proof />
      <JournalPreview />
    </>
  )
}
