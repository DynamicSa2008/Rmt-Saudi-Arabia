<?php
/**
 * RMT theme bootstrap.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

$rmt_inc = get_template_directory() . '/inc/';
require $rmt_inc . 'defaults.php';
require $rmt_inc . 'defaults-en.php';
require $rmt_inc . 'i18n.php';
require $rmt_inc . 'options.php';
require $rmt_inc . 'setup.php';
require $rmt_inc . 'template-tags.php';
require $rmt_inc . 'seo.php';
require $rmt_inc . 'mail.php';
require $rmt_inc . 'legal.php';
require $rmt_inc . 'leads.php';
if ( is_admin() ) {
	require $rmt_inc . 'admin.php';
}
