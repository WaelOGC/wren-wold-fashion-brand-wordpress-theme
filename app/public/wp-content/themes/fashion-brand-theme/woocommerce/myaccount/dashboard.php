<?php
/**
 * My Account dashboard.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

$user       = wp_get_current_user();
$first_name = $user->first_name ? $user->first_name : $user->display_name;
$recent     = null;

if ( function_exists( 'wc_get_orders' ) ) {
	$orders = wc_get_orders(
		array(
			'customer' => get_current_user_id(),
			'limit'    => 1,
			'orderby'  => 'date',
			'order'    => 'DESC',
		)
	);

	if ( ! empty( $orders ) ) {
		$recent = $orders[0];
	}
}

$coupons = fashion_brand_theme_get_active_coupons( 2 );

$quicklinks = array(
	array(
		'endpoint' => 'orders',
		'label'    => __( 'Orders', 'fashion-brand-theme' ),
		'icon'     => 'package',
	),
	array(
		'endpoint' => 'edit-address',
		'label'    => __( 'Addresses', 'fashion-brand-theme' ),
		'icon'     => 'map-pin',
	),
	array(
		'endpoint' => 'wishlist',
		'label'    => __( 'Wishlist', 'fashion-brand-theme' ),
		'icon'     => 'heart',
	),
	array(
		'endpoint' => 'coupons',
		'label'    => __( 'Coupons', 'fashion-brand-theme' ),
		'icon'     => 'tag',
	),
);
?>

<div class="acct-welcome">
	<?php fashion_brand_theme_render_account_avatar( array( 'size' => 'md' ) ); ?>
	<div>
		<h2>
			<?php
			printf(
				/* translators: %s: customer first name */
				esc_html__( 'Welcome back, %s', 'fashion-brand-theme' ),
				esc_html( $first_name )
			);
			?>
		</h2>
		<p><?php esc_html_e( "Here's what's happening with your account.", 'fashion-brand-theme' ); ?></p>
	</div>
</div>

<div class="acct-card">
	<p class="acct-section-label"><?php esc_html_e( 'Recent Order', 'fashion-brand-theme' ); ?></p>
	<?php if ( $recent instanceof WC_Order ) : ?>
		<?php
		fashion_brand_theme_render_account_order_card(
			$recent,
			array(
				'show_reorder'  => false,
				'placed_prefix' => true,
				'view_label'    => __( 'View Order', 'fashion-brand-theme' ),
			)
		);
		?>
	<?php else : ?>
		<p class="acct-empty"><?php esc_html_e( 'You have not placed any orders yet.', 'fashion-brand-theme' ); ?></p>
		<a class="btn btn--primary" href="<?php echo esc_url( fashion_brand_theme_get_shop_url() ); ?>">
			<?php esc_html_e( 'Start shopping', 'fashion-brand-theme' ); ?>
		</a>
	<?php endif; ?>
</div>

<div class="acct-card">
	<p class="acct-section-label"><?php esc_html_e( 'Quick Links', 'fashion-brand-theme' ); ?></p>
	<div class="quicklinks">
		<?php foreach ( $quicklinks as $link ) : ?>
			<a class="quicklink" href="<?php echo esc_url( wc_get_account_endpoint_url( $link['endpoint'] ) ); ?>">
				<?php echo fashion_brand_theme_get_icon( $link['icon'], array( 'width' => 22, 'height' => 22 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo esc_html( $link['label'] ); ?>
			</a>
		<?php endforeach; ?>
	</div>
</div>

<div class="acct-card">
	<p class="acct-section-label"><?php esc_html_e( 'Your Coupons', 'fashion-brand-theme' ); ?></p>
	<?php
	fashion_brand_theme_render_coupon_cards(
		$coupons,
		array(
			'show_copy'   => false,
			'show_expiry' => false,
		)
	);
	?>
</div>
