<?php
/**
 * Account-based wishlist (customer user meta).
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User meta key for wishlist product IDs.
 */
define( 'FASHION_BRAND_THEME_WISHLIST_META', '_fbt_wishlist' );

/**
 * Get wishlist product IDs for a user.
 *
 * @param int $user_id User ID. Defaults to current user.
 * @return int[]
 */
function fashion_brand_theme_get_wishlist_ids( $user_id = 0 ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();
	if ( $user_id <= 0 ) {
		return array();
	}

	$ids = get_user_meta( $user_id, FASHION_BRAND_THEME_WISHLIST_META, true );
	if ( ! is_array( $ids ) ) {
		return array();
	}

	$ids = array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );

	return $ids;
}

/**
 * Persist wishlist product IDs for a user.
 *
 * @param int   $user_id User ID.
 * @param int[] $ids     Product IDs.
 * @return void
 */
function fashion_brand_theme_set_wishlist_ids( $user_id, $ids ) {
	$user_id = (int) $user_id;
	if ( $user_id <= 0 ) {
		return;
	}

	$ids = array_values( array_unique( array_filter( array_map( 'absint', $ids ) ) ) );
	update_user_meta( $user_id, FASHION_BRAND_THEME_WISHLIST_META, $ids );
}

/**
 * Whether a product ID is a valid published product.
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function fashion_brand_theme_is_valid_wishlist_product( $product_id ) {
	$product_id = absint( $product_id );
	if ( $product_id <= 0 || ! function_exists( 'wc_get_product' ) ) {
		return false;
	}

	$product = wc_get_product( $product_id );
	if ( ! $product || 'publish' !== get_post_status( $product_id ) ) {
		return false;
	}

	return true;
}

/**
 * Localized wishlist config for front-end scripts.
 *
 * @return array<string, mixed>
 */
function fashion_brand_theme_wishlist_script_data() {
	return array(
		'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
		'nonce'      => wp_create_nonce( 'fbt_wishlist' ),
		'isLoggedIn' => is_user_logged_in() ? 1 : 0,
		'accountUrl' => function_exists( 'fashion_brand_theme_get_account_url' ) ? fashion_brand_theme_get_account_url() : '',
		'i18n'       => array(
			'signIn' => __( 'Sign in to save favorites', 'fashion-brand-theme' ),
			'signInCta' => __( 'Sign in', 'fashion-brand-theme' ),
		),
	);
}

/**
 * AJAX: toggle a product on the current user's wishlist.
 *
 * @return void
 */
function fashion_brand_theme_ajax_wishlist_toggle() {
	check_ajax_referer( 'fbt_wishlist', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error(
			array( 'message' => __( 'You must be signed in to save favorites.', 'fashion-brand-theme' ) ),
			401
		);
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
	if ( ! fashion_brand_theme_is_valid_wishlist_product( $product_id ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Invalid product.', 'fashion-brand-theme' ) ),
			400
		);
	}

	$user_id = get_current_user_id();
	$ids     = fashion_brand_theme_get_wishlist_ids( $user_id );
	$added   = false;
	$index   = array_search( $product_id, $ids, true );

	if ( false === $index ) {
		$ids[] = $product_id;
		$added = true;
	} else {
		array_splice( $ids, (int) $index, 1 );
		$added = false;
	}

	fashion_brand_theme_set_wishlist_ids( $user_id, $ids );

	wp_send_json_success(
		array(
			'added'      => $added,
			'count'      => count( $ids ),
			'ids'        => $ids,
			'product_id' => $product_id,
		)
	);
}
add_action( 'wp_ajax_fbt_wishlist_toggle', 'fashion_brand_theme_ajax_wishlist_toggle' );

/**
 * AJAX: return the current user's wishlist IDs.
 *
 * @return void
 */
function fashion_brand_theme_ajax_wishlist_get() {
	check_ajax_referer( 'fbt_wishlist', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error(
			array( 'message' => __( 'You must be signed in.', 'fashion-brand-theme' ) ),
			401
		);
	}

	$ids = fashion_brand_theme_get_wishlist_ids();

	wp_send_json_success(
		array(
			'ids'   => $ids,
			'count' => count( $ids ),
		)
	);
}
add_action( 'wp_ajax_fbt_wishlist_get', 'fashion_brand_theme_ajax_wishlist_get' );
