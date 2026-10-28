<?php
/**
 * Off-canvas mini-cart drawer (backdrop + sliding panel).
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'WC' ) ) {
	return;
}
?>
<div
	id="cart-drawer"
	class="cart-drawer"
	data-cart-drawer
	hidden
>
	<div class="cart-drawer__backdrop" data-cart-drawer-close tabindex="-1"></div>
	<div
		class="cart-drawer__dialog"
		role="dialog"
		aria-modal="true"
		aria-labelledby="cart-drawer-title"
	>
		<header class="cart-drawer__header">
			<h2 id="cart-drawer-title" class="cart-drawer__title">
				<?php esc_html_e( 'Your bag', 'fashion-brand-theme' ); ?>
			</h2>
			<button
				type="button"
				class="cart-drawer__close"
				data-cart-drawer-close
				aria-label="<?php esc_attr_e( 'Close bag', 'fashion-brand-theme' ); ?>"
			>
				<?php echo fashion_brand_theme_get_icon( 'x', array( 'width' => 20, 'height' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</header>

		<?php fashion_brand_theme_render_cart_drawer_body(); ?>
	</div>
</div>
