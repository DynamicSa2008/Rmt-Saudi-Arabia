<?php
/**
 * Footer.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
$g      = rmt_get( 'general' );
$f      = rmt_get( 'footer' );
$social = array(
	'social_x'         => array( 'x', 'X' ),
	'social_instagram' => array( 'instagram', 'إنستغرام' ),
	'social_snapchat'  => array( 'snapchat', 'سناب شات' ),
	'social_tiktok'    => array( 'tiktok', 'تيك توك' ),
	'social_linkedin'  => array( 'linkedin', 'لينكدإن' ),
	'social_youtube'   => array( 'youtube', 'يوتيوب' ),
);
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
	<div class="sadu-band" aria-hidden="true"></div>
	<div class="container footer-grid">
		<div class="f-col f-about">
			<a class="brand brand-light" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="<?php echo esc_url( rmt_logo_url( 'small' ) ); ?>" alt="شعار <?php echo esc_attr( $g['company_name'] ); ?>" width="64" height="64" loading="lazy">
				<span class="brand-txt"><strong><?php echo esc_html( $g['company_name'] ); ?></strong><small><?php echo esc_html( $g['name_en'] ); ?></small></span>
			</a>
			<p><?php echo esc_html( $f['about'] ); ?></p>
			<?php
			$has = false;
			foreach ( $social as $k => $s ) {
				$has = $has || ! empty( $g[ $k ] );
			}
			if ( $has ) :
				?>
				<ul class="social">
					<?php foreach ( $social as $k => $s ) : ?>
						<?php if ( ! empty( $g[ $k ] ) ) : ?>
							<li><a href="<?php echo esc_url( $g[ $k ] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $s[1] ); ?>"><?php echo rmt_icon( $s[0], 18 ); // phpcs:ignore ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="f-col">
			<h3>روابط سريعة</h3>
			<?php rmt_nav( 'f-links' ); ?>
		</div>

		<div class="f-col">
			<h3>خدماتنا</h3>
			<ul class="f-links">
				<?php foreach ( array_slice( (array) rmt_get( 'services', 'items' ), 0, 6 ) as $i => $s ) : ?>
					<li><a href="<?php echo esc_url( rmt_page_url( 'services' ) . '#' . rmt_slug( $s['title'], $i ) ); ?>"><?php echo esc_html( $s['title'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="f-col">
			<h3>تواصل معنا</h3>
			<ul class="f-contact">
				<li><?php echo rmt_icon( 'pin', 18 ); // phpcs:ignore ?><span><?php echo esc_html( $g['address'] ); ?><br><small><?php echo esc_html( $g['po_box'] ); ?></small></span></li>
				<li><?php echo rmt_icon( 'phone', 18 ); // phpcs:ignore ?><a href="tel:<?php echo esc_attr( rmt_tel() ); ?>" dir="ltr"><?php echo esc_html( rmt_phone_display() ); ?></a></li>
				<li><?php echo rmt_icon( 'whatsapp', 18 ); // phpcs:ignore ?><a href="<?php echo esc_url( rmt_whatsapp_url() ); ?>" target="_blank" rel="noopener">واتساب: <span dir="ltr"><?php echo esc_html( rmt_phone_display() ); ?></span></a></li>
				<?php if ( $g['landline'] ) : ?>
					<li><?php echo rmt_icon( 'fax', 18 ); // phpcs:ignore ?><span>هاتف وفاكس: <span dir="ltr"><?php echo esc_html( $g['landline'] ); ?></span></span></li>
				<?php endif; ?>
				<li><?php echo rmt_icon( 'mail', 18 ); // phpcs:ignore ?><span class="f-mails"><a href="mailto:<?php echo esc_attr( $g['email_primary'] ); ?>" dir="ltr"><?php echo esc_html( $g['email_primary'] ); ?></a><?php if ( $g['email_secondary'] ) : ?><a href="mailto:<?php echo esc_attr( $g['email_secondary'] ); ?>" dir="ltr"><?php echo esc_html( $g['email_secondary'] ); ?></a><?php endif; ?></span></li>
			</ul>
		</div>
	</div>

	<div class="footer-bottom">
		<div class="container fb-in">
			<p class="copy">© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( $f['copyright'] ); ?></p>
			<p class="dev"><?php echo esc_html( $f['dev_text'] ); ?> — <a href="<?php echo esc_url( $f['dev_url'] ); ?>" target="_blank" rel="noopener" dir="ltr"><?php echo esc_html( preg_replace( '#^https?://(www\.)?#', '', untrailingslashit( $f['dev_url'] ) ) ); ?></a></p>
		</div>
	</div>
</footer>

<?php if ( '1' === $g['show_float'] ) : ?>
<div class="float-cta" aria-label="تواصل سريع">
	<a class="fc fc-wa" href="<?php echo esc_url( rmt_whatsapp_url() ); ?>" target="_blank" rel="noopener" aria-label="تواصل عبر واتساب" data-track="whatsapp"><?php echo rmt_icon( 'whatsapp', 28 ); // phpcs:ignore ?><span>واتساب</span></a>
	<a class="fc fc-call" href="tel:<?php echo esc_attr( rmt_tel() ); ?>" aria-label="اتصل بنا" data-track="call"><?php echo rmt_icon( 'phone', 24 ); // phpcs:ignore ?><span>اتصال</span></a>
</div>
<?php endif; ?>
<button class="to-top" type="button" aria-label="العودة للأعلى" data-top><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M6 11l6-6 6 6"/></svg></button>

<div class="lightbox" data-lightbox hidden role="dialog" aria-modal="true" aria-label="عرض العمل">
	<button class="lb-close" type="button" aria-label="إغلاق" data-lb-close><?php echo rmt_icon( 'close', 26 ); // phpcs:ignore ?></button>
	<button class="lb-nav lb-prev" type="button" aria-label="السابق" data-lb-prev>‹</button>
	<div class="lb-stage" data-lb-stage></div>
	<button class="lb-nav lb-next" type="button" aria-label="التالي" data-lb-next>›</button>
	<p class="lb-cap" data-lb-cap></p>
</div>

<?php wp_footer(); ?>
</body>
</html>
