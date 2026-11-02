/**
 * Moving pictures load when they come near and play only while seen. Under
 * reduced motion they stay on their poster frame.
 */
export function initVideos(reduced: boolean) {
  const vids = [...document.querySelectorAll<HTMLVideoElement>('video[data-src]')]
  if (!('IntersectionObserver' in window)) return
  const io = new IntersectionObserver(
    (entries) => {
      for (const e of entries) {
        const v = e.target as HTMLVideoElement
        if (e.isIntersecting) {
          if (!v.src) {
            v.src = v.dataset.src!
            v.load()
          }
          if (!reduced) v.play().catch(() => {})
        } else if (!v.paused) {
          v.pause()
        }
      }
    },
    { rootMargin: '200px 0px' },
  )
  vids.forEach((v) => io.observe(v))
}
