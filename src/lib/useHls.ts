import { useEffect, useRef, useState } from 'react'

/**
 * Attach an HLS source to a <video>, using the browser's native player where
 * it exists (Safari, iOS) and hls.js everywhere else.
 *
 * hls.js is imported dynamically so its ~150KB never lands in the initial
 * bundle — the hero's first paint is the poster frame, and the player arrives
 * afterwards. Anything that fails degrades to the poster rather than a black
 * rectangle, which matters because this is decorative material: no information
 * is lost if it never plays.
 */
export function useHls(src: string, enabled = true) {
  const ref = useRef<HTMLVideoElement>(null)
  const [ready, setReady] = useState(false)

  useEffect(() => {
    const video = ref.current
    if (!video || !enabled) return

    // Save the visitor's data if they have asked us to.
    const conn = (navigator as Navigator & { connection?: { saveData?: boolean } }).connection
    if (conn?.saveData) return

    let destroy: (() => void) | undefined
    let cancelled = false

    if (video.canPlayType('application/vnd.apple.mpegurl')) {
      video.src = src
      setReady(true)
    } else {
      import('hls.js')
        .then(({ default: Hls }) => {
          if (cancelled || !Hls.isSupported()) return
          const hls = new Hls({ capLevelToPlayerSize: true, maxBufferLength: 12 })
          hls.loadSource(src)
          hls.attachMedia(video)
          hls.on(Hls.Events.MANIFEST_PARSED, () => setReady(true))
          destroy = () => hls.destroy()
        })
        .catch(() => {
          /* Poster stays. Nothing here is load-bearing. */
        })
    }

    return () => {
      cancelled = true
      destroy?.()
    }
  }, [src, enabled])

  return { ref, ready }
}
