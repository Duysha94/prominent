import { useMemo, useState, type FormEvent } from 'react'
import { CutText } from '../components/primitives/CutText'
import { STUDIO } from '../data/studio'
import { LANES } from '../data/services'
import { usePageMeta } from '../lib/usePageMeta'
import { cn } from '../lib/cn'

/**
 * THE BRIEF — a measuring instrument, not a contact form.
 *
 * A "tell us about your project" textarea puts the whole cost of the exchange
 * on the visitor and gives them nothing back until someone replies. This does
 * the opposite: four questions a founder can answer without thinking, and a
 * spec sheet that recalculates live — which lanes they need, a realistic
 * shape of engagement, and what we would need from them to start.
 *
 * The visitor leaves with something even if they never send it, which is the
 * whole argument of the studio performed rather than claimed.
 *
 * Accessibility notes: every control is a real input inside a labelled
 * fieldset, the readout is a polite live region so the recalculation is
 * announced rather than silently redrawn, and nothing here depends on hover.
 */

type Stage = 'launching' | 'trading' | 'scaling'

const STAGES: { id: Stage; label: string; note: string }[] = [
  { id: 'launching', label: 'Launching', note: 'No collection out yet, or a first drop' },
  { id: 'trading', label: 'Trading', note: 'Selling, but growth is flat or unpredictable' },
  { id: 'scaling', label: 'Scaling', note: 'Growing, and the seams are starting to show' },
]

const SPEND = [
  { id: 0, label: 'None yet' },
  { id: 1, label: 'Under £5k / mo' },
  { id: 2, label: '£5k – £25k / mo' },
  { id: 3, label: '£25k+ / mo' },
]

export function Contact() {
  usePageMeta(
    'Start a brief — AK Brand Development Studio',
    'Four questions, and a spec sheet back. Work out which lanes you need before you talk to anyone.',
  )

  const [stage, setStage] = useState<Stage>('trading')
  const [wants, setWants] = useState<string[]>(['build'])
  const [spend, setSpend] = useState(1)
  const [sent, setSent] = useState(false)

  /** The recommendation. Deliberately readable logic, not a black box. */
  const spec = useMemo(() => {
    const lanes = new Set(wants)

    // A brand that cannot say what it is will waste whatever it spends, so
    // advisory is non-negotiable before media at the earliest stage.
    if (stage === 'launching') lanes.add('advise')
    // Buying traffic to a store nobody has audited is the commonest way to
    // burn a budget — if they want media, the build has to be in scope.
    if (lanes.has('grow') && spend >= 2) lanes.add('build')
    // At scale with no advisory, the constraint is almost never the media.
    if (stage === 'scaling' && lanes.has('grow')) lanes.add('advise')

    const ordered = LANES.filter((l) => lanes.has(l.id))
    const weeks = ordered.reduce(
      (n, l) => n + (l.id === 'advise' ? 6 : l.id === 'build' ? 10 : 0),
      0,
    )

    return {
      lanes: ordered,
      shape: weeks
        ? `${weeks}–${weeks + 4} weeks, then ongoing`
        : 'Ongoing, minimum three months',
      needs:
        stage === 'launching'
          ? ['Your range plan or line sheet', 'Whatever the brand already looks like', 'Your target landed cost']
          : [
              'Read access to your ad accounts',
              'Last 12 months of revenue by channel',
              'Your current range, by SKU and margin',
            ],
    }
  }, [stage, wants, spend])

  const toggleWant = (id: string) =>
    setWants((w) => (w.includes(id) ? w.filter((x) => x !== id) : [...w, id]))

  const onSubmit = (e: FormEvent) => {
    e.preventDefault()
    // Prototype only — no data leaves the browser. The production build posts
    // to the studio's CRM with consent handling and server-side validation.
    setSent(true)
  }

  return (
    <div className="pt-28 md:pt-36">
      <header className="mx-auto max-w-[1440px] px-5 md:px-10">
        <p className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--accent-text)]">
          Start a brief — next intake {STUDIO.nextIntake}
        </p>
        <CutText
          as="h1"
          trigger="mount"
          delay={0.15}
          className="mt-6 max-w-[15ch] font-display text-[clamp(2.5rem,8vw,6rem)] italic leading-[0.9] tracking-[-0.03em] text-[var(--text)]"
        >
          Four questions. A spec sheet back.
        </CutText>
        <p className="mt-8 max-w-[54ch] text-[clamp(0.9375rem,1.5vw,1.125rem)] leading-relaxed text-[var(--text-muted)]">
          Answer these and the panel on the right works out which lanes you actually need. Nothing
          is sent until you choose to send it.
        </p>
      </header>

      <div className="mx-auto mb-24 mt-16 grid max-w-[1440px] gap-12 px-5 md:px-10 lg:grid-cols-[1fr_24rem] lg:gap-16">
        <form onSubmit={onSubmit} className="space-y-12">
          <fieldset className="border-t border-[var(--rule)] pt-7">
            <legend className="sr-only">Where the brand is now</legend>
            <p aria-hidden="true" className="font-mono text-[0.5625rem] uppercase tracking-[0.2em] text-[var(--text-faint)]">
              01 — Where the brand is now
            </p>
            <div className="mt-5 grid gap-px bg-[var(--rule)] sm:grid-cols-3">
              {STAGES.map((s) => (
                <label
                  key={s.id}
                  /* Both branches must supply their own background. Listing a
                     base `bg-*` and then a conditional `bg-*` lets stylesheet
                     order decide the winner, not the class string — which is
                     how the selected card ended up with ink text on ink. */
                  className={cn(
                    'cursor-pointer p-5 transition-colors',
                    stage === s.id
                      ? 'bg-[var(--accent-fill)] text-[var(--accent-on)]'
                      : 'bg-[var(--bg)] text-[var(--text)]',
                  )}
                >
                  <input
                    type="radio"
                    name="stage"
                    value={s.id}
                    checked={stage === s.id}
                    onChange={() => setStage(s.id)}
                    className="sr-only"
                  />
                  <span className="block font-display text-xl italic">{s.label}</span>
                  <span
                    className={cn(
                      'mt-2 block text-[0.75rem] leading-relaxed',
                      stage === s.id ? 'opacity-75' : 'text-[var(--text-muted)]',
                    )}
                  >
                    {s.note}
                  </span>
                </label>
              ))}
            </div>
          </fieldset>

          <fieldset className="border-t border-[var(--rule)] pt-7">
            <legend className="sr-only">What you think you need</legend>
            <p aria-hidden="true" className="font-mono text-[0.5625rem] uppercase tracking-[0.2em] text-[var(--text-faint)]">
              02 — What you think you need
            </p>
            <div className="mt-5 flex flex-wrap gap-2">
              {LANES.map((l) => (
                <label
                  key={l.id}
                  className={cn(
                    'cursor-pointer border px-5 py-3 font-mono text-[0.625rem] uppercase tracking-[0.16em] transition-colors',
                    wants.includes(l.id)
                      ? 'border-transparent bg-[var(--accent-fill)] text-[var(--accent-on)]'
                      : 'border-[var(--rule-strong)] text-[var(--text-muted)] hover:border-[var(--accent-line)]',
                  )}
                >
                  <input
                    type="checkbox"
                    checked={wants.includes(l.id)}
                    onChange={() => toggleWant(l.id)}
                    className="sr-only"
                  />
                  {l.title}
                </label>
              ))}
            </div>
          </fieldset>

          <fieldset className="border-t border-[var(--rule)] pt-7">
            <legend className="sr-only">Current monthly media spend</legend>
            <p aria-hidden="true" className="font-mono text-[0.5625rem] uppercase tracking-[0.2em] text-[var(--text-faint)]">
              03 — Current monthly media spend
            </p>
            <label htmlFor="spend" className="sr-only">
              Current monthly media spend
            </label>
            <input
              id="spend"
              type="range"
              min={0}
              max={3}
              step={1}
              value={spend}
              onChange={(e) => setSpend(Number(e.target.value))}
              aria-valuetext={SPEND[spend].label}
              className="ak-range mt-6 w-full"
            />
            <div className="mt-3 flex justify-between font-mono text-[0.5rem] uppercase tracking-[0.14em] text-[var(--text-faint)]">
              {SPEND.map((s) => (
                <span key={s.id} className={cn(spend === s.id && 'text-[var(--accent-text)]')}>
                  {s.label}
                </span>
              ))}
            </div>
          </fieldset>

          <fieldset className="border-t border-[var(--rule)] pt-7">
            <legend className="sr-only">Who you are</legend>
            <p aria-hidden="true" className="font-mono text-[0.5625rem] uppercase tracking-[0.2em] text-[var(--text-faint)]">
              04 — Who you are
            </p>
            <div className="mt-5 grid gap-5 sm:grid-cols-2">
              <div>
                <label htmlFor="brand" className="block font-mono text-[0.5625rem] uppercase tracking-[0.16em] text-[var(--text-muted)]">
                  Brand
                </label>
                <input
                  id="brand"
                  name="brand"
                  required
                  autoComplete="organization"
                  className="ak-input mt-2"
                />
              </div>
              <div>
                <label htmlFor="email" className="block font-mono text-[0.5625rem] uppercase tracking-[0.16em] text-[var(--text-muted)]">
                  Email
                </label>
                <input
                  id="email"
                  name="email"
                  type="email"
                  required
                  autoComplete="email"
                  className="ak-input mt-2"
                />
              </div>
            </div>
            <div className="mt-5">
              <label htmlFor="note" className="block font-mono text-[0.5625rem] uppercase tracking-[0.16em] text-[var(--text-muted)]">
                Anything the panel got wrong
              </label>
              <textarea id="note" name="note" rows={3} className="ak-input mt-2 resize-y" />
            </div>
          </fieldset>

          <button
            type="submit"
            className="w-full bg-[var(--accent-fill)] px-7 py-4 font-mono text-[0.625rem] uppercase tracking-[0.2em] text-[var(--accent-on)] sm:w-auto"
          >
            {sent ? 'Sent — we reply within two days' : 'Send the brief'}
          </button>
          <p aria-live="polite" className="sr-only">
            {sent ? 'Brief sent.' : ''}
          </p>
        </form>

        {/* The spec sheet. */}
        <aside
          aria-live="polite"
          className="h-fit border border-[var(--rule-strong)] lg:sticky lg:top-28"
        >
          <p className="border-b border-[var(--rule)] px-5 py-3 font-mono text-[0.5rem] uppercase tracking-[0.2em] text-[var(--text-faint)]">
            Indicative spec
          </p>

          <div className="space-y-6 p-5">
            <div>
              <p className="font-mono text-[0.5rem] uppercase tracking-[0.18em] text-[var(--text-faint)]">
                Lanes
              </p>
              <ul className="mt-3 space-y-2">
                {spec.lanes.map((l) => (
                  <li key={l.id} className="flex items-baseline gap-2">
                    <span aria-hidden="true" className="h-1.5 w-1.5 shrink-0 bg-[var(--accent-fill)]" />
                    <span className="font-display text-lg italic text-[var(--text)]">{l.title}</span>
                    <span className="ml-auto font-mono text-[0.5rem] uppercase tracking-[0.14em] text-[var(--text-faint)]">
                      {wants.includes(l.id) ? 'You asked' : 'We would add'}
                    </span>
                  </li>
                ))}
                {spec.lanes.length === 0 && (
                  <li className="text-[0.8125rem] text-[var(--text-muted)]">
                    Pick at least one lane above.
                  </li>
                )}
              </ul>
            </div>

            <div className="border-t border-[var(--rule)] pt-5">
              <p className="font-mono text-[0.5rem] uppercase tracking-[0.18em] text-[var(--text-faint)]">
                Shape
              </p>
              <p className="mt-2 font-mono text-[0.875rem] text-[var(--accent-text)]">{spec.shape}</p>
            </div>

            <div className="border-t border-[var(--rule)] pt-5">
              <p className="font-mono text-[0.5rem] uppercase tracking-[0.18em] text-[var(--text-faint)]">
                What we would need from you
              </p>
              <ul className="mt-3 space-y-2">
                {spec.needs.map((n) => (
                  <li key={n} className="flex gap-2 text-[0.8125rem] leading-relaxed text-[var(--text-muted)]">
                    <span aria-hidden="true" className="text-[var(--accent-text)]">
                      —
                    </span>
                    {n}
                  </li>
                ))}
              </ul>
            </div>

            <div className="border-t border-[var(--rule)] pt-5">
              <p className="font-mono text-[0.5rem] uppercase tracking-[0.18em] text-[var(--text-faint)]">
                Next intake
              </p>
              <p className="mt-2 text-[0.8125rem] text-[var(--text)]">{STUDIO.nextIntake}</p>
            </div>
          </div>
        </aside>
      </div>
    </div>
  )
}
