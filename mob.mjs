import { chromium } from 'playwright';
const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium' });
const pages = ['front-page','home','single','archive-portfolio','single-portfolio','template-services','template-about','template-contact'];
const widths = [360, 390, 430, 768, 1024];
const issues = [];
for (const w of widths) {
  const ctx = await b.newContext({ viewport: { width: w, height: 900 } });
  const p = await ctx.newPage();
  p.on('pageerror', e => issues.push(`[${w}] PAGEERROR ${e.message}`));
  for (const t of pages) {
    await p.goto(`http://127.0.0.1:4180/index-${t}.html`, { waitUntil: 'domcontentloaded' });
    await p.waitForTimeout(700);
    const r = await p.evaluate(() => {
      const vw = document.documentElement.clientWidth;
      const over = document.documentElement.scrollWidth - vw;
      const wide = [];
      if (over > 1) document.querySelectorAll('*').forEach(el => {
        const b = el.getBoundingClientRect();
        if (b.right > vw + 2 && b.width > 4) wide.push(el.tagName + '.' + String(el.className).split(' ').slice(0,2).join('.') + ':' + Math.round(b.right));
      });
      // elements clipped or zero-height that should have content
      const broken = [];
      document.querySelectorAll('.ak-section, .ak-founder, .ak-plate, .ak-monogram, .ak-footer__grid, .ak-chips, .ak-claim, .ak-index__link').forEach(el => {
        const b = el.getBoundingClientRect();
        if (el.textContent.trim().length > 8 && b.height < 6) broken.push('collapsed:' + (el.className||el.tagName));
      });
      const loader = document.querySelector('[data-ak-loader]');
      return { over, wide: wide.slice(0,4), broken: broken.slice(0,4), firstLoad: document.documentElement.classList.contains('first--load'), loaderGone: !loader || loader.classList.contains('is-done'),
               toggleVisible: !!document.querySelector('.ak-mode--header') && getComputedStyle(document.querySelector('.ak-mode--header')).display !== 'none' };
    });
    if (r.over > 1) issues.push(`[${w}] ${t} OVERFLOW ${r.over}px -> ${r.wide.join(' | ')}`);
    if (r.broken.length) issues.push(`[${w}] ${t} BROKEN ${r.broken.join(' | ')}`);
    if (r.firstLoad) issues.push(`[${w}] ${t} first--load NOT cleared`);
    if (!r.toggleVisible) issues.push(`[${w}] ${t} mode toggle not visible`);
  }
  await ctx.close();
}
console.log(issues.length ? 'ISSUES:\n' + issues.join('\n') : 'CLEAN across ' + widths.join('/') + ' x ' + pages.length + ' templates');
await b.close();
