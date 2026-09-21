<?php
/**
 * Fixed mobile bottom tab bar.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! fashion_brand_theme_should_show_mobile_bottom_nav() ) {
	return;
}

$shop_url        = fashion_brand_theme_get_shop_url();
$collections_url = fashion_brand_theme_get_page_url( 'collections' );
$account_url     = fashion_brand_theme_get_account_url();
$wishlist_page   = get_page_by_path( 'wishlist' );
$show_wishlist   = ( $wishlist_page instanceof WP_Post && 'publish' === $wishlist_page->post_status );
$wishlist_url    = $show_wishlist ? get_permalink( $wishlist_page ) : '';

$is_home_active        = is_front_page();
$is_shop_active        = ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() );
$is_collections_active = is_page( 'collections' );
$is_wishlist_active    = is_page( 'wishlist' );
$is_account_active     = function_exists( 'is_account_page' ) && is_account_page();
?>
<nav class="mobile-bottom-nav" aria-label="<?php esc_attr_e( 'Mobile navigation', 'fashion-brand-theme' ); ?>">
	<a
		class="mobile-bottom-nav__link<?php echo $is_home_active ? ' is-active' : ''; ?>"
		href="<?php echo esc_url( home_url( '/' ) ); ?>"
		<?php echo $is_home_active ? ' aria-current="page"' : ''; ?>
	>
		<svg class="mobile-bottom-nav__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
			<path d="M3.5 10.5 12 3.5l8.5 7V20a1 1 0 0 1-1 1h-5.2v-6.2h-4.6V21H4.5a1 1 0 0 1-1-1v-9.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<span class="mobile-bottom-nav__label"><?php esc_html_e( 'Home', 'fashion-brand-theme' ); ?></span>
	</a>

	<a
		class="mobile-bottom-nav__link<?php echo $is_shop_active ? ' is-active' : ''; ?>"
		href="<?php echo esc_url( $shop_url ); ?>"
		<?php echo $is_shop_active ? ' aria-current="page"' : ''; ?>
	>
		<svg class="mobile-bottom-nav__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
			<path d="M4.5 6.5h15l-1.4 9.2a1.5 1.5 0 0 1-1.5 1.3H7.4a1.5 1.5 0 0 1-1.5-1.3L4.5 6.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
			<path d="M8 6.5V5.2A2.2 2.2 0 0 1 10.2 3h3.6A2.2 2.2 0 0 1 16 5.2V6.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
		</svg>
		<span class="mobile-bottom-nav__label"><?php esc_html_e( 'Shop', 'fashion-brand-theme' ); ?></span>
	</a>

	<a
		class="mobile-bottom-nav__link<?php echo $is_collections_active ? ' is-active' : ''; ?>"
		href="<?php echo esc_url( $collections_url ); ?>"
		<?php echo $is_collections_active ? ' aria-current="page"' : ''; ?>
	>
		<svg class="mobile-bottom-nav__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
			<rect x="3.5" y="3.5" width="7" height="7" rx="0.5" stroke="currentColor" stroke-width="1.5"/>
			<rect x="13.5" y="3.5" width="7" height="7" rx="0.5" stroke="currentColor" stroke-width="1.5"/>
			<rect x="3.5" y="13.5" width="7" height="7" rx="0.5" stroke="currentColor" stroke-width="1.5"/>
			<rect x="13.5" y="13.5" width="7" height="7" rx="0.5" stroke="currentColor" stroke-width="1.5"/>
		</svg>
		<span class="mobile-bottom-nav__label"><?php esc_html_e( 'Collections', 'fashion-brand-theme' ); ?></span>
	</a>

	<?php if ( $show_wishlist && $wishlist_url ) : ?>
		<a
			class="mobile-bottom-nav__link mobile-bottom-nav__link--wishlist<?php echo $is_wishlist_active ? ' is-active' : ''; ?>"
			href="<?php echo esc_url( $wishlist_url ); ?>"
			<?php echo $is_wishlist_active ? ' aria-current="page"' : ''; ?>
		>
			<span class="mobile-bottom-nav__icon-wrap">
				<svg class="mobile-bottom-nav__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
					<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
				</svg>
				<span class="mobile-bottom-nav__badge" data-wishlist-count hidden>0</span>
			</span>
			<span class="mobile-bottom-nav__label"><?php esc_html_e( 'Wishlist', 'fashion-brand-theme' ); ?></span>
		</a>
	<?php endif; ?>

	<a
		class="mobile-bottom-nav__link<?php echo $is_account_active ? ' is-active' : ''; ?>"
		href="<?php echo esc_url( $account_url ); ?>"
		<?php echo $is_account_active ? ' aria-current="page"' : ''; ?>
	>
		<svg class="mobile-bottom-nav__icon" width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
			<circle cx="12" cy="8" r="3.25" stroke="currentColor" stroke-width="1.5"/>
			<path d="M5.5 19.25c1.6-3.1 4-4.75 6.5-4.75s4.9 1.65 6.5 4.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
		</svg>
		<span class="mobile-bottom-nav__label"><?php esc_html_e( 'Account', 'fashion-brand-theme' ); ?></span>
	</a>
</nav>
