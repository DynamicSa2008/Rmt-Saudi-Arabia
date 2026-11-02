<?php
/**
 * Header.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
$g = rmt_get( 'general' );
?><!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php if ( rmt_get( 'seo', 'gtm' ) ) : ?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( rmt_get( 'seo', 'gtm' ) ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php endif; ?>
<a class="skip-link" href="#main">تخطَّ إلى المحتوى</a>

<div class="topbar">
	<div class="container topbar-in">
		<div class="topbar-info">
			<a href="tel:<?php echo esc_attr( rmt_tel() ); ?>"><?php echo rmt_icon( 'phone', 15 ); // phpcs:ignore ?><span dir="ltr"><?php echo esc_html( rmt_phone_display() ); ?></span></a>
			<a href="mailto:<?php echo esc_attr( $g['email_primary'] ); ?>"><?php echo rmt_icon( 'mail', 15 ); // phpcs:ignore ?><span dir="ltr"><?php echo esc_html( $g['email_primary'] ); ?></span></a>
			<span class="hide-sm"><?php echo rmt_icon( 'pin', 15 ); // phpcs:ignore ?><?php echo esc_html( $g['city'] . ' – ' . $g['street'] ); ?></span>
		</div>
		<div class="topbar-note hide-sm"><?php echo rmt_icon( 'clock', 15 ); // phpcs:ignore ?><?php echo esc_html( $g['hours'] ); ?></div>
	</div>
</div>

<header class="site-header" data-header>
	<div class="container header-in">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $g['company_name'] ); ?> – الرئيسية">
			<img src="<?php echo esc_url( rmt_logo_url( 'small' ) ); ?>" alt="شعار <?php echo esc_attr( $g['company_name'] ); ?>" width="56" height="56">
			<span class="brand-txt">
				<strong><?php echo esc_html( $g['short_name'] ); ?></strong>
				<small>للمقاولات والنظافة</small>
			</span>
		</a>

		<nav class="main-nav" id="main-nav" aria-label="القائمة الرئيسية">
			<?php rmt_nav( 'nav' ); ?>
			<div class="nav-mobile-extra">
				<?php rmt_cta_buttons(); ?>
			</div>
		</nav>

		<div class="header-cta">
			<a class="btn btn-sm btn-wa hide-sm" href="<?php echo esc_url( rmt_whatsapp_url() ); ?>" target="_blank" rel="noopener" data-track="whatsapp"><?php echo rmt_icon( 'whatsapp', 18 ); // phpcs:ignore ?>واتساب</a>
			<a class="btn btn-sm btn-primary hide-xs" href="<?php echo esc_url( rmt_page_url( 'contact' ) ); ?>#quote">عرض سعر</a>
			<button class="burger" type="button" aria-controls="main-nav" aria-expanded="false" aria-label="فتح القائمة" data-burger><span></span><span></span><span></span></button>
		</div>
	</div>
</header>
<main id="main" tabindex="-1">
