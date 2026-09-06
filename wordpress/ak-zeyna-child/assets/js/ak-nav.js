/*!
 * AK navigation — the studio's own soft-navigation and page transition.
 *
 * Replaces Barba, which came from the parent theme, was configured through
 * Redux, and initialised only when a Redux-driven `.page--transitions`
 * element happened to be in the document. This owns the behaviour outright.
 *
 * THE RULE THIS FILE EXISTS TO KEEP
 * ---------------------------------
 * Navigation must never depend on JavaScript. Every link on the site is a
 * real <a href> to a real server-rendered URL. This script makes some of
 * those navigations softer; if it is absent, throws, times out, or meets a
 * page it does not understand, the browser navigates normally and the visitor
 * notices nothing except the absence of an animation.
 *
 * Every failure path below ends in location.assign(). There is no path that
 * ends in "nothing happened".
 *
 * WHAT IT DELIBERATELY DOES NOT INTERCEPT
 * ---------------------------------------
 * External origins, downloads, targets, modified clicks (new tab, save),
 * non-GET, hashes on the current page, wp-admin and wp-login, anything
 * marked data-no-swap, and — the one most bespoke implementations get wrong —
 * any page whose assets differ from the current one. Contact loads Contact
 * Form 7's script; soft-swapping into it from the homepage would produce a
 * form with no JavaScript behind it. Missing stylesheets are injected;
 * missing scripts hand the navigation back to the browser.
 *
 * Form submissions are not intercepted at all. A POST that half-works is
 * worse than one that reloads.
 */
(function () {
  'use strict'

  if (window.AKNav) return

  var doc = document
  var root = doc.documentElement
  var CONTAINER = '[data-ak-container]'
  var TIMEOUT = 8000

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)')
  var supported = (function () {
    // Probing by property NAME is not probing the API. `'fetch' in window` is
    // true even when reading window.fetch throws.
    try {
      return typeof window.fetch === 'function' &&
        typeof window.AbortController === 'function' &&
        typeof window.DOMParser === 'function' &&
        !!(window.history && window.history.pushState)
    } catch (e) {
      return false
    }
  })()

  var inFlight = null
  var busy = false

  function container() {
    return doc.querySelector(CONTAINER)
  }

  function emit(name, detail) {
    try {
      doc.dispatchEvent(new CustomEvent('ak:' + name, { detail: detail || {} }))
    } catch (e) { /* an old browser: the swap still happens, listeners just miss it */ }
  }

  /* Asset parity ---------------------------------------------------------
   * Compare what the incoming document loads against what is already here.
   * Stylesheets can be added safely. Scripts cannot: a script that has
   * already run cannot be re-run for a new page, and a script that is
   * missing means the new page is not actually functional. */
  function assetUrls(scope, selector, attr) {
    var out = []
    var nodes = scope.querySelectorAll(selector)
    for (var i = 0; i < nodes.length; i++) {
      var v = nodes[i].getAttribute(attr)
      if (v) out.push(v.split('?')[0])
    }
    return out
  }

  function missingScripts(incoming) {
    var have = assetUrls(doc, 'script[src]', 'src')
    var want = assetUrls(incoming, 'script[src]', 'src')
    return want.filter(function (u) { return have.indexOf(u) === -1 })
  }

  function addMissingStyles(incoming) {
    var have = assetUrls(doc, 'link[rel="stylesheet"][href]', 'href')
    var links = incoming.querySelectorAll('link[rel="stylesheet"][href]')
    for (var i = 0; i < links.length; i++) {
      var href = links[i].getAttribute('href')
      if (have.indexOf(href.split('?')[0]) !== -1) continue
      var link = doc.createElement('link')
      link.rel = 'stylesheet'
      link.href = href
      doc.head.appendChild(link)
    }
  }

  /* Which clicks are ours -------------------------------------------------- */
  function interceptable(link, event) {
    if (!supported || busy) return false
    if (event.defaultPrevented) return false
    if (event.button !== 0) return false
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false
    if (!link || !link.href) return false
    if (link.target && link.target !== '_self') return false
    if (link.hasAttribute('download')) return false
    if (link.getAttribute('rel') === 'external') return false
    if (link.closest('[data-no-swap]')) return false

    var url
    try { url = new URL(link.href, location.href) } catch (e) { return false }
    if (url.origin !== location.origin) return false
    if (url.protocol !== 'http:' && url.protocol !== 'https:') return false

    // A hash on the page we are already on is the browser's job.
    if (url.pathname === location.pathname && url.search === location.search && url.hash) return false
    // Same URL entirely: let the browser decide (usually nothing).
    if (url.href === location.href) return false

    // WordPress's own surfaces, and anything that is plainly a file.
    if (/\/wp-admin\/|\/wp-login\.php|\/wp-json\//.test(url.pathname)) return false
    if (/\.(zip|pdf|jpe?g|png|gif|svg|webp|mp4|webm|txt|xml|csv|docx?|xlsx?)$/i.test(url.pathname)) return false

    return url
  }

  /* The overlay ----------------------------------------------------------- */
  function overlay() {
    return doc.querySelector('.ak-transition')
  }

  function cover() {
    var el = overlay()
    if (!el || reduced.matches) return Promise.resolve()
    el.classList.add('is-covering')
    return new Promise(function (resolve) {
      var done = false
      function finish() { if (!done) { done = true; resolve() } }
      el.addEventListener('transitionend', finish, { once: true })
      // Never wait on a transition that may not fire (display:none, a
      // stylesheet that failed, a browser that skipped it).
      window.setTimeout(finish, 420)
    })
  }

  function uncover() {
    var el = overlay()
    if (!el) return
    el.classList.add('is-clearing')
    el.classList.remove('is-covering')
    window.setTimeout(function () { el.classList.remove('is-clearing') }, 520)
  }

  /* Metadata the swap must carry across ------------------------------------ */
  function adoptHead(incoming) {
    doc.title = incoming.title || doc.title

    var lang = incoming.documentElement.getAttribute('lang')
    if (lang) root.setAttribute('lang', lang)

    var pairs = [
      ['meta[name="description"]', 'content'],
      ['link[rel="canonical"]', 'href'],
      ['meta[property="og:title"]', 'content'],
      ['meta[property="og:description"]', 'content'],
      ['meta[property="og:url"]', 'content'],
    ]
    pairs.forEach(function (pair) {
      var from = incoming.querySelector(pair[0])
      var to = doc.querySelector(pair[0])
      if (from && to) to.setAttribute(pair[1], from.getAttribute(pair[1]) || '')
      else if (from && !to) doc.head.appendChild(from.cloneNode(true))
    })

    // The body class carries WordPress's page context, which CSS uses.
    var incomingBody = incoming.body
    if (incomingBody) doc.body.className = incomingBody.className

    // The admin bar's Edit link points at the page being viewed. Left alone it
    // would edit whichever page the visitor first landed on.
    var edit = doc.querySelector('#wp-admin-bar-edit a')
    var incomingEdit = incoming.querySelector('#wp-admin-bar-edit a')
    var editItem = doc.querySelector('#wp-admin-bar-edit')
    if (edit && incomingEdit) edit.href = incomingEdit.href
    else if (editItem && !incomingEdit) editItem.hidden = true
    else if (editItem) editItem.hidden = false
  }

  function announce() {
    var region = doc.getElementById('ak-route-announcer')
    if (region) region.textContent = doc.title
  }

  function focusMain(next) {
    // Focus moves to the new content so a keyboard or screen-reader user is
    // not left at the top of a document they have already navigated past.
    if (!next) return
    if (!next.hasAttribute('tabindex')) next.setAttribute('tabindex', '-1')
    try { next.focus({ preventScroll: true }) } catch (e) { next.focus() }
  }

  /* Scroll restoration ------------------------------------------------------ */
  if ('scrollRestoration' in history) history.scrollRestoration = 'manual'

  function rememberScroll() {
    try {
      history.replaceState(
        Object.assign({}, history.state, { akScroll: window.scrollY }),
        '',
        location.href
      )
    } catch (e) { /* state that cannot be cloned: scroll simply resets */ }
  }

  /* The navigation itself --------------------------------------------------- */
  function hardNavigate(url) {
    location.assign(url.href || url)
  }

  function go(url, opts) {
    // Everything below runs AFTER preventDefault() has cancelled the browser's
    // own navigation, so a synchronous throw anywhere in here leaves the
    // visitor exactly where they were, having clicked a link that did nothing.
    // Found by deliberately breaking window.fetch: `supported` was true
    // because `'fetch' in window` is true, and the call site threw.
    try {
      return navigate(url, opts)
    } catch (err) {
      emit('navigate:error', { error: String(err) })
      hardNavigate(url)
    }
  }

  function navigate(url, opts) {
    opts = opts || {}
    if (busy) return
    busy = true
    root.classList.add('ak-navigating')
    doc.body.setAttribute('aria-busy', 'true')
    emit('navigate:start', { to: url.href })

    if (inFlight) inFlight.abort()
    var controller = new AbortController()
    inFlight = controller
    var timer = window.setTimeout(function () { controller.abort() }, TIMEOUT)

    if (!opts.pop) rememberScroll()

    var fetched = fetch(url.href, {
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'ak-nav' },
      signal: controller.signal,
    })
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status)
        // A redirect to another origin, or to a URL we would not have
        // intercepted, is the browser's business.
        var landed = new URL(res.url, location.href)
        if (landed.origin !== location.origin) throw new Error('cross-origin redirect')
        return res.text().then(function (html) { return { html: html, url: landed } })
      })

    Promise.all([fetched, cover()])
      .then(function (results) {
        var payload = results[0]
        var incoming = new DOMParser().parseFromString(payload.html, 'text/html')
        var next = incoming.querySelector(CONTAINER)
        if (!next) throw new Error('no AK container in the response')

        var missing = missingScripts(incoming)
        if (missing.length) throw new Error('page needs scripts not loaded here: ' + missing[0])

        addMissingStyles(incoming)

        var current = container()
        emit('navigate:before-swap', { container: current })

        if (!opts.pop) {
          history.pushState({ akScroll: 0 }, '', payload.url.href)
        }

        adoptHead(incoming)
        current.replaceWith(next)

        var y = opts.pop && opts.scroll != null ? opts.scroll : 0
        window.scrollTo(0, y)

        emit('navigate:after-swap', { container: next })
        announce()
        focusMain(next)
        uncover()
      })
      .catch(function (err) {
        emit('navigate:error', { error: String(err) })
        // The visitor asked to go somewhere. They go there.
        if (err && err.name === 'AbortError' && !controller.signal.aborted) return
        hardNavigate(url)
      })
      .then(function () {
        window.clearTimeout(timer)
        busy = false
        inFlight = null
        root.classList.remove('ak-navigating')
        doc.body.removeAttribute('aria-busy')
      })
  }

  /* Wiring ------------------------------------------------------------------ */
  if (supported) {
    doc.addEventListener('click', function (e) {
      var link = e.target.closest && e.target.closest('a[href]')
      if (!link) return
      var url = interceptable(link, e)
      if (!url) return
      e.preventDefault()
      go(url)
    })

    window.addEventListener('popstate', function (e) {
      // A hash-only change within the same document is not a navigation.
      if (location.pathname === (window.AKNav._path || location.pathname) && location.hash) {
        window.AKNav._path = location.pathname
        return
      }
      window.AKNav._path = location.pathname
      go(new URL(location.href), { pop: true, scroll: (e.state && e.state.akScroll) || 0 })
    })

    window.addEventListener('beforeunload', rememberScroll)
  }

  window.AKNav = {
    supported: supported,
    _path: location.pathname,
    navigate: function (href) {
      var url
      try { url = new URL(href, location.href) } catch (e) { return hardNavigate(href) }
      if (!supported || url.origin !== location.origin) return hardNavigate(url)
      go(url)
    },
  }
})()
