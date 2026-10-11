<?php
/**
 * View Order — themed single-order detail.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 10.6.0
 */

defined( 'ABSPATH' ) || exit;

$notes   = $order->get_customer_order_notes();
$actions = array_filter(
	wc_get_account_orders_actions( $order ),
	static function ( $key ) {
		return 'view' !== $key;
	},
	ARRAY_FILTER_USE_KEY
);
?>

<div class="block view-order-block">
	<div class="block-head">
		<h2>
			<?php
			printf(
				/* translators: %s: order number */
				esc_html__( 'Order #%s', 'fashion-brand-theme' ),
				esc_html( $order->get_order_number() )
			);
			?>
		</h2>
		<span class="micro">
			<?php echo esc_html( wc_format_datetime( $order->get_date_created(), get_option( 'date_format' ) ) ); ?>
		</span>
	</div>

	<div class="view-order__meta">
		<div class="<?php echo esc_attr( fashion_brand_theme_get_order_status_row_class( $order->get_status() ) ); ?>">
			<span class="dot" aria-hidden="true"></span>
			<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
		</div>
		<a class="text-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
			<?php esc_html_e( 'Back to orders ↗', 'fashion-brand-theme' ); ?>
		</a>
	</div>

	<?php if ( ! empty( $actions ) ) : ?>
		<p class="view-order__actions">
			<?php
			foreach ( $actions as $key => $action ) {
				$classes = 'pay' === $key ? 'save button' : 'text-link';
				printf(
					'<a href="%1$s" class="%2$s woocommerce-button %3$s">%4$s</a>',
					esc_url( $action['url'] ),
					esc_attr( $classes ),
					sanitize_html_class( $key ),
					esc_html( $action['name'] )
				);
			}
			?>
		</p>
	<?php endif; ?>
</div>

<?php if ( $notes ) : ?>
	<div class="block">
		<div class="block-head">
			<h2><?php esc_html_e( 'Order updates', 'woocommerce' ); ?></h2>
			<span class="micro"><?php esc_html_e( 'Notes', 'fashion-brand-theme' ); ?></span>
		</div>
		<ol class="woocommerce-OrderUpdates commentlist notes">
			<?php foreach ( $notes as $note ) : ?>
				<li class="woocommerce-OrderUpdate comment note">
					<div class="woocommerce-OrderUpdate-inner comment_container">
						<div class="woocommerce-OrderUpdate-text comment-text">
							<p class="woocommerce-OrderUpdate-meta meta">
								<?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $note->comment_date ) ) ); ?>
							</p>
							<div class="woocommerce-OrderUpdate-description description">
								<?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?>
							</div>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
<?php endif; ?>

<div class="block view-order-details-block">
	<?php do_action( 'woocommerce_view_order', $order_id ); ?>
</div>
