<?php
/**
 * My Account navigation — sticky green sidebar.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );

$user         = wp_get_current_user();
$display_name = $user->display_name ? $user->display_name : $user->user_login;

$icon_map = array(
	'dashboard'       => 'layout-dashboard',
	'orders'          => 'package',
	'edit-address'    => 'map-pin',
	'wishlist'        => 'heart',
	'coupons'         => 'tag',
	'edit-account'    => 'user',
	'customer-logout' => 'log-out',
);
?>

<aside class="sidebar woocommerce-MyAccount-navigation" aria-label="<?php esc_attr_e( 'Account pages', 'woocommerce' ); ?>">
	<div class="profile-mini">
		<?php fashion_brand_theme_render_account_avatar( array( 'size' => 'md' ) ); ?>
		<div>
			<strong><?php echo esc_html( $display_name ); ?></strong>
			<small><?php esc_html_e( 'WREN WOLD member', 'fashion-brand-theme' ); ?></small>
		</div>
	</div>
	<div class="nav-label"><?php esc_html_e( 'Your account', 'fashion-brand-theme' ); ?></div>
	<nav class="nav-list">
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<?php
			if ( 'customer-logout' === $endpoint ) {
				continue;
			}

			$icon_name = isset( $icon_map[ $endpoint ] ) ? $icon_map[ $endpoint ] : 'circle';
			$classes   = array( 'nav-item' );

			if ( function_exists( 'wc_is_current_account_menu_item' ) && wc_is_current_account_menu_item( $endpoint ) ) {
				$classes[] = 'active';
			}
			?>
			<a
				class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
				href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
			>
				<?php echo fashion_brand_theme_get_icon( $icon_name, array( 'width' => 16, 'height' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endforeach; ?>
		<a class="nav-item logout" href="<?php echo esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ); ?>" rel="nofollow">
			<?php echo fashion_brand_theme_get_icon( 'log-out', array( 'width' => 16, 'height' => 16 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php esc_html_e( 'Sign out', 'fashion-brand-theme' ); ?>
		</a>
	</nav>
</aside>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
