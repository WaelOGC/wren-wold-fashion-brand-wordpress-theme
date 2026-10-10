<?php
/**
 * Orders — v3 order rows.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );

$order_total_label = $has_orders
	? sprintf(
		/* translators: %s: order count */
		_n( '%s order', '%s orders', $customer_orders->total, 'fashion-brand-theme' ),
		esc_html( fashion_brand_theme_format_account_stat( (int) $customer_orders->total ) )
	)
	: '';
?>

<div class="block">
	<div class="block-head">
		<h2><?php esc_html_e( 'Order history', 'fashion-brand-theme' ); ?></h2>
		<?php if ( $has_orders ) : ?>
			<span class="micro"><?php echo esc_html( $order_total_label ); ?></span>
		<?php endif; ?>
	</div>

	<?php if ( $has_orders ) : ?>
		<?php
		foreach ( $customer_orders->orders as $customer_order ) {
			$order = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

			if ( ! $order instanceof WC_Order ) {
				continue;
			}

			fashion_brand_theme_render_account_order_row(
				$order,
				array(
					'show_reorder' => true,
					'view_label'   => __( 'View', 'fashion-brand-theme' ),
				)
			);
		}
		?>
	<?php else : ?>
		<p class="block-empty"><?php esc_html_e( 'No orders yet. Browse the collection and your history will live here.', 'fashion-brand-theme' ); ?></p>
		<a class="text-link" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Browse products ↗', 'fashion-brand-theme' ); ?>
		</a>
	<?php endif; ?>
</div>

<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

<?php if ( $has_orders && 1 < $customer_orders->max_num_pages ) : ?>
	<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination account-orders__pagination">
		<?php if ( 1 !== $current_page ) : ?>
			<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button text-link<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
		<?php endif; ?>

		<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
			<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button text-link<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
