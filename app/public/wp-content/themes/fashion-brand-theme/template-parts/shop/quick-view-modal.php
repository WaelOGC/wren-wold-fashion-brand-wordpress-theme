<?php
/**
 * Quick view modal shell.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="quick-view-modal" data-quick-view-modal hidden>
	<div class="quick-view-modal__backdrop" data-quick-view-close tabindex="-1"></div>
	<div class="quick-view-modal__dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Quick view', 'fashion-brand-theme' ); ?>">
		<button type="button" class="quick-view-modal__close" data-quick-view-close aria-label="<?php esc_attr_e( 'Close', 'fashion-brand-theme' ); ?>">
			<?php echo fashion_brand_theme_get_icon( 'x', array( 'width' => 20, 'height' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
		<div class="quick-view-modal__content" data-quick-view-content></div>
	</div>
</div>
