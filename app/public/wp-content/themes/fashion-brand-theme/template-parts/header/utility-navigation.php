<?php
/**
 * Utility navigation — icon-only Search / Account / Cart.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$show_search  = fashion_brand_theme_is_setting_enabled( 'header_search_enabled' );
$show_account = fashion_brand_theme_is_setting_enabled( 'header_account_enabled' );
$show_cart    = fashion_brand_theme_is_setting_enabled( 'header_cart_enabled' );
$use_panel    = fashion_brand_theme_is_setting_enabled( 'header_search_panel_enabled' );

if ( ! $show_search && ! $show_account && ! $show_cart ) {
	return;
}
?>
<nav class="utility-navigation" aria-label="<?php esc_attr_e( 'Utility Navigation', 'fashion-brand-theme' ); ?>">
	<ul id="utility-menu" class="utility-menu">
		<?php if ( $show_search ) : ?>
			<li class="menu-item menu-item-search">
				<?php if ( $use_panel ) : ?>
					<button
						type="button"
						class="utility-menu__search-toggle"
						aria-expanded="false"
						aria-controls="header-search-panel"
						aria-label="<?php esc_attr_e( 'Search', 'fashion-brand-theme' ); ?>"
						data-search-toggle
					>
						<svg class="utility-menu__icon" width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
							<circle cx="10.5" cy="10.5" r="5.75" stroke="currentColor" stroke-width="1.25"/>
							<path d="M15.5 15.5 20 20" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
						</svg>
					</button>
				<?php else : ?>
					<a href="<?php echo esc_url( fashion_brand_theme_get_search_url() ); ?>" aria-label="<?php esc_attr_e( 'Search', 'fashion-brand-theme' ); ?>">
						<svg class="utility-menu__icon" width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
							<circle cx="10.5" cy="10.5" r="5.75" stroke="currentColor" stroke-width="1.25"/>
							<path d="M15.5 15.5 20 20" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
						</svg>
					</a>
				<?php endif; ?>
			</li>
		<?php endif; ?>

		<?php if ( $show_account ) : ?>
			<li class="menu-item menu-item-account">
				<a href="<?php echo esc_url( fashion_brand_theme_get_account_url() ); ?>" aria-label="<?php esc_attr_e( 'Account', 'fashion-brand-theme' ); ?>">
					<svg class="utility-menu__icon" width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
						<circle cx="12" cy="8" r="3.25" stroke="currentColor" stroke-width="1.25"/>
						<path d="M5.5 19.25c1.6-3.1 4-4.75 6.5-4.75s4.9 1.65 6.5 4.75" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
					</svg>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( $show_cart ) : ?>
			<li class="menu-item menu-item-cart">
				<a class="utility-menu__cart" href="<?php echo esc_url( fashion_brand_theme_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'fashion-brand-theme' ); ?>">
					<svg class="utility-menu__icon" width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
						<path d="M4.5 6.5h15l-1.4 9.2a1.5 1.5 0 0 1-1.5 1.3H7.4a1.5 1.5 0 0 1-1.5-1.3L4.5 6.5Z" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"/>
						<path d="M8 6.5V5.2A2.2 2.2 0 0 1 10.2 3h3.6A2.2 2.2 0 0 1 16 5.2V6.5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
					</svg>
					<?php fashion_brand_theme_render_header_cart_count(); ?>
				</a>
			</li>
		<?php endif; ?>
	</ul>
</nav>
