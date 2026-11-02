/**
 * The film studio: renders the placeholder footage the site plays, one
 * deterministic frame at a time, so a headless browser can hand every frame
 * to ffmpeg. None of this ships with the site — replace the clips with real
 * London Fashion Day / Odessa Fashion Day footage when it exists.
 *
 *   film.html?clip=runway-a&w=1600&h=900
 *   window.film.frame(i) → JPEG data URL of frame i
 */
import * as THREE from 'three'
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js'
import { EffectComposer } from 'three/examples/jsm/postprocessing/EffectComposer.js'
import { RenderPass } from 'three/examples/jsm/postprocessing/RenderPass.js'
import { BokehPass } from 'three/examples/jsm/postprocessing/BokehPass.js'
import { OutputPass } from 'three/examples/jsm/postprocessing/OutputPass.js'
import { silkNormalMap } from '../gl/silk'

const q = new URLSearchParams(location.search)
const CLIP = q.get('clip') || 'runway-a'
const W = +(q.get('w') || 1280)
const H = +(q.get('h') || 720)
const FPS = 30
const SECONDS = +(q.get('s') || 6)
const TAU = Math.PI * 2

function rng(seed: number) {
  return () => {
    seed |= 0
    seed = (seed + 0x6d2b79f5) | 0
    let t = Math.imul(seed ^ (seed >>> 15), 1 | seed)
    t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t
    return ((t ^ (t >>> 14)) >>> 0) / 4294967296
  }
}

const renderer = new THREE.WebGLRenderer({ antialias: true, preserveDrawingBuffer: true })
renderer.setPixelRatio(1)
renderer.setSize(W, H)
renderer.outputColorSpace = THREE.SRGBColorSpace
renderer.toneMapping = THREE.ACESFilmicToneMapping
renderer.shadowMap.enabled = true
renderer.shadowMap.type = THREE.PCFShadowMap
document.body.appendChild(renderer.domElement)

const COBALT = new THREE.Color('#1f2de6')

/** A soft round glow, for lamps and flashes. */
function glowTexture() {
  const c = document.createElement('canvas')
  c.width = c.height = 128
  const g = c.getContext('2d')!
  const grad = g.createRadialGradient(64, 64, 0, 64, 64, 64)
  grad.addColorStop(0, 'rgba(255,255,255,1)')
  grad.addColorStop(0.18, 'rgba(255,244,228,0.85)')
  grad.addColorStop(0.45, 'rgba(255,230,200,0.18)')
  grad.addColorStop(1, 'rgba(255,220,190,0)')
  g.fillStyle = grad
  g.fillRect(0, 0, 128, 128)
  const t = new THREE.CanvasTexture(c)
  t.colorSpace = THREE.SRGBColorSpace
  return t
}

/** A strapless gown on an invisible model: lathe profile, elliptical section, skirt folds. */
function gownGeometry() {
  const prof: [number, number][] = [
    [0.0, 1.42], [0.13, 1.42], [0.17, 1.36], [0.165, 1.22], [0.13, 1.08], [0.118, 1.0],
    [0.15, 0.88], [0.18, 0.78], [0.22, 0.6], [0.29, 0.38], [0.38, 0.16], [0.47, 0.0],
  ]
  const geo = new THREE.LatheGeometry(prof.map(([r, y]) => new THREE.Vector2(r, y)), 96)
  // densify vertically for the skirt to move
  const pos = geo.attributes.position as THREE.BufferAttribute
  for (let i = 0; i < pos.count; i++) {
    const x = pos.getX(i)
    const y = pos.getY(i)
    const z = pos.getZ(i)
    const a = Math.atan2(z, x)
    const skirt = Math.max(0, (0.85 - y) / 0.85)
    const fold = 1 + 0.06 * Math.sin(a * 13) * skirt + 0.03 * Math.sin(a * 5 + 1.3) * skirt
    pos.setXYZ(i, x * fold, y, z * 0.74 * fold)
  }
  geo.computeVertexNormals()
  return geo
}

function silkMaterial(color: THREE.ColorRepresentation, sheen: THREE.ColorRepresentation) {
  const n = silkNormalMap(512)
  n.repeat.set(3, 3)
  return new THREE.MeshPhysicalMaterial({
    color, roughness: 0.42, sheen: 1, sheenColor: new THREE.Color(sheen), sheenRoughness: 0.35,
    anisotropy: 0.9, anisotropyRotation: Math.PI / 2, normalMap: n, normalScale: new THREE.Vector2(0.7, 0.7),
    side: THREE.DoubleSide,
  })
}

/** The gown walks: hips sway, the skirt lags and kicks forward with each step. */
function walkingGown(mat: THREE.MeshPhysicalMaterial) {
  const uni = { uPhase: { value: 0 } }
  mat.onBeforeCompile = (sh) => {
    sh.uniforms.uPhase = uni.uPhase
    sh.vertexShader = sh.vertexShader
      .replace('void main() {', 'uniform float uPhase;\nvoid main() {')
      .replace(
        '#include <begin_vertex>',
        `#include <begin_vertex>
        float low = clamp((0.85 - position.y) / 0.85, 0.0, 1.0);
        float lag = uPhase - (1.0 - low) * 0.0 - low * 0.9;
        transformed.x += sin(lag) * 0.045 * low * low;
        float side = position.x > 0.0 ? 0.0 : 3.14159;
        float kick = max(0.0, sin(uPhase + side));
        transformed.z += kick * 0.09 * low * low * smoothstep(-0.05, 0.25, position.z);
        transformed.y += sin(uPhase * 2.0) * 0.008;`,
      )
  }
  const mesh = new THREE.Mesh(gownGeometry(), mat)
  mesh.castShadow = true
  return { mesh, uni }
}

// ───────────────────────────────────────────────────────────── runway ──
function runwayClip(variant: 'a' | 'b') {
  const scene = new THREE.Scene()
  const bg = new THREE.Color('#040405')
  scene.background = bg
  scene.fog = new THREE.FogExp2(bg, variant === 'a' ? 0.045 : 0.06)
  const camera = new THREE.PerspectiveCamera(variant === 'a' ? 24 : 32, W / H, 0.1, 200)
  const R = rng(variant === 'a' ? 7 : 19)
  const glow = glowTexture()

  const floor = new THREE.Mesh(new THREE.PlaneGeometry(80, 120), new THREE.MeshStandardMaterial({ color: '#070708', roughness: 0.95 }))
  floor.rotation.x = -Math.PI / 2
  floor.receiveShadow = true
  scene.add(floor)
  const runway = new THREE.Mesh(
    new THREE.BoxGeometry(2.4, 0.3, 70),
    new THREE.MeshPhysicalMaterial({ color: '#0b0c0f', roughness: 0.16, clearcoat: 1, clearcoatRoughness: 0.08 }),
  )
  runway.position.set(0, 0.15, -30)
  runway.receiveShadow = true
  scene.add(runway)

  // Lamps on the truss, with their glow and their streak on the lacquered runway.
  const warm = variant === 'a' ? '#fff1dc' : '#e6ecff'
  const lampMat = new THREE.SpriteMaterial({ map: glow, color: warm, blending: THREE.AdditiveBlending, depthWrite: false, transparent: true })
  const streakTex = (() => {
    const c = document.createElement('canvas')
    c.width = 32
    c.height = 256
    const g = c.getContext('2d')!
    const grad = g.createLinearGradient(0, 0, 0, 256)
    grad.addColorStop(0, 'rgba(255,255,255,0)')
    grad.addColorStop(0.5, 'rgba(255,255,255,0.55)')
    grad.addColorStop(1, 'rgba(255,255,255,0)')
    g.fillStyle = grad
    g.fillRect(0, 0, 32, 256)
    const h = g.getImageData(0, 0, 32, 256)
    for (let y = 0; y < 256; y++) for (let x = 0; x < 32; x++) {
      const k = Math.pow(Math.sin((x / 31) * Math.PI), 2)
      h.data[(y * 32 + x) * 4 + 3] *= k
    }
    g.putImageData(h, 0, 0)
    const t = new THREE.CanvasTexture(c)
    return t
  })()
  const streakMat = new THREE.MeshBasicMaterial({ map: streakTex, color: warm, blending: THREE.AdditiveBlending, transparent: true, depthWrite: false, opacity: 0.22 })
  for (let z = 2; z > -62; z -= 2.6) {
    for (const x of [-2.1, 2.1]) {
      const s = new THREE.Sprite(lampMat)
      s.position.set(x, 3.4, z)
      s.scale.setScalar(0.85)
      scene.add(s)
      const st = new THREE.Mesh(new THREE.PlaneGeometry(0.1, 5.2), streakMat)
      st.rotation.x = -Math.PI / 2
      st.position.set(x * 0.5, 0.302, z + 2.2)
      scene.add(st)
    }
  }
  // Truss light that actually lights the walk.
  const spots: THREE.SpotLight[] = []
  for (let i = 0; i < 6; i++) {
    const s = new THREE.SpotLight(warm, 60, 14, 0.42, 0.6, 1.6)
    s.position.set(i % 2 ? 1.8 : -1.8, 3.3, -2 - i * 3.2)
    s.target.position.set(0, 0.3, -2 - i * 3.2)
    scene.add(s, s.target)
    spots.push(s)
  }
  // The backdrop: a lit entrance at the far end.
  const door = new THREE.Mesh(new THREE.PlaneGeometry(4.6, 3.6), new THREE.MeshBasicMaterial({ color: '#d9dcff' }))
  door.position.set(0, 1.8, -62)
  scene.add(door)

  // The audience: phones held up, a few lit at any moment.
  const phones: THREE.Sprite[] = []
  const phoneMat = new THREE.SpriteMaterial({ map: glow, color: '#b9c4ff', blending: THREE.AdditiveBlending, transparent: true, depthWrite: false })
  for (let i = 0; i < 160; i++) {
    const side = R() < 0.5 ? -1 : 1
    const s = new THREE.Sprite(phoneMat.clone())
    s.position.set(side * (2.4 + R() * 5), 0.9 + R() * 1.1, 2 - R() * 50)
    s.scale.setScalar(0.12 + R() * 0.08)
    s.userData.seed = R() * 100
    scene.add(s)
    phones.push(s)
  }

  // The look: a cobalt silk gown on an invisible model.
  const { mesh: gown, uni } = walkingGown(silkMaterial(variant === 'a' ? COBALT : '#a89c86', variant === 'a' ? '#6f7dff' : '#f3e6cf'))
  scene.add(gown)
  const key = new THREE.SpotLight('#ffffff', variant === 'a' ? 90 : 38, 20, 0.35, 0.5, 1.4)
  key.castShadow = true
  scene.add(key, key.target)
  const rim = new THREE.PointLight(warm, 25, 8, 1.5)
  scene.add(rim)

  // Photographers' flashes at the end of the runway.
  const flashes: { t: number; x: number; y: number }[] = []
  for (let i = 0; i < 14; i++) flashes.push({ t: R() * SECONDS, x: (R() - 0.5) * 3.2, y: 0.5 + R() * 1.2 })
  const flashLight = new THREE.PointLight('#ffffff', 0, 30, 1.2)
  scene.add(flashLight)
  const flashSprite = new THREE.Sprite(new THREE.SpriteMaterial({ map: glow, color: '#ffffff', blending: THREE.AdditiveBlending, transparent: true, depthWrite: false, opacity: 0 }))
  flashSprite.scale.setScalar(2.4)
  scene.add(flashSprite)

  const composer = new EffectComposer(renderer)
  composer.addPass(new RenderPass(scene, camera))
  const bokeh = new BokehPass(scene, camera, { focus: 8, aperture: variant === 'a' ? 0.0022 : 0.0016, maxblur: 0.009 })
  composer.addPass(bokeh)
  composer.addPass(new OutputPass())

  return (t: number) => {
    const u = t / SECONDS
    const walkZ = variant === 'a' ? -11.5 + u * 7.4 : -9 + u * 7.8
    const phase = t * 5.2
    uni.uPhase.value = phase
    gown.position.set(Math.sin(phase * 0.5) * 0.03, 0.3, walkZ)
    gown.rotation.y = Math.sin(phase * 0.5) * 0.06
    key.position.set(0.6, 3.6, walkZ + 4.5)
    key.target.position.set(0, 0.9, walkZ)
    rim.position.set(0, 2.2, walkZ - 1.6)
    if (variant === 'a') {
      camera.position.set(0.15, 0.95, 3.2 - u * 0.6)
      camera.lookAt(0, 0.95, walkZ - 2)
    } else {
      camera.position.set(3.2 - u * 0.4, 0.62, -3.6)
      camera.lookAt(0, 0.95, walkZ - 0.4)
    }
    const dist = camera.position.distanceTo(gown.position)
    ;(bokeh.uniforms as Record<string, THREE.IUniform>).focus.value = dist
    for (const s of phones) {
      const on = Math.sin(t * 2.1 + s.userData.seed) > 0.55 ? 1 : 0.22
      ;(s.material as THREE.SpriteMaterial).opacity = on
    }
    let f = 0
    let fx = 0
    let fy = 1
    for (const fl of flashes) {
      const dt = t - fl.t
      const v = dt >= 0 && dt < 0.12 ? 1 - dt / 0.12 : 0
      if (v > f) {
        f = v
        fx = fl.x
        fy = fl.y
      }
    }
    // More cameras fire as the look reaches the end of the runway.
    f *= 0.45 + 0.55 * u
    flashLight.intensity = f * 140
    flashLight.position.set(fx, fy, walkZ + 3.4)
    ;(flashSprite.material as THREE.SpriteMaterial).opacity = f * 0.9
    flashSprite.position.set(fx, fy, (variant === 'a' ? camera.position.z : walkZ + 3.6) - 0.6)
    spots.forEach((s, i) => (s.intensity = 60 + Math.sin(t * 0.7 + i) * 6))
    composer.render()
  }
}

// ─────────────────────────────────────────────────────────────── silk ──
function silkClip() {
  const scene = new THREE.Scene()
  scene.background = new THREE.Color('#05060d')
  const pm = new THREE.PMREMGenerator(renderer)
  scene.environment = pm.fromScene(new RoomEnvironment(), 0.04).texture
  scene.environmentIntensity = 0.35
  const camera = new THREE.PerspectiveCamera(30, W / H, 0.1, 100)
  camera.position.set(0, 3.4, 1.2)
  camera.lookAt(0, 0, -0.35)
  const geo = new THREE.PlaneGeometry(7, 5, 320, 220)
  geo.rotateX(-Math.PI / 2)
  const mat = silkMaterial(COBALT, '#7f8bff')
  mat.anisotropyRotation = 0
  const uni = { uT: { value: 0 } }
  mat.onBeforeCompile = (sh) => {
    sh.uniforms.uT = uni.uT
    const wave = `
      float w(vec2 p, float t){
        return 0.12*sin(p.x*2.2 + p.y*0.4 + t) + 0.07*sin(p.x*3.9 - p.y*1.3 + 2.0*t + 1.0)
          + 0.05*sin(p.y*3.1 + p.x*1.4 - t + 2.0) + 0.02*sin(p.x*9.0 + p.y*2.0 + 3.0*t);
      }`
    sh.vertexShader = sh.vertexShader
      .replace('void main() {', `uniform float uT;\n${wave}\nvoid main() {`)
      .replace(
        '#include <beginnormal_vertex>',
        `vec2 pp = position.xz; float e = 0.02;
         float h0 = w(pp, uT); float hx = w(pp + vec2(e,0.), uT); float hz = w(pp + vec2(0.,e), uT);
         vec3 objectNormal = normalize(vec3(-(hx-h0)/e, 1.0, -(hz-h0)/e));
         #ifdef USE_TANGENT
           vec3 objectTangent = vec3( tangent.xyz );
         #endif`,
      )
      .replace('#include <begin_vertex>', '#include <begin_vertex>\ntransformed.y += w(position.xz, uT);')
  }
  scene.add(new THREE.Mesh(geo, mat))
  const key = new THREE.DirectionalLight('#ffffff', 3.2)
  scene.add(key)
  const back = new THREE.DirectionalLight('#9aa6ff', 2)
  back.position.set(0, 2, -4)
  scene.add(back)
  return (t: number) => {
    const ph = (t / SECONDS) * TAU
    uni.uT.value = ph
    key.position.set(Math.cos(ph) * 3, 2.2, Math.sin(ph) * 2 + 1)
    renderer.render(scene, camera)
  }
}

// ────────────────────────────────────────────────────────────── pages ──
function pagesClip() {
  const scene = new THREE.Scene()
  scene.background = new THREE.Color('#eef0f3')
  const pm = new THREE.PMREMGenerator(renderer)
  scene.environment = pm.fromScene(new RoomEnvironment(), 0.04).texture
  scene.environmentIntensity = 0.9
  renderer.toneMapping = THREE.NeutralToneMapping
  const camera = new THREE.PerspectiveCamera(32, W / H, 0.1, 100)
  camera.position.set(0, 0, 9)
  const R = rng(5)
  const page = (seed: number) => {
    const r = rng(seed)
    const c = document.createElement('canvas')
    c.width = 420
    c.height = 594
    const g = c.getContext('2d')!
    g.fillStyle = '#f7f7f4'
    g.fillRect(0, 0, 420, 594)
    const layout = Math.floor(r() * 3)
    if (layout === 0) {
      g.fillStyle = r() < 0.5 ? '#0b0c0e' : '#1f2de6'
      g.fillRect(28, 28, 364, 330)
    } else if (layout === 1) {
      g.fillStyle = '#0b0c0e'
      g.fillRect(28, 28, 176, 250)
      g.fillStyle = '#1f2de6'
      g.fillRect(216, 28, 176, 120)
    } else {
      g.fillStyle = '#1f2de6'
      g.fillRect(0, 0, 420, 594 * (0.4 + r() * 0.3))
    }
    g.fillStyle = '#2b2d33'
    let y = layout === 2 ? 420 : 392
    g.fillRect(28, y, 200 + r() * 140, 22)
    y += 44
    g.fillStyle = '#9aa0aa'
    while (y < 566) {
      g.fillRect(28, y, 300 + r() * 64, 6)
      y += 16
    }
    const t = new THREE.CanvasTexture(c)
    t.colorSpace = THREE.SRGBColorSpace
    t.anisotropy = 8
    return t
  }
  const sheets: { m: THREE.Mesh; a: number; b: number; c: number; r: number; y0: number; x0: number; z0: number }[] = []
  for (let i = 0; i < 16; i++) {
    const geo = new THREE.PlaneGeometry(1.4, 1.98, 12, 16)
    const mat = new THREE.MeshPhysicalMaterial({ map: page(i * 31 + 3), roughness: 0.6, side: THREE.DoubleSide, sheen: 0.3 })
    const m = new THREE.Mesh(geo, mat)
    scene.add(m)
    sheets.push({ m, a: R() * TAU, b: R() * TAU, c: R() * TAU, r: 0.6 + R() * 0.8, x0: (R() - 0.5) * 9, y0: (R() - 0.5) * 6, z0: -R() * 6 })
  }
  const key = new THREE.DirectionalLight('#ffffff', 1.6)
  key.position.set(-3, 4, 6)
  scene.add(key)
  return (t: number) => {
    const ph = (t / SECONDS) * TAU
    for (const s of sheets) {
      // Every motion completes whole turns over the clip, so it loops.
      const y = ((s.y0 + 4 - (t / SECONDS) * 8 * 1 + 400) % 8) - 4
      s.m.position.set(s.x0 + Math.sin(ph + s.a) * s.r, -y, s.z0 + Math.sin(ph * 2 + s.b) * 0.3)
      s.m.rotation.set(Math.sin(ph + s.c) * 0.5, ph + s.a, Math.sin(ph + s.b) * 0.3)
    }
    renderer.render(scene, camera)
  }
}

const draw = CLIP === 'silk' ? silkClip() : CLIP === 'pages' ? pagesClip() : runwayClip(CLIP === 'runway-b' ? 'b' : 'a')

;(window as unknown as { film: unknown }).film = {
  fps: FPS,
  frames: Math.round(SECONDS * FPS),
  ready: true,
  frame(i: number) {
    draw(i / FPS)
    return renderer.domElement.toDataURL('image/jpeg', 0.93)
  },
}
