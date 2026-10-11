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
define( 'FASHION_BRAND_THEME_ACCOUNT_ENDPOINTS_VERSION', '2' );

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
 * Dequeue WooCommerce password strength meter on account routes (plain password fields).
 *
 * @return void
 */
function fashion_brand_theme_account_dequeue_password_meter() {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return;
	}

	wp_dequeue_script( 'wc-password-strength-meter' );
}
add_action( 'wp_enqueue_scripts', 'fashion_brand_theme_account_dequeue_password_meter', 100 );

/**
 * Format a stat count for dashboard display (two digits, max 99).
 *
 * @param int $count Raw count.
 * @return string
 */
function fashion_brand_theme_format_account_stat( $count ) {
	$count = max( 0, (int) $count );

	if ( $count > 99 ) {
		return '99+';
	}

	return str_pad( (string) $count, 2, '0', STR_PAD_LEFT );
}

/**
 * Control My Account menu order and labels.
 *
 * @param array $items Menu items.
 * @return array
 */
function fashion_brand_theme_account_menu_items( $items ) {
	return array(
		'dashboard'       => __( 'Overview', 'fashion-brand-theme' ),
		'orders'          => __( 'Orders', 'fashion-brand-theme' ),
		'edit-address'    => __( 'Addresses', 'fashion-brand-theme' ),
		'wishlist'        => __( 'Wishlist', 'fashion-brand-theme' ),
		'coupons'         => __( 'Offers', 'fashion-brand-theme' ),
		'edit-account'    => __( 'Details', 'fashion-brand-theme' ),
		'customer-logout' => __( 'Sign out', 'fashion-brand-theme' ),
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
		return __( 'Offers', 'fashion-brand-theme' );
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
function fashion_brand_theme_render_coupon_cards( $coupons, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'show_copy'   => true,
			'show_expiry' => true,
		)
	);

	if ( empty( $coupons ) ) {
		?>
		<p class="block-empty" role="status"><?php esc_html_e( 'There are no active offers right now. Check back soon.', 'fashion-brand-theme' ); ?></p>
		<?php
		return;
	}

	echo '<div class="coupon-grid">';

	foreach ( $coupons as $coupon ) {
		$code    = $coupon->get_code();
		$expires = $coupon->get_date_expires();
		?>
		<div class="coupon">
			<div class="coupon-code"><?php echo esc_html( strtoupper( $code ) ); ?></div>
			<p class="coupon-desc"><?php echo wp_kses_post( fashion_brand_theme_format_coupon_discount( $coupon ) ); ?></p>
			<?php if ( $args['show_expiry'] ) : ?>
				<?php if ( $expires ) : ?>
					<p class="coupon-expiry">
						<?php
						printf(
							/* translators: %s: expiry date */
							esc_html__( 'Expires %s', 'fashion-brand-theme' ),
							esc_html( wc_format_datetime( $expires, get_option( 'date_format' ) ) )
						);
						?>
					</p>
				<?php else : ?>
					<p class="coupon-expiry"><?php esc_html_e( 'No expiry', 'fashion-brand-theme' ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ( $args['show_copy'] ) : ?>
				<button
					type="button"
					class="text-link copy-btn"
					data-code="<?php echo esc_attr( $code ); ?>"
					data-coupon-copy
					data-coupon-code="<?php echo esc_attr( $code ); ?>"
					data-label-default="<?php esc_attr_e( 'Copy code ↗', 'fashion-brand-theme' ); ?>"
					data-label-copied="<?php esc_attr_e( 'Copied ✓', 'fashion-brand-theme' ); ?>"
				>
					<?php esc_html_e( 'Copy code ↗', 'fashion-brand-theme' ); ?>
				</button>
			<?php endif; ?>
		</div>
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
	$count   = count( $coupons );
	?>
	<div class="block">
		<div class="block-head">
			<h2><?php esc_html_e( 'Your member offers', 'fashion-brand-theme' ); ?></h2>
			<span class="micro">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: offer count */
						_n( '%s active', '%s active', $count, 'fashion-brand-theme' ),
						fashion_brand_theme_format_account_stat( $count )
					)
				);
				?>
			</span>
		</div>
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
	$wishlist_ids = fashion_brand_theme_get_wishlist_ids();
	$count        = count( $wishlist_ids );
	?>
	<div class="block wishlist-account-block">
		<div class="block-head">
			<h2><?php esc_html_e( 'Saved for later', 'fashion-brand-theme' ); ?></h2>
			<span class="micro">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: piece count */
						_n( '%s piece', '%s pieces', $count, 'fashion-brand-theme' ),
						fashion_brand_theme_format_account_stat( $count )
					)
				);
				?>
			</span>
		</div>
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

if ( ! defined( 'FASHION_BRAND_THEME_ACCOUNT_AVATAR_META' ) ) {
	define( 'FASHION_BRAND_THEME_ACCOUNT_AVATAR_META', 'fashion_brand_theme_avatar_id' );
}

/**
 * Get the URL for a user's custom account avatar.
 *
 * @param int $user_id User ID. 0 = current user.
 * @return string
 */
function fashion_brand_theme_get_account_avatar_url( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	if ( ! $user_id ) {
		return '';
	}

	$attachment_id = (int) get_user_meta( $user_id, FASHION_BRAND_THEME_ACCOUNT_AVATAR_META, true );

	if ( ! $attachment_id ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $attachment_id, 'medium' );

	if ( ! $url ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
	}

	return $url ? $url : '';
}

/**
 * Derive display initials for an account avatar fallback.
 *
 * @param WP_User|null $user User object.
 * @return string
 */
function fashion_brand_theme_get_account_initials( $user = null ) {
	if ( ! $user instanceof WP_User ) {
		$user = wp_get_current_user();
	}

	if ( ! $user || ! $user->ID ) {
		return '';
	}

	$first = trim( (string) $user->first_name );
	$last  = trim( (string) $user->last_name );

	if ( $first && $last ) {
		return strtoupper( mb_substr( $first, 0, 1 ) . mb_substr( $last, 0, 1 ) );
	}

	$source = $first ? $first : ( $user->display_name ? $user->display_name : $user->user_login );

	return strtoupper( mb_substr( $source, 0, 1 ) );
}

/**
 * Render account avatar markup.
 *
 * @param array $args {
 *     @type string $size     md|lg|profile.
 *     @type bool   $editable Legacy; unused in v3 (use Change photo label).
 *     @type int    $user_id  User ID.
 * }
 * @return void
 */
function fashion_brand_theme_render_account_avatar( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'size'     => 'md',
			'editable' => false,
			'user_id'  => get_current_user_id(),
		)
	);

	$user_id  = (int) $args['user_id'];
	$url      = fashion_brand_theme_get_account_avatar_url( $user_id );
	$user     = get_userdata( $user_id );
	$initials = fashion_brand_theme_get_account_initials( $user );
	$classes  = array( 'avatar' );

	if ( 'lg' === $args['size'] ) {
		$classes[] = 'large';
	}

	if ( 'profile' === $args['size'] ) {
		$classes[] = 'large';
		$classes[] = 'profile';
	}

	if ( $url ) {
		$classes[] = 'avatar--has-img';
	}

	echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-account-avatar data-initials="' . esc_attr( $initials ) . '">';

	if ( $url ) {
		printf(
			'<img class="avatar__img" src="%1$s" alt="" width="96" height="96" decoding="async" />',
			esc_url( $url )
		);
	} else {
		echo esc_html( $initials );
	}

	echo '</div>';
}

/**
 * CSS class for v3 order status row (.status.done / .status.cancelled).
 *
 * @param string $status Order status slug.
 * @return string
 */
function fashion_brand_theme_get_order_status_row_class( $status ) {
	$classes = array( 'status' );

	if ( in_array( $status, array( 'completed', 'complete' ), true ) ) {
		$classes[] = 'done';
	} elseif ( in_array( $status, array( 'cancelled', 'refunded', 'failed' ), true ) ) {
		$classes[] = 'cancelled';
	}

	return implode( ' ', $classes );
}

/**
 * CSS class string for an order status badge (legacy).
 *
 * @param string $status Order status slug.
 * @return string
 */
function fashion_brand_theme_get_order_status_badge_class( $status ) {
	return fashion_brand_theme_get_order_status_row_class( $status );
}

/**
 * Collect product thumbnail markup for an order (up to N line items).
 *
 * @param WC_Order $order Order.
 * @param int      $limit Max thumbnails.
 * @return string[]
 */
function fashion_brand_theme_get_order_row_thumbnail( $order ) {
	if ( ! $order instanceof WC_Order ) {
		return '';
	}

	foreach ( $order->get_items() as $item ) {
		if ( ! $item instanceof WC_Order_Item_Product ) {
			continue;
		}

		$product = $item->get_product();

		if ( $product && $product->get_image_id() ) {
			return $product->get_image(
				'woocommerce_thumbnail',
				array(
					'class' => 'order-thumb__img',
				)
			);
		}

		break;
	}

	return '';
}

/**
 * Render a single order row for dashboard / orders list.
 *
 * @param WC_Order $order Order object.
 * @param array    $args {
 *     @type bool   $show_reorder  Include reorder action when available.
 *     @type bool   $placed_prefix Prefix meta with "Placed" (dashboard recent order).
 *     @type string $view_label    View link label.
 * }
 * @return void
 */
function fashion_brand_theme_render_account_order_row( $order, $args = array() ) {
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array(
			'show_reorder'  => true,
			'placed_prefix' => false,
			'view_label'    => __( 'View order ↗', 'fashion-brand-theme' ),
		)
	);

	$status   = $order->get_status();
	$actions  = wc_get_account_orders_actions( $order );
	$thumb    = fashion_brand_theme_get_order_row_thumbnail( $order );
	$date_str = wc_format_datetime( $order->get_date_created(), get_option( 'date_format' ) );
	$total    = wp_strip_all_tags( $order->get_formatted_order_total() );

	if ( $args['placed_prefix'] ) {
		$meta = sprintf(
			/* translators: 1: order date 2: order total */
			__( 'Placed %1$s · %2$s', 'fashion-brand-theme' ),
			$date_str,
			$total
		);
	} else {
		$meta = $date_str . ' · ' . $total;
	}

	$view_url     = '';
	$reorder_url  = '';
	$view_aria    = '';
	$reorder_aria = '';

	foreach ( $actions as $key => $action ) {
		if ( 'view' === $key ) {
			$view_url  = $action['url'];
			$view_aria = ! empty( $action['aria-label'] )
				? $action['aria-label']
				: sprintf(
					/* translators: %s: order number */
					__( 'View order %s', 'fashion-brand-theme' ),
					$order->get_order_number()
				);
		}

		if ( 'reorder' === $key && $args['show_reorder'] ) {
			$reorder_url  = $action['url'];
			$reorder_aria = ! empty( $action['aria-label'] ) ? $action['aria-label'] : $action['name'];
		}
	}
	?>
	<div class="order-row woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $status ); ?>">
		<div class="order-thumb">
			<?php
			if ( $thumb ) {
				echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
		<div>
			<div class="order-id">
				<?php
				printf(
					/* translators: %s: order number */
					esc_html__( '#%s', 'fashion-brand-theme' ),
					esc_html( $order->get_order_number() )
				);
				?>
			</div>
			<div class="meta"><?php echo esc_html( $meta ); ?></div>
		</div>
		<div class="order-right">
			<div class="<?php echo esc_attr( fashion_brand_theme_get_order_status_row_class( $status ) ); ?>">
				<span class="dot" aria-hidden="true"></span>
				<?php echo esc_html( wc_get_order_status_name( $status ) ); ?>
			</div>
			<?php if ( $view_url || $reorder_url ) : ?>
				<p class="order-actions">
					<?php if ( $view_url ) : ?>
						<a class="text-link woocommerce-button view" href="<?php echo esc_url( $view_url ); ?>" aria-label="<?php echo esc_attr( $view_aria ); ?>">
							<?php echo esc_html( $args['view_label'] ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $reorder_url ) : ?>
						<?php if ( $view_url ) : ?>
							<span class="order-actions__sep" aria-hidden="true"> · </span>
						<?php endif; ?>
						<a class="text-link woocommerce-button reorder" href="<?php echo esc_url( $reorder_url ); ?>" aria-label="<?php echo esc_attr( $reorder_aria ); ?>">
							<?php esc_html_e( 'Reorder', 'fashion-brand-theme' ); ?>
						</a>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * @param WC_Order $order Order.
 * @param array    $args  Arguments.
 * @return void
 */
function fashion_brand_theme_render_account_order_card( $order, $args = array() ) {
	fashion_brand_theme_render_account_order_row( $order, $args );
}

/**
 * Process an account avatar upload for a user.
 *
 * @param int   $user_id User ID.
 * @param array $file    Single $_FILES entry.
 * @return array|WP_Error { attachment_id: int, url: string }
 */
function fashion_brand_theme_process_account_avatar_upload( $user_id, $file ) {
	$user_id = (int) $user_id;

	if ( ! $user_id || empty( $file['name'] ) || empty( $file['tmp_name'] ) ) {
		return new WP_Error( 'avatar_missing', __( 'Please choose an image to upload.', 'fashion-brand-theme' ) );
	}

	if ( ! empty( $file['error'] ) ) {
		return new WP_Error( 'avatar_upload_error', __( 'The image could not be uploaded. Please try again.', 'fashion-brand-theme' ) );
	}

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$allowed = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'gif'          => 'image/gif',
		'png'          => 'image/png',
		'webp'         => 'image/webp',
	);

	$upload = wp_handle_upload(
		$file,
		array(
			'test_form' => false,
			'mimes'     => $allowed,
		)
	);

	if ( isset( $upload['error'] ) ) {
		return new WP_Error( 'avatar_upload_error', $upload['error'] );
	}

	$attachment = array(
		'post_mime_type' => $upload['type'],
		'post_title'     => sanitize_file_name( wp_basename( $upload['file'] ) ),
		'post_content'   => '',
		'post_status'    => 'inherit',
		'post_author'    => $user_id,
	);

	$attachment_id = wp_insert_attachment( $attachment, $upload['file'] );

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return new WP_Error( 'avatar_attach_error', __( 'The image could not be saved. Please try again.', 'fashion-brand-theme' ) );
	}

	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
	wp_update_attachment_metadata( $attachment_id, $metadata );

	$previous_id = (int) get_user_meta( $user_id, FASHION_BRAND_THEME_ACCOUNT_AVATAR_META, true );

	update_user_meta( $user_id, FASHION_BRAND_THEME_ACCOUNT_AVATAR_META, $attachment_id );

	if ( $previous_id && $previous_id !== (int) $attachment_id ) {
		wp_delete_attachment( $previous_id, true );
	}

	$url = wp_get_attachment_image_url( $attachment_id, 'medium' );

	if ( ! $url ) {
		$url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
	}

	if ( ! $url ) {
		$url = $upload['url'];
	}

	return array(
		'attachment_id' => (int) $attachment_id,
		'url'           => $url,
	);
}

/**
 * Save uploaded account avatar on account details save (legacy form fallback).
 *
 * @param int $user_id User ID.
 * @return void
 */
function fashion_brand_theme_save_account_avatar( $user_id ) {
	if ( empty( $_FILES['account_avatar']['name'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	$result = fashion_brand_theme_process_account_avatar_upload(
		$user_id,
		$_FILES['account_avatar'] // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	);

	if ( is_wp_error( $result ) ) {
		wc_add_notice( $result->get_error_message(), 'error' );
	}
}
add_action( 'woocommerce_save_account_details', 'fashion_brand_theme_save_account_avatar', 10, 1 );

/**
 * AJAX: upload account avatar immediately from the change-photo modal.
 *
 * @return void
 */
function fashion_brand_theme_ajax_upload_account_avatar() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_error(
			array( 'message' => __( 'You must be signed in to change your photo.', 'fashion-brand-theme' ) ),
			401
		);
	}

	check_ajax_referer( 'fashion_brand_theme_avatar_upload', 'nonce' );

	$user_id = get_current_user_id();

	if ( empty( $_FILES['account_avatar'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		wp_send_json_error(
			array( 'message' => __( 'Please choose an image to upload.', 'fashion-brand-theme' ) ),
			400
		);
	}

	$result = fashion_brand_theme_process_account_avatar_upload(
		$user_id,
		$_FILES['account_avatar'] // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	);

	if ( is_wp_error( $result ) ) {
		wp_send_json_error(
			array( 'message' => $result->get_error_message() ),
			400
		);
	}

	wp_send_json_success(
		array(
			'url'           => $result['url'],
			'attachment_id' => $result['attachment_id'],
			'message'       => __( 'Photo saved.', 'fashion-brand-theme' ),
		)
	);
}
add_action( 'wp_ajax_fashion_brand_theme_upload_avatar', 'fashion_brand_theme_ajax_upload_account_avatar' );

/**
 * Render the shared auth-screen visual panel (login / register / lost / reset).
 *
 * @return void
 */
function fashion_brand_theme_render_auth_visual() {
	$logo = fashion_brand_theme_get_brand_asset_uri( 'logo/wren-wold-logo-white.svg' );
	?>
	<div class="auth-visual">
		<div class="tag"><?php esc_html_e( 'WREN WOLD / Member space', 'fashion-brand-theme' ); ?></div>
		<?php if ( $logo ) : ?>
			<div class="auth-visual__logo" aria-hidden="true">
				<img src="<?php echo esc_url( $logo ); ?>" alt="" width="200" height="76" decoding="async" />
			</div>
		<?php endif; ?>
		<div class="visual-copy">
			<small><?php esc_html_e( 'Made for the considered life', 'fashion-brand-theme' ); ?></small>
			<h2><?php esc_html_e( 'Less, but', 'fashion-brand-theme' ); ?><br><em><?php esc_html_e( 'better.', 'fashion-brand-theme' ); ?></em></h2>
		</div>
		<div class="bottom-note"><?php esc_html_e( 'Personal account · Private access', 'fashion-brand-theme' ); ?></div>
	</div>
	<?php
}

/**
 * Before WC validates account details, ensure display name is present in POST
 * (the theme collapses that field visually, so it can be blank for new users).
 *
 * @return void
 */
function fashion_brand_theme_prefill_account_display_name_post() {
	if ( empty( $_POST['action'] ) || 'save_account_details' !== $_POST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	if ( ! empty( $_POST['account_display_name'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	$first = ! empty( $_POST['account_first_name'] ) ? wc_clean( wp_unslash( $_POST['account_first_name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$last  = ! empty( $_POST['account_last_name'] ) ? wc_clean( wp_unslash( $_POST['account_last_name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$email = ! empty( $_POST['account_email'] ) ? wc_clean( wp_unslash( $_POST['account_email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

	$fallback = trim( $first . ' ' . $last );

	if ( '' === $fallback && $email ) {
		$fallback = $email;
	}

	if ( '' === $fallback ) {
		$current  = wp_get_current_user();
		$fallback = ( $current && $current->user_login ) ? $current->user_login : 'member';
	}

	$_POST['account_display_name'] = $fallback;
}
add_action( 'wp_loaded', 'fashion_brand_theme_prefill_account_display_name_post', 5 );
