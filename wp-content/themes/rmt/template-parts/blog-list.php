<?php
/**
 * Blog listing (posts page, categories, tags, search).
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

if ( is_category() ) {
	$title = single_cat_title( '', false );
	$lead  = wp_strip_all_tags( category_description() ) ? wp_strip_all_tags( category_description() ) : 'مقالات ' . $title . ' من فريق رفال المدينة.';
} elseif ( is_tag() ) {
	$title = 'وسم: ' . single_tag_title( '', false );
	$lead  = 'كل المقالات المرتبطة بهذا الموضوع.';
} elseif ( is_search() ) {
	$title = 'نتائج البحث عن: ' . get_search_query();
	$lead  = sprintf( 'وجدنا %d مقال.', (int) $GLOBALS['wp_query']->found_posts );
} else {
	$title = 'المدونة';
	$lead  = 'نصائح وإرشادات من خبرة أكثر من 20 عامًا في تنظيف الواجهات والأبراج وخدمات النظافة والصيانة.';
}
rmt_page_hero( 'blog', $title, $lead );

$paged    = max( 1, (int) get_query_var( 'paged' ) );
$featured = ( is_home() && 1 === $paged && have_posts() );
?>
<section class="section blog-sec">
	<div class="container">
		<?php rmt_blog_toolbar(); ?>

		<?php if ( have_posts() ) : ?>
			<?php
			if ( $featured ) :
				the_post();
				$p   = get_post();
				$cat = get_the_category();
				?>
				<article class="post-feature reveal">
					<a class="post-feature-media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
						<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => esc_attr( get_the_title() ), 'sizes' => '(max-width: 900px) 100vw, 55vw' ) ); } ?>
						<span class="post-badge">أحدث مقال</span>
					</a>
					<div class="post-feature-body">
						<div class="post-meta">
							<?php if ( $cat ) : ?><a class="post-cat" href="<?php echo esc_url( get_category_link( $cat[0] ) ); ?>"><?php echo esc_html( $cat[0]->name ); ?></a><?php endif; ?>
							<span><?php echo rmt_icon( 'clock', 14 ); // phpcs:ignore ?><?php echo esc_html( rmt_reading_label() ); ?></span>
						</div>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						<div class="post-foot">
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( rmt_ar_date() ); ?></time>
							<a class="btn btn-primary btn-sm" href="<?php the_permalink(); ?>">اقرأ المقال</a>
						</div>
					</div>
				</article>
			<?php endif; ?>

			<div class="post-grid">
				<?php
				$i = 0;
				while ( have_posts() ) :
					the_post();
					rmt_post_card( get_post(), '', $i++ < 1 && ! $featured );
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 1,
					'prev_text'          => 'السابق',
					'next_text'          => 'التالي',
					'screen_reader_text' => 'تصفح الصفحات',
					'class'              => 'blog-pages',
				)
			);
			?>
		<?php else : ?>
			<div class="blog-empty reveal">
				<span class="card-ico"><?php echo rmt_icon( 'contract', 28 ); // phpcs:ignore ?></span>
				<h2><?php echo is_search() ? 'لا توجد نتائج مطابقة' : 'لا توجد مقالات بعد'; ?></h2>
				<p><?php echo is_search() ? 'جرّب كلمات بحث أخرى أو تصفّح التصنيفات بالأعلى.' : 'نعمل على نشر مقالات مفيدة قريبًا.'; ?></p>
				<a class="btn btn-outline" href="<?php echo esc_url( rmt_page_url( 'blog' ) ); ?>">كل المقالات</a>
			</div>
		<?php endif; ?>
	</div>
</section>
