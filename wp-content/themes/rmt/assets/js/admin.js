/* RMT control panel: tabs, repeaters, media pickers, SEO counters, search, interactive tour. */
(function ($) {
	'use strict';
	var $form = $('.rmt-form');
	if (!$form.length) return;

	/* ---------- Tabs ---------- */
	function showTab(id) {
		if (!$('#panel-' + id).length) id = 'general';
		$('.rmt-tabs a').removeClass('is-active').filter('[data-tab="' + id + '"]').addClass('is-active');
		$('.rmt-panel').removeClass('is-active').filter('#panel-' + id).addClass('is-active');
		$('.rmt-current-tab').val(id);
		if (history.replaceState) history.replaceState(null, '', '#' + id);
	}
	$('.rmt-tabs').on('click', 'a', function (e) { e.preventDefault(); showTab($(this).data('tab')); window.scrollTo(0, 0); });
	showTab((location.hash || '#general').slice(1));

	/* ---------- Dirty state ---------- */
	var dirty = false;
	$form.on('input change', ':input', function () { dirty = true; $form.addClass('is-dirty'); });
	$form.on('submit', function () { dirty = false; });
	window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
	function markDirty() { dirty = true; $form.addClass('is-dirty'); }

	/* ---------- Media picker ---------- */
	function setPreview($box, url) {
		var $p = $box.find('.rmt-media-preview').empty();
		if (!url) return;
		if (/\.(mp4|webm|mov)(\?|$)/i.test(url)) $p.append($('<video muted preload="metadata">').attr('src', url));
		else $p.append($('<img alt="">').attr('src', url));
	}
	$form.on('click', '.rmt-media-pick', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.rmt-media'), lib = $box.data('lib');
		var opts = { title: 'اختر ملفًا أو ارفع ملفًا جديدًا', button: { text: 'استخدام هذا الملف' }, multiple: false };
		if (lib) opts.library = { type: lib };
		var frame = wp.media(opts);
		frame.on('select', function () {
			var a = frame.state().get('selection').first().toJSON();
			$box.find('.rmt-media-url').val(a.url).trigger('change');
			setPreview($box, a.url);
			// Auto-fill alt/title in the same repeater row if empty.
			var $row = $box.closest('.rmt-row');
			if ($row.length) {
				var $t = $row.find('[name$="[title]"], [name$="[name]"]').first();
				if ($t.length && !$t.val()) $t.val(a.alt || a.title || '').trigger('input');
				var $type = $row.find('select[name$="[type]"]');
				if ($type.length && a.type) $type.val(a.type === 'video' ? 'video' : 'image');
			}
		});
		frame.open();
	});
	$form.on('click', '.rmt-media-clear', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.rmt-media');
		$box.find('.rmt-media-url').val('').trigger('change');
		setPreview($box, '');
	});
	$form.on('change', '.rmt-media-url', function () { setPreview($(this).closest('.rmt-media'), this.value); });

	/* ---------- Repeaters ---------- */
	var uid = Date.now();
	function newRow($rep) {
		var html = $rep.find('.rmt-row-tpl').html().replace(/__i__/g, 'n' + (uid++));
		var $row = $(html).addClass('is-open');
		$rep.find('.rmt-rows').append($row);
		updateCount($rep);
		markDirty();
		return $row;
	}
	function updateCount($rep) { $rep.find('> label .rmt-count').text('(' + $rep.find('.rmt-row').length + ')'); }

	$form.on('click', '.rmt-row-add', function (e) {
		e.preventDefault();
		var $row = newRow($(this).closest('.rmt-repeater'));
		$row.find(':input:visible').first().trigger('focus');
	});
	$form.on('click', '.rmt-row-head', function (e) {
		if ($(e.target).is('button, .rmt-handle')) return;
		$(this).closest('.rmt-row').toggleClass('is-open');
	});
	$form.on('click', '.rmt-row-toggle', function (e) { e.preventDefault(); $(this).closest('.rmt-row').toggleClass('is-open'); });
	$form.on('click', '.rmt-row-del', function (e) {
		e.preventDefault();
		if (!window.confirm('حذف هذا العنصر؟')) return;
		var $rep = $(this).closest('.rmt-repeater');
		$(this).closest('.rmt-row').slideUp(150, function () { $(this).remove(); updateCount($rep); });
		markDirty();
	});
	$form.on('click', '.rmt-row-dup', function (e) {
		e.preventDefault();
		var $row = $(this).closest('.rmt-row'), $rep = $row.closest('.rmt-repeater');
		var $clone = $row.clone();
		var base = $rep.data('base'), id = 'n' + (uid++);
		$clone.find(':input[name]').each(function () {
			var n = this.name.slice(base.length).replace(/^\[[^\]]+\]/, '[' + id + ']');
			this.name = base + n;
		});
		// Copy select values (clone() loses them).
		$row.find('select').each(function (i) { $clone.find('select').eq(i).val($(this).val()); });
		$row.after($clone);
		updateCount($rep);
		markDirty();
	});
	$form.on('input', '.rmt-row [name$="[title]"], .rmt-row [name$="[name]"], .rmt-row [name$="[q]"], .rmt-row [name$="[label]"], .rmt-row [name$="[year]"]', function () {
		$(this).closest('.rmt-row').find('.rmt-row-title').text(this.value || 'عنصر جديد');
	});
	$('.rmt-rows').sortable({ handle: '.rmt-handle', placeholder: 'rmt-row-placeholder', axis: 'y', update: markDirty });

	/* Bulk add from the media library (clients logos / portfolio) */
	$form.on('click', '.rmt-bulk-add', function (e) {
		e.preventDefault();
		var $rep = $(this).closest('.rmt-repeater'), kind = $(this).data('kind');
		var frame = wp.media({
			title: kind === 'clients' ? 'اختر شعارات العملاء (يمكن اختيار أكثر من ملف)' : 'اختر الصور والفيديوهات (يمكن اختيار أكثر من ملف)',
			button: { text: 'إضافة المحدد' },
			multiple: 'add',
			library: kind === 'clients' ? { type: 'image' } : { type: ['image', 'video'] }
		});
		frame.on('select', function () {
			frame.state().get('selection').each(function (m) {
				var a = m.toJSON(), $row = newRow($rep).removeClass('is-open');
				var label = a.alt || a.title || '';
				if (kind === 'clients') {
					$row.find('[name$="[logo]"]').val(a.url).trigger('change');
					$row.find('[name$="[name]"]').val(label).trigger('input');
				} else {
					var isVid = a.type === 'video';
					$row.find('select[name$="[type]"]').val(isVid ? 'video' : 'image');
					$row.find('[name$="[src]"]').val(a.url).trigger('change');
					if (isVid && a.image && a.image.src && !/wp-includes/.test(a.image.src)) $row.find('[name$="[poster]"]').val(a.image.src).trigger('change');
					$row.find('[name$="[title]"]').val(label).trigger('input');
				}
			});
			markDirty();
		});
		frame.open();
	});

	/* ---------- SEO counters ---------- */
	function count($w) {
		var max = +$w.data('max'), $i = $w.find('input, textarea'), n = ($i.val() || '').length;
		$w.find('.rmt-counter b').text(n);
		$w.find('.rmt-bar i').css('width', Math.min(100, n / max * 100) + '%');
		var min = max === 60 ? 30 : 110;
		$w.toggleClass('bad', n > max || n === 0).toggleClass('warn', n > 0 && n < min);
	}
	$('.rmt-seo-field').each(function () { count($(this)); }).on('input', 'input, textarea', function () { count($(this).closest('.rmt-seo-field')); });

	/* ---------- Field search ---------- */
	$('.rmt-search').on('input', function () {
		var q = $.trim(this.value);
		$('.rmt-field').removeClass('is-match');
		if (q.length < 2) return;
		var $hit = $('.rmt-field').filter(function () { return $(this).children('label').text().indexOf(q) > -1; }).first();
		if ($hit.length) {
			showTab($hit.closest('.rmt-panel').data('panel'));
			$hit.closest('.rmt-row').addClass('is-open');
			$hit.addClass('is-match');
			$('html, body').animate({ scrollTop: $hit.offset().top - 120 }, 250);
		}
	});

	/* ---------- Interactive tour ---------- */
	var steps = [
		{ el: '[data-tour="top"]', tab: 'general', t: 'أهلًا بك في لوحة رفال 👋', d: 'من هنا تتحكم في كل محتوى الموقع بدون أي كود. الجولة تستغرق دقيقة واحدة.' },
		{ el: '[data-tour="tabs"]', tab: 'general', t: 'أقسام اللوحة', d: 'كل قسم من الموقع له تبويب: البيانات العامة، الرئيسية، من نحن، الخدمات، سابقة الأعمال، العملاء، التواصل، السيو، والفوتر.' },
		{ el: '#panel-general .rmt-card:nth-of-type(2)', tab: 'general', t: 'أرقام التواصل', d: 'غيّر رقم الجوال أو واتساب أو الإيميل هنا مرة واحدة، فيتحدث تلقائيًا في كل مكان بالموقع.' },
		{ el: '[data-tour="rep-services-items"]', tab: 'services', t: 'إدارة الخدمات', d: 'اضغط على أي خدمة لتعديلها، واسحب من أيقونة ☰ لإعادة الترتيب، واستخدم "إضافة عنصر" لخدمة جديدة.' },
		{ el: '[data-tour="rep-projects-items"]', tab: 'projects', t: 'سابقة الأعمال', d: 'زر "إضافة دفعة من المكتبة" يسمح برفع عدة صور وفيديوهات مرة واحدة. أضف صورة غلاف لكل فيديو لسرعة أعلى.' },
		{ el: '[data-tour="rep-clients-items"]', tab: 'clients', t: 'شعارات العملاء', d: 'ارفع شعارات العملاء دفعة واحدة. العميل بدون شعار يظهر ببطاقة أنيقة باسمه حتى ترفع شعاره.' },
		{ el: '#panel-seo .rmt-card:nth-of-type(2)', tab: 'seo', t: 'السيو لكل صفحة', d: 'عنوان ووصف كل صفحة كما يظهر في جوجل. العدّاد يتحول للأخضر عندما يكون الطول مثاليًا.' },
		{ el: '[data-tour="seo-check"]', tab: 'seo', t: 'فحص السيو السريع', d: 'قائمة تتأكد من الإعدادات المهمة: الأرشفة، الروابط، HTTPS، Search Console، Analytics. أكمل البنود الصفراء.' },
		{ el: '[data-tour="save"]', tab: null, t: 'لا تنسَ الحفظ', d: 'زر الحفظ ثابت أسفل الشاشة، ويظهر تنبيه إذا كانت لديك تغييرات غير محفوظة.' },
		{ el: '[data-tour="help"]', tab: null, t: 'الدليل متاح دائمًا', d: 'يمكنك إعادة هذه الجولة في أي وقت من هذا الزر، أو فتح صفحة "دليل الاستخدام" من القائمة الجانبية.' }
	];
	var cur = 0, $hole, $tip, startTab;
	function place() {
		var s = steps[cur], $el = $(s.el).filter(':visible').first();
		if (!$el.length) { return next(); }
		$el[0].scrollIntoView({ block: 'center' });
		setTimeout(function () {
			var r = $el[0].getBoundingClientRect(), pad = 8, vw = window.innerWidth, vh = window.innerHeight;
			var h = Math.min(r.height + pad * 2, vh - 40);
			$hole.css({ top: r.top - pad, left: r.left - pad, width: r.width + pad * 2, height: h });
			$tip.find('h4').text(s.t);
			$tip.find('p').text(s.d);
			$tip.find('.rmt-tour-dots').html(steps.map(function (x, i) { return '<i' + (i === cur ? ' class="on"' : '') + '></i>'; }).join(''));
			$tip.find('.rmt-tour-prev').prop('disabled', cur === 0);
			$tip.find('.rmt-tour-next').text(cur === steps.length - 1 ? 'إنهاء ✔' : 'التالي');
			var tw = $tip.outerWidth(), th = $tip.outerHeight();
			var top = r.bottom + 16;
			if (top + th > vh - 10) top = Math.max(10, r.top - th - 16);
			if (top < 10) top = Math.max(10, vh - th - 20);
			var left = Math.min(Math.max(10, r.left + r.width / 2 - tw / 2), vw - tw - 10);
			$tip.css({ top: top, left: left });
		}, 280);
	}
	function go(i) {
		cur = i;
		if (steps[cur].tab) showTab(steps[cur].tab);
		place();
	}
	function next() { if (cur < steps.length - 1) go(cur + 1); else endTour(); }
	function endTour() {
		$hole && $hole.remove(); $tip && $tip.remove(); $(window).off('resize.rmtTour');
		if (startTab) showTab(startTab);
		$.post(RMT_ADMIN.ajax, { action: 'rmt_tour_done', nonce: RMT_ADMIN.nonce });
	}
	function startTour() {
		startTab = $('.rmt-current-tab').val();
		$hole = $('<div class="rmt-tour-hole"></div>').appendTo(document.body);
		$tip = $('<div class="rmt-tour-tip" role="dialog" aria-live="polite"><h4></h4><p></p><div class="rmt-tour-foot"><span class="rmt-tour-dots"></span><button type="button" class="button-link rmt-tour-skip">تخطي</button><button type="button" class="button rmt-tour-prev">السابق</button><button type="button" class="button button-primary rmt-tour-next">التالي</button></div></div>').appendTo(document.body);
		$tip.on('click', '.rmt-tour-next', next)
			.on('click', '.rmt-tour-prev', function () { if (cur > 0) go(cur - 1); })
			.on('click', '.rmt-tour-skip', endTour);
		$(window).on('resize.rmtTour', place);
		$(document).on('keydown.rmtTour', function (e) {
			if (!$tip || !$tip.closest('body').length) { $(document).off('keydown.rmtTour'); return; }
			if (e.key === 'Escape') endTour();
			if (e.key === 'ArrowLeft') next();
			if (e.key === 'ArrowRight' && cur > 0) go(cur - 1);
		});
		go(0);
	}
	$('.rmt-start-tour').on('click', startTour);
	if (RMT_ADMIN.firstVisit || /[?&]tour=1/.test(location.search)) setTimeout(startTour, 500);
})(jQuery);
