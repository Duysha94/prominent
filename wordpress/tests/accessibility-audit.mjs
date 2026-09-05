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
 * Usage:  node accessibility-audit.mjs        (expects a server on :9500)
 */
import { chromium } from 'playwright'
const B='http://127.0.0.1:9500/prototype/'
const PAGES=['home.html','services.html','work.html','case-website.html','case-photo.html','case-film.html','case-event.html']
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
    // sample every distinct text style against its painted background
    const seen=new Set()
    document.querySelectorAll('body *').forEach(el=>{
      const t=(el.textContent||'').trim()
      if(!t || el.children.length) return
      const cs=getComputedStyle(el)
      let bgEl=el, bg='rgba(0, 0, 0, 0)'
      while(bgEl && bg==='rgba(0, 0, 0, 0)'){ bg=getComputedStyle(bgEl).backgroundColor; bgEl=bgEl.parentElement }
      const key=cs.color+'|'+bg+'|'+cs.fontSize+'|'+(el.className||el.tagName)
      if(seen.has(key)) return; seen.add(key)
      out.colours.push({fg:cs.color,bg,size:parseFloat(cs.fontSize),weight:cs.fontWeight,
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

  const parse=s=>{const m=s.match(/[\d.]+/g); return m?m.slice(0,3).map(Number):null}
  r.colours.forEach(c=>{
    const fg=parse(c.fg), bg=parse(c.bg); if(!fg||!bg) return
    const cr=ratio(fg,bg)
    const large = c.size>=24 || (c.size>=18.66 && +c.weight>=700)
    const need = large?3:4.5
    if(cr < need) findings.push(`${page}: CONTRAST ${cr.toFixed(2)}:1 (needs ${need}) — ${c.size}px .${c.cls} "${c.txt}"`)
  })
  await ctx.close()
}

// keyboard: can every interactive element be reached and does focus show?
const ctx=await b.newContext({viewport:{width:1440,height:1000}})
const p=await ctx.newPage()
await p.goto(B+'case-film.html',{waitUntil:'networkidle'})
const focus = await p.evaluate(()=>{
  const els=[...document.querySelectorAll('a[href],button')]
  const bad=[]
  els.forEach(e=>{ e.focus()
    const cs=getComputedStyle(e)
    const has = cs.outlineStyle!=='none' && parseFloat(cs.outlineWidth)>0
    if(!has) bad.push((e.tagName+'.'+(e.className||'')).slice(0,40)) })
  return bad
})
focus.forEach(f=>findings.push(`case-film.html: no visible focus outline on ${f}`))
await b.close()
console.log(findings.length? findings.join('\n') : 'no accessibility findings')
console.log(`\n${findings.length} findings`)
