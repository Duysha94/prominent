import type { ReactNode } from 'react'
import { CutText } from './CutText'
import { cn } from '../../lib/cn'

/**
 * The house section opener: folio, rule, eyebrow, then the statement.
 * Deliberately left-aligned everywhere — centring every heading is the fastest
 * way to make a page read as a template.
 */
export function SectionHead({
  folio,
  eyebrow,
  title,
  lead,
  aside,
  className,
  level = 'h2',
}: {
  folio: string
  eyebrow: string
  title: string
  lead?: string
  aside?: ReactNode
  className?: string
  /**
   * Heading level. Defaults to h2 because this is usually a section opener,
   * but a page whose only heading IS this opener must pass 'h1' — every page
   * needs exactly one h1, and a page that opens with an h2 leaves a hole at
   * the top of its outline.
   */
  level?: 'h1' | 'h2'
}) {
  return (
    <div className={cn('grid gap-8 md:grid-cols-[1fr_auto] md:items-end', className)}>
      <div>
        <p className="flex items-center gap-3 font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-muted)]">
          <span className="tabular-nums text-[var(--text-faint)]">{folio}</span>
          <span aria-hidden="true" className="ak-draw h-px w-12 bg-[var(--accent-line)]" />
          {eyebrow}
        </p>
        <CutText
          as={level}
          className="ak-vf mt-5 max-w-[18ch] font-display text-[clamp(2rem,5.5vw,4.25rem)] italic leading-[0.94] tracking-[-0.02em] text-[var(--text)]"
        >
          {title}
        </CutText>
        {lead && (
          <p className="mt-5 max-w-[52ch] text-[0.9375rem] leading-relaxed text-[var(--text-muted)]">
            {lead}
          </p>
        )}
      </div>
      {aside}
    </div>
  )
}
