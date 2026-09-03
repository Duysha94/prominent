import { chromium } from 'playwright';
const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium' });
const pages = ['front-page','home','single','archive-portfolio','single-portfolio','template-services','template-about','template-contact'];
const widths = [360, 390, 430, 600, 768, 900, 1024, 1366, 1920];
const issues = [];
for (const theme of ['light','dark']) {
  for (const w of widths) {
    const ctx = await b.newContext({ viewport: { width: w, height: 900 }, colorScheme: theme });
    const p = await ctx.newPage();
    p.on('pageerror', e => issues.push(`[${theme}/${w}] PAGEERROR ${e.message}`));
    p.on('console', m => { if (m.type()==='error' && !/favicon|ERR_TUNNEL/.test(m.text())) issues.push(`[${theme}/${w}] ${m.text().slice(0,90)}`); });
    for (const t of pages) {
      await p.goto(`http://127.0.0.1:4180/index-${t}.html`, { waitUntil: 'domcontentloaded' });
      await p.waitForTimeout(1500);
      const r = await p.evaluate(() => {
        const vw = document.documentElement.clientWidth;
        const over = document.documentElement.scrollWidth - vw;
        const wide = [];
        if (over > 1) document.querySelectorAll('*').forEach(el => {
          const b = el.getBoundingClientRect();
          if (b.right > vw + 2 && b.width > 4) wide.push(el.tagName+'.'+String(el.className).split(' ').slice(0,2).join('.'));
        });
        const tog = document.querySelector('.ak-mode--header');
        const small = [];
        document.querySelectorAll('.main-navigation a, .ak-footer ul a, .ak-mode--header, .menu-toggle').forEach(el => {
          const r = el.getBoundingClientRect();
          if (r.width > 0 && r.height < 24) small.push(`${el.className||el.tagName}:${Math.round(r.height)}px`);
        });
        return { over, wide: [...new Set(wide)].slice(0,3), h1: document.querySelectorAll('h1').length,
          firstLoad: document.documentElement.classList.contains('first--load'),
          toggle: tog ? getComputedStyle(tog).display !== 'none' : false,
          small: [...new Set(small)].slice(0,3),
          minPx: Math.min(...[...document.querySelectorAll('.ak-band__key,.ak-site-card__domain,.ak-eyebrow')].map(e => parseFloat(getComputedStyle(e).fontSize)).filter(Boolean).concat([99])) };
      });
      if (r.over > 1) issues.push(`[${theme}/${w}] ${t} OVERFLOW ${r.over}px ${r.wide.join('|')}`);
      if (r.h1 !== 1) issues.push(`[${theme}/${w}] ${t} ${r.h1} h1`);
      if (r.firstLoad) issues.push(`[${theme}/${w}] ${t} loader stuck`);
      if (!r.toggle) issues.push(`[${theme}/${w}] ${t} no mode toggle`);
      if (r.small.length) issues.push(`[${theme}/${w}] ${t} tap<24: ${r.small.join('|')}`);
      if (r.minPx < 8.5) issues.push(`[${theme}/${w}] ${t} type ${r.minPx}px too small`);
    }
    await ctx.close();
  }
}
console.log(issues.length ? 'ISSUES ('+issues.length+'):\n' + [...new Set(issues)].slice(0,25).join('\n')
  : `CLEAN — ${pages.length} templates x ${widths.length} widths x 2 modes = ${pages.length*widths.length*2} renders`);
await b.close();
