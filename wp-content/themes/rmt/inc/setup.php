<?php
/**
 * Theme setup, assets, performance clean-up and first-run installer.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

/** Core pages created on activation: slug => title. */
function rmt_core_pages() {
	return array(
		'home'     => 'الرئيسية',
		'about'    => 'من نحن',
		'services' => 'خدماتنا',
		'clients'  => 'عملاؤنا',
		'contact'  => 'اتصل بنا',
	);
}

/** Theme supports. */
function rmt_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	register_nav_menus( array( 'primary' => 'القائمة الرئيسية' ) );
	add_image_size( 'rmt-card', 640, 480, true );
	add_image_size( 'rmt-logo', 320, 160, false );
}
add_action( 'after_setup_theme', 'rmt_setup' );

/** Front-end assets (no jQuery, deferred JS, one small CSS file). */
function rmt_assets() {
	$v   = wp_get_theme()->get( 'Version' );
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'rmt-fonts', 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;700&display=swap', array(), null );
	wp_enqueue_style( 'rmt-main', $uri . '/assets/css/main.css', array(), $v );
	wp_enqueue_script( 'rmt-main', $uri . '/assets/js/main.js', array(), $v, array( 'strategy' => 'defer', 'in_footer' => true ) );

	// Classic theme: drop block-editor CSS we don't use.
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
}
add_action( 'wp_enqueue_scripts', 'rmt_assets', 20 );

/** Preconnect + preload for faster first paint. */
function rmt_resource_hints() {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	if ( 'home' === rmt_current_key() ) {
		$poster = rmt_get( 'home', 'hero_poster' );
		if ( $poster ) {
			// Preload exactly the file the <picture> will use (WebP for bundled assets).
			$theme_uri = get_template_directory_uri();
			if ( 0 === strpos( $poster, $theme_uri ) && preg_match( '/\.jpe?g$/i', $poster ) ) {
				$webp = preg_replace( '/\.jpe?g$/i', '.webp', $poster );
				if ( file_exists( get_template_directory() . substr( $webp, strlen( $theme_uri ) ) ) ) {
					$poster = $webp;
				}
			}
			echo '<link rel="preload" as="image" href="' . esc_url( $poster ) . '" fetchpriority="high">' . "\n";
		}
	}
}
add_action( 'wp_head', 'rmt_resource_hints', 1 );

/**
 * Page-transition loader: decide before first paint whether to show it
 * (first visit in the session, or arriving from an internal link click).
 */
function rmt_loader_boot() {
	echo "<script>(function(d){try{var s=sessionStorage,m=window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches;if(!m&&(s.getItem('rmt-nav')||!s.getItem('rmt-seen'))){d.documentElement.classList.add('is-loading');}s.setItem('rmt-seen','1');s.removeItem('rmt-nav');}catch(e){}})(document);</script>\n";
}
add_action( 'wp_head', 'rmt_loader_boot', 0 );

/** Head clean-up: remove emoji, oEmbed discovery, RSD, generator, shortlinks. */
function rmt_cleanup() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'rmt_cleanup' );

/** Remove the block-library SVG filters printed in the body. */
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );


/** Body classes. */
function rmt_body_class( $c ) {
	$c[] = 'rmt';
	if ( is_page() ) {
		$c[] = 'page-' . get_post_field( 'post_name', get_queried_object_id() );
	}
	return $c;
}
add_filter( 'body_class', 'rmt_body_class' );

/**
 * First-run installer: create the 5 pages, static front page, pretty permalinks,
 * Riyadh timezone, Arabic site title. Safe to run more than once.
 */
function rmt_install() {
	$ids = array();
	foreach ( rmt_core_pages() as $slug => $title ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $title,
					'post_content' => '',
				)
			);
		} else {
			$id = $page->ID;
		}
		$ids[ $slug ] = $id;
	}
	// English pages live under /en/.
	$en = get_page_by_path( 'en' );
	$en_id = $en ? $en->ID : wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_name'   => 'en',
			'post_title'  => 'Home',
		)
	);
	$en_titles = array( 'about' => 'About Us', 'services' => 'Our Services', 'clients' => 'Our Clients', 'contact' => 'Contact Us' );
	foreach ( $en_titles as $slug => $title ) {
		if ( ! get_page_by_path( 'en/' . $slug ) ) {
			wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_name'   => $slug,
					'post_title'  => $title,
					'post_parent' => $en_id,
				)
			);
		}
	}

	rmt_install_legal();
	rmt_install_blog();
	rmt_remove_hello_world();

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home'] );
	global $wp_rewrite;
	if ( false === strpos( (string) get_option( 'permalink_structure' ), '%postname%' ) ) {
		// set_permalink_structure() updates the option AND the in-memory rewrite
		// object, so the flush below writes the right rules to .htaccess.
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}
	update_option( 'timezone_string', 'Asia/Riyadh' );
	update_option( 'blogname', 'رفال المدينة للمقاولات' );
	update_option( 'blogdescription', 'شركة تنظيف واجهات المباني والأبراج بالرياض' );
	update_option( 'start_of_week', 6 );
	update_option( 'rmt_installed', wp_get_theme()->get( 'Version' ) );
	$wp_rewrite->init();
	flush_rewrite_rules( true );
}
add_action( 'after_switch_theme', 'rmt_install' );

/**
 * Safety net for Apache hosts (e.g. LocalWP/Apache, cPanel): make sure
 * .htaccess carries the WordPress rewrite block, otherwise every inner page
 * (/about/, /en/about/ …) returns 404.
 */
function rmt_ensure_rewrites() {
	if ( ! current_user_can( 'manage_options' ) || ! get_option( 'permalink_structure' ) ) {
		return;
	}
	$rules = get_option( 'rewrite_rules' );
	$need  = empty( $rules );
	if ( ! $need && function_exists( 'got_mod_rewrite' ) && got_mod_rewrite() ) {
		$ht   = get_home_path() . '.htaccess';
		$need = ! file_exists( $ht ) || false === strpos( (string) file_get_contents( $ht ), 'RewriteRule' ); // phpcs:ignore
	}
	if ( $need ) {
		flush_rewrite_rules( true );
	}
}
add_action(
	'admin_init',
	function () {
		if ( ! function_exists( 'get_home_path' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		if ( ! function_exists( 'got_mod_rewrite' ) ) {
			require_once ABSPATH . 'wp-admin/includes/misc.php';
		}
		rmt_ensure_rewrites();
	},
	20
);

/** Re-run installer silently if pages were removed. */
function rmt_maybe_install() {
	if ( version_compare( (string) get_option( 'rmt_installed', '0' ), wp_get_theme()->get( 'Version' ), '<' ) ) {
		rmt_install();
	}
}
add_action( 'admin_init', 'rmt_maybe_install' );

/** Primary navigation items (used when no WP menu is assigned). */
function rmt_nav_items() {
	$items = array(
		'home'     => rmt_t( 'الرئيسية' ),
		'about'    => rmt_t( 'من نحن' ),
		'services' => rmt_t( 'خدماتنا' ),
		'clients'  => rmt_t( 'عملاؤنا' ),
		'blog'     => rmt_t( 'المدونة' ),
		'contact'  => rmt_t( 'اتصل بنا' ),
	);
	if ( rmt_is_en() ) {
		unset( $items['blog'] ); // The blog is Arabic only.
	}
	return $items;
}

/** Current core page key. */
function rmt_current_key() {
	if ( is_front_page() ) {
		return 'home';
	}
	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		return 'en' === $slug ? 'home' : $slug;
	}
	if ( function_exists( 'rmt_is_blog' ) && rmt_is_blog() ) {
		return 'blog';
	}
	return '';
}

/** Render the menu (WP menu if assigned, otherwise core pages). */
function rmt_nav( $class = 'nav' ) {
	if ( has_nav_menu( 'primary' ) && ! rmt_is_en() ) {
		wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => $class, 'depth' => 1 ) );
		return;
	}
	$cur = rmt_current_key();
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( rmt_nav_items() as $k => $label ) {
		printf(
			'<li%s><a href="%s"%s>%s</a></li>',
			$cur === $k ? ' class="current"' : '',
			esc_url( rmt_page_url( $k ) ),
			$cur === $k ? ' aria-current="page"' : '',
			esc_html( $label )
		);
	}
	echo '</ul>';
}
