/**
 * MATERIAL.
 *
 * Grain earns its place for a specific technical reason: the palette is one
 * saturated orange against a near-monochrome ground, and large flat fields of
 * that orange band visibly on 8-bit displays. Grain is dithering with a point
 * of view.
 *
 * The two modes get genuinely different substrate rather than the same texture
 * at two opacities — ATELIER gets a low-frequency paper fibre, RUNWAY a
 * high-frequency film grain. Most dual-mode sites swap hex values; swapping
 * material is what makes the two rooms feel authored separately.
 *
 * Both filters are declared once and selected in CSS, so switching themes
 * costs no re-render and no JS.
 */
export function Grain() {
  return (
    <div className="ak-grain" aria-hidden="true">
      <svg width="100%" height="100%">
        <defs>
          <filter id="ak-fibre">
            <feTurbulence type="fractalNoise" baseFrequency="0.62 0.78" numOctaves={2} stitchTiles="stitch" />
            <feColorMatrix type="saturate" values="0" />
          </filter>
          <filter id="ak-film">
            <feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves={3} stitchTiles="stitch" />
            <feColorMatrix type="saturate" values="0" />
          </filter>
        </defs>
        <rect className="ak-grain__fibre" width="100%" height="100%" filter="url(#ak-fibre)" />
        <rect className="ak-grain__film" width="100%" height="100%" filter="url(#ak-film)" />
      </svg>
    </div>
  )
}
