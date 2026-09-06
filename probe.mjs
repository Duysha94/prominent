import { chromium } from 'playwright'
const b=await chromium.launch({executablePath:'/opt/pw-browsers/chromium-1194/chrome-linux/chrome'})
const ctx=await b.newContext(); const p=await ctx.newPage()
const reqs=[]
p.on('response', async r => {
  const u=r.url(); if(!/\.(js|css|woff2?|ttf)(\?|$)/.test(u)) return
  let size=0; try { size=(await r.body()).length } catch(e){}
  reqs.push({u, size, status:r.status()})
})
await p.goto('http://127.0.0.1:9410/',{waitUntil:'networkidle'})
const globals = await p.evaluate(()=>({
  barba: typeof window.barba, gsap: typeof window.gsap, Lenis: typeof window.Lenis,
  lenis: typeof window.lenis, ScrollTrigger: typeof window.ScrollTrigger, SplitText: typeof window.SplitText,
  jQuery: typeof window.jQuery, THREE: typeof window.THREE, ak: typeof window.AK,
  zeynaVars: Object.keys(window).filter(k=>/zeyna|pe_|pe-/i.test(k)),
}))
const marks = await p.evaluate(()=>({
  pageTransitions: document.querySelectorAll('.page--transitions').length,
  barbaContainer: document.querySelectorAll('[data-barba]').length,
  siteTitleH5: document.querySelectorAll('h5.site-title').length,
  bodyClasses: document.body.className,
  htmlClasses: document.documentElement.className,
}))
console.log(JSON.stringify({globals, marks}, null, 1))
let pj=0,pc=0,aj=0,ac=0,oj=0,oc=0
for(const r of reqs){
  const isZ=/themes\/zeyna\//.test(r.u), isA=/ak-zeyna-child/.test(r.u)
  const isJ=/\.js(\?|$)/.test(r.u), isC=/\.css(\?|$)/.test(r.u)
  if(isZ&&isJ)pj+=r.size; else if(isZ&&isC)pc+=r.size
  else if(isA&&isJ)aj+=r.size; else if(isA&&isC)ac+=r.size
  else if(isJ)oj+=r.size; else if(isC)oc+=r.size
}
const k=n=>Math.round(n/1024)+'KB'
console.log('\nREQUESTS', reqs.length)
console.log('zeyna  JS', k(pj), ' CSS', k(pc))
console.log('ak     JS', k(aj), ' CSS', k(ac))
console.log('other  JS', k(oj), ' CSS', k(oc))
console.log('\nzeyna assets loaded:')
reqs.filter(r=>/themes\/zeyna\//.test(r.u)).forEach(r=>console.log('  ', k(r.size).padStart(7), r.u.split('/themes/zeyna/')[1].split('?')[0]))
console.log('\nak assets loaded:')
reqs.filter(r=>/ak-zeyna-child/.test(r.u)).forEach(r=>console.log('  ', k(r.size).padStart(7), r.u.split('ak-zeyna-child/')[1].split('?')[0]))
await b.close()
