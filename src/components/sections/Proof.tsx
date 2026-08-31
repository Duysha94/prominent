import { Counter } from '../primitives/Counter'
import { Reveal } from '../primitives/Reveal'
import { SectionHead } from '../primitives/SectionHead'
import { CREDENTIALS } from '../../data/studio'

/**
 * THE READOUT — numbers as the image.
 *
 * Creative agencies almost never publish figures; performance agencies publish
 * figures that are obviously cherry-picked. The gap is a studio that shows the
 * numbers *with their conditions attached* — the window, the basis, the caveat
 * — in the same technical register as the rest of the site.
 *
 * Set in Martian Mono, whose width axis animates on scroll: the numbers
 * literally widen as you read them. Tabular figures throughout so nothing
 * shuffles while a counter runs.
 */
const ROWS = [
  { label: 'Blended ROAS', value: 4.8, decimals: 1, suffix: '×', basis: 'Trailing 90d, all paid, 6 accounts' },
  { label: 'Median LCP', value: 0.9, decimals: 1, suffix: 's', basis: 'Field data, p75, mobile' },
  { label: 'AOV lift', value: 38, decimals: 0, prefix: '+', suffix: '%', basis: 'Pre/post, 12 weeks, 4 brands' },
  { label: 'Brands since 2017', value: 40, decimals: 0, suffix: '+', basis: 'Advisory, build or media' },
]

export function Proof() {
  return (
    <section className="border-y border-[var(--rule)] px-5 py-20 md:px-10 md:py-28">
      <div className="mx-auto max-w-[1440px]">
        <SectionHead
          folio="03"
          eyebrow="The readout"
          title="Numbers, with their conditions attached."
          lead="Every figure below carries its basis. A ROAS without a window and a channel mix is a decoration, and we would rather show you a smaller number you can check."
        />

        <dl className="mt-14 grid gap-px border border-[var(--rule)] bg-[var(--rule)] sm:grid-cols-2 lg:grid-cols-4">
          {ROWS.map((row, i) => (
            <Reveal key={row.label} delay={i * 0.06}>
              <div className="h-full bg-[var(--bg)] p-6">
                <dt className="font-mono text-[0.5rem] uppercase tracking-[0.2em] text-[var(--text-faint)]">
                  {row.label}
                </dt>
                <dd className="ak-vf-num mt-4 font-mono text-[clamp(2rem,4.5vw,3.25rem)] leading-none text-[var(--text)]">
                  <Counter
                    to={row.value}
                    decimals={row.decimals}
                    prefix={row.prefix}
                    suffix={row.suffix}
                  />
                </dd>
                <p className="mt-4 border-t border-[var(--rule)] pt-3 font-mono text-[0.5rem] uppercase leading-relaxed tracking-[0.12em] text-[var(--text-faint)]">
                  {row.basis}
                </p>
              </div>
            </Reveal>
          ))}
        </dl>

        {/* Credentials sit with the numbers, not in a separate trust band —
            the badge and the figure answer the same objection. */}
        <ul className="mt-10 flex flex-wrap gap-x-8 gap-y-3">
          {CREDENTIALS.map((c) => (
            <li key={c.label} className="flex items-baseline gap-2">
              <span
                aria-hidden="true"
                className="h-1.5 w-1.5 shrink-0 bg-[var(--accent-fill)]"
              />
              <span className="font-mono text-[0.5625rem] uppercase tracking-[0.14em] text-[var(--text)]">
                {c.label}
              </span>
              <span className="font-mono text-[0.5rem] uppercase tracking-[0.14em] text-[var(--text-faint)]">
                {c.meta}
              </span>
            </li>
          ))}
        </ul>
      </div>
    </section>
  )
}
