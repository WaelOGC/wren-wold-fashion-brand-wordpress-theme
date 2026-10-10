<?php
/**
 * My Account navigation.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );

$icon_map = array(
	'dashboard'       => 'dashboard',
	'orders'          => 'package',
	'edit-address'    => 'map-pin',
	'wishlist'        => 'heart',
	'coupons'         => 'tag',
	'edit-account'    => 'user',
	'customer-logout' => 'log-out',
);
?>

<nav class="acct-nav woocommerce-MyAccount-navigation" aria-label="<?php esc_attr_e( 'Account pages', 'woocommerce' ); ?>">
	<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
		<?php
		$icon_name = isset( $icon_map[ $endpoint ] ) ? $icon_map[ $endpoint ] : 'circle';
		$classes   = array( 'acct-nav-item' );

		if ( function_exists( 'wc_is_current_account_menu_item' ) && wc_is_current_account_menu_item( $endpoint ) ) {
			$classes[] = 'active';
		}

		if ( 'customer-logout' === $endpoint ) {
			$classes[] = 'logout';
		}
		?>
		<a
			class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
			href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
			<?php echo 'customer-logout' === $endpoint ? ' rel="nofollow"' : ''; ?>
		>
			<?php echo fashion_brand_theme_get_icon( $icon_name, array( 'width' => 18, 'height' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo esc_html( $label ); ?>
		</a>
	<?php endforeach; ?>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
