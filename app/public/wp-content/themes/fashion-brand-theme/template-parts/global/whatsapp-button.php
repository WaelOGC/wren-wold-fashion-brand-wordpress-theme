<?php
/**
 * Floating WhatsApp contact button.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fashion_brand_theme_should_render_whatsapp_button' ) || ! fashion_brand_theme_should_render_whatsapp_button() ) {
	return;
}

$url = fashion_brand_theme_get_whatsapp_url();

if ( '' === $url ) {
	return;
}
?>
<a
	class="whatsapp-float"
	href="<?php echo esc_url( $url ); ?>"
	target="_blank"
	rel="noopener noreferrer"
	aria-label="<?php esc_attr_e( 'Chat on WhatsApp', 'fashion-brand-theme' ); ?>"
	data-whatsapp-float
	data-float-slot="whatsapp"
>
	<?php echo fashion_brand_theme_get_whatsapp_glyph_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG. ?>
</a>
