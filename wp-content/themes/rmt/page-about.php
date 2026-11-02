<?php
/**
 * About page (/about).
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
get_header();
$a = rmt_get( 'about' );
$g = rmt_get( 'general' );
rmt_page_hero( 'about', $a['title'], $g['tagline'] );
?>

<section class="section">
	<div class="container split">
		<div class="split-copy reveal">
			<span class="eyebrow">نبذة عنا</span>
			<h2 class="sec-title start">مؤسسة سعودية بخبرة تمتد منذ <?php echo esc_html( $g['founded_hijri'] ); ?>هـ</h2>
			<?php echo rmt_ornament( 'start' ); // phpcs:ignore ?>
			<p class="lead"><?php echo esc_html( $a['intro'] ); ?></p>
			<ul class="facts">
				<li><strong><?php echo esc_html( $g['founded_hijri'] ); ?>هـ</strong><span>سنة التأسيس</span></li>
				<li><strong><?php echo esc_html( $g['city'] ); ?></strong><span>المقر الرئيسي</span></li>
				<li><strong>حكومي وخاص</strong><span>القطاعات المخدومة</span></li>
			</ul>
		</div>
		<div class="split-media reveal">
			<?php echo rmt_img( $a['image'], 'فني من رفال المدينة يعمل على واجهة زجاجية بنظام الحبال', 'rounded', false, '(max-width: 900px) 100vw, 45vw' ); // phpcs:ignore ?>
		</div>
	</div>
</section>

<section class="section sand">
	<div class="container mv">
		<article class="mv-card reveal">
			<span class="mv-ico"><?php echo rmt_icon( 'star', 30 ); // phpcs:ignore ?></span>
			<h2>رسالتنا</h2>
			<p><?php echo esc_html( $a['mission'] ); ?></p>
		</article>
		<article class="mv-card reveal" style="--d:120ms">
			<span class="mv-ico"><?php echo rmt_icon( 'tower', 30 ); // phpcs:ignore ?></span>
			<h2>رؤيتنا</h2>
			<p><?php echo esc_html( $a['vision'] ); ?></p>
		</article>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php rmt_section_head( 'قيمنا', 'المبادئ التي نلتزم بها في كل مشروع.', 'h2', 'ما يميزنا' ); ?>
		<div class="values">
			<?php
			$vi = array( 'shield', 'star', 'clock', 'building' );
			foreach ( (array) $a['values'] as $i => $v ) :
				?>
				<article class="value reveal" style="--d:<?php echo (int) $i * 90; ?>ms">
					<span class="value-num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<span class="card-ico"><?php echo rmt_icon( $vi[ $i % 4 ], 28 ); // phpcs:ignore ?></span>
					<h3><?php echo esc_html( $v['title'] ); ?></h3>
					<p><?php echo esc_html( $v['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php if ( ! empty( $a['timeline'] ) ) : ?>
<section class="section sand">
	<div class="container narrow">
		<?php rmt_section_head( 'مسيرتنا', '', 'h2', 'منذ 2003' ); ?>
		<ol class="timeline">
			<?php foreach ( (array) $a['timeline'] as $t ) : ?>
				<li class="reveal"><span class="tl-dot"></span><strong><?php echo esc_html( $t['year'] ); ?></strong><p><?php echo esc_html( $t['text'] ); ?></p></li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
<?php endif; ?>

<section class="section navy">
	<div class="container">
		<?php rmt_section_head( $a['safety_title'], $a['safety_text'], 'h2', 'السلامة أولًا' ); ?>
		<div class="certs">
			<?php foreach ( (array) $a['certs'] as $c ) : ?>
				<div class="cert reveal">
					<?php if ( $c['logo'] ) : ?>
						<?php echo rmt_img( $c['logo'], 'شعار ' . $c['name'], 'cert-logo', false, '160px' ); // phpcs:ignore ?>
					<?php else : ?>
						<span class="cert-mark" dir="ltr"><?php echo esc_html( $c['name'] ); ?></span>
					<?php endif; ?>
					<span><?php echo esc_html( $c['desc'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
get_footer();
