<?php
/**
 * Services page (/services).
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
get_header();
$sv    = rmt_get( 'services' );
$cats  = rmt_service_cats();
$items = (array) $sv['items'];
rmt_page_hero( 'services', $sv['title'], $sv['intro'] );
?>

<section class="section">
	<div class="container">
		<p class="cats-note reveal"><?php echo esc_html( $sv['cats_note'] ); ?></p>

		<div class="filters reveal" role="tablist" aria-label="فئات الخدمات">
			<button class="chip is-active" data-filter="all" role="tab" aria-selected="true">جميع الخدمات <em><?php echo count( $items ); ?></em></button>
			<?php foreach ( $cats as $k => $label ) : ?>
				<?php
				$count = count( array_filter( $items, function ( $s ) use ( $k ) { return $s['cat'] === $k; } ) );
				if ( ! $count ) {
					continue;
				}
				?>
				<button class="chip" data-filter="<?php echo esc_attr( $k ); ?>" role="tab" aria-selected="false"><?php echo esc_html( $label ); ?> <em><?php echo (int) $count; ?></em></button>
			<?php endforeach; ?>
		</div>

		<nav class="svc-index reveal" aria-label="فهرس الخدمات">
			<?php foreach ( $items as $i => $s ) : ?>
				<a href="#<?php echo esc_attr( rmt_slug( $s['title'], $i ) ); ?>" data-type="<?php echo esc_attr( $s['cat'] ); ?>"><?php echo rmt_icon( $s['icon'], 18 ); // phpcs:ignore ?><?php echo esc_html( $s['title'] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<div class="svc-list" data-filter-target>
			<?php foreach ( $items as $i => $s ) : ?>
				<article class="svc reveal<?php echo $s['image'] ? ' has-img' : ''; ?>" id="<?php echo esc_attr( rmt_slug( $s['title'], $i ) ); ?>" data-type="<?php echo esc_attr( $s['cat'] ); ?>">
					<div class="svc-body">
						<div class="svc-top">
							<span class="card-ico"><?php echo rmt_icon( $s['icon'], 30 ); // phpcs:ignore ?></span>
							<span class="svc-cat"><?php echo esc_html( isset( $cats[ $s['cat'] ] ) ? $cats[ $s['cat'] ] : '' ); ?></span>
						</div>
						<h2><?php echo esc_html( $s['title'] ); ?></h2>
						<p><?php echo esc_html( $s['text'] ); ?></p>
						<div class="svc-actions">
							<a class="btn btn-primary btn-sm" href="<?php echo esc_url( add_query_arg( 'service', rawurlencode( $s['title'] ), rmt_page_url( 'contact' ) ) ); ?>#quote">اطلب عرض سعر لهذه الخدمة</a>
							<a class="btn btn-wa btn-sm" href="<?php echo esc_url( rmt_whatsapp_url( 'السلام عليكم، أرغب في عرض سعر لخدمة: ' . $s['title'] ) ); ?>" target="_blank" rel="noopener" data-track="whatsapp"><?php echo rmt_icon( 'whatsapp', 16 ); // phpcs:ignore ?>استفسار واتساب</a>
						</div>
					</div>
					<?php if ( $s['image'] ) : ?>
						<div class="svc-media"><?php echo rmt_img( $s['image'], $s['title'] . ' – ' . rmt_get( 'general', 'short_name' ), '', false, '(max-width: 900px) 100vw, 40vw' ); // phpcs:ignore ?></div>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
get_footer();
