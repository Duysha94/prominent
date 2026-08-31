import { useSyncExternalStore } from 'react'

const QUERY = '(hover: hover) and (pointer: fine)'

const subscribe = (cb: () => void) => {
  const mq = window.matchMedia(QUERY)
  mq.addEventListener('change', cb)
  return () => mq.removeEventListener('change', cb)
}

/**
 * True when the visitor has a real pointer that can hover. Everything gated on
 * this must have a non-hover path — hover is an enhancement, never the only
 * way to reach information.
 */
export const useHasHover = () =>
  useSyncExternalStore(
    subscribe,
    () => window.matchMedia(QUERY).matches,
    () => true,
  )
