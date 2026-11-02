import * as THREE from 'three'
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js'
import { state } from '../state'
import { FONT, type LetterId, bbox, letterGeometry, outlineGeometry, voxelPoints } from './glyph'
import { silkNormalMap } from './silk'

const INK = new THREE.Color('#0b0c0e')
const COBALT = new THREE.Color('#1f2de6')
const BG = new THREE.Color('#eef0f3')

interface Pose { x: number; y: number; z: number; s: number; rx: number; ry: number; rz: number }
const newPose = (): Pose => ({ x: 0, y: 0, z: 0, s: 1, rx: 0, ry: 0, rz: 0 })
function mixPose(a: Pose, b: Pose, t: number, out: Pose) {
  for (const k of ['x', 'y', 'z', 's', 'rx', 'ry', 'rz'] as const) out[k] = a[k] + (b[k] - a[k]) * t
  return out
}
const clamp01 = (t: number) => Math.min(1, Math.max(0, t))
const easeIO = (t: number) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2)
const easeOut = (t: number) => 1 - Math.pow(1 - t, 3)
const damp = (a: number, b: number, lambda: number, dt: number) => a + (b - a) * (1 - Math.exp(-lambda * dt))

const VIDEO_ASPECT = 16 / 9
const CLIPS = ['media/runway-a.mp4', 'media/pages.mp4', 'media/runway-b.mp4']

interface Letter {
  id: LetterId
  group: THREE.Group
  solid: THREE.Mesh
  lines: THREE.LineSegments
  vox: THREE.InstancedMesh
  voxMat: THREE.MeshStandardMaterial
  echo: THREE.InstancedMesh
  echoOffsets: THREE.Vector3[]
  span: HTMLElement | null
  fs: number
  pose: Pose
  phase: number
}

export class Stage {
  private renderer: THREE.WebGLRenderer
  private scene = new THREE.Scene()
  private camera = new THREE.PerspectiveCamera(28, 1, 0.1, 400)
  private vw = 1
  private vh = 1
  private letters: Record<LetterId, Letter>
  private key = new THREE.DirectionalLight('#fff7ee', 2.4)
  private fill = new THREE.DirectionalLight('#e9eeff', 0.55)
  private hemi = new THREE.HemisphereLight('#ffffff', '#c7ccd6', 0.32)
  private rim = new THREE.DirectionalLight('#ffe9cc', 0)
  private spots: THREE.SpotLight[] = []
  private cones: THREE.Mesh[] = []
  private pools: THREE.Mesh[] = []
  private wall: THREE.Mesh
  private floor: THREE.Mesh
  private clip = new THREE.Plane(new THREE.Vector3(0, -1, 0), 1000)
  private aFace!: THREE.MeshPhysicalMaterial
  private silk: THREE.Texture
  private kMat!: THREE.MeshPhysicalMaterial
  private lineMats: THREE.LineBasicMaterial[] = []
  private videos: HTMLVideoElement[] = []
  private vtex: THREE.VideoTexture[] = []
  private cross = { cur: 0, mix: 0, hold: 0 }
  private pointer = { x: 0, y: 0, tx: 0, ty: 0 }
  private trim = typeof CSS !== 'undefined' && CSS.supports('text-box', 'trim-both cap alphabetic')
  private time = 0
  private reduced: boolean
  private idle = false
  private gain = 0.95
  private vel = 0
  private silkDrift = 0

  constructor(private canvas: HTMLCanvasElement, opts: { reduced: boolean }) {
    this.reduced = opts.reduced
    const small = Math.min(innerWidth, innerHeight) < 700 || matchMedia('(pointer: coarse)').matches
    this.renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true, powerPreference: 'high-performance' })
    this.renderer.setPixelRatio(Math.min(devicePixelRatio, small ? 1.5 : 1.75))
    this.renderer.setClearColor(0x000000, 0)
    this.renderer.outputColorSpace = THREE.SRGBColorSpace
    this.renderer.toneMapping = THREE.NeutralToneMapping
    this.renderer.toneMappingExposure = 1.0
    this.renderer.shadowMap.enabled = true
    this.renderer.shadowMap.type = THREE.PCFShadowMap
    this.renderer.localClippingEnabled = true

    const pmrem = new THREE.PMREMGenerator(this.renderer)
    this.scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture
    this.scene.environmentIntensity = 0.62

    // ── light ──
    this.key.castShadow = true
    this.key.shadow.mapSize.set(small ? 1024 : 2048, small ? 1024 : 2048)
    this.key.shadow.radius = 5
    this.key.shadow.blurSamples = 16
    this.key.shadow.bias = -0.0004
    this.key.shadow.normalBias = 0.02
    this.scene.add(this.key, this.key.target, this.fill, this.fill.target, this.hemi, this.rim, this.rim.target)

    // ── the room: only its shadows are drawn; the page itself is the cyclorama ──
    this.wall = new THREE.Mesh(new THREE.PlaneGeometry(200, 120), new THREE.ShadowMaterial({ color: INK, opacity: 0.13 }))
    this.wall.receiveShadow = true
    this.floor = new THREE.Mesh(new THREE.PlaneGeometry(200, 200), new THREE.ShadowMaterial({ color: INK, opacity: 0.22 }))
    this.floor.rotation.x = -Math.PI / 2
    this.floor.receiveShadow = true
    this.scene.add(this.wall, this.floor)

    // ── the A's screen ──
    for (const src of CLIPS) {
      const v = document.createElement('video')
      v.src = src
      v.muted = true
      v.loop = true
      v.playsInline = true
      v.preload = 'auto'
      v.setAttribute('muted', '')
      v.setAttribute('playsinline', '')
      const t = new THREE.VideoTexture(v)
      t.colorSpace = THREE.SRGBColorSpace
      const [x1, y1, x2, y2] = bbox('A')
      const aspect = (x2 - x1) / (y2 - y1)
      t.repeat.set(aspect / VIDEO_ASPECT, 1)
      t.offset.set((1 - aspect / VIDEO_ASPECT) / 2, 0)
      this.videos.push(v)
      this.vtex.push(t)
    }
    this.silk = silkNormalMap()
    this.silk.repeat.set(2.4, 2.4)

    this.letters = { A: this.makeLetter('A'), K: this.makeLetter('K') }
    this.makeShowLights()

    this.measure()
    addEventListener('resize', () => this.measure())
    addEventListener('pointermove', (e) => {
      this.pointer.tx = (e.clientX / innerWidth) * 2 - 1
      this.pointer.ty = (e.clientY / innerHeight) * 2 - 1
    }, { passive: true })
    document.addEventListener('visibilitychange', () => this.playVideos(!document.hidden))
    this.playVideos(true)
  }

  // ───────────────────────────────────────────────────────────── build ──

  private makeLetter(id: LetterId): Letter {
    const geo = letterGeometry(id)
    let mats: THREE.Material | THREE.Material[]
    if (id === 'A') {
      // Black glass with moving pictures on its face: digital, identity, media.
      this.aFace = new THREE.MeshPhysicalMaterial({
        color: '#050506', roughness: 0.14, metalness: 0, clearcoat: 0.6, clearcoatRoughness: 0.08,
        envMapIntensity: 0.55,
        emissive: '#ffffff', emissiveIntensity: 1, emissiveMap: this.vtex[0],
        clippingPlanes: [this.clip], clipShadows: true,
      })
      this.aFace.onBeforeCompile = (sh) => {
        sh.uniforms.uMap2 = { value: this.vtex[1] }
        sh.uniforms.uMix = { value: 0 }
        sh.uniforms.uGain = { value: 1 }
        sh.fragmentShader = sh.fragmentShader
          .replace('void main() {', 'uniform sampler2D uMap2;\nuniform float uMix;\nuniform float uGain;\nvoid main() {')
          .replace(
            '#include <emissivemap_fragment>',
            `#ifdef USE_EMISSIVEMAP
              vec4 e1 = texture2D( emissiveMap, vEmissiveMapUv );
              vec4 e2 = texture2D( uMap2, vEmissiveMapUv );
              #ifdef DECODE_VIDEO_TEXTURE_EMISSIVE
                e1 = sRGBTransferEOTF( e1 );
                e2 = sRGBTransferEOTF( e2 );
              #endif
              totalEmissiveRadiance *= mix( e1.rgb, e2.rgb, uMix ) * uGain;
            #endif`,
          )
        this.aFace.userData.shader = sh
      }
      const side = new THREE.MeshPhysicalMaterial({
        color: '#0c0d10', roughness: 0.2, metalness: 0.55, clearcoat: 1, clearcoatRoughness: 0.06,
        clippingPlanes: [this.clip], clipShadows: true,
      })
      mats = [this.aFace, side]
    } else {
      // Cobalt silk, stretched over the letter: strategy and fashion production.
      this.kMat = new THREE.MeshPhysicalMaterial({
        color: '#1726c9', roughness: 0.46, metalness: 0,
        sheen: 1, sheenColor: new THREE.Color('#4a5cff'), sheenRoughness: 0.42,
        anisotropy: 0.92, anisotropyRotation: Math.PI * 0.32,
        normalMap: this.silk, normalScale: new THREE.Vector2(1.05, 1.05),
        envMapIntensity: 0.5,
        clippingPlanes: [this.clip], clipShadows: true,
      })
      mats = this.kMat
    }
    const solid = new THREE.Mesh(geo, mats)
    solid.castShadow = true
    solid.receiveShadow = true

    // Strategy: the letter as a drawing.
    const lineMat = new THREE.LineBasicMaterial({ color: INK, transparent: true, opacity: 0, depthWrite: false })
    this.lineMats.push(lineMat)
    const lines = new THREE.LineSegments(new THREE.EdgesGeometry(outlineGeometry(id), 30), lineMat)

    // Digital: the letter as pixels.
    const pts = voxelPoints(id)
    const step = 0.026
    const voxMat = new THREE.MeshStandardMaterial({
      color: id === 'A' ? '#111216' : COBALT, roughness: id === 'A' ? 0.25 : 0.45, metalness: id === 'A' ? 0.4 : 0,
    })
    voxMat.onBeforeCompile = (sh) => {
      sh.uniforms.uVox = { value: 0 }
      sh.uniforms.uTime = { value: 0 }
      sh.uniforms.uBlink = { value: id === 'A' ? new THREE.Color('#e8ecff') : new THREE.Color('#ffffff') }
      sh.vertexShader = sh.vertexShader
        .replace('void main() {', 'uniform float uVox;\nuniform float uTime;\nvarying float vBlink;\nvoid main() {')
        .replace(
          '#include <begin_vertex>',
          `#include <begin_vertex>
          vec3 ip = instanceMatrix[3].xyz;
          float seed = fract( sin( dot( ip.xy * 37.0 + ip.z * 11.0, vec2( 12.9898, 78.233 ) ) ) * 43758.5453 );
          float k = clamp( uVox * 1.7 - seed * 0.7, 0.0, 1.0 );
          transformed *= mix( 1.0, 0.5, k );
          transformed.z += sin( uTime * 2.2 + ip.x * 14.0 + ip.y * 9.0 ) * 0.04 * k;
          vBlink = k * step( 0.88, fract( seed * 91.7 + floor( uTime * 3.0 + seed * 5.0 ) * 0.37 ) );`,
        )
      sh.fragmentShader = sh.fragmentShader
        .replace('void main() {', 'uniform vec3 uBlink;\nvarying float vBlink;\nvoid main() {')
        .replace('#include <emissivemap_fragment>', '#include <emissivemap_fragment>\ntotalEmissiveRadiance += uBlink * vBlink * 1.6;')
      voxMat.userData.shader = sh
    }
    const vox = new THREE.InstancedMesh(new THREE.BoxGeometry(step, step, step), voxMat, pts.length / 3)
    const m = new THREE.Matrix4()
    for (let i = 0; i < pts.length / 3; i++) vox.setMatrixAt(i, m.makeTranslation(pts[i * 3], pts[i * 3 + 1], pts[i * 3 + 2]))
    vox.castShadow = true
    vox.visible = false

    // Visibility: the letter everywhere — a wall of copies receding into the room.
    const echoOffsets: THREE.Vector3[] = []
    // A tower of echoes receding up and back behind the pair, clear of the text on the right.
    for (let r = 0; r < 5; r++) {
      for (let c = -1; c <= 1; c++) {
        if (r === 0 && c !== 1) continue
        if (r >= 2 && c === 1) continue
        echoOffsets.push(new THREE.Vector3(c * 1.62, r * 0.52, -r * 2.1))
      }
    }
    const echoMat = new THREE.MeshStandardMaterial({ color: '#ffffff', roughness: 0.5, metalness: 0.05 })
    const echo = new THREE.InstancedMesh(geo, echoMat, echoOffsets.length)
    const base = id === 'A' ? INK : COBALT
    const col = new THREE.Color()
    echoOffsets.forEach((o, i) => {
      const depth = clamp01(-o.z / 9)
      echo.setColorAt(i, col.copy(base).lerp(BG, Math.min(0.9, 0.45 + depth * 0.45 + Math.abs(o.x) * 0.02)))
    })
    echo.visible = false

    const group = new THREE.Group()
    group.add(solid, lines, vox, echo)
    this.scene.add(group)
    return {
      id, group, solid, lines, vox, voxMat, echo, echoOffsets,
      span: document.querySelector<HTMLElement>(`.monument [data-glyph="${id}"]`),
      fs: 100, pose: newPose(), phase: id === 'A' ? 0 : 2.1,
    }
  }

  private makeShowLights() {
    const coneMat = (o = 0) =>
      new THREE.ShaderMaterial({
        transparent: true, depthWrite: false, blending: THREE.AdditiveBlending, side: THREE.DoubleSide,
        uniforms: { uOpacity: { value: o }, uColor: { value: new THREE.Color('#fff3e2') } },
        vertexShader: `varying float vH; varying vec3 vN; varying vec3 vV;
          void main(){ vH = uv.y; vec4 mv = modelViewMatrix * vec4(position,1.); vN = normalize(normalMatrix*normal); vV = normalize(-mv.xyz); gl_Position = projectionMatrix*mv; }`,
        fragmentShader: `uniform float uOpacity; uniform vec3 uColor; varying float vH; varying vec3 vN; varying vec3 vV;
          void main(){ float f = pow(abs(dot(vN, vV)), 1.6); float a = mix(0.03, 0.34, vH) * f * uOpacity; gl_FragColor = vec4(uColor * a, a); }`,
      })
    const poolMat = () =>
      new THREE.ShaderMaterial({
        transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
        uniforms: { uOpacity: { value: 0 }, uColor: { value: new THREE.Color('#fff1dd') } },
        vertexShader: `varying vec2 vUv; void main(){ vUv = uv; gl_Position = projectionMatrix*modelViewMatrix*vec4(position,1.); }`,
        fragmentShader: `uniform float uOpacity; uniform vec3 uColor; varying vec2 vUv;
          void main(){ float d = length(vUv-0.5)*2.; float a = pow(1.-smoothstep(0.,1.,d), 2.2) * 0.55 * uOpacity; gl_FragColor = vec4(uColor*a, a); }`,
      })
    for (let i = 0; i < 2; i++) {
      const s = new THREE.SpotLight('#fff1dc', 0, 0, 0.32, 0.55, 0)
      this.scene.add(s, s.target)
      this.spots.push(s)
      const cone = new THREE.Mesh(new THREE.ConeGeometry(1, 1, 64, 1, true), coneMat())
      cone.renderOrder = 2
      const pool = new THREE.Mesh(new THREE.CircleGeometry(1, 64), poolMat())
      pool.rotation.x = -Math.PI / 2
      pool.renderOrder = 1
      this.scene.add(cone, pool)
      this.cones.push(cone)
      this.pools.push(pool)
    }
  }

  // ─────────────────────────────────────────────────────────── layout ──

  private measure() {
    this.vw = innerWidth
    this.vh = innerHeight
    this.renderer.setSize(this.vw, this.vh, false)
    // 1 world unit = 100 CSS px on the z = 0 plane.
    const halfH = this.vh / 200
    this.camera.aspect = this.vw / this.vh
    this.camera.position.set(0, 0, halfH / Math.tan(THREE.MathUtils.degToRad(this.camera.fov / 2)))
    this.camera.lookAt(0, 0, 0)
    this.camera.updateProjectionMatrix()
    for (const L of Object.values(this.letters)) {
      if (L.span) L.fs = parseFloat(getComputedStyle(L.span).fontSize) || 100
    }
    const half = (Math.max(this.vw, this.vh) / 100) * 0.85
    const sc = this.key.shadow.camera
    sc.left = -half
    sc.right = half
    sc.top = half
    sc.bottom = -half
    sc.near = 0.5
    sc.far = 260
    sc.updateProjectionMatrix()
  }

  private wx = (px: number) => (px - this.vw / 2) / 100
  private wy = (py: number) => (this.vh / 2 - py) / 100

  /** Where the DOM glyph is: its ink box from the span's origin and baseline. */
  private domPose(L: Letter, out: Pose) {
    const [x1, y1, x2, y2] = bbox(L.id)
    if (!L.span) return out
    const r = L.span.getBoundingClientRect()
    const fs = L.fs
    const baseline = this.trim
      ? r.bottom
      : r.top + ((0.74 - (FONT.ascender - FONT.descender)) / 2 + FONT.ascender) * fs
    out.x = this.wx(r.left + ((x1 + x2) / 2) * fs)
    out.y = this.wy(baseline - ((y1 + y2) / 2) * fs)
    out.z = 0
    out.s = fs / 100
    out.rx = out.ry = out.rz = 0
    return out
  }

  private portrait() {
    return this.vw < 700 || this.vw / this.vh < 0.8
  }

  /** The two letters at opposite edges; the page happens between them. */
  private splitPose(L: Letter, out: Pose) {
    const a = L.id === 'A'
    if (this.portrait()) {
      const h = Math.min(this.vh * 0.3, this.vw * 0.62)
      const fs = h / FONT.capHeight
      out.s = fs / 100
      out.x = this.wx(a ? this.vw * 0.27 : this.vw * 0.73)
      out.y = this.wy(this.vh * 0.27)
      out.ry = a ? 0.32 : -0.32
    } else {
      const h = Math.min(this.vh * 0.7, this.vw * 0.4)
      const fs = h / FONT.capHeight
      out.s = fs / 100
      out.x = this.wx(a ? this.vw * 0.075 : this.vw * 0.925)
      out.y = this.wy(this.vh * 0.53)
      out.ry = a ? 0.42 : -0.42
    }
    out.z = 0
    out.rx = 0
    out.rz = 0
    return out
  }

  /** Practice: the two letters together in the frame's empty lower-left. */
  private stairsPose(L: Letter, out: Pose) {
    const a = L.id === 'A'
    const portrait = this.portrait()
    const h = portrait ? Math.min(this.vh * 0.2, this.vw * 0.42) : this.vh * 0.23
    const fs = h / FONT.capHeight
    const wA = (bbox('A')[2] - bbox('A')[0]) * fs
    const wK = (bbox('K')[2] - bbox('K')[0]) * fs
    const gap = fs * 0.16
    const m = Math.max(20, Math.min(32, this.vw * 0.022))
    const left = portrait ? (this.vw - (wA + gap + wK)) / 2 : m + this.vw * 0.01
    const cx = a ? left + wA / 2 : left + wA + gap + wK / 2
    const cy = this.vh - (portrait ? this.vh * 0.06 : this.vh * 0.065) - h / 2
    out.s = fs / 100
    out.x = this.wx(cx)
    out.y = this.wy(cy)
    out.z = 0
    out.rx = -0.06
    out.ry = a ? 0.22 : 0.16
    out.rz = 0
    return out
  }

  // ─────────────────────────────────────────────────────────── public ──

  playVideos(on: boolean) {
    for (const v of this.videos) {
      if (on && !this.idle) v.play().catch(() => {})
      else v.pause()
    }
  }

  // ─────────────────────────────────────────────────────────── frame ──

  private tmpA = newPose()
  private tmpB = newPose()
  private tmpC = newPose()

  render(dt: number) {
    dt = Math.min(dt, 1 / 20)
    this.time += this.reduced ? 0 : dt
    const t = this.time
    const S = state

    // Nothing left on stage: stop drawing until something comes back.
    const offstage = S.exit >= 0.999 && S.dark < 0.01
    if (offstage) {
      if (!this.idle) {
        this.idle = true
        this.renderer.clear()
        this.playVideos(false)
      }
      return
    }
    if (this.idle) {
      this.idle = false
      this.playVideos(true)
    }

    this.pointer.x = damp(this.pointer.x, this.pointer.tx, 3, dt)
    this.pointer.y = damp(this.pointer.y, this.pointer.ty, 3, dt)
    // Momentum: the letters lean into a fast scroll and settle when it stops.
    S.velocity *= Math.exp(-dt * 6)
    this.vel = damp(this.vel, THREE.MathUtils.clamp(S.velocity, -60, 60), 5, dt)
    const lean = this.reduced ? 0 : this.vel * 0.0045
    const px = this.reduced ? 0 : this.pointer.x
    const py = this.reduced ? 0 : this.pointer.y

    const split = easeIO(clamp01(S.split))
    const stairs = easeIO(clamp01(S.stairs))
    const wire = easeIO(clamp01(S.wire))
    const exit = easeIO(clamp01(S.exit))
    const intro = easeOut(clamp01(S.intro))
    const pickA = Math.max(0, -S.pick)
    const pickK = Math.max(0, S.pick)

    let lo = Infinity
    let hi = -Infinity
    let floorY = Infinity
    let maxS = 0
    for (const L of Object.values(this.letters)) {
      const a = L.id === 'A'
      const p = L.pose
      this.domPose(L, this.tmpA)
      this.splitPose(L, this.tmpB)
      mixPose(this.tmpA, this.tmpB, split, p)
      this.stairsPose(L, this.tmpC)
      mixPose(p, this.tmpC, stairs, p)

      // Live thing: the chosen letter steps forward, the other gives way.
      const mine = a ? pickA : pickK
      const theirs = a ? pickK : pickA
      const dir = a ? 1 : -1
      const live = split * (1 - stairs)
      // It comes toward the visitor and turns to face them, but never into the text column.
      p.x -= (dir * this.vw * 0.02 * mine + dir * this.vw * 0.05 * theirs) * live / 100
      p.s *= 1 + (0.02 * mine - 0.1 * theirs) * live
      p.ry += -dir * 0.3 * mine * live
      p.z += 0.3 * mine * live

      // Strategy: turn to an axonometric so the drawing shows its depth.
      p.rx += -0.34 * wire
      p.ry += (a ? 0.7 : 0.55) * wire

      // Image: a new pose for every photograph.
      p.rx += S.poseX
      p.ry += S.poseY * (a ? 1 : 0.85)
      p.rz += S.poseZ

      // Rising on arrival, sinking on exit.
      p.y -= (1 - intro) * (this.vh / 100) * 0.55
      p.rx += (1 - intro) * 0.45
      p.y -= exit * (this.vh / 100) * 0.95
      p.rx += exit * 0.5

      // Idle breath and the visitor's hand.
      if (!this.reduced) {
        p.ry += Math.sin(t * 0.45 + L.phase) * 0.035 + px * 0.12
        p.rx += Math.sin(t * 0.33 + L.phase) * 0.015 - py * 0.07 + lean * (a ? 1 : 0.8)
      }

      L.group.position.set(p.x, p.y, p.z)
      L.group.rotation.set(p.rx, p.ry, p.rz)
      L.group.scale.setScalar(p.s)

      const half = (FONT.capHeight / 2) * p.s
      lo = Math.min(lo, p.y - half)
      hi = Math.max(hi, p.y + half)
      floorY = Math.min(floorY, p.y - half * Math.cos(p.rx))
      maxS = Math.max(maxS, p.s)

      // Digital / Visibility visibility switches.
      const vox = clamp01(S.vox)
      L.vox.visible = vox > 0.01
      L.solid.visible = !L.vox.visible
      L.lines.visible = wire > 0.002 && !L.vox.visible
      const sh = L.voxMat.userData.shader as THREE.WebGLProgramParametersWithUniforms | undefined
      if (sh) {
        sh.uniforms.uVox.value = vox
        sh.uniforms.uTime.value = t
      }
      this.layoutEcho(L, clamp01(S.echo))
    }

    // Identity: the solid is built up from the floor, through the drawing.
    const build = clamp01(S.build)
    if (build < 0.999) {
      const box = new THREE.Box3()
      for (const L of Object.values(this.letters)) box.expandByObject(L.solid)
      lo = box.min.y
      hi = box.max.y
    }
    this.clip.constant = build >= 0.999 ? 1000 : build <= 0.001 ? -1000 : THREE.MathUtils.lerp(lo - 0.02, hi + 0.02, build)
    const dark = clamp01(S.dark)
    for (const m of this.lineMats) {
      m.opacity = wire * 0.9
      m.color.copy(INK).lerp(BG, dark)
    }

    // The room follows the letters.
    this.floor.position.y = floorY
    this.wall.position.set(0, floorY + 30, -2.8 * maxS - 1.5)
    ;(this.wall.material as THREE.ShadowMaterial).opacity = 0.13 * (1 - dark)
    ;(this.floor.material as THREE.ShadowMaterial).opacity = 0.22 * (1 - dark)

    // Light: the key follows the visitor's hand; the show takes the house lights down.
    const cx = (this.letters.A.pose.x + this.letters.K.pose.x) / 2
    const cy = (this.letters.A.pose.y + this.letters.K.pose.y) / 2
    this.key.target.position.set(cx, cy, 0)
    this.key.position.set(cx - 7 + px * 6, cy + 8 - py * 4, 12)
    this.fill.target.position.set(cx, cy, 0)
    this.fill.position.set(cx + 10, cy + 2, 8)
    S.flash = Math.max(0, S.flash - dt * 3.2)
    const flash = S.flash
    // The flash lives in the picture, never over the words: a burst of exposure on the letters.
    this.renderer.toneMappingExposure = 1 + flash * 0.75
    this.key.intensity = THREE.MathUtils.lerp(2.6, 0.15, dark) * (0.35 + 0.65 * intro) + flash * 9
    this.fill.intensity = THREE.MathUtils.lerp(0.55, 0.05, dark) + flash * 2
    this.hemi.intensity = THREE.MathUtils.lerp(0.32, 0.04, dark) + flash * 1.5
    this.rim.target.position.set(cx, cy, 0)
    this.rim.position.set(cx + 3, cy + 9, -10)
    this.rim.intensity = 4.5 * dark
    this.scene.environmentIntensity = THREE.MathUtils.lerp(0.62, 0.12, dark) + flash * 0.6
    this.layoutShow(dark, floorY)

    // The A's screen: three clips, crossfading slowly (the sheet's own motion).
    this.updateScreen(dt, pickA, dark)
    // The K's silk drifts; faster when it is chosen.
    this.silkDrift += dt * (0.01 + pickK * 0.05 + Math.abs(this.vel) * 0.004)
    this.silk.offset.x = this.silkDrift
    this.silk.offset.y = Math.sin(t * 0.21) * 0.03
    this.kMat.sheenRoughness = 0.3 - pickK * 0.12

    this.renderer.render(this.scene, this.camera)
  }

  private layoutEcho(L: Letter, echo: number) {
    L.echo.visible = echo > 0.002
    if (!L.echo.visible) return
    const m = new THREE.Matrix4()
    const q = new THREE.Quaternion()
    const v = new THREE.Vector3()
    const s = new THREE.Vector3()
    L.echoOffsets.forEach((o, i) => {
      const d = o.length()
      const k = easeOut(clamp01(echo * 1.8 - d * 0.08))
      v.set(o.x * k, o.y * k, o.z * k)
      s.setScalar(k < 0.01 ? 0.0001 : 1)
      L.echo.setMatrixAt(i, m.compose(v, q, s))
    })
    L.echo.instanceMatrix.needsUpdate = true
  }

  private layoutShow(dark: number, floorY: number) {
    const on = dark > 0.01
    ;(['A', 'K'] as const).forEach((id, i) => {
      const L = this.letters[id]
      const spot = this.spots[i]
      const cone = this.cones[i]
      const pool = this.pools[i]
      cone.visible = pool.visible = on
      spot.intensity = 7 * dark
      if (!on) return
      const p = L.pose
      const r = (FONT.capHeight * p.s) * 0.62
      const h = (this.vh / 100) * 1.4
      spot.position.set(p.x, floorY + h, p.z + 1.2)
      spot.target.position.set(p.x, floorY + 0.2, p.z)
      cone.scale.set(r, h, r)
      cone.position.set(p.x, floorY + h / 2, p.z - 0.2)
      ;(cone.material as THREE.ShaderMaterial).uniforms.uOpacity.value = dark
      pool.scale.setScalar(r * 1.25)
      pool.position.set(p.x, floorY + 0.005, p.z - 0.1)
      ;(pool.material as THREE.ShaderMaterial).uniforms.uOpacity.value = dark
    })
  }

  private updateScreen(dt: number, pickA: number, dark: number) {
    const sh = this.aFace.userData.shader as THREE.WebGLProgramParametersWithUniforms | undefined
    const c = this.cross
    if (!this.reduced) {
      c.hold += dt
      if (c.hold > 5.5) {
        c.mix = Math.min(1, c.mix + dt / 1.6)
        if (c.mix >= 1) {
          c.cur = (c.cur + 1) % this.vtex.length
          c.mix = 0
          c.hold = 0
          this.aFace.emissiveMap = this.vtex[c.cur]
        }
      }
    }
    this.gain = damp(this.gain, 0.95 + pickA * 0.45 + dark * 0.35, 4, dt)
    if (sh) {
      sh.uniforms.uMap2.value = this.vtex[(c.cur + 1) % this.vtex.length]
      sh.uniforms.uMix.value = easeIO(c.mix)
      sh.uniforms.uGain.value = this.gain
    }
  }
}
