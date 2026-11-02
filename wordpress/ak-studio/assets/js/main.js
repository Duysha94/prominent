/**
 * AK Studio — motion layer. Plain script on top of GSAP, ScrollTrigger and
 * Lenis (all enqueued by the theme). Every effect has a still fallback.
 */
(() => {
	const d = document;
	const root = d.documentElement;
	const gsap = window.gsap;
	const ST = window.ScrollTrigger;
	const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
	const fine = matchMedia('(hover: hover) and (pointer: fine)').matches;

	if (!gsap) {
		root.classList.remove('js'); // nothing will animate the entrances in
		return;
	}
	if (ST) gsap.registerPlugin(ST);

	/* ── Smooth scroll ─────────────────────────────────────────────── */
	let lenis = null;
	let velocity = 0;
	if (!reduced && window.Lenis) {
		lenis = new window.Lenis({ lerp: 0.1, smoothWheel: true });
		lenis.on('scroll', (l) => {
			velocity = l.velocity;
			if (ST) ST.update();
		});
		gsap.ticker.add((t) => lenis.raf(t * 1000));
		gsap.ticker.lagSmoothing(0);
	}
	d.addEventListener('click', (e) => {
		const a = e.target.closest('a[href^="#"]');
		if (!a || a.getAttribute('href').length < 2) return;
		const target = d.querySelector(a.getAttribute('href'));
		if (!target) return;
		e.preventDefault();
		if (lenis) lenis.scrollTo(target, { offset: -70, duration: 1.4 });
		else target.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth' });
	});

	/* ── Header: solid after the fold starts moving, away while reading down ── */
	const head = d.querySelector('[data-head]');
	const menuBtn = d.querySelector('.menu-btn');
	let lastY = scrollY;
	const onScroll = () => {
		const y = scrollY;
		if (head) {
			head.classList.toggle('is-scrolled', y > 24);
			head.classList.toggle('is-hidden', y > lastY && y > innerHeight * 0.8 && !head.classList.contains('is-open'));
		}
		lastY = y;
	};
	addEventListener('scroll', onScroll, { passive: true });
	onScroll();
	if (menuBtn && head) {
		const setMenu = (open) => {
			head.classList.toggle('is-open', open);
			menuBtn.setAttribute('aria-expanded', String(open));
			menuBtn.textContent = open ? menuBtn.dataset.close || 'Close' : menuBtn.dataset.open || 'Menu';
			if (lenis) open ? lenis.stop() : lenis.start();
		};
		menuBtn.dataset.open = menuBtn.textContent.trim();
		menuBtn.addEventListener('click', () => setMenu(!head.classList.contains('is-open')));
		d.querySelectorAll('.nav a').forEach((a) => a.addEventListener('click', () => setMenu(false)));
		addEventListener('keydown', (e) => { if (e.key === 'Escape' && head.classList.contains('is-open')) { setMenu(false); menuBtn.focus(); } });
	}

	/* ── Kinetic headline: rows drift forever, faster when the page moves ── */
	const rows = [...d.querySelectorAll('.kinetic__row')].map((row) => {
		const track = row.querySelector('.kinetic__track');
		return { row, track, dir: Number(row.dataset.dir) || -1, speed: Number(row.dataset.speed) || 0.5, x: 0, w: 1, set: gsap.quickSetter(track, 'x', 'px') };
	});
	const measure = () => rows.forEach((r) => {
		r.w = r.track.scrollWidth / 2 || 1;
		r.x = -r.w * (r.dir < 0 ? 0.15 : 0.6);
		r.set(r.x);
	});
	measure();
	addEventListener('resize', measure);
	d.fonts && d.fonts.ready.then(measure);
	if (!reduced && rows.length) {
		gsap.ticker.add((_t, dt) => {
			const boost = 1 + Math.min(Math.abs(velocity) * 0.08, 7);
			for (const r of rows) {
				r.x += r.dir * r.speed * 100 * (dt / 1000) * boost;
				if (r.x <= -r.w) r.x += r.w;
				if (r.x > 0) r.x -= r.w;
				r.set(r.x);
			}
			velocity *= 0.9;
		});
	}

	/* ── Arrival ───────────────────────────────────────────────────── */
	const rise = gsap.utils.toArray('[data-rise]');
	if (reduced) {
		gsap.set(rise, { opacity: 1, y: 0 });
	} else {
		const tl = gsap.timeline({ delay: 0.15 });
		tl.from('.kinetic__track', { yPercent: 118, duration: 1.4, ease: 'expo.out', stagger: 0.12 }, 0)
			.to(rise, { opacity: 1, y: 0, duration: 1.1, ease: 'expo.out', stagger: 0.08 }, 0.55)
			.from('.hero__media', { scale: 1.12, opacity: 0, duration: 2.2, ease: 'expo.out' }, 0);
		d.querySelectorAll('[data-count]').forEach((el) => {
			const end = Number(el.dataset.count);
			const o = { v: 0 };
			tl.to(o, { v: end, duration: 1.6, ease: 'power3.out', onUpdate: () => { el.textContent = String(Math.round(o.v)); } }, 0.8);
		});
	}

	/* ── Hero reel: one platform at a time ─────────────────────────── */
	const clips = [...d.querySelectorAll('.hero__clip')];
	const num = d.querySelector('[data-reel-num]');
	const label = d.querySelector('[data-reel-label]');
	const kind = d.querySelector('[data-reel-kind]');
	const bar = d.querySelector('[data-reel-bar]');
	const HOLD = 6.5;
	let cur = 0;
	let timer = null;
	const load = (v) => { if (v && !v.getAttribute('src')) { v.src = v.dataset.src; v.load(); } };
	const play = (v) => { load(v); const p = v.play(); if (p) p.catch(() => {}); };
	const runBar = () => { if (bar) gsap.fromTo(bar, { scaleX: 0 }, { scaleX: 1, duration: HOLD, ease: 'none', overwrite: true }); };
	const swapText = (el, text) => {
		if (!el) return;
		gsap.timeline()
			.to(el, { yPercent: -60, opacity: 0, filter: 'blur(6px)', duration: 0.35, ease: 'power2.in' })
			.add(() => { el.textContent = text; })
			.fromTo(el, { yPercent: 60, opacity: 0, filter: 'blur(6px)' }, { yPercent: 0, opacity: 1, filter: 'blur(0px)', duration: 0.6, ease: 'expo.out' });
	};
	const next = () => {
		if (clips.length < 2) return;
		const prev = clips[cur];
		cur = (cur + 1) % clips.length;
		const v = clips[cur];
		v.currentTime = 0;
		play(v);
		v.classList.add('is-on');
		prev.classList.remove('is-on');
		setTimeout(() => prev.pause(), 1700);
		load(clips[(cur + 1) % clips.length]);
		swapText(num, String(cur + 1).padStart(2, '0'));
		swapText(label, v.dataset.label || '');
		swapText(kind, v.dataset.kind || '');
		runBar();
	};
	if (clips.length) {
		play(clips[0]);
		if (!reduced && clips.length > 1) {
			load(clips[1]);
			runBar();
			const start = () => { if (!timer) timer = setInterval(next, HOLD * 1000); };
			const stop = () => { clearInterval(timer); timer = null; };
			start();
			const hero = d.querySelector('[data-hero]');
			if (hero && 'IntersectionObserver' in window) {
				new IntersectionObserver(([e]) => {
					if (e.isIntersecting) { start(); clips[cur].play().catch(() => {}); } else { stop(); clips.forEach((c) => c.pause()); }
				}).observe(hero);
			}
			d.addEventListener('visibilitychange', () => (d.hidden ? stop() : start()));
		}
	}

	/* ── Depth while leaving the hero ──────────────────────────────── */
	if (ST && !reduced) {
		gsap.to('.hero__media', { yPercent: 16, ease: 'none', scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true } });
		gsap.to('.kinetic__row:nth-child(2)', { xPercent: -6, ease: 'none', scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true } });
		gsap.to('.kinetic__row:nth-child(4)', { xPercent: 6, ease: 'none', scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true } });
		gsap.to('.hero__inner', { opacity: 0.25, y: -60, ease: 'none', scrollTrigger: { trigger: '.hero', start: '55% top', end: 'bottom top', scrub: true } });
	}

	/* ── Magnetic buttons ──────────────────────────────────────────── */
	if (fine && !reduced) {
		d.querySelectorAll('[data-magnetic]').forEach((el) => {
			el.addEventListener('pointermove', (e) => {
				const r = el.getBoundingClientRect();
				gsap.to(el, { x: (e.clientX - r.left - r.width / 2) * 0.22, y: (e.clientY - r.top - r.height / 2) * 0.35, duration: 0.5, ease: 'power3.out' });
			});
			el.addEventListener('pointerleave', () => gsap.to(el, { x: 0, y: 0, duration: 0.8, ease: 'elastic.out(1, 0.45)' }));
		});
	}

	/* ── Cursor ────────────────────────────────────────────────────── */
	const cursor = d.querySelector('.cursor');
	if (cursor && fine && !reduced) {
		root.classList.add('has-cursor');
		const tag = cursor.querySelector('.cursor__label');
		const xTo = gsap.quickTo(cursor, 'x', { duration: 0.2, ease: 'power3' });
		const yTo = gsap.quickTo(cursor, 'y', { duration: 0.2, ease: 'power3' });
		addEventListener('pointermove', (e) => { cursor.classList.add('is-live'); xTo(e.clientX); yTo(e.clientY); }, { passive: true });
		d.addEventListener('pointerleave', () => cursor.classList.remove('is-live'));
		d.addEventListener('pointerover', (e) => {
			const t = e.target.closest('a, button, [data-cursor]');
			cursor.classList.toggle('is-hover', Boolean(t));
			if (tag) tag.textContent = (t && t.dataset.cursor) || '';
		});
	}
})();
