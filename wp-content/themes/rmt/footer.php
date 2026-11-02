<?php
/**
 * Footer – editorial layout: a large contact statement, plain-text columns,
 * hairline rules and a quiet legal bar.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
$g      = rmt_get( 'general' );
$f      = rmt_get( 'footer' );
$social = array(
	'social_x'         => array( 'x', 'X' ),
	'social_instagram' => array( 'instagram', 'Instagram' ),
	'social_snapchat'  => array( 'snapchat', 'Snapchat' ),
	'social_tiktok'    => array( 'tiktok', 'TikTok' ),
	'social_linkedin'  => array( 'linkedin', 'LinkedIn' ),
	'social_youtube'   => array( 'youtube', 'YouTube' ),
);
$dev_host = preg_replace( '#^https?://(www\.)?#', '', untrailingslashit( $f['dev_url'] ) );
?>
</main>

<?php if ( 'contact' !== rmt_current_key() ) : ?>
<section class="cta-band">
	<div class="container cta-in reveal">
		<div>
			<h2><?php rmt_e( 'home', 'cta_title' ); ?></h2>
			<p><?php rmt_e( 'home', 'cta_text' ); ?></p>
		</div>
		<?php rmt_cta_buttons( true ); ?>
	</div>
</section>
<?php endif; ?>

<footer class="site-footer">
	<div class="container">

		<div class="ft-lead">
			<a class="ft-brand" href="<?php echo esc_url( rmt_page_url( 'home' ) ); ?>">
				<img src="<?php echo esc_url( rmt_logo_url( 'small' ) ); ?>" alt="<?php echo esc_attr( rmt_t( 'شعار ' ) . $g['company_name'] ); ?>" width="72" height="72" loading="lazy">
				<span>
					<strong><?php echo esc_html( $g['company_name'] ); ?></strong>
					<small lang="<?php echo rmt_is_en() ? 'ar' : 'en'; ?>" dir="<?php echo rmt_is_en() ? 'rtl' : 'ltr'; ?>"><?php echo esc_html( rmt_is_en() ? 'مؤسسة رفال المدينة للمقاولات' : $g['name_en'] ); ?></small>
				</span>
			</a>
			<div class="ft-call">
				<span class="ft-label"><?php rmt_te( 'للاستفسار وطلب المعاينة' ); ?></span>
				<a class="ft-phone" href="tel:<?php echo esc_attr( rmt_tel() ); ?>" dir="ltr" data-track="call"><?php echo esc_html( rmt_phone_display() ); ?></a>
				<a class="ft-wa" href="<?php echo esc_url( rmt_whatsapp_url() ); ?>" target="_blank" rel="noopener" data-track="whatsapp"><?php rmt_te( 'أو راسلنا على واتساب' ); ?> <span aria-hidden="true">↗</span></a>
			</div>
		</div>

		<div class="ft-grid">
			<div class="ft-col ft-about">
				<h3 class="ft-label"><?php rmt_te( 'عن المؤسسة' ); ?></h3>
				<p><?php echo esc_html( $f['about'] ); ?></p>
			</div>

			<nav class="ft-col" aria-label="<?php echo esc_attr( rmt_t( 'روابط الموقع' ) ); ?>">
				<h3 class="ft-label"><?php rmt_te( 'الصفحات' ); ?></h3>
				<?php rmt_nav( 'ft-links' ); ?>
			</nav>

			<div class="ft-col">
				<h3 class="ft-label"><?php rmt_te( 'خدماتنا' ); ?></h3>
				<ul class="ft-links">
					<?php foreach ( array_slice( (array) rmt_get( 'services', 'items' ), 0, 6 ) as $i => $s ) : ?>
						<li><a href="<?php echo esc_url( rmt_page_url( 'services' ) . '#' . rmt_slug( $s['title'], $i ) ); ?>"><?php echo esc_html( $s['title'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="ft-col ft-contact">
				<h3 class="ft-label"><?php rmt_te( 'العنوان' ); ?></h3>
				<address>
					<?php echo esc_html( $g['address'] ); ?><br>
					<?php echo esc_html( $g['po_box'] ); ?>
				</address>
				<h3 class="ft-label"><?php rmt_te( 'البريد الإلكتروني' ); ?></h3>
				<p class="ft-mails">
					<a href="mailto:<?php echo esc_attr( $g['email_primary'] ); ?>" dir="ltr"><?php echo esc_html( $g['email_primary'] ); ?></a>
					<?php if ( $g['email_secondary'] ) : ?>
						<a href="mailto:<?php echo esc_attr( $g['email_secondary'] ); ?>" dir="ltr"><?php echo esc_html( $g['email_secondary'] ); ?></a>
					<?php endif; ?>
				</p>
				<?php if ( $g['landline'] ) : ?>
					<h3 class="ft-label"><?php rmt_te( 'الهاتف والفاكس' ); ?></h3>
					<p dir="ltr" class="ft-ltr"><?php echo esc_html( $g['landline'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="ft-bottom">
			<p class="ft-copy">© <?php echo esc_html( wp_date( 'Y' ) ); ?> — <?php echo esc_html( $f['copyright'] ); ?></p>
			<?php
			$has = false;
			foreach ( $social as $k => $s ) {
				$has = $has || ! empty( $g[ $k ] );
			}
			if ( $has ) :
				?>
				<ul class="ft-social">
					<?php foreach ( $social as $k => $s ) : ?>
						<?php if ( ! empty( $g[ $k ] ) ) : ?>
							<li><a href="<?php echo esc_url( $g[ $k ] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $s[1] ); ?>"><?php echo rmt_icon( $s[0], 17 ); // phpcs:ignore ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<a class="ft-dev" href="<?php echo esc_url( $f['dev_url'] ); ?>" target="_blank" rel="noopener" title="<?php echo esc_attr( $dev_host ); ?>">
				<span><?php echo esc_html( $f['dev_text'] ); ?></span>
				<b dir="ltr"><?php echo esc_html( $dev_host ); ?></b>
			</a>
		</div>
	</div>
</footer>

<?php if ( '1' === $g['show_float'] ) : ?>
<div class="float-cta" aria-label="<?php echo esc_attr( rmt_t( 'تواصل سريع' ) ); ?>">
	<a class="fc fc-wa" href="<?php echo esc_url( rmt_whatsapp_url() ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( rmt_t( 'تواصل عبر واتساب' ) ); ?>" data-track="whatsapp"><?php echo rmt_icon( 'whatsapp', 28 ); // phpcs:ignore ?><span><?php rmt_te( 'واتساب' ); ?></span></a>
	<a class="fc fc-call" href="tel:<?php echo esc_attr( rmt_tel() ); ?>" aria-label="<?php echo esc_attr( rmt_t( 'اتصل بنا' ) ); ?>" data-track="call"><?php echo rmt_icon( 'phone', 24 ); // phpcs:ignore ?><span><?php rmt_te( 'اتصال' ); ?></span></a>
</div>
<?php endif; ?>
<button class="to-top" type="button" aria-label="<?php echo esc_attr( rmt_t( 'العودة للأعلى' ) ); ?>" data-top><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M6 11l6-6 6 6"/></svg></button>

<div class="lightbox" data-lightbox hidden role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( rmt_t( 'عرض العمل' ) ); ?>">
	<button class="lb-close" type="button" aria-label="<?php echo esc_attr( rmt_t( 'إغلاق' ) ); ?>" data-lb-close><?php echo rmt_icon( 'close', 26 ); // phpcs:ignore ?></button>
	<button class="lb-nav lb-prev" type="button" aria-label="<?php echo esc_attr( rmt_t( 'السابق' ) ); ?>" data-lb-prev>‹</button>
	<div class="lb-stage" data-lb-stage></div>
	<button class="lb-nav lb-next" type="button" aria-label="<?php echo esc_attr( rmt_t( 'التالي' ) ); ?>" data-lb-next>›</button>
	<p class="lb-cap" data-lb-cap></p>
</div>

<?php wp_footer(); ?>
</body>
</html>
