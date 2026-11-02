<?php
/**
 * Clients page (/clients) – client logos by group + full portfolio.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
get_header();
$c      = rmt_get( 'clients' );
$groups = rmt_client_groups();
$items  = (array) $c['items'];
rmt_page_hero( 'clients', $c['title'], $c['intro'] );
?>

<section class="section">
	<div class="container">
		<?php foreach ( $groups as $k => $label ) : ?>
			<?php
			$list = array_filter( $items, function ( $x ) use ( $k ) { return $x['group'] === $k; } );
			if ( ! $list ) {
				continue;
			}
			?>
			<div class="client-group">
				<h2 class="group-title reveal"><span><?php echo esc_html( $label ); ?></span><em><?php echo count( $list ); ?></em></h2>
				<div class="clients-grid">
					<?php foreach ( $list as $x ) { echo '<div class="reveal">'; rmt_client_card( $x ); echo '</div>'; } ?>
					<?php if ( 'gov' === $k ) : ?>
						<div class="reveal"><div class="client client-more"><span class="c-mono">+</span><span class="c-name"><?php rmt_te( 'والعديد من الجهات الحكومية الأخرى في المملكة' ); ?></span></div></div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>

		<?php if ( $c['outside'] ) : ?>
			<div class="outside reveal">
				<span class="card-ico"><?php echo rmt_icon( 'pin', 26 ); // phpcs:ignore ?></span>
				<div><h2><?php rmt_te( 'خارج المملكة' ); ?></h2><p><?php echo esc_html( $c['outside'] ); ?></p></div>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="section sand" id="work">
	<div class="container">
		<?php rmt_section_head( rmt_get( 'projects', 'title' ), rmt_get( 'projects', 'intro' ), 'h2', rmt_t( 'معرض المشاريع' ) ); ?>
		<?php rmt_gallery(); ?>
	</div>
</section>

<?php
get_footer();
