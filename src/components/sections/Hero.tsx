import { Link } from 'react-router-dom'
import { motion, useScroll, useTransform } from 'motion/react'
import { useRef } from 'react'
import { CutText } from '../primitives/CutText'
import { MeasureFrame } from '../primitives/MeasureFrame'
import { Magnetic } from '../primitives/Magnetic'
import { Plate } from '../primitives/Plate'
import { useHls } from '../../lib/useHls'
import { useReducedMotion } from '../../lib/useReducedMotion'
import { EASE } from '../../lib/motion'
import { STUDIO } from '../../data/studio'
import { LANES } from '../../data/services'

/** PLACEHOLDER ASSET — swap for the studio's own footage before launch. */
const SWATCH_SRC = 'https://stream.mux.com/Aa02T7oM1wH5Mk5EEVDYhbZ1ChcdhRsS2m1NYyx4Ua1g.m3u8'

/**
 * THE SWATCH — the hero.
 *
 * The brief asked for a full-bleed HLS video hero. Full-bleed muted video
 * behind centred type is the single most-shipped hero of the last three years,
 * it fights every headline placed on it, and it makes the largest element on
 * the page the slowest one. So the video stays and the *crop* changes: it
 * becomes a swatch — a narrow measured band of material, annotated like a
 * fabric sample, sitting under a headline that owns its own space.
 *
 * This also fixes the performance problem by construction. The LCP element is
 * the headline (text, instant), not a video, and the band is short enough that
 * a poster frame reads as finished on its own.
 */
export function Hero() {
  const section = useRef<HTMLElement>(null)
  const reduced = useReducedMotion()
  const { ref: videoRef } = useHls(SWATCH_SRC, !reduced)

  const { scrollYProgress } = useScroll({
    target: section,
    offset: ['start start', 'end start'],
  })
  // The swatch drifts at a different rate to the type — depth without parallax
  // theatre. 8% of travel, not 40%.
  const swatchY = useTransform(scrollYProgress, [0, 1], ['0%', reduced ? '0%' : '8%'])

  return (
    <section ref={section} className="relative px-5 pb-20 pt-32 md:px-10 md:pb-28 md:pt-40">
      <div className="mx-auto max-w-[1440px]">
        <motion.p
          className="flex flex-wrap items-center gap-x-4 gap-y-1 font-mono text-[0.5625rem] uppercase tracking-[0.22em] text-[var(--text-muted)]"
          initial={reduced ? false : { opacity: 0, y: 10 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.8, ease: EASE.cut, delay: 0.15 }}
        >
          <span className="text-[var(--accent-text)]">{STUDIO.discipline}</span>
          <span aria-hidden="true" className="h-px w-8 bg-[var(--rule-strong)]" />
          <span>Est. {STUDIO.founded}</span>
          <span aria-hidden="true" className="h-px w-8 bg-[var(--rule-strong)]" />
          <span>{STUDIO.city}</span>
        </motion.p>

        <CutText
          as="h1"
          trigger="mount"
          delay={0.25}
          className="mt-8 max-w-[13ch] font-display text-[clamp(3rem,11vw,10rem)] italic leading-[0.88] tracking-[-0.03em] text-[var(--text)]"
        >
          The measure of a brand.
        </CutText>

        <div className="mt-10 grid gap-8 md:grid-cols-[1fr_auto] md:items-end">
          <motion.p
            className="max-w-[46ch] text-[clamp(0.9375rem,1.4vw,1.125rem)] leading-relaxed text-[var(--text-muted)]"
            initial={reduced ? false : { opacity: 0, y: 14 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.9, ease: EASE.cut, delay: 0.6 }}
          >
            {STUDIO.promise}
          </motion.p>

          <motion.div
            className="flex flex-wrap gap-3"
            initial={reduced ? false : { opacity: 0, y: 14 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.9, ease: EASE.cut, delay: 0.72 }}
          >
            <Magnetic>
              <Link
                to="/work"
                className="bg-[var(--accent-fill)] px-6 py-3.5 font-mono text-[0.625rem] uppercase tracking-[0.2em] text-[var(--accent-on)]"
              >
                See the work
              </Link>
            </Magnetic>
            <Link
              to="/contact"
              className="border border-[var(--rule-strong)] px-6 py-3.5 font-mono text-[0.625rem] uppercase tracking-[0.2em] text-[var(--text)] transition-colors hover:border-[var(--accent-line)] hover:text-[var(--accent-text)]"
            >
              Start a brief
            </Link>
          </motion.div>
        </div>

        {/* The swatch itself. */}
        <motion.div
          style={{ y: swatchY }}
          className="mt-16 md:mt-20"
          initial={reduced ? false : { opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 1.2, ease: EASE.drape, delay: 0.5 }}
        >
          <MeasureFrame
            always
            label="Material — SS26"
            measures={[
              { key: 'LANES', value: '03', edge: 'right', at: 0.5 },
              { key: 'BRANDS', value: '40+', edge: 'left', at: 0.5 },
            ]}
          >
            <div className="relative aspect-[16/5] w-full overflow-hidden bg-[var(--bg-sunk)] md:aspect-[21/5]">
              {/* The woven plate is the poster frame. It is drawn, not loaded,
                  so the swatch is never an empty black box — on a blocked
                  stream, a Save-Data connection or reduced motion, this is
                  simply what the swatch is. */}
              <Plate
                seed="hero-swatch"
                tint={44}
                className="absolute inset-0 h-full w-full"
              />
              <video
                ref={videoRef}
                className="relative h-full w-full object-cover"
                autoPlay
                muted
                loop
                playsInline
                preload="none"
                aria-hidden="true"
                tabIndex={-1}
              />
              {/* Halftone, at a scale that reads as print screen rather than
                  as a generic "texture overlay". */}
              <div
                aria-hidden="true"
                className="pointer-events-none absolute inset-0 opacity-25 mix-blend-multiply"
                style={{
                  backgroundImage:
                    'radial-gradient(circle, var(--color-ink-950) 0.5px, transparent 0.5px)',
                  backgroundSize: '3px 3px',
                }}
              />
            </div>
          </MeasureFrame>
        </motion.div>

        {/* The three lanes, stated immediately — the site's argument up front. */}
        <ul className="mt-14 grid gap-px border border-[var(--rule)] bg-[var(--rule)] sm:grid-cols-3">
          {LANES.map((lane, i) => (
            <motion.li
              key={lane.id}
              className="bg-[var(--bg)]"
              initial={reduced ? false : { opacity: 0, y: 16 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{
                duration: 0.8,
                ease: EASE.cut,
                delay: 0.85 + i * 0.08,
              }}
            >
              <Link to={`/services#${lane.id}`} className="group block p-6">
                <span className="flex items-baseline gap-2 font-mono text-[0.5rem] uppercase tracking-[0.2em] text-[var(--text-faint)]">
                  {lane.index}
                  <span className="h-px flex-1 bg-[var(--rule)] transition-colors group-hover:bg-[var(--accent-line)]" />
                </span>
                <h2 className="mt-3 font-display text-2xl italic text-[var(--text)] transition-colors group-hover:text-[var(--accent-text)]">
                  {lane.title}
                </h2>
                <p className="mt-2 text-[0.8125rem] leading-relaxed text-[var(--text-muted)]">
                  {lane.claim}
                </p>
              </Link>
            </motion.li>
          ))}
        </ul>
      </div>
    </section>
  )
}
