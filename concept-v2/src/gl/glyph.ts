import * as THREE from 'three'
import data from './glyphs.json'

export type LetterId = 'A' | 'K'

interface GlyphData {
  advance: number
  bbox: [number, number, number, number]
  commands: (string | number)[][]
}

export const FONT = data as unknown as {
  ascender: number
  descender: number
  capHeight: number
  glyphs: Record<LetterId, GlyphData>
}

/** Extrusion, in em. Deep enough to read as a monument, not a sign. */
export const DEPTH = 0.22
export const BEVEL = 0.012

export const bbox = (id: LetterId) => FONT.glyphs[id].bbox

function shapePath(id: LetterId) {
  const p = new THREE.ShapePath()
  for (const c of FONT.glyphs[id].commands) {
    const [t, ...n] = c as [string, ...number[]]
    if (t === 'M') p.moveTo(n[0], n[1])
    else if (t === 'L') p.lineTo(n[0], n[1])
    else if (t === 'Q') p.quadraticCurveTo(n[0], n[1], n[2], n[3])
    else if (t === 'C') p.bezierCurveTo(n[0], n[1], n[2], n[3], n[4], n[5])
  }
  return p
}

export function glyphShapes(id: LetterId): THREE.Shape[] {
  // three resolves solids and holes by the nonzero fill rule, as fonts do,
  // so the A keeps its counter whichever way the contours wind.
  return shapePath(id).toShapes()
}

/** Caps map the glyph's bounding box onto 0..1 so a video can sit on the face. */
function uvGenerator(id: LetterId) {
  const [x1, y1, x2, y2] = bbox(id)
  const w = x2 - x1
  const h = y2 - y1
  return {
    generateTopUV(_g: unknown, v: ArrayLike<number>, a: number, b: number, c: number) {
      return [a, b, c].map((i) => new THREE.Vector2((v[i * 3] - x1) / w, (v[i * 3 + 1] - y1) / h))
    },
    generateSideWallUV(_g: unknown, v: ArrayLike<number>, a: number, b: number, c: number, d: number) {
      return [a, b, c, d].map(
        (i) => new THREE.Vector2((v[i * 3] - v[i * 3 + 1]) * 1.6, v[i * 3 + 2] * 2.4),
      )
    },
  }
}

/**
 * The letter as a solid, centred on its ink box in x/y, with its front face at
 * z = 0 so it can be laid exactly over the DOM glyph it replaces.
 */
export function letterGeometry(id: LetterId) {
  const geo = new THREE.ExtrudeGeometry(glyphShapes(id), {
    depth: DEPTH,
    bevelEnabled: true,
    bevelThickness: BEVEL,
    bevelSize: BEVEL * 0.8,
    bevelOffset: -BEVEL * 0.8,
    bevelSegments: 5,
    curveSegments: 12,
    UVGenerator: uvGenerator(id) as unknown as THREE.UVGenerator,
  })
  const [x1, y1, x2, y2] = bbox(id)
  geo.translate(-(x1 + x2) / 2, -(y1 + y2) / 2, -(DEPTH + BEVEL))
  return geo
}

/** The same letter without bevels: its edges make a clean technical drawing. */
export function outlineGeometry(id: LetterId) {
  const geo = new THREE.ExtrudeGeometry(glyphShapes(id), { depth: DEPTH + 2 * BEVEL, bevelEnabled: false, curveSegments: 12 })
  const [x1, y1, x2, y2] = bbox(id)
  geo.translate(-(x1 + x2) / 2, -(y1 + y2) / 2, -(DEPTH + 2 * BEVEL))
  return geo
}

/** Points on a regular grid inside the letter's volume — the letter as pixels. */
export function voxelPoints(id: LetterId, step = 0.026): Float32Array {
  const g = FONT.glyphs[id]
  const [x1, y1, x2, y2] = g.bbox
  const R = 320
  const W = Math.ceil((x2 - x1) * R) + 4
  const H = Math.ceil((y2 - y1) * R) + 4
  const cv = document.createElement('canvas')
  cv.width = W
  cv.height = H
  const ctx = cv.getContext('2d', { willReadFrequently: true })!
  ctx.setTransform(R, 0, 0, -R, -x1 * R + 2, y2 * R + 2)
  const p = new Path2D()
  for (const c of g.commands) {
    const [t, ...n] = c as [string, ...number[]]
    if (t === 'M') p.moveTo(n[0], n[1])
    else if (t === 'L') p.lineTo(n[0], n[1])
    else if (t === 'Q') p.quadraticCurveTo(n[0], n[1], n[2], n[3])
    else if (t === 'C') p.bezierCurveTo(n[0], n[1], n[2], n[3], n[4], n[5])
    else if (t === 'Z') p.closePath()
  }
  ctx.fill(p, 'nonzero')
  const px = ctx.getImageData(0, 0, W, H).data
  const cx = (x1 + x2) / 2
  const cy = (y1 + y2) / 2
  const zs: number[] = []
  for (let z = -step / 2; z > -(DEPTH + 2 * BEVEL); z -= step) zs.push(z)
  const out: number[] = []
  for (let y = y1 + step / 2; y < y2; y += step) {
    for (let x = x1 + step / 2; x < x2; x += step) {
      const ix = Math.round((x - x1) * R + 2)
      const iy = Math.round((y2 - y) * R + 2)
      if (px[(iy * W + ix) * 4 + 3] > 127) for (const z of zs) out.push(x - cx, y - cy, z)
    }
  }
  return new Float32Array(out)
}
