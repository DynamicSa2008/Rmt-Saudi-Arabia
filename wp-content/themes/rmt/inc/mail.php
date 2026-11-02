<?php
/**
 * Reliable email: optional SMTP transport, From headers, failure capture
 * and a "save & send test email" action for the control panel.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

/** Configure PHPMailer for SMTP when enabled in the panel. */
add_action(
	'phpmailer_init',
	function ( $mailer ) {
		$c = rmt_get( 'contact' );
		if ( '1' !== $c['smtp_on'] || empty( $c['smtp_host'] ) ) {
			return;
		}
		$mailer->isSMTP();
		$mailer->Host       = $c['smtp_host']; // phpcs:ignore
		$mailer->Port       = (int) ( $c['smtp_port'] ? $c['smtp_port'] : 587 ); // phpcs:ignore
		$mailer->SMTPSecure = 'none' === $c['smtp_secure'] ? '' : $c['smtp_secure']; // phpcs:ignore
		$mailer->SMTPAutoTLS = 'none' !== $c['smtp_secure']; // phpcs:ignore
		if ( ! empty( $c['smtp_user'] ) ) {
			$mailer->SMTPAuth = true; // phpcs:ignore
			$mailer->Username = $c['smtp_user']; // phpcs:ignore
			$mailer->Password = $c['smtp_pass']; // phpcs:ignore
		}
		$mailer->Timeout = 20; // phpcs:ignore
		$mailer->CharSet = 'UTF-8'; // phpcs:ignore
	}
);

/** From address / name. */
add_filter(
	'wp_mail_from',
	function ( $from ) {
		$c = rmt_get( 'contact' );
		$f = $c['from_email'] ? $c['from_email'] : ( '1' === $c['smtp_on'] && is_email( $c['smtp_user'] ) ? $c['smtp_user'] : '' );
		if ( ! $f ) {
			// WordPress defaults to wordpress@<host>, which is invalid on local/IP
			// hosts and often rejected as spoofed. Fall back to the company email.
			$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
			$f    = ( false === strpos( $host, '.' ) || preg_match( '/\.(local|test|localhost)$/', $host ) || ! is_email( $from ) )
				? rmt_get( 'general', 'email_primary' )
				: $from;
		}
		return is_email( $f ) ? $f : $from;
	}
);
add_filter(
	'wp_mail_from_name',
	function ( $name ) {
		$n = rmt_get( 'contact', 'from_name' );
		return $n ? $n : $name;
	}
);

/** Remember the last mail error so we can show it. */
add_action(
	'wp_mail_failed',
	function ( $err ) {
		$GLOBALS['rmt_mail_error'] = is_wp_error( $err ) ? $err->get_error_message() : 'unknown';
		update_option( 'rmt_last_mail_error', array( 'time' => time(), 'msg' => $GLOBALS['rmt_mail_error'] ), false );
	}
);

/** Recipients list from the panel. */
function rmt_mail_recipients() {
	$to = array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', (string) rmt_get( 'contact', 'recipients' ) ) ) ) );
	return $to ? array_values( $to ) : array( get_option( 'admin_email' ) );
}

/** Save the panel, then send a test email and report the result. */
function rmt_test_mail() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( 'غير مسموح.' );
	}
	check_admin_referer( 'rmt_save_panel' );
	$lang  = isset( $_POST['rmt_lang'] ) && 'en' === $_POST['rmt_lang'] ? 'en' : 'ar';
	$input = isset( $_POST['rmt'] ) && is_array( $_POST['rmt'] ) ? $_POST['rmt'] : array(); // phpcs:ignore -- sanitized in rmt_sanitize_options.
	update_option( RMT_OPTION, rmt_sanitize_options( $input, $lang ), true );

	$GLOBALS['rmt_mail_error'] = '';
	$ok = wp_mail(
		rmt_mail_recipients(),
		'رسالة تجريبية من موقع ' . rmt_get( 'general', 'short_name' ),
		"هذه رسالة تجريبية للتأكد من وصول طلبات عرض السعر.\nإذا وصلتك فالإعدادات سليمة ✔\n\n" . home_url( '/' ),
		array( 'Content-Type: text/plain; charset=UTF-8' )
	);
	$msg = $ok ? 'mail_ok' : 'mail_fail';
	set_transient( 'rmt_mail_test', array( 'ok' => $ok, 'err' => $GLOBALS['rmt_mail_error'], 'to' => implode( ', ', rmt_mail_recipients() ) ), 300 );
	wp_safe_redirect( admin_url( 'admin.php?page=rmt-panel&lang=' . $lang . '&msg=' . $msg . '#contact' ) );
	exit;
}
add_action( 'admin_post_rmt_test_mail', 'rmt_test_mail' );
