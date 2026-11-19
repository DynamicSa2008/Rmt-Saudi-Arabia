<?php
/**
 * Site health for the theme's pages: verifies that every core/legal page
 * exists, is published, sits under the right parent and has the right slug;
 * repairs automatically (admin visit or a 404 on a known /en/ URL) and shows
 * a diagnostics table in the control panel.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

/**
 * Expected pages: list of array( path, slug, parent_slug|'' , title ).
 *
 * @return array
 */
function rmt_expected_pages() {
	$list = array();
	foreach ( rmt_core_pages() as $slug => $title ) {
		$list[] = array( $slug, $slug, '', $title );
	}
	$list[] = array( 'blog', 'blog', '', 'المدونة' );
	$list[] = array( 'en', 'en', '', 'Home' );
	$en_titles = array( 'about' => 'About Us', 'services' => 'Our Services', 'clients' => 'Our Clients', 'contact' => 'Contact Us' );
	foreach ( $en_titles as $slug => $title ) {
		$list[] = array( 'en/' . $slug, $slug, 'en', $title );
	}
	foreach ( rmt_legal_pages() as $slug => $t ) {
		$list[] = array( $slug, $slug, '', $t[0] );
		$list[] = array( 'en/' . $slug, $slug, 'en', $t[1] );
	}
	return $list;
}

/**
 * Find a page by slug + parent in ANY status (incl. trash, whose slug gets "__trashed").
 *
 * @param string $slug   Slug.
 * @param int    $parent Parent ID.
 * @return WP_Post|null
 */
function rmt_find_page( $slug, $parent ) {
	$q = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future', 'trash' ),
			'post_parent'    => (int) $parent,
			'post_name__in'  => array( $slug, $slug . '__trashed' ),
			'posts_per_page' => 1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
	return $q ? $q[0] : null;
}

/**
 * Status of every expected page.
 *
 * @return array path => array( ok, msg, id, url )
 */
function rmt_pages_status() {
	$out = array();
	$ids = array( '' => 0 );
	foreach ( rmt_expected_pages() as $p ) {
		list( $path, $slug, $parent ) = $p;
		$pid = isset( $ids[ $parent ] ) ? $ids[ $parent ] : -1;
		if ( $pid < 0 ) {
			$out[ $path ] = array( false, 'الصفحة الأم غير موجودة', 0, '' );
			continue;
		}
		$page = rmt_find_page( $slug, $pid );
		if ( ! $page ) {
			$out[ $path ] = array( false, 'غير موجودة', 0, '' );
			$ids[ $path ] = -1;
			continue;
		}
		$ids[ $path ] = $page->ID;
		if ( 'publish' !== $page->post_status ) {
			$out[ $path ] = array( false, 'حالتها: ' . $page->post_status, $page->ID, '' );
			continue;
		}
		if ( $page->post_name !== $slug ) {
			$out[ $path ] = array( false, 'الرابط الحالي: ' . $page->post_name, $page->ID, get_permalink( $page ) );
			continue;
		}
		$out[ $path ] = array( true, 'سليمة', $page->ID, get_permalink( $page ) );
	}
	return $out;
}

/** Are all pages healthy? */
function rmt_pages_ok() {
	foreach ( rmt_pages_status() as $s ) {
		if ( ! $s[0] ) {
			return false;
		}
	}
	return true;
}

/**
 * Repair every page: restore from trash, publish, fix slug/parent, create missing.
 * Then ensure pretty permalinks and hard-flush rewrite rules.
 *
 * @return array Log lines.
 */
function rmt_repair_pages() {
	$log = array();
	$ids = array( '' => 0 );
	foreach ( rmt_expected_pages() as $p ) {
		list( $path, $slug, $parent, $title ) = $p;
		$pid  = isset( $ids[ $parent ] ) ? (int) $ids[ $parent ] : 0;
		$page = rmt_find_page( $slug, $pid );
		if ( $page && 'trash' === $page->post_status ) {
			wp_untrash_post( $page->ID );
			$page = get_post( $page->ID );
			$log[] = $path . ': استُعيدت من سلة المهملات';
		}
		if ( $page ) {
			$upd = array( 'ID' => $page->ID );
			if ( 'publish' !== $page->post_status ) {
				$upd['post_status'] = 'publish';
			}
			if ( $page->post_name !== $slug ) {
				$upd['post_name'] = $slug;
			}
			if ( count( $upd ) > 1 ) {
				wp_update_post( $upd );
				$log[] = $path . ': تم نشرها/تصحيح رابطها';
			}
			$id = $page->ID;
		} else {
			$content = in_array( $slug, array_keys( rmt_legal_pages() ), true ) ? rmt_legal_content( $slug, $parent ? 'en' : 'ar' ) : '';
			$id      = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $title,
					'post_parent'  => $pid,
					'post_content' => $content,
				)
			);
			$log[] = $path . ': أُنشئت';
		}
		// Another object may already own the slug (WordPress would rename ours to slug-2).
		$now = get_post( $id );
		if ( $now && $now->post_name !== $slug ) {
			$log[] = $path . ': ⚠ الرابط محجوز لعنصر آخر (' . $now->post_name . ')';
		}
		$ids[ $path ] = $id;
	}
	if ( ! empty( $ids['blog'] ) ) {
		update_option( 'page_for_posts', $ids['blog'] );
	}
	$home = isset( $ids['home'] ) ? $ids['home'] : 0;
	if ( $home ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
	}
	global $wp_rewrite;
	if ( false === strpos( (string) get_option( 'permalink_structure' ), '%postname%' ) ) {
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$log[] = 'الروابط الدائمة: /%postname%/';
	}
	if ( ! function_exists( 'save_mod_rewrite_rules' ) ) {
		require_once ABSPATH . 'wp-admin/includes/misc.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}
	$wp_rewrite->init();
	flush_rewrite_rules( true );
	$log[] = 'تم تحديث قواعد الروابط';
	// Purge page caches so a cached 404 is not served again.
	do_action( 'litespeed_purge_all' );
	if ( function_exists( 'rocket_clean_domain' ) ) {
		rocket_clean_domain();
	}
	if ( function_exists( 'w3tc_flush_all' ) ) {
		w3tc_flush_all();
	}
	if ( function_exists( 'wp_cache_clear_cache' ) ) {
		wp_cache_clear_cache();
	}
	if ( class_exists( 'autoptimizeCache' ) ) {
		autoptimizeCache::clearall();
	}
	wp_cache_flush();
	$log[] = 'تم مسح الكاش';
	return $log;
}

/** Automatic check on every admin visit (cheap; repairs only when needed). */
add_action(
	'admin_init',
	function () {
		if ( wp_doing_ajax() || ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		if ( ! rmt_pages_ok() ) {
			rmt_repair_pages();
		}
	},
	30
);

/** Self-heal: a 404 on a known theme URL repairs the pages once and retries. */
add_action(
	'template_redirect',
	function () {
		if ( ! is_404() ) {
			return;
		}
		$path = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ), '/' ); // phpcs:ignore
		$base = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
		if ( $base && 0 === strpos( $path, $base ) ) {
			$path = trim( substr( $path, strlen( $base ) ), '/' );
		}
		$known = wp_list_pluck( rmt_expected_pages(), 0 );
		if ( ! in_array( $path, $known, true ) || get_transient( 'rmt_self_heal' ) ) {
			return;
		}
		set_transient( 'rmt_self_heal', 1, 10 * MINUTE_IN_SECONDS );
		rmt_repair_pages();
		wp_safe_redirect( home_url( '/' . $path . '/' ), 302 );
		exit;
	},
	1
);

/** Manual repair from the panel. */
add_action(
	'admin_post_rmt_repair',
	function () {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( 'غير مسموح.' );
		}
		check_admin_referer( 'rmt_repair' );
		set_transient( 'rmt_repair_log', rmt_repair_pages(), 300 );
		wp_safe_redirect( admin_url( 'admin.php?page=rmt-guide&repaired=1#health' ) );
		exit;
	}
);

/** Diagnostics card (rendered on the guide page). */
function rmt_render_health() {
	$status = rmt_pages_status();
	$server = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '?'; // phpcs:ignore
	$ht     = function_exists( 'get_home_path' ) ? get_home_path() . '.htaccess' : ABSPATH . '.htaccess';
	$ht_ok  = file_exists( $ht ) && false !== strpos( (string) file_get_contents( $ht ), 'RewriteRule' ); // phpcs:ignore
	$log    = get_transient( 'rmt_repair_log' );
	?>
	<div class="rmt-card" id="health">
		<h3>فحص الصفحات والروابط</h3>
		<?php if ( $log ) : delete_transient( 'rmt_repair_log' ); ?>
			<div class="notice notice-success inline"><p><strong>تم الإصلاح:</strong><br><?php echo wp_kses_post( implode( '<br>', array_map( 'esc_html', $log ) ) ); ?></p></div>
		<?php endif; ?>
		<p>
			السيرفر: <code dir="ltr"><?php echo esc_html( $server ); ?></code> —
			الروابط الدائمة: <code dir="ltr"><?php echo esc_html( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'plain' ); ?></code> —
			‎.htaccess: <?php echo $ht_ok ? '✔ يحتوي قواعد ووردبرس' : ( false !== stripos( $server, 'apache' ) || false !== stripos( $server, 'litespeed' ) ? '<b style="color:#d63638">✖ بدون قواعد</b>' : 'غير مطلوب لهذا السيرفر' ); ?>
		</p>
		<table class="widefat striped rmt-health">
			<thead><tr><th>الصفحة</th><th>الحالة</th><th>الرابط</th><th>اختبار الفتح</th></tr></thead>
			<tbody>
			<?php foreach ( $status as $path => $s ) : ?>
				<tr>
					<td dir="ltr">/<?php echo esc_html( $path ); ?>/</td>
					<td><?php echo $s[0] ? '<span style="color:#1faa53">✔ ' . esc_html( $s[1] ) . '</span>' : '<span style="color:#d63638">✖ ' . esc_html( $s[1] ) . '</span>'; ?></td>
					<td><?php echo $s[3] ? '<a href="' . esc_url( $s[3] ) . '" target="_blank" dir="ltr">' . esc_html( $s[3] ) . '</a>' : '—'; ?></td>
					<td class="rmt-probe" data-url="<?php echo esc_url( $s[3] ); ?>"><?php echo $s[3] ? '…' : '—'; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<script>
		/* Probe each page from the browser (parallel, no server loopback). */
		document.querySelectorAll('.rmt-probe[data-url]').forEach(function (td) {
			var u = td.getAttribute('data-url'); if (!u) return;
			fetch(u, { method: 'GET', redirect: 'manual', cache: 'no-store', credentials: 'omit' }).then(function (r) {
				var c = r.type === 'opaqueredirect' ? 'تحويل' : r.status;
				td.innerHTML = c === 200 ? '<span style="color:#1faa53">200 ✔</span>' : '<b style="color:#d63638">' + c + '</b>';
			}).catch(function () { td.textContent = 'تعذّر الاختبار'; });
		});
		</script>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:12px">
			<input type="hidden" name="action" value="rmt_repair">
			<?php wp_nonce_field( 'rmt_repair' ); ?>
			<button class="button button-primary">🛠 إصلاح تلقائي للصفحات والروابط</button>
		</form>
	</div>
	<?php
}
