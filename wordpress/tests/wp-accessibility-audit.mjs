/**
 * Accessibility audit — contrast, landmarks, headings, names, focus.
 *
 * Contrast is measured by resolving every colour through a 1x1 canvas, which
 * handles oklch() and any other CSS colour syntax. Parsing the computed string
 * with a number regex does NOT: it reads `oklch(0.162 0.008 58)` as if those
 * were RGB bytes and reports ~1.0:1 for text that is plainly readable. That
 * false alarm is why this file exists rather than a one-liner.
 *
 * Two classes of finding are expected and are NOT defects:
 *   · outline type (`color: transparent` + `-webkit-text-stroke`) — the
 *     computed colour is meaningless; legibility comes from the stroke
 *   · elements that are `display:none` at the tested viewport
 * Verify anything in those classes against painted pixels before "fixing" it.
 *
 * Usage:  node wp-accessibility-audit.mjs     (expects WordPress on :9410)
 *
 * The same audit as accessibility-audit.mjs, run against the real WordPress
 * render rather than the prototype. Both are kept: the prototype proves the
 * design, this proves the build, and a pass on one says nothing about the
 * other — the ported stylesheet meets different markup here.
 */
import { chromium } from 'playwright'
const B='http://127.0.0.1:9410'
const PAGES=['/','/work/','/services/','/work/london-fashion-day/','/about/','/contact/','/journal/','/journal/what-it-costs-to-show-at-a-fashion-week/','/privacy/','/?s=fashion','/?s=zzzznothing']
const b=await chromium.launch({executablePath:'/opt/pw-browsers/chromium-1194/chrome-linux/chrome'})
const findings=[]

// sRGB relative luminance → WCAG contrast ratio
const lum = c => { const s=c.map(v=>{v/=255; return v<=0.03928?v/12.92:Math.pow((v+0.055)/1.055,2.4)});
  return 0.2126*s[0]+0.7152*s[1]+0.0722*s[2] }
const ratio = (a,bb) => { const L1=lum(a),L2=lum(bb); return (Math.max(L1,L2)+0.05)/(Math.min(L1,L2)+0.05) }

for (const page of PAGES){
  const ctx=await b.newContext({viewport:{width:1440,height:1000}})
  const p=await ctx.newPage()
  await p.goto(B+page,{waitUntil:'networkidle'}); await p.waitForTimeout(400)

  const r = await p.evaluate(() => {
    const out={heads:[],noAlt:[],emptyLink:[],btnNoName:[],landmarks:{},langs:document.documentElement.lang,
      title:document.title, colours:[], tabbables:0, skip:false}
    // heading order
    document.querySelectorAll('h1,h2,h3,h4,h5,h6').forEach(h=>out.heads.push(+h.tagName[1]))
    document.querySelectorAll('img').forEach(i=>{ if(!i.hasAttribute('alt')) out.noAlt.push(i.src.slice(-30)) })
    document.querySelectorAll('a').forEach(a=>{ if(!a.textContent.trim() && !a.getAttribute('aria-label')) out.emptyLink.push(a.outerHTML.slice(0,60)) })
    document.querySelectorAll('button').forEach(x=>{ if(!x.textContent.trim() && !x.getAttribute('aria-label')) out.btnNoName.push(x.outerHTML.slice(0,60)) })
    out.landmarks={main:document.querySelectorAll('main').length,nav:document.querySelectorAll('nav').length,
      header:document.querySelectorAll('header').length,footer:document.querySelectorAll('footer').length}
    out.skip = !!document.querySelector('a[href^="#"][class*=skip]')
    out.tabbables = document.querySelectorAll('a[href],button,input,select,textarea,[tabindex]:not([tabindex="-1"])').length
    /* Resolve every colour through a 1x1 canvas, IN THE PAGE.
       This file's header has always said contrast must be resolved this way,
       and the code underneath parsed the computed string with a number regex
       instead. Against a stylesheet in hex that happens to work, because the
       computed value is already `rgb(...)`. Against this theme, whose tokens
       are oklch(), the regex read `oklch(0.162 0.008 58)` as three RGB bytes
       and reported ~1.0:1 for every pair on the site — 293 findings, none of
       them real. The canvas resolves any CSS colour syntax, including oklch,
       color-mix and lab. */
    const cv=document.createElement('canvas'); cv.width=cv.height=1
    const cx=cv.getContext('2d',{willReadFrequently:true})
    const resolve=(colour,over)=>{
      cx.clearRect(0,0,1,1)
      if(over){ cx.fillStyle=over; cx.fillRect(0,0,1,1) }
      cx.fillStyle=colour; cx.fillRect(0,0,1,1)
      const d=cx.getImageData(0,0,1,1).data
      return [d[0],d[1],d[2],d[3]/255]
    }

    // sample every distinct text style against its painted background
    const seen=new Set()
    document.querySelectorAll('body *').forEach(el=>{
      const t=(el.textContent||'').trim()
      if(!t || el.children.length) return
      const cs=getComputedStyle(el)

      /* Not painted, not a contrast question. Visually hidden text (skip
         links at rest, .screen-reader-text, aria live regions) is read aloud,
         never seen, and a zero-height clip rect is the mechanism that makes
         that work. Reporting it as a contrast failure buries the real ones. */
      const r=el.getBoundingClientRect()
      if(!r.width || !r.height) return
      if(cs.visibility==='hidden' || cs.display==='none' || parseFloat(cs.opacity)===0) return
      if(/(^|\s)screen-reader-text(\s|$)/.test(typeof el.className==='string'?el.className:'')) return
      if(cs.clip==='rect(0px, 0px, 0px, 0px)' || cs.clipPath==='inset(50%)') return
      if(el.closest('[aria-hidden="true"]')) return

      // The painted ground: walk up until something is fully opaque, then
      // composite every translucent layer back down over it in order.
      const layers=[]
      let opaque='rgb(255, 255, 255)'
      for(let a=el; a; a=a.parentElement){
        const abg=getComputedStyle(a).backgroundColor
        const rgba=resolve(abg)
        if(rgba[3]===0) continue
        if(rgba[3]===1){ opaque=abg; break }
        layers.unshift(abg)
      }
      let ground=resolve(opaque)
      for(const layer of layers){ ground=resolve(layer, `rgb(${ground[0]},${ground[1]},${ground[2]})`) }
      const fg=resolve(cs.color, `rgb(${ground[0]},${ground[1]},${ground[2]})`)

      const key=cs.color+'|'+opaque+'|'+layers.join()+'|'+cs.fontSize+'|'+(el.className||el.tagName)
      if(seen.has(key)) return; seen.add(key)
      out.colours.push({fg:[fg[0],fg[1],fg[2]],bg:[ground[0],ground[1],ground[2]],
        size:parseFloat(cs.fontSize),weight:cs.fontWeight,
        stroke:cs.webkitTextStrokeWidth,
        cls:(typeof el.className==='string'?el.className:'').slice(0,34)||el.tagName, txt:t.slice(0,22)})
    })
    return out
  })

  // heading order check
  let prev=0
  r.heads.forEach((h,i)=>{ if(i===0 && h!==1) findings.push(`${page}: first heading is h${h}, not h1`)
    else if(h>prev+1) findings.push(`${page}: heading jumps h${prev}→h${h}`); prev=h })
  if(r.heads.filter(h=>h===1).length!==1) findings.push(`${page}: ${r.heads.filter(h=>h===1).length} h1 elements`)
  if(!r.landmarks.main) findings.push(`${page}: no <main> landmark`)
  if(!r.skip) findings.push(`${page}: no skip link`)
  if(!r.langs) findings.push(`${page}: no lang attribute`)
  r.noAlt.forEach(s=>findings.push(`${page}: img without alt — ${s}`))
  r.emptyLink.forEach(s=>findings.push(`${page}: link with no accessible name — ${s}`))
  r.btnNoName.forEach(s=>findings.push(`${page}: button with no accessible name — ${s}`))

  r.colours.forEach(c=>{
    const fg=c.fg, bg=c.bg
    /* Outline type sets `color: transparent` and draws with
       -webkit-text-stroke. The computed colour is meaningless there and the
       stroke carries the legibility, so measuring it produces a finding that
       cannot be fixed without destroying the effect. */
    if(parseFloat(c.stroke) > 0 && fg[0]===bg[0] && fg[1]===bg[1] && fg[2]===bg[2]) return
    const cr=ratio(fg,bg)
    const large = c.size>=24 || (c.size>=18.66 && +c.weight>=700)
    const need = large?3:4.5
    if(cr < need) findings.push(`${page}: CONTRAST ${cr.toFixed(2)}:1 (needs ${need}) — ${c.size}px .${c.cls} "${c.txt}"`)
  })
  await ctx.close()
}

// keyboard: does a visible focus ring follow real Tab presses?
//
// This used to call element.focus() from script and read the outline. Chromium
// applies the :focus-visible heuristic from the LAST INPUT MODALITY, and a
// script call carries none — so every link and button on the page reported "no
// visible focus outline", 35 findings that were all the measurement. Tabbing
// for real sets the modality, which is also what an actual keyboard user does.
const ctx=await b.newContext({viewport:{width:1440,height:1000}})
const p=await ctx.newPage()
await p.goto(B+'/work/',{waitUntil:'networkidle'})
await p.evaluate(()=>document.body.focus())
const bad=new Set()
for (let i=0;i<60;i++){
  await p.keyboard.press('Tab')
  const r=await p.evaluate(()=>{
    const el=document.activeElement
    if(!el||el===document.body) return null
    const cs=getComputedStyle(el)
    const rect=el.getBoundingClientRect()
    // Off-screen or unpainted controls have no ring to show and no user to
    // show it to; the skip link paints itself only once focused, which this
    // measurement takes AFTER focus, so it is included fairly.
    if(!rect.width||!rect.height) return null
    const ring = (cs.outlineStyle!=='none' && parseFloat(cs.outlineWidth)>0) ||
                 cs.boxShadow!=='none' ||
                 getComputedStyle(el,':focus-visible').outlineStyle!=='none'
    return ring?null:(el.tagName+'.'+((typeof el.className==='string'?el.className:'')||'')).slice(0,44)
  })
  if(r) bad.add(r)
}
bad.forEach(f=>findings.push(`focus: no visible ring on ${f}`))
await b.close()
console.log(findings.length? findings.join('\n') : 'no accessibility findings')
console.log(`\n${findings.length} findings`)
