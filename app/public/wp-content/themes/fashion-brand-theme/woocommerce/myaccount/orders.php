<?php
/**
 * Orders — card layout.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );
?>

<?php if ( $has_orders ) : ?>

	<div class="account-orders">
		<?php
		foreach ( $customer_orders->orders as $customer_order ) {
			$order      = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$item_count = $order->get_item_count() - $order->get_item_count_refunded();
			$status     = $order->get_status();
			$actions    = wc_get_account_orders_actions( $order );
			?>
			<article class="account-order-card woocommerce-orders-table__row--status-<?php echo esc_attr( $status ); ?>">
				<div class="account-order-card__top">
					<h2 class="account-order-card__number">
						<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
							<?php
							printf(
								/* translators: %s: order number */
								esc_html__( 'Order #%s', 'fashion-brand-theme' ),
								esc_html( $order->get_order_number() )
							);
							?>
						</a>
					</h2>
					<span class="account-order-card__status account-order-card__status--<?php echo esc_attr( $status ); ?>">
						<?php echo esc_html( wc_get_order_status_name( $status ) ); ?>
					</span>
				</div>

				<div class="account-order-card__meta">
					<p class="account-order-card__date">
						<span class="account-order-card__label"><?php esc_html_e( 'Placed', 'fashion-brand-theme' ); ?></span>
						<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>">
							<?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
						</time>
					</p>
					<p class="account-order-card__total">
						<span class="account-order-card__label"><?php esc_html_e( 'Total', 'fashion-brand-theme' ); ?></span>
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: 1: formatted order total 2: total order items */
								_n( '%1$s for %2$s item', '%1$s for %2$s items', $item_count, 'woocommerce' ),
								$order->get_formatted_order_total(),
								$item_count
							)
						);
						?>
					</p>
				</div>

				<?php if ( ! empty( $actions ) ) : ?>
					<div class="account-order-card__actions">
						<?php
						foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
							if ( empty( $action['aria-label'] ) ) {
								/* translators: %1$s Action name, %2$s Order number. */
								$action_aria_label = sprintf( __( '%1$s order number %2$s', 'woocommerce' ), $action['name'], $order->get_order_number() );
							} else {
								$action_aria_label = $action['aria-label'];
							}

							$btn_class = 'woocommerce-button button ' . sanitize_html_class( $key );
							if ( in_array( $key, array( 'pay', 'reorder' ), true ) ) {
								$btn_class .= ' button--primary';
							}
							$btn_class .= $wp_button_class;

							echo '<a href="' . esc_url( $action['url'] ) . '" class="' . esc_attr( trim( $btn_class ) ) . '" aria-label="' . esc_attr( $action_aria_label ) . '">' . esc_html( $action['name'] ) . '</a>';
						}
						?>
					</div>
				<?php endif; ?>
			</article>
			<?php
		}
		?>
	</div>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination account-orders__pagination">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>

	<?php wc_print_notice( esc_html__( 'No order has been made yet.', 'woocommerce' ) . ' <a class="woocommerce-Button wc-forward button button--primary' . esc_attr( $wp_button_class ) . '" href="' . esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ) . '">' . esc_html__( 'Browse products', 'woocommerce' ) . '</a>', 'notice' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
