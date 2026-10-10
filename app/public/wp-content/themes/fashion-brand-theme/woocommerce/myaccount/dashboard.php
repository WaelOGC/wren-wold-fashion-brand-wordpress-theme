<?php
/**
 * My Account dashboard — v3 overview.
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

$order_count    = function_exists( 'wc_get_customer_order_count' ) ? (int) wc_get_customer_order_count( get_current_user_id() ) : 0;
$wishlist_count = count( fashion_brand_theme_get_wishlist_ids() );
$coupon_count   = count( fashion_brand_theme_get_active_coupons() );
$coupons        = fashion_brand_theme_get_active_coupons( 2 );

$quicklinks = array(
	array(
		'endpoint' => 'orders',
		'label'    => __( 'Track an order ↗', 'fashion-brand-theme' ),
		'icon'     => 'package',
	),
	array(
		'endpoint' => 'edit-address',
		'label'    => __( 'Manage addresses ↗', 'fashion-brand-theme' ),
		'icon'     => 'map-pin',
	),
	array(
		'endpoint' => 'wishlist',
		'label'    => __( 'Saved pieces ↗', 'fashion-brand-theme' ),
		'icon'     => 'heart',
	),
	array(
		'endpoint' => 'coupons',
		'label'    => __( 'Member offers ↗', 'fashion-brand-theme' ),
		'icon'     => 'tag',
	),
);
?>

<div class="dash-welcome">
	<div class="copy">
		<div class="eyebrow"><?php esc_html_e( 'Welcome back', 'fashion-brand-theme' ); ?></div>
		<h2>
			<?php
			printf(
				/* translators: %s: customer first name */
				esc_html__( '%s, good to have you.', 'fashion-brand-theme' ),
				esc_html( $first_name )
			);
			?>
		</h2>
		<p><?php esc_html_e( 'Your personal edit is ready. Here is a little overview of your account.', 'fashion-brand-theme' ); ?></p>
	</div>
	<?php fashion_brand_theme_render_account_avatar( array( 'size' => 'lg' ) ); ?>
</div>

<div class="stat-grid">
	<div class="stat">
		<small><?php esc_html_e( 'Orders placed', 'fashion-brand-theme' ); ?></small>
		<strong><?php echo esc_html( fashion_brand_theme_format_account_stat( $order_count ) ); ?></strong>
		<span><?php esc_html_e( 'Your order history', 'fashion-brand-theme' ); ?></span>
	</div>
	<div class="stat">
		<small><?php esc_html_e( 'Saved pieces', 'fashion-brand-theme' ); ?></small>
		<strong><?php echo esc_html( fashion_brand_theme_format_account_stat( $wishlist_count ) ); ?></strong>
		<span><?php esc_html_e( 'Your wishlist', 'fashion-brand-theme' ); ?></span>
	</div>
	<div class="stat">
		<small><?php esc_html_e( 'Member offers', 'fashion-brand-theme' ); ?></small>
		<strong><?php echo esc_html( fashion_brand_theme_format_account_stat( $coupon_count ) ); ?></strong>
		<span><?php esc_html_e( 'Ready when you are', 'fashion-brand-theme' ); ?></span>
	</div>
</div>

<div class="block">
	<div class="block-head">
		<h2><?php esc_html_e( 'Latest order', 'fashion-brand-theme' ); ?></h2>
		<span class="micro"><?php esc_html_e( '01 / Order activity', 'fashion-brand-theme' ); ?></span>
	</div>
	<?php if ( $recent instanceof WC_Order ) : ?>
		<?php
		fashion_brand_theme_render_account_order_row(
			$recent,
			array(
				'show_reorder'  => false,
				'placed_prefix' => true,
				'view_label'    => __( 'View order ↗', 'fashion-brand-theme' ),
			)
		);
		?>
	<?php else : ?>
		<p class="block-empty"><?php esc_html_e( 'You have not placed any orders yet. When you do, your latest order will appear here.', 'fashion-brand-theme' ); ?></p>
		<a class="text-link" href="<?php echo esc_url( fashion_brand_theme_get_shop_url() ); ?>"><?php esc_html_e( 'Start shopping ↗', 'fashion-brand-theme' ); ?></a>
	<?php endif; ?>
</div>

<div class="block">
	<div class="block-head">
		<h2><?php esc_html_e( 'Go to', 'fashion-brand-theme' ); ?></h2>
		<span class="micro"><?php esc_html_e( 'Shortcuts', 'fashion-brand-theme' ); ?></span>
	</div>
	<div class="quick-grid">
		<?php foreach ( $quicklinks as $link ) : ?>
			<a class="quick" href="<?php echo esc_url( wc_get_account_endpoint_url( $link['endpoint'] ) ); ?>">
				<?php echo fashion_brand_theme_get_icon( $link['icon'], array( 'width' => 18, 'height' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo esc_html( $link['label'] ); ?>
			</a>
		<?php endforeach; ?>
	</div>
</div>

<div class="block">
	<div class="block-head">
		<h2><?php esc_html_e( 'A little something for you', 'fashion-brand-theme' ); ?></h2>
		<span class="micro"><?php esc_html_e( 'Member offers', 'fashion-brand-theme' ); ?></span>
	</div>
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
