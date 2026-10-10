<?php
/**
 * My Addresses.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Billing address', 'woocommerce' ),
			'shipping' => __( 'Shipping address', 'woocommerce' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Billing address', 'woocommerce' ),
		),
		$customer_id
	);
}

if ( empty( $get_addresses ) ) {
	return;
}
?>

<div class="acct-card">
	<p class="acct-section-label"><?php esc_html_e( 'Your Addresses', 'fashion-brand-theme' ); ?></p>
	<div class="addr-grid">
		<?php foreach ( $get_addresses as $name => $address_title ) : ?>
			<?php
			$address  = wc_get_account_formatted_address( $name );
			$edit_url = wc_get_endpoint_url( 'edit-address', $name );
			?>
			<div class="addr-card woocommerce-Address">
				<h3><?php echo esc_html( $address_title ); ?></h3>
				<?php if ( $address ) : ?>
					<p><?php echo wp_kses_post( $address ); ?></p>
				<?php else : ?>
					<p><?php esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' ); ?></p>
				<?php endif; ?>
				<a href="<?php echo esc_url( $edit_url ); ?>" class="btn btn--sm">
					<?php esc_html_e( 'Edit', 'fashion-brand-theme' ); ?>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>
