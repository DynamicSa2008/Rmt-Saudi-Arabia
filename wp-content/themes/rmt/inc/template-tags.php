<?php
/**
 * Template helpers: icons, responsive images, section heads, page hero.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline SVG icon (stroke icons, 24x24, currentColor).
 *
 * @param string $name Icon key.
 * @param int    $size Pixel size.
 * @return string
 */
function rmt_icon( $name, $size = 24 ) {
	$p = array(
		'glass'    => '<rect x="4" y="3" width="16" height="18" rx="1"/><path d="M12 3v18M4 12h16M7 6l3 3M14 14l3 3"/>',
		'rope'     => '<path d="M12 2v8"/><circle cx="12" cy="13" r="3"/><path d="M9 21l3-5 3 5M5 2h14"/>',
		'tower'    => '<path d="M7 22V6l5-4 5 4v16M4 22h16M10 9h4M10 13h4M10 17h4"/>',
		'paint'    => '<rect x="3" y="3" width="14" height="6" rx="1"/><path d="M17 6h3v5h-8v3"/><rect x="10" y="14" width="4" height="8" rx="1"/>',
		'home'     => '<path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10M10 20v-6h4v6"/>',
		'sofa'     => '<path d="M4 11V8a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v3"/><path d="M2 13a2 2 0 0 1 4 0v2h12v-2a2 2 0 0 1 4 0v5H2zM5 18v2M19 18v2"/>',
		'bug'      => '<rect x="8" y="7" width="8" height="12" rx="4"/><path d="M12 7V4M9 4l1 3M15 4l-1 3M4 11h4M16 11h4M4 17l4-2M20 17l-4-2"/>',
		'fan'      => '<circle cx="12" cy="12" r="2"/><path d="M12 10c0-4 1-7 4-7s2 5-2 8M14 12c4 0 7 1 7 4s-5 2-8-2M12 14c0 4-1 7-4 7s-2-5 2-8M10 12c-4 0-7-1-7-4s5-2 8 2"/>',
		'flame'    => '<path d="M12 22c4 0 7-3 7-7 0-5-5-7-5-12-3 2-5 5-5 8-1-1-2-2-2-4-2 2-2 5-2 8 0 4 3 7 7 7z"/>',
		'ship'     => '<path d="M3 17l2 4h14l2-4H3zM5 17V11h14v6M9 11V5h6v6M12 2v3"/>',
		'contract' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/>',
		'shield'   => '<path d="M12 2l8 3v6c0 5-3.5 9.5-8 11-4.5-1.5-8-6-8-11V5z"/><path d="M9 12l2 2 4-4"/>',
		'star'     => '<path d="M12 2l3 6.5 7 .9-5.2 4.8 1.4 7L12 17.8 5.8 21.2l1.4-7L2 9.4l7-.9z"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'building' => '<rect x="4" y="2" width="16" height="20" rx="1"/><path d="M9 22v-4h6v4M8 6h2M14 6h2M8 10h2M14 10h2M8 14h2M14 14h2"/>',
		'phone'    => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
		'mail'     => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/>',
		'pin'      => '<path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/>',
		'fax'      => '<rect x="6" y="2" width="12" height="7"/><rect x="3" y="9" width="18" height="9" rx="2"/><rect x="6" y="15" width="12" height="7"/>',
		'arrow'    => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
		'check'    => '<path d="M20 6L9 17l-5-5"/>',
		'play'     => '<path d="M7 4v16l13-8z" fill="currentColor"/>',
		'close'    => '<path d="M6 6l12 12M18 6L6 18"/>',
		'menu'     => '<path d="M3 6h18M3 12h18M3 18h18"/>',
		'plus'     => '<path d="M12 5v14M5 12h14"/>',
		'x'        => '<path d="M4 4l16 16M20 4L4 20"/>',
		'instagram'=> '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>',
		'snapchat' => '<path d="M12 3c3 0 5 2.2 5 5v3l2 .5c.6.2.6 1 0 1.2l-2 .8c.6 1.6 2 2.8 3.5 3.2-.3.8-1.5 1-2.5 1.2-.3.7-.3 1.4-1.1 1.4-.9 0-1.8-.6-3 .2-.8.6-1.3 1.5-1.9 1.5s-1.1-.9-1.9-1.5c-1.2-.8-2.1-.2-3-.2-.8 0-.8-.7-1.1-1.4-1-.2-2.2-.4-2.5-1.2 1.5-.4 2.9-1.6 3.5-3.2l-2-.8c-.6-.2-.6-1 0-1.2L7 11V8c0-2.8 2-5 5-5z"/>',
		'tiktok'   => '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5M14 3c.5 2.5 2.5 4.5 5 5"/>',
		'linkedin' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/>',
		'youtube'  => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="M10 9l5 3-5 3z" fill="currentColor"/>',
	);
	if ( 'whatsapp' === $name ) {
		return '<svg class="ico" width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.4-.5c.2-.2.2-.3.3-.5.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.2-.3-.3-.6-.4zM12 21.8c-1.8 0-3.5-.5-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4A9.8 9.8 0 0 1 12 2.2a9.8 9.8 0 0 1 9.8 9.8c0 5.4-4.4 9.8-9.8 9.8zM20.5 3.5A11.8 11.8 0 0 0 1.7 17.6L0 24l6.5-1.7a11.8 11.8 0 0 0 17.3-10.4c0-3.1-1.2-6.1-3.3-8.4z"/></svg>';
	}
	$path = isset( $p[ $name ] ) ? $p[ $name ] : $p['star'];
	return '<svg class="ico' . ( 'arrow' === $name ? ' ico-arrow' : '' ) . '" width="' . (int) $size . '" height="' . (int) $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $path . '</svg>';
}

/**
 * Responsive <img>. Uses WP srcset for library uploads, WebP <picture> for
 * bundled theme images. Always lazy unless $eager.
 *
 * @param string $url   Image URL.
 * @param string $alt   Alt text.
 * @param string $class CSS class.
 * @param bool   $eager Load eagerly (above the fold).
 * @param string $sizes sizes attribute.
 * @return string
 */
function rmt_img( $url, $alt = '', $class = '', $eager = false, $sizes = '(max-width: 768px) 100vw, 50vw' ) {
	if ( ! $url ) {
		return '';
	}
	$load = $eager ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"';
	$id   = attachment_url_to_postid( $url );
	if ( $id ) {
		return wp_get_attachment_image(
			$id,
			'large',
			false,
			array(
				'alt'      => $alt,
				'class'    => $class,
				'sizes'    => $sizes,
				'loading'  => $eager ? 'eager' : 'lazy',
				'decoding' => 'async',
			)
		);
	}
	$dims = '';
	$theme_uri = get_template_directory_uri();
	if ( 0 === strpos( $url, $theme_uri ) ) {
		$file = get_template_directory() . substr( $url, strlen( $theme_uri ) );
		if ( file_exists( $file ) ) {
			$s = @getimagesize( $file ); // phpcs:ignore
			if ( $s ) {
				$dims = ' width="' . (int) $s[0] . '" height="' . (int) $s[1] . '"';
			}
			$webp = preg_replace( '/\.jpe?g$/i', '.webp', $file );
			if ( $webp !== $file && file_exists( $webp ) ) {
				return '<picture><source type="image/webp" srcset="' . esc_url( preg_replace( '/\.jpe?g$/i', '.webp', $url ) ) . '"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" class="' . esc_attr( $class ) . '"' . $dims . ' ' . $load . ' decoding="async"></picture>';
			}
		}
	}
	return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" class="' . esc_attr( $class ) . '"' . $dims . ' ' . $load . ' decoding="async">';
}

/** Najdi / Sadu-inspired ornament (light Saudi touch). */
function rmt_ornament( $class = '' ) {
	return '<span class="ornament ' . esc_attr( $class ) . '" aria-hidden="true"><i></i><b></b><i></i></span>';
}

/**
 * Section heading block.
 *
 * @param string $title  Heading text.
 * @param string $text   Lead text.
 * @param string $tag    h2 / h1.
 * @param string $eyebrow Small label above.
 */
function rmt_section_head( $title, $text = '', $tag = 'h2', $eyebrow = '' ) {
	echo '<header class="sec-head reveal">';
	if ( $eyebrow ) {
		echo '<span class="eyebrow">' . esc_html( $eyebrow ) . '</span>';
	}
	printf( '<%1$s class="sec-title">%2$s</%1$s>', tag_escape( $tag ), esc_html( $title ) );
	echo rmt_ornament(); // phpcs:ignore
	if ( $text ) {
		echo '<p class="sec-lead">' . esc_html( $text ) . '</p>';
	}
	echo '</header>';
}

/**
 * Inner page hero with breadcrumbs + H1.
 *
 * @param string $key   Page key.
 * @param string $title H1.
 * @param string $lead  Intro text.
 */
function rmt_page_hero( $key, $title, $lead = '' ) {
	$items = rmt_nav_items();
	?>
	<section class="page-hero">
		<div class="page-hero-bg" aria-hidden="true"></div>
		<div class="container">
			<nav class="crumbs" aria-label="<?php echo esc_attr( rmt_t( 'مسار التنقل' ) ); ?>">
				<a href="<?php echo esc_url( rmt_page_url( 'home' ) ); ?>"><?php rmt_te( 'الرئيسية' ); ?></a>
				<span aria-hidden="true">›</span>
				<span aria-current="page"><?php echo esc_html( isset( $items[ $key ] ) ? $items[ $key ] : $title ); ?></span>
			</nav>
			<h1 class="page-title"><?php echo esc_html( $title ); ?></h1>
			<?php echo rmt_ornament( 'light' ); // phpcs:ignore ?>
			<?php if ( $lead ) : ?>
				<p class="page-lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/** Contact buttons pair. */
function rmt_cta_buttons( $light = false ) {
	?>
	<div class="btns">
		<a class="btn btn-primary" href="<?php echo esc_url( rmt_page_url( 'contact' ) ); ?>#quote"><?php echo rmt_icon( 'contract', 20 ); // phpcs:ignore ?><?php rmt_e( 'home', 'btn_quote' ); ?></a>
		<a class="btn btn-wa" href="<?php echo esc_url( rmt_whatsapp_url() ); ?>" target="_blank" rel="noopener" data-track="whatsapp"><?php echo rmt_icon( 'whatsapp', 20 ); // phpcs:ignore ?><?php rmt_e( 'home', 'btn_whatsapp' ); ?></a>
		<a class="btn <?php echo $light ? 'btn-ghost-light' : 'btn-ghost'; ?>" href="tel:<?php echo esc_attr( rmt_tel() ); ?>" data-track="call"><?php echo rmt_icon( 'phone', 20 ); // phpcs:ignore ?><span dir="ltr"><?php echo esc_html( rmt_phone_display() ); ?></span></a>
	</div>
	<?php
}

/**
 * Portfolio gallery grid (images + videos with lightbox).
 *
 * @param int $limit 0 = all.
 */
function rmt_gallery( $limit = 0 ) {
	$items = (array) rmt_get( 'projects', 'items' );
	if ( $limit ) {
		$items = array_slice( $items, 0, $limit );
	}
	$has_img = false;
	$has_vid = false;
	foreach ( $items as $it ) {
		if ( 'video' === $it['type'] ) {
			$has_vid = true;
		} else {
			$has_img = true;
		}
	}
	if ( $has_img && $has_vid ) {
		echo '<div class="filters reveal" role="tablist" aria-label="' . esc_attr( rmt_t( 'تصفية الأعمال' ) ) . '"><button class="chip is-active" data-filter="all" role="tab" aria-selected="true">' . esc_html( rmt_t( 'الكل' ) ) . '</button><button class="chip" data-filter="video" role="tab" aria-selected="false">' . esc_html( rmt_t( 'فيديو' ) ) . '</button><button class="chip" data-filter="image" role="tab" aria-selected="false">' . esc_html( rmt_t( 'صور' ) ) . '</button></div>';
	}
	echo '<div class="gallery" data-gallery>';
	foreach ( $items as $i => $it ) {
		$is_vid = 'video' === $it['type'];
		$thumb  = $is_vid ? $it['poster'] : $it['src'];
		$alt    = $it['title'] . ' – ' . rmt_get( 'general', 'short_name' ) . ( $it['place'] ? ' – ' . $it['place'] : '' );
		printf(
			'<figure class="g-item reveal" data-type="%1$s" style="--d:%6$dms"><button type="button" class="g-open" data-src="%2$s" data-kind="%1$s" data-poster="%3$s" data-title="%4$s" aria-label="' . esc_attr( rmt_t( 'عرض:' ) ) . ' %4$s">%5$s',
			esc_attr( $is_vid ? 'video' : 'image' ),
			esc_url( $it['src'] ),
			esc_url( $it['poster'] ),
			esc_attr( $it['title'] ),
			$thumb ? rmt_img( $thumb, $alt, 'g-thumb', false, '(max-width:600px) 50vw, 25vw' ) : '<span class="g-thumb g-ph"></span>', // phpcs:ignore
			( $i % 4 ) * 80
		);
		if ( $is_vid ) {
			echo '<span class="g-play">' . rmt_icon( 'play', 22 ) . '</span>'; // phpcs:ignore
		}
		echo '</button><figcaption><strong>' . esc_html( $it['title'] ) . '</strong>' . ( $it['place'] ? '<span>' . rmt_icon( 'pin', 14 ) . esc_html( $it['place'] ) . '</span>' : '' ) . '</figcaption></figure>'; // phpcs:ignore
	}
	echo '</div>';
}

/**
 * Client logo/name card.
 *
 * @param array $c Client row.
 */
function rmt_client_card( $c ) {
	$inner = $c['logo']
		? rmt_img( $c['logo'], rmt_t( 'شعار ' ) . $c['name'], 'c-logo', false, '200px' )
		: '<span class="c-mono" aria-hidden="true">' . esc_html( mb_strtoupper( mb_substr( preg_replace( array( '/^(شركة|فندق|مجموعة|أمانة منطقة|مجمعات|مستشفيات|بنك|The|Al)\s+/u', '/^ال(?=\S{2,})/u', '/^Al[ -]/u' ), '', $c['name'] ), 0, 1 ) ) ) . '</span>';
	$tag   = $c['url'] ? 'a' : 'div';
	$href  = $c['url'] ? ' href="' . esc_url( $c['url'] ) . '" target="_blank" rel="noopener nofollow"' : '';
	printf(
		'<%1$s class="client%4$s"%2$s title="%3$s">%5$s<span class="c-name">%3$s</span></%1$s>',
		$tag, // phpcs:ignore
		$href, // phpcs:ignore
		esc_attr( $c['name'] ),
		$c['logo'] ? ' has-logo' : '',
		$inner // phpcs:ignore
	);
}

/**
 * Saudi Vision 2030 badge. Uses the official logo uploaded from the panel;
 * until then shows a text badge (the official mark is never redrawn).
 *
 * @param string $context header|footer.
 */
function rmt_vision_badge( $context = 'header' ) {
	if ( '1' !== rmt_get( 'general', 'show_vision' ) ) {
		return;
	}
	$logo  = rmt_get( 'general', 'vision_logo' );
	$light = rmt_get( 'general', 'vision_logo_light' );
	$src   = ( 'footer' === $context && $light ) ? $light : $logo;
	$label = rmt_t( 'رؤية المملكة 2030' );
	$cls   = 'v2030 v2030-' . $context . ( $src ? ' has-logo' : '' ) . ( 'footer' === $context && $logo && ! $light ? ' on-white' : '' );
	echo '<a class="' . esc_attr( $cls ) . '" href="https://www.vision2030.gov.sa/" target="_blank" rel="noopener" title="' . esc_attr( $label ) . '">';
	if ( $src ) {
		echo '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $label ) . '" height="44" loading="' . ( 'header' === $context ? 'eager' : 'lazy' ) . '" decoding="async">';
	} else {
		echo '<span class="v2030-txt"><small>' . esc_html( rmt_t( 'رؤية المملكة' ) ) . '</small><b>2030</b></span>';
	}
	echo '</a>';
}
