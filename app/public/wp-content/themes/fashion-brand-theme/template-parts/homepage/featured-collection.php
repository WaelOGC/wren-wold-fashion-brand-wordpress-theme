<?php
/**
 * Homepage scene 04 — Featured collection / The Seasonal Edit.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$collection_image        = fashion_brand_theme_homepage_image_uri( 'featured-collection/featured-collection-urban-01.webp' );
$collection_image_mobile = fashion_brand_theme_homepage_image_uri( 'featured-collection/featured-collection-urban-01-mobile.webp' );
$collections_url         = fashion_brand_theme_get_page_url( 'collections' );
?>
<section
	class="scene"
	data-title="<?php echo esc_attr__( 'The Seasonal Edit', 'fashion-brand-theme' ); ?>"
	aria-labelledby="homepage-collection-heading"
>
	<picture>
		<source media="(max-width: 860px)" srcset="<?php echo esc_url( $collection_image_mobile ); ?>">
		<img
			class="scene-img"
			src="<?php echo esc_url( $collection_image ); ?>"
			alt=""
			width="1920"
			height="1280"
			loading="lazy"
			decoding="async"
		>
	</picture>
	<div class="scene-top-scrim" aria-hidden="true"></div>
	<div class="scene-scrim scrim-side" aria-hidden="true"></div>
	<div class="scene-copy">
		<p class="scene-eyebrow"><?php esc_html_e( 'The Seasonal Edit', 'fashion-brand-theme' ); ?></p>
		<h2 id="homepage-collection-heading" class="scene-headline">
			<?php esc_html_e( 'The seasonal edit.', 'fashion-brand-theme' ); ?>
		</h2>
		<p class="scene-sub">
			<?php esc_html_e( 'A curated selection of versatile pieces — composed for work, everyday and evening.', 'fashion-brand-theme' ); ?>
		</p>
		<a class="cinematic-link" href="<?php echo esc_url( $collections_url ); ?>">
			<?php esc_html_e( 'View all collections', 'fashion-brand-theme' ); ?>
		</a>
	</div>
</section>
