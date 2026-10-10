<?php
/**
 * Live product search — render helpers and AJAX handler.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render product search matches using the Shop product card grid.
 *
 * @param string $term  Search term.
 * @param int    $limit Maximum number of products to display.
 * @return int Match count (total found).
 */
function fashion_brand_theme_render_live_search_results( $term, $limit = 6 ) {
	$term  = trim( (string) $term );
	$limit = max( 1, absint( $limit ) );

	if ( '' === $term || ! function_exists( 'wc_get_template_part' ) ) {
		fashion_brand_theme_render_live_search_idle();
		return 0;
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			's'              => $term,
			'posts_per_page' => $limit,
		)
	);

	$count = (int) $query->found_posts;

	if ( ! $query->have_posts() ) {
		fashion_brand_theme_render_live_search_empty();
		wp_reset_postdata();
		return 0;
	}

	$columns = $limit >= 12 ? 4 : 3;
	wc_set_loop_prop( 'columns', $columns );
	woocommerce_product_loop_start();

	while ( $query->have_posts() ) {
		$query->the_post();
		do_action( 'woocommerce_shop_loop' );
		wc_get_template_part( 'content', 'product' );
	}

	woocommerce_product_loop_end();
	wp_reset_postdata();

	return $count;
}

/**
 * Render the empty / no-results state.
 *
 * @return void
 */
function fashion_brand_theme_render_live_search_empty() {
	$shop_url = function_exists( 'fashion_brand_theme_get_shop_url' )
		? fashion_brand_theme_get_shop_url()
		: home_url( '/' );
	?>
	<div class="live-search-empty" role="status">
		<h2 class="live-search-empty__heading"><?php esc_html_e( 'No products found', 'fashion-brand-theme' ); ?></h2>
		<p class="live-search-empty__text"><?php esc_html_e( 'Try a different term, or browse the full shop.', 'fashion-brand-theme' ); ?></p>
		<a class="button button--primary live-search-empty__cta" href="<?php echo esc_url( $shop_url ); ?>">
			<?php esc_html_e( 'Browse the shop', 'fashion-brand-theme' ); ?>
		</a>
	</div>
	<?php
}

/**
 * Render the idle "start typing" state.
 *
 * @return void
 */
function fashion_brand_theme_render_live_search_idle() {
	?>
	<div class="live-search-idle" role="status">
		<p class="live-search-idle__text"><?php esc_html_e( 'Start typing to search products.', 'fashion-brand-theme' ); ?></p>
	</div>
	<?php
}

/**
 * AJAX: live product search.
 *
 * @return void
 */
function fashion_brand_theme_ajax_live_search() {
	check_ajax_referer( 'fashion_brand_theme_live_search', 'nonce' );

	$term  = isset( $_REQUEST['term'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['term'] ) ) : '';
	$limit = isset( $_REQUEST['limit'] ) ? absint( $_REQUEST['limit'] ) : 6;

	if ( $limit < 1 ) {
		$limit = 6;
	}

	$min_chars = 2;
	$count     = 0;

	ob_start();

	if ( strlen( $term ) < $min_chars ) {
		fashion_brand_theme_render_live_search_idle();
	} else {
		$count = fashion_brand_theme_render_live_search_results( $term, $limit );
	}

	$html = ob_get_clean();

	wp_send_json_success(
		array(
			'html'  => $html,
			'count' => $count,
		)
	);
}
add_action( 'wp_ajax_fashion_brand_theme_live_search', 'fashion_brand_theme_ajax_live_search' );
add_action( 'wp_ajax_nopriv_fashion_brand_theme_live_search', 'fashion_brand_theme_ajax_live_search' );
