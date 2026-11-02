/**
 * AK Studio — motion layer. Plain script on top of GSAP, ScrollTrigger and
 * Lenis (all enqueued by the theme). Every effect has a still fallback:
 * without script, with reduced motion, or on a data-saver connection the
 * pages read the same, just without the movement.
 */
(() => {
	const d = document;
	const root = d.documentElement;
	const gsap = window.gsap;
	const ST = window.ScrollTrigger;
	const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
	const fine = matchMedia('(hover: hover) and (pointer: fine)').matches;
	const small = () => matchMedia('(max-width: 900px)').matches;
	const saveData = Boolean(navigator.connection && navigator.connection.saveData);

	const i18n = window.akStudio || { close: 'Close', prev: 'Previous image', next: 'Next image', play: 'Play film' };

	/* The page column is measured from the viewport minus the scrollbar. */
	const setSbw = () => root.style.setProperty('--sbw', Math.max(0, innerWidth - root.clientWidth) + 'px');
	setSbw();
	addEventListener('resize', setSbw);

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
	const headOffset = () => -((d.querySelector('[data-head]') || {}).offsetHeight || 70) - 10;
	d.addEventListener('click', (e) => {
		const a = e.target.closest('a[href*="#"]');
		if (!a || e.defaultPrevented) return;
		const url = new URL(a.href, location.href);
		if (url.pathname !== location.pathname || url.hash.length < 2) return;
		if (a.hasAttribute('data-consent-open')) return;
		const target = d.getElementById(decodeURIComponent(url.hash.slice(1)));
		if (!target) return;
		e.preventDefault();
		if (lenis) lenis.scrollTo(target, { offset: headOffset(), duration: 1.4 });
		else target.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth' });
		history.replaceState(null, '', url.hash);
	});

	/* ── Header: solid once the page moves, away while reading down ─── */
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
			root.style.overflow = open ? 'hidden' : '';
		};
		menuBtn.dataset.open = menuBtn.textContent.trim();
		menuBtn.addEventListener('click', () => setMenu(!head.classList.contains('is-open')));
		d.querySelectorAll('.nav a').forEach((a) => a.addEventListener('click', () => setMenu(false)));
		addEventListener('keydown', (e) => { if (e.key === 'Escape' && head.classList.contains('is-open')) { setMenu(false); menuBtn.focus(); } });
		matchMedia('(min-width: 901px)').addEventListener('change', (m) => { if (m.matches) setMenu(false); });
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
	if (rows.length) {
		measure();
		addEventListener('resize', measure);
		if (d.fonts) d.fonts.ready.then(measure);
	}
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

	/* ── Split headings into words that rise from a mask ───────────── */
	const split = (el) => {
		if (el.dataset.splitDone) return [];
		const walk = (node) => {
			[...node.childNodes].forEach((n) => {
				if (n.nodeType === 3) {
					const frag = d.createDocumentFragment();
					n.textContent.split(/(\s+)/).forEach((part) => {
						if (!part) return;
						if (/^\s+$/.test(part)) { frag.appendChild(d.createTextNode(part)); return; }
						const w = d.createElement('span');
						w.className = 'w';
						const inner = d.createElement('span');
						inner.className = 'w__in';
						inner.textContent = part;
						w.appendChild(inner);
						frag.appendChild(w);
					});
					n.replaceWith(frag);
				} else if (n.nodeType === 1 && n.tagName !== 'BR') {
					walk(n);
				}
			});
		};
		walk(el);
		el.dataset.splitDone = '1';
		return el.querySelectorAll('.w__in');
	};

	/* ── Arrival (front page hero) ─────────────────────────────────── */
	const hero = d.querySelector('[data-hero]');
	const heroRise = hero ? gsap.utils.toArray('[data-rise]', hero) : [];
	if (hero && !reduced) {
		const tl = gsap.timeline({ delay: 0.15 });
		tl.from('.kinetic__track', { yPercent: 118, duration: 1.4, ease: 'expo.out', stagger: 0.12 }, 0)
			.to(heroRise, { opacity: 1, y: 0, duration: 1.1, ease: 'expo.out', stagger: 0.08 }, 0.55)
			.from('.hero__media', { scale: 1.12, opacity: 0, duration: 2.2, ease: 'expo.out' }, 0);
		hero.querySelectorAll('[data-count]').forEach((el) => {
			const end = Number(el.dataset.count);
			const o = { v: 0 };
			tl.to(o, { v: end, duration: 1.6, ease: 'power3.out', onUpdate: () => { el.textContent = String(Math.round(o.v)); } }, 0.8);
		});
	}

	/* ── Entrances everywhere else ─────────────────────────────────── */
	// Blocks typed in the editor join in, but only those not yet on screen (no flash).
	const below = (el) => el.getBoundingClientRect().top > innerHeight * 0.9;
	if (!reduced) {
		d.querySelectorAll('.entry :is(.is-style-display, .is-style-display-xl), .site-foot__body .is-style-display-xl').forEach((el) => { if (below(el) && !el.closest('[data-split]')) el.setAttribute('data-split', ''); });
		d.querySelectorAll('.entry :is(.is-style-eyebrow, .is-style-lead, .wp-block-buttons, .wp-block-details, .ak-faq > .wp-block-group__inner-container > h2, ol.is-style-steps-row > li, ul.is-style-cards > li, ul.is-style-timeline > li, .wp-block-quote, .is-style-founder, .facts)').forEach((el) => {
			if (below(el) && !el.closest('[data-rise]') && !el.querySelector('[data-rise]')) { el.setAttribute('data-rise', ''); gsap.set(el, { opacity: 0, y: 26 }); }
		});
		d.querySelectorAll('.entry :is(.wp-block-gallery.is-style-masonry figure.wp-block-image, .wp-block-video.is-style-film, .wp-block-image:not(.wp-block-gallery .wp-block-image))').forEach((el) => {
			if (below(el) && !el.closest('[data-reveal]')) el.setAttribute('data-reveal', '');
		});
	}
	const rise = gsap.utils.toArray('[data-rise]').filter((el) => !heroRise.includes(el));
	const reveals = gsap.utils.toArray('[data-reveal]');
	const splits = gsap.utils.toArray('[data-split]');
	if (reduced || !ST) {
		gsap.set(rise, { opacity: 1, y: 0 });
		reveals.forEach((el) => el.classList.add('is-in'));
	} else {
		ST.batch(rise, {
			start: 'top 92%',
			once: true,
			onEnter: (batch) => gsap.to(batch, { opacity: 1, y: 0, duration: 1.05, ease: 'expo.out', stagger: 0.08, overwrite: true }),
		});
		ST.batch(reveals, {
			start: 'top 94%',
			once: true,
			onEnter: (batch) => batch.forEach((el, i) => setTimeout(() => el.classList.add('is-in'), i * 90)),
		});
		splits.forEach((el) => {
			const words = split(el);
			if (!words.length) return;
			gsap.from(words, {
				yPercent: 112, duration: 1.15, ease: 'expo.out', stagger: 0.035,
				scrollTrigger: { trigger: el, start: 'top 90%', once: true },
			});
		});
	}

	/* ── Depth: hero and image parallax ────────────────────────────── */
	if (ST && !reduced) {
		if (hero) {
			const span = { trigger: hero, start: 'top top', end: 'bottom top', scrub: true };
			gsap.to('.hero__media', { yPercent: 16, ease: 'none', scrollTrigger: span });
			gsap.to('.kinetic__row:nth-child(2)', { xPercent: -6, ease: 'none', scrollTrigger: span });
			gsap.to('.kinetic__row:nth-child(4)', { xPercent: 6, ease: 'none', scrollTrigger: span });
			gsap.to('.hero__inner', { opacity: 0.25, y: -60, ease: 'none', scrollTrigger: { trigger: hero, start: '55% top', end: 'bottom top', scrub: true } });
		}
		gsap.utils.toArray('[data-parallax]').forEach((el) => {
			const amount = Number(el.dataset.parallax) || 10;
			const box = el.parentElement;
			const atTop = box.getBoundingClientRect().top < innerHeight * 0.5;
			gsap.fromTo(el, { yPercent: atTop ? 0 : -amount / 2 }, {
				yPercent: atTop ? amount : amount / 2, ease: 'none',
				scrollTrigger: { trigger: box, start: atTop ? 'top top' : 'top bottom', end: 'bottom top', scrub: true },
			});
		});
	}

	/* ── Video: load what is needed, play only what is seen ────────── */
	const pickSrc = (v) => (small() && v.dataset.srcSm ? v.dataset.srcSm : v.dataset.src);
	const load = (v) => { if (v && !v.getAttribute('src') && v.dataset.src) { v.src = pickSrc(v); v.load(); } };
	const play = (v) => { if (saveData) return; load(v); const p = v.play(); if (p) p.catch(() => {}); };

	const autos = [...d.querySelectorAll('video[data-autoplay]')];
	if (autos.length && !reduced && !saveData && 'IntersectionObserver' in window) {
		const io = new IntersectionObserver((entries) => entries.forEach(({ target, isIntersecting }) => {
			if (isIntersecting) play(target); else target.pause();
		}), { rootMargin: '160px 0px' });
		autos.forEach((v) => io.observe(v));
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
	if (clips.length && !reduced && !saveData) {
		play(clips[0]);
		if (clips.length > 1) {
			setTimeout(() => load(clips[1]), 2500);
			runBar();
			const start = () => { if (!timer) timer = setInterval(next, HOLD * 1000); };
			const stop = () => { clearInterval(timer); timer = null; };
			start();
			if (hero && 'IntersectionObserver' in window) {
				new IntersectionObserver(([e]) => {
					if (e.isIntersecting) { start(); clips[cur].play().catch(() => {}); } else { stop(); clips.forEach((c) => c.pause()); }
				}).observe(hero);
			}
			d.addEventListener('visibilitychange', () => (d.hidden ? stop() : start()));
		}
	}

	/* ── Selected work: pinned horizontal rail on wide screens ─────── */
	const rail = d.querySelector('[data-rail]');
	if (rail && ST && !reduced) {
		const viewport = rail.querySelector('.work__viewport');
		const track = rail.querySelector('[data-rail-track]');
		const mm = gsap.matchMedia();
		mm.add('(min-width: 901px)', () => {
			const dist = () => Math.max(0, track.scrollWidth - viewport.clientWidth);
			if (dist() < 40) return undefined;
			const tween = gsap.to(track, {
				x: () => -dist(), ease: 'none',
				scrollTrigger: { trigger: viewport, start: 'center center', end: () => '+=' + dist(), pin: true, scrub: 0.6, invalidateOnRefresh: true, anticipatePin: 1 },
			});
			return () => tween.scrollTrigger && tween.scrollTrigger.kill();
		});
	}

	/* ── Services: a photograph follows the pointer ────────────────── */
	const svc = d.querySelector('[data-svc]');
	const float = d.querySelector('[data-svc-float]');
	if (svc && float && fine && !reduced) {
		const img = float.querySelector('img');
		gsap.set(float, { xPercent: -50, yPercent: -50 });
		const xTo = gsap.quickTo(float, 'x', { duration: 0.55, ease: 'power3' });
		const yTo = gsap.quickTo(float, 'y', { duration: 0.55, ease: 'power3' });
		const rot = gsap.quickTo(float, 'rotation', { duration: 0.8, ease: 'power3' });
		let lastX = 0;
		svc.addEventListener('pointermove', (e) => { xTo(e.clientX + 40); yTo(e.clientY); rot(Math.max(-8, Math.min(8, (e.clientX - lastX) * 0.4))); lastX = e.clientX; });
		const sources = [];
		svc.querySelectorAll('[data-svc-img]').forEach((a) => {
			if (a.dataset.svcImg) sources.push(a.dataset.svcImg);
			a.addEventListener('pointerenter', () => {
				const s = a.dataset.svcImg;
				if (!s) return;
				if (img.getAttribute('src') !== s) img.src = s;
				float.classList.add('is-on');
			});
			a.addEventListener('pointerleave', () => float.classList.remove('is-on'));
		});
		const warm = () => sources.forEach((s) => { const i = new Image(); i.src = s; });
		('requestIdleCallback' in window) ? requestIdleCallback(warm, { timeout: 4000 }) : setTimeout(warm, 3000);
	}

	/* ── Work filters ──────────────────────────────────────────────── */
	const filters = d.querySelector('[data-filters]');
	const grid = d.querySelector('[data-filter-grid]');
	if (filters && grid) {
		const cards = [...grid.children];
		const empty = d.querySelector('[data-filter-empty]');
		filters.addEventListener('click', (e) => {
			const b = e.target.closest('[data-filter]');
			if (!b) return;
			filters.querySelectorAll('[data-filter]').forEach((x) => {
				x.classList.toggle('is-on', x === b);
				x.setAttribute('aria-pressed', String(x === b));
			});
			const f = b.dataset.filter;
			let shown = 0;
			cards.forEach((c) => {
				const ok = f === '*' || (' ' + (c.dataset.types || '') + ' ').includes(' ' + f + ' ');
				c.hidden = !ok;
				if (ok) {
					c.classList.toggle('is-shifted', shown % 2 === 1);
					c.classList.toggle('is-flat', shown % 2 === 0);
					c.querySelectorAll('[data-reveal]').forEach((r) => r.classList.add('is-in'));
					shown++;
				}
			});
			if (empty) empty.hidden = shown > 0;
			const url = new URL(location.href);
			if (f === '*') url.searchParams.delete('type'); else url.searchParams.set('type', f);
			history.replaceState(null, '', url);
			if (ST) ST.refresh();
		});
		// Cards hidden on arrival (?type=…) still alternate correctly.
		let shown = 0;
		cards.forEach((c) => { if (!c.hidden) { c.classList.toggle('is-shifted', shown % 2 === 1); c.classList.toggle('is-flat', shown % 2 === 0); shown++; } });
	}

	/* ── Gallery lightbox: photographs open large, one gallery at a time ── */
	const galleries = [...d.querySelectorAll('.wp-block-gallery.is-style-masonry')];
	if (galleries.length && typeof HTMLDialogElement === 'function') {
		const dlg = d.createElement('dialog');
		dlg.className = 'lightbox';
		dlg.innerHTML = '<img alt=""><p class="lightbox__cap"></p><button type="button" class="lightbox__close"></button><button type="button" class="lightbox__nav lightbox__nav--prev">←</button><button type="button" class="lightbox__nav lightbox__nav--next">→</button>';
		d.body.appendChild(dlg);
		const im = dlg.querySelector('img');
		const cap = dlg.querySelector('.lightbox__cap');
		dlg.querySelector('.lightbox__close').textContent = i18n.close;
		dlg.querySelector('.lightbox__nav--prev').setAttribute('aria-label', i18n.prev);
		dlg.querySelector('.lightbox__nav--next').setAttribute('aria-label', i18n.next);
		let links = [];
		let idx = 0;
		const show = (i) => {
			idx = (i + links.length) % links.length;
			const a = links[idx];
			const thumb = a.querySelector('img');
			im.src = a.href;
			im.alt = thumb ? thumb.alt : '';
			const fc = a.closest('figure') ? a.closest('figure').querySelector('figcaption') : null;
			cap.textContent = fc ? fc.textContent : '';
		};
		galleries.forEach((g) => {
			const own = [...g.querySelectorAll('figure.wp-block-image > a[href]')].filter((a) => /\.(jpe?g|png|webp|gif|avif)(\?.*)?$/i.test(a.getAttribute('href')));
			own.forEach((a, i) => a.addEventListener('click', (e) => {
				e.preventDefault();
				links = own;
				show(i);
				dlg.showModal();
				if (lenis) lenis.stop();
			}));
		});
		dlg.querySelector('.lightbox__close').addEventListener('click', () => dlg.close());
		dlg.querySelector('.lightbox__nav--prev').addEventListener('click', () => show(idx - 1));
		dlg.querySelector('.lightbox__nav--next').addEventListener('click', () => show(idx + 1));
		dlg.addEventListener('close', () => { if (lenis) lenis.start(); });
		dlg.addEventListener('keydown', (e) => {
			if (e.key === 'ArrowLeft') show(idx - 1);
			if (e.key === 'ArrowRight') show(idx + 1);
		});
		dlg.addEventListener('click', (e) => { if (e.target === dlg) dlg.close(); });
	}

	/* ── Films: a play button over the frame, then sound and controls ── */
	d.querySelectorAll('.wp-block-video.is-style-film').forEach((frame) => {
		const v = frame.querySelector('video');
		if (!v) return;
		v.controls = false;
		const b = d.createElement('button');
		b.type = 'button';
		b.className = 'film__play';
		b.setAttribute('data-cursor', i18n.play);
		b.innerHTML = '<span class="film__icon" aria-hidden="true"></span><span class="film__label"></span>';
		b.querySelector('.film__label').textContent = i18n.play;
		frame.appendChild(b);
		b.addEventListener('click', () => {
			frame.classList.add('is-playing');
			v.controls = true;
			const p = v.play();
			if (p) p.catch(() => {});
			v.focus();
		});
		v.addEventListener('ended', () => { frame.classList.remove('is-playing'); v.controls = false; });
	});

	/* ── Lists styled as Marquee: names drift across the page ───────── */
	if (!reduced) {
		d.querySelectorAll('ul.wp-block-list.is-style-marquee').forEach((ul, n) => {
			const items = [...ul.children];
			if (!items.length) return;
			const run = () => {
				ul.classList.remove('is-moving');
				ul.querySelectorAll('[data-clone]').forEach((c) => c.remove());
				const w = items.reduce((sum, li) => sum + li.getBoundingClientRect().width, 0);
				if (!w) return;
				let total = w;
				while (total < ul.clientWidth + w) {
					items.forEach((li) => { const c = li.cloneNode(true); c.setAttribute('aria-hidden', 'true'); c.setAttribute('data-clone', ''); ul.appendChild(c); });
					total += w;
				}
				ul.style.setProperty('--marquee-w', w + 'px');
				ul.style.setProperty('--marquee-t', Math.max(20, w / 55) + 's');
				ul.querySelectorAll('li').forEach((li) => { li.style.animationDirection = n % 2 ? 'reverse' : ''; });
				ul.classList.add('is-moving');
			};
			if (d.fonts) d.fonts.ready.then(run); else run();
			let t;
			addEventListener('resize', () => { clearTimeout(t); t = setTimeout(run, 250); });
		});
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
			const t = e.target.closest('a, button, [data-cursor], label, summary');
			cursor.classList.toggle('is-hover', Boolean(t));
			if (tag) tag.textContent = (t && t.dataset.cursor) || '';
		});
	}

	/* Layout can change after fonts and images settle: keep triggers honest. */
	if (ST) {
		addEventListener('load', () => ST.refresh());
		if (d.fonts) d.fonts.ready.then(() => ST.refresh());
	}
})();
