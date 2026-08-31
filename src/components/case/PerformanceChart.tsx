import { useId, useState } from 'react'
import { cn } from '../../lib/cn'

export type SeriesPoint = { week: string; reach: number; press: number }

/**
 * CAMPAIGN REACH AND PRESS PICK-UP — as two charts, not one.
 *
 * Reach is in the hundreds of thousands and press mentions are in the tens.
 * Putting them on one plot needs two y-scales, and a dual-axis chart lets you
 * place the two series' crossings anywhere you like by choosing the scales —
 * which is exactly the wrong chart for a page whose argument is that the
 * numbers are honest. Small multiples share the x axis, keep one scale each,
 * and let the reader compare shapes without being steered.
 *
 * One series per panel, so neither needs a legend — the panel title names it.
 * Thin marks, recessive grid, only the final value directly labelled.
 *
 * The SVG is decorative; the accessible representation is the real table
 * below, which is always in the DOM.
 */
export function PerformanceChart({
  series,
  className,
}: {
  series: SeriesPoint[]
  className?: string
}) {
  const [hover, setHover] = useState<number | null>(null)
  const [showTable, setShowTable] = useState(false)
  const tableId = useId()

  if (series.length < 2) return null

  const maxReach = Math.max(...series.map((d) => d.reach))
  const maxPress = Math.max(...series.map((d) => d.press))

  const x = (i: number) => (i / (series.length - 1)) * 100
  const pressY = (v: number) => 100 - (v / (maxPress || 1)) * 100

  const compact = (n: number) =>
    n >= 1000 ? `${(n / 1000).toFixed(n >= 10000 ? 0 : 1)}k` : String(n)

  const at = hover ?? series.length - 1

  return (
    <div className={cn('w-full', className)}>
      {/* ── Panel 1: weekly reach ─────────────────────────────────────── */}
      <figure className="m-0">
        <figcaption className="flex items-baseline justify-between gap-4 border-b border-[var(--rule)] pb-2">
          <span className="font-mono text-[0.5rem] uppercase tracking-[0.18em] text-[var(--text-faint)]">
            Weekly reach
          </span>
          <span className="font-mono text-[0.625rem] text-[var(--text)]">
            {compact(series[at].reach)}
          </span>
        </figcaption>

        <div className="relative mt-4 flex h-28 items-end gap-[2px]" aria-hidden="true">
          {series.map((d, i) => (
            <button
              key={d.week}
              type="button"
              tabIndex={-1}
              onMouseEnter={() => setHover(i)}
              onMouseLeave={() => setHover(null)}
              className="flex h-full flex-1 items-end"
            >
              <span
                className={cn(
                  'w-full rounded-t-[4px] transition-colors',
                  hover === i ? 'bg-[var(--accent-fill)]' : 'bg-[var(--text-muted)]',
                )}
                style={{ height: `${(d.reach / maxReach) * 100}%` }}
              />
            </button>
          ))}
        </div>
      </figure>

      {/* ── Panel 2: cumulative press ─────────────────────────────────── */}
      <figure className="m-0 mt-8">
        <figcaption className="flex items-baseline justify-between gap-4 border-b border-[var(--rule)] pb-2">
          <span className="font-mono text-[0.5rem] uppercase tracking-[0.18em] text-[var(--text-faint)]">
            Press mentions, cumulative
          </span>
          <span className="font-mono text-[0.625rem] text-[var(--accent-text)]">
            {series[at].press}
          </span>
        </figcaption>

        <div className="relative mt-4 h-28" aria-hidden="true">
          {[0, 50, 100].map((t) => (
            <span
              key={t}
              className="absolute inset-x-0 h-px bg-[var(--rule)]"
              style={{ top: `${t}%` }}
            />
          ))}

          <svg
            className="absolute inset-0 h-full w-full"
            viewBox="0 0 100 100"
            preserveAspectRatio="none"
            fill="none"
          >
            <polyline
              points={series.map((d, i) => `${x(i)},${pressY(d.press)}`).join(' ')}
              stroke="var(--accent-text)"
              strokeWidth={2}
              strokeLinejoin="round"
              strokeLinecap="round"
              vectorEffect="non-scaling-stroke"
            />
          </svg>

          {/* Markers are HTML so they stay circular under the stretched
              viewBox and stay 9px regardless of container width. */}
          {series.map((d, i) => (
            <span
              key={d.week}
              className={cn(
                'absolute block h-[9px] w-[9px] -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-[var(--bg)] transition-colors',
                hover === i ? 'bg-[var(--accent-fill)]' : 'bg-[var(--accent-text)]',
              )}
              style={{ left: `${x(i)}%`, top: `${pressY(d.press)}%` }}
            />
          ))}
        </div>
      </figure>

      <div className="mt-3 flex justify-between border-t border-[var(--rule)] pt-2" aria-hidden="true">
        {series.map((d, i) => (
          <span
            key={d.week}
            className={cn(
              'font-mono text-[0.5rem] tracking-[0.1em] transition-colors',
              hover === i ? 'text-[var(--accent-text)]' : 'text-[var(--text-faint)]',
            )}
          >
            {d.week}
          </span>
        ))}
      </div>

      <button
        type="button"
        onClick={() => setShowTable((v) => !v)}
        aria-expanded={showTable}
        aria-controls={tableId}
        className="mt-5 border border-[var(--rule-strong)] px-3 py-1.5 font-mono text-[0.5rem] uppercase tracking-[0.16em] text-[var(--text-muted)] transition-colors hover:border-[var(--accent-line)] hover:text-[var(--text)]"
      >
        {showTable ? 'Hide the figures' : 'Show the figures'}
      </button>

      <div id={tableId} hidden={!showTable} className="ak-disclosure mt-4">
        <table className="w-full border-collapse text-left">
          <caption className="sr-only">
            Weekly reach and cumulative press mentions over eight weeks
          </caption>
          <thead>
            <tr className="border-b border-[var(--rule-strong)]">
              {['Week', 'Reach', 'Press'].map((h) => (
                <th
                  key={h}
                  scope="col"
                  className="py-2 font-mono text-[0.5rem] font-normal uppercase tracking-[0.16em] text-[var(--text-faint)]"
                >
                  {h}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {series.map((d) => (
              <tr key={d.week} className="border-b border-[var(--rule)]">
                <th
                  scope="row"
                  className="py-2 font-mono text-[0.625rem] font-normal text-[var(--text-muted)]"
                >
                  {d.week}
                </th>
                <td className="py-2 font-mono text-[0.625rem] text-[var(--text)]">
                  {d.reach.toLocaleString('en-GB')}
                </td>
                <td className="py-2 font-mono text-[0.625rem] text-[var(--accent-text)]">{d.press}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  )
}
