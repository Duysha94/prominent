/**
 * Zeyna exit — the assertions that stop it coming back.
 *
 * The exit is not a one-off cleanup, it is a property the theme has to keep.
 * A parent handle re-enters the page the moment anything declares it as a
 * dependency; a parent hook re-attaches the moment someone removes a
 * remove_action; a parent template takes over the moment a route appears that
 * the child does not cover. Each of those has happened at least once during
 * this build.
 *
 * Usage:  node zeyna-exit-test.mjs      (expects WordPress on :9410)
 */
import { chromium } from 'playwright'
import { readFileSync, readdirSync } from 'node:fs'

const B = 'http://127.0.0.1:9410'
const ROUTES = [
  '/', '/about/', '/services/', '/work/', '/work/london-fashion-day/',
  '/journal/', '/journal/what-it-costs-to-show-at-a-fashion-week/',
  '/contact/', '/privacy/', '/?s=fashion', '/journal/category/strategy/',
  '/journal/2026/', '/nope-404/',
]

let pass = 0, fail = 0
const ok = (n, c, extra) => { c ? pass++ : fail++; console.log(`${c ? 'PASS' : 'FAIL'}  ${n}${!c && extra ? '  — ' + extra : ''}`) }

/* ── Source assertions ─────────────────────────────────────────────────────
 * Four properties of the exit that NO browser check can reach on this
 * install, because the code paths only fire with plugins that are not here:
 * WooCommerce template resolution, the Pe Core `project-categories`
 * taxonomy, and every parent helper that is gated on `class_exists("Redux")`.
 *
 * That gate is exactly why they need asserting. On this test site the parent's
 * helpers return early and look harmless; on the studio's real site, where
 * Redux and Pe Core are active, they run. "It renders fine here" has been the
 * wrong answer to this question at every stage of the build, so these are
 * checked against the source instead.
 * ---------------------------------------------------------------------- */
{
  const T = new URL('../ak-zeyna-child/', import.meta.url)
  const read = f => { try { return readFileSync(new URL(f, T), 'utf8') } catch { return '' } }
  const php = readdirSync(T, { recursive: true })
    .filter(f => typeof f === 'string' && f.endsWith('.php'))
  const src = php.map(f => [f, read(f)])

  // A live call to a parent helper is a parent dependency, whatever it is
  // gated on. Comments naming them are fine and deliberate — the exit is
  // documented in place — so strip comment lines before looking.
  const calls = src.flatMap(([f, body]) => body.split('\n')
    .filter(l => !/^\s*(\*|\/\/|#)/.test(l))
    .filter(l => /\bzeyna_[a-z_]+\s*\(/.test(l))
    .map(l => `${f}: ${l.trim().slice(0, 60)}`))
  ok('source: no live calls to parent theme helpers', calls.length === 0, calls.join(' | '))

  // The frontend must not filter a plugin-owned option into something other
  // than what is stored.
  const forcing = src.filter(([, b]) => /add_filter\(\s*['"]option_pe-redux/.test(b)).map(([f]) => f)
  ok('source: nothing filters option_pe-redux', forcing.length === 0, forcing.join(', '))

  // remove_all_filters on a plugin-owned option destroys OTHER components'
  // callbacks for the rest of the request. The raw read goes through the
  // options table instead.
  const nuking = src.filter(([, b]) => /^\s*remove_all_filters\(/m.test(b)).map(([f]) => f)
  ok('source: no remove_all_filters on plugin options', nuking.length === 0, nuking.join(', '))

  // Every parent template must have a child template that outranks it.
  const parentTemplates = ['404.php', 'archive-portfolio.php', 'archive.php', 'footer.php',
    'header.php', 'index.php', 'page.php', 'search.php', 'single-portfolio.php', 'single.php',
    'taxonomy-project-categories.php']
  const uncovered = parentTemplates.filter(f => !read(f))
  ok('source: every reachable parent template is overridden', uncovered.length === 0, uncovered.join(', '))
  // comments.php and sidebar.php are the deliberate exception: they are only
  // ever loaded by comments_template() and get_sidebar(), and the child calls
  // neither. Assert that, rather than shipping two unused templates.
  const summons = src.flatMap(([f, b]) => /comments_template\(|get_sidebar\(/.test(b) ? [f] : [])
  ok('source: nothing summons the parent comments/sidebar templates', summons.length === 0, summons.join(', '))

  // WooCommerce resolves child → parent → plugin. The parent ships a full
  // override set, so without this filter a shop renders through Zeyna.
  ok('source: parent WooCommerce templates are refused',
    /add_filter\(\s*\n?\s*['"]woocommerce_locate_template/.test(read('inc/zeyna-exit.php')))
}

const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' })

for (const route of ROUTES) {
  const ctx = await b.newContext({ viewport: { width: 1440, height: 1000 } })
  const p = await ctx.newPage()
  const parentAssets = []
  p.on('response', r => { if (/\/themes\/zeyna\//.test(r.url())) parentAssets.push(r.url().split('/themes/zeyna/')[1].split('?')[0]) })
  await p.goto(B + route, { waitUntil: 'networkidle' })
  const html = await p.content()
  const tag = route.padEnd(50)

  ok(`${tag} no parent theme assets`, parentAssets.length === 0, parentAssets.join(', '))
  ok(`${tag} no Zeyna transition markup`, !/page--transitions|data-barba|pt__overlay/.test(html))
  ok(`${tag} no parent menu classes`, !/pe--styled--object|text--anim--inner/.test(html))
  ok(`${tag} no parent grid classes`, !/class="[^"]*\bpe-(wrapper|section|col-\d|items-)/.test(html))
  ok(`${tag} AK container present`, /data-ak-container/.test(html))
  // The boot class is AK's own mechanism and carries AK's own name; the
  // palette probe the parent's scripts.js read is gone entirely.
  ok(`${tag} no parent boot vocabulary`, !/first--load|layout--colors|layout--switched/.test(html))

  await ctx.close()
}

/* Runtime globals: nothing of the parent's may exist. */
{
  const ctx = await b.newContext({ viewport: { width: 1440, height: 1000 } })
  const p = await ctx.newPage()
  await p.goto(B + '/', { waitUntil: 'networkidle' })
  await p.waitForTimeout(800)
  const g = await p.evaluate(() => ({
    barba: typeof window.barba,
    zeynaLenis: typeof window.zeynaLenis,
    Lenis: typeof window.Lenis,
    jQuery: typeof window.jQuery,
    THREE: typeof window.THREE,
    gsap: typeof window.gsap,
    AK: typeof window.AK,
    AKNav: typeof window.AKNav,
    zeynaKeys: Object.keys(window).filter(k => /zeyna/i.test(k)),
  }))
  ok('runtime: no Barba', g.barba === 'undefined')
  ok('runtime: no Zeyna Lenis instance', g.zeynaLenis === 'undefined')
  ok('runtime: no Lenis library', g.Lenis === 'undefined')
  ok('runtime: no jQuery (it was only there for the parent)', g.jQuery === 'undefined')
  ok('runtime: no THREE', g.THREE === 'undefined')
  ok('runtime: no zeyna* globals', g.zeynaKeys.length === 0, g.zeynaKeys.join(','))
  ok('runtime: AK owns GSAP', g.gsap === 'object')
  ok('runtime: AK modules present', g.AK === 'object' && g.AKNav === 'object')

  /* The two things a Redux-driven parent would inject. */
  const html = await p.content()
  ok('no parent body classes', !/page--loader--active|page--transitions--active|body--grained|loader__/.test(html))
  ok('no parent header <style> block', !/\.site-title[^{]*\{[^}]*color/.test(html.split('</head>')[0] || ''))

  /* The Zeyna variable bridge. Ten custom properties used to be defined for
   * a parent stylesheet that no longer loads. A theme that still declares
   * them is still shaped around the parent, so assert they resolve to
   * nothing — and assert an AK token in the same breath, so a broken
   * stylesheet cannot make this pass by making everything empty. */
  const vars = await p.evaluate(() => {
    const cs = getComputedStyle(document.documentElement)
    const read = n => cs.getPropertyValue(n).trim()
    return {
      bridge: ['--mainColor', '--secondaryColor', '--mainBackground', '--secondaryBackground',
        '--linesColor', '--customMainColor', '--customSecondaryColor', '--customMainBackground',
        '--customSecondaryBackground', '--customLinesColor'].filter(read),
      ak: read('--bg'),
    }
  })
  ok('no Zeyna variable bridge on :root', vars.bridge.length === 0, vars.bridge.join(','))
  ok('AK tokens do resolve (the check above is not vacuous)', vars.ak.length > 0)

  /* Mode resolution must not consult the parent's ACF field. The inline
   * head script is the whole decision, and it should mention neither. */
  const headScript = (html.split('</head>')[0] || '')
  ok('mode script reads no parent configuration',
    /localStorage\.getItem\('ak-theme'\)/.test(headScript) &&
    !/layout--switched|page_layout/.test(headScript))

  /* Browser back/forward across a soft navigation. */
  await p.locator('a[href$="/work/"]').first().click(); await p.waitForTimeout(1500)
  const atWork = p.url().includes('/work/')
  await p.goBack(); await p.waitForTimeout(1500)
  const backHome = !p.url().includes('/work/')
  await p.goForward(); await p.waitForTimeout(1500)
  ok('soft navigation: forward works', atWork && backHome && p.url().includes('/work/'))
  ok('soft navigation: content matches the URL after back/forward',
    (await p.locator('main').innerText()).includes('Selected records'))
  await ctx.close()
}

await b.close()
console.log(`\n${pass} passed, ${fail} failed`)
process.exit(fail ? 1 : 0)
