<?php
/**
 * "لوحة رفال" – lightweight control panel + interactive guide.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

/** Register the menu. */
function rmt_admin_menu() {
	add_menu_page(
		'لوحة تحكم رفال',
		'لوحة رفال',
		'edit_theme_options',
		'rmt-panel',
		'rmt_render_panel',
		'dashicons-admin-site-alt3',
		2
	);
	add_submenu_page( 'rmt-panel', 'إعدادات الموقع', 'إعدادات الموقع', 'edit_theme_options', 'rmt-panel', 'rmt_render_panel' );
	add_submenu_page( 'rmt-panel', 'طلبات عروض الأسعار', 'طلبات العملاء', 'edit_theme_options', 'edit.php?post_type=rmt_lead' );
	add_submenu_page( 'rmt-panel', 'دليل الاستخدام', 'دليل الاستخدام', 'edit_theme_options', 'rmt-guide', 'rmt_render_guide_page' );
}
add_action( 'admin_menu', 'rmt_admin_menu' );

/** Assets for our screens only (keeps the rest of wp-admin untouched & fast). */
function rmt_admin_assets( $hook ) {
	if ( false === strpos( $hook, 'rmt-' ) && 'index.php' !== $hook ) {
		return;
	}
	$v = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'rmt-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), $v );
	if ( false !== strpos( $hook, 'rmt-panel' ) ) {
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'rmt-admin', get_template_directory_uri() . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), $v, true );
		wp_localize_script(
			'rmt-admin',
			'RMT_ADMIN',
			array(
				'firstVisit' => get_user_meta( get_current_user_id(), 'rmt_tour_done', true ) ? 0 : 1,
				'ajax'       => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'rmt_tour' ),
				'site'       => home_url( '/' ),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'rmt_admin_assets' );

/** Mark tour as completed for the current user. */
function rmt_ajax_tour_done() {
	check_ajax_referer( 'rmt_tour', 'nonce' );
	update_user_meta( get_current_user_id(), 'rmt_tour_done', 1 );
	wp_send_json_success();
}
add_action( 'wp_ajax_rmt_tour_done', 'rmt_ajax_tour_done' );

/** Save handler. */
function rmt_save_panel() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( 'غير مسموح.' );
	}
	check_admin_referer( 'rmt_save_panel' );
	$tab = isset( $_POST['rmt_tab'] ) ? sanitize_key( wp_unslash( $_POST['rmt_tab'] ) ) : 'general';

	if ( isset( $_POST['rmt_reset'] ) ) {
		delete_option( RMT_OPTION );
		$msg = 'reset';
	} else {
		$input = isset( $_POST['rmt'] ) && is_array( $_POST['rmt'] ) ? $_POST['rmt'] : array(); // phpcs:ignore -- sanitized below.
		update_option( RMT_OPTION, rmt_sanitize_options( $input ), true );
		$msg = 'saved';
	}
	rmt_flush_page_cache();
	wp_safe_redirect( admin_url( 'admin.php?page=rmt-panel&msg=' . $msg . '#' . $tab ) );
	exit;
}
add_action( 'admin_post_rmt_save_panel', 'rmt_save_panel' );

/** Clear common page caches after saving. */
function rmt_flush_page_cache() {
	if ( function_exists( 'wp_cache_flush' ) ) {
		wp_cache_flush();
	}
	do_action( 'litespeed_purge_all' );
	if ( function_exists( 'rocket_clean_domain' ) ) {
		rocket_clean_domain();
	}
	if ( function_exists( 'w3tc_flush_all' ) ) {
		w3tc_flush_all();
	}
}

/** Read a value for the form using "section.key". */
function rmt_field_value( $path ) {
	list( $s, $k ) = explode( '.', $path );
	return rmt_get( $s, $k );
}

/**
 * Render a single input.
 *
 * @param string $type  Type.
 * @param string $name  Input name.
 * @param mixed  $value Current value.
 * @param array  $opts  Select options.
 * @param string $id    Input id.
 */
function rmt_render_input( $type, $name, $value, $opts = array(), $id = '' ) {
	$id_attr = $id ? ' id="' . esc_attr( $id ) . '"' : '';
	switch ( $type ) {
		case 'textarea':
			printf( '<textarea name="%s"%s rows="3">%s</textarea>', esc_attr( $name ), $id_attr, esc_textarea( $value ) );
			break;
		case 'seo_title':
		case 'seo_desc':
			$max = 'seo_title' === $type ? 60 : 160;
			echo '<div class="rmt-seo-field" data-max="' . (int) $max . '">';
			if ( 'seo_title' === $type ) {
				printf( '<input type="text" name="%s"%s value="%s">', esc_attr( $name ), $id_attr, esc_attr( $value ) );
			} else {
				printf( '<textarea name="%s"%s rows="2">%s</textarea>', esc_attr( $name ), $id_attr, esc_textarea( $value ) );
			}
			echo '<div class="rmt-counter"><span class="rmt-bar"><i></i></span><b>0</b> / ' . (int) $max . ' حرف</div></div>';
			break;
		case 'toggle':
			printf(
				'<label class="rmt-switch"><input type="hidden" name="%1$s" value="0"><input type="checkbox" name="%1$s" value="1"%2$s%3$s><span></span></label>',
				esc_attr( $name ),
				$id_attr,
				checked( '1', $value, false )
			);
			break;
		case 'select':
			printf( '<select name="%s"%s>', esc_attr( $name ), $id_attr );
			foreach ( (array) $opts as $k => $label ) {
				printf( '<option value="%s"%s>%s</option>', esc_attr( $k ), selected( $value, $k, false ), esc_html( $label ) );
			}
			echo '</select>';
			break;
		case 'image':
		case 'video':
		case 'media':
			$lib     = 'image' === $type ? 'image' : ( 'video' === $type ? 'video' : '' );
			$is_vid  = $value && preg_match( '/\.(mp4|webm|mov)(\?|$)/i', $value );
			echo '<div class="rmt-media" data-lib="' . esc_attr( $lib ) . '">';
			echo '<div class="rmt-media-preview">';
			if ( $value ) {
				if ( $is_vid ) {
					echo '<video src="' . esc_url( $value ) . '" muted preload="metadata"></video>';
				} else {
					echo '<img src="' . esc_url( $value ) . '" alt="">';
				}
			}
			echo '</div>';
			printf( '<input type="url" class="rmt-media-url" name="%s"%s value="%s" placeholder="رابط الملف">', esc_attr( $name ), $id_attr, esc_attr( $value ) );
			echo '<button type="button" class="button rmt-media-pick">اختيار / رفع</button> <button type="button" class="button-link rmt-media-clear">إزالة</button></div>';
			break;
		case 'url':
			printf( '<input type="url" name="%s"%s value="%s" dir="ltr">', esc_attr( $name ), $id_attr, esc_attr( $value ) );
			break;
		case 'email':
			printf( '<input type="email" name="%s"%s value="%s" dir="ltr">', esc_attr( $name ), $id_attr, esc_attr( $value ) );
			break;
		default:
			printf( '<input type="text" name="%s"%s value="%s">', esc_attr( $name ), $id_attr, esc_attr( $value ) );
	}
}

/**
 * Render one repeater row.
 *
 * @param string     $base Name base e.g. rmt[home][stats].
 * @param array      $subs Sub-field defs.
 * @param array      $row  Values.
 * @param string|int $i    Index or __i__ template token.
 */
function rmt_render_row( $base, $subs, $row, $i ) {
	$label = '';
	foreach ( array( 'title', 'name', 'q', 'label', 'year' ) as $k ) {
		if ( ! empty( $row[ $k ] ) ) {
			$label = $row[ $k ];
			break;
		}
	}
	echo '<div class="rmt-row">';
	echo '<div class="rmt-row-head"><span class="rmt-handle dashicons dashicons-menu" title="اسحب لإعادة الترتيب"></span><strong class="rmt-row-title">' . esc_html( $label ? $label : 'عنصر جديد' ) . '</strong>';
	echo '<span class="rmt-row-actions"><button type="button" class="button-link rmt-row-toggle">تعديل</button> <button type="button" class="button-link rmt-row-dup">نسخ</button> <button type="button" class="button-link rmt-row-del">حذف</button></span></div>';
	echo '<div class="rmt-row-body">';
	foreach ( $subs as $sub ) {
		$v = isset( $row[ $sub[0] ] ) ? $row[ $sub[0] ] : ( 'toggle' === $sub[1] ? '0' : '' );
		echo '<div class="rmt-field rmt-field-' . esc_attr( $sub[1] ) . '"><label>' . esc_html( $sub[2] ) . '</label>';
		rmt_render_input( $sub[1], $base . '[' . $i . '][' . $sub[0] . ']', $v, isset( $sub[4] ) ? $sub[4] : array() );
		echo '</div>';
	}
	echo '</div></div>';
}

/** Render the panel page. */
function rmt_render_panel() {
	$schema = rmt_panel_schema();
	$msg    = isset( $_GET['msg'] ) ? sanitize_key( $_GET['msg'] ) : ''; // phpcs:ignore
	$leads  = wp_count_posts( 'rmt_lead' );
	$newl   = isset( $leads->publish ) ? (int) $leads->publish : 0;
	?>
	<div class="wrap rmt-wrap" dir="rtl">
		<header class="rmt-top" data-tour="top">
			<img src="<?php echo esc_url( rmt_logo_url( 'small' ) ); ?>" alt="" width="54" height="54">
			<div>
				<h1>لوحة تحكم <?php echo esc_html( rmt_get( 'general', 'short_name' ) ); ?></h1>
				<p>كل محتوى الموقع من مكان واحد. عدّل ثم اضغط «حفظ التغييرات».</p>
			</div>
			<div class="rmt-top-actions">
				<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener" data-tour="view">عرض الموقع ↗</a>
				<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=rmt_lead' ) ); ?>">طلبات العملاء <?php echo $newl ? '<span class="rmt-pill">' . (int) $newl . '</span>' : ''; ?></a>
				<button type="button" class="button button-secondary rmt-start-tour" data-tour="help">▶ الدليل التفاعلي</button>
			</div>
		</header>

		<?php if ( 'saved' === $msg ) : ?>
			<div class="notice notice-success is-dismissible"><p>تم حفظ التغييرات بنجاح ✔</p></div>
		<?php elseif ( 'reset' === $msg ) : ?>
			<div class="notice notice-warning is-dismissible"><p>تمت استعادة المحتوى الافتراضي.</p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="rmt-form">
			<input type="hidden" name="action" value="rmt_save_panel">
			<input type="hidden" name="rmt_tab" value="general" class="rmt-current-tab">
			<?php wp_nonce_field( 'rmt_save_panel' ); ?>

			<div class="rmt-layout">
				<nav class="rmt-tabs" data-tour="tabs" aria-label="أقسام اللوحة">
					<?php foreach ( $schema as $id => $tab ) : ?>
						<a href="#<?php echo esc_attr( $id ); ?>" data-tab="<?php echo esc_attr( $id ); ?>" data-tour="tab-<?php echo esc_attr( $id ); ?>">
							<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span><?php echo esc_html( $tab['title'] ); ?>
						</a>
					<?php endforeach; ?>
					<input type="search" class="rmt-search" placeholder="ابحث عن حقل…" data-tour="search">
				</nav>

				<div class="rmt-panels">
					<?php foreach ( $schema as $id => $tab ) : ?>
						<section class="rmt-panel" id="panel-<?php echo esc_attr( $id ); ?>" data-panel="<?php echo esc_attr( $id ); ?>">
							<div class="rmt-panel-head">
								<h2><?php echo esc_html( $tab['title'] ); ?></h2>
								<p><?php echo esc_html( $tab['desc'] ); ?></p>
							</div>
							<?php foreach ( $tab['groups'] as $group ) : ?>
								<div class="rmt-card">
									<h3><?php echo esc_html( $group['title'] ); ?></h3>
									<?php
									foreach ( $group['fields'] as $f ) :
										list( $s, $k ) = explode( '.', $f[0] );
										$name          = 'rmt[' . $s . '][' . $k . ']';
										$help          = isset( $f[3] ) ? $f[3] : '';
										$fid           = 'rmt-' . $s . '-' . $k;
										if ( 'repeater' === $f[1] ) :
											$rows = (array) rmt_field_value( $f[0] );
											?>
											<div class="rmt-field rmt-repeater" data-base="<?php echo esc_attr( $name ); ?>" data-tour="rep-<?php echo esc_attr( $s . '-' . $k ); ?>">
												<label><?php echo esc_html( $f[2] ); ?> <span class="rmt-count">(<?php echo count( $rows ); ?>)</span></label>
												<?php if ( $help ) : ?><p class="description"><?php echo esc_html( $help ); ?></p><?php endif; ?>
												<input type="hidden" name="<?php echo esc_attr( $name ); ?>[__keep]" value="">
												<div class="rmt-rows">
													<?php
													$i = 0;
													foreach ( $rows as $row ) {
														rmt_render_row( $name, $f[4], $row, $i++ );
													}
													?>
												</div>
												<script type="text/template" class="rmt-row-tpl"><?php rmt_render_row( $name, $f[4], array(), '__i__' ); ?></script>
												<p class="rmt-rep-actions">
													<button type="button" class="button rmt-row-add">+ إضافة عنصر</button>
													<?php if ( ! empty( $f[5] ) ) : ?>
														<button type="button" class="button button-primary rmt-bulk-add" data-kind="<?php echo esc_attr( $s ); ?>">⇪ إضافة دفعة من المكتبة</button>
													<?php endif; ?>
												</p>
											</div>
										<?php else : ?>
											<div class="rmt-field rmt-field-<?php echo esc_attr( $f[1] ); ?>">
												<label for="<?php echo esc_attr( $fid ); ?>"><?php echo esc_html( $f[2] ); ?></label>
												<?php rmt_render_input( $f[1], $name, rmt_field_value( $f[0] ), isset( $f[4] ) ? $f[4] : array(), $fid ); ?>
												<?php if ( $help ) : ?><p class="description"><?php echo esc_html( $help ); ?></p><?php endif; ?>
											</div>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							<?php endforeach; ?>

							<?php if ( 'seo' === $id ) : ?>
								<div class="rmt-card rmt-seo-check" data-tour="seo-check">
									<h3>فحص السيو السريع</h3>
									<?php rmt_render_seo_checklist(); ?>
								</div>
							<?php endif; ?>
						</section>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="rmt-savebar" data-tour="save">
				<span class="rmt-dirty">لديك تغييرات غير محفوظة</span>
				<button type="submit" class="button button-primary button-hero">حفظ التغييرات</button>
				<button type="submit" name="rmt_reset" value="1" class="button-link rmt-reset" onclick="return confirm('سيتم حذف كل التعديلات واستعادة المحتوى الافتراضي. متأكد؟');">استعادة الافتراضي</button>
			</div>
		</form>
	</div>
	<?php
}

/** SEO health checklist shown in the SEO tab. */
function rmt_render_seo_checklist() {
	$checks = array(
		array( '1' !== rmt_get( 'seo', 'noindex_site' ) && '1' === get_option( 'blog_public' ), 'الموقع مسموح بأرشفته في محركات البحث', 'من الإعدادات ← القراءة: أزل علامة "منع محركات البحث" وأوقف خيار الإخفاء المؤقت.' ),
		array( false !== strpos( (string) get_option( 'permalink_structure' ), '%postname%' ), 'الروابط الدائمة بصيغة اسم الصفحة', 'الإعدادات ← الروابط الدائمة ← اسم المقالة.' ),
		array( is_ssl() || 0 === strpos( home_url(), 'https' ), 'الموقع يعمل على HTTPS', 'فعّل شهادة SSL من الاستضافة.' ),
		array( (bool) rmt_get( 'seo', 'gsc' ), 'تم ربط Google Search Console', 'أضف رمز التحقق ثم أرسل خريطة الموقع: ' . home_url( '/wp-sitemap.xml' ) ),
		array( (bool) ( rmt_get( 'seo', 'ga4' ) || rmt_get( 'seo', 'gtm' ) ), 'تم ربط Google Analytics', 'أضف Measurement ID.' ),
		array( (bool) ( rmt_get( 'general', 'lat' ) && rmt_get( 'general', 'lng' ) ), 'إحداثيات الموقع مضافة (للبحث المحلي)', 'أضف خط العرض والطول من تبويب البيانات العامة.' ),
		array( (bool) rmt_get( 'seo', 'og_image' ), 'صورة المشاركة الاجتماعية محددة', 'أضف صورة 1200×630.' ),
	);
	$clients_logos = 0;
	foreach ( (array) rmt_get( 'clients', 'items' ) as $c ) {
		$clients_logos += empty( $c['logo'] ) ? 0 : 1;
	}
	$checks[] = array( $clients_logos > 0, 'شعارات العملاء مرفوعة (' . $clients_logos . ')', 'ارفع الشعارات من تبويب العملاء.' );
	echo '<ul class="rmt-checklist">';
	foreach ( $checks as $c ) {
		printf( '<li class="%s"><span class="dashicons %s"></span><div><strong>%s</strong>%s</div></li>', $c[0] ? 'ok' : 'todo', $c[0] ? 'dashicons-yes-alt' : 'dashicons-warning', esc_html( $c[1] ), $c[0] ? '' : '<small>' . esc_html( $c[2] ) . '</small>' );
	}
	echo '</ul><p class="description">خريطة الموقع: <a href="' . esc_url( home_url( '/wp-sitemap.xml' ) ) . '" target="_blank" dir="ltr">' . esc_html( home_url( '/wp-sitemap.xml' ) ) . '</a> — ملف robots: <a href="' . esc_url( home_url( '/robots.txt' ) ) . '" target="_blank" dir="ltr">robots.txt</a></p>';
}

/** Static guide page (also reachable from the panel). */
function rmt_render_guide_page() {
	$steps = array(
		array( 'dashicons-building', 'البيانات العامة', 'غيّر رقم الجوال أو الإيميل أو العنوان مرة واحدة فيتحدث في كل الموقع تلقائيًا (الهيدر، الفوتر، زر واتساب، صفحة التواصل، وبيانات جوجل).' ),
		array( 'dashicons-admin-home', 'الصفحة الرئيسية', 'عدّل العنوان الرئيسي والنص وفيديو الخلفية والأرقام ونقاط "لماذا تختارنا" والأسئلة الشائعة.' ),
		array( 'dashicons-hammer', 'الخدمات', 'أضف خدمة بزر "إضافة عنصر"، واسحب الصفوف لإعادة ترتيبها. فعّل "تظهر في الرئيسية" لعرضها في الصفحة الرئيسية.' ),
		array( 'dashicons-format-video', 'سابقة الأعمال', 'اضغط "إضافة دفعة من المكتبة" واختر عدة صور وفيديوهات مرة واحدة. للفيديو أضف صورة غلاف لسرعة التحميل.' ),
		array( 'dashicons-awards', 'العملاء', 'ارفع شعار كل عميل (PNG شفاف). اختر عدة شعارات مرة واحدة من زر "إضافة دفعة" ثم اكتب اسم كل عميل.' ),
		array( 'dashicons-email-alt', 'الطلبات', 'كل طلب عرض سعر يُرسل على الإيميلات المحددة ويُحفظ في "طلبات العملاء" حتى لو لم يصل الإيميل.' ),
		array( 'dashicons-search', 'السيو', 'لكل صفحة عنوان ووصف مع عدّاد للأحرف. راجع "فحص السيو السريع" وأكمل البنود غير المكتملة.' ),
		array( 'dashicons-saved', 'الحفظ', 'زر "حفظ التغييرات" ثابت أسفل الشاشة. بعد الحفظ اضغط "عرض الموقع" للتأكد.' ),
	);
	?>
	<div class="wrap rmt-wrap rmt-guide" dir="rtl">
		<header class="rmt-top">
			<img src="<?php echo esc_url( rmt_logo_url( 'small' ) ); ?>" alt="" width="54" height="54">
			<div><h1>دليل استخدام لوحة التحكم</h1><p>8 خطوات بسيطة لإدارة الموقع بالكامل.</p></div>
			<div class="rmt-top-actions"><a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=rmt-panel&tour=1' ) ); ?>">▶ ابدأ الجولة التفاعلية</a></div>
		</header>
		<ol class="rmt-guide-steps">
			<?php foreach ( $steps as $n => $s ) : ?>
				<li><span class="rmt-num"><?php echo (int) $n + 1; ?></span><span class="dashicons <?php echo esc_attr( $s[0] ); ?>"></span><h3><?php echo esc_html( $s[1] ); ?></h3><p><?php echo esc_html( $s[2] ); ?></p></li>
			<?php endforeach; ?>
		</ol>
		<div class="rmt-card"><h3>نصائح مهمة</h3>
			<ul class="ul-disc">
				<li>الصور: ارفعها بعرض لا يزيد عن 1600px، ووردبرس يولّد أحجامًا أصغر تلقائيًا.</li>
				<li>الفيديو: MP4 بحجم أقل من 5MB لكل مقطع لسرعة الموقع.</li>
				<li>اكتب "العنوان / النص البديل" لكل صورة، فهو مهم للسيو.</li>
				<li>لا تغيّر روابط الصفحات (about, services, clients, contact) حتى لا تتأثر نتائج البحث.</li>
			</ul>
		</div>
	</div>
	<?php
}

/** Dashboard widget with quick links. */
function rmt_dashboard_widget() {
	wp_add_dashboard_widget(
		'rmt_quick',
		'لوحة رفال – وصول سريع',
		function () {
			$links = array(
				'general'  => 'البيانات العامة',
				'home'     => 'الرئيسية',
				'services' => 'الخدمات',
				'projects' => 'سابقة الأعمال',
				'clients'  => 'العملاء',
				'seo'      => 'السيو',
			);
			echo '<div class="rmt-quick">';
			foreach ( $links as $k => $l ) {
				echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=rmt-panel#' . $k ) ) . '">' . esc_html( $l ) . '</a>';
			}
			echo '</div><p><a href="' . esc_url( admin_url( 'admin.php?page=rmt-guide' ) ) . '">دليل الاستخدام ←</a></p>';
		}
	);
}
add_action( 'wp_dashboard_setup', 'rmt_dashboard_widget' );

/** Admin bar shortcut. */
function rmt_admin_bar( $bar ) {
	if ( current_user_can( 'edit_theme_options' ) ) {
		$bar->add_node( array( 'id' => 'rmt-panel', 'title' => '⚙ لوحة رفال', 'href' => admin_url( 'admin.php?page=rmt-panel' ) ) );
	}
}
add_action( 'admin_bar_menu', 'rmt_admin_bar', 80 );
