<?php
/**
 * Built-in SEO (bilingual): titles, meta descriptions, canonical, hreflang,
 * robots, Open Graph, Twitter cards, verification tags, analytics, JSON-LD
 * (LocalBusiness, WebSite, WebPage, BreadcrumbList, OfferCatalog, ItemList,
 * FAQPage, VideoObject), sitemap & robots.txt tuning.
 *
 * Automatically disabled when Yoast SEO / Rank Math / AIOSEO / SEOPress is active.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

/** Is our SEO layer active? */
function rmt_seo_on() {
	if ( '1' !== rmt_get( 'seo', 'enabled' ) ) {
		return false;
	}
	return ! ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) );
}

/** SEO key of the current request (home/about/services/clients/contact). */
function rmt_seo_key() {
	$k = rmt_current_key();
	return in_array( $k, array( 'home', 'about', 'services', 'clients', 'contact' ), true ) ? $k : '';
}

/** Meta title for current page. */
function rmt_seo_title() {
	$k = rmt_seo_key();
	if ( $k && rmt_get( 'seo', $k . '_title' ) ) {
		return rmt_get( 'seo', $k . '_title' );
	}
	if ( is_404() ) {
		return rmt_t( 'الصفحة غير موجودة' ) . ' | ' . rmt_get( 'general', 'short_name' );
	}
	return wp_get_document_title();
}

/** Meta description for current page. */
function rmt_seo_desc() {
	$k = rmt_seo_key();
	if ( $k ) {
		return rmt_get( 'seo', $k . '_desc' );
	}
	if ( is_singular() ) {
		$p = get_post();
		return wp_trim_words( wp_strip_all_tags( $p->post_excerpt ? $p->post_excerpt : $p->post_content ), 28, '…' );
	}
	return rmt_get( 'seo', 'home_desc' );
}

/** Canonical URL (never carries query strings such as ?service= or ?sent=). */
function rmt_canonical() {
	$k = rmt_seo_key();
	if ( $k ) {
		return rmt_page_url( $k );
	}
	if ( is_singular() ) {
		return get_permalink();
	}
	return '';
}

/** Absolute dimensions of an image URL (theme asset or media library). */
function rmt_image_size( $url ) {
	$theme_uri = get_template_directory_uri();
	if ( 0 === strpos( $url, $theme_uri ) ) {
		$file = get_template_directory() . substr( $url, strlen( $theme_uri ) );
		$s    = file_exists( $file ) ? @getimagesize( $file ) : false; // phpcs:ignore
		return $s ? array( $s[0], $s[1], $s['mime'] ) : array();
	}
	$id = attachment_url_to_postid( $url );
	if ( $id ) {
		$m = wp_get_attachment_metadata( $id );
		if ( ! empty( $m['width'] ) ) {
			return array( $m['width'], $m['height'], get_post_mime_type( $id ) );
		}
	}
	return array();
}

/** Override <title>. */
add_filter(
	'pre_get_document_title',
	function ( $t ) {
		return rmt_seo_on() && ( rmt_seo_key() || is_404() ) ? rmt_seo_title() : $t;
	}
);
add_filter( 'document_title_separator', function () { return '|'; } );

/** Robots meta via core API. */
add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( ! rmt_seo_on() ) {
			return $robots;
		}
		// phpcs:ignore WordPress.Security.NonceVerification
		$utility = isset( $_GET['sent'] ) || isset( $_GET['err'] );
		if ( '1' === rmt_get( 'seo', 'noindex_site' ) || $utility || is_404() || is_search() || is_attachment() || is_author() || is_date() ) {
			return array( 'noindex' => true, 'follow' => true );
		}
		$robots['index']             = true;
		$robots['follow']            = true;
		$robots['max-image-preview'] = 'large';
		$robots['max-snippet']       = '-1';
		$robots['max-video-preview'] = '-1';
		return $robots;
	}
);

/** Core prints canonical for singular; we print our own instead. */
add_action(
	'wp',
	function () {
		if ( rmt_seo_on() ) {
			remove_action( 'wp_head', 'rel_canonical' );
		}
	}
);

/** Main head output. */
function rmt_seo_head() {
	$g = rmt_get( 'general' );
	echo "\n<!-- RMT SEO -->\n";
	echo '<meta name="theme-color" content="#0b2545">' . "\n";
	echo '<meta name="format-detection" content="telephone=no">' . "\n";

	// Verification (also printed when an SEO plugin is active).
	foreach ( array( 'gsc' => 'google-site-verification', 'bing' => 'msvalidate.01', 'yandex' => 'yandex-verification' ) as $k => $n ) {
		if ( rmt_get( 'seo', $k ) ) {
			printf( '<meta name="%s" content="%s">' . "\n", esc_attr( $n ), esc_attr( rmt_get( 'seo', $k ) ) );
		}
	}

	if ( ! rmt_seo_on() ) {
		return;
	}

	$title = rmt_seo_title();
	$desc  = rmt_seo_desc();
	$canon = rmt_canonical();
	$key   = rmt_seo_key();
	$img   = rmt_get( 'seo', 'og_image' );
	if ( 'about' === $key && rmt_get( 'about', 'image' ) ) {
		$img = rmt_get( 'about', 'image' );
	}

	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $canon ) {
		echo '<link rel="canonical" href="' . esc_url( $canon ) . '">' . "\n";
	}
	// hreflang pairs between the Arabic and English versions of the same page.
	$alt = $key ? $key : ( function_exists( 'rmt_legal_key' ) ? rmt_legal_key() : '' );
	if ( $alt ) {
		echo '<link rel="alternate" hreflang="ar" href="' . esc_url( rmt_page_url( $alt, 'ar' ) ) . '">' . "\n";
		echo '<link rel="alternate" hreflang="en" href="' . esc_url( rmt_page_url( $alt, 'en' ) ) . '">' . "\n";
		echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( rmt_page_url( $alt, 'ar' ) ) . '">' . "\n";
	}
	// Local SEO geo tags.
	echo '<meta name="geo.region" content="SA-01">' . "\n";
	echo '<meta name="geo.placename" content="' . esc_attr( $g['city'] ) . '">' . "\n";
	if ( $g['lat'] && $g['lng'] ) {
		echo '<meta name="geo.position" content="' . esc_attr( $g['lat'] . ';' . $g['lng'] ) . '">' . "\n";
		echo '<meta name="ICBM" content="' . esc_attr( $g['lat'] . ', ' . $g['lng'] ) . '">' . "\n";
	}

	// Open Graph.
	$en = rmt_is_en();
	$og = array(
		'og:locale'           => $en ? 'en_US' : 'ar_SA',
		'og:locale:alternate' => $en ? 'ar_SA' : 'en_US',
		'og:type'             => 'home' === $key ? 'website' : 'article',
		'og:site_name'        => $g['company_name'],
		'og:title'            => $title,
		'og:description'      => $desc,
		'og:url'              => $canon ? $canon : home_url( '/' ),
		'og:image'            => $img,
		'og:image:alt'        => $title,
	);
	$dim = $img ? rmt_image_size( $img ) : array();
	if ( $dim ) {
		$og['og:image:width']  = $dim[0];
		$og['og:image:height'] = $dim[1];
		$og['og:image:type']   = $dim[2];
	}
	foreach ( $og as $p => $c ) {
		if ( $c ) {
			printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $p ), esc_attr( $c ) );
		}
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	if ( $img ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $img ) );
		printf( '<meta name="twitter:image:alt" content="%s">' . "\n", esc_attr( $title ) );
	}
	if ( $g['social_x'] ) {
		$handle = '@' . trim( wp_parse_url( $g['social_x'], PHP_URL_PATH ), '/' );
		printf( '<meta name="twitter:site" content="%s">' . "\n", esc_attr( $handle ) );
	}

	rmt_print_schema();
	echo "<!-- /RMT SEO -->\n";
}
add_action( 'wp_head', 'rmt_seo_head', 2 );

/** Favicons / app icons (only if no Site Icon was set in Customizer). */
function rmt_icons() {
	if ( has_site_icon() ) {
		return;
	}
	$u = get_template_directory_uri() . '/assets/img/';
	echo '<link rel="icon" href="' . esc_url( $u . 'favicon-32.png' ) . '" sizes="32x32">' . "\n";
	echo '<link rel="icon" href="' . esc_url( $u . 'icon-512.png' ) . '" sizes="512x512">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $u . 'apple-touch-icon.png' ) . '">' . "\n";
}
add_action( 'wp_head', 'rmt_icons', 3 );

/** Build & print JSON-LD graph. */
function rmt_print_schema() {
	$g     = rmt_get( 'general' );
	$home  = home_url( '/' );
	$orgid = $home . '#organization';
	$key   = rmt_seo_key();
	$lang  = rmt_is_en() ? 'en' : 'ar';
	$lhome = rmt_page_url( 'home' );

	$same = array_values( array_filter( array( $g['social_x'], $g['social_instagram'], $g['social_snapchat'], $g['social_tiktok'], $g['social_linkedin'], $g['social_youtube'], $g['map_url'] ) ) );

	$images = array_values(
		array_unique(
			array_filter(
				array( rmt_get( 'seo', 'og_image' ), rmt_get( 'about', 'image' ), rmt_get( 'home', 'hero_poster' ) )
			)
		)
	);

	$business = array(
		'@type'                     => array( 'LocalBusiness', 'ProfessionalService' ),
		'@id'                       => $orgid,
		'name'                      => $g['company_name'],
		'alternateName'             => array_values( array_unique( array( $g['short_name'], $g['name_en'], 'RMT' ) ) ),
		'description'               => rmt_get( 'seo', 'home_desc' ),
		'slogan'                    => $g['tagline'],
		'url'                       => $lhome,
		'logo'                      => array( '@type' => 'ImageObject', 'url' => rmt_logo_url(), 'width' => 600, 'height' => 600 ),
		'image'                     => $images,
		'telephone'                 => rmt_tel(),
		'email'                     => $g['email_primary'],
		'foundingDate'              => $g['founded'],
		'priceRange'                => rmt_get( 'seo', 'price_range' ),
		'currenciesAccepted'        => 'SAR',
		'address'                   => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $g['street'],
			'addressLocality' => $g['city'],
			'addressRegion'   => $g['region'],
			'postalCode'      => $g['postal_code'],
			'addressCountry'  => 'SA',
		),
		'areaServed'                => array(
			array( '@type' => 'City', 'name' => rmt_t( 'الرياض' ) ),
			array( '@type' => 'Country', 'name' => rmt_t( 'المملكة العربية السعودية' ) ),
			array( '@type' => 'Country', 'name' => rmt_t( 'البحرين' ) ),
		),
		'contactPoint'              => array(
			array(
				'@type'             => 'ContactPoint',
				'telephone'         => rmt_tel(),
				'email'             => $g['email_primary'],
				'contactType'       => 'customer service',
				'areaServed'        => array( 'SA', 'BH' ),
				'availableLanguage' => array( 'Arabic', 'English' ),
			),
		),
		'openingHoursSpecification' => array(
			'@type'     => 'OpeningHoursSpecification',
			'dayOfWeek' => array( 'Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday' ),
			'opens'     => '08:00',
			'closes'    => '18:00',
		),
		'knowsAbout'                => array_values( array_filter( array_map( 'trim', explode( ',', rmt_get( 'seo', 'keywords' ) ) ) ) ),
	);
	if ( $g['landline'] ) {
		$business['faxNumber'] = $g['landline'];
	}
	if ( $g['lat'] && $g['lng'] ) {
		$business['geo'] = array( '@type' => 'GeoCoordinates', 'latitude' => (float) $g['lat'], 'longitude' => (float) $g['lng'] );
	}
	if ( $g['map_url'] ) {
		$business['hasMap'] = $g['map_url'];
	}
	if ( $same ) {
		$business['sameAs'] = $same;
	}

	// Service catalogue.
	$offers = array();
	foreach ( (array) rmt_get( 'services', 'items' ) as $i => $s ) {
		$offers[] = array(
			'@type'       => 'Offer',
			'itemOffered' => array(
				'@type'       => 'Service',
				'name'        => $s['title'],
				'description' => $s['short'],
				'url'         => rmt_page_url( 'services' ) . '#' . rmt_slug( $s['title'], $i ),
				'provider'    => array( '@id' => $orgid ),
				'areaServed'  => 'SA',
			),
		);
	}
	$business['hasOfferCatalog'] = array( '@type' => 'OfferCatalog', 'name' => rmt_t( 'خدماتنا' ), 'itemListElement' => $offers );

	$graph   = array( $business );
	$graph[] = array(
		'@type'      => 'WebSite',
		'@id'        => $home . '#website',
		'url'        => $home,
		'name'       => $g['company_name'],
		'inLanguage' => array( 'ar-SA', 'en' ),
		'publisher'  => array( '@id' => $orgid ),
	);

	$url   = rmt_canonical();
	$types = array( 'home' => 'WebPage', 'about' => 'AboutPage', 'contact' => 'ContactPage', 'services' => 'WebPage', 'clients' => 'CollectionPage' );
	if ( $url ) {
		$page = array(
			'@type'       => $key && isset( $types[ $key ] ) ? $types[ $key ] : 'WebPage',
			'@id'         => $url . '#webpage',
			'url'         => $url,
			'name'        => rmt_seo_title(),
			'description' => rmt_seo_desc(),
			'inLanguage'  => 'en' === $lang ? 'en' : 'ar-SA',
			'isPartOf'    => array( '@id' => $home . '#website' ),
			'about'       => array( '@id' => $orgid ),
		);
		if ( $key && 'home' !== $key ) {
			$page['breadcrumb'] = array( '@id' => $url . '#breadcrumb' );
		}
		$graph[] = $page;
	}

	if ( $key && 'home' !== $key ) {
		$items   = rmt_nav_items();
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $url . '#breadcrumb',
			'itemListElement' => array(
				array( '@type' => 'ListItem', 'position' => 1, 'name' => $items['home'], 'item' => $lhome ),
				array( '@type' => 'ListItem', 'position' => 2, 'name' => $items[ $key ], 'item' => $url ),
			),
		);
	}

	if ( 'home' === $key ) {
		$faq = array();
		foreach ( (array) rmt_get( 'home', 'faq' ) as $q ) {
			$faq[] = array( '@type' => 'Question', 'name' => $q['q'], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $q['a'] ) );
		}
		if ( $faq ) {
			$graph[] = array( '@type' => 'FAQPage', 'mainEntity' => $faq );
		}
	}

	if ( 'services' === $key ) {
		$list = array();
		foreach ( (array) rmt_get( 'services', 'items' ) as $i => $s ) {
			$list[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => $s['title'], 'url' => $url . '#' . rmt_slug( $s['title'], $i ) );
		}
		$graph[] = array( '@type' => 'ItemList', 'name' => rmt_t( 'خدماتنا' ), 'itemListElement' => $list );
	}

	if ( 'clients' === $key ) {
		$list = array();
		foreach ( (array) rmt_get( 'clients', 'items' ) as $i => $c ) {
			$list[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'item' => array( '@type' => 'Organization', 'name' => $c['name'] ) );
		}
		$graph[] = array( '@type' => 'ItemList', 'name' => rmt_t( 'عملاؤنا' ), 'itemListElement' => $list );
	}

	// Portfolio videos (home + clients pages show the gallery).
	if ( in_array( $key, array( 'home', 'clients' ), true ) ) {
		foreach ( (array) rmt_get( 'projects', 'items' ) as $p ) {
			if ( 'video' === $p['type'] && $p['src'] ) {
				$graph[] = array(
					'@type'        => 'VideoObject',
					'name'         => $p['title'],
					'description'  => $p['title'] . ' – ' . $g['company_name'] . ( $p['place'] ? ' – ' . $p['place'] : '' ),
					'thumbnailUrl' => $p['poster'] ? $p['poster'] : rmt_get( 'seo', 'og_image' ),
					'contentUrl'   => $p['src'],
					'uploadDate'   => gmdate( 'c', (int) get_option( 'rmt_media_date', time() ) ),
					'inLanguage'   => 'en' === $lang ? 'en' : 'ar',
					'publisher'    => array( '@id' => $orgid ),
				);
			}
		}
	}

	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

/** Analytics (GA4 or GTM), printed late in head and only for visitors. */
function rmt_analytics() {
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		return; // Don't pollute stats with admin visits.
	}
	$gtm = rmt_get( 'seo', 'gtm' );
	$ga4 = rmt_get( 'seo', 'ga4' );
	if ( $gtm && preg_match( '/^GTM-[A-Z0-9]+$/', $gtm ) ) {
		echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . esc_js( $gtm ) . "');</script>\n";
	} elseif ( $ga4 && preg_match( '/^G-[A-Z0-9]+$/', $ga4 ) ) {
		echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $ga4 ) . '"></script>' . "\n";
		echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','" . esc_js( $ga4 ) . "');</script>\n";
	}
}
add_action( 'wp_head', 'rmt_analytics', 99 );

/** Core sitemap: keep only pages, drop users/taxonomies/leads. */
add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $name ) {
		return in_array( $name, array( 'users', 'taxonomies' ), true ) ? false : $provider;
	},
	10,
	2
);
add_filter(
	'wp_sitemaps_post_types',
	function ( $types ) {
		unset( $types['rmt_lead'], $types['attachment'] );
		if ( ! wp_count_posts( 'post' )->publish ) {
			unset( $types['post'] );
		}
		return $types;
	}
);
/** Sitemap: drop the duplicate Arabic "home" page (it is the front page "/"). */
add_filter(
	'wp_sitemaps_posts_query_args',
	function ( $args, $post_type ) {
		if ( 'page' === $post_type ) {
			$home = get_page_by_path( 'home' );
			if ( $home && (int) get_option( 'page_on_front' ) !== (int) $home->ID ) {
				$args['post__not_in'] = array( $home->ID );
			}
		}
		return $args;
	},
	10,
	2
);
add_filter(
	'wp_sitemaps_posts_entry',
	function ( $entry, $post ) {
		$entry['lastmod'] = get_post_modified_time( 'c', true, $post );
		return $entry;
	},
	10,
	2
);
/** Bump sitemap lastmod when panel content changes. */
add_action(
	'update_option_' . RMT_OPTION,
	function () {
		foreach ( get_pages( array( 'post_status' => 'publish' ) ) as $p ) {
			wp_update_post( array( 'ID' => $p->ID ) );
		}
	}
);

/** robots.txt */
add_filter(
	'robots_txt',
	function ( $out, $public ) {
		if ( ! $public || '1' === rmt_get( 'seo', 'noindex_site' ) ) {
			return "User-agent: *\nDisallow: /\n";
		}
		$out  = "User-agent: *\n";
		$out .= "Disallow: /wp-admin/\n";
		$out .= "Allow: /wp-admin/admin-ajax.php\n";
		$out .= "Disallow: /?s=\n";
		$out .= "Disallow: /search/\n";
		$out .= "Disallow: /wp-login.php\n";
		$out .= "Disallow: /*?sent=\n";
		$out .= "Disallow: /*?err=\n\n";
		$out .= 'Sitemap: ' . home_url( '/wp-sitemap.xml' ) . "\n";
		return $out;
	},
	20,
	2
);

/** Redirect attachment pages & author archives (thin content) to home. */
add_action(
	'template_redirect',
	function () {
		if ( is_attachment() || is_author() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
		if ( is_page( 'home' ) && ! is_front_page() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}
);

/** Stable anchor id for a service. */
function rmt_slug( $title, $i ) {
	return 'service-' . ( (int) $i + 1 );
}

/** Store a "media upload date" once for VideoObject. */
add_action(
	'after_switch_theme',
	function () {
		if ( ! get_option( 'rmt_media_date' ) ) {
			update_option( 'rmt_media_date', time() );
		}
	}
);

/** Use the localized company name in <title> on non-panel pages (e.g. legal pages in English). */
add_filter(
	'document_title_parts',
	function ( $parts ) {
		if ( isset( $parts['site'] ) ) {
			$parts['site'] = rmt_get( 'general', 'short_name' );
		}
		unset( $parts['tagline'] );
		return $parts;
	}
);
