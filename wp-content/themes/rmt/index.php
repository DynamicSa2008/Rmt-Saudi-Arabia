<?php
/**
 * Fallback template (blog / archives).
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
get_header();
rmt_page_hero( '', is_home() ? rmt_t( 'المدونة' ) : wp_strip_all_tags( get_the_archive_title() ) );
?>
<section class="section"><div class="container">
	<?php if ( have_posts() ) : ?>
		<div class="cards">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article class="card reveal">
				<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'rmt-card', array( 'class' => 'card-img', 'loading' => 'lazy' ) ); } ?>
				<h2 class="h3"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
				<a class="card-link" href="<?php the_permalink(); ?>"><?php rmt_te( 'اقرأ المزيد' ); ?> <?php echo rmt_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
			</article>
		<?php endwhile; ?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p class="center"><?php rmt_te( 'لا يوجد محتوى بعد.' ); ?></p>
	<?php endif; ?>
</div></section>
<?php
get_footer();
