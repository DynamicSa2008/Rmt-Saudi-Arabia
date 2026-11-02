<?php
/**
 * Bilingual layer (Arabic default + English under /en/).
 *
 * - Language is detected from the URL (/en/...).
 * - Translatable panel fields are stored next to the Arabic value as "key_en".
 * - Static template strings go through rmt_t().
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

/** Current front-end language: 'ar' or 'en'. */
function rmt_lang() {
	static $lang = null;
	if ( null !== $lang ) {
		return $lang;
	}
	$lang = 'ar';
	if ( is_admin() && ! wp_doing_ajax() ) {
		return $lang;
	}
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$base = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( $base && '/' !== $base && 0 === strpos( $path, $base ) ) {
		$path = '/' . substr( $path, strlen( $base ) );
	}
	if ( preg_match( '#^/en(/|$)#', $path ) ) {
		$lang = 'en';
	}
	return $lang;
}

/** Is English? */
function rmt_is_en() {
	return 'en' === rmt_lang();
}

/**
 * Translate a static Arabic UI string.
 *
 * @param string $ar Arabic source string.
 * @return string
 */
function rmt_t( $ar ) {
	if ( ! rmt_is_en() ) {
		return $ar;
	}
	static $dict = null;
	if ( null === $dict ) {
		$dict = require __DIR__ . '/lang-en.php';
	}
	return isset( $dict[ $ar ] ) ? $dict[ $ar ] : $ar;
}

/** Echo escaped translation. */
function rmt_te( $ar ) {
	echo esc_html( rmt_t( $ar ) );
}

/**
 * Keys that are NOT translatable even though they are text inputs.
 *
 * @return array
 */
function rmt_i18n_skip() {
	return array(
		'general.phone', 'general.whatsapp', 'general.landline', 'general.founded', 'general.founded_hijri',
		'general.postal_code', 'general.map_embed_query', 'general.lat', 'general.lng', 'general.name_en',
		'general.email_primary', 'general.email_secondary',
		'seo.ga4', 'seo.gtm', 'seo.gsc', 'seo.bing', 'seo.yandex', 'seo.price_range',
		'contact.recipients',
		'sub.number', 'sub.suffix', 'sub.floors', 'sub.area', 'sub.team', 'sub.name@about.certs',
	);
}

/**
 * Is a field translatable?
 *
 * @param string $type Field type.
 * @param string $path "section.key" or "sub.key".
 * @return bool
 */
function rmt_is_i18n( $type, $path ) {
	if ( ! in_array( $type, array( 'text', 'textarea', 'seo_title', 'seo_desc' ), true ) ) {
		return false;
	}
	return ! in_array( $path, rmt_i18n_skip(), true );
}

/**
 * Localize a merged options array for English: key_en overrides key.
 *
 * @param array $o Raw merged options.
 * @return array
 */
function rmt_localize_options( $o ) {
	foreach ( rmt_panel_schema() as $tab ) {
		foreach ( $tab['groups'] as $group ) {
			foreach ( $group['fields'] as $f ) {
				list( $s, $k ) = explode( '.', $f[0] );
				if ( 'repeater' === $f[1] ) {
					if ( empty( $o[ $s ][ $k ] ) || ! is_array( $o[ $s ][ $k ] ) ) {
						continue;
					}
					foreach ( $o[ $s ][ $k ] as $i => $row ) {
						foreach ( $f[4] as $sub ) {
							if ( rmt_is_i18n( $sub[1], 'sub.' . $sub[0] ) && ! in_array( 'sub.' . $sub[0] . '@' . $f[0], rmt_i18n_skip(), true ) ) {
								$en = isset( $row[ $sub[0] . '_en' ] ) ? trim( (string) $row[ $sub[0] . '_en' ] ) : '';
								if ( '' !== $en ) {
									$o[ $s ][ $k ][ $i ][ $sub[0] ] = $row[ $sub[0] . '_en' ];
								}
							}
						}
					}
				} elseif ( rmt_is_i18n( $f[1], $f[0] ) ) {
					$en = isset( $o[ $s ][ $k . '_en' ] ) ? trim( (string) $o[ $s ][ $k . '_en' ] ) : '';
					if ( '' !== $en ) {
						$o[ $s ][ $k ] = $o[ $s ][ $k . '_en' ];
					}
				}
			}
		}
	}
	return $o;
}

/** <html lang> + dir. */
add_filter(
	'language_attributes',
	function ( $out ) {
		if ( is_admin() || wp_doing_ajax() ) {
			return $out; // Leave wp-admin to the user's own language settings.
		}
		return rmt_is_en() ? 'lang="en-US" dir="ltr"' : 'lang="ar" dir="rtl"';
	}
);

/** Body class for language. */
add_filter(
	'body_class',
	function ( $c ) {
		$c[] = rmt_is_en() ? 'lang-en' : 'lang-ar';
		return $c;
	}
);

/**
 * URL of the same page in the other language.
 *
 * @param string $lang Target language.
 * @return string
 */
function rmt_alt_url( $lang ) {
	$key = rmt_current_key();
	if ( ! $key ) {
		$key = 'home';
	}
	return rmt_page_url( $key, $lang );
}
