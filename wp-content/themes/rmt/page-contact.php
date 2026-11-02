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

$embed = rmt_map_embed_url();
?>

<section class="section">
	<div class="container contact-grid">
		<aside class="contact-info reveal">
			<h2><?php rmt_te( 'بيانات التواصل' ); ?></h2>
			<ul class="ci-list">
				<li><span class="ci-ico"><?php echo rmt_icon( 'pin', 22 ); // phpcs:ignore ?></span><div><small><?php rmt_te( 'العنوان' ); ?></small><strong><?php echo esc_html( $g['address'] ); ?></strong><span><?php echo esc_html( $g['po_box'] ); ?></span></div></li>
				<li><span class="ci-ico"><?php echo rmt_icon( 'phone', 22 ); // phpcs:ignore ?></span><div><small><?php rmt_te( 'الجوال (اتصال)' ); ?></small><a href="tel:<?php echo esc_attr( rmt_tel() ); ?>" dir="ltr" data-track="call"><?php echo esc_html( rmt_tel() ); ?></a></div></li>
				<li><span class="ci-ico wa"><?php echo rmt_icon( 'whatsapp', 22 ); // phpcs:ignore ?></span><div><small><?php rmt_te( 'واتساب' ); ?></small><a href="<?php echo esc_url( rmt_whatsapp_url() ); ?>" target="_blank" rel="noopener" dir="ltr" data-track="whatsapp"><?php echo esc_html( rmt_tel() ); ?></a></div></li>
				<?php if ( $g['landline'] ) : ?>
					<li><span class="ci-ico"><?php echo rmt_icon( 'fax', 22 ); // phpcs:ignore ?></span><div><small><?php rmt_te( 'الهاتف والفاكس' ); ?></small><span dir="ltr"><?php echo esc_html( $g['landline'] ); ?></span></div></li>
				<?php endif; ?>
				<li><span class="ci-ico"><?php echo rmt_icon( 'mail', 22 ); // phpcs:ignore ?></span><div><small><?php rmt_te( 'البريد الإلكتروني' ); ?></small><a href="mailto:<?php echo esc_attr( $g['email_primary'] ); ?>" dir="ltr"><?php echo esc_html( $g['email_primary'] ); ?></a><?php if ( $g['email_secondary'] ) : ?><a href="mailto:<?php echo esc_attr( $g['email_secondary'] ); ?>" dir="ltr"><?php echo esc_html( $g['email_secondary'] ); ?></a><?php endif; ?></div></li>
				<li><span class="ci-ico"><?php echo rmt_icon( 'clock', 22 ); // phpcs:ignore ?></span><div><small><?php rmt_te( 'أوقات العمل' ); ?></small><strong><?php echo esc_html( $g['hours'] ); ?></strong></div></li>
			</ul>
			<div class="ci-btns">
				<a class="btn btn-wa" href="<?php echo esc_url( rmt_whatsapp_url() ); ?>" target="_blank" rel="noopener" data-track="whatsapp"><?php echo rmt_icon( 'whatsapp', 20 ); // phpcs:ignore ?><?php rmt_te( 'واتساب مباشر' ); ?></a>
				<a class="btn btn-primary" href="tel:<?php echo esc_attr( rmt_tel() ); ?>" data-track="call"><?php echo rmt_icon( 'phone', 20 ); // phpcs:ignore ?><?php rmt_te( 'اتصل الآن' ); ?></a>
			</div>
		</aside>

		<div class="quote-box reveal" id="quote">
			<h2><?php rmt_te( 'نموذج طلب عرض سعر' ); ?></h2>
			<p class="muted"><?php rmt_te( 'الحقول المعلّمة بـ * مطلوبة. نرد عادة خلال ساعات العمل.' ); ?></p>

			<?php if ( $sent ) : ?>
				<div class="alert ok" role="status"><?php echo rmt_icon( 'check', 20 ); // phpcs:ignore ?><?php echo esc_html( $ct['success'] ); ?></div>
			<?php elseif ( $err ) : ?>
				<div class="alert err" role="alert"><?php rmt_te( 'تعذّر إرسال الطلب، يرجى التأكد من الاسم ورقم الجوال والموافقة على سياسة الخصوصية ثم المحاولة مجددًا، أو التواصل عبر واتساب.' ); ?></div>
			<?php endif; ?>

			<form class="quote-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-form>
				<input type="hidden" name="action" value="rmt_quote">
				<input type="hidden" name="rmt_ts" value="<?php echo esc_attr( time() ); ?>">
				<?php wp_nonce_field( 'rmt_quote', 'rmt_nonce' ); ?>
				<div class="hp" aria-hidden="true"><label><?php rmt_te( 'الموقع' ); ?><input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

				<div class="f-row">
					<label class="fld"><span><?php rmt_te( 'الاسم *' ); ?></span><input type="text" name="name" required autocomplete="name"></label>
					<label class="fld"><span><?php rmt_te( 'اسم الجهة' ); ?></span><input type="text" name="org" autocomplete="organization"></label>
				</div>
				<div class="f-row">
					<label class="fld"><span><?php rmt_te( 'رقم الجوال *' ); ?></span><input type="tel" name="phone" required inputmode="tel" pattern="[0-9+\s\-]{9,16}" placeholder="05xxxxxxxx" dir="ltr" autocomplete="tel"></label>
					<label class="fld"><span><?php rmt_te( 'البريد الإلكتروني' ); ?></span><input type="email" name="email" dir="ltr" autocomplete="email"></label>
				</div>
				<div class="f-row">
					<label class="fld"><span><?php rmt_te( 'المدينة' ); ?></span><input type="text" name="city" value="<?php echo esc_attr( rmt_t( 'الرياض' ) ); ?>" autocomplete="address-level2"></label>
					<label class="fld"><span><?php rmt_te( 'نوع الخدمة *' ); ?></span>
						<select name="service" required>
							<?php foreach ( rmt_form_services() as $s ) : ?>
								<option<?php selected( $pre, $s ); ?>><?php echo esc_html( $s ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
				<label class="fld"><span><?php rmt_te( 'نوع المبنى وعدد الأدوار' ); ?></span><input type="text" name="building" placeholder="<?php echo esc_attr( rmt_t( 'مثال: برج تجاري – 18 دورًا' ) ); ?>"></label>
				<label class="fld"><span><?php rmt_te( 'تفاصيل إضافية' ); ?></span><textarea name="details" rows="4"></textarea></label>
				<label class="consent"><input type="checkbox" name="consent" value="1" required> <span><?php rmt_te( 'أوافق على' ); ?> <a href="<?php echo esc_url( rmt_page_url( 'privacy' ) ); ?>" target="_blank"><?php rmt_te( 'سياسة الخصوصية' ); ?></a> <?php rmt_te( 'ومعالجة بياناتي للرد على طلبي.' ); ?> *</span></label>
				<button class="btn btn-primary btn-block" type="submit"><?php echo rmt_icon( 'mail', 20 ); // phpcs:ignore ?><?php rmt_te( 'إرسال الطلب' ); ?></button>
			</form>
		</div>
	</div>
</section>

<?php
$dir_url = ( $g['lat'] && $g['lng'] )
	? 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $g['lat'] . ',' . $g['lng'] )
	: $g['map_url'];
?>
<section class="section map-sec sand" id="location">
	<div class="container">
		<?php rmt_section_head( rmt_t( 'موقعنا على الخريطة' ), '', 'h2', rmt_t( 'زورونا' ) ); ?>
		<div class="map-wrap reveal">
			<div class="map" data-map="<?php echo esc_url( $embed ); ?>">
				<button type="button" class="map-load" data-map-load>
					<?php echo rmt_icon( 'pin', 34 ); // phpcs:ignore ?>
					<strong><?php rmt_te( 'عرض الخريطة التفاعلية' ); ?></strong>
					<span><?php echo esc_html( $g['street'] . ' – ' . $g['city'] ); ?></span>
				</button>
			</div>
			<div class="map-card">
				<span class="ci-ico"><?php echo rmt_icon( 'pin', 22 ); // phpcs:ignore ?></span>
				<h3><?php echo esc_html( $g['company_name'] ); ?></h3>
				<p><?php echo esc_html( $g['address'] ); ?><br><small><?php echo esc_html( $g['po_box'] ); ?></small></p>
				<p class="map-hours"><?php echo rmt_icon( 'clock', 16 ); // phpcs:ignore ?><?php echo esc_html( $g['hours'] ); ?></p>
				<div class="map-btns">
					<a class="btn btn-primary btn-sm" href="<?php echo esc_url( $dir_url ); ?>" target="_blank" rel="noopener"><?php rmt_te( 'احصل على الاتجاهات' ); ?></a>
					<a class="btn btn-ghost btn-sm" href="<?php echo esc_url( $g['map_url'] ); ?>" target="_blank" rel="noopener"><?php rmt_te( 'فتح في خرائط Google' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
