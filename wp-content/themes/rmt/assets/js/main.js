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
			$$('[data-type]', scope).forEach(function (it) {
				var show = f === 'all' || it.getAttribute('data-type') === f;
				it.classList.toggle('is-hidden', !show);
				if (show) it.classList.add('in');
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
			if (e.key === 'ArrowLeft') go(1);   /* RTL: left = next */
			if (e.key === 'ArrowRight') go(-1);
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
				var wrap = inp.closest('.fld'), valid = inp.value.trim() !== '' && (!inp.pattern || new RegExp('^(?:' + inp.pattern + ')$').test(inp.value.trim()));
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

	/* Conversion tracking hooks (GA4 / GTM if present) */
	d.addEventListener('click', function (e) {
		var t = e.target.closest('[data-track]');
		if (!t) return;
		var ev = t.getAttribute('data-track') === 'call' ? 'click_call' : 'click_whatsapp';
		if (window.gtag) window.gtag('event', ev, { link_url: t.href });
		if (window.dataLayer) window.dataLayer.push({ event: ev });
	});
})();
