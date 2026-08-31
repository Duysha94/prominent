/**
 * PLATE — the image system, drawn rather than photographed.
 *
 * There is no photography in this prototype, and two of the obvious ways to
 * fill the gap are worse than nothing: stock fashion imagery says something
 * false about the work, and AI-generated imagery is now an active liability
 * for a creative studio — the 2026 backlash is about intent, not aesthetics,
 * and a generated hero loses the pitch in the first three seconds.
 *
 * So the plates are duotone halftones: ink ground, brand orange screen, in the
 * two brand colours and nothing else. The screen angle, dot pitch, tonal
 * gradient and the geometric form are all derived from a hash of the seed, so
 * every plate is stable, distinct and unmistakably *printed* rather than
 * photographed. It reads as a designed placeholder, which is honest, instead
 * of as a failed image.
 *
 * In production these are replaced by commissioned photography put through the
 * same duotone halftone treatment — the component boundary is what survives,
 * and the art direction stays identical.
 */
const hash = (s: string) => {
  let h = 2166136261
  for (let i = 0; i < s.length; i++) {
    h ^= s.charCodeAt(i)
    h = Math.imul(h, 16777619)
  }
  return Math.abs(h)
}

type Form = 'disc' | 'arc' | 'band' | 'column' | 'corner'
const FORMS: Form[] = ['disc', 'arc', 'band', 'column', 'corner']

export function Plate({
  seed,
  tint,
  className,
  label,
}: {
  seed: string
  /** Retained for call-site compatibility; nudges the screen angle only. */
  tint?: number
  className?: string
  label?: string
}) {
  const h = hash(seed)
  const angle = ((h % 4) * 15 + 15 + (tint ?? 0) * 0.05) % 90 // 15/30/45/60-ish
  const pitch = 6 + (h % 3) // dot pitch in viewBox units
  const form = FORMS[h % FORMS.length]
  const flip = (h >> 7) % 2 === 0
  const id = `p-${seed.replace(/[^a-z0-9]/gi, '')}`

  const ink = 'var(--color-ink-950)'
  const accent = 'var(--color-brand-500)'

  return (
    <div className={className} aria-hidden="true">
      <svg
        width="100%"
        height="100%"
        preserveAspectRatio="xMidYMid slice"
        viewBox="0 0 600 390"
        role="presentation"
      >
        <defs>
          {/* Two screens at the same angle but different dot sizes. Crossed
              with opposing gradient masks they read as one continuous tonal
              ramp — the way a real halftone gets its midtones. */}
          <pattern
            id={`${id}-fine`}
            width={pitch}
            height={pitch}
            patternUnits="userSpaceOnUse"
            patternTransform={`rotate(${angle})`}
          >
            <circle cx={pitch / 2} cy={pitch / 2} r={pitch * 0.13} fill={accent} />
          </pattern>
          <pattern
            id={`${id}-coarse`}
            width={pitch * 1.9}
            height={pitch * 1.9}
            patternUnits="userSpaceOnUse"
            patternTransform={`rotate(${angle})`}
          >
            <circle cx={pitch * 0.95} cy={pitch * 0.95} r={pitch * 0.4} fill={accent} />
          </pattern>

          <pattern
            id={`${id}-ink-screen`}
            width={pitch * 1.9}
            height={pitch * 1.9}
            patternUnits="userSpaceOnUse"
            patternTransform={`rotate(${angle + 45})`}
          >
            <circle cx={pitch * 0.95} cy={pitch * 0.95} r={pitch * 0.36} fill={ink} />
          </pattern>

          <linearGradient id={`${id}-ramp`} x1="0" y1={flip ? '1' : '0'} x2="1" y2={flip ? '0' : '1'}>
            <stop offset="0%" stopColor="#fff" stopOpacity="1" />
            <stop offset="100%" stopColor="#fff" stopOpacity="0" />
          </linearGradient>
          <linearGradient id={`${id}-ramp-inv`} x1="0" y1={flip ? '1' : '0'} x2="1" y2={flip ? '0' : '1'}>
            <stop offset="0%" stopColor="#fff" stopOpacity="0" />
            <stop offset="60%" stopColor="#fff" stopOpacity="0.85" />
          </linearGradient>

          <mask id={`${id}-m1`}>
            <rect width="600" height="390" fill={`url(#${id}-ramp)`} />
          </mask>
          <mask id={`${id}-m2`}>
            <rect width="600" height="390" fill={`url(#${id}-ramp-inv)`} />
          </mask>

          {/* The form is what makes each plate a different picture rather than
              a different texture. */}
          <clipPath id={`${id}-form`}>
            {form === 'disc' && <circle cx={flip ? 210 : 390} cy={195} r={108} />}
            {form === 'arc' && <path d="M90 390 A 210 210 0 0 1 510 390 Z" />}
            {form === 'band' && <rect x="0" y="156" width="600" height="86" />}
            {form === 'column' && <rect x={flip ? 66 : 384} y="0" width="150" height="390" />}
            {form === 'corner' && <path d="M600 0 L600 390 L210 390 Z" />}
          </clipPath>
        </defs>

        <rect width="600" height="390" fill={ink} />
        <rect width="600" height="390" fill={`url(#${id}-coarse)`} mask={`url(#${id}-m2)`} />
        <rect width="600" height="390" fill={`url(#${id}-fine)`} mask={`url(#${id}-m1)`} />

        {/* Inside the form the screen inverts to solid — the highlight. */}
        <g clipPath={`url(#${id}-form)`}>
          <rect width="600" height="390" fill={accent} opacity="0.9" />
          {/* Ink screen over the highlight: a duotone's light tone still
              carries dot structure, and a flat accent field would spend the
              accent's impact on every plate at once. */}
          <rect width="600" height="390" fill={`url(#${id}-ink-screen)`} opacity="0.7" />
        </g>

        {/* The seam, present on every plate. */}
        <path
          d={`M0 ${70 + (h % 250)} Q 300 ${30 + ((h >> 5) % 330)} 600 ${90 + ((h >> 9) % 220)}`}
          stroke={ink}
          strokeWidth="2.5"
          strokeDasharray="9 7"
          fill="none"
          opacity="0.55"
        />

        {label && (
          <text
            x="18"
            y="372"
            fill={accent}
            fontFamily="var(--font-mono)"
            fontSize="11"
            letterSpacing="2.5"
          >
            {label.toUpperCase()}
          </text>
        )}
      </svg>
    </div>
  )
}
