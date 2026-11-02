import * as THREE from 'three'

/**
 * A tileable normal map of satin folds: long soft ridges, warped so they never
 * run parallel for long. Every term is an integer multiple of 2πu and 2πv, so
 * the map repeats without a seam.
 */
export function silkNormalMap(size = 512): THREE.CanvasTexture {
  const h = new Float32Array(size * size)
  const TAU = Math.PI * 2
  for (let y = 0; y < size; y++) {
    const b = (TAU * y) / size
    for (let x = 0; x < size; x++) {
      const a = (TAU * x) / size
      const warp = 0.7 * Math.sin(b + 0.6 * Math.sin(2 * a)) + 0.35 * Math.sin(3 * a + 2 * b)
      h[y * size + x] =
        0.62 * Math.sin(5 * a + 2.4 * warp) +
        0.22 * Math.sin(11 * a + 3 * b + warp) +
        0.3 * Math.sin(2 * a - 4 * b + 0.8 * warp) +
        0.08 * Math.sin(23 * a - 7 * b)
    }
  }
  const cv = document.createElement('canvas')
  cv.width = cv.height = size
  const ctx = cv.getContext('2d')!
  const img = ctx.createImageData(size, size)
  const k = 2.2
  for (let y = 0; y < size; y++) {
    for (let x = 0; x < size; x++) {
      const l = h[y * size + ((x - 1 + size) % size)]
      const r = h[y * size + ((x + 1) % size)]
      const u = h[((y - 1 + size) % size) * size + x]
      const d = h[((y + 1) % size) * size + x]
      let nx = (l - r) * k
      let ny = (d - u) * k
      let nz = 1
      const len = Math.hypot(nx, ny, nz)
      nx /= len
      ny /= len
      nz /= len
      const i = (y * size + x) * 4
      img.data[i] = (nx * 0.5 + 0.5) * 255
      img.data[i + 1] = (ny * 0.5 + 0.5) * 255
      img.data[i + 2] = (nz * 0.5 + 0.5) * 255
      img.data[i + 3] = 255
    }
  }
  ctx.putImageData(img, 0, 0)
  const tex = new THREE.CanvasTexture(cv)
  tex.wrapS = tex.wrapT = THREE.RepeatWrapping
  tex.colorSpace = THREE.NoColorSpace
  tex.anisotropy = 4
  return tex
}
