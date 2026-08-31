import { Link } from 'react-router-dom'
import { useEffect, useState } from 'react'
import { CutText } from '../primitives/CutText'
import { Marquee } from '../primitives/Marquee'
import { Magnetic } from '../primitives/Magnetic'
import { STUDIO, LIVE } from '../../data/studio'

/**
 * THE CARE LABEL — the footer.
 *
 * Every garment carries a sewn-in label: what it is made of, who made it,
 * where, and how to look after it. That is exactly the content a footer holds,
 * so the footer *is* the label. It keeps the theme's structural slots (closing
 * CTA, link columns, legal bar) but the middle block is set as care copy, and
 * the availability state is live rather than a decorative green dot.
 */
export function Footer() {
  const [time, setTime] = useState('')

  useEffect(() => {
    const tick = () =>
      setTime(
        new Intl.DateTimeFormat('en-GB', {
          hour: '2-digit',
          minute: '2-digit',
          timeZone: STUDIO.timeZone,
        }).format(new Date()),
      )
    tick()
    const id = window.setInterval(tick, 30_000)
    return () => clearInterval(id)
  }, [])

  return (
    <footer className="relative z-10 border-t border-[var(--rule)] bg-[var(--bg-sunk)]">
      {/* Closing CTA */}
      <div className="mx-auto max-w-[1440px] px-5 pb-16 pt-20 md:px-10 md:pb-20 md:pt-28">
        <p className="font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-muted)]">
          Next intake — {STUDIO.nextIntake}
        </p>
        <CutText
          as="h2"
          className="mt-6 max-w-[16ch] font-display text-[clamp(2.5rem,8vw,6.5rem)] italic leading-[0.92] tracking-[-0.02em] text-[var(--text)]"
        >
          Let us take your measurements.
        </CutText>

        <div className="mt-10 flex flex-wrap items-center gap-4">
          <Magnetic>
            <Link
              to="/contact"
              className="bg-[var(--accent-fill)] px-7 py-4 font-mono text-[0.625rem] uppercase tracking-[0.2em] text-[var(--accent-on)]"
            >
              Start a brief
            </Link>
          </Magnetic>
          <a
            href={`mailto:${STUDIO.email}`}
            className="border border-[var(--rule-strong)] px-7 py-4 font-mono text-[0.625rem] uppercase tracking-[0.2em] text-[var(--text)] transition-colors hover:border-[var(--accent-line)] hover:text-[var(--accent-text)]"
          >
            {STUDIO.email}
          </a>
        </div>
      </div>

      {/* Not a logo strip and not service words — the studio's live figures.
          A band that moves should be telling you something that moves. */}
      <Marquee
        className="border-y border-[var(--rule)] py-4"
        speed={44}
        label="Live studio figures"
      >
        {LIVE.map((item) => (
          <span key={item.key} className="flex items-baseline gap-3 px-8 whitespace-nowrap">
            <span className="font-mono text-[0.5rem] uppercase tracking-[0.18em] text-[var(--text-faint)]">
              {item.key}
            </span>
            <span className="font-mono text-[clamp(0.875rem,1.6vw,1.25rem)] text-[var(--accent-text)]">
              {item.value}
            </span>
          </span>
        ))}
      </Marquee>

      {/* The care label proper */}
      <div className="mx-auto grid max-w-[1440px] gap-10 px-5 py-14 md:grid-cols-[1.2fr_1fr_1fr_1fr] md:px-10">
        <div>
          <p className="font-mono text-[0.5rem] uppercase tracking-[0.22em] text-[var(--text-faint)]">
            Care instructions
          </p>
          <ul className="mt-4 space-y-2 font-mono text-[0.625rem] uppercase leading-relaxed tracking-[0.06em] text-[var(--text-muted)]">
            {STUDIO.care.map((line) => (
              <li key={line} className="flex gap-2">
                <span aria-hidden="true" className="text-[var(--accent-text)]">
                  —
                </span>
                {line}
              </li>
            ))}
          </ul>
        </div>

        {STUDIO.footerColumns.map((col) => (
          <div key={col.title}>
            <p className="font-mono text-[0.5rem] uppercase tracking-[0.22em] text-[var(--text-faint)]">
              {col.title}
            </p>
            <ul className="mt-4 space-y-2">
              {col.links.map((l) => (
                <li key={l.label}>
                  {l.to.startsWith('http') || l.to.startsWith('mailto') ? (
                    <a
                      href={l.to}
                      className="text-[0.8125rem] text-[var(--text-muted)] transition-colors hover:text-[var(--accent-text)]"
                      {...(l.to.startsWith('http') ? { rel: 'noopener noreferrer', target: '_blank' } : {})}
                    >
                      {l.label}
                    </a>
                  ) : (
                    <Link
                      to={l.to}
                      className="text-[0.8125rem] text-[var(--text-muted)] transition-colors hover:text-[var(--accent-text)]"
                    >
                      {l.label}
                    </Link>
                  )}
                </li>
              ))}
            </ul>
          </div>
        ))}
      </div>

      {/* Legal bar */}
      <div className="border-t border-[var(--rule)]">
        <div className="mx-auto flex max-w-[1440px] flex-col gap-3 px-5 py-5 font-mono text-[0.5625rem] uppercase tracking-[0.16em] text-[var(--text-faint)] md:flex-row md:items-center md:justify-between md:px-10">
          <span>
            © {new Date().getFullYear()} {STUDIO.legalName}
          </span>
          <span className="flex items-center gap-2">
            <span
              className="inline-block h-1.5 w-1.5 rounded-full bg-[var(--accent-fill)]"
              aria-hidden="true"
            />
            {STUDIO.city} — {time} local — {STUDIO.availability}
          </span>
        </div>
      </div>
    </footer>
  )
}
