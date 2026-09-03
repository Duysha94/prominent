/*!
 * AK Brand Development Studio — motion layer for WordPress.
 *
 * Zeyna already loads GSAP (with SplitText, ScrollTrigger, Flip, Observer),
 * Lenis and Barba, so this file loads none of them. It adds the studio's own
 * devices on top and — critically — tears them down and rebuilds them around
 * Barba's container swap, which is what breaks on most bespoke work bolted
 * onto a transition theme.
 *
 * Zeyna puts data-barba="container" on <main id="primary"> and calls
 * get_footer() after it, so the footer lives OUTSIDE the container and
 * persists. The Seam therefore lives in the footer and initialises once.
 */
(function () {
  'use strict'

  // Run-once guard: a page optimizer inlining the script beside the enqueued
  // copy would otherwise double-bind every Barba hook and listener.
  if (window.AK) return

  var doc = document
  var root = doc.documentElement
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)')

  /* ── Registry ────────────────────────────────────────────────────────────
   * Components register themselves instead of the transition code hard-coding
   * every one of them. Each phase runs in registration order inside try/catch,
   * so one throwing handler can never break navigation.
   * -------------------------------------------------------------------- */
  var registry = new Map()

  var AK = (window.AK = {
    register: function (entry) {
      registry.set(entry.id, entry)
      if (entry.init) safely(entry.init.bind(entry), currentContainer())
    },
    unregister: function (id) {
      var e = registry.get(id)
      if (e && e.cleanup) safely(e.cleanup.bind(e), currentContainer())
      registry.delete(id)
    },
    reducedMotion: function () {
      return reduced.matches
    }
  })

  function currentContainer() {
    return doc.querySelector('[data-barba="container"]') || doc.body
  }

  function safely(fn, container) {
    try {
      fn(container)
    } catch (err) {
      if (window.console) console.warn('[AK]', err)
    }
  }

  function runPhase(name, container) {
    registry.forEach(function (entry) {
      if (entry[name]) safely(entry[name].bind(entry), container)
    })
  }

  /* ── Named eases ─────────────────────────────────────────────────────────
   * The same four curves as the CSS tokens, so a GSAP tween and a CSS
   * transition on one element are physically identical. Registered as plain
   * functions because Zeyna's GSAP bundle does not include CustomEase.
   * -------------------------------------------------------------------- */
  function bezier(x1, y1, x2, y2) {
    return function (t) {
      var lo = 0, hi = 1, u = t
      for (var i = 0; i < 20; i++) {
        var mt = 1 - u
        var x = 3 * mt * mt * u * x1 + 3 * mt * u * u * x2 + u * u * u
        if (Math.abs(x - t) < 0.0005) break
        if (x < t) lo = u; else hi = u
        u = (lo + hi) / 2
      }
      var m = 1 - u
      return 3 * m * m * u * y1 + 3 * m * u * u * y2 + u * u * u
    }
  }

  if (window.gsap) {
    gsap.registerEase('ak.cut', bezier(0.16, 1, 0.3, 1))
    gsap.registerEase('ak.thread', bezier(0.65, 0.05, 0.36, 1))
    gsap.registerEase('ak.snap', bezier(0.34, 1.56, 0.64, 1))
    gsap.registerEase('ak.drape', bezier(0.22, 0.61, 0.36, 1))
  }

  /* ── Mode switch: ATELIER / RUNWAY ───────────────────────────────────────
   * Zeyna has no light/dark mode — verified, there is not one selector for it
   * anywhere in the theme. This is ours.
   *
   * State lives on <html data-theme>, NOT on <body>. That is deliberate: Barba
   * replaces body classes wholesale on every navigation, so a mode kept on the
   * body flashes back on every soft navigation. An attribute on the
   * documentElement survives untouched.
   *
   * First paint is handled by a blocking inline script in <head> (see
   * inc/theme-mode.php), so a dark visitor never sees a white flash.
   * -------------------------------------------------------------------- */
  function applyMode(mode, persist) {
    root.setAttribute('data-theme', mode)
    // Only an explicit click persists. Writing the OS-derived value would
    // turn a default into a stored "choice" and pin the visitor to their
    // first-visit mode forever.
    if (persist) {
      try {
        localStorage.setItem('ak-theme', mode)
      } catch (e) { /* private mode: the choice lasts this visit */ }
    }

    var dark = mode === 'dark'
    Array.prototype.forEach.call(doc.querySelectorAll('[data-ak-mode]'), function (btn) {
      btn.setAttribute('aria-pressed', String(dark))
      btn.setAttribute('aria-label', 'Switch to ' + (dark ? 'Atelier (light)' : 'Runway (dark)') + ' mode')
      var label = btn.querySelector('.ak-mode__label')
      if (label) label.textContent = dark ? 'Runway' : 'Atelier'
    })
  }

  var chosen = false

  doc.addEventListener('click', function (e) {
    var btn = e.target.closest ? e.target.closest('[data-ak-mode]') : null
    if (!btn) return
    e.preventDefault()
    chosen = true
    applyMode(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark', true)
  })

  var stored = null
  try { stored = localStorage.getItem('ak-theme') } catch (e) { /* ignore */ }
  if (!stored) {
    var osMode = window.matchMedia('(prefers-color-scheme: dark)')
    if (osMode.addEventListener) {
      osMode.addEventListener('change', function (ev) {
        if (chosen) return
        applyMode(ev.matches ? 'dark' : 'light')
      })
    }
  }
  applyMode(root.getAttribute('data-theme') || 'light')

  // Insurance: if the header rendered no toggle (a builder header that
  // bypasses the menu filter), float one in so the two modes are always
  // reachable.
  if (!doc.querySelector('[data-ak-mode]')) {
    var fbToggle = doc.createElement('button')
    fbToggle.type = 'button'
    fbToggle.className = 'ak-mode ak-mode--floating'
    fbToggle.setAttribute('data-ak-mode', '')
    fbToggle.setAttribute('aria-pressed', 'false')
    fbToggle.innerHTML = '<span class="ak-mode__label">Atelier</span><span class="ak-mode__track" aria-hidden="true"><span class="ak-mode__knob"></span></span>'
    doc.body.appendChild(fbToggle)
    applyMode(root.getAttribute('data-theme') || 'light')
  }

  /* ── Cut text ────────────────────────────────────────────────────────────
   * The house headline reveal: each line split along its midline into two
   * halves that start displaced in opposite directions and close.
   *
   * The split is DISMANTLED on completion — clip released, duplicate hidden —
   * because display type at ~0.9 leading puts descenders outside the line box,
   * and clip-path clips to the element's own box no matter what padding a
   * parent carries. Clipping exists only while something is moving.
   * -------------------------------------------------------------------- */
  AK.register({
    id: 'ak:cut-text',
    _ctx: null,
    _timers: [],
    init: function (container) {
      if (!window.gsap || !window.SplitText || reduced.matches) return
      var self = this
      self._ctx = gsap.context(function () {
        Array.prototype.forEach.call(container.querySelectorAll('[data-ak-cut]'), function (host) {
          SplitText.create(host, {
            type: 'lines',
            linesClass: 'ak-cut-line',
            autoSplit: true,
            aria: 'auto',
            onSplit: function (split) {
              var halves = []
              split.lines.forEach(function (line) {
                var html = line.innerHTML
                line.innerHTML = ''
                var top = doc.createElement('span')
                top.className = 'ak-cut-half ak-cut-half--top'
                top.innerHTML = html
                var bottom = doc.createElement('span')
                bottom.className = 'ak-cut-half ak-cut-half--bottom'
                bottom.innerHTML = html
                line.appendChild(top)
                line.appendChild(bottom)
                halves.push([top, bottom])
              })

              var tl = gsap.timeline({
                defaults: { ease: 'ak.cut' },
                scrollTrigger: { trigger: host, start: 'top 86%', once: true },
                onComplete: function () {
                  split.lines.forEach(function (l) { l.setAttribute('data-cut-settled', '') })
                  halves.forEach(function (pair) {
                    pair[0].style.clipPath = 'none'
                    pair[1].style.display = 'none'
                  })
                }
              })

              halves.forEach(function (pair, i) {
                var at = i * 0.028
                tl.fromTo(pair[0], { xPercent: 4, yPercent: -46, opacity: 0 },
                  { xPercent: 0, yPercent: 0, opacity: 1, duration: 1.2 }, at)
                  .fromTo(pair[1], { xPercent: -4, yPercent: 46, opacity: 0 },
                    { xPercent: 0, yPercent: 0, opacity: 1, duration: 1.2 }, at)
              })

              // A reveal that starts at opacity 0 must never be able to
              // strand content if its trigger never fires — but only rescue
              // hosts actually IN the viewport: everything below the fold is
              // legitimately waiting for the visitor to scroll to it.
              self._timers.push(setTimeout(function () {
                var r = host.getBoundingClientRect()
                if (r.top < window.innerHeight && r.bottom > 0 &&
                    tl.progress() === 0 && !tl.isActive()) tl.play()
              }, 2500))

              return tl
            }
          })
        })
      }, container)
    },
    cleanup: function () {
      this._timers.forEach(clearTimeout)
      this._timers = []
      if (this._ctx) this._ctx.revert()
      this._ctx = null
    },
    reinit: function () {
      if (window.ScrollTrigger) ScrollTrigger.refresh()
    }
  })

  /* ── Measure frame ───────────────────────────────────────────────────────
   * Hover and focus do the work on fine pointers (pure CSS). On touch the
   * annotations appear when the block scrolls into view, so no figure is ever
   * hover-only.
   * -------------------------------------------------------------------- */
  AK.register({
    id: 'ak:measure',
    _io: null,
    init: function (container) {
      var frames = container.querySelectorAll('[data-ak-measure]')
      if (!frames.length) return
      if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) return

      this._io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          entry.target.classList.toggle('is-measured', entry.isIntersecting)
        })
      }, { threshold: 0.55 })

      var io = this._io
      Array.prototype.forEach.call(frames, function (f) { io.observe(f) })
    },
    cleanup: function () {
      if (this._io) this._io.disconnect()
      this._io = null
    }
  })

  /* ── Tilt ────────────────────────────────────────────────────────────────
   * Pointer-tracking 3D tilt with a travelling sheen (the sheen itself is
   * CSS, driven by --tilt-x/--tilt-y). Fine pointers only, never under
   * reduced motion — on touch the cards simply hold still. The angles are
   * driven through gsap.quickTo, so the card glides rather than jitters.
   * -------------------------------------------------------------------- */
  AK.register({
    id: 'ak:tilt',
    _bound: [],
    init: function (container) {
      if (!window.gsap || reduced.matches) return
      if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return
      var self = this
      Array.prototype.forEach.call(container.querySelectorAll('[data-ak-tilt]'), function (el) {
        gsap.set(el, { transformPerspective: 900 })
        var rx = gsap.quickTo(el, 'rotationX', { duration: 0.5, ease: 'power2.out' })
        var ry = gsap.quickTo(el, 'rotationY', { duration: 0.5, ease: 'power2.out' })
        function move (e) {
          var r = el.getBoundingClientRect()
          if (!r.width || !r.height) return
          var px = (e.clientX - r.left) / r.width - 0.5
          var py = (e.clientY - r.top) / r.height - 0.5
          rx(py * -6)
          ry(px * 8)
          el.style.setProperty('--tilt-x', (px * 100 + 50) + '%')
          el.style.setProperty('--tilt-y', (py * 100 + 50) + '%')
        }
        function leave () { rx(0); ry(0) }
        el.addEventListener('pointermove', move)
        el.addEventListener('pointerleave', leave)
        self._bound.push({ el: el, move: move, leave: leave })
      })
    },
    cleanup: function () {
      this._bound.forEach(function (b) {
        b.el.removeEventListener('pointermove', b.move)
        b.el.removeEventListener('pointerleave', b.leave)
        if (window.gsap) gsap.set(b.el, { clearProps: 'transform' })
      })
      this._bound = []
    }
  })

  /* ── Line reveal ─────────────────────────────────────────────────────────
   * Every lead paragraph arrives line by line from behind a mask — the
   * standard grammar of a motion site, done with the SplitText the parent
   * already ships. The split is reverted on completion so the DOM (and
   * anything that copies text) stays clean.
   * -------------------------------------------------------------------- */
  AK.register({
    id: 'ak:lines',
    _ctx: null,
    _timers: [],
    init: function (container) {
      if (!window.gsap || !window.SplitText || !window.ScrollTrigger || reduced.matches) return
      var self = this
      self._ctx = gsap.context(function () {
        Array.prototype.forEach.call(container.querySelectorAll('.ak-lead'), function (host) {
          if (host.closest('.ak-prose')) return
          SplitText.create(host, {
            type: 'lines',
            mask: 'lines',
            linesClass: 'ak-line',
            autoSplit: true,
            aria: 'auto',
            onSplit: function (split) {
              var tl = gsap.timeline({
                scrollTrigger: { trigger: host, start: 'top 88%', once: true },
                onComplete: function () { split.revert() }
              })
              tl.fromTo(split.lines, { yPercent: 105 }, {
                yPercent: 0, duration: 0.9, ease: 'ak.drape', stagger: 0.07
              })
              // Same stranding rule as the cut text: only rescue what is
              // actually in the viewport.
              self._timers.push(setTimeout(function () {
                var r = host.getBoundingClientRect()
                if (r.top < window.innerHeight && r.bottom > 0 &&
                    tl.progress() === 0 && !tl.isActive()) tl.play()
              }, 2500))
              return tl
            }
          })
        })
      }, container)
    },
    cleanup: function () {
      this._timers.forEach(clearTimeout)
      this._timers = []
      if (this._ctx) this._ctx.revert()
      this._ctx = null
    },
    reinit: function () {
      if (window.ScrollTrigger) ScrollTrigger.refresh()
    }
  })

  /* ── Magnetic buttons ────────────────────────────────────────────────────
   * The buttons lean toward the cursor and spring home when it leaves.
   * Fine pointers only; .ak-btn transitions colours, never transform, so
   * GSAP owns the movement without a tug-of-war.
   * -------------------------------------------------------------------- */
  AK.register({
    id: 'ak:magnet',
    _bound: [],
    init: function (container) {
      if (!window.gsap || reduced.matches) return
      if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) return
      var self = this
      Array.prototype.forEach.call(container.querySelectorAll('.ak-btn'), function (el) {
        var qx = gsap.quickTo(el, 'x', { duration: 0.4, ease: 'power3.out' })
        var qy = gsap.quickTo(el, 'y', { duration: 0.4, ease: 'power3.out' })
        function move (e) {
          var r = el.getBoundingClientRect()
          qx((e.clientX - r.left - r.width / 2) * 0.22)
          qy((e.clientY - r.top - r.height / 2) * 0.22)
        }
        function leave () { qx(0); qy(0) }
        el.addEventListener('pointermove', move)
        el.addEventListener('pointerleave', leave)
        self._bound.push({ el: el, move: move, leave: leave })
      })
    },
    cleanup: function () {
      this._bound.forEach(function (b) {
        b.el.removeEventListener('pointermove', b.move)
        b.el.removeEventListener('pointerleave', b.leave)
        if (window.gsap) gsap.set(b.el, { clearProps: 'x,y' })
      })
      this._bound = []
    }
  })

  /* ── Scramble ────────────────────────────────────────────────────────────
   * Folio numbers and tech-pack figures decode into place as they enter
   * the viewport — the measurement being taken, in ~600ms of monospace
   * noise. Purely decorative characters, so nothing important is ever
   * unreadable, and reduced-motion users skip it entirely.
   * -------------------------------------------------------------------- */
  AK.register({
    id: 'ak:scramble',
    _io: null,
    _frames: [],
    init: function (container) {
      if (reduced.matches || !('IntersectionObserver' in window)) return
      var els = container.querySelectorAll('.ak-eyebrow__folio, .ak-index__folio, .ak-callout__value, .ak-chapter__measure b')
      if (!els.length) return
      var CHARS = '0123456789·—/AKX'
      var self = this
      this._io = new IntersectionObserver(function (entries, io) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return
          io.unobserve(entry.target)
          var el = entry.target
          var final = el.textContent
          if (!final || final.length > 24 || el.children.length) return
          var start = null
          var dur = 450 + 300 * Math.random()
          function tick (ts) {
            if (start === null) start = ts
            var p = (ts - start) / dur
            if (p >= 1) { el.textContent = final; return }
            var out = ''
            for (var i = 0; i < final.length; i++) {
              out += (i / final.length < p || ' ' === final[i]) ? final[i] : CHARS[(Math.random() * CHARS.length) | 0]
            }
            el.textContent = out
            self._frames.push(requestAnimationFrame(tick))
          }
          self._frames.push(requestAnimationFrame(tick))
        })
      }, { threshold: 0.6 })
      var io = this._io
      Array.prototype.forEach.call(els, function (el) { io.observe(el) })
    },
    cleanup: function () {
      if (this._io) this._io.disconnect()
      this._io = null
      this._frames.forEach(cancelAnimationFrame)
      this._frames = []
    }
  })

  /* ── Band velocity ───────────────────────────────────────────────────────
   * The facts band skews against scroll velocity — the strip behaves like
   * tape being pulled. The skew sits on .ak-band (the marquee keyframes
   * own the track's transform), and the loop only runs on pages that
   * actually have a band.
   * -------------------------------------------------------------------- */
  AK.register({
    id: 'ak:band-velocity',
    _raf: null,
    init: function (container) {
      if (reduced.matches) return
      var bands = container.querySelectorAll('.ak-band')
      if (!bands.length) return
      var self = this
      var last = window.scrollY
      var skew = 0
      function loop () {
        var y = window.scrollY
        var target = Math.max(-6, Math.min(6, (y - last) * 0.35))
        last = y
        skew += (target - skew) * 0.12
        if (Math.abs(skew) < 0.01) skew = 0
        for (var i = 0; i < bands.length; i++) {
          bands[i].style.transform = skew ? 'skewX(' + skew.toFixed(2) + 'deg)' : ''
        }
        self._raf = requestAnimationFrame(loop)
      }
      self._raf = requestAnimationFrame(loop)
    },
    cleanup: function () {
      if (this._raf) cancelAnimationFrame(this._raf)
      this._raf = null
    }
  })

  /* ── Count-up ────────────────────────────────────────────────────────────
   * The stat figures count up as they enter the viewport. The markup ships
   * with the FINAL value, so crawlers and reduced-motion visitors read the
   * real number with zero JavaScript; the animation only ever counts toward
   * what is already there.
   * -------------------------------------------------------------------- */
  AK.register({
    id: 'ak:count',
    _io: null,
    _frames: [],
    init: function (container) {
      if (reduced.matches || !('IntersectionObserver' in window)) return
      var els = container.querySelectorAll('[data-ak-count]')
      if (!els.length) return
      var self = this
      this._io = new IntersectionObserver(function (entries, io) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return
          io.unobserve(entry.target)
          var el = entry.target
          var final = parseFloat(el.getAttribute('data-ak-count'))
          if (isNaN(final)) return
          var padLen = el.textContent.length
          var start = null
          function fmt (n) {
            var s = String(Math.round(n))
            while (s.length < padLen) s = '0' + s
            return s
          }
          function tick (ts) {
            if (null === start) start = ts
            var p = Math.min(1, (ts - start) / 900)
            var e = 1 - Math.pow(1 - p, 3)
            el.textContent = fmt(final * e)
            if (p < 1) self._frames.push(requestAnimationFrame(tick))
          }
          self._frames.push(requestAnimationFrame(tick))
        })
      }, { threshold: 0.6 })
      var io = this._io
      Array.prototype.forEach.call(els, function (el) { io.observe(el) })
    },
    cleanup: function () {
      if (this._io) this._io.disconnect()
      this._io = null
      this._frames.forEach(cancelAnimationFrame)
      this._frames = []
    }
  })

  /* ── The loader ──────────────────────────────────────────────────────────
   * Dismiss the boot panel and release the document. This is the ONLY code
   * that removes `first--load` now: Zeyna's own remover lives inside its
   * loader script, which is gated on its markup, and this theme prints its
   * own panel instead. So the class is cleared here on window load, and
   * again on a hard 3.5s timeout — a stalled font or image must never leave
   * a visitor staring at the panel.
   * -------------------------------------------------------------------- */
  ;(function () {
    var panel = doc.querySelector('[data-ak-loader]')
    var released = false

    function release() {
      if (released) return
      released = true
      root.classList.remove('first--load')
      if (panel) {
        panel.classList.add('is-done')
        // Take it out of the tree once the fade has finished.
        window.setTimeout(function () {
          if (panel && panel.parentNode) panel.parentNode.removeChild(panel)
        }, 700)
      }
      if (window.ScrollTrigger) {
        window.setTimeout(function () { ScrollTrigger.refresh() }, 60)
      }
    }

    if (!panel) { release(); return }

    // Release on DOMContentLoaded, not on `load`. `load` waits for every
    // subresource — including the hero showreel and each platform preview —
    // so on a real connection the panel was holding the page for seconds
    // and taking Largest Contentful Paint down with it. The markup and the
    // stylesheet are all the page needs to be worth showing.
    var MIN = reduced.matches ? 0 : 700
    var started = performance.now()
    function releaseAfterMinimum() {
      window.setTimeout(release, Math.max(0, MIN - (performance.now() - started)))
    }

    if (doc.readyState !== 'loading') releaseAfterMinimum()
    else doc.addEventListener('DOMContentLoaded', releaseAfterMinimum, { once: true })

    window.setTimeout(release, 2500)   // hard ceiling
  })()

  /* ── The Seam ────────────────────────────────────────────────────────────
   * One orange thread down the page, bowing against scroll velocity on an
   * underdamped spring. It lives in the footer, outside Barba's container, so
   * it initialises once and survives every navigation.
   * -------------------------------------------------------------------- */
  ;(function seam() {
    var host = doc.querySelector('[data-ak-seam]')
    if (!host || !window.gsap) return
    var paths = host.querySelectorAll('path')
    var knot = host.querySelector('circle')
    if (paths.length < 2) return

    // A straight, taut thread. It used to bow against scroll velocity on a
    // spring; the curve read as a glitch rather than a material, so the
    // thread now stays true — the orange fill is scroll progress and the
    // knot is where you are in the document.
    var X = 30, H = 1000
    var d = 'M ' + X + ' 0 L ' + X + ' ' + H
    paths[0].setAttribute('d', d)
    paths[1].setAttribute('d', d)
    paths[1].setAttribute('pathLength', '1')
    paths[1].style.strokeDasharray = '1'
    paths[1].style.strokeDashoffset = '1'

    function draw() {
      var max = doc.documentElement.scrollHeight - window.innerHeight
      var prog = max > 0 ? Math.min(1, Math.max(0, window.scrollY / max)) : 0
      paths[1].style.strokeDashoffset = String(1 - prog)
      if (knot) knot.setAttribute('cy', String(prog * H))
    }

    gsap.ticker.add(draw)
  })()

  /* ── Barba bridge ────────────────────────────────────────────────────────
   * Without this, every navigation leaks ScrollTriggers and observers, scroll
   * positions drift, and pinned sections compute against the wrong document
   * height. Zeyna ships no teardown for third-party work.
   * -------------------------------------------------------------------- */
  function bindBarba() {
    if (!window.barba || !barba.hooks) return

    barba.hooks.beforeLeave(function (data) {
      runPhase('cleanup', (data && data.current && data.current.container) || currentContainer())
    })

    barba.hooks.afterEnter(function (data) {
      var container = (data && data.next && data.next.container) || currentContainer()
      runPhase('init', container)
      runPhase('reinit', container)

      // Accessibility across a soft navigation — Barba ships none of this.
      container.setAttribute('tabindex', '-1')
      container.focus({ preventScroll: true })
      var live = doc.getElementById('ak-route-announcer')
      if (live) live.textContent = doc.title
    })
  }

  if (doc.readyState === 'complete') bindBarba()
  else window.addEventListener('load', bindBarba)
})()
