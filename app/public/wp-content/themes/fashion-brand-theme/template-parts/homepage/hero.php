<?php
/**
 * Homepage scene 01 — Hero / Quiet Character.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero_image        = fashion_brand_theme_homepage_image_uri( 'hero/hero-editorial-01.webp' );
$hero_image_mobile = fashion_brand_theme_homepage_image_uri( 'hero/hero-editorial-01-mobile.webp' );
?>
<section
	class="scene"
	data-title="<?php echo esc_attr( fashion_brand_theme_get_homepage_text( 'homepage_hero_eyebrow' ) ); ?>"
	aria-labelledby="homepage-hero-heading"
>
	<picture>
		<source media="(max-width: 860px)" srcset="<?php echo esc_url( $hero_image_mobile ); ?>">
		<img
			class="scene-img"
			src="<?php echo esc_url( $hero_image ); ?>"
			alt=""
			width="1600"
			height="2000"
			decoding="async"
			fetchpriority="high"
		>
	</picture>
	<div class="scene-top-scrim" aria-hidden="true"></div>
	<div class="scene-scrim scrim-bottom" aria-hidden="true"></div>
	<div class="scene-copy">
		<p class="scene-eyebrow"><?php echo esc_html( fashion_brand_theme_get_homepage_text( 'homepage_hero_eyebrow' ) ); ?></p>
		<h1 id="homepage-hero-heading" class="scene-headline">
			<?php echo esc_html( fashion_brand_theme_get_homepage_text( 'homepage_hero_heading' ) ); ?>
		</h1>
		<p class="scene-sub">
			<?php echo esc_html( fashion_brand_theme_get_homepage_text( 'homepage_hero_text' ) ); ?>
		</p>
		<a
			class="cinematic-link"
			href="<?php echo esc_url( fashion_brand_theme_get_homepage_url( 'homepage_hero_cta_url', fashion_brand_theme_get_shop_url() ) ); ?>"
		>
			<?php echo esc_html( fashion_brand_theme_get_homepage_text( 'homepage_hero_cta_label' ) ); ?>
		</a>
	</div>
</section>
