<?php
/**
 * Generic page (also used by the legal pages).
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	$legal = function_exists( 'rmt_legal_key' ) ? rmt_legal_key() : '';
	rmt_page_hero( '', get_the_title(), $legal ? rmt_get( 'general', 'company_name' ) : '' );
	?>
	<section class="section">
		<div class="container narrow">
			<?php if ( $legal ) : ?>
				<p class="legal-meta"><?php echo rmt_icon( 'clock', 16 ); // phpcs:ignore ?><?php rmt_te( 'آخر تحديث:' ); ?> <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date( 'j F Y' ) ); ?></time></p>
			<?php endif; ?>
			<article class="prose<?php echo $legal ? ' legal' : ''; ?>"><?php the_content(); ?></article>
			<?php if ( $legal ) : ?>
				<nav class="legal-nav" aria-label="<?php echo esc_attr( rmt_t( 'الصفحات القانونية' ) ); ?>"><?php rmt_legal_links(); ?></nav>
			<?php endif; ?>
		</div>
	</section>
	<?php
endwhile;
get_footer();
