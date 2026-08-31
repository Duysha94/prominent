import { Link } from 'react-router-dom'
import { CutText } from '../components/primitives/CutText'
import { usePageMeta } from '../lib/usePageMeta'

export function NotFound() {
  usePageMeta('Not found — AK Brand Development Studio')

  return (
    <section className="mx-auto flex min-h-[70svh] max-w-[1440px] flex-col justify-center px-5 py-32 md:px-10">
      <p className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--accent-text)]">
        404 — off the pattern
      </p>
      <CutText
        as="h1"
        trigger="mount"
        className="mt-6 max-w-[16ch] font-display text-[clamp(2.5rem,8vw,6rem)] italic leading-[0.92] text-[var(--text)]"
      >
        This piece was never cut.
      </CutText>
      <Link
        to="/"
        className="mt-10 self-start bg-[var(--accent-fill)] px-6 py-3.5 font-mono text-[0.625rem] uppercase tracking-[0.2em] text-[var(--accent-on)]"
      >
        Back to the index
      </Link>
    </section>
  )
}
