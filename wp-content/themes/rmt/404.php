<?php
/**
 * 404.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="section e404">
	<div class="container narrow center">
		<p class="e404-num" aria-hidden="true">404</p>
		<h1 class="page-title dark"><?php rmt_te( 'الصفحة غير موجودة' ); ?></h1>
		<p class="lead"><?php rmt_te( 'ربما تم نقل الصفحة أو أن الرابط غير صحيح. يمكنك العودة للرئيسية أو التواصل معنا مباشرة.' ); ?></p>
		<div class="btns center-btns">
			<a class="btn btn-primary" href="<?php echo esc_url( rmt_page_url( 'home' ) ); ?>"><?php rmt_te( 'العودة للرئيسية' ); ?></a>
			<a class="btn btn-outline" href="<?php echo esc_url( rmt_page_url( 'services' ) ); ?>"><?php rmt_te( 'خدماتنا' ); ?></a>
		</div>
	</div>
</section>
<?php
get_footer();
