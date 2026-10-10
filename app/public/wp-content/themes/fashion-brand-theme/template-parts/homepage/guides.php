<?php
/**
 * Homepage scene 05 — Guides / Make Better Decisions.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$guide_teasers         = fashion_brand_theme_get_homepage_guide_teasers();
$guides_url            = fashion_brand_theme_get_page_url( 'guides' );
$guides_image          = fashion_brand_theme_homepage_image_uri( 'guides/guides-canal-01.webp' );
$guides_image_mobile   = fashion_brand_theme_homepage_image_uri( 'guides/guides-canal-01-mobile.webp' );
?>
<section
	class="scene guides-scene"
	data-title="<?php echo esc_attr__( 'Make Better Decisions', 'fashion-brand-theme' ); ?>"
	aria-labelledby="homepage-guides-heading"
>
	<picture>
		<source media="(max-width: 860px)" srcset="<?php echo esc_url( $guides_image_mobile ); ?>">
		<img
			class="scene-img"
			src="<?php echo esc_url( $guides_image ); ?>"
			alt=""
			width="1400"
			height="1050"
			loading="lazy"
			decoding="async"
		>
	</picture>
	<div class="scene-top-scrim" aria-hidden="true"></div>
	<div class="scene-scrim scrim-side" aria-hidden="true"></div>
	<div class="scene-copy">
		<p class="scene-eyebrow"><?php esc_html_e( 'Make Better Decisions', 'fashion-brand-theme' ); ?></p>
		<h2 id="homepage-guides-heading" class="scene-headline scene-headline--guides">
			<?php esc_html_e( 'Make better clothing decisions', 'fashion-brand-theme' ); ?>
		</h2>
		<?php foreach ( $guide_teasers as $guide ) : ?>
			<a class="guide-entry" href="<?php echo esc_url( $guides_url ); ?>" data-cursor-label="<?php esc_attr_e( 'Read', 'fashion-brand-theme' ); ?>">
				<span class="guide-title"><?php echo esc_html( $guide['title'] ); ?></span>
				<span class="guide-desc"><?php echo esc_html( $guide['excerpt'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
