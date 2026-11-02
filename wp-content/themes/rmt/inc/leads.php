<?php
/**
 * Quote-request form: handler + private "leads" post type.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

/** Private CPT that stores every submission (backup when email fails). */
function rmt_register_leads() {
	register_post_type(
		'rmt_lead',
		array(
			'labels'          => array(
				'name'          => 'طلبات العملاء',
				'singular_name' => 'طلب',
				'menu_name'     => 'طلبات العملاء',
				'edit_item'     => 'تفاصيل الطلب',
				'search_items'  => 'بحث في الطلبات',
				'not_found'     => 'لا توجد طلبات بعد',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => false,
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
			'rewrite'         => false,
			'query_var'       => false,
		)
	);
}
add_action( 'init', 'rmt_register_leads' );

/** Extra columns in the leads list. */
function rmt_lead_columns( $cols ) {
	return array(
		'cb'          => $cols['cb'],
		'title'       => 'الاسم',
		'rmt_phone'   => 'الجوال',
		'rmt_service' => 'الخدمة',
		'rmt_city'    => 'المدينة',
		'date'        => 'التاريخ',
	);
}
add_filter( 'manage_rmt_lead_posts_columns', 'rmt_lead_columns' );

/** Column values. */
function rmt_lead_column_value( $col, $post_id ) {
	$map = array( 'rmt_phone' => '_phone', 'rmt_service' => '_service', 'rmt_city' => '_city' );
	if ( isset( $map[ $col ] ) ) {
		$v = get_post_meta( $post_id, $map[ $col ], true );
		if ( 'rmt_phone' === $col && $v ) {
			echo '<a href="tel:' . esc_attr( rmt_tel( $v ) ) . '" dir="ltr">' . esc_html( $v ) . '</a> · <a href="' . esc_url( 'https://wa.me/' . ltrim( rmt_tel( $v ), '+' ) ) . '" target="_blank">واتساب</a>';
		} else {
			echo esc_html( $v );
		}
	}
}
add_action( 'manage_rmt_lead_posts_custom_column', 'rmt_lead_column_value', 10, 2 );

/** Form fields definition (service list is built from the services in the panel). */
function rmt_form_services() {
	$list = array();
	foreach ( (array) rmt_get( 'services', 'items' ) as $s ) {
		if ( ! empty( $s['title'] ) ) {
			$list[] = $s['title'];
		}
	}
	$list[] = 'أخرى';
	return $list;
}

/** Handle submission. */
function rmt_handle_quote() {
	$back = wp_get_referer() ? wp_get_referer() : rmt_page_url( 'contact' );
	$back = remove_query_arg( array( 'sent', 'err' ), $back );

	if ( ! isset( $_POST['rmt_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rmt_nonce'] ) ), 'rmt_quote' ) ) {
		wp_safe_redirect( add_query_arg( 'err', 'nonce', $back ) . '#quote' );
		exit;
	}
	// Honeypot + minimum fill time (simple, cookie-free spam protection).
	$hp = isset( $_POST['website'] ) ? trim( (string) wp_unslash( $_POST['website'] ) ) : '';
	$ts = isset( $_POST['rmt_ts'] ) ? (int) $_POST['rmt_ts'] : 0;
	if ( '' !== $hp || ( $ts && time() - $ts < 3 ) ) {
		wp_safe_redirect( add_query_arg( 'sent', '1', $back ) . '#quote' );
		exit;
	}

	$f = array();
	foreach ( array( 'name', 'org', 'phone', 'email', 'city', 'service', 'building', 'details' ) as $k ) {
		$raw     = isset( $_POST[ $k ] ) ? wp_unslash( $_POST[ $k ] ) : ''; // phpcs:ignore
		$f[ $k ] = 'details' === $k ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
	}
	$f['email'] = sanitize_email( $f['email'] );

	if ( '' === $f['name'] || ! preg_match( '/^[0-9+\s\-]{9,16}$/', $f['phone'] ) ) {
		wp_safe_redirect( add_query_arg( 'err', 'fields', $back ) . '#quote' );
		exit;
	}

	$labels = array(
		'name'     => 'الاسم',
		'org'      => 'اسم الجهة',
		'phone'    => 'الجوال',
		'email'    => 'البريد',
		'city'     => 'المدينة',
		'service'  => 'نوع الخدمة',
		'building' => 'نوع المبنى وعدد الأدوار',
		'details'  => 'تفاصيل إضافية',
	);
	$body = '';
	foreach ( $labels as $k => $l ) {
		$body .= $l . ': ' . ( '' !== $f[ $k ] ? $f[ $k ] : '—' ) . "\n";
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'rmt_lead',
			'post_status'  => 'publish',
			'post_title'   => $f['name'] . ( $f['org'] ? ' – ' . $f['org'] : '' ),
			'post_content' => $body,
		)
	);
	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_phone', $f['phone'] );
		update_post_meta( $post_id, '_service', $f['service'] );
		update_post_meta( $post_id, '_city', $f['city'] );
	}

	$to = array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', rmt_get( 'contact', 'recipients' ) ) ) ) );
	if ( ! $to ) {
		$to = array( get_option( 'admin_email' ) );
	}
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( is_email( $f['email'] ) ) {
		$headers[] = 'Reply-To: ' . $f['name'] . ' <' . $f['email'] . '>';
	}
	wp_mail( $to, 'طلب عرض سعر جديد: ' . $f['service'] . ' – ' . $f['name'], $body . "\n— " . home_url( '/' ), $headers );

	wp_safe_redirect( add_query_arg( 'sent', '1', $back ) . '#quote' );
	exit;
}
add_action( 'admin_post_nopriv_rmt_quote', 'rmt_handle_quote' );
add_action( 'admin_post_rmt_quote', 'rmt_handle_quote' );
