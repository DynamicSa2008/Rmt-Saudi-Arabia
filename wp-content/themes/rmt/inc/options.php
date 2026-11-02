<?php
/**
 * Options storage, getters and the field schema that drives the control panel.
 *
 * All site content is stored in ONE autoloaded option (`rmt_options`) to keep
 * the site light: one DB query loads everything.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

const RMT_OPTION = 'rmt_options';

/**
 * Get merged options (saved values over defaults). Cached per request.
 *
 * @return array
 */
function rmt_options() {
	static $cache = null;
	if ( null !== $cache && ! doing_action( 'update_option_' . RMT_OPTION ) ) {
		return $cache;
	}
	$defaults = rmt_defaults();
	$saved    = get_option( RMT_OPTION, array() );
	$saved    = is_array( $saved ) ? $saved : array();
	foreach ( $defaults as $section => $values ) {
		if ( isset( $saved[ $section ] ) && is_array( $saved[ $section ] ) ) {
			$defaults[ $section ] = array_merge( $values, $saved[ $section ] );
		}
	}
	$cache = $defaults;
	return $cache;
}

/**
 * Get a single value: rmt_get( 'general', 'phone' ).
 *
 * @param string $section Section key.
 * @param string $key     Field key.
 * @param mixed  $default Fallback.
 * @return mixed
 */
function rmt_get( $section, $key = null, $default = '' ) {
	$o = rmt_options();
	if ( null === $key ) {
		return isset( $o[ $section ] ) ? $o[ $section ] : array();
	}
	return isset( $o[ $section ][ $key ] ) ? $o[ $section ][ $key ] : $default;
}

/** Echo escaped text value. */
function rmt_e( $section, $key ) {
	echo esc_html( rmt_get( $section, $key ) );
}

/** Logo URL (uploaded or bundled). */
function rmt_logo_url( $size = 'full' ) {
	$logo = rmt_get( 'general', 'logo' );
	if ( $logo ) {
		return $logo;
	}
	return get_template_directory_uri() . ( 'small' === $size ? '/assets/img/logo-300.png' : '/assets/img/logo.png' );
}

/** International tel link e.g. +966505203019 */
function rmt_tel( $raw = null ) {
	$raw    = null === $raw ? rmt_get( 'general', 'phone' ) : $raw;
	$digits = preg_replace( '/\D+/', '', $raw );
	if ( 0 === strpos( $digits, '05' ) ) {
		$digits = '966' . substr( $digits, 1 );
	}
	return '+' . ltrim( $digits, '+' );
}

/** WhatsApp link with prefilled message. */
function rmt_whatsapp_url( $msg = null ) {
	$num = preg_replace( '/\D+/', '', rmt_get( 'general', 'whatsapp' ) );
	$msg = null === $msg ? rmt_get( 'general', 'whatsapp_msg' ) : $msg;
	return 'https://wa.me/' . $num . ( $msg ? '?text=' . rawurlencode( $msg ) : '' );
}

/** Human-friendly phone display: 050 520 3019 */
function rmt_phone_display() {
	$d = preg_replace( '/\D+/', '', rmt_get( 'general', 'phone' ) );
	if ( 10 === strlen( $d ) ) {
		return substr( $d, 0, 3 ) . ' ' . substr( $d, 3, 3 ) . ' ' . substr( $d, 6 );
	}
	return rmt_get( 'general', 'phone' );
}

/** URL of one of the 5 core pages by key. */
function rmt_page_url( $key ) {
	if ( 'home' === $key ) {
		return home_url( '/' );
	}
	$page = get_page_by_path( $key );
	return $page ? get_permalink( $page ) : home_url( '/' . $key . '/' );
}

/**
 * Schema for the control panel. Field keys are "section.key".
 *
 * @return array
 */
function rmt_panel_schema() {
	$icons = array(
		'glass' => 'زجاج', 'rope' => 'حبال', 'tower' => 'برج', 'paint' => 'دهان', 'home' => 'سكني',
		'sofa' => 'كنب', 'bug' => 'حشرات', 'fan' => 'تكييف', 'flame' => 'مداخن', 'ship' => 'سفن', 'contract' => 'عقود',
		'shield' => 'سلامة', 'star' => 'نجمة', 'clock' => 'وقت', 'building' => 'مبنى',
	);

	return array(
		'general' => array(
			'title' => 'البيانات العامة',
			'icon'  => 'dashicons-building',
			'desc'  => 'اسم المؤسسة والشعار وأرقام التواصل والعنوان. هذه البيانات تظهر في الهيدر والفوتر وصفحة التواصل وبيانات السيو.',
			'groups'=> array(
				array( 'title' => 'الهوية', 'fields' => array(
					array( 'general.company_name', 'text', 'الاسم الرسمي للمؤسسة' ),
					array( 'general.short_name', 'text', 'الاسم المختصر', 'يظهر بجانب الشعار في الجوال.' ),
					array( 'general.name_en', 'text', 'الاسم بالإنجليزية' ),
					array( 'general.tagline', 'text', 'الشعار النصي (Tagline)' ),
					array( 'general.logo', 'image', 'الشعار', 'اتركه فارغًا لاستخدام الشعار المأخوذ من البروفايل. يُفضّل PNG بخلفية شفافة.' ),
					array( 'general.founded', 'text', 'سنة التأسيس (ميلادي)' ),
					array( 'general.founded_hijri', 'text', 'سنة التأسيس (هجري)' ),
				) ),
				array( 'title' => 'التواصل', 'fields' => array(
					array( 'general.phone', 'text', 'رقم الجوال (اتصال)', 'مثال: 0505203019' ),
					array( 'general.whatsapp', 'text', 'رقم واتساب بالصيغة الدولية', 'بدون + أو أصفار، مثال: 966505203019' ),
					array( 'general.whatsapp_msg', 'textarea', 'الرسالة الافتراضية في واتساب' ),
					array( 'general.landline', 'text', 'الهاتف والفاكس' ),
					array( 'general.email_primary', 'email', 'البريد الرسمي' ),
					array( 'general.email_secondary', 'email', 'بريد إضافي' ),
					array( 'general.hours', 'text', 'أوقات العمل' ),
					array( 'general.show_float', 'toggle', 'إظهار أزرار الاتصال وواتساب العائمة' ),
				) ),
				array( 'title' => 'العنوان والخريطة', 'fields' => array(
					array( 'general.address', 'text', 'العنوان الكامل' ),
					array( 'general.street', 'text', 'الشارع والحي', 'يُستخدم في البيانات المنظّمة (Schema).' ),
					array( 'general.city', 'text', 'المدينة' ),
					array( 'general.region', 'text', 'المنطقة' ),
					array( 'general.postal_code', 'text', 'الرمز البريدي' ),
					array( 'general.po_box', 'text', 'صندوق البريد' ),
					array( 'general.map_url', 'url', 'رابط الموقع على خرائط Google' ),
					array( 'general.map_embed_query', 'text', 'نص البحث للخريطة المضمّنة', 'اسم المكان أو الإحداثيات كما تُكتب في خرائط Google.' ),
					array( 'general.lat', 'text', 'خط العرض (Latitude)', 'من خرائط Google: اضغط مطولًا على الموقع وانسخ الرقم الأول. يحسّن الظهور في البحث المحلي.' ),
					array( 'general.lng', 'text', 'خط الطول (Longitude)' ),
				) ),
				array( 'title' => 'حسابات التواصل الاجتماعي', 'fields' => array(
					array( 'general.social_x', 'url', 'X (تويتر)' ),
					array( 'general.social_instagram', 'url', 'إنستغرام' ),
					array( 'general.social_snapchat', 'url', 'سناب شات' ),
					array( 'general.social_tiktok', 'url', 'تيك توك' ),
					array( 'general.social_linkedin', 'url', 'لينكدإن' ),
					array( 'general.social_youtube', 'url', 'يوتيوب' ),
				) ),
			),
		),

		'home' => array(
			'title' => 'الصفحة الرئيسية',
			'icon'  => 'dashicons-admin-home',
			'desc'  => 'نصوص الواجهة الرئيسية والأرقام ولماذا تختارنا والأسئلة الشائعة.',
			'groups'=> array(
				array( 'title' => 'الواجهة (Hero)', 'fields' => array(
					array( 'home.hero_badge', 'text', 'الشارة الصغيرة' ),
					array( 'home.hero_title', 'text', 'العنوان الرئيسي (H1)', 'يجب أن يحتوي على الكلمة المفتاحية الرئيسية.' ),
					array( 'home.hero_text', 'textarea', 'النص التعريفي' ),
					array( 'home.hero_video', 'video', 'فيديو الخلفية', 'MP4 خفيف (أقل من 3MB). اتركه فارغًا لاستخدام صورة فقط.' ),
					array( 'home.hero_poster', 'image', 'صورة الخلفية / غلاف الفيديو' ),
					array( 'home.btn_quote', 'text', 'نص زر عرض السعر' ),
					array( 'home.btn_whatsapp', 'text', 'نص زر واتساب' ),
				) ),
				array( 'title' => 'الأرقام والإحصائيات', 'fields' => array(
					array( 'home.stats', 'repeater', 'الأرقام', '', array(
						array( 'number', 'text', 'الرقم' ),
						array( 'suffix', 'text', 'لاحقة (+ أو %)' ),
						array( 'label', 'text', 'الوصف' ),
					) ),
				) ),
				array( 'title' => 'الأقسام', 'fields' => array(
					array( 'home.services_title', 'text', 'عنوان قسم الخدمات' ),
					array( 'home.services_text', 'text', 'وصف قسم الخدمات' ),
					array( 'home.why_title', 'text', 'عنوان "لماذا تختارنا"' ),
					array( 'home.why', 'repeater', 'نقاط "لماذا تختارنا"', '', array(
						array( 'title', 'text', 'العنوان' ),
						array( 'text', 'textarea', 'الوصف' ),
					) ),
					array( 'home.trust_title', 'text', 'عنوان قسم العملاء' ),
					array( 'home.trust_text', 'textarea', 'وصف قسم العملاء' ),
					array( 'home.projects_title', 'text', 'عنوان قسم سابقة الأعمال' ),
					array( 'home.projects_text', 'text', 'وصف قسم سابقة الأعمال' ),
					array( 'home.cta_title', 'text', 'عنوان شريط الدعوة للتواصل' ),
					array( 'home.cta_text', 'textarea', 'نص شريط الدعوة للتواصل' ),
				) ),
				array( 'title' => 'الأسئلة الشائعة', 'fields' => array(
					array( 'home.faq_title', 'text', 'عنوان القسم' ),
					array( 'home.faq', 'repeater', 'الأسئلة', 'تظهر في الرئيسية وتُضاف تلقائيًا كبيانات FAQ منظمة لجوجل.', array(
						array( 'q', 'text', 'السؤال' ),
						array( 'a', 'textarea', 'الإجابة' ),
					) ),
				) ),
			),
		),

		'about' => array(
			'title' => 'من نحن',
			'icon'  => 'dashicons-groups',
			'desc'  => 'نبذة المؤسسة والرسالة والرؤية والقيم والاعتمادات.',
			'groups'=> array(
				array( 'title' => 'المحتوى', 'fields' => array(
					array( 'about.title', 'text', 'عنوان الصفحة (H1)' ),
					array( 'about.intro', 'textarea', 'النبذة التعريفية' ),
					array( 'about.image', 'image', 'صورة النبذة' ),
					array( 'about.mission', 'textarea', 'رسالتنا' ),
					array( 'about.vision', 'textarea', 'رؤيتنا' ),
					array( 'about.values', 'repeater', 'قيمنا', '', array(
						array( 'title', 'text', 'القيمة' ),
						array( 'text', 'text', 'الوصف' ),
					) ),
					array( 'about.timeline', 'repeater', 'مسيرتنا', '', array(
						array( 'year', 'text', 'السنة / المرحلة' ),
						array( 'text', 'text', 'الوصف' ),
					) ),
				) ),
				array( 'title' => 'السلامة والاعتمادات', 'fields' => array(
					array( 'about.safety_title', 'text', 'العنوان' ),
					array( 'about.safety_text', 'textarea', 'النص', 'يُرجى تأكيد الاعتمادات الفعلية قبل النشر.' ),
					array( 'about.certs', 'repeater', 'الاعتمادات', '', array(
						array( 'name', 'text', 'الاسم' ),
						array( 'desc', 'text', 'الوصف' ),
						array( 'logo', 'image', 'الشعار' ),
					) ),
				) ),
			),
		),

		'services' => array(
			'title' => 'الخدمات',
			'icon'  => 'dashicons-hammer',
			'desc'  => 'أضف أو عدّل أو رتّب الخدمات. كل خدمة تظهر في صفحة الخدمات، والمعلَّم عليها "في الرئيسية" تظهر في الصفحة الرئيسية.',
			'groups'=> array(
				array( 'title' => 'مقدمة الصفحة', 'fields' => array(
					array( 'services.title', 'text', 'عنوان الصفحة (H1)' ),
					array( 'services.intro', 'textarea', 'المقدمة' ),
					array( 'services.cats_note', 'textarea', 'ملاحظة الفئات' ),
				) ),
				array( 'title' => 'قائمة الخدمات', 'fields' => array(
					array( 'services.items', 'repeater', 'الخدمات', 'اسحب الصفوف لإعادة الترتيب.', array(
						array( 'title', 'text', 'اسم الخدمة' ),
						array( 'cat', 'select', 'الفئة', '', rmt_service_cats() ),
						array( 'icon', 'select', 'الأيقونة', '', $icons ),
						array( 'short', 'textarea', 'وصف مختصر (للبطاقات)' ),
						array( 'text', 'textarea', 'الوصف الكامل' ),
						array( 'image', 'image', 'صورة الخدمة (اختياري)' ),
						array( 'home', 'toggle', 'تظهر في الرئيسية' ),
					) ),
				) ),
			),
		),

		'projects' => array(
			'title' => 'سابقة الأعمال',
			'icon'  => 'dashicons-format-video',
			'desc'  => 'معرض الصور والفيديوهات. يمكنك اختيار عدة ملفات مرة واحدة من زر "إضافة دفعة من المكتبة".',
			'groups'=> array(
				array( 'title' => 'المعرض', 'fields' => array(
					array( 'projects.title', 'text', 'عنوان القسم' ),
					array( 'projects.intro', 'textarea', 'الوصف' ),
					array( 'projects.items', 'repeater', 'الأعمال', 'الفيديو: ارفع MP4 وصورة غلاف. الصورة: ارفع الصورة في خانة "الملف".', array(
						array( 'type', 'select', 'النوع', '', array( 'image' => 'صورة', 'video' => 'فيديو' ) ),
						array( 'src', 'media', 'الملف (صورة أو فيديو)' ),
						array( 'poster', 'image', 'صورة الغلاف (للفيديو)' ),
						array( 'title', 'text', 'العنوان / النص البديل' ),
						array( 'place', 'text', 'المكان' ),
					), 'bulk' ),
				) ),
			),
		),

		'clients' => array(
			'title' => 'العملاء',
			'icon'  => 'dashicons-awards',
			'desc'  => 'ارفع شعارات العملاء. العميل بدون شعار يظهر كبطاقة باسمه بشكل أنيق حتى ترفع شعاره.',
			'groups'=> array(
				array( 'title' => 'صفحة العملاء', 'fields' => array(
					array( 'clients.title', 'text', 'عنوان الصفحة (H1)' ),
					array( 'clients.intro', 'textarea', 'المقدمة' ),
					array( 'clients.outside', 'text', 'خارج المملكة' ),
				) ),
				array( 'title' => 'قائمة العملاء', 'fields' => array(
					array( 'clients.items', 'repeater', 'العملاء', 'الأفضل: شعارات PNG شفافة بعرض 400px تقريبًا.', array(
						array( 'name', 'text', 'اسم العميل' ),
						array( 'group', 'select', 'التصنيف', '', rmt_client_groups() ),
						array( 'logo', 'image', 'الشعار' ),
						array( 'url', 'url', 'رابط موقع العميل (اختياري)' ),
					), 'bulk' ),
				) ),
			),
		),

		'contact' => array(
			'title' => 'التواصل والنموذج',
			'icon'  => 'dashicons-email-alt',
			'desc'  => 'نصوص صفحة التواصل والإيميلات التي تستقبل طلبات عرض السعر. كل الطلبات تُحفظ أيضًا في قائمة "طلبات العملاء".',
			'groups'=> array(
				array( 'title' => 'الصفحة', 'fields' => array(
					array( 'contact.title', 'text', 'عنوان الصفحة (H1)' ),
					array( 'contact.intro', 'textarea', 'المقدمة' ),
					array( 'contact.success', 'textarea', 'رسالة نجاح الإرسال' ),
					array( 'contact.recipients', 'text', 'الإيميلات المستلمة للطلبات', 'افصل بينها بفاصلة.' ),
				) ),
			),
		),

		'seo' => array(
			'title' => 'السيو (SEO)',
			'icon'  => 'dashicons-search',
			'desc'  => 'عنوان ووصف كل صفحة كما يظهر في جوجل، وأدوات التحقق والتحليلات. العنوان المثالي 50–60 حرفًا والوصف 120–160 حرفًا.',
			'groups'=> array(
				array( 'title' => 'الإعدادات', 'fields' => array(
					array( 'seo.enabled', 'toggle', 'تفعيل سيو القالب', 'أوقفه فقط إذا ثبّتّ إضافة Yoast أو Rank Math (يتوقف تلقائيًا عند تفعيلهما).' ),
					array( 'seo.noindex_site', 'toggle', 'إخفاء الموقع عن محركات البحث مؤقتًا', 'فعّله أثناء التجهيز فقط، وأوقفه عند الإطلاق.' ),
					array( 'seo.keywords', 'textarea', 'الكلمات المفتاحية المستهدفة' ),
					array( 'seo.og_image', 'image', 'صورة المشاركة الافتراضية (1200×630)' ),
					array( 'seo.price_range', 'text', 'نطاق الأسعار (Schema)' ),
				) ),
				array( 'title' => 'الصفحات', 'fields' => array(
					array( 'seo.home_title', 'seo_title', 'الرئيسية – العنوان' ),
					array( 'seo.home_desc', 'seo_desc', 'الرئيسية – الوصف' ),
					array( 'seo.about_title', 'seo_title', 'من نحن – العنوان' ),
					array( 'seo.about_desc', 'seo_desc', 'من نحن – الوصف' ),
					array( 'seo.services_title', 'seo_title', 'الخدمات – العنوان' ),
					array( 'seo.services_desc', 'seo_desc', 'الخدمات – الوصف' ),
					array( 'seo.clients_title', 'seo_title', 'العملاء – العنوان' ),
					array( 'seo.clients_desc', 'seo_desc', 'العملاء – الوصف' ),
					array( 'seo.contact_title', 'seo_title', 'اتصل بنا – العنوان' ),
					array( 'seo.contact_desc', 'seo_desc', 'اتصل بنا – الوصف' ),
				) ),
				array( 'title' => 'التحقق والتحليلات', 'fields' => array(
					array( 'seo.ga4', 'text', 'Google Analytics 4 (Measurement ID)', 'مثال: G-XXXXXXXXXX' ),
					array( 'seo.gtm', 'text', 'Google Tag Manager ID', 'مثال: GTM-XXXXXXX (استخدم أحدهما فقط).' ),
					array( 'seo.gsc', 'text', 'رمز التحقق من Google Search Console', 'قيمة content فقط من وسم meta.' ),
					array( 'seo.bing', 'text', 'رمز التحقق من Bing' ),
					array( 'seo.yandex', 'text', 'رمز التحقق من Yandex' ),
				) ),
			),
		),

		'footer' => array(
			'title' => 'الفوتر',
			'icon'  => 'dashicons-editor-insertmore',
			'desc'  => 'نص الفوتر وحقوق النشر.',
			'groups'=> array(
				array( 'title' => 'الفوتر', 'fields' => array(
					array( 'footer.about', 'textarea', 'نبذة قصيرة' ),
					array( 'footer.copyright', 'text', 'نص الحقوق' ),
					array( 'footer.dev_text', 'text', 'نص المطوّر' ),
					array( 'footer.dev_url', 'url', 'رابط المطوّر' ),
				) ),
			),
		),
	);
}

/**
 * Sanitize a single value by field type.
 *
 * @param string $type  Field type.
 * @param mixed  $value Raw value.
 * @param array  $opts  Select options.
 * @return mixed
 */
function rmt_sanitize_value( $type, $value, $opts = array() ) {
	$value = is_string( $value ) ? wp_unslash( $value ) : $value;
	switch ( $type ) {
		case 'textarea':
		case 'seo_desc':
			return sanitize_textarea_field( $value );
		case 'url':
		case 'image':
		case 'video':
		case 'media':
			return esc_url_raw( trim( (string) $value ) );
		case 'email':
			return sanitize_email( $value );
		case 'toggle':
			return ! empty( $value ) ? '1' : '0';
		case 'select':
			return array_key_exists( $value, (array) $opts ) ? $value : (string) key( (array) $opts );
		default:
			return sanitize_text_field( $value );
	}
}

/**
 * Sanitize the whole panel submission against the schema.
 *
 * @param array $input Posted data (section => key => value).
 * @return array
 */
function rmt_sanitize_options( $input ) {
	$input = is_array( $input ) ? $input : array();
	$out   = get_option( RMT_OPTION, array() );
	$out   = is_array( $out ) ? $out : array();

	foreach ( rmt_panel_schema() as $tab ) {
		foreach ( $tab['groups'] as $group ) {
			foreach ( $group['fields'] as $f ) {
				list( $section, $key ) = explode( '.', $f[0] );
				if ( ! isset( $input[ $section ] ) ) {
					continue; // Tab not submitted.
				}
				$type = $f[1];
				if ( 'repeater' === $type ) {
					$rows  = isset( $input[ $section ][ $key ] ) && is_array( $input[ $section ][ $key ] ) ? $input[ $section ][ $key ] : array();
					$clean = array();
					foreach ( $rows as $row ) {
						if ( ! is_array( $row ) ) {
							continue;
						}
						$r     = array();
						$empty = true;
						foreach ( $f[4] as $sub ) {
							$v = isset( $row[ $sub[0] ] ) ? $row[ $sub[0] ] : '';
							$r[ $sub[0] ] = rmt_sanitize_value( $sub[1], $v, isset( $sub[4] ) ? $sub[4] : array() );
							if ( '' !== $r[ $sub[0] ] && ! in_array( $sub[1], array( 'select', 'toggle' ), true ) ) {
								$empty = false;
							}
						}
						if ( ! $empty ) {
							$clean[] = $r;
						}
					}
					$out[ $section ][ $key ] = $clean;
				} else {
					$v = isset( $input[ $section ][ $key ] ) ? $input[ $section ][ $key ] : '';
					$out[ $section ][ $key ] = rmt_sanitize_value( $type, $v, isset( $f[4] ) ? $f[4] : array() );
				}
			}
		}
	}
	return $out;
}
