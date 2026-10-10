<?php
/**
 * Homepage scene 02 — Philosophy / Less, Better.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$philosophy_image        = fashion_brand_theme_homepage_image_uri( 'philosophy/philosophy-detail-01.webp' );
$philosophy_image_mobile = fashion_brand_theme_homepage_image_uri( 'philosophy/philosophy-detail-01-mobile.webp' );
$about_url               = fashion_brand_theme_get_page_url( 'about' );
?>
<section
	class="scene"
	data-title="<?php echo esc_attr__( 'Less, Better', 'fashion-brand-theme' ); ?>"
	aria-labelledby="homepage-philosophy-heading"
>
	<picture>
		<source media="(max-width: 860px)" srcset="<?php echo esc_url( $philosophy_image_mobile ); ?>">
		<img
			class="scene-img"
			src="<?php echo esc_url( $philosophy_image ); ?>"
			alt=""
			width="960"
			height="1200"
			loading="lazy"
			decoding="async"
		>
	</picture>
	<div class="scene-top-scrim" aria-hidden="true"></div>
	<div class="scene-scrim scrim-side" aria-hidden="true"></div>
	<div class="scene-copy">
		<p class="scene-eyebrow"><?php esc_html_e( 'Less, Better', 'fashion-brand-theme' ); ?></p>
		<h2 id="homepage-philosophy-heading" class="scene-headline">
			<?php esc_html_e( 'Quality over quantity.', 'fashion-brand-theme' ); ?>
		</h2>
		<p class="scene-sub">
			<?php esc_html_e( 'A wardrobe built with care — fewer pieces, chosen thoughtfully, made to work across the rhythms of a modern European life.', 'fashion-brand-theme' ); ?>
		</p>
		<a class="cinematic-link" href="<?php echo esc_url( $about_url ); ?>">
			<?php esc_html_e( 'Our approach', 'fashion-brand-theme' ); ?>
		</a>
	</div>
</section>
