<?php
/**
 * WooCommerce My Account — endpoints, dashboard, coupons, reorder.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Option flag for one-time rewrite flush after custom endpoints are registered. */
define( 'FASHION_BRAND_THEME_ACCOUNT_ENDPOINTS_VERSION', '1' );

/**
 * Register custom My Account query vars with WooCommerce.
 *
 * @param array $vars Query vars.
 * @return array
 */
function fashion_brand_theme_account_query_vars( $vars ) {
	$vars['coupons']  = 'coupons';
	$vars['wishlist'] = 'wishlist';
	return $vars;
}
add_filter( 'woocommerce_get_query_vars', 'fashion_brand_theme_account_query_vars' );

/**
 * Flush rewrite rules once when account endpoints are newly introduced.
 *
 * @return void
 */
function fashion_brand_theme_maybe_flush_account_endpoints() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$stored = (string) get_option( 'fashion_brand_theme_account_endpoints_version', '' );

	if ( FASHION_BRAND_THEME_ACCOUNT_ENDPOINTS_VERSION === $stored ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'fashion_brand_theme_account_endpoints_version', FASHION_BRAND_THEME_ACCOUNT_ENDPOINTS_VERSION, false );
}
add_action( 'init', 'fashion_brand_theme_maybe_flush_account_endpoints', 99 );

/**
 * Control My Account menu order and labels.
 *
 * @param array $items Menu items.
 * @return array
 */
function fashion_brand_theme_account_menu_items( $items ) {
	return array(
		'dashboard'       => __( 'Dashboard', 'fashion-brand-theme' ),
		'orders'          => __( 'Orders', 'fashion-brand-theme' ),
		'edit-address'    => __( 'Addresses', 'fashion-brand-theme' ),
		'wishlist'        => __( 'Wishlist', 'fashion-brand-theme' ),
		'coupons'         => __( 'Coupons', 'fashion-brand-theme' ),
		'edit-account'    => __( 'Account Details', 'fashion-brand-theme' ),
		'customer-logout' => __( 'Logout', 'fashion-brand-theme' ),
	);
}
add_filter( 'woocommerce_account_menu_items', 'fashion_brand_theme_account_menu_items' );

/**
 * Endpoint titles for custom tabs.
 *
 * @param string $title    Endpoint title.
 * @param string $endpoint Endpoint key.
 * @return string
 */
function fashion_brand_theme_account_endpoint_title( $title, $endpoint ) {
	if ( 'coupons' === $endpoint ) {
		return __( 'Coupons', 'fashion-brand-theme' );
	}

	if ( 'wishlist' === $endpoint ) {
		return __( 'Wishlist', 'fashion-brand-theme' );
	}

	return $title;
}
add_filter( 'woocommerce_endpoint_coupons_title', 'fashion_brand_theme_account_endpoint_title', 10, 2 );
add_filter( 'woocommerce_endpoint_wishlist_title', 'fashion_brand_theme_account_endpoint_title', 10, 2 );

/**
 * Fetch currently active (non-expired) published coupons.
 *
 * @param int $limit Max coupons to return. 0 = all.
 * @return WC_Coupon[]
 */
function fashion_brand_theme_get_active_coupons( $limit = 0 ) {
	if ( ! class_exists( 'WC_Coupon' ) ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'shop_coupon',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'fields'         => 'ids',
		)
	);

	$coupons = array();
	$now     = time();

	foreach ( $query->posts as $coupon_id ) {
		$coupon = new WC_Coupon( (int) $coupon_id );

		if ( ! $coupon->get_id() ) {
			continue;
		}

		$expires = $coupon->get_date_expires();

		if ( $expires && $expires->getTimestamp() < $now ) {
			continue;
		}

		$coupons[] = $coupon;

		if ( $limit > 0 && count( $coupons ) >= $limit ) {
			break;
		}
	}

	return $coupons;
}

/**
 * Format a coupon's discount label.
 *
 * @param WC_Coupon $coupon Coupon object.
 * @return string
 */
function fashion_brand_theme_format_coupon_discount( $coupon ) {
	$amount = $coupon->get_amount();
	$type   = $coupon->get_discount_type();

	if ( 'percent' === $type ) {
		return sprintf(
			/* translators: %s: percentage amount */
			__( '%s%% off', 'fashion-brand-theme' ),
			wc_format_decimal( $amount, 0 )
		);
	}

	if ( in_array( $type, array( 'fixed_cart', 'fixed_product' ), true ) ) {
		return sprintf(
			/* translators: %s: formatted price */
			__( '%s off', 'fashion-brand-theme' ),
			wc_price( $amount )
		);
	}

	return wc_get_coupon_type( $type );
}

/**
 * Render coupon cards markup.
 *
 * @param WC_Coupon[] $coupons Coupons to render.
 * @return void
 */
function fashion_brand_theme_render_coupon_cards( $coupons ) {
	if ( empty( $coupons ) ) {
		?>
		<div class="account-coupons__empty" role="status">
			<p><?php esc_html_e( 'There are no active coupons right now. Check back soon.', 'fashion-brand-theme' ); ?></p>
		</div>
		<?php
		return;
	}

	echo '<div class="account-coupons__grid">';

	foreach ( $coupons as $coupon ) {
		$code    = $coupon->get_code();
		$expires = $coupon->get_date_expires();
		?>
		<article class="account-coupon-card">
			<p class="account-coupon-card__code" data-coupon-code="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( strtoupper( $code ) ); ?></p>
			<p class="account-coupon-card__discount"><?php echo wp_kses_post( fashion_brand_theme_format_coupon_discount( $coupon ) ); ?></p>
			<?php if ( $expires ) : ?>
				<p class="account-coupon-card__expiry">
					<?php
					printf(
						/* translators: %s: expiry date */
						esc_html__( 'Expires %s', 'fashion-brand-theme' ),
						esc_html( wc_format_datetime( $expires, get_option( 'date_format' ) ) )
					);
					?>
				</p>
			<?php else : ?>
				<p class="account-coupon-card__expiry account-coupon-card__expiry--none"><?php esc_html_e( 'No expiry', 'fashion-brand-theme' ); ?></p>
			<?php endif; ?>
			<button
				type="button"
				class="button account-coupon-card__copy"
				data-coupon-copy
				data-coupon-code="<?php echo esc_attr( $code ); ?>"
				data-label-default="<?php esc_attr_e( 'Copy code', 'fashion-brand-theme' ); ?>"
				data-label-copied="<?php esc_attr_e( 'Copied', 'fashion-brand-theme' ); ?>"
			>
				<?php esc_html_e( 'Copy code', 'fashion-brand-theme' ); ?>
			</button>
		</article>
		<?php
	}

	echo '</div>';
}

/**
 * Coupons endpoint content.
 *
 * @return void
 */
function fashion_brand_theme_account_coupons_endpoint() {
	$coupons = fashion_brand_theme_get_active_coupons();
	?>
	<div class="account-coupons">
		<header class="account-coupons__header">
			<h2 class="account-coupons__title"><?php esc_html_e( 'Coupons', 'fashion-brand-theme' ); ?></h2>
			<p class="account-coupons__intro"><?php esc_html_e( 'Active store offers you can use at checkout.', 'fashion-brand-theme' ); ?></p>
		</header>
		<?php fashion_brand_theme_render_coupon_cards( $coupons ); ?>
	</div>
	<?php
}
add_action( 'woocommerce_account_coupons_endpoint', 'fashion_brand_theme_account_coupons_endpoint' );

/**
 * Wishlist endpoint content.
 *
 * @return void
 */
function fashion_brand_theme_account_wishlist_endpoint() {
	?>
	<div class="account-wishlist">
		<header class="account-wishlist__header">
			<h2 class="account-wishlist__title"><?php esc_html_e( 'Wishlist', 'fashion-brand-theme' ); ?></h2>
		</header>
		<?php get_template_part( 'template-parts/account/wishlist', 'content' ); ?>
	</div>
	<?php
}
add_action( 'woocommerce_account_wishlist_endpoint', 'fashion_brand_theme_account_wishlist_endpoint' );

/**
 * Add Reorder action to My Account orders list.
 *
 * @param array    $actions Order actions.
 * @param WC_Order $order   Order object.
 * @return array
 */
function fashion_brand_theme_account_orders_actions( $actions, $order ) {
	if ( ! $order instanceof WC_Order ) {
		return $actions;
	}

	$order_id = $order->get_id();

	$actions['reorder'] = array(
		'url'        => wp_nonce_url(
			add_query_arg(
				array(
					'fbt_reorder' => $order_id,
				),
				wc_get_page_permalink( 'myaccount' )
			),
			'fbt_reorder_' . $order_id
		),
		'name'       => __( 'Reorder', 'fashion-brand-theme' ),
		/* translators: %s: order number */
		'aria-label' => sprintf( __( 'Reorder order %s', 'fashion-brand-theme' ), $order->get_order_number() ),
	);

	return $actions;
}
add_filter( 'woocommerce_my_account_my_orders_actions', 'fashion_brand_theme_account_orders_actions', 10, 2 );

/**
 * Handle reorder request: add previous order items to the current cart.
 *
 * @return void
 */
function fashion_brand_theme_handle_reorder() {
	if ( empty( $_GET['fbt_reorder'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	if ( ! is_user_logged_in() || ! function_exists( 'WC' ) ) {
		return;
	}

	$order_id = absint( wp_unslash( $_GET['fbt_reorder'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! $order_id || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'fbt_reorder_' . $order_id ) ) {
		wc_add_notice( __( 'This reorder link is invalid or has expired.', 'fashion-brand-theme' ), 'error' );
		wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
		exit;
	}

	$order = wc_get_order( $order_id );

	if ( ! $order || (int) $order->get_customer_id() !== get_current_user_id() ) {
		wc_add_notice( __( 'You cannot reorder this order.', 'fashion-brand-theme' ), 'error' );
		wp_safe_redirect( wc_get_account_endpoint_url( 'orders' ) );
		exit;
	}

	if ( is_null( WC()->cart ) ) {
		wc_load_cart();
	}

	$added   = 0;
	$skipped = 0;

	foreach ( $order->get_items() as $item ) {
		if ( ! $item instanceof WC_Order_Item_Product ) {
			continue;
		}

		$product = $item->get_product();
		$qty     = (int) $item->get_quantity();

		if ( $qty < 1 || ! $product || ! $product->exists() || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
			$skipped++;
			continue;
		}

		if ( $product->managing_stock() && ! $product->has_enough_stock( $qty ) ) {
			$skipped++;
			continue;
		}

		$product_id   = $item->get_product_id();
		$variation_id = $item->get_variation_id();
		$variation    = array();

		if ( $variation_id ) {
			$variation_product = wc_get_product( $variation_id );

			if ( $variation_product && is_a( $variation_product, 'WC_Product_Variation' ) ) {
				$variation = $variation_product->get_variation_attributes();
			}
		}

		$cart_item_key = WC()->cart->add_to_cart( $product_id, $qty, $variation_id, $variation );

		if ( $cart_item_key ) {
			$added++;
		} else {
			$skipped++;
		}
	}

	if ( $added > 0 && 0 === $skipped ) {
		wc_add_notice( __( 'Your previous order items have been added to the cart.', 'fashion-brand-theme' ), 'success' );
	} elseif ( $added > 0 ) {
		wc_add_notice( __( 'Some items from your previous order were added to the cart. Unavailable items were skipped.', 'fashion-brand-theme' ), 'notice' );
	} else {
		wc_add_notice( __( 'None of the items from that order could be added to the cart.', 'fashion-brand-theme' ), 'error' );
		wp_safe_redirect( wc_get_account_endpoint_url( 'orders' ) );
		exit;
	}

	wp_safe_redirect( wc_get_cart_url() );
	exit;
}
add_action( 'template_redirect', 'fashion_brand_theme_handle_reorder' );

/**
 * Custom My Account dashboard content.
 *
 * @return void
 */
function fashion_brand_theme_account_dashboard() {
	$user       = wp_get_current_user();
	$first_name = $user->first_name ? $user->first_name : $user->display_name;
	$orders     = array();

	if ( function_exists( 'wc_get_orders' ) ) {
		$orders = wc_get_orders(
			array(
				'customer' => get_current_user_id(),
				'limit'    => 1,
				'orderby'  => 'date',
				'order'    => 'DESC',
			)
		);
	}

	$recent  = ! empty( $orders ) ? $orders[0] : null;
	$coupons = fashion_brand_theme_get_active_coupons( 3 );
	?>
	<div class="account-dashboard">
		<header class="account-dashboard__welcome">
			<h2 class="account-dashboard__heading">
				<?php
				printf(
					/* translators: %s: customer first name */
					esc_html__( 'Welcome back, %s', 'fashion-brand-theme' ),
					esc_html( $first_name )
				);
				?>
			</h2>
			<p class="account-dashboard__sub"><?php esc_html_e( 'Manage your orders, addresses, wishlist, and offers from here.', 'fashion-brand-theme' ); ?></p>
		</header>

		<?php if ( $recent instanceof WC_Order ) : ?>
			<section class="account-dashboard__recent account-order-card account-order-card--summary">
				<div class="account-order-card__top">
					<p class="account-order-card__number">
						<?php
						printf(
							/* translators: %s: order number */
							esc_html__( 'Order #%s', 'fashion-brand-theme' ),
							esc_html( $recent->get_order_number() )
						);
						?>
					</p>
					<span class="account-order-card__status account-order-card__status--<?php echo esc_attr( $recent->get_status() ); ?>">
						<?php echo esc_html( wc_get_order_status_name( $recent->get_status() ) ); ?>
					</span>
				</div>
				<p class="account-order-card__meta">
					<time datetime="<?php echo esc_attr( $recent->get_date_created()->date( 'c' ) ); ?>">
						<?php echo esc_html( wc_format_datetime( $recent->get_date_created() ) ); ?>
					</time>
					<span class="account-order-card__total"><?php echo wp_kses_post( $recent->get_formatted_order_total() ); ?></span>
				</p>
				<a class="button button--primary" href="<?php echo esc_url( $recent->get_view_order_url() ); ?>">
					<?php esc_html_e( 'View order', 'fashion-brand-theme' ); ?>
				</a>
			</section>
		<?php else : ?>
			<section class="account-dashboard__recent account-dashboard__recent--empty">
				<p><?php esc_html_e( 'You have not placed any orders yet.', 'fashion-brand-theme' ); ?></p>
				<a class="button button--primary" href="<?php echo esc_url( fashion_brand_theme_get_shop_url() ); ?>">
					<?php esc_html_e( 'Start shopping', 'fashion-brand-theme' ); ?>
				</a>
			</section>
		<?php endif; ?>

		<nav class="account-dashboard__quick-links" aria-label="<?php esc_attr_e( 'Account shortcuts', 'fashion-brand-theme' ); ?>">
			<a class="account-dashboard__quick-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'Orders', 'fashion-brand-theme' ); ?></a>
			<a class="account-dashboard__quick-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>"><?php esc_html_e( 'Addresses', 'fashion-brand-theme' ); ?></a>
			<a class="account-dashboard__quick-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'wishlist' ) ); ?>"><?php esc_html_e( 'Wishlist', 'fashion-brand-theme' ); ?></a>
			<a class="account-dashboard__quick-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'coupons' ) ); ?>"><?php esc_html_e( 'Coupons', 'fashion-brand-theme' ); ?></a>
		</nav>

		<?php if ( ! empty( $coupons ) ) : ?>
			<section class="account-dashboard__coupons">
				<div class="account-dashboard__coupons-head">
					<h3 class="account-dashboard__coupons-title"><?php esc_html_e( 'Active offers', 'fashion-brand-theme' ); ?></h3>
					<a class="account-dashboard__coupons-all" href="<?php echo esc_url( wc_get_account_endpoint_url( 'coupons' ) ); ?>">
						<?php esc_html_e( 'See all coupons', 'fashion-brand-theme' ); ?>
					</a>
				</div>
				<?php fashion_brand_theme_render_coupon_cards( $coupons ); ?>
			</section>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'woocommerce_account_dashboard', 'fashion_brand_theme_account_dashboard' );
