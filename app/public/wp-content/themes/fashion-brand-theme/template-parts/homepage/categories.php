<?php
/**
 * Homepage scene 03 — Categories / The Capsule.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$categories            = fashion_brand_theme_get_homepage_categories();
$category_image        = fashion_brand_theme_homepage_image_uri( 'categories/categories-rack-01.webp' );
$category_image_mobile = fashion_brand_theme_homepage_image_uri( 'categories/categories-rack-01-mobile.webp' );
?>
<section
	class="scene cat-scene"
	data-title="<?php echo esc_attr__( 'The Capsule', 'fashion-brand-theme' ); ?>"
	aria-labelledby="homepage-categories-heading"
>
	<picture>
		<source media="(max-width: 860px)" srcset="<?php echo esc_url( $category_image_mobile ); ?>">
		<img
			class="scene-img"
			src="<?php echo esc_url( $category_image ); ?>"
			alt=""
			width="1200"
			height="1600"
			loading="lazy"
			decoding="async"
		>
	</picture>
	<div class="scene-top-scrim" aria-hidden="true"></div>
	<div class="scene-scrim scrim-full" aria-hidden="true"></div>
	<div class="scene-copy">
		<p class="scene-eyebrow"><?php esc_html_e( 'The Capsule', 'fashion-brand-theme' ); ?></p>
		<h2 id="homepage-categories-heading" class="screen-reader-text">
			<?php esc_html_e( 'Shop by category', 'fashion-brand-theme' ); ?>
		</h2>
		<div class="cat-list">
			<?php foreach ( $categories as $slug => $label ) : ?>
				<a
					class="cat-item"
					href="<?php echo esc_url( fashion_brand_theme_get_product_category_url( $slug ) ); ?>"
					data-cursor-label="<?php esc_attr_e( 'Shop', 'fashion-brand-theme' ); ?>"
				>
					<span class="cat-name"><?php echo esc_html( $label ); ?></span>
					<span class="cat-arrow" aria-hidden="true">→</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
