<?php
/**
 * "The descent" – the company story told as a rope-access descent down a
 * tower. A technician slides down the rope with scroll progress and every
 * milestone (floor) lights up as he passes it.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
$steps = (array) rmt_get( 'about', 'timeline' );
if ( ! $steps ) {
	return;
}
$n = count( $steps );
?>
<section class="section descent" data-descent>
	<div class="descent-bg" aria-hidden="true"></div>
	<div class="container">
		<?php rmt_section_head( rmt_t( 'مسيرتنا… نزولًا بالحبل' ), rmt_t( 'كل مرحلة في قصتنا كانت دورًا جديدًا نصل إليه بثبات وأمان.' ), 'h2', rmt_t( 'منذ 2003' ) ); ?>

		<div class="descent-track">
			<div class="descent-rope" aria-hidden="true">
				<span class="descent-anchor"></span>
				<span class="descent-line"><i></i></span>
				<span class="descent-climber">
					<svg viewBox="0 0 40 56" width="40" height="56"><circle cx="20" cy="9" r="7" fill="#ef8c3c"/><path d="M12 18h16l-2 20H14z" fill="#fff"/><path d="M15 38l-4 14M25 38l4 14M12 22l-8 8M28 22l-6 12" stroke="#fff" stroke-width="3" stroke-linecap="round" fill="none"/><path d="M20 0v2" stroke="#ef8c3c" stroke-width="2"/></svg>
				</span>
			</div>

			<ol class="descent-floors">
				<?php foreach ( $steps as $i => $t ) : ?>
					<li class="floor" style="--i:<?php echo (int) $i; ?>">
						<span class="floor-no"><?php echo esc_html( rmt_t( 'الدور' ) . ' ' . sprintf( '%02d', $n - $i ) ); ?></span>
						<div class="floor-card">
							<strong><?php echo esc_html( $t['year'] ); ?></strong>
							<p><?php echo esc_html( $t['text'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
				<li class="floor floor-ground">
					<span class="floor-no"><?php rmt_te( 'الأرض' ); ?></span>
					<div class="floor-card floor-next">
						<strong><?php rmt_te( 'الدور القادم… مشروعك' ); ?></strong>
						<p><?php rmt_te( 'نستعد للنزول على واجهة مبناك بنفس الالتزام والدقة.' ); ?></p>
						<a class="btn btn-primary btn-sm" href="<?php echo esc_url( rmt_page_url( 'contact' ) ); ?>#quote"><?php rmt_te( 'ابدأ مشروعك معنا' ); ?></a>
					</div>
				</li>
			</ol>
		</div>
	</div>
</section>
