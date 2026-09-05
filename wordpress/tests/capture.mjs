/**
 * Full-page capture of a scroll-animated theme.
 *
 * Three of this theme's effects resolve against the real scroll position, so a
 * naive fullPage screenshot shows everything below the fold in its start
 * state — blank, or mid-transition. Each needs a different neutralisation, and
 * getting one of them wrong produces a "defect" that only exists in the
 * screenshot:
 *
 *   .ak-rise      opacity/translate reveal. Safe to flatten completely.
 *   .ak-vf        variable-font weight animation. Only opacity matters.
 *   [data-ak-cut] GSAP splits the heading into TWO absolutely positioned
 *                 halves that converge. Forcing transform:none on both stacks
 *                 them on each other and over the paragraph below — which is
 *                 exactly what a first attempt at this file did, and it read
 *                 as a broken heading on the homepage. Show one half, hide
 *                 the other; both carry the same text.
 *   .ak-reel      a deliberate 220svh sticky scroll stage. NOT flattened: the
 *                 tall empty region in a full-page capture is the runway, and
 *                 "fixing" it would remove the effect.
 *
 * Separately, folio numbers decode out of monospace noise when they enter the
 * viewport (~450-750ms each). A capture taken while that runs shows `0K`,
 * `07`, `0-` where `01`..`06` belong — which reads as broken data rather than
 * an unfinished animation. So the page is scrolled through once to trigger
 * every IntersectionObserver, then given time to settle, before anything is
 * captured.
 */
export const CAPTURE_CSS = `
.ak-rise{animation:none!important;opacity:1!important;transform:none!important}
.ak-vf{opacity:1!important}
[data-ak-cut]{opacity:1!important}
[data-ak-cut] .ak-cut-half--bottom{display:none!important}
/* The surviving half is clipped to the TOP of its glyphs by design; without
   removing the clip the capture shows headings with their lower halves sliced
   off, which reads as a rendering bug. */
[data-ak-cut] .ak-cut-half--top{opacity:1!important;transform:none!important;
  clip-path:none!important;-webkit-clip-path:none!important;clip:auto!important}
`

/** Neutralise the reveals, trigger the observers, and settle. */
export async function flatten (page) {
  await page.addStyleTag({ content: CAPTURE_CSS })
  await page.evaluate(async () => {
    const step = Math.round(innerHeight * 0.8)
    for (let y = 0; y < document.body.scrollHeight; y += step) {
      scrollTo(0, y)
      await new Promise(r => setTimeout(r, 60))
    }
    scrollTo(0, 0)
  })
  await page.waitForTimeout(1400)
}
