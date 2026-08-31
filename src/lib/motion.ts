/**
 * MOTION TOKENS — one vocabulary for the whole site.
 *
 * Doctrine: motion here is either FEEDBACK (you did something), ORIENTATION
 * (where you are), CONTINUITY (this thing became that thing) or ATMOSPHERE
 * (the room is alive). Anything that is none of those is deleted.
 *
 * These mirror the CSS custom properties in tokens.css so GSAP, Motion and
 * plain CSS all move on the same curves.
 */

/** A cubic-bezier as Motion and CSS both want it: exactly four control points. */
export type Bezier = [number, number, number, number]

/** cubic-bezier control points, GSAP `CustomEase` / Motion `ease` compatible. */
export const EASE: Record<'cut' | 'thread' | 'snap' | 'drape', Bezier> = {
  /** expo-out. Reveals, cuts, anything arriving. Fast out of the gate, long tail. */
  cut: [0.16, 1, 0.3, 1],
  /** in-out. The seam. Things that travel rather than arrive. */
  thread: [0.65, 0.05, 0.36, 1],
  /** slight overshoot. Measurement pins snapping to a garment. Never on large objects. */
  snap: [0.34, 1.56, 0.64, 1],
  /** soft settle, no overshoot. Fabric falling. Theme swaps, image crossfades. */
  drape: [0.22, 0.61, 0.36, 1],
}

/** GSAP takes eases as strings; these are the registered CustomEase names. */
export const GSAP_EASE = {
  cut: 'ak.cut',
  thread: 'ak.thread',
  snap: 'ak.snap',
  drape: 'ak.drape',
} as const

/** Seconds, for GSAP. Milliseconds are `D_MS`. */
export const D = {
  instant: 0.12,
  quick: 0.24,
  base: 0.48,
  slow: 0.82,
  reveal: 1.2,
} as const

export const D_MS = {
  instant: 120,
  quick: 240,
  base: 480,
  slow: 820,
  reveal: 1200,
} as const

/**
 * Stagger doctrine: a headline reads left-to-right, so its parts arrive
 * left-to-right — but faster than reading speed, or the reveal becomes a
 * queue. 28ms per line, 14ms per word, 6ms per character.
 */
export const STAGGER = {
  char: 0.006,
  word: 0.014,
  line: 0.028,
  block: 0.08,
  card: 0.06,
} as const

/** Spring configs for Motion (`type: 'spring'`). */
export const SPRING = {
  /** Cursor, magnetic pulls — must feel weightless. */
  light: { stiffness: 420, damping: 32, mass: 0.6 },
  /** Panels, drawers — has body. */
  body: { stiffness: 240, damping: 30, mass: 1 },
  /** The seam's slack. Deliberately underdamped so it wobbles like thread. */
  thread: { stiffness: 90, damping: 14, mass: 1.1 },
} as const

/** Distance vocabulary (px). Reveals travel a short, consistent distance. */
export const TRAVEL = {
  hair: 6,
  short: 18,
  medium: 40,
  long: 88,
} as const

export const cssEase = (e: Bezier) => `cubic-bezier(${e.join(',')})`
