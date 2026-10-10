<?php
/**
 * Cinematic homepage chrome — brand mark, utilities, overlay nav.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url        = fashion_brand_theme_get_shop_url();
$collections_url = fashion_brand_theme_get_page_url( 'collections' );
$guides_url      = fashion_brand_theme_get_page_url( 'guides' );
$about_url       = fashion_brand_theme_get_page_url( 'about' );
$contact_url     = fashion_brand_theme_get_page_url( 'contact' );
$account_url     = fashion_brand_theme_get_account_url();
$cart_url        = fashion_brand_theme_get_cart_url();
$cart_count      = fashion_brand_theme_get_cart_count();
$monogram_uri    = fashion_brand_theme_get_monogram_uri( 'white' );
$label           = fashion_brand_theme_get_display_label();

$preview_shop         = fashion_brand_theme_homepage_image_uri( 'hero/hero-editorial-01.jpg' );
$preview_collections  = fashion_brand_theme_homepage_image_uri( 'featured-collection/featured-collection-golden-hour-01.jpg' );
$preview_guides       = fashion_brand_theme_homepage_image_uri( 'guides/guides-flatlay-01.jpg' );
$preview_about        = fashion_brand_theme_homepage_image_uri( 'philosophy/philosophy-detail-01.jpg' );
$preview_contact      = fashion_brand_theme_homepage_image_uri( 'closing-cta/closing-cta-departure-01.jpg' );
?>
<a class="cinematic-brand-mark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
	<?php if ( $monogram_uri ) : ?>
		<img
			class="cinematic-brand-mark__glyph"
			src="<?php echo esc_url( $monogram_uri ); ?>"
			alt=""
			width="30"
			height="30"
			decoding="async"
		>
	<?php endif; ?>
	<span class="cinematic-brand-mark__wordmark"><?php echo esc_html( $label ); ?></span>
</a>

<div class="cinematic-utility">
	<a
		class="cinematic-utility__link"
		href="<?php echo esc_url( $account_url ); ?>"
		aria-label="<?php esc_attr_e( 'Account', 'fashion-brand-theme' ); ?>"
	>
		<?php echo fashion_brand_theme_get_icon( 'user', array( 'width' => 19, 'height' => 19, 'class' => 'cinematic-utility__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>

	<a
		class="cinematic-utility__link cinematic-cart"
		href="<?php echo esc_url( $cart_url ); ?>"
		aria-label="<?php esc_attr_e( 'Cart', 'fashion-brand-theme' ); ?>"
	>
		<?php echo fashion_brand_theme_get_icon( 'shopping-bag', array( 'width' => 19, 'height' => 19, 'class' => 'cinematic-utility__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php fashion_brand_theme_render_cinematic_cart_count( $cart_count ); ?>
	</a>

	<button type="button" class="cinematic-menu-btn" aria-controls="cinematic-overlay-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'fashion-brand-theme' ); ?>">
		<span></span>
		<span></span>
		<span></span>
	</button>
</div>

<div class="cinematic-counter" aria-live="polite">
	<b>01</b> / 06 — Quiet Character
</div>

<nav id="cinematic-overlay-nav" class="cinematic-overlay-nav" aria-label="<?php esc_attr_e( 'Site', 'fashion-brand-theme' ); ?>" aria-hidden="true">
	<button type="button" class="cinematic-overlay-close" aria-label="<?php esc_attr_e( 'Close menu', 'fashion-brand-theme' ); ?>">
		<?php esc_html_e( 'Close', 'fashion-brand-theme' ); ?>
	</button>

	<a
		href="<?php echo esc_url( $shop_url ); ?>"
		data-preview-img="<?php echo esc_url( $preview_shop ); ?>"
		data-preview-caption="<?php echo esc_attr__( 'Explore the full capsule', 'fashion-brand-theme' ); ?>"
	><?php esc_html_e( 'Shop', 'fashion-brand-theme' ); ?></a>
	<a
		href="<?php echo esc_url( $collections_url ); ?>"
		data-preview-img="<?php echo esc_url( $preview_collections ); ?>"
		data-preview-caption="<?php echo esc_attr__( 'The Seasonal Edit', 'fashion-brand-theme' ); ?>"
	><?php esc_html_e( 'Collections', 'fashion-brand-theme' ); ?></a>
	<a
		href="<?php echo esc_url( $guides_url ); ?>"
		data-preview-img="<?php echo esc_url( $preview_guides ); ?>"
		data-preview-caption="<?php echo esc_attr__( 'Make better clothing decisions', 'fashion-brand-theme' ); ?>"
	><?php esc_html_e( 'Guides', 'fashion-brand-theme' ); ?></a>
	<a
		href="<?php echo esc_url( $about_url ); ?>"
		data-preview-img="<?php echo esc_url( $preview_about ); ?>"
		data-preview-caption="<?php echo esc_attr__( 'Quality over quantity', 'fashion-brand-theme' ); ?>"
	><?php esc_html_e( 'About', 'fashion-brand-theme' ); ?></a>
	<a
		href="<?php echo esc_url( $contact_url ); ?>"
		data-preview-img="<?php echo esc_url( $preview_contact ); ?>"
		data-preview-caption="<?php echo esc_attr__( 'Get in touch', 'fashion-brand-theme' ); ?>"
	><?php esc_html_e( 'Contact', 'fashion-brand-theme' ); ?></a>

	<div class="cinematic-overlay-preview" aria-hidden="true">
		<img src="" alt="" width="320" height="400" decoding="async">
		<p class="cinematic-overlay-preview__caption"></p>
	</div>

	<p class="cinematic-overlay-foot"><?php echo esc_html( $label ); ?></p>
</nav>
