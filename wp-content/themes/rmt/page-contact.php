<?php
/**
 * Contact page (/contact) – quote form + details + map.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
get_header();
$ct   = rmt_get( 'contact' );
$g    = rmt_get( 'general' );
$sent = isset( $_GET['sent'] ); // phpcs:ignore
$err  = isset( $_GET['err'] ) ? sanitize_key( $_GET['err'] ) : ''; // phpcs:ignore
$pre  = isset( $_GET['service'] ) ? sanitize_text_field( wp_unslash( $_GET['service'] ) ) : ''; // phpcs:ignore
rmt_page_hero( 'contact', $ct['title'], $ct['intro'] );

$map_q = $g['lat'] && $g['lng'] ? $g['lat'] . ',' . $g['lng'] : $g['map_embed_query'];
$embed = 'https://maps.google.com/maps?q=' . rawurlencode( $map_q ) . '&hl=ar&z=15&output=embed';
?>

<section class="section">
	<div class="container contact-grid">
		<aside class="contact-info reveal">
			<h2>بيانات التواصل</h2>
			<ul class="ci-list">
				<li><span class="ci-ico"><?php echo rmt_icon( 'pin', 22 ); // phpcs:ignore ?></span><div><small>العنوان</small><strong><?php echo esc_html( $g['address'] ); ?></strong><span><?php echo esc_html( $g['po_box'] ); ?></span></div></li>
				<li><span class="ci-ico"><?php echo rmt_icon( 'phone', 22 ); // phpcs:ignore ?></span><div><small>الجوال (اتصال)</small><a href="tel:<?php echo esc_attr( rmt_tel() ); ?>" dir="ltr" data-track="call"><?php echo esc_html( rmt_tel() ); ?></a></div></li>
				<li><span class="ci-ico wa"><?php echo rmt_icon( 'whatsapp', 22 ); // phpcs:ignore ?></span><div><small>واتساب</small><a href="<?php echo esc_url( rmt_whatsapp_url() ); ?>" target="_blank" rel="noopener" dir="ltr" data-track="whatsapp"><?php echo esc_html( rmt_tel() ); ?></a></div></li>
				<?php if ( $g['landline'] ) : ?>
					<li><span class="ci-ico"><?php echo rmt_icon( 'fax', 22 ); // phpcs:ignore ?></span><div><small>الهاتف والفاكس</small><span dir="ltr"><?php echo esc_html( $g['landline'] ); ?></span></div></li>
				<?php endif; ?>
				<li><span class="ci-ico"><?php echo rmt_icon( 'mail', 22 ); // phpcs:ignore ?></span><div><small>البريد الإلكتروني</small><a href="mailto:<?php echo esc_attr( $g['email_primary'] ); ?>" dir="ltr"><?php echo esc_html( $g['email_primary'] ); ?></a><?php if ( $g['email_secondary'] ) : ?><a href="mailto:<?php echo esc_attr( $g['email_secondary'] ); ?>" dir="ltr"><?php echo esc_html( $g['email_secondary'] ); ?></a><?php endif; ?></div></li>
				<li><span class="ci-ico"><?php echo rmt_icon( 'clock', 22 ); // phpcs:ignore ?></span><div><small>أوقات العمل</small><strong><?php echo esc_html( $g['hours'] ); ?></strong></div></li>
			</ul>
			<div class="ci-btns">
				<a class="btn btn-wa" href="<?php echo esc_url( rmt_whatsapp_url() ); ?>" target="_blank" rel="noopener" data-track="whatsapp"><?php echo rmt_icon( 'whatsapp', 20 ); // phpcs:ignore ?>واتساب مباشر</a>
				<a class="btn btn-primary" href="tel:<?php echo esc_attr( rmt_tel() ); ?>" data-track="call"><?php echo rmt_icon( 'phone', 20 ); // phpcs:ignore ?>اتصل الآن</a>
			</div>
		</aside>

		<div class="quote-box reveal" id="quote">
			<h2>نموذج طلب عرض سعر</h2>
			<p class="muted">الحقول المعلّمة بـ * مطلوبة. نرد عادة خلال ساعات العمل.</p>

			<?php if ( $sent ) : ?>
				<div class="alert ok" role="status"><?php echo rmt_icon( 'check', 20 ); // phpcs:ignore ?><?php echo esc_html( $ct['success'] ); ?></div>
			<?php elseif ( $err ) : ?>
				<div class="alert err" role="alert">تعذّر إرسال الطلب، يرجى التأكد من الاسم ورقم الجوال ثم المحاولة مجددًا، أو التواصل عبر واتساب.</div>
			<?php endif; ?>

			<form class="quote-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-form>
				<input type="hidden" name="action" value="rmt_quote">
				<input type="hidden" name="rmt_ts" value="<?php echo esc_attr( time() ); ?>">
				<?php wp_nonce_field( 'rmt_quote', 'rmt_nonce' ); ?>
				<div class="hp" aria-hidden="true"><label>الموقع<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

				<div class="f-row">
					<label class="fld"><span>الاسم *</span><input type="text" name="name" required autocomplete="name"></label>
					<label class="fld"><span>اسم الجهة</span><input type="text" name="org" autocomplete="organization"></label>
				</div>
				<div class="f-row">
					<label class="fld"><span>رقم الجوال *</span><input type="tel" name="phone" required inputmode="tel" pattern="[0-9+\s\-]{9,16}" placeholder="05xxxxxxxx" dir="ltr" autocomplete="tel"></label>
					<label class="fld"><span>البريد الإلكتروني</span><input type="email" name="email" dir="ltr" autocomplete="email"></label>
				</div>
				<div class="f-row">
					<label class="fld"><span>المدينة</span><input type="text" name="city" value="الرياض" autocomplete="address-level2"></label>
					<label class="fld"><span>نوع الخدمة *</span>
						<select name="service" required>
							<?php foreach ( rmt_form_services() as $s ) : ?>
								<option<?php selected( $pre, $s ); ?>><?php echo esc_html( $s ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
				<label class="fld"><span>نوع المبنى وعدد الأدوار</span><input type="text" name="building" placeholder="مثال: برج تجاري – 18 دورًا"></label>
				<label class="fld"><span>تفاصيل إضافية</span><textarea name="details" rows="4"></textarea></label>
				<button class="btn btn-primary btn-block" type="submit"><?php echo rmt_icon( 'mail', 20 ); // phpcs:ignore ?>إرسال الطلب</button>
			</form>
		</div>
	</div>
</section>

<section class="section map-sec sand">
	<div class="container">
		<?php rmt_section_head( 'موقعنا على الخريطة', $g['address'], 'h2', 'زورونا' ); ?>
		<div class="map reveal" data-map="<?php echo esc_url( $embed ); ?>">
			<button type="button" class="map-load" data-map-load>
				<?php echo rmt_icon( 'pin', 34 ); // phpcs:ignore ?>
				<strong>عرض الخريطة التفاعلية</strong>
				<span><?php echo esc_html( $g['street'] . ' – ' . $g['city'] ); ?></span>
			</button>
		</div>
		<p class="center"><a class="btn btn-outline" href="<?php echo esc_url( $g['map_url'] ); ?>" target="_blank" rel="noopener"><?php echo rmt_icon( 'pin', 18 ); // phpcs:ignore ?>فتح الموقع في خرائط Google</a></p>
	</div>
</section>

<?php
get_footer();
