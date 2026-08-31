/**
 * One drawing idea, executed three times.
 *
 * Each lane gets a technical drawing in the same hand as the Measure Frame:
 * construction lines, one accent stroke, mono annotation. The point is that
 * the three diagrams are visibly the same *kind* of object — a brand mark
 * under construction, a page under construction, a funnel under construction —
 * so the three services read as one method rather than a service list.
 *
 * They draw themselves on a CSS view() timeline: no JS, and the finished state
 * is what renders where scroll timelines or motion are unavailable.
 */
export function LaneDiagram({ lane }: { lane: 'advise' | 'build' | 'grow' }) {
  // pathLength normalises every accent path to a length of 1, so a single
  // keyframe (dashoffset 1 -> 0) draws all of them regardless of geometry.
  const common = {
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
      {lane === 'advise' && (
        <>
          {/* A mark being constructed from its own guides. */}
          <circle cx="120" cy="96" r="72" {...guide} strokeDasharray="3 4" />
          <line x1="120" y1="16" x2="120" y2="176" {...guide} strokeDasharray="3 4" />
          <line x1="40" y1="96" x2="200" y2="96" {...guide} strokeDasharray="3 4" />
          <path className="ak-stroke" d="M84 140 L120 44 L156 140" {...common} />
          <path className="ak-stroke" d="M99 108 L141 108" {...common} />
        </>
      )}

      {lane === 'build' && (
        <>
          {/* A page: frame, column guides, one live element. */}
          <rect x="34" y="30" width="172" height="130" {...guide} />
          <line x1="34" y1="48" x2="206" y2="48" {...guide} />
          <circle cx="44" cy="39" r="2.5" {...guide} />
          <circle cx="53" cy="39" r="2.5" {...guide} />
          {[70, 104, 138, 172].map((x) => (
            <line key={x} x1={x} y1="48" x2={x} y2="160" {...guide} strokeDasharray="2 5" />
          ))}
          <path className="ak-stroke" d="M50 68 L120 68" {...common} />
          <rect className="ak-stroke" x="50" y="84" width="86" height="58" {...common} />
        </>
      )}

      {lane === 'grow' && (
        <>
          {/* A funnel, annotated like a measurement rather than a marketing icon. */}
          <path d="M28 34 L212 34 L146 108 L146 172 L94 150 L94 108 Z" {...guide} />
          {[54, 78, 102].map((y, i) => (
            <line key={y} x1={40 + i * 22} y1={y} x2={200 - i * 22} y2={y} {...guide} strokeDasharray="2 4" />
          ))}
          <path className="ak-stroke" d="M28 34 L212 34" {...common} />
          <path className="ak-stroke" d="M94 150 L146 172" {...common} />
          <circle className="ak-stroke" cx="120" cy="161" r="6" {...common} />
        </>
      )}
    </svg>
  )
}
