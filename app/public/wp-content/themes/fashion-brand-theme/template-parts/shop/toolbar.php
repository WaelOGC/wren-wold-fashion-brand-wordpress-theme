<?php
/**
 * Shop bar: category pills, filter triggers, sort.
 *
 * @package Fashion_Brand_Theme
 */

defined( 'ABSPATH' ) || exit;

$categories   = fashion_brand_theme_get_product_category_slugs();
$shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$current_slug = is_product_category() ? get_queried_object()->slug : '';
$active       = fashion_brand_theme_get_active_filters();

$filter_count = count( $active['colors'] ) + count( $active['sizes'] );
if ( null !== $active['min_price'] || null !== $active['max_price'] ) {
	++$filter_count;
}

$total = (int) wc_get_loop_prop( 'total' );
?>
<div class="shop-bar container">
	<div class="shop-bar__start">
		<nav class="shop-bar__pills" aria-label="<?php esc_attr_e( 'Product categories', 'fashion-brand-theme' ); ?>">
			<a
				class="shop-bar__pill<?php echo '' === $current_slug ? ' is-active' : ''; ?>"
				href="<?php echo esc_url( $shop_url ); ?>"
				<?php echo '' === $current_slug ? ' aria-current="page"' : ''; ?>
			>
				<?php esc_html_e( 'All', 'fashion-brand-theme' ); ?>
			</a>
			<?php foreach ( $categories as $slug => $label ) : ?>
				<?php
				$term = get_term_by( 'slug', $slug, 'product_cat' );
				$url  = ( $term && ! is_wp_error( $term ) ) ? get_term_link( $term ) : $shop_url;
				if ( is_wp_error( $url ) ) {
					$url = $shop_url;
				}
				$is_active = ( $current_slug === $slug );
				?>
				<a
					class="shop-bar__pill<?php echo $is_active ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( $url ); ?>"
					<?php echo $is_active ? ' aria-current="page"' : ''; ?>
				>
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<span class="shop-bar__divider" aria-hidden="true"></span>

		<button
			type="button"
			class="shop-bar__filter-btn"
			data-shop-filters-open
			aria-expanded="false"
			aria-controls="shop-sidebar-filters"
		>
			<svg class="shop-bar__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
				<path d="M4 6h16M7 12h10M10 18h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
			</svg>
			<span class="is-desktop"><?php esc_html_e( 'Filter', 'fashion-brand-theme' ); ?></span>
			<span class="is-mobile"><?php esc_html_e( 'Filters', 'fashion-brand-theme' ); ?></span>
			<?php if ( $filter_count > 0 ) : ?>
				<span class="shop-bar__count" aria-hidden="true"><?php echo esc_html( (string) $filter_count ); ?></span>
				<span class="screen-reader-text">
					<?php
					printf(
						/* translators: %d: number of active filters */
						esc_html( _n( '%d active filter', '%d active filters', $filter_count, 'fashion-brand-theme' ) ),
						(int) $filter_count
					);
					?>
				</span>
			<?php endif; ?>
		</button>

		<div class="shop-bar__triggers">
			<button type="button" class="shop-bar__trigger" data-shop-filters-open="color" aria-controls="shop-sidebar-filters">
				<svg class="shop-bar__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
					<circle cx="12" cy="12" r="7.5" stroke="currentColor" stroke-width="1.5"/>
				</svg>
				<span><?php esc_html_e( 'Color', 'fashion-brand-theme' ); ?></span>
			</button>
			<button type="button" class="shop-bar__trigger" data-shop-filters-open="size" aria-controls="shop-sidebar-filters">
				<svg class="shop-bar__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
					<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
					<path d="M7 7h.01" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
				</svg>
				<span><?php esc_html_e( 'Size', 'fashion-brand-theme' ); ?></span>
			</button>
			<button type="button" class="shop-bar__trigger" data-shop-filters-open="price" aria-controls="shop-sidebar-filters">
				<span><?php esc_html_e( 'Price', 'fashion-brand-theme' ); ?></span>
				<svg class="shop-bar__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
					<path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
		</div>
	</div>

	<div class="shop-bar__end">
		<p class="shop-bar__count-text">
			<?php
			printf(
				/* translators: %d: number of products */
				esc_html( _n( '%d product', '%d products', $total, 'fashion-brand-theme' ) ),
				(int) $total
			);
			?>
		</p>
		<div class="shop-bar__sort">
			<span class="shop-bar__sort-label"><?php esc_html_e( 'Sort by:', 'fashion-brand-theme' ); ?></span>
			<?php woocommerce_catalog_ordering(); ?>
		</div>
	</div>
</div>
