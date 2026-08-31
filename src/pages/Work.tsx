import { useMemo, useState } from 'react'
import { WorkIndex } from '../components/sections/WorkIndex'
import { SectionHead } from '../components/primitives/SectionHead'
import { WORK } from '../data/work'
import { usePageMeta } from '../lib/usePageMeta'
import { cn } from '../lib/cn'

const FILTERS = [
  { id: 'all', label: 'All' },
  { id: 'strategy', label: 'Strategy' },
  { id: 'identity', label: 'Identity' },
  { id: 'production', label: 'Production' },
  { id: 'presence', label: 'Presence' },
] as const

export function Work() {
  usePageMeta(
    'Work — AK Brand Development Studio',
    'Case studies across advisory, e-commerce build and paid media for fashion brands.',
  )
  const [filter, setFilter] = useState<(typeof FILTERS)[number]['id']>('all')

  const items = useMemo(
    () => (filter === 'all' ? WORK : WORK.filter((c) => c.movements.includes(filter))),
    [filter],
  )

  return (
    <div className="pt-28 md:pt-36">
      <div className="mx-auto max-w-[1440px] px-5 md:px-10">
        <SectionHead
          level="h1"
          folio="01"
          eyebrow="Index"
          title="Every project, and the movement it came through."
          lead="Filter by what we actually did. Most of these ran through more than one movement — that is usually the point."
        />

        {/* Filters are real links to state, sized for touch, and the active one
            is announced rather than only coloured. */}
        <div className="mt-10 flex flex-wrap gap-2" role="group" aria-label="Filter work by lane">
          {FILTERS.map((f) => {
            const active = filter === f.id
            const count =
              f.id === 'all' ? WORK.length : WORK.filter((c) => c.movements.includes(f.id)).length
            return (
              <button
                key={f.id}
                type="button"
                onClick={() => setFilter(f.id)}
                aria-pressed={active}
                className={cn(
                  'flex items-baseline gap-2 border px-4 py-2.5 font-mono text-[0.5625rem] uppercase tracking-[0.16em] transition-colors',
                  active
                    ? 'border-transparent bg-[var(--accent-fill)] text-[var(--accent-on)]'
                    : 'border-[var(--rule-strong)] text-[var(--text-muted)] hover:border-[var(--accent-line)] hover:text-[var(--text)]',
                )}
              >
                {f.label}
                <span className="tabular-nums opacity-60">{String(count).padStart(2, '0')}</span>
              </button>
            )
          })}
        </div>
      </div>

      <WorkIndex items={items} />

      {items.length === 0 && (
        <p className="mx-auto max-w-[1440px] px-5 pb-24 font-mono text-[0.75rem] uppercase tracking-[0.16em] text-[var(--text-muted)] md:px-10">
          Nothing in this lane yet.
        </p>
      )}
    </div>
  )
}
