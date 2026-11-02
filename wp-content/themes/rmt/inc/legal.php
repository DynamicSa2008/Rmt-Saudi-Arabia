<?php
/**
 * Legal pages (Privacy, Terms, Cookies) in Arabic and English.
 * Created once on activation as normal WordPress pages, so the client can
 * edit them later from Pages. Content is filled with the company's details.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

/** slug => array( ar title, en title ). */
function rmt_legal_pages() {
	return array(
		'privacy' => array( 'سياسة الخصوصية', 'Privacy Policy' ),
		'terms'   => array( 'الشروط والأحكام', 'Terms & Conditions' ),
		'cookies' => array( 'سياسة ملفات تعريف الارتباط', 'Cookie Policy' ),
	);
}

/** Is the current request one of the legal pages? Returns slug or ''. */
function rmt_legal_key() {
	if ( ! is_page() ) {
		return '';
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	return array_key_exists( $slug, rmt_legal_pages() ) ? $slug : '';
}

/**
 * Build legal page HTML.
 *
 * @param string $slug privacy|terms|cookies.
 * @param string $lang ar|en.
 * @return string
 */
function rmt_legal_content( $slug, $lang ) {
	$o     = rmt_raw_options();
	$g     = $o['general'];
	$en    = 'en' === $lang;
	$name  = $en ? ( ! empty( $g['company_name_en'] ) ? $g['company_name_en'] : $g['name_en'] ) : $g['company_name'];
	$addr  = $en ? ( ! empty( $g['address_en'] ) ? $g['address_en'] : $g['address'] ) : $g['address'];
	$mail  = $g['email_primary'];
	$phone = rmt_tel();
	$site  = wp_parse_url( home_url(), PHP_URL_HOST );
	$e     = function ( $s ) {
		return esc_html( $s );
	};

	if ( 'privacy' === $slug && ! $en ) {
		return '<p>توضح هذه السياسة كيف تجمع ' . $e( $name ) . ' («المؤسسة» أو «نحن») البيانات الشخصية وتستخدمها وتحميها عند زيارتك للموقع ' . $e( $site ) . ' أو تواصلك معنا، وذلك وفقًا لـ<strong>نظام حماية البيانات الشخصية</strong> في المملكة العربية السعودية ولوائحه التنفيذية.</p>
<h2>1. البيانات التي نجمعها</h2>
<ul>
<li><strong>بيانات تقدّمها أنت</strong> عبر نموذج طلب عرض السعر: الاسم، اسم الجهة، رقم الجوال، البريد الإلكتروني، المدينة، نوع الخدمة، نوع المبنى وعدد الأدوار، وأي تفاصيل تكتبها.</li>
<li><strong>بيانات التواصل المباشر</strong> عند مراسلتنا عبر واتساب أو الهاتف أو البريد الإلكتروني.</li>
<li><strong>بيانات تقنية</strong> تُجمع تلقائيًا مثل نوع الجهاز والمتصفح والصفحات التي تزورها، وذلك فقط عند تفعيل أدوات التحليلات (راجع سياسة ملفات تعريف الارتباط).</li>
</ul>
<h2>2. أغراض استخدام البيانات</h2>
<ul>
<li>الرد على استفساراتك وترتيب المعاينة وإعداد عروض الأسعار.</li>
<li>تنفيذ الخدمات والعقود المتفق عليها والتواصل بخصوصها.</li>
<li>تحسين الموقع وخدماتنا من خلال إحصاءات استخدام مجمّعة.</li>
<li>الالتزام بالأنظمة والمتطلبات النظامية في المملكة.</li>
</ul>
<p>لا نستخدم بياناتك لأغراض تسويقية دون موافقتك، ولا نبيعها لأي طرف.</p>
<h2>3. الأساس النظامي للمعالجة</h2>
<p>نعالج بياناتك بناءً على <strong>موافقتك</strong> عند إرسال النموذج، أو لأن المعالجة <strong>ضرورية لتنفيذ طلبك أو عقد</strong> معك، أو للوفاء بـ<strong>التزام نظامي</strong>. ويمكنك سحب موافقتك في أي وقت.</p>
<h2>4. مشاركة البيانات</h2>
<p>لا نشارك بياناتك إلا مع: مزودي الخدمات التقنية الذين يعملون نيابة عنا (مثل الاستضافة والبريد الإلكتروني) وبالقدر اللازم فقط وبموجب التزامات بالسرية، أو الجهات الحكومية المختصة عند وجود طلب نظامي.</p>
<h2>5. مدة الاحتفاظ</h2>
<p>نحتفظ ببيانات طلبات عروض الأسعار مدة لا تزيد عن <strong>سنتين</strong> من آخر تواصل، ما لم تتحول إلى عقد فنحتفظ بها طوال مدة العقد والمدة التي تتطلبها الأنظمة، ثم نحذفها أو نجعلها مجهولة الهوية.</p>
<h2>6. حماية البيانات</h2>
<p>نطبق إجراءات تقنية وتنظيمية مناسبة لحماية بياناتك من الوصول أو الاستخدام غير المصرح به، منها تشفير الاتصال بالموقع (HTTPS) وتقييد الوصول إلى البيانات على المختصين فقط.</p>
<h2>7. حقوقك</h2>
<p>يحق لك وفق نظام حماية البيانات الشخصية:</p>
<ul>
<li>العلم بكيفية جمع بياناتك ومعالجتها والغرض من ذلك.</li>
<li>الوصول إلى بياناتك والحصول على نسخة منها.</li>
<li>طلب تصحيح بياناتك أو استكمالها أو تحديثها.</li>
<li>طلب إتلاف بياناتك عند انتفاء الحاجة إليها.</li>
<li>سحب موافقتك على المعالجة في أي وقت.</li>
</ul>
<p>لممارسة أي من هذه الحقوق راسلنا على <a href="mailto:' . esc_attr( $mail ) . '">' . $e( $mail ) . '</a>، وسنرد خلال المدة النظامية. ويحق لك تقديم شكوى إلى الهيئة السعودية للبيانات والذكاء الاصطناعي (سدايا).</p>
<h2>8. روابط خارجية</h2>
<p>قد يحتوي الموقع على روابط لمواقع أخرى (مثل واتساب وخرائط Google). لا نتحمل مسؤولية سياسات الخصوصية لتلك المواقع.</p>
<h2>9. تحديث السياسة</h2>
<p>قد نحدّث هذه السياسة من وقت لآخر، ويظهر تاريخ آخر تحديث أعلى الصفحة.</p>
<h2>10. التواصل معنا</h2>
<p>' . $e( $name ) . '<br>' . $e( $addr ) . '<br>البريد الإلكتروني: <a href="mailto:' . esc_attr( $mail ) . '">' . $e( $mail ) . '</a><br>الجوال: <span dir="ltr">' . $e( $phone ) . '</span></p>';
	}

	if ( 'privacy' === $slug ) {
		return '<p>This policy explains how ' . $e( $name ) . ' (“the Establishment”, “we”) collects, uses and protects personal data when you visit ' . $e( $site ) . ' or contact us, in line with the Saudi <strong>Personal Data Protection Law (PDPL)</strong> and its implementing regulations.</p>
<h2>1. Data we collect</h2>
<ul>
<li><strong>Data you provide</strong> through the quotation form: name, organisation, mobile number, email, city, service type, building type and number of floors, and any details you write.</li>
<li><strong>Direct communication data</strong> when you contact us by WhatsApp, phone or email.</li>
<li><strong>Technical data</strong> collected automatically, such as device, browser and pages visited — only when analytics tools are enabled (see our Cookie Policy).</li>
</ul>
<h2>2. How we use your data</h2>
<ul>
<li>To answer your enquiries, arrange site inspections and prepare quotations.</li>
<li>To deliver agreed services and contracts and communicate about them.</li>
<li>To improve the website and our services using aggregated usage statistics.</li>
<li>To comply with applicable laws and regulatory requirements in the Kingdom.</li>
</ul>
<p>We do not use your data for marketing without your consent, and we never sell it.</p>
<h2>3. Legal basis</h2>
<p>We process your data based on your <strong>consent</strong> when you submit the form, because processing is <strong>necessary to fulfil your request or a contract</strong> with you, or to meet a <strong>legal obligation</strong>. You may withdraw your consent at any time.</p>
<h2>4. Sharing</h2>
<p>We only share data with technical service providers acting on our behalf (such as hosting and email), to the extent necessary and under confidentiality obligations, or with competent government authorities when legally required.</p>
<h2>5. Retention</h2>
<p>We keep quotation request data for no longer than <strong>two years</strong> from the last contact, unless it becomes a contract, in which case we keep it for the contract term and any period required by law, then delete or anonymise it.</p>
<h2>6. Security</h2>
<p>We apply appropriate technical and organisational measures to protect your data against unauthorised access or use, including encrypted connections (HTTPS) and access limited to authorised staff.</p>
<h2>7. Your rights</h2>
<p>Under the PDPL you have the right to:</p>
<ul>
<li>Be informed about how and why your data is processed.</li>
<li>Access your data and obtain a copy.</li>
<li>Request correction, completion or updating of your data.</li>
<li>Request destruction of your data when it is no longer needed.</li>
<li>Withdraw your consent at any time.</li>
</ul>
<p>To exercise any of these rights, email <a href="mailto:' . esc_attr( $mail ) . '">' . $e( $mail ) . '</a> and we will respond within the statutory period. You may also lodge a complaint with the Saudi Data & AI Authority (SDAIA).</p>
<h2>8. External links</h2>
<p>The website may link to other services (such as WhatsApp and Google Maps). We are not responsible for their privacy practices.</p>
<h2>9. Changes</h2>
<p>We may update this policy from time to time. The date of the last update is shown at the top of this page.</p>
<h2>10. Contact</h2>
<p>' . $e( $name ) . '<br>' . $e( $addr ) . '<br>Email: <a href="mailto:' . esc_attr( $mail ) . '">' . $e( $mail ) . '</a><br>Mobile: <span dir="ltr">' . $e( $phone ) . '</span></p>';
	}

	if ( 'terms' === $slug && ! $en ) {
		return '<p>باستخدامك لموقع ' . $e( $site ) . ' فإنك توافق على الشروط والأحكام التالية. إذا كنت لا توافق عليها، يُرجى عدم استخدام الموقع.</p>
<h2>1. التعريف</h2>
<p>الموقع مملوك ومُدار من قِبل ' . $e( $name ) . '، ومقرها: ' . $e( $addr ) . '.</p>
<h2>2. طبيعة المحتوى</h2>
<p>المحتوى المنشور في الموقع لأغراض تعريفية بخدمات المؤسسة. نسعى لأن تكون المعلومات دقيقة ومحدّثة، لكن تفاصيل الخدمات ونطاقها وأسعارها تُحدد في العرض أو العقد المكتوب لكل مشروع.</p>
<h2>3. طلبات عروض الأسعار</h2>
<ul>
<li>إرسال نموذج طلب عرض السعر لا يُعد تعاقدًا ولا يُلزم أي طرف.</li>
<li>العروض المقدمة مبدئية وتخضع للمعاينة الميدانية، ولا تصبح ملزمة إلا بعقد أو أمر عمل مكتوب وموقع من الطرفين.</li>
<li>تلتزم بتقديم بيانات صحيحة عند التواصل معنا.</li>
</ul>
<h2>4. الملكية الفكرية</h2>
<p>جميع محتويات الموقع من نصوص وصور ومقاطع فيديو وشعارات وتصاميم مملوكة للمؤسسة أو مرخصة لها، ولا يجوز نسخها أو إعادة نشرها أو استخدامها تجاريًا دون إذن كتابي مسبق. الشعارات والعلامات الخاصة بالعملاء والجهات الأخرى ملك لأصحابها.</p>
<h2>5. الاستخدام المقبول</h2>
<p>يُمنع استخدام الموقع بأي شكل يخالف الأنظمة، أو محاولة الإضرار به أو تعطيله، أو إرسال محتوى مضلل أو رسائل غير مرغوب فيها عبر نماذج التواصل.</p>
<h2>6. الروابط الخارجية</h2>
<p>قد يتضمن الموقع روابط لمواقع أو خدمات تابعة لأطراف أخرى، ولا نتحمل مسؤولية محتواها أو خدماتها.</p>
<h2>7. حدود المسؤولية</h2>
<p>يُقدم الموقع «كما هو»، ولا تتحمل المؤسسة مسؤولية أي أضرار غير مباشرة ناتجة عن استخدامه أو تعذر الوصول إليه. أما مسؤوليات تنفيذ الخدمات فتحكمها العقود المبرمة لكل مشروع.</p>
<h2>8. الخصوصية</h2>
<p>تخضع معالجة بياناتك الشخصية لـ<a href="' . esc_url( rmt_page_url( 'privacy', 'ar' ) ) . '">سياسة الخصوصية</a>.</p>
<h2>9. النظام الواجب التطبيق</h2>
<p>تخضع هذه الشروط لأنظمة المملكة العربية السعودية، وتختص المحاكم المختصة في مدينة الرياض بالنظر في أي نزاع ينشأ عنها.</p>
<h2>10. التعديلات</h2>
<p>يحق للمؤسسة تعديل هذه الشروط في أي وقت، ويسري التعديل من تاريخ نشره على هذه الصفحة.</p>
<h2>11. التواصل</h2>
<p>لأي استفسار: <a href="mailto:' . esc_attr( $mail ) . '">' . $e( $mail ) . '</a> — <span dir="ltr">' . $e( $phone ) . '</span></p>';
	}

	if ( 'terms' === $slug ) {
		return '<p>By using ' . $e( $site ) . ' you agree to the following terms and conditions. If you do not agree, please do not use the website.</p>
<h2>1. Who we are</h2>
<p>This website is owned and operated by ' . $e( $name ) . ', located at ' . $e( $addr ) . '.</p>
<h2>2. Website content</h2>
<p>Content on this website is provided to introduce our services. We aim to keep it accurate and up to date; however, the scope, details and prices of services are defined in the written quotation or contract for each project.</p>
<h2>3. Quotation requests</h2>
<ul>
<li>Submitting a quotation request is not a contract and does not bind either party.</li>
<li>Quotations are preliminary and subject to site inspection; they become binding only through a written contract or work order signed by both parties.</li>
<li>You agree to provide accurate information when contacting us.</li>
</ul>
<h2>4. Intellectual property</h2>
<p>All website content — text, photos, videos, logos and designs — is owned by or licensed to the Establishment and may not be copied, republished or used commercially without prior written permission. Client and third-party logos and trademarks belong to their respective owners.</p>
<h2>5. Acceptable use</h2>
<p>You may not use the website in any unlawful way, attempt to harm or disrupt it, or send misleading content or spam through its forms.</p>
<h2>6. External links</h2>
<p>The website may link to third-party websites or services. We are not responsible for their content or services.</p>
<h2>7. Limitation of liability</h2>
<p>The website is provided “as is”. The Establishment is not liable for indirect damages arising from its use or unavailability. Responsibilities for delivering services are governed by the contract for each project.</p>
<h2>8. Privacy</h2>
<p>Processing of your personal data is governed by our <a href="' . esc_url( rmt_page_url( 'privacy', 'en' ) ) . '">Privacy Policy</a>.</p>
<h2>9. Governing law</h2>
<p>These terms are governed by the laws of the Kingdom of Saudi Arabia, and the competent courts in Riyadh have jurisdiction over any dispute arising from them.</p>
<h2>10. Changes</h2>
<p>We may amend these terms at any time; changes take effect from the date they are published on this page.</p>
<h2>11. Contact</h2>
<p>For any questions: <a href="mailto:' . esc_attr( $mail ) . '">' . $e( $mail ) . '</a> — <span dir="ltr">' . $e( $phone ) . '</span></p>';
	}

	if ( 'cookies' === $slug && ! $en ) {
		return '<p>ملفات تعريف الارتباط (الكوكيز) ملفات نصية صغيرة يحفظها المتصفح على جهازك عند زيارة المواقع. توضح هذه الصفحة كيف يستخدمها موقع ' . $e( $site ) . '.</p>
<h2>1. أنواع الملفات التي نستخدمها</h2>
<ul>
<li><strong>ملفات ضرورية:</strong> لازمة لعمل الموقع وحمايته، مثل حماية نموذج التواصل من الرسائل المزعجة وتذكّر تفضيلات العرض. لا يمكن تعطيلها لأن الموقع لا يعمل بشكل صحيح دونها.</li>
<li><strong>ملفات التحليلات:</strong> عند تفعيل Google Analytics نستخدمها لفهم عدد الزوار والصفحات الأكثر زيارة بشكل مجمّع، بهدف تحسين الموقع.</li>
</ul>
<p>لا نستخدم ملفات إعلانية أو ملفات لتتبعك عبر مواقع أخرى.</p>
<h2>2. خدمات طرف ثالث</h2>
<p>عند فتح الخريطة التفاعلية (خرائط Google) أو الضغط على روابط واتساب، قد تضع تلك الخدمات ملفات خاصة بها وفق سياساتها.</p>
<h2>3. التحكم في الملفات</h2>
<p>يمكنك حذف ملفات تعريف الارتباط أو منعها من إعدادات متصفحك في أي وقت. قد يؤثر منع بعضها على عمل أجزاء من الموقع.</p>
<h2>4. المزيد من المعلومات</h2>
<p>لمعرفة كيفية تعاملنا مع بياناتك راجع <a href="' . esc_url( rmt_page_url( 'privacy', 'ar' ) ) . '">سياسة الخصوصية</a>، أو راسلنا على <a href="mailto:' . esc_attr( $mail ) . '">' . $e( $mail ) . '</a>.</p>';
	}

	return '<p>Cookies are small text files that your browser stores on your device when you visit websites. This page explains how ' . $e( $site ) . ' uses them.</p>
<h2>1. Types of cookies we use</h2>
<ul>
<li><strong>Essential cookies:</strong> needed for the website to work and stay secure, such as protecting the contact form from spam and remembering display preferences. They cannot be switched off because the site would not work properly without them.</li>
<li><strong>Analytics cookies:</strong> when Google Analytics is enabled, we use them to understand visitor numbers and popular pages in aggregate, to improve the website.</li>
</ul>
<p>We do not use advertising cookies or cookies that track you across other websites.</p>
<h2>2. Third-party services</h2>
<p>When you open the interactive map (Google Maps) or follow WhatsApp links, those services may set their own cookies under their own policies.</p>
<h2>3. Managing cookies</h2>
<p>You can delete or block cookies in your browser settings at any time. Blocking some cookies may affect parts of the website.</p>
<h2>4. More information</h2>
<p>To learn how we handle your data, see our <a href="' . esc_url( rmt_page_url( 'privacy', 'en' ) ) . '">Privacy Policy</a>, or email <a href="mailto:' . esc_attr( $mail ) . '">' . $e( $mail ) . '</a>.</p>';
}

/** Create the legal pages (AR at root, EN under /en/). Safe to re-run. */
function rmt_install_legal() {
	$en = get_page_by_path( 'en' );
	foreach ( rmt_legal_pages() as $slug => $titles ) {
		if ( ! get_page_by_path( $slug ) ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $titles[0],
					'post_content' => rmt_legal_content( $slug, 'ar' ),
				)
			);
			if ( 'privacy' === $slug && $id && ! is_wp_error( $id ) ) {
				update_option( 'wp_page_for_privacy_policy', $id );
			}
		}
		if ( $en && ! get_page_by_path( 'en/' . $slug ) ) {
			wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $titles[1],
					'post_parent'  => $en->ID,
					'post_content' => rmt_legal_content( $slug, 'en' ),
				)
			);
		}
	}
}

/** Footer legal links. */
function rmt_legal_links() {
	echo '<ul class="ft-legal">';
	foreach ( rmt_legal_pages() as $slug => $titles ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( rmt_page_url( $slug ) ), esc_html( rmt_is_en() ? $titles[1] : $titles[0] ) );
	}
	echo '</ul>';
}
