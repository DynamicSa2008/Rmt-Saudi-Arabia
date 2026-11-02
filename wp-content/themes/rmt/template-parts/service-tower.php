<?php
/**
 * "From rooftop to ground" – interactive building cross-section where every
 * part of the tower reveals the matching service. Pure SVG + CSS + tiny JS.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

// Hotspot position (percent of the illustration) for each service icon.
$spots = array(
	'fan'   => array( 37, 8 ),
	'glass' => array( 50, 24 ),
	'rope'  => array( 79.5, 34 ),
	'tower' => array( 62.5, 41.5 ),
	'paint' => array( 66, 56 ),
	'home'  => array( 34, 67 ),
	'sofa'  => array( 51.25, 76 ),
	'flame' => array( 21, 84 ),
	'bug'   => array( 82, 92 ),
);

$items = array();
foreach ( (array) rmt_get( 'services', 'items' ) as $i => $s ) {
	if ( '1' === $s['home'] ) {
		$s['index'] = $i;
		$items[]    = $s;
	}
}
if ( ! $items ) {
	return;
}
$total = count( $items );
?>
<section class="section tower-sec" data-tower>
	<div class="container">
		<?php rmt_section_head( rmt_get( 'home', 'services_title' ), rmt_get( 'home', 'services_text' ), 'h2', rmt_t( 'من القمة إلى القاعدة' ) ); ?>

		<div class="tower-wrap">
			<div class="tower-art reveal">
				<svg class="tower-svg" viewBox="0 0 400 520" role="img" aria-labelledby="tower-title">
					<title id="tower-title"><?php rmt_te( 'رسم توضيحي لمبنى يوضح خدماتنا من السطح حتى الأرض' ); ?></title>
					<defs>
						<linearGradient id="tw-glass" x1="0" y1="0" x2="1" y2="1">
							<stop offset="0" stop-color="#4c70b5"/><stop offset="1" stop-color="#1d3f72"/>
						</linearGradient>
						<linearGradient id="tw-shine" x1="0" y1="0" x2="1" y2="0">
							<stop offset="0" stop-color="#fff" stop-opacity="0"/><stop offset=".5" stop-color="#fff" stop-opacity=".55"/><stop offset="1" stop-color="#fff" stop-opacity="0"/>
						</linearGradient>
						<pattern id="tw-grid" width="22.5" height="22.5" patternUnits="userSpaceOnUse" x="110" y="70">
							<rect width="22.5" height="22.5" fill="none" stroke="#9fb6e0" stroke-opacity=".35" stroke-width="1"/>
						</pattern>
						<clipPath id="tw-glass-clip"><rect x="110" y="70" width="180" height="180"/></clipPath>
						<clipPath id="tw-cut"><circle cx="205" cy="395" r="23"/></clipPath>
					</defs>

					<!-- ground & backdrop -->
					<circle cx="200" cy="250" r="190" class="tw-sun"/>
					<rect x="20" y="470" width="360" height="22" rx="4" class="tw-ground"/>
					<path d="M330 470c-4-10 2-16 6-20 3 6 4 12-2 20zM344 470c0-8 5-12 9-14 0 6-2 10-5 14z" class="tw-plant"/>

					<!-- kitchen exhaust chimney -->
					<rect x="92" y="40" width="10" height="400" rx="3" class="tw-pipe"/>
					<g class="tw-smoke"><circle cx="97" cy="30" r="6"/><circle cx="104" cy="18" r="8"/><circle cx="96" cy="6" r="5"/></g>

					<!-- tower body -->
					<rect x="105" y="50" width="190" height="20" rx="3" class="tw-roof"/>
					<rect x="110" y="70" width="180" height="400" class="tw-body"/>
					<rect x="110" y="70" width="180" height="180" fill="url(#tw-glass)"/>
					<rect x="110" y="70" width="180" height="180" fill="url(#tw-grid)"/>
					<g clip-path="url(#tw-glass-clip)"><rect class="tw-sweep" x="-60" y="60" width="60" height="200" fill="url(#tw-shine)" transform="skewX(-18)"/></g>

					<!-- cracked pane -->
					<rect x="245" y="205" width="22" height="22" class="tw-pane"/>
					<path d="M256 205l-3 8 6 4-5 10M253 213l-8 3M259 217l8-2" class="tw-crack"/>

					<!-- painting band -->
					<rect x="110" y="250" width="180" height="80" class="tw-wall"/>
					<rect x="200" y="250" width="90" height="80" class="tw-paint"/>
					<rect x="234" y="296" width="44" height="6" rx="2" class="tw-cradle"/>
					<line x1="238" y1="250" x2="238" y2="296" class="tw-cable"/><line x1="274" y1="250" x2="274" y2="296" class="tw-cable"/>

					<!-- apartments -->
					<g class="tw-flats">
						<?php
						for ( $r = 0; $r < 4; $r++ ) {
							for ( $c = 0; $c < 6; $c++ ) {
								$x = 122 + $c * 28;
								$y = 338 + $r * 22;
								if ( $r >= 2 && $c >= 2 && $c <= 4 ) {
									continue; // space for the cut-away window.
								}
								printf( '<rect x="%d" y="%d" width="16" height="12" rx="1.5" class="%s"/>', (int) $x, (int) $y, ( ( $r + $c ) % 3 ) ? 'tw-win' : 'tw-win lit' );
							}
						}
						?>
					</g>
					<!-- cut-away window with a sofa -->
					<circle cx="205" cy="395" r="26" class="tw-cut-ring"/>
					<g clip-path="url(#tw-cut)">
						<rect x="178" y="370" width="54" height="50" class="tw-room"/>
						<path d="M187 400v-9a4 4 0 0 1 4-4h28a4 4 0 0 1 4 4v9" class="tw-sofa-back"/>
						<rect x="184" y="398" width="42" height="10" rx="3" class="tw-sofa"/>
						<path d="M188 408v4M222 408v4" class="tw-sofa-leg"/>
					</g>

					<!-- ground floor restaurant -->
					<rect x="110" y="430" width="180" height="40" class="tw-shop"/>
					<path d="M110 430h180l-6 10H116z" class="tw-awning"/>
					<path d="M128 430l-4 10M150 430l-3 10M172 430l-2 10M194 430l-1 10M216 430v10M238 430l1 10M260 430l2 10M280 430l3 10" class="tw-stripe"/>
					<rect x="190" y="446" width="22" height="24" class="tw-door"/>
					<rect x="102" y="434" width="14" height="10" class="tw-hood"/>

					<!-- roof: A/C units + davit + rope + technician -->
					<g class="tw-ac"><rect x="128" y="30" width="38" height="20" rx="3"/><circle cx="147" cy="40" r="6" class="tw-fan"/></g>
					<path d="M282 50V28h40" class="tw-davit"/>
					<line x1="320" y1="28" x2="320" y2="300" class="tw-rope"/>
					<g class="tw-climber">
						<circle cx="320" cy="152" r="7" class="tw-helmet"/>
						<path d="M312 160h16l-2 18h-12z" class="tw-suit"/>
						<path d="M314 178l-4 12M326 178l4 12M312 166l-10 6M328 166l-8 12" class="tw-limb"/>
						<rect x="296" y="170" width="8" height="10" rx="1" class="tw-bucket"/>
					</g>
				</svg>

				<?php foreach ( $items as $n => $s ) : ?>
					<?php
					if ( ! isset( $spots[ $s['icon'] ] ) ) {
						continue;
					}
					$p = $spots[ $s['icon'] ];
					?>
					<button type="button" class="tw-spot" style="--x:<?php echo esc_attr( $p[0] ); ?>%;--y:<?php echo esc_attr( $p[1] ); ?>%" data-go="<?php echo (int) $n; ?>" aria-label="<?php echo esc_attr( $s['title'] ); ?>">
						<span class="tw-spot-dot"></span>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="tower-panel reveal">
				<div class="tw-chips" role="tablist" aria-label="<?php echo esc_attr( rmt_t( 'خدماتنا' ) ); ?>">
					<?php foreach ( $items as $n => $s ) : ?>
						<button type="button" class="tw-chip<?php echo 0 === $n ? ' is-on' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $n ? 'true' : 'false'; ?>" aria-controls="tw-card-<?php echo (int) $n; ?>" data-go="<?php echo (int) $n; ?>" title="<?php echo esc_attr( $s['title'] ); ?>">
							<?php echo rmt_icon( $s['icon'], 22 ); // phpcs:ignore ?>
							<span class="sr-only"><?php echo esc_html( $s['title'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="tw-cards">
					<?php foreach ( $items as $n => $s ) : ?>
						<article class="tw-card<?php echo 0 === $n ? ' is-on' : ''; ?>" id="tw-card-<?php echo (int) $n; ?>" role="tabpanel" <?php echo 0 === $n ? '' : 'hidden'; ?>>
							<div class="tw-card-top">
								<span class="card-ico"><?php echo rmt_icon( $s['icon'], 30 ); // phpcs:ignore ?></span>
								<span class="tw-count"><b><?php echo esc_html( sprintf( '%02d', $n + 1 ) ); ?></b> / <?php echo esc_html( sprintf( '%02d', $total ) ); ?></span>
							</div>
							<h3><?php echo esc_html( $s['title'] ); ?></h3>
							<p><?php echo esc_html( $s['short'] ); ?></p>
							<div class="tw-actions">
								<a class="btn btn-primary btn-sm" href="<?php echo esc_url( add_query_arg( 'service', rawurlencode( $s['title'] ), rmt_page_url( 'contact' ) ) ); ?>#quote"><?php rmt_te( 'اطلب عرض سعر' ); ?></a>
								<a class="card-link" href="<?php echo esc_url( rmt_page_url( 'services' ) . '#' . rmt_slug( $s['title'], $s['index'] ) ); ?>"><?php rmt_te( 'التفاصيل' ); ?> <?php echo rmt_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<div class="tw-progress" aria-hidden="true"><i></i></div>
				<p class="tw-hint"><?php rmt_te( 'اضغط على أي نقطة في المبنى لتتعرّف على الخدمة المناسبة لها.' ); ?></p>
			</div>
		</div>

		<p class="center reveal"><a class="btn btn-outline" href="<?php echo esc_url( rmt_page_url( 'services' ) ); ?>"><?php rmt_te( 'عرض جميع الخدمات' ); ?> <?php echo rmt_icon( 'arrow', 18 ); // phpcs:ignore ?></a></p>
	</div>
</section>
