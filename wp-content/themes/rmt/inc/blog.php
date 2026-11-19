<?php
/**
 * Arabic blog: page setup, categories, reading time, automatic table of
 * contents, related posts, share links and starter articles.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

/**
 * Date with Arabic month names regardless of the WordPress site language.
 *
 * @param string           $which 'date' or 'modified'.
 * @param int|WP_Post|null $post  Post.
 * @return string e.g. "19 نوفمبر 2026"
 */
function rmt_ar_date( $which = 'date', $post = null ) {
	$ts = 'modified' === $which ? get_post_modified_time( 'U', true, $post ) : get_post_time( 'U', true, $post );
	if ( function_exists( 'rmt_is_en' ) && rmt_is_en() ) {
		return wp_date( 'j F Y', $ts );
	}
	$m = array( 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر' );
	return wp_date( 'j', $ts ) . ' ' . $m[ (int) wp_date( 'n', $ts ) - 1 ] . ' ' . wp_date( 'Y', $ts );
}

/** Blog categories: slug => name. */
function rmt_blog_cats() {
	return array(
		'facades'     => 'الواجهات والأبراج',
		'cleaning'    => 'نصائح النظافة',
		'kitchens'    => 'المطاعم والفنادق',
		'maintenance' => 'الصيانة وجودة الهواء',
	);
}

/** Create the blog page, set it as the posts page, create categories. */
function rmt_install_blog() {
	$page = get_page_by_path( 'blog' );
	if ( ! $page ) {
		$id = wp_insert_post(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_name'   => 'blog',
				'post_title'  => 'المدونة',
			)
		);
	} else {
		$id = $page->ID;
	}
	update_option( 'page_for_posts', $id );
	update_option( 'posts_per_page', 9 );
	update_option( 'default_comment_status', 'closed' );

	foreach ( rmt_blog_cats() as $slug => $name ) {
		if ( ! term_exists( $slug, 'category' ) ) {
			wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
		}
	}
	// Rename WordPress' default "Uncategorized".
	$un = get_term_by( 'slug', 'uncategorized', 'category' );
	if ( $un ) {
		wp_update_term( $un->term_id, 'category', array( 'name' => 'عام', 'slug' => 'general' ) );
	}

	if ( ! get_option( 'rmt_blog_seeded' ) ) {
		rmt_seed_articles();
		update_option( 'rmt_blog_seeded', 1 );
	}
}

/** Remove WordPress' sample "Hello world!" post on fresh installs. */
function rmt_remove_hello_world() {
	$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
	if ( $hello && false !== strpos( $hello->post_content, 'wp:paragraph' ) && strlen( $hello->post_content ) < 400 ) {
		wp_delete_post( $hello->ID, true );
	}
}

/** Is this request part of the blog (index, single post, category, tag, search)? */
function rmt_is_blog() {
	return is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_search() || is_author() || is_date();
}

/**
 * Estimated reading time in minutes (Arabic ≈ 180 words/min).
 *
 * @param int|WP_Post|null $post Post.
 * @return int
 */
function rmt_reading_time( $post = null ) {
	$post  = get_post( $post );
	$words = count( preg_split( '/\s+/u', trim( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) ), -1, PREG_SPLIT_NO_EMPTY ) );
	return max( 1, (int) ceil( $words / 180 ) );
}

/** "5 دقائق قراءة" with correct Arabic plural. */
function rmt_reading_label( $post = null ) {
	$m = rmt_reading_time( $post );
	if ( 1 === $m ) {
		return 'دقيقة قراءة';
	}
	if ( 2 === $m ) {
		return 'دقيقتان قراءة';
	}
	return $m . ( $m <= 10 ? ' دقائق' : ' دقيقة' ) . ' قراءة';
}

/**
 * Add ids to H2/H3 inside single posts and collect them for the TOC.
 */
add_filter(
	'the_content',
	function ( $html ) {
		if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
			return $html;
		}
		$GLOBALS['rmt_toc'] = array();
		$n                  = 0;
		return preg_replace_callback(
			'#<h([23])([^>]*)>(.*?)</h\1>#su',
			function ( $m ) use ( &$n ) {
				$n++;
				$text = trim( wp_strip_all_tags( $m[3] ) );
				if ( preg_match( '/id="([^"]+)"/', $m[2], $idm ) ) {
					$id = $idm[1];
					$attrs = $m[2];
				} else {
					$id    = 'section-' . $n;
					$attrs = $m[2] . ' id="' . $id . '"';
				}
				$GLOBALS['rmt_toc'][] = array( (int) $m[1], $id, $text );
				return '<h' . $m[1] . $attrs . '>' . $m[3] . '</h' . $m[1] . '>';
			},
			$html
		);
	},
	9
);

/**
 * Related posts: same category first, then latest.
 *
 * @param int $id    Post ID.
 * @param int $count Number.
 * @return WP_Post[]
 */
function rmt_related_posts( $id, $count = 3 ) {
	$cats = wp_get_post_categories( $id );
	$rel  = $cats ? get_posts(
		array(
			'category__in'        => $cats,
			'post__not_in'        => array( $id ),
			'posts_per_page'      => $count,
			'ignore_sticky_posts' => true,
		)
	) : array();
	if ( count( $rel ) < $count ) {
		$more = get_posts(
			array(
				'post__not_in'   => array_merge( array( $id ), wp_list_pluck( $rel, 'ID' ) ),
				'posts_per_page' => $count - count( $rel ),
			)
		);
		$rel  = array_merge( $rel, $more );
	}
	return $rel;
}

/** Share links for the current post. */
function rmt_share_links() {
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( get_the_title() );
	return array(
		'whatsapp' => array( 'واتساب', 'https://wa.me/?text=' . $title . '%20' . $url ),
		'x'        => array( 'X', 'https://twitter.com/intent/tweet?text=' . $title . '&url=' . $url ),
		'linkedin' => array( 'لينكدإن', 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url ),
	);
}

/**
 * Post card used in listings.
 *
 * @param WP_Post $p     Post.
 * @param string  $class Extra class.
 * @param bool    $eager Eager image.
 */
function rmt_post_card( $p, $class = '', $eager = false ) {
	$cat = get_the_category( $p->ID );
	$cat = $cat ? $cat[0] : null;
	?>
	<article class="post-card reveal <?php echo esc_attr( $class ); ?>">
		<a class="post-thumb" href="<?php echo esc_url( get_permalink( $p ) ); ?>" tabindex="-1" aria-hidden="true">
			<?php if ( has_post_thumbnail( $p ) ) : ?>
				<?php echo get_the_post_thumbnail( $p, 'large', array( 'loading' => $eager ? 'eager' : 'lazy', 'alt' => esc_attr( get_the_title( $p ) ), 'sizes' => '(max-width: 640px) 100vw, (max-width: 1100px) 50vw, 33vw' ) ); ?>
			<?php else : ?>
				<span class="post-ph"><?php echo rmt_icon( 'building', 42 ); // phpcs:ignore ?></span>
			<?php endif; ?>
		</a>
		<div class="post-body">
			<div class="post-meta">
				<?php if ( $cat ) : ?>
					<a class="post-cat" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
				<?php endif; ?>
				<span><?php echo rmt_icon( 'clock', 14 ); // phpcs:ignore ?><?php echo esc_html( rmt_reading_label( $p ) ); ?></span>
			</div>
			<h3 class="post-title"><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a></h3>
			<p class="post-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $p ), 24, '…' ) ); ?></p>
			<div class="post-foot">
				<time datetime="<?php echo esc_attr( get_the_date( 'c', $p ) ); ?>"><?php echo esc_html( rmt_ar_date( 'date', $p ) ); ?></time>
				<a class="card-link" href="<?php echo esc_url( get_permalink( $p ) ); ?>">اقرأ المقال <?php echo rmt_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
			</div>
		</div>
	</article>
	<?php
}

/** Blog listing header: category chips + search. */
function rmt_blog_toolbar() {
	$current = is_category() ? get_queried_object_id() : 0;
	$cats    = get_categories( array( 'hide_empty' => true ) );
	?>
	<div class="blog-bar reveal">
		<nav class="blog-cats" aria-label="تصنيفات المدونة">
			<a class="chip<?php echo is_home() ? ' is-active' : ''; ?>" href="<?php echo esc_url( rmt_page_url( 'blog' ) ); ?>">كل المقالات</a>
			<?php foreach ( $cats as $c ) : ?>
				<a class="chip<?php echo $current === $c->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $c ) ); ?>"><?php echo esc_html( $c->name ); ?> <em><?php echo (int) $c->count; ?></em></a>
			<?php endforeach; ?>
		</nav>
		<form class="blog-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label for="blog-s" class="sr-only">ابحث في المدونة</label>
			<input type="search" id="blog-s" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="ابحث في المقالات…">
			<button type="submit" aria-label="بحث"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg></button>
		</form>
	</div>
	<?php
}

/** Search only searches blog posts. */
add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( ! is_admin() && $q->is_main_query() && $q->is_search() ) {
			$q->set( 'post_type', 'post' );
		}
	}
);

/** Shorter auto excerpts. */
add_filter( 'excerpt_length', function () { return 30; } );
add_filter( 'excerpt_more', function () { return '…'; } );

/** Keep comments off on the blog (business site). */
add_filter( 'comments_open', function ( $open, $id ) { return 'post' === get_post_type( $id ) ? false : $open; }, 10, 2 );

/**
 * Copy a bundled theme image into the media library (for starter articles).
 *
 * @param string $file  File name inside assets/media.
 * @param string $title Attachment title / alt.
 * @return int Attachment ID or 0.
 */
function rmt_import_theme_image( $file, $title ) {
	$src = get_template_directory() . '/assets/media/' . $file;
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	$up = wp_upload_bits( 'rmt-blog-' . $file, null, file_get_contents( $src ) ); // phpcs:ignore
	if ( ! empty( $up['error'] ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $up['file'] )['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$up['file']
	);
	if ( $id && ! is_wp_error( $id ) ) {
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $up['file'] ) );
		update_post_meta( $id, '_wp_attachment_image_alt', $title );
		return $id;
	}
	return 0;
}

/** Insert the starter articles (Arabic). */
function rmt_seed_articles() {
	$tel  = rmt_phone_display();
	$cta  = '<p><strong>هل تحتاج إلى معاينة لمبناك؟</strong> تواصل مع فريق رفال المدينة على الجوال وواتساب <span dir="ltr">' . esc_html( $tel ) . '</span> أو اطلب عرض سعر من <a href="' . esc_url( rmt_page_url( 'contact', 'ar' ) ) . '">صفحة التواصل</a>.</p>';
	$list = require __DIR__ . '/blog-articles.php';
	$i    = 0;
	foreach ( $list as $a ) {
		if ( get_page_by_path( $a['slug'], OBJECT, 'post' ) ) {
			continue;
		}
		$cat = get_term_by( 'slug', $a['cat'], 'category' );
		$id  = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_name'     => $a['slug'],
				'post_title'    => $a['title'],
				'post_excerpt'  => $a['excerpt'],
				'post_content'  => $a['content'] . "\n" . $cta,
				'post_category' => $cat ? array( $cat->term_id ) : array(),
				'tags_input'    => $a['tags'],
				'post_date'     => gmdate( 'Y-m-d H:i:s', time() - ( 3 - $i ) * 6 * DAY_IN_SECONDS ),
				'comment_status'=> 'closed',
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			if ( ! empty( $a['seo_title'] ) ) {
				update_post_meta( $id, '_rmt_seo_title', $a['seo_title'] );
			}
			$img = rmt_import_theme_image( $a['image'], $a['title'] );
			if ( $img ) {
				set_post_thumbnail( $id, $img );
			}
		}
		$i++;
	}
}

/* ---------- Per-article SEO title & description (meta box) ---------- */
add_action(
	'add_meta_boxes',
	function () {
		add_meta_box( 'rmt_seo', 'السيو – كيف يظهر المقال في جوجل', 'rmt_seo_metabox', array( 'post', 'page' ), 'normal', 'high' );
	}
);

/** Render the SEO meta box with a live Google preview and counters. */
function rmt_seo_metabox( $post ) {
	wp_nonce_field( 'rmt_seo_meta', 'rmt_seo_nonce' );
	$t = get_post_meta( $post->ID, '_rmt_seo_title', true );
	$d = get_post_meta( $post->ID, '_rmt_seo_desc', true );
	?>
	<div class="rmt-seo-box" dir="rtl">
		<p><label for="rmt_seo_title"><strong>عنوان السيو</strong> (اختياري – يُفضّل 50–60 حرفًا)</label><br>
		<input type="text" id="rmt_seo_title" name="rmt_seo_title" value="<?php echo esc_attr( $t ); ?>" style="width:100%" placeholder="<?php echo esc_attr( get_the_title( $post ) ); ?>"></p>
		<p><label for="rmt_seo_desc"><strong>وصف السيو</strong> (اختياري – يُفضّل 120–160 حرفًا، وإلا يُستخدم المقتطف)</label><br>
		<textarea id="rmt_seo_desc" name="rmt_seo_desc" rows="3" style="width:100%"><?php echo esc_textarea( $d ); ?></textarea></p>
		<div class="rmt-serp" style="border:1px solid #e3e7ee;border-radius:10px;padding:12px 14px;max-width:620px;font-family:Arial,sans-serif">
			<div style="color:#1a0dab;font-size:18px;line-height:1.3" data-serp-t></div>
			<div style="color:#006621;font-size:13px;direction:ltr;text-align:right"><?php echo esc_html( get_permalink( $post ) ); ?></div>
			<div style="color:#4d5156;font-size:13px" data-serp-d></div>
		</div>
		<p class="description" data-serp-count></p>
	</div>
	<script>
	(function () {
		var t = document.getElementById('rmt_seo_title'), d = document.getElementById('rmt_seo_desc');
		var pt = document.querySelector('[data-serp-t]'), pd = document.querySelector('[data-serp-d]'), c = document.querySelector('[data-serp-count]');
		var fallbackD = <?php echo wp_json_encode( wp_trim_words( wp_strip_all_tags( $post->post_excerpt ? $post->post_excerpt : $post->post_content ), 28, '…' ) ); ?>;
		function up() {
			var tv = t.value || t.placeholder, dv = d.value || fallbackD;
			pt.textContent = tv.length > 62 ? tv.slice(0, 60) + '…' : tv;
			pd.textContent = dv.length > 162 ? dv.slice(0, 160) + '…' : dv;
			c.textContent = 'العنوان: ' + tv.length + ' حرف' + (tv.length > 60 ? ' ⚠ طويل' : ' ✔') + ' — الوصف: ' + dv.length + ' حرف' + (dv.length > 160 ? ' ⚠ طويل' : (dv.length < 110 ? ' ⚠ قصير' : ' ✔'));
		}
		t.addEventListener('input', up); d.addEventListener('input', up); up();
	})();
	</script>
	<?php
}

add_action(
	'save_post',
	function ( $id ) {
		if ( ! isset( $_POST['rmt_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rmt_seo_nonce'] ) ), 'rmt_seo_meta' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $id ) ) {
			return;
		}
		update_post_meta( $id, '_rmt_seo_title', sanitize_text_field( wp_unslash( isset( $_POST['rmt_seo_title'] ) ? $_POST['rmt_seo_title'] : '' ) ) );
		update_post_meta( $id, '_rmt_seo_desc', sanitize_textarea_field( wp_unslash( isset( $_POST['rmt_seo_desc'] ) ? $_POST['rmt_seo_desc'] : '' ) ) );
	}
);
