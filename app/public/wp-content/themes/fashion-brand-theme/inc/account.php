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
		'customer-logout' => __( 'Log Out', 'fashion-brand-theme' ),
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
		<p class="acct-empty" role="status"><?php esc_html_e( 'There are no active coupons right now. Check back soon.', 'fashion-brand-theme' ); ?></p>
		<?php
		return;
	}

	echo '<div class="coupon-row">';

	foreach ( $coupons as $coupon ) {
		$code    = $coupon->get_code();
		$expires = $coupon->get_date_expires();
		?>
		<div class="coupon-card">
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
					class="btn btn--sm copy-btn"
					data-code="<?php echo esc_attr( $code ); ?>"
					data-coupon-copy
					data-coupon-code="<?php echo esc_attr( $code ); ?>"
					data-label-default="<?php esc_attr_e( 'Copy Code', 'fashion-brand-theme' ); ?>"
					data-label-copied="<?php esc_attr_e( 'Copied!', 'fashion-brand-theme' ); ?>"
				>
					<?php esc_html_e( 'Copy Code', 'fashion-brand-theme' ); ?>
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
	?>
	<div class="acct-card">
		<p class="acct-section-label"><?php esc_html_e( 'Available Coupons', 'fashion-brand-theme' ); ?></p>
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
	$shop_url     = fashion_brand_theme_get_shop_url();
	?>
	<div class="acct-card">
		<p class="acct-section-label"><?php esc_html_e( 'Your Wishlist', 'fashion-brand-theme' ); ?></p>
		<?php if ( empty( $wishlist_ids ) ) : ?>
			<p class="acct-empty"><?php esc_html_e( 'Your wishlist is empty.', 'fashion-brand-theme' ); ?></p>
			<a class="btn btn--primary" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop now', 'fashion-brand-theme' ); ?></a>
		<?php else : ?>
			<div class="wishlist-grid">
				<?php
				foreach ( $wishlist_ids as $product_id ) {
					$product = wc_get_product( $product_id );

					if ( ! $product || ! $product->is_visible() ) {
						continue;
					}
					?>
					<a class="wishlist-item" href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<?php
						echo $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'wishlist-item__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
						<p class="wishlist-item__name"><?php echo esc_html( $product->get_name() ); ?></p>
						<p class="wishlist-item__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
					</a>
					<?php
				}
				?>
			</div>
		<?php endif; ?>
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

	$url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );

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
 *     @type string $size     md|lg.
 *     @type bool   $editable Show camera edit badge linked to #account_avatar.
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
	$classes  = array( 'acct-avatar' );

	if ( 'lg' === $args['size'] ) {
		$classes[] = 'acct-avatar--lg';
	}

	if ( $url ) {
		$classes[] = 'acct-avatar--has-img';
	}

	echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';

	if ( $url ) {
		printf(
			'<img class="acct-avatar__img" src="%1$s" alt="" width="96" height="96" decoding="async" />',
			esc_url( $url )
		);
	} else {
		echo esc_html( $initials );
	}

	if ( $args['editable'] ) {
		echo '<label class="acct-avatar-edit" for="account_avatar">';
		echo fashion_brand_theme_get_icon( 'camera', array( 'width' => 14, 'height' => 14 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</label>';
	}

	echo '</div>';
}

/**
 * CSS class string for an order status badge.
 *
 * @param string $status Order status slug.
 * @return string
 */
function fashion_brand_theme_get_order_status_badge_class( $status ) {
	return 'status-badge status-badge--' . sanitize_html_class( $status );
}

/**
 * Collect product thumbnail markup for an order (up to N line items).
 *
 * @param WC_Order $order Order.
 * @param int      $limit Max thumbnails.
 * @return string[]
 */
function fashion_brand_theme_get_order_card_thumbnails( $order, $limit = 3 ) {
	$thumbs = array();

	if ( ! $order instanceof WC_Order ) {
		return $thumbs;
	}

	foreach ( $order->get_items() as $item ) {
		if ( ! $item instanceof WC_Order_Item_Product ) {
			continue;
		}

		$product = $item->get_product();

		if ( $product && $product->get_image_id() ) {
			$thumbs[] = $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'order-card__thumb' ) );
		} else {
			$thumbs[] = '<span class="order-card__thumb order-card__thumb--placeholder" aria-hidden="true"></span>';
		}

		if ( count( $thumbs ) >= $limit ) {
			break;
		}
	}

	return $thumbs;
}

/**
 * Render a single order card for dashboard / orders list.
 *
 * @param WC_Order $order Order object.
 * @param array    $args {
 *     @type bool $show_reorder Include reorder action when available.
 *     @type bool $placed_prefix Prefix meta with "Placed" (dashboard recent order).
 * }
 * @return void
 */
function fashion_brand_theme_render_account_order_card( $order, $args = array() ) {
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array(
			'show_reorder'   => true,
			'placed_prefix'  => false,
			'view_label'     => __( 'View Order', 'fashion-brand-theme' ),
		)
	);

	$status   = $order->get_status();
	$actions  = wc_get_account_orders_actions( $order );
	$thumbs   = fashion_brand_theme_get_order_card_thumbnails( $order, 3 );
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
	?>
	<div class="order-card woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $status ); ?>">
		<?php if ( ! empty( $thumbs ) ) : ?>
			<div class="order-card__thumbs">
				<?php echo implode( '', $thumbs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>
		<div class="order-card__info">
			<p class="order-card__id">
				<?php
				printf(
					/* translators: %s: order number */
					esc_html__( '#%s', 'fashion-brand-theme' ),
					esc_html( $order->get_order_number() )
				);
				?>
			</p>
			<p class="order-card__meta"><?php echo esc_html( $meta ); ?></p>
		</div>
		<span class="<?php echo esc_attr( fashion_brand_theme_get_order_status_badge_class( $status ) ); ?>">
			<?php echo esc_html( wc_get_order_status_name( $status ) ); ?>
		</span>
		<div class="order-card__actions">
			<?php
			foreach ( $actions as $key => $action ) {
				if ( ! $args['show_reorder'] && 'reorder' === $key ) {
					continue;
				}

				if ( empty( $action['aria-label'] ) ) {
					/* translators: %1$s Action name, %2$s Order number. */
					$action_aria_label = sprintf( __( '%1$s order number %2$s', 'woocommerce' ), $action['name'], $order->get_order_number() );
				} else {
					$action_aria_label = $action['aria-label'];
				}

				$btn_class = 'btn btn--sm woocommerce-button ' . sanitize_html_class( $key );

				if ( in_array( $key, array( 'pay', 'reorder' ), true ) ) {
					$btn_class .= ' btn--primary';
				}

				if ( 'view' === $key && ! empty( $args['view_label'] ) ) {
					$action['name'] = $args['view_label'];
				}

				printf(
					'<a href="%1$s" class="%2$s" aria-label="%3$s">%4$s</a>',
					esc_url( $action['url'] ),
					esc_attr( $btn_class ),
					esc_attr( $action_aria_label ),
					esc_html( $action['name'] )
				);
			}
			?>
		</div>
	</div>
	<?php
}

/**
 * Add multipart encoding to the edit-account form for avatar uploads.
 *
 * @return void
 */
function fashion_brand_theme_edit_account_form_enctype() {
	echo ' enctype="multipart/form-data"';
}
add_action( 'woocommerce_edit_account_form_tag', 'fashion_brand_theme_edit_account_form_enctype' );

/**
 * Save uploaded account avatar on account details save.
 *
 * @param int $user_id User ID.
 * @return void
 */
function fashion_brand_theme_save_account_avatar( $user_id ) {
	if ( empty( $_FILES['account_avatar']['name'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	if ( ! function_exists( 'wp_handle_upload' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$file = $_FILES['account_avatar']; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

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
		wc_add_notice( $upload['error'], 'error' );
		return;
	}

	$attachment = array(
		'post_mime_type' => $upload['type'],
		'post_title'     => sanitize_file_name( wp_basename( $upload['file'] ) ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attachment_id = wp_insert_attachment( $attachment, $upload['file'] );

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return;
	}

	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
	wp_update_attachment_metadata( $attachment_id, $metadata );

	$previous_id = (int) get_user_meta( $user_id, FASHION_BRAND_THEME_ACCOUNT_AVATAR_META, true );

	update_user_meta( $user_id, FASHION_BRAND_THEME_ACCOUNT_AVATAR_META, $attachment_id );

	if ( $previous_id && $previous_id !== $attachment_id ) {
		wp_delete_attachment( $previous_id, true );
	}
}
add_action( 'woocommerce_save_account_details', 'fashion_brand_theme_save_account_avatar', 10, 1 );
