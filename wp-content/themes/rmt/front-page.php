<?php
/**
 * Home page (/).
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
get_header();
$h        = rmt_get( 'home' );
$g        = rmt_get( 'general' );
$services = (array) rmt_get( 'services', 'items' );
$clients  = (array) rmt_get( 'clients', 'items' );
?>

<section class="hero">
	<div class="hero-media" aria-hidden="true">
		<?php if ( $h['hero_poster'] ) : ?>
			<?php echo rmt_img( $h['hero_poster'], '', 'hero-poster', true, '100vw' ); // phpcs:ignore ?>
		<?php endif; ?>
		<?php if ( $h['hero_video'] ) : ?>
			<video class="hero-video" muted loop playsinline preload="none" data-src="<?php echo esc_url( $h['hero_video'] ); ?>"></video>
		<?php endif; ?>
		<span class="hero-shade"></span>
		<span class="hero-pattern"></span>
	</div>
	<div class="container hero-in">
		<div class="hero-copy">
			<span class="badge reveal"><?php echo rmt_icon( 'shield', 16 ); // phpcs:ignore ?><?php echo esc_html( $h['hero_badge'] ); ?></span>
			<h1 class="hero-title reveal"><?php echo esc_html( $h['hero_title'] ); ?></h1>
			<p class="hero-text reveal"><?php echo esc_html( $h['hero_text'] ); ?></p>
			<div class="reveal"><?php rmt_cta_buttons( true ); ?></div>
		</div>
	</div>
	<div class="container">
		<ul class="stats reveal" aria-label="أرقامنا">
			<?php foreach ( (array) $h['stats'] as $s ) : ?>
				<li><strong><span data-count="<?php echo esc_attr( $s['number'] ); ?>"><?php echo esc_html( $s['number'] ); ?></span><?php echo esc_html( $s['suffix'] ); ?></strong><span><?php echo esc_html( $s['label'] ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section services-home" id="services">
	<div class="container">
		<?php rmt_section_head( $h['services_title'], $h['services_text'], 'h2', 'ماذا نقدم' ); ?>
		<div class="cards">
			<?php
			$n = 0;
			foreach ( $services as $i => $s ) :
				if ( '1' !== $s['home'] ) {
					continue;
				}
				?>
				<article class="card reveal" style="--d:<?php echo (int) ( $n++ % 3 ) * 90; ?>ms">
					<span class="card-ico"><?php echo rmt_icon( $s['icon'], 30 ); // phpcs:ignore ?></span>
					<h3><?php echo esc_html( $s['title'] ); ?></h3>
					<p><?php echo esc_html( $s['short'] ); ?></p>
					<a class="card-link" href="<?php echo esc_url( rmt_page_url( 'services' ) . '#' . rmt_slug( $s['title'], $i ) ); ?>" aria-label="المزيد عن <?php echo esc_attr( $s['title'] ); ?>">التفاصيل <?php echo rmt_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
				</article>
			<?php endforeach; ?>
		</div>
		<p class="center reveal"><a class="btn btn-outline" href="<?php echo esc_url( rmt_page_url( 'services' ) ); ?>">عرض جميع الخدمات <?php echo rmt_icon( 'arrow', 18 ); // phpcs:ignore ?></a></p>
	</div>
</section>

<section class="section why sand">
	<div class="container why-grid">
		<div class="why-media reveal">
			<?php echo rmt_img( rmt_get( 'about', 'image' ), 'فريق رفال المدينة ينظف واجهة برج زجاجي بالحبال في الرياض', 'why-img', false, '(max-width: 900px) 100vw, 45vw' ); // phpcs:ignore ?>
			<div class="why-badge"><strong><?php echo esc_html( (int) wp_date( 'Y' ) - (int) $g['founded'] ); ?>+</strong><span>عامًا من الثقة</span></div>
		</div>
		<div class="why-copy">
			<?php rmt_section_head( $h['why_title'], '', 'h2', 'قيمتنا المضافة' ); ?>
			<ul class="why-list">
				<?php foreach ( (array) $h['why'] as $i => $w ) : ?>
					<li class="reveal" style="--d:<?php echo (int) $i * 80; ?>ms"><span class="tick"><?php echo rmt_icon( 'check', 18 ); // phpcs:ignore ?></span><div><h3><?php echo esc_html( $w['title'] ); ?></h3><p><?php echo esc_html( $w['text'] ); ?></p></div></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>

<section class="section projects">
	<div class="container">
		<?php rmt_section_head( $h['projects_title'], $h['projects_text'], 'h2', 'سابقة الأعمال' ); ?>
		<?php rmt_gallery( 8 ); ?>
		<p class="center reveal"><a class="btn btn-outline" href="<?php echo esc_url( rmt_page_url( 'clients' ) ); ?>#work">مشاهدة كل الأعمال <?php echo rmt_icon( 'arrow', 18 ); // phpcs:ignore ?></a></p>
	</div>
</section>

<section class="section trust navy">
	<div class="container">
		<?php rmt_section_head( $h['trust_title'], $h['trust_text'], 'h2', 'عملاؤنا' ); ?>
	</div>
	<div class="marquee reveal" aria-label="شعارات العملاء">
		<div class="marquee-track">
			<?php for ( $loop = 0; $loop < 2; $loop++ ) : ?>
				<div class="marquee-group"<?php echo $loop ? ' aria-hidden="true"' : ''; ?>>
					<?php foreach ( $clients as $c ) { rmt_client_card( $c ); } ?>
				</div>
			<?php endfor; ?>
		</div>
	</div>
	<p class="center reveal"><a class="btn btn-ghost-light" href="<?php echo esc_url( rmt_page_url( 'clients' ) ); ?>">تعرّف على عملائنا <?php echo rmt_icon( 'arrow', 18 ); // phpcs:ignore ?></a></p>
</section>

<?php if ( ! empty( $h['faq'] ) ) : ?>
<section class="section faq">
	<div class="container narrow">
		<?php rmt_section_head( $h['faq_title'], '', 'h2', 'استفسارات' ); ?>
		<div class="accordion">
			<?php foreach ( (array) $h['faq'] as $i => $q ) : ?>
				<details class="acc reveal"<?php echo 0 === $i ? ' open' : ''; ?>>
					<summary><h3><?php echo esc_html( $q['q'] ); ?></h3><span class="acc-ico"><?php echo rmt_icon( 'plus', 18 ); // phpcs:ignore ?></span></summary>
					<div class="acc-body"><p><?php echo esc_html( $q['a'] ); ?></p></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
get_footer();
