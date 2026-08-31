/**
 * One drawing idea, executed four times.
 *
 * Each movement gets a technical drawing in the same hand as the Measure
 * Frame: construction lines, one accent stroke, no decoration. The point is
 * that the four are visibly the same *kind* of object — a mark, a grid, a
 * runway and a screen, each under construction — so four services read as one
 * method rather than as a price list.
 *
 * They draw themselves on a CSS view() timeline: no JavaScript, and the
 * finished state is what renders wherever scroll timelines or motion are
 * unavailable.
 */
export function MovementDiagram({
  movement,
}: {
  movement: 'strategy' | 'identity' | 'production' | 'presence'
}) {
  // pathLength normalises every accent path to 1, so a single keyframe draws
  // all of them regardless of geometry.
  const ink = {
    fill: 'none',
    stroke: 'var(--accent-line)',
    strokeWidth: 1.25,
    vectorEffect: 'non-scaling-stroke' as const,
    pathLength: 1,
  }
  const guide = {
    fill: 'none',
    stroke: 'var(--rule-strong)',
    strokeWidth: 1,
    vectorEffect: 'non-scaling-stroke' as const,
  }

  return (
    <svg viewBox="0 0 240 200" className="ak-diagram h-auto w-full max-w-[16rem]" aria-hidden="true">
      {movement === 'strategy' && (
        <>
          {/* A mark being constructed from its own guides. */}
          <circle cx="120" cy="96" r="72" {...guide} strokeDasharray="3 4" />
          <line x1="120" y1="16" x2="120" y2="176" {...guide} strokeDasharray="3 4" />
          <line x1="40" y1="96" x2="200" y2="96" {...guide} strokeDasharray="3 4" />
          <path className="ak-stroke" d="M84 140 L120 44 L156 140" {...ink} />
          <path className="ak-stroke" d="M99 108 L141 108" {...ink} />
        </>
      )}

      {movement === 'identity' && (
        <>
          {/* A type specimen on a baseline grid. */}
          {[52, 84, 116, 148].map((y) => (
            <line key={y} x1="34" y1={y} x2="206" y2={y} {...guide} strokeDasharray="2 5" />
          ))}
          <line x1="34" y1="30" x2="34" y2="170" {...guide} />
          <line x1="206" y1="30" x2="206" y2="170" {...guide} />
          <path className="ak-stroke" d="M52 148 L52 52 L120 52" {...ink} />
          <path className="ak-stroke" d="M52 100 L104 100" {...ink} />
          <path className="ak-stroke" d="M140 148 L140 84 L188 84 L188 148" {...ink} />
        </>
      )}

      {movement === 'production' && (
        <>
          {/* A runway seen in plan: the walk, the seats, the exit. */}
          <rect x="96" y="24" width="48" height="152" {...guide} />
          {[40, 64, 88, 112, 136, 160].map((y) => (
            <g key={y}>
              <line x1="44" y1={y} x2="88" y2={y} {...guide} strokeDasharray="2 4" />
              <line x1="152" y1={y} x2="196" y2={y} {...guide} strokeDasharray="2 4" />
            </g>
          ))}
          <path className="ak-stroke" d="M120 176 L120 24" {...ink} />
          <path className="ak-stroke" d="M96 24 L144 24" {...ink} />
          <circle className="ak-stroke" cx="120" cy="44" r="7" {...ink} />
        </>
      )}

      {movement === 'presence' && (
        <>
          {/* A screen: frame, column guides, one live element. */}
          <rect x="34" y="30" width="172" height="130" {...guide} />
          <line x1="34" y1="48" x2="206" y2="48" {...guide} />
          <circle cx="44" cy="39" r="2.5" {...guide} />
          <circle cx="53" cy="39" r="2.5" {...guide} />
          {[70, 104, 138, 172].map((x) => (
            <line key={x} x1={x} y1="48" x2={x} y2="160" {...guide} strokeDasharray="2 5" />
          ))}
          <path className="ak-stroke" d="M50 68 L120 68" {...ink} />
          <rect className="ak-stroke" x="50" y="84" width="86" height="58" {...ink} />
        </>
      )}
    </svg>
  )
}
