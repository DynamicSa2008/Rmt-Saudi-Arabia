<?php
/**
 * Before / After project gallery.
 *
 * A large stage where a squeegee wipes the "before" photo into the "after"
 * photo (button, drag or autoplay), project facts with count-up numbers,
 * thumbnails with an autoplay progress bar, prev/next and swipe.
 * Only projects that have BOTH photos are shown publicly.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

$all   = (array) rmt_get( 'projects', 'ba' );
$items = array_values(
	array_filter(
		$all,
		function ( $p ) {
			return ! empty( $p['before'] ) && ! empty( $p['after'] );
		}
	)
);

if ( ! $items ) {
	if ( current_user_can( 'edit_theme_options' ) ) {
		echo '<section class="section"><div class="container"><p class="ba-admin-note">' . esc_html( 'قسم «قبل / بعد» مخفي عن الزوار حتى ترفع صورتي قبل وبعد لمشروع واحد على الأقل من: لوحة رفال ← سابقة الأعمال ← مشاريع قبل / بعد.' ) . ' <a href="' . esc_url( admin_url( 'admin.php?page=rmt-panel#projects' ) ) . '">' . esc_html( 'رفع الصور' ) . '</a></p></div></section>';
	}
	return;
}

$total = count( $items );
$facts = array(
	'floors'   => array( 'building', rmt_t( 'الأدوار' ) ),
	'area'     => array( 'glass', rmt_t( 'م² زجاج وواجهات' ) ),
	'duration' => array( 'clock', rmt_t( 'مدة التنفيذ' ) ),
	'team'     => array( 'rope', rmt_t( 'فنيين' ) ),
);
?>
<section class="section ba-sec" data-ba>
	<div class="container">
		<?php rmt_section_head( rmt_get( 'projects', 'ba_title' ), rmt_get( 'projects', 'ba_text' ), 'h2', rmt_t( 'قبل / بعد' ) ); ?>

		<div class="ba-grid reveal">
			<div class="ba-stage" data-ba-stage tabindex="0" role="slider" aria-label="<?php echo esc_attr( rmt_t( 'اسحب للمقارنة بين قبل وبعد' ) ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
				<?php foreach ( array( 'before', 'after' ) as $layer ) : ?>
					<div class="ba-layer ba-<?php echo esc_attr( $layer ); ?>">
						<img src="<?php echo esc_url( $items[0][ $layer ] ); ?>" alt="<?php echo esc_attr( $items[0]['title'] . ' – ' . ( 'before' === $layer ? rmt_t( 'قبل التنظيف' ) : rmt_t( 'بعد التنظيف' ) ) ); ?>" data-ba-img="<?php echo esc_attr( $layer ); ?>" loading="lazy" decoding="async">
					</div>
				<?php endforeach; ?>
				<span class="ba-wiper" aria-hidden="true"><i></i><b></b></span>
				<span class="ba-sparks" aria-hidden="true"></span>
				<span class="ba-state" data-ba-state><span class="ba-dot"></span><em><?php rmt_te( 'قبل' ); ?></em></span>
				<span class="ba-count" aria-hidden="true"><b data-ba-cur>01</b> / <?php echo esc_html( sprintf( '%02d', $total ) ); ?></span>
				<div class="ba-toggle" role="group" aria-label="<?php echo esc_attr( rmt_t( 'عرض الحالة' ) ); ?>">
					<button type="button" data-ba-go="0" aria-pressed="true"><?php rmt_te( 'قبل' ); ?></button>
					<button type="button" data-ba-go="100" aria-pressed="false"><?php rmt_te( 'بعد' ); ?></button>
				</div>
				<span class="ba-hint" aria-hidden="true"><?php rmt_te( 'اسحب على الصورة' ); ?> ⟷</span>
			</div>

			<div class="ba-side">
				<article class="ba-info" aria-live="polite">
					<span class="ba-svc" data-ba-f="service"><?php echo esc_html( $items[0]['service'] ); ?></span>
					<h3 data-ba-f="title"><?php echo esc_html( $items[0]['title'] ); ?></h3>
					<p class="ba-place"><?php echo rmt_icon( 'pin', 16 ); // phpcs:ignore ?><span data-ba-f="place"><?php echo esc_html( $items[0]['place'] ); ?></span></p>
					<dl class="ba-facts">
						<?php foreach ( $facts as $k => $f ) : ?>
							<div class="ba-fact" data-ba-fact="<?php echo esc_attr( $k ); ?>"<?php echo '' === trim( (string) $items[0][ $k ] ) ? ' hidden' : ''; ?>>
								<dt><?php echo rmt_icon( $f[0], 18 ); // phpcs:ignore ?><?php echo esc_html( $f[1] ); ?></dt>
								<dd data-ba-num><?php echo esc_html( $items[0][ $k ] ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
					<div class="ba-nav">
						<button type="button" class="ba-arrow" data-ba-step="-1" aria-label="<?php echo esc_attr( rmt_t( 'المشروع السابق' ) ); ?>"><?php echo rmt_icon( 'arrow', 18 ); // phpcs:ignore ?></button>
						<button type="button" class="ba-arrow ba-next" data-ba-step="1" aria-label="<?php echo esc_attr( rmt_t( 'المشروع التالي' ) ); ?>"><?php echo rmt_icon( 'arrow', 18 ); // phpcs:ignore ?></button>
						<a class="btn btn-primary btn-sm" href="<?php echo esc_url( rmt_page_url( 'contact' ) ); ?>#quote"><?php rmt_te( 'أريد نتيجة مثلها' ); ?></a>
					</div>
				</article>

				<?php if ( $total > 1 ) : ?>
					<div class="ba-thumbs" role="group" aria-label="<?php echo esc_attr( rmt_t( 'المشاريع' ) ); ?>">
						<?php foreach ( $items as $i => $p ) : ?>
							<button type="button" class="ba-thumb" data-ba-i="<?php echo (int) $i; ?>" aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( $p['title'] ); ?>">
								<img src="<?php echo esc_url( $p['after'] ); ?>" alt="" loading="lazy" decoding="async">
								<span class="ba-thumb-bar"><i></i></span>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<script type="application/json" data-ba-data><?php
		echo wp_json_encode(
			array_map(
				function ( $p ) {
					return array(
						'title'    => $p['title'],
						'service'  => $p['service'],
						'place'    => $p['place'],
						'duration' => $p['duration'],
						'floors'   => $p['floors'],
						'area'     => $p['area'],
						'team'     => $p['team'],
						'before'   => $p['before'],
						'after'    => $p['after'],
					);
				},
				$items
			),
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
		);
		?></script>
		<script type="application/json" data-ba-i18n><?php echo wp_json_encode( array( 'before' => rmt_t( 'قبل' ), 'after' => rmt_t( 'بعد' ), 'beforeAlt' => rmt_t( 'قبل التنظيف' ), 'afterAlt' => rmt_t( 'بعد التنظيف' ) ), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ); ?></script>
	</div>
</section>
