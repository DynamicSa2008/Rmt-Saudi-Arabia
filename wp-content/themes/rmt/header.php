<?php
/**
 * Header.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
$g = rmt_get( 'general' );
?><!doctype html>
<html <?php language_attributes(); ?>>
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
<div class="loader" aria-hidden="true">
	<div class="loader-in">
		<div class="loader-mark">
			<svg class="loader-ring" viewBox="0 0 120 120" aria-hidden="true"><rect x="22" y="22" width="76" height="76" rx="16" transform="rotate(45 60 60)" pathLength="100"/></svg>
			<img src="<?php echo esc_url( rmt_logo_url( 'small' ) ); ?>" alt="" width="86" height="86">
		</div>
		<span class="loader-name"><?php echo esc_html( $g['company_name'] ); ?></span>
		<span class="loader-bar"><i></i></span>
	</div>
</div>
<a class="skip-link" href="#main"><?php rmt_te( 'تخطَّ إلى المحتوى' ); ?></a>

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
		<a class="brand" href="<?php echo esc_url( rmt_page_url( 'home' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $g['company_name'] . ' – ' . rmt_t( 'الرئيسية' ) ); ?>">
			<img src="<?php echo esc_url( rmt_logo_url( 'small' ) ); ?>" alt="<?php echo esc_attr( rmt_t( 'شعار ' ) . $g['company_name'] ); ?>" width="56" height="56">
			<span class="brand-txt">
				<strong><?php echo esc_html( $g['short_name'] ); ?></strong>
				<small><?php rmt_te( 'للمقاولات والنظافة' ); ?></small>
			</span>
		</a>

		<nav class="main-nav" id="main-nav" aria-label="<?php echo esc_attr( rmt_t( 'القائمة الرئيسية' ) ); ?>">
			<?php rmt_nav( 'nav' ); ?>
			<div class="nav-mobile-extra">
				<?php rmt_cta_buttons(); ?>
				<a class="nav-lang" href="<?php echo esc_url( rmt_alt_url( rmt_is_en() ? 'ar' : 'en' ) ); ?>" hreflang="<?php echo rmt_is_en() ? 'ar' : 'en'; ?>" lang="<?php echo rmt_is_en() ? 'ar' : 'en'; ?>"><?php echo rmt_is_en() ? 'التصفح بالعربية' : 'Browse in English'; ?></a>
				<div class="nav-vision"><?php rmt_vision_badge( 'menu' ); ?></div>
			</div>
		</nav>

		<div class="header-cta">
			<a class="btn btn-sm btn-wa hide-sm" href="<?php echo esc_url( rmt_whatsapp_url() ); ?>" target="_blank" rel="noopener" data-track="whatsapp"><?php echo rmt_icon( 'whatsapp', 18 ); // phpcs:ignore ?><?php rmt_te( 'واتساب' ); ?></a>
			<a class="btn btn-sm btn-primary hide-xs" href="<?php echo esc_url( rmt_page_url( 'contact' ) ); ?>#quote"><?php rmt_te( 'عرض سعر' ); ?></a>
			<?php rmt_vision_badge( 'header' ); ?>
			<a class="lang-switch" href="<?php echo esc_url( rmt_alt_url( rmt_is_en() ? 'ar' : 'en' ) ); ?>" hreflang="<?php echo rmt_is_en() ? 'ar' : 'en'; ?>" lang="<?php echo rmt_is_en() ? 'ar' : 'en'; ?>" aria-label="<?php echo esc_attr( rmt_is_en() ? 'التصفح بالعربية' : 'Browse in English' ); ?>"><?php echo rmt_is_en() ? 'عربي' : 'EN'; ?></a>
			<button class="burger" type="button" aria-controls="main-nav" aria-expanded="false" aria-label="<?php echo esc_attr( rmt_t( 'فتح القائمة' ) ); ?>" data-burger><span></span><span></span><span></span></button>
		</div>
	</div>
</header>
<main id="main" tabindex="-1">
