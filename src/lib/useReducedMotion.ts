import { useSyncExternalStore } from 'react'

const QUERY = '(prefers-reduced-motion: reduce)'

const subscribe = (cb: () => void) => {
  const mq = window.matchMedia(QUERY)
  mq.addEventListener('change', cb)
  return () => mq.removeEventListener('change', cb)
}

/**
 * Reduced motion is a design mode, not a kill switch. Components read this and
 * substitute an *instant* state change for travel — they do not simply run the
 * same animation faster, and they never drop the information the motion carried.
 */
export const useReducedMotion = () =>
  useSyncExternalStore(
    subscribe,
    () => window.matchMedia(QUERY).matches,
    () => false, // SSR / first paint: assume full motion, correct on hydrate
  )

export const prefersReducedMotion = () =>
  typeof window !== 'undefined' && window.matchMedia(QUERY).matches
