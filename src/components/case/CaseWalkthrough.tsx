import { Reveal } from '../primitives/Reveal'
import { Plate } from '../primitives/Plate'
import type { CaseStudy } from '../../data/work'

/**
 * THE WALKTHROUGH — the template for build-led work.
 *
 * A shipped store is not a picture; it is something you move through at three
 * sizes. This template shows it at real device proportions rather than as a
 * flat screenshot on a gradient, and puts the field measurements next to it —
 * because on a build, "it looks good" and "it is fast" are the same claim.
 */
export function CaseWalkthrough({ study }: { study: CaseStudy }) {
  const DEVICES = [
    { label: 'Desktop — 1440', ratio: 'aspect-[16/10]', span: 'md:col-span-8' },
    { label: 'Tablet — 834', ratio: 'aspect-[3/4]', span: 'md:col-span-4' },
    { label: 'Phone — 390', ratio: 'aspect-[9/19]', span: 'md:col-span-3' },
    { label: 'Checkout — 390', ratio: 'aspect-[9/19]', span: 'md:col-span-3' },
    { label: 'Product — 1440', ratio: 'aspect-[16/9]', span: 'md:col-span-6' },
  ]

  return (
    <div className="mx-auto max-w-[1440px] px-5 md:px-10">
      <section className="border-t border-[var(--rule)] pt-12">
        <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-faint)]">
          The store, at the sizes people use it
        </h2>

        <div className="mt-8 grid grid-cols-2 gap-5 md:grid-cols-12">
          {DEVICES.map((d, i) => (
            <Reveal key={d.label} delay={i * 0.05} className={d.span}>
              <figure className="m-0">
                <Plate
                  seed={`${study.slug}-${d.label}`}
                  tint={study.tint}
                  className={`${d.ratio} w-full overflow-hidden border border-[var(--rule)]`}
                />
                <figcaption className="mt-2 font-mono text-[0.5rem] uppercase tracking-[0.16em] text-[var(--text-faint)]">
                  {d.label}
                </figcaption>
              </figure>
            </Reveal>
          ))}
        </div>
      </section>

      {/* Before/after, stated as a table because that is what it is. */}
      <section className="mt-16 border-t border-[var(--rule)] pt-12">
        <h2 className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-faint)]">
          Measured on the shipped store
        </h2>
        <table className="mt-6 w-full max-w-[42rem] border-collapse text-left">
          <thead>
            <tr className="border-b border-[var(--rule-strong)]">
              {['', 'Before', 'After'].map((h, i) => (
                <th
                  key={i}
                  scope="col"
                  className="py-2 font-mono text-[0.5rem] font-normal uppercase tracking-[0.16em] text-[var(--text-faint)]"
                >
                  {h}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {(study.vitals ?? []).map((v) => (
              <tr key={v.key} className="border-b border-[var(--rule)]">
                <th
                  scope="row"
                  className="py-3 font-mono text-[0.625rem] font-normal uppercase tracking-[0.14em] text-[var(--text-muted)]"
                >
                  {v.key}
                </th>
                <td className="py-3 font-mono text-[clamp(0.875rem,1.6vw,1.125rem)] text-[var(--text-faint)] line-through">
                  {v.before}
                </td>
                <td className="py-3 font-mono text-[clamp(0.875rem,1.6vw,1.125rem)] text-[var(--accent-text)]">
                  {v.after}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        <p className="mt-5 max-w-[54ch] text-[0.75rem] leading-relaxed text-[var(--text-muted)]">
          Field data, 75th percentile, mobile. Lab numbers on a developer laptop are not a
          performance claim.
        </p>
      </section>
    </div>
  )
}
