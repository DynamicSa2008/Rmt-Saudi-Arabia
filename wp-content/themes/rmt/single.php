<?php
/**
 * Single blog article.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	$cat     = get_the_category();
	$cat     = $cat ? $cat[0] : null;
	$content = apply_filters( 'the_content', get_the_content() ); // Builds the TOC.
	$toc     = isset( $GLOBALS['rmt_toc'] ) ? $GLOBALS['rmt_toc'] : array();
	$g       = rmt_get( 'general' );
	?>
	<div class="read-progress" aria-hidden="true"><i data-progress></i></div>

	<header class="post-hero">
		<div class="page-hero-bg" aria-hidden="true"></div>
		<div class="container narrow-wide">
			<nav class="crumbs" aria-label="مسار التنقل">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">الرئيسية</a><span aria-hidden="true">›</span>
				<a href="<?php echo esc_url( rmt_page_url( 'blog' ) ); ?>">المدونة</a>
				<?php if ( $cat ) : ?><span aria-hidden="true">›</span><a href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a><?php endif; ?>
			</nav>
			<?php if ( $cat ) : ?><a class="post-cat light" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a><?php endif; ?>
			<h1 class="post-h1"><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?><p class="post-lead"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
			<div class="post-info">
				<span class="post-author"><img src="<?php echo esc_url( rmt_logo_url( 'small' ) ); ?>" alt="" width="34" height="34"><?php echo esc_html( 'فريق ' . $g['short_name'] ); ?></span>
				<span><?php echo rmt_icon( 'clock', 15 ); // phpcs:ignore ?><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( rmt_ar_date() ); ?></time></span>
				<span><?php echo rmt_icon( 'contract', 15 ); // phpcs:ignore ?><?php echo esc_html( rmt_reading_label() ); ?></span>
				<?php if ( get_the_modified_date( 'Ymd' ) > get_the_date( 'Ymd' ) ) : ?>
					<span class="post-upd">حُدّث في <?php echo esc_html( rmt_ar_date( 'modified' ) ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="container narrow-wide">
			<figure class="post-cover">
				<?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => esc_attr( get_the_title() ), 'sizes' => '(max-width: 1000px) 100vw, 1000px' ) ); ?>
			</figure>
		</div>
	<?php endif; ?>

	<section class="section post-sec">
		<div class="container post-layout<?php echo $toc ? '' : ' no-toc'; ?>">
			<?php if ( count( $toc ) > 1 ) : ?>
				<aside class="post-aside">
					<details class="toc" open data-toc>
						<summary>محتويات المقال</summary>
						<ol>
							<?php foreach ( $toc as $t ) : ?>
								<li class="lvl-<?php echo (int) $t[0]; ?>"><a href="#<?php echo esc_attr( $t[1] ); ?>"><?php echo esc_html( $t[2] ); ?></a></li>
							<?php endforeach; ?>
						</ol>
					</details>
					<div class="aside-cta">
						<strong>تحتاج الخدمة لمبناك؟</strong>
						<p>معاينة وعرض سعر مناسب في الرياض وكل مناطق المملكة.</p>
						<a class="btn btn-primary btn-sm btn-block" href="<?php echo esc_url( rmt_page_url( 'contact' ) ); ?>#quote">اطلب عرض سعر</a>
						<a class="btn btn-wa btn-sm btn-block" href="<?php echo esc_url( rmt_whatsapp_url( 'السلام عليكم، قرأت مقال: ' . get_the_title() . ' وأرغب في الاستفسار.' ) ); ?>" target="_blank" rel="noopener" data-track="whatsapp"><?php echo rmt_icon( 'whatsapp', 16 ); // phpcs:ignore ?>واتساب</a>
					</div>
				</aside>
			<?php endif; ?>

			<div class="post-main">
				<article class="prose post-content">
					<?php echo $content; // phpcs:ignore -- already filtered by the_content. ?>
				</article>

				<?php $tags = get_the_tags(); ?>
				<?php if ( $tags ) : ?>
					<div class="post-tags">
						<?php foreach ( $tags as $t ) : ?>
							<a href="<?php echo esc_url( get_tag_link( $t ) ); ?>">#<?php echo esc_html( $t->name ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="post-share">
					<span>شارك المقال:</span>
					<?php foreach ( rmt_share_links() as $k => $s ) : ?>
						<a class="share-<?php echo esc_attr( $k ); ?>" href="<?php echo esc_url( $s[1] ); ?>" target="_blank" rel="noopener" aria-label="مشاركة عبر <?php echo esc_attr( $s[0] ); ?>"><?php echo rmt_icon( $k, 18 ); // phpcs:ignore ?></a>
					<?php endforeach; ?>
					<button type="button" class="share-copy" data-copy="<?php echo esc_url( get_permalink() ); ?>" aria-label="نسخ رابط المقال"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg><span>نسخ الرابط</span></button>
				</div>

				<div class="post-cta">
					<div>
						<h2>هل لديك مبنى يحتاج إلى هذه الخدمة؟</h2>
						<p>فريق رفال المدينة جاهز للمعاينة وتقديم عرض سعر مناسب لمشروعك.</p>
					</div>
					<?php rmt_cta_buttons( true ); ?>
				</div>

				<?php
				$prev = get_previous_post();
				$next = get_next_post();
				if ( $prev || $next ) :
					?>
					<nav class="post-nav" aria-label="مقالات أخرى">
						<?php if ( $prev ) : ?>
							<a class="pn-prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>"><small>المقال السابق</small><strong><?php echo esc_html( get_the_title( $prev ) ); ?></strong></a>
						<?php endif; ?>
						<?php if ( $next ) : ?>
							<a class="pn-next" href="<?php echo esc_url( get_permalink( $next ) ); ?>"><small>المقال التالي</small><strong><?php echo esc_html( get_the_title( $next ) ); ?></strong></a>
						<?php endif; ?>
					</nav>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php $rel = rmt_related_posts( get_the_ID(), 3 ); ?>
	<?php if ( $rel ) : ?>
		<section class="section sand related-sec">
			<div class="container">
				<?php rmt_section_head( 'مقالات ذات صلة', '', 'h2', 'تابع القراءة' ); ?>
				<div class="post-grid">
					<?php foreach ( $rel as $r ) { rmt_post_card( $r ); } ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;
get_footer();
