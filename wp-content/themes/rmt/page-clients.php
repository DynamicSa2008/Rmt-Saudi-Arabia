<?php
/**
 * Clients page (/clients) – stats band, filterable trust wall with sector
 * icons and tilt/shine cards, two-way logo marquee, presence cards,
 * before/after gallery and full portfolio.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
get_header();
$c      = rmt_get( 'clients' );
$groups = rmt_client_groups();
$items  = (array) $c['items'];
$icons  = array( 'gov' => 'shield', 'corp' => 'building', 'hotel' => 'star' );
$count  = array();
foreach ( $groups as $k => $l ) {
	$count[ $k ] = count( array_filter( $items, function ( $x ) use ( $k ) { return $x['group'] === $k; } ) );
}
rmt_page_hero( 'clients', $c['title'], $c['intro'] );
?>

<section class="cl-stats-wrap">
	<div class="container">
		<ul class="stats cl-stats reveal" aria-label="<?php echo esc_attr( rmt_t( 'أرقامنا' ) ); ?>">
			<li><strong><span data-count="<?php echo (int) count( $items ); ?>"><?php echo (int) count( $items ); ?></span>+</strong><span><?php rmt_te( 'عميل وشريك نجاح' ); ?></span></li>
			<li><strong><span data-count="<?php echo (int) $count['gov']; ?>"><?php echo (int) $count['gov']; ?></span>+</strong><span><?php rmt_te( 'جهات حكومية' ); ?></span></li>
			<li><strong><span data-count="<?php echo (int) $count['hotel']; ?>"><?php echo (int) $count['hotel']; ?></span></strong><span><?php rmt_te( 'فنادق ومستشفيات' ); ?></span></li>
			<li><strong><span data-count="<?php echo esc_attr( max( 1, (int) wp_date( 'Y' ) - (int) rmt_get( 'general', 'founded' ) ) ); ?>"><?php echo esc_html( max( 1, (int) wp_date( 'Y' ) - (int) rmt_get( 'general', 'founded' ) ) ); ?></span>+</strong><span><?php rmt_te( 'عامًا من الثقة' ); ?></span></li>
		</ul>
	</div>
</section>

<section class="section cl-wall">
	<div class="container">
		<?php rmt_section_head( rmt_t( 'جدار الثقة' ), rmt_t( 'جهات حكومية وشركات وفنادق ومستشفيات اختارتنا لأعمال النظافة والواجهات.' ), 'h2', rmt_t( 'شركاء النجاح' ) ); ?>

		<div class="filters reveal" role="tablist" aria-label="<?php echo esc_attr( rmt_t( 'تصنيف العملاء' ) ); ?>">
			<button class="chip is-active" data-filter="all" role="tab" aria-selected="true"><?php rmt_te( 'الكل' ); ?> <em><?php echo count( $items ); ?></em></button>
			<?php foreach ( $groups as $k => $label ) : ?>
				<?php if ( $count[ $k ] ) : ?>
					<button class="chip" data-filter="<?php echo esc_attr( $k ); ?>" role="tab" aria-selected="false"><?php echo rmt_icon( $icons[ $k ], 16 ); // phpcs:ignore ?><?php echo esc_html( $label ); ?> <em><?php echo (int) $count[ $k ]; ?></em></button>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<div class="cl-grid" data-flip>
			<?php foreach ( $items as $i => $x ) : ?>
				<div class="cl-item reveal" data-type="<?php echo esc_attr( $x['group'] ); ?>" style="--d:<?php echo (int) ( $i % 5 ) * 60; ?>ms">
					<div class="cl-card" data-tilt>
						<span class="cl-sector" title="<?php echo esc_attr( isset( $groups[ $x['group'] ] ) ? $groups[ $x['group'] ] : '' ); ?>"><?php echo rmt_icon( isset( $icons[ $x['group'] ] ) ? $icons[ $x['group'] ] : 'star', 15 ); // phpcs:ignore ?></span>
						<?php rmt_client_card( $x ); ?>
						<span class="cl-shine" aria-hidden="true"></span>
					</div>
				</div>
			<?php endforeach; ?>
			<div class="cl-item reveal" data-type="gov">
				<div class="client client-more"><span class="c-mono">+</span><span class="c-name"><?php rmt_te( 'والعديد من الجهات الحكومية الأخرى في المملكة' ); ?></span></div>
			</div>
		</div>
	</div>
</section>

<?php if ( count( $items ) > 3 ) : ?>
<section class="cl-marquee-sec" aria-label="<?php echo esc_attr( rmt_t( 'شعارات العملاء' ) ); ?>">
	<?php
	$half = (int) ceil( count( $items ) / 2 );
	$rows = array( array_slice( $items, 0, $half ), array_slice( $items, $half ) );
	foreach ( $rows as $r => $row ) :
		if ( ! $row ) {
			continue;
		}
		?>
		<div class="marquee cl-marquee<?php echo $r ? ' reverse' : ''; ?>">
			<div class="marquee-track">
				<?php for ( $loop = 0; $loop < 4; $loop++ ) : ?>
					<div class="marquee-group"<?php echo $loop ? ' aria-hidden="true"' : ''; ?>>
						<?php foreach ( $row as $x ) { rmt_client_card( $x ); } ?>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	<?php endforeach; ?>
</section>
<?php endif; ?>

<section class="section cl-presence">
	<div class="container">
		<?php rmt_section_head( rmt_t( 'أين عملنا' ), rmt_t( 'من مقرنا في الرياض إلى المنطقة الشرقية ومملكة البحرين.' ), 'h2', rmt_t( 'حضورنا' ) ); ?>
		<div class="cl-places">
			<article class="cl-place reveal">
				<span class="cl-ping" aria-hidden="true"></span>
				<span class="cl-place-tag"><?php rmt_te( 'المقر الرئيسي' ); ?></span>
				<h3><?php rmt_te( 'الرياض' ); ?></h3>
				<p><?php rmt_te( 'أمانة منطقة الرياض، أبراج ومجمعات تجارية، فنادق ومستشفيات وبنوك.' ); ?></p>
			</article>
			<article class="cl-place reveal" style="--d:120ms">
				<span class="cl-ping" aria-hidden="true"></span>
				<span class="cl-place-tag"><?php rmt_te( 'المنطقة الشرقية' ); ?></span>
				<h3><?php rmt_te( 'الدمام' ); ?></h3>
				<p><?php rmt_te( 'عقود نظافة لأمانة منطقة الدمام.' ); ?></p>
			</article>
			<?php if ( $c['outside'] ) : ?>
				<article class="cl-place reveal" style="--d:240ms">
					<span class="cl-ping" aria-hidden="true"></span>
					<span class="cl-place-tag"><?php rmt_te( 'خارج المملكة' ); ?></span>
					<h3><?php rmt_te( 'البحرين' ); ?></h3>
					<p><?php echo esc_html( $c['outside'] ); ?></p>
				</article>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/before-after' ); ?>

<section class="section sand" id="work">
	<div class="container">
		<?php rmt_section_head( rmt_get( 'projects', 'title' ), rmt_get( 'projects', 'intro' ), 'h2', rmt_t( 'معرض المشاريع' ) ); ?>
		<?php rmt_gallery(); ?>
	</div>
</section>

<?php
get_footer();
