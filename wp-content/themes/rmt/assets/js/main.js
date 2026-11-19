/* RMT – front-end interactions (vanilla JS, no dependencies). */
(function () {
	'use strict';
	var d = document, root = d.documentElement, body = d.body;
	root.classList.add('js');
	var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var $ = function (s, c) { return (c || d).querySelector(s); };
	var $$ = function (s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); };

	/* Sticky header shadow + back-to-top */
	var header = $('[data-header]'), top = $('[data-top]'), ticking = false;
	function onScroll() {
		var y = window.scrollY;
		if (header) header.classList.toggle('is-scrolled', y > 10);
		if (top) top.classList.toggle('show', y > 600);
		ticking = false;
	}
	window.addEventListener('scroll', function () { if (!ticking) { requestAnimationFrame(onScroll); ticking = true; } }, { passive: true });
	onScroll();
	if (top) top.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' }); });

	/* Mobile navigation */
	var burger = $('[data-burger]'), nav = $('#main-nav');
	function setNav(open) {
		body.classList.toggle('nav-open', open);
		burger.setAttribute('aria-expanded', open ? 'true' : 'false');
		burger.setAttribute('aria-label', open ? 'إغلاق القائمة' : 'فتح القائمة');
		body.style.overflow = open ? 'hidden' : '';
	}
	if (burger && nav) {
		burger.addEventListener('click', function () { setNav(!body.classList.contains('nav-open')); });
		nav.addEventListener('click', function (e) { if (e.target.closest('a')) setNav(false); });
		d.addEventListener('keydown', function (e) { if (e.key === 'Escape' && body.classList.contains('nav-open')) setNav(false); });
		window.addEventListener('resize', function () { if (window.innerWidth > 960 && body.classList.contains('nav-open')) setNav(false); });
	}

	/* Reveal on scroll */
	var reveals = $$('.reveal');
	if ('IntersectionObserver' in window && !reduce) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
		reveals.forEach(function (el) { io.observe(el); });
	} else {
		reveals.forEach(function (el) { el.classList.add('in'); });
	}

	/* Animated counters */
	var counters = $$('[data-count]');
	function countUp(el) {
		var end = parseFloat(el.getAttribute('data-count')) || 0, start = null, dur = 1600;
		if (reduce || !end) { el.textContent = end; return; }
		function step(ts) {
			if (!start) start = ts;
			var p = Math.min((ts - start) / dur, 1), e = 1 - Math.pow(1 - p, 3);
			el.textContent = Math.round(end * e);
			if (p < 1) requestAnimationFrame(step);
		}
		requestAnimationFrame(step);
	}
	if (counters.length && 'IntersectionObserver' in window) {
		var co = new IntersectionObserver(function (entries) {
			entries.forEach(function (en) { if (en.isIntersecting) { countUp(en.target); co.unobserve(en.target); } });
		}, { threshold: 0.6 });
		counters.forEach(function (c) { if (!reduce) c.textContent = '0'; co.observe(c); });
	}

	/* Hero background video: load only on larger screens, good connections, after page load */
	var hv = $('.hero-video');
	if (hv) {
		var conn = navigator.connection || {};
		var ok = !reduce && window.innerWidth > 640 && !conn.saveData && !/2g/.test(conn.effectiveType || '');
		if (ok) {
			window.addEventListener('load', function () {
				hv.src = hv.getAttribute('data-src');
				hv.addEventListener('canplay', function () { hv.play().then(function () { hv.classList.add('is-playing'); }).catch(function () {}); }, { once: true });
			});
		}
	}

	/* Generic filters (services + gallery) */
	$$('.filters').forEach(function (bar) {
		var scope = bar.parentNode;
		bar.addEventListener('click', function (e) {
			var btn = e.target.closest('[data-filter]');
			if (!btn) return;
			var f = btn.getAttribute('data-filter');
			$$('[data-filter]', bar).forEach(function (b) {
				var on = b === btn;
				b.classList.toggle('is-active', on);
				b.setAttribute('aria-selected', on ? 'true' : 'false');
			});
			var flip = $('[data-flip]', scope), first = new Map();
			if (flip && !reduce) $$('[data-type]', flip).forEach(function (it) { if (!it.classList.contains('is-hidden')) first.set(it, it.getBoundingClientRect()); });
			$$('[data-type]', scope).forEach(function (it) {
				var show = f === 'all' || it.getAttribute('data-type') === f;
				it.classList.toggle('is-hidden', !show);
				if (show) it.classList.add('in');
			});
			/* FLIP: slide kept cards to their new place, pop new ones in */
			if (flip && !reduce) $$('[data-type]', flip).forEach(function (it) {
				if (it.classList.contains('is-hidden')) return;
				var a = first.get(it), b = it.getBoundingClientRect();
				var kf = a ? [{ transform: 'translate(' + (a.left - b.left) + 'px,' + (a.top - b.top) + 'px)' }, { transform: 'none' }]
					: [{ opacity: 0, transform: 'scale(.85)' }, { opacity: 1, transform: 'none' }];
				it.animate(kf, { duration: 450, easing: 'cubic-bezier(.2,.7,.2,1)' });
			});
		});
	});

	/* Lightbox (images + videos) */
	var lb = $('[data-lightbox]');
	if (lb) {
		var stage = $('[data-lb-stage]', lb), cap = $('[data-lb-cap]', lb), list = [], idx = 0, lastFocus = null;
		function visibleItems() { return $$('.g-open').filter(function (b) { return !b.closest('.is-hidden'); }); }
		function render() {
			var b = list[idx]; if (!b) return;
			var kind = b.getAttribute('data-kind'), src = b.getAttribute('data-src'), title = b.getAttribute('data-title');
			stage.innerHTML = '';
			var el;
			if (kind === 'video') {
				el = d.createElement('video');
				el.src = src; el.controls = true; el.autoplay = true; el.playsInline = true;
				var p = b.getAttribute('data-poster'); if (p) el.poster = p;
			} else {
				el = d.createElement('img');
				el.src = src; el.alt = title || '';
			}
			stage.appendChild(el);
			cap.textContent = title ? title + '  (' + (idx + 1) + ' / ' + list.length + ')' : '';
		}
		function open(btn) {
			list = visibleItems(); idx = Math.max(0, list.indexOf(btn)); lastFocus = btn;
			lb.hidden = false; body.classList.add('lb-open'); render();
			$('[data-lb-close]', lb).focus();
		}
		function close() {
			lb.hidden = true; body.classList.remove('lb-open'); stage.innerHTML = '';
			if (lastFocus) lastFocus.focus();
		}
		function go(n) { idx = (idx + n + list.length) % list.length; render(); }
		d.addEventListener('click', function (e) {
			var b = e.target.closest('.g-open');
			if (b) { e.preventDefault(); open(b); }
		});
		$('[data-lb-close]', lb).addEventListener('click', close);
		$('[data-lb-prev]', lb).addEventListener('click', function () { go(-1); });
		$('[data-lb-next]', lb).addEventListener('click', function () { go(1); });
		lb.addEventListener('click', function (e) { if (e.target === lb) close(); });
		d.addEventListener('keydown', function (e) {
			if (lb.hidden) return;
			if (e.key === 'Escape') close();
			var rtl = root.getAttribute('dir') !== 'ltr';
			if (e.key === 'ArrowLeft') go(rtl ? 1 : -1);
			if (e.key === 'ArrowRight') go(rtl ? -1 : 1);
		});
		var sx = 0;
		lb.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
		lb.addEventListener('touchend', function (e) { var dx = e.changedTouches[0].clientX - sx; if (Math.abs(dx) > 50) go(dx < 0 ? 1 : -1); });
	}

	/* Map: click-to-load (keeps Google Maps out of the initial page weight) */
	var map = $('[data-map]');
	if (map) {
		var loadMap = function () {
			if (map.querySelector('iframe')) return;
			var f = d.createElement('iframe');
			f.src = map.getAttribute('data-map');
			f.title = 'موقعنا على خرائط Google';
			f.loading = 'lazy';
			f.referrerPolicy = 'no-referrer-when-downgrade';
			f.allowFullscreen = true;
			map.innerHTML = '';
			map.appendChild(f);
		};
		var mb = $('[data-map-load]', map);
		if (mb) mb.addEventListener('click', loadMap);
		if ('IntersectionObserver' in window) {
			var mo = new IntersectionObserver(function (en) { if (en[0].isIntersecting) { setTimeout(loadMap, 600); mo.disconnect(); } }, { rootMargin: '200px' });
			mo.observe(map);
		}
	}

	/* Quote form validation */
	var form = $('[data-form]');
	if (form) {
		form.addEventListener('submit', function (e) {
			var bad = null;
			$$('[required]', form).forEach(function (inp) {
				var wrap = inp.closest('.fld, .consent'), valid = inp.type === 'checkbox' ? inp.checked : (inp.value.trim() !== '' && (!inp.pattern || new RegExp('^(?:' + inp.pattern + ')$').test(inp.value.trim())));
				if (wrap) wrap.classList.toggle('invalid', !valid);
				if (!valid && !bad) bad = inp;
			});
			if (bad) { e.preventDefault(); bad.focus(); return; }
			var btn = form.querySelector('[type=submit]');
			btn.disabled = true; btn.style.opacity = '.7';
			btn.lastChild.textContent = 'جاري الإرسال…';
		});
		form.addEventListener('input', function (e) { var w = e.target.closest('.fld'); if (w) w.classList.remove('invalid'); });
	}

	/* ---------- Page-transition loader ---------- */
	(function () {
		var start = performance.now(), MIN = 1500;
		function done() {
			if (!root.classList.contains('is-loading')) return;
			var wait = Math.max(0, MIN - (performance.now() - start));
			setTimeout(function () {
				root.classList.add('is-loaded');
				setTimeout(function () { root.classList.remove('is-loading', 'is-loaded'); }, 1500);
			}, wait);
		}
		if (d.readyState === 'complete') done(); else window.addEventListener('load', done);
		setTimeout(done, 2600); /* never hold the page longer than this */

		/* Back/forward cache: never show a stuck curtain */
		window.addEventListener('pageshow', function (e) { if (e.persisted) root.classList.remove('is-leaving', 'is-loading', 'is-loaded'); });

		if (reduce) return;
		d.addEventListener('click', function (e) {
			var a = e.target.closest('a[href]');
			if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
			if (a.target && a.target !== '_self') return;
			if (a.hasAttribute('download') || a.closest('.g-open')) return;
			var url;
			try { url = new URL(a.href, location.href); } catch (err) { return; }
			if (url.origin !== location.origin) return;
			if (/\/wp-(admin|login)|\.(pdf|jpe?g|png|webp|mp4|zip)$/i.test(url.pathname)) return;
			if (url.pathname === location.pathname && url.search === location.search) return; /* same page / anchors */
			e.preventDefault();
			try { sessionStorage.setItem('rmt-nav', '1'); } catch (err) {}
			root.classList.add('is-leaving');
			setTimeout(function () { location.href = url.href; }, 750);
		});
	})();

	/* ---------- Tower: services from rooftop to ground ---------- */
	var tower = $('[data-tower]');
	if (tower) {
		var chips = $$('.tw-chip', tower), cards = $$('.tw-card', tower), spots = $$('.tw-spot', tower);
		var prog = $('.tw-progress', tower), cur = 0, timer = null, DUR = 5000, user = false, visible = false;
		tower.style.setProperty('--tw-dur', DUR + 'ms');
		function show(i, fromUser) {
			cur = (i + cards.length) % cards.length;
			cards.forEach(function (c, n) { var on = n === cur; c.hidden = !on; c.classList.toggle('is-on', on); });
			chips.forEach(function (c, n) { var on = n === cur; c.classList.toggle('is-on', on); c.setAttribute('aria-selected', on ? 'true' : 'false'); });
			spots.forEach(function (s) { s.classList.toggle('is-on', +s.getAttribute('data-go') === cur); });
			var chip = chips[cur];
			if (chip && chip.parentNode.scrollWidth > chip.parentNode.clientWidth) {
				chip.parentNode.scrollTo({ left: chip.offsetLeft - chip.parentNode.clientWidth / 2 + chip.clientWidth / 2, behavior: reduce ? 'auto' : 'smooth' });
			}
			if (fromUser) { user = true; stop(); }
			else restart();
		}
		function restart() {
			stop();
			if (reduce || user || !visible) return;
			prog.classList.remove('run'); void prog.offsetWidth; prog.classList.add('run');
			timer = setTimeout(function () { show(cur + 1); }, DUR);
		}
		function stop() { clearTimeout(timer); prog.classList.remove('run'); }
		tower.addEventListener('click', function (e) {
			var b = e.target.closest('[data-go]');
			if (b) show(+b.getAttribute('data-go'), true);
		});
		var tabs = $('.tw-chips', tower);
		tabs.addEventListener('keydown', function (e) {
			var rtl = root.getAttribute('dir') !== 'ltr';
			var k = e.key, dir = 0;
			if (k === 'ArrowRight') dir = rtl ? -1 : 1;
			if (k === 'ArrowLeft') dir = rtl ? 1 : -1;
			if (!dir) return;
			e.preventDefault(); show(cur + dir, true); chips[cur].focus();
		});
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (en) { visible = en[0].isIntersecting; visible ? restart() : stop(); }, { threshold: 0.35 }).observe(tower);
		}
		show(0);
	}

	/* ---------- About: rope descent tied to scroll ---------- */
	var desc = $('[data-descent] .descent-track');
	if (desc) {
		var floors = $$('.floor', desc), dt = false;
		function onDesc() {
			var r = desc.getBoundingClientRect(), vh = window.innerHeight;
			var p = (vh * 0.55 - r.top) / r.height;
			p = Math.max(0, Math.min(1, p));
			desc.style.setProperty('--p', p.toFixed(4));
			var y = r.top + p * r.height;
			floors.forEach(function (f) {
				var fr = f.getBoundingClientRect();
				f.classList.toggle('is-on', fr.top + fr.height / 2 <= y + 4);
			});
			dt = false;
		}
		if (reduce) { desc.style.setProperty('--p', '1'); floors.forEach(function (f) { f.classList.add('is-on'); }); }
		else {
			window.addEventListener('scroll', function () { if (!dt) { requestAnimationFrame(onDesc); dt = true; } }, { passive: true });
			window.addEventListener('resize', onDesc);
			onDesc();
		}
	}

	/* ---------- Client cards: tilt + light that follows the pointer ---------- */
	if (!reduce && window.matchMedia('(hover: hover)').matches) {
		$$('[data-tilt]').forEach(function (card) {
			card.addEventListener('pointermove', function (e) {
				var r = card.getBoundingClientRect(), x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
				card.style.transform = 'perspective(700px) rotateX(' + ((.5 - y) * 10).toFixed(2) + 'deg) rotateY(' + ((x - .5) * 12).toFixed(2) + 'deg) translateY(-4px)';
				card.style.setProperty('--mx', (x * 100) + '%'); card.style.setProperty('--my', (y * 100) + '%');
			});
			card.addEventListener('pointerleave', function () { card.style.transform = ''; });
		});
	}

	/* ---------- Before / After gallery ---------- */
	var baSec = $('[data-ba]');
	if (baSec) (function () {
		var data, t9n;
		try { data = JSON.parse($('[data-ba-data]', baSec).textContent); t9n = JSON.parse($('[data-ba-i18n]', baSec).textContent); } catch (e) { return; }
		var stage = $('[data-ba-stage]', baSec), info = $('.ba-info', baSec), sparks = $('.ba-sparks', baSec);
		var imgB = $('[data-ba-img="before"]', stage), imgA = $('[data-ba-img="after"]', stage);
		var stateEl = $('[data-ba-state] em', stage), curEl = $('[data-ba-cur]', stage);
		var toggles = $$('[data-ba-go]', stage), thumbs = $$('.ba-thumb', baSec);
		var rtl = root.getAttribute('dir') !== 'ltr';
		var idx = 0, pos = 0, anim = null, timers = [], auto = !reduce, visible = false, DUR = 7000;
		baSec.style.setProperty('--ba-dur', DUR + 'ms');

		function setPos(v, sparkle) {
			pos = Math.max(0, Math.min(100, v));
			stage.style.setProperty('--pos', pos);
			stage.setAttribute('aria-valuenow', Math.round(pos));
			stage.toggleAttribute('data-pos-zero', pos <= 0.5);
			stage.toggleAttribute('data-pos-full', pos >= 99.5);
			var after = pos >= 50;
			stage.classList.toggle('is-after', after);
			stateEl.textContent = after ? t9n.after : t9n.before;
			toggles.forEach(function (b) { b.setAttribute('aria-pressed', (+b.getAttribute('data-ba-go') === 100) === after ? 'true' : 'false'); });
			if (sparkle && !reduce && Math.random() < 0.35) spark();
		}
		function spark() {
			var s = d.createElement('span'), x = rtl ? 100 - pos : pos;
			s.className = 'ba-spark';
			s.style.left = 'calc(' + x + '% + ' + (rtl ? 6 : -16) + 'px)';
			s.style.top = (10 + Math.random() * 80) + '%';
			s.style.width = s.style.height = (8 + Math.random() * 10) + 'px';
			sparks.appendChild(s);
			setTimeout(function () { s.remove(); }, 1000);
		}
		function animateTo(target, ms, done) {
			cancelAnimationFrame(anim);
			if (reduce) { setPos(target); if (done) done(); return; }
			var from = pos, t0 = null;
			function step(ts) {
				if (!t0) t0 = ts;
				var p = Math.min((ts - t0) / ms, 1), e = p < .5 ? 4 * p * p * p : 1 - Math.pow(-2 * p + 2, 3) / 2;
				setPos(from + (target - from) * e, true);
				if (p < 1) anim = requestAnimationFrame(step); else if (done) done();
			}
			anim = requestAnimationFrame(step);
		}
		function countUp(el, raw) {
			var m = String(raw).match(/^([^0-9]*)([0-9][0-9,.]*)(.*)$/);
			if (!m || reduce) { el.textContent = raw; return; }
			var end = parseFloat(m[2].replace(/,/g, '')), t0 = null;
			function step(ts) {
				if (!t0) t0 = ts;
				var p = Math.min((ts - t0) / 900, 1), v = Math.round(end * (1 - Math.pow(1 - p, 3)));
				el.textContent = m[1] + v.toLocaleString('en-US') + m[3];
				if (p < 1) requestAnimationFrame(step);
			}
			requestAnimationFrame(step);
		}
		function show(i) {
			idx = (i + data.length) % data.length;
			var p = data[idx];
			stage.classList.add('is-changing');
			setTimeout(function () {
				imgB.src = p.before; imgA.src = p.after;
				imgB.alt = p.title + ' – ' + t9n.beforeAlt; imgA.alt = p.title + ' – ' + t9n.afterAlt;
				setPos(reduce ? 100 : 0);
				stage.classList.remove('is-changing');
			}, reduce ? 0 : 300);
			curEl.textContent = (idx < 9 ? '0' : '') + (idx + 1);
			['title', 'service', 'place'].forEach(function (k) { var el = $('[data-ba-f="' + k + '"]', info); if (el) el.textContent = p[k] || ''; });
			$$('[data-ba-fact]', info).forEach(function (f) {
				var k = f.getAttribute('data-ba-fact'), v = (p[k] || '').trim();
				f.hidden = !v;
				if (v) countUp($('[data-ba-num]', f), v);
			});
			info.classList.remove('swap'); void info.offsetWidth; info.classList.add('swap');
			thumbs.forEach(function (t) { t.setAttribute('aria-pressed', +t.getAttribute('data-ba-i') === idx ? 'true' : 'false'); });
			preload(idx + 1);
		}
		function preload(i) { var p = data[(i + data.length) % data.length]; [p.before, p.after].forEach(function (u) { var im = new Image(); im.src = u; }); }

		/* Autoplay: before (1.4s) -> wipe -> hold -> next project */
		function clearTimers() { timers.forEach(clearTimeout); timers = []; baSec.classList.remove('playing'); }
		function cycle() {
			clearTimers();
			if (!auto || !visible) return;
			baSec.classList.remove('playing'); void baSec.offsetWidth; baSec.classList.add('playing');
			timers.push(setTimeout(function () { animateTo(100, 1600); }, 1400));
			if (data.length > 1) timers.push(setTimeout(function () { show(idx + 1); timers.push(setTimeout(cycle, 350)); }, DUR));
		}
		function userTook() { auto = false; clearTimers(); stage.classList.add('touched'); }

		/* Drag on the photo */
		var dragging = false, sx = 0, sy = 0, decided = false, horiz = false;
		function posFromX(x) { var r = stage.getBoundingClientRect(), p = (x - r.left) / r.width * 100; return rtl ? 100 - p : p; }
		stage.addEventListener('pointerdown', function (e) {
			if (e.target.closest('button')) return;
			dragging = true; decided = e.pointerType === 'mouse'; horiz = decided; sx = e.clientX; sy = e.clientY;
			if (horiz) { stage.setPointerCapture(e.pointerId); userTook(); cancelAnimationFrame(anim); setPos(posFromX(e.clientX)); }
		});
		stage.addEventListener('pointermove', function (e) {
			if (!dragging) return;
			if (!decided) {
				var dx = Math.abs(e.clientX - sx), dy = Math.abs(e.clientY - sy);
				if (dx < 6 && dy < 6) return;
				decided = true; horiz = dx > dy;
				if (horiz) { stage.setPointerCapture(e.pointerId); userTook(); cancelAnimationFrame(anim); }
			}
			if (horiz) setPos(posFromX(e.clientX), true);
		});
		['pointerup', 'pointercancel'].forEach(function (ev) { stage.addEventListener(ev, function () { dragging = false; }); });
		stage.addEventListener('keydown', function (e) {
			var k = e.key, step = e.shiftKey ? 20 : 8, dir = 0;
			if (k === 'ArrowRight') dir = rtl ? -1 : 1;
			if (k === 'ArrowLeft') dir = rtl ? 1 : -1;
			if (k === 'Home') { e.preventDefault(); userTook(); setPos(0); }
			if (k === 'End') { e.preventDefault(); userTook(); setPos(100); }
			if (dir) { e.preventDefault(); userTook(); cancelAnimationFrame(anim); setPos(pos + dir * step); }
		});

		toggles.forEach(function (b) { b.addEventListener('click', function () { userTook(); animateTo(+b.getAttribute('data-ba-go'), 1100); }); });
		thumbs.forEach(function (t) { t.addEventListener('click', function () { userTook(); show(+t.getAttribute('data-ba-i')); setTimeout(function () { animateTo(100, 1400); }, 700); }); });
		$$('[data-ba-step]', baSec).forEach(function (b) {
			b.addEventListener('click', function () { userTook(); show(idx + (+b.getAttribute('data-ba-step'))); setTimeout(function () { animateTo(100, 1400); }, 700); });
		});

		/* Swipe between projects on the info card (touch) */
		var tx = 0;
		info.addEventListener('touchstart', function (e) { tx = e.touches[0].clientX; }, { passive: true });
		info.addEventListener('touchend', function (e) {
			var dx = e.changedTouches[0].clientX - tx;
			if (Math.abs(dx) > 50 && data.length > 1) { userTook(); show(idx + ((dx < 0) !== rtl ? 1 : -1)); setTimeout(function () { animateTo(100, 1400); }, 700); }
		});

		baSec.addEventListener('focusin', function (e) { if (e.target !== baSec) { auto = false; clearTimers(); } });

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (en) { visible = en[0].isIntersecting; if (visible) cycle(); else clearTimers(); }, { threshold: 0.4 }).observe(stage);
		}
		setPos(reduce ? 100 : 0);
		show(0);
	})();

	/* ---------- Blog article: reading progress, active TOC, copy link ---------- */
	var bar = $('[data-progress]'), art = $('.post-content');
	if (bar && art) {
		var heads = $$('.post-content h2[id], .post-content h3[id]');
		var links = $$('.toc a');
		var pt = false;
		function onRead() {
			var r = art.getBoundingClientRect(), vh = window.innerHeight;
			var p = Math.max(0, Math.min(1, (vh * 0.3 - r.top) / Math.max(1, r.height - vh * 0.4)));
			bar.style.transform = 'scaleX(' + p.toFixed(3) + ')';
			var cur = null;
			heads.forEach(function (h) { if (h.getBoundingClientRect().top < 140) cur = h.id; });
			links.forEach(function (a) { a.classList.toggle('is-active', a.getAttribute('href') === '#' + cur); });
			pt = false;
		}
		window.addEventListener('scroll', function () { if (!pt) { requestAnimationFrame(onRead); pt = true; } }, { passive: true });
		onRead();
		var toc = $('[data-toc]');
		if (toc && window.innerWidth < 960) toc.removeAttribute('open');
	}
	var copyBtn = $('[data-copy]');
	if (copyBtn) {
		copyBtn.addEventListener('click', function () {
			var u = copyBtn.getAttribute('data-copy'), label = copyBtn.querySelector('span');
			function ok() { copyBtn.classList.add('done'); label.textContent = 'تم النسخ ✔'; setTimeout(function () { copyBtn.classList.remove('done'); label.textContent = 'نسخ الرابط'; }, 2000); }
			if (navigator.clipboard) navigator.clipboard.writeText(u).then(ok, function () { window.prompt('انسخ الرابط:', u); });
			else window.prompt('انسخ الرابط:', u);
		});
	}

	/* Conversion tracking hooks (GA4 / GTM if present) */
	d.addEventListener('click', function (e) {
		var t = e.target.closest('[data-track]');
		if (!t) return;
		var ev = t.getAttribute('data-track') === 'call' ? 'click_call' : 'click_whatsapp';
		if (window.gtag) window.gtag('event', ev, { link_url: t.href });
		if (window.dataLayer) window.dataLayer.push({ event: ev });
	});
})();
