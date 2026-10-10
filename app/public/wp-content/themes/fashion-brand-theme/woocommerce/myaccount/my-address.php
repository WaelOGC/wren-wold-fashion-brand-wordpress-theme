<?php
/**
 * My Addresses — v3 address cards.
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

$address_count = count( $get_addresses );
$index         = 0;
?>

<div class="block">
	<div class="block-head">
		<h2><?php esc_html_e( 'Your addresses', 'fashion-brand-theme' ); ?></h2>
		<span class="micro">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: number of saved addresses */
					_n( '%s saved', '%s saved', $address_count, 'fashion-brand-theme' ),
					fashion_brand_theme_format_account_stat( $address_count )
				)
			);
			?>
		</span>
	</div>
	<div class="address-grid">
		<?php foreach ( $get_addresses as $name => $address_title ) : ?>
			<?php
			++$index;
			$address  = wc_get_account_formatted_address( $name );
			$edit_url = wc_get_endpoint_url( 'edit-address', $name );
			$eyebrow  = 'billing' === $name
				? __( '01 / Billing', 'fashion-brand-theme' )
				: __( '02 / Delivery', 'fashion-brand-theme' );
			if ( 1 === $address_count ) {
				$eyebrow = __( '01 / Billing', 'fashion-brand-theme' );
			}
			?>
			<div class="address woocommerce-Address">
				<div class="eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
				<h3><?php echo esc_html( $address_title ); ?></h3>
				<?php if ( $address ) : ?>
					<p><?php echo wp_kses_post( $address ); ?></p>
				<?php else : ?>
					<p><?php esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' ); ?></p>
				<?php endif; ?>
				<a href="<?php echo esc_url( $edit_url ); ?>" class="text-link">
					<?php esc_html_e( 'Edit address ↗', 'fashion-brand-theme' ); ?>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
</div>
