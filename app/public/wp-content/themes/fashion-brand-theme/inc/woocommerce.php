<?php
/**
 * WooCommerce theme integration.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register WooCommerce theme support when the plugin is active.
 */
function fashion_brand_theme_woocommerce_setup() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 800,
			'single_image_width'    => 1200,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);

	// Custom gallery — default WC zoom / lightbox / slider not enabled.
}
add_action( 'after_setup_theme', 'fashion_brand_theme_woocommerce_setup' );

/**
 * Theme content wrappers for WooCommerce pages.
 */
function fashion_brand_theme_wc_wrapper_start() {
	echo '<main id="primary" class="site-main site-main--shop">';
}

/**
 * Close theme content wrappers for WooCommerce pages.
 */
function fashion_brand_theme_wc_wrapper_end() {
	echo '</main>';
}

/**
 * Replace default WooCommerce wrappers and trim the product loop.
 */
function fashion_brand_theme_woocommerce_hooks() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_before_main_content', 'fashion_brand_theme_wc_wrapper_start', 10 );
	add_action( 'woocommerce_after_main_content', 'fashion_brand_theme_wc_wrapper_end', 10 );

	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

	// Custom shop bar renders count + ordering.
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

	add_filter( 'woocommerce_catalog_orderby', 'fashion_brand_theme_catalog_orderby_labels' );

	// Custom breadcrumbs on product + archive templates.
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

	// Tabs + related are placed manually in content-single-product.php.
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
}
add_action( 'wp', 'fashion_brand_theme_woocommerce_hooks' );

/**
 * Force four-column shop grid to match the approved layout.
 *
 * @return int
 */
function fashion_brand_theme_loop_shop_columns() {
	return 4;
}
add_filter( 'loop_shop_columns', 'fashion_brand_theme_loop_shop_columns', 30 );

/**
 * Rename default catalog orderby label to Featured.
 *
 * @param array $options Orderby options.
 * @return array
 */
function fashion_brand_theme_catalog_orderby_labels( $options ) {
	if ( isset( $options['menu_order'] ) ) {
		$options['menu_order'] = __( 'Featured', 'fashion-brand-theme' );
	}

	return $options;
}

/**
 * Body class for shop/product templates.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function fashion_brand_theme_woocommerce_body_class( $classes ) {
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		$classes[] = 'woocommerce-brand';
	}

	return $classes;
}
add_filter( 'body_class', 'fashion_brand_theme_woocommerce_body_class' );

/**
 * Related products args — 3-up grid matching the approved reference.
 *
 * @param array $args Related products args.
 * @return array
 */
function fashion_brand_theme_related_products_args( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'fashion_brand_theme_related_products_args' );

/**
 * Keep related products within WooCommerce’s real related set (same category/tags).
 *
 * IMPORTANT: WooCommerce calls this filter with 3 arguments only. Accepting a 4th
 * required parameter caused ArgumentCountError on single product pages.
 *
 * @param int[] $related_ids Related product IDs.
 * @param int   $product_id  Current product ID.
 * @param array $args        Query args (limit, etc.).
 * @return int[]
 */
function fashion_brand_theme_related_products( $related_ids, $product_id, $args = array() ) {
	$limit = isset( $args['posts_per_page'] ) ? (int) $args['posts_per_page'] : ( isset( $args['limit'] ) ? (int) $args['limit'] : 3 );

	if ( ! is_array( $related_ids ) ) {
		$related_ids = array();
	}

	$related_ids = array_values( array_filter( array_map( 'absint', $related_ids ) ) );
	$related_ids = array_diff( $related_ids, array( absint( $product_id ) ) );

	return array_slice( $related_ids, 0, max( 1, $limit ) );
}
add_filter( 'woocommerce_related_products', 'fashion_brand_theme_related_products', 10, 3 );

/**
 * Primary + hover image IDs for a product card.
 *
 * Featured = primary flat-lay. First gallery image = hover crop.
 *
 * @param WC_Product $product Product.
 * @return array{primary:int,hover:int}
 */
function fashion_brand_theme_get_product_card_image_ids( $product ) {
	$primary = (int) $product->get_image_id();
	$gallery = $product->get_gallery_image_ids();
	$hover   = ! empty( $gallery[0] ) ? (int) $gallery[0] : $primary;

	return array(
		'primary' => $primary,
		'hover'   => $hover,
	);
}

/**
 * Ordered gallery IDs for the single product page.
 *
 * Simple products keep worn → primary → hover (max 3).
 * Variable products: parent featured image first, then one
 * representative image per color variation (uncapped).
 *
 * @param WC_Product $product Product.
 * @return int[]
 */
function fashion_brand_theme_get_product_page_gallery_ids( $product ) {
	if ( $product->is_type( 'variable' ) ) {
		$ordered    = array();
		$seen       = array();
		$seen_color = array();

		$primary = (int) $product->get_image_id();
		if ( $primary > 0 ) {
			$ordered[]        = $primary;
			$seen[ $primary ] = true;
		}

		foreach ( $product->get_children() as $child_id ) {
			$variation = wc_get_product( $child_id );
			if ( ! $variation || ! $variation->exists() ) {
				continue;
			}

			$image_id = (int) $variation->get_image_id();
			if ( $image_id <= 0 || isset( $seen[ $image_id ] ) ) {
				continue;
			}

			$attrs = $variation->get_attributes();
			$color = '';
			if ( isset( $attrs['pa_color'] ) ) {
				$color = (string) $attrs['pa_color'];
			} elseif ( isset( $attrs['attribute_pa_color'] ) ) {
				$color = (string) $attrs['attribute_pa_color'];
			}

			// One representative image per color; if no color attr, still include once.
			$color_key = '' !== $color ? $color : 'variation-' . (int) $child_id;
			if ( isset( $seen_color[ $color_key ] ) ) {
				continue;
			}
			$seen_color[ $color_key ] = true;

			$ordered[]         = $image_id;
			$seen[ $image_id ] = true;
		}

		return array_values( $ordered );
	}

	$primary = (int) $product->get_image_id();
	$gallery = array_map( 'intval', $product->get_gallery_image_ids() );
	$hover   = $gallery[0] ?? 0;
	$worn    = $gallery[1] ?? 0;

	$ordered = array_filter( array( $worn, $primary, $hover ) );

	return array_values( array_unique( $ordered ) );
}

/**
 * Render a product image by attachment ID.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $size          Image size.
 * @param string $class         CSS class.
 * @param string $alt           Alt text.
 * @return void
 */
function fashion_brand_theme_render_product_img( $attachment_id, $size, $class, $alt = '' ) {
	if ( ! $attachment_id ) {
		return;
	}

	echo wp_get_attachment_image(
		$attachment_id,
		$size,
		false,
		array(
			'class'   => $class,
			'alt'     => $alt,
			'loading' => 'lazy',
		)
	);
}

/**
 * Branded sale flash markup — shared by loop, single, and custom gallery output.
 *
 * @param string     $html    Default sale flash HTML.
 * @param WP_Post    $post    Product post.
 * @param WC_Product $product Product.
 * @return string
 */
function fashion_brand_theme_sale_flash_html( $html, $post, $product ) {
	if ( ! $product instanceof WC_Product || ! $product->is_on_sale() ) {
		return '';
	}

	return sprintf(
		'<span class="product-badge product-badge--sale onsale">%s</span>',
		esc_html__( 'Sale', 'fashion-brand-theme' )
	);
}
add_filter( 'woocommerce_sale_flash', 'fashion_brand_theme_sale_flash_html', 10, 3 );

/**
 * Output sale badge on the custom single-product main gallery image.
 *
 * Theme templates skip woocommerce_before_single_product_summary; inject here instead.
 *
 * @param string       $html              Image HTML.
 * @param int          $attachment_id     Attachment ID.
 * @param string|int[] $size              Image size.
 * @param bool         $icon              Whether icon.
 * @param string[]     $attr              Attributes.
 * @return string
 */
function fashion_brand_theme_single_gallery_sale_flash( $html, $attachment_id, $size, $icon, $attr ) {
	if ( is_admin() || ! is_product() || empty( $attr['data-gallery-main'] ) ) {
		return $html;
	}

	global $product;

	if ( ! $product instanceof WC_Product || ! $product->is_on_sale() ) {
		return $html;
	}

	$badge = apply_filters( 'woocommerce_sale_flash', '', get_post( $product->get_id() ), $product );

	if ( '' === $badge ) {
		return $html;
	}

	return $badge . $html;
}
add_filter( 'wp_get_attachment_image', 'fashion_brand_theme_single_gallery_sale_flash', 10, 5 );

/**
 * Cart trust line above totals.
 *
 * @return void
 */
function fashion_brand_theme_cart_trust_line() {
	$items = array(
		__( 'Free shipping over €150', 'fashion-brand-theme' ),
		__( 'Returns within 14 days', 'fashion-brand-theme' ),
	);

	$markup = array();
	foreach ( $items as $item ) {
		$markup[] = '<span class="product-trust-line__item">' . esc_html( $item ) . '</span>';
	}

	echo '<p class="product-trust-line cart-trust-line">';
	echo wp_kses_post( implode( '<span class="product-trust-line__sep" aria-hidden="true"> · </span>', $markup ) );
	echo '</p>';
}
add_action( 'woocommerce_before_cart_totals', 'fashion_brand_theme_cart_trust_line' );

/**
 * Checkout trust row before Place Order.
 *
 * @return void
 */
function fashion_brand_theme_checkout_trust_row() {
	echo '<p class="checkout-trust-row">';
	echo '<svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 11V8a5 5 0 0 1 10 0v3" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><rect x="5" y="11" width="14" height="10" rx="2" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
	echo '<span>' . esc_html__( 'Secure checkout', 'fashion-brand-theme' ) . '</span>';
	echo '</p>';
}
add_action( 'woocommerce_review_order_before_submit', 'fashion_brand_theme_checkout_trust_row' );
