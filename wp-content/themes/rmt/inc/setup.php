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
	if ( is_front_page() ) {
		$poster = rmt_get( 'home', 'hero_poster' );
		if ( $poster ) {
			echo '<link rel="preload" as="image" href="' . esc_url( $poster ) . '" fetchpriority="high">' . "\n";
		}
	}
}
add_action( 'wp_head', 'rmt_resource_hints', 1 );

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
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home'] );
	if ( false === strpos( (string) get_option( 'permalink_structure' ), '%postname%' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	update_option( 'timezone_string', 'Asia/Riyadh' );
	update_option( 'blogname', 'رفال المدينة للمقاولات' );
	update_option( 'blogdescription', 'شركة تنظيف واجهات المباني والأبراج بالرياض' );
	update_option( 'start_of_week', 6 );
	update_option( 'rmt_installed', wp_get_theme()->get( 'Version' ) );
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'rmt_install' );

/** Re-run installer silently if pages were removed. */
function rmt_maybe_install() {
	if ( ! get_option( 'rmt_installed' ) ) {
		rmt_install();
	}
}
add_action( 'admin_init', 'rmt_maybe_install' );

/** Primary navigation items (used when no WP menu is assigned). */
function rmt_nav_items() {
	return array(
		'home'     => 'الرئيسية',
		'about'    => 'من نحن',
		'services' => 'خدماتنا',
		'clients'  => 'عملاؤنا',
		'contact'  => 'اتصل بنا',
	);
}

/** Current core page key. */
function rmt_current_key() {
	if ( is_front_page() ) {
		return 'home';
	}
	if ( is_page() ) {
		return get_post_field( 'post_name', get_queried_object_id() );
	}
	return '';
}

/** Render the menu (WP menu if assigned, otherwise core pages). */
function rmt_nav( $class = 'nav' ) {
	if ( has_nav_menu( 'primary' ) ) {
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
