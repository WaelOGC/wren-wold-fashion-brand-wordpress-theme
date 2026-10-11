<?php
/**
 * Order customer details — address cards matching My Account.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 8.7.0
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details view-order-addresses">
	<div class="address-grid">
		<div class="address woocommerce-column--billing-address">
			<div class="eyebrow"><?php esc_html_e( '01 / Billing', 'fashion-brand-theme' ); ?></div>
			<h3><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></h3>
			<p>
				<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
				<?php if ( $order->get_billing_phone() ) : ?>
					<br><?php echo esc_html( $order->get_billing_phone() ); ?>
				<?php endif; ?>
				<?php if ( $order->get_billing_email() ) : ?>
					<br><?php echo esc_html( $order->get_billing_email() ); ?>
				<?php endif; ?>
			</p>
			<?php do_action( 'woocommerce_order_details_after_customer_address', 'billing', $order ); ?>
		</div>

		<?php if ( $show_shipping ) : ?>
			<div class="address woocommerce-column--shipping-address">
				<div class="eyebrow"><?php esc_html_e( '02 / Delivery', 'fashion-brand-theme' ); ?></div>
				<h3><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></h3>
				<p>
					<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
					<?php if ( $order->get_shipping_phone() ) : ?>
						<br><?php echo esc_html( $order->get_shipping_phone() ); ?>
					<?php endif; ?>
				</p>
				<?php do_action( 'woocommerce_order_details_after_customer_address', 'shipping', $order ); ?>
			</div>
		<?php endif; ?>
	</div>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
</section>
