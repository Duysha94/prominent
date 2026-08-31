import { useEffect, useRef } from 'react'

/**
 * Decorative background video, started late and only when it is welcome.
 *
 * No `autoplay` attribute: playback begins after first paint, so the video
 * never competes with the LCP element for bandwidth or decode. Nothing here is
 * load-bearing — the poster underneath is a drawn plate, so a video that never
 * plays costs the visitor no information at all.
 *
 * Three gates, all of which are real signals rather than proxies for them:
 * the visitor's data-saver preference, a slow effective connection, and
 * whether the element is actually on screen.
 */
export function useBackgroundVideo(enabled = true) {
  const ref = useRef<HTMLVideoElement>(null)

  useEffect(() => {
    const video = ref.current
    if (!video || !enabled) return

    const conn = (
      navigator as Navigator & {
        connection?: { saveData?: boolean; effectiveType?: string }
      }
    ).connection

    // Save-Data is an explicit request, not a heuristic. Honour it.
    if (conn?.saveData) return
    if (/(^|-)2g$/.test(conn?.effectiveType ?? '')) return

    // Only load and play while on screen; pause when it leaves, so a video
    // scrolled past is not decoding frames nobody is looking at.
    const io = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          video.preload = 'auto'
          video.play().catch(() => {
            /* Autoplay refused. The plate underneath is the finished state. */
          })
        } else {
          video.pause()
        }
      },
      { threshold: 0.1 },
    )

    const start = () => io.observe(video)
    // Wait for load so the hero headline paints first.
    if (document.readyState === 'complete') start()
    else window.addEventListener('load', start, { once: true })

    const onVisibility = () => {
      if (document.hidden) video.pause()
    }
    document.addEventListener('visibilitychange', onVisibility)

    return () => {
      io.disconnect()
      window.removeEventListener('load', start)
      document.removeEventListener('visibilitychange', onVisibility)
    }
  }, [enabled])

  return ref
}
