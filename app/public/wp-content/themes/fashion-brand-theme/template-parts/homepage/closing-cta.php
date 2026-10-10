<?php
/**
 * Homepage scene 06 — Closing CTA / Begin.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$closing_image        = fashion_brand_theme_homepage_image_uri( 'closing-cta/closing-cta-cafe-01.webp' );
$closing_image_mobile = fashion_brand_theme_homepage_image_uri( 'closing-cta/closing-cta-cafe-01-mobile.webp' );
?>
<section
	class="scene"
	data-title="<?php echo esc_attr__( 'Begin', 'fashion-brand-theme' ); ?>"
	aria-labelledby="homepage-cta-heading"
>
	<picture>
		<source media="(max-width: 860px)" srcset="<?php echo esc_url( $closing_image_mobile ); ?>">
		<img
			class="scene-img"
			src="<?php echo esc_url( $closing_image ); ?>"
			alt=""
			width="1920"
			height="1280"
			loading="lazy"
			decoding="async"
		>
	</picture>
	<div class="scene-top-scrim" aria-hidden="true"></div>
	<div class="scene-scrim scrim-bottom" aria-hidden="true"></div>
	<div class="scene-copy">
		<p class="scene-eyebrow"><?php esc_html_e( 'Begin', 'fashion-brand-theme' ); ?></p>
		<h2 id="homepage-cta-heading" class="scene-headline">
			<?php esc_html_e( 'Begin.', 'fashion-brand-theme' ); ?>
		</h2>
		<p class="scene-sub">
			<?php echo esc_html( fashion_brand_theme_get_homepage_text( 'homepage_cta_text' ) ); ?>
		</p>
		<div class="closing-ctas">
			<a
				class="cinematic-link"
				href="<?php echo esc_url( fashion_brand_theme_get_homepage_url( 'homepage_cta_primary_url', fashion_brand_theme_get_shop_url() ) ); ?>"
			>
				<?php esc_html_e( 'Shop WREN WOLD', 'fashion-brand-theme' ); ?>
			</a>
			<a
				class="cinematic-link"
				href="<?php echo esc_url( fashion_brand_theme_get_homepage_url( 'homepage_cta_secondary_url', fashion_brand_theme_get_page_url( 'collections' ) ) ); ?>"
			>
				<?php esc_html_e( 'View collections', 'fashion-brand-theme' ); ?>
			</a>
		</div>
	</div>
</section>
