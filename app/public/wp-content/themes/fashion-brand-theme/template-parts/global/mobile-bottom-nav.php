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
		<?php echo fashion_brand_theme_get_icon( 'house', array( 'width' => 22, 'height' => 22, 'class' => 'mobile-bottom-nav__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span class="mobile-bottom-nav__label"><?php esc_html_e( 'Home', 'fashion-brand-theme' ); ?></span>
	</a>

	<a
		class="mobile-bottom-nav__link<?php echo $is_shop_active ? ' is-active' : ''; ?>"
		href="<?php echo esc_url( $shop_url ); ?>"
		<?php echo $is_shop_active ? ' aria-current="page"' : ''; ?>
	>
		<?php echo fashion_brand_theme_get_icon( 'shopping-bag', array( 'width' => 22, 'height' => 22, 'class' => 'mobile-bottom-nav__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span class="mobile-bottom-nav__label"><?php esc_html_e( 'Shop', 'fashion-brand-theme' ); ?></span>
	</a>

	<a
		class="mobile-bottom-nav__link<?php echo $is_collections_active ? ' is-active' : ''; ?>"
		href="<?php echo esc_url( $collections_url ); ?>"
		<?php echo $is_collections_active ? ' aria-current="page"' : ''; ?>
	>
		<?php echo fashion_brand_theme_get_icon( 'layout-grid', array( 'width' => 22, 'height' => 22, 'class' => 'mobile-bottom-nav__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span class="mobile-bottom-nav__label"><?php esc_html_e( 'Collections', 'fashion-brand-theme' ); ?></span>
	</a>

	<?php if ( $show_wishlist && $wishlist_url ) : ?>
		<a
			class="mobile-bottom-nav__link mobile-bottom-nav__link--wishlist<?php echo $is_wishlist_active ? ' is-active' : ''; ?>"
			href="<?php echo esc_url( $wishlist_url ); ?>"
			<?php echo $is_wishlist_active ? ' aria-current="page"' : ''; ?>
		>
			<span class="mobile-bottom-nav__icon-wrap">
				<?php echo fashion_brand_theme_get_icon( 'heart', array( 'width' => 22, 'height' => 22, 'class' => 'mobile-bottom-nav__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
		<?php echo fashion_brand_theme_get_icon( 'user', array( 'width' => 22, 'height' => 22, 'class' => 'mobile-bottom-nav__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<span class="mobile-bottom-nav__label"><?php esc_html_e( 'Account', 'fashion-brand-theme' ); ?></span>
	</a>
</nav>
