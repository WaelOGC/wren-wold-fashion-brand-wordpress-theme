<?php
/**
 * Off-canvas mini-cart drawer — markup helpers, fragments, AJAX quantity.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the cart drawer should render on the front end.
 *
 * @return bool
 */
function fashion_brand_theme_should_render_cart_drawer() {
	if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'WC' ) ) {
		return false;
	}

	return fashion_brand_theme_is_setting_enabled( 'header_cart_enabled' );
}

/**
 * Force AJAX add-to-cart so loop buttons and the drawer stay in sync without a reload.
 *
 * @param mixed $value Option value.
 * @return string
 */
function fashion_brand_theme_enable_ajax_add_to_cart( $value ) {
	return 'yes';
}
add_filter( 'pre_option_woocommerce_enable_ajax_add_to_cart', 'fashion_brand_theme_enable_ajax_add_to_cart' );

/**
 * Keep “redirect after add” off so AJAX success can open the drawer instead.
 *
 * @param mixed $value Option value.
 * @return string
 */
function fashion_brand_theme_disable_cart_redirect_after_add( $value ) {
	return 'no';
}
add_filter( 'pre_option_woocommerce_cart_redirect_after_add', 'fashion_brand_theme_disable_cart_redirect_after_add' );

/**
 * Render one cart line using the same filters/logic as the Cart page table.
 *
 * @param string $cart_item_key Cart item key.
 * @param array  $cart_item     Cart item.
 * @return void
 */
function fashion_brand_theme_render_cart_drawer_item( $cart_item_key, $cart_item ) {
	$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
	$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
	$visible    = apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key );

	if ( ! $_product instanceof WC_Product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! $visible ) {
		return;
	}

	$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
	$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
	$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_gallery_thumbnail' ), $cart_item, $cart_item_key );
	$line_subtotal     = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );

	if ( $_product->is_sold_individually() ) {
		$min_quantity = 1;
		$max_quantity = 1;
	} else {
		$min_quantity = 0;
		$max_quantity = $_product->get_max_purchase_quantity();
	}

	$product_quantity = woocommerce_quantity_input(
		array(
			'input_name'   => "cart[{$cart_item_key}][qty]",
			'input_value'  => $cart_item['quantity'],
			'max_value'    => $max_quantity,
			'min_value'    => $min_quantity,
			'product_name' => $product_name,
			'classes'      => array( 'input-text', 'qty', 'text', 'cart-drawer__qty-input' ),
		),
		$_product,
		false
	);
	$product_quantity = apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
	?>
	<li class="cart-drawer__item woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
		<?php
		echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'woocommerce_cart_item_remove_link',
			sprintf(
				'<a role="button" href="%s" class="remove remove_from_cart_button cart-drawer__remove" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s">&times;</a>',
				esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
				/* translators: %s is the product name */
				esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
				esc_attr( (string) $product_id ),
				esc_attr( $cart_item_key ),
				esc_attr( $_product->get_sku() ),
				/* translators: %s is the product name */
				esc_attr( sprintf( __( '&ldquo;%s&rdquo; has been removed from your cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) )
			),
			$cart_item_key
		);
		?>

		<div class="cart-drawer__thumb">
			<?php
			if ( $product_permalink ) {
				printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>

		<div class="cart-drawer__details">
			<div class="cart-drawer__name product-name">
				<?php
				if ( $product_permalink ) {
					echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
				} else {
					echo wp_kses_post( $product_name );
				}

				do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
				echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
					echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
				}
				?>
			</div>

			<div class="cart-drawer__meta-row">
				<div class="cart-drawer__quantity product-quantity" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
					<?php echo $product_quantity; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="cart-drawer__line-total product-subtotal">
					<?php echo $line_subtotal; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>
	</li>
	<?php
}

/**
 * Render the replaceable drawer body (items + footer / empty state).
 *
 * @return void
 */
function fashion_brand_theme_render_cart_drawer_body() {
	$cart       = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart : null;
	$is_empty   = ! $cart || $cart->is_empty();
	$cart_url   = function_exists( 'fashion_brand_theme_get_cart_url' ) ? fashion_brand_theme_get_cart_url() : wc_get_cart_url();
	$checkout   = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : $cart_url;
	$shop_url   = function_exists( 'fashion_brand_theme_get_shop_url' ) ? fashion_brand_theme_get_shop_url() : home_url( '/' );
	?>
	<div class="cart-drawer__body">
		<?php if ( $is_empty ) : ?>
			<div class="cart-drawer__empty">
				<p class="cart-drawer__empty-text"><?php esc_html_e( 'Your bag is empty', 'fashion-brand-theme' ); ?></p>
				<a class="cart-drawer__empty-cta" href="<?php echo esc_url( $shop_url ); ?>">
					<?php esc_html_e( 'Shop', 'fashion-brand-theme' ); ?>
				</a>
			</div>
		<?php else : ?>
			<ul class="cart-drawer__items woocommerce-mini-cart cart_list product_list_widget">
				<?php
				foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
					fashion_brand_theme_render_cart_drawer_item( $cart_item_key, $cart_item );
				}
				?>
			</ul>

			<footer class="cart-drawer__footer">
				<div class="cart-drawer__subtotal">
					<span class="cart-drawer__subtotal-label"><?php esc_html_e( 'Subtotal', 'fashion-brand-theme' ); ?></span>
					<span class="cart-drawer__subtotal-value"><?php echo wp_kses_post( $cart->get_cart_subtotal() ); ?></span>
				</div>
				<a class="cart-drawer__checkout button" href="<?php echo esc_url( $checkout ); ?>">
					<?php esc_html_e( 'Checkout', 'fashion-brand-theme' ); ?>
				</a>
				<a class="cart-drawer__view-cart" href="<?php echo esc_url( $cart_url ); ?>">
					<?php esc_html_e( 'View full cart', 'fashion-brand-theme' ); ?>
				</a>
			</footer>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Cart drawer body fragment for WooCommerce cart fragments.
 *
 * @param array<string, string> $fragments Fragments.
 * @return array<string, string>
 */
function fashion_brand_theme_cart_drawer_fragment( $fragments ) {
	if ( ! fashion_brand_theme_should_render_cart_drawer() ) {
		return $fragments;
	}

	ob_start();
	fashion_brand_theme_render_cart_drawer_body();
	$fragments['div.cart-drawer__body'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'fashion_brand_theme_cart_drawer_fragment' );

/**
 * AJAX: update a cart line quantity, then return standard cart fragments.
 *
 * @return void
 */
function fashion_brand_theme_ajax_update_cart_item() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		wp_send_json_error();
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- mirrors WC cart AJAX endpoints.
	$cart_item_key = isset( $_POST['cart_item_key'] ) ? wc_clean( wp_unslash( $_POST['cart_item_key'] ) ) : '';
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$quantity = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : null;

	if ( ! $cart_item_key || null === $quantity || ! is_string( $cart_item_key ) ) {
		wp_send_json_error();
	}

	$cart_item = WC()->cart->get_cart_item( $cart_item_key );

	if ( empty( $cart_item ) ) {
		wp_send_json_error();
	}

	WC()->cart->set_quantity( $cart_item_key, $quantity, true );

	if ( class_exists( 'WC_AJAX' ) ) {
		WC_AJAX::get_refreshed_fragments();
	}

	wp_send_json_error();
}
add_action( 'wc_ajax_fashion_brand_update_cart_item', 'fashion_brand_theme_ajax_update_cart_item' );
add_action( 'wp_ajax_fashion_brand_update_cart_item', 'fashion_brand_theme_ajax_update_cart_item' );
add_action( 'wp_ajax_nopriv_fashion_brand_update_cart_item', 'fashion_brand_theme_ajax_update_cart_item' );
