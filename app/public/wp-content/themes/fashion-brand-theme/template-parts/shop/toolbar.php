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
<div class="shop-bar container container--wide">
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
			<?php echo fashion_brand_theme_get_icon( 'sliders-horizontal', array( 'width' => 16, 'height' => 16, 'class' => 'shop-bar__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
				<?php echo fashion_brand_theme_get_icon( 'circle', array( 'width' => 16, 'height' => 16, 'class' => 'shop-bar__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php esc_html_e( 'Color', 'fashion-brand-theme' ); ?></span>
			</button>
			<button type="button" class="shop-bar__trigger" data-shop-filters-open="size" aria-controls="shop-sidebar-filters">
				<?php echo fashion_brand_theme_get_icon( 'tag', array( 'width' => 16, 'height' => 16, 'class' => 'shop-bar__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php esc_html_e( 'Size', 'fashion-brand-theme' ); ?></span>
			</button>
			<button type="button" class="shop-bar__trigger" data-shop-filters-open="price" aria-controls="shop-sidebar-filters">
				<span><?php esc_html_e( 'Price', 'fashion-brand-theme' ); ?></span>
				<?php echo fashion_brand_theme_get_icon( 'chevron-down', array( 'width' => 16, 'height' => 16, 'class' => 'shop-bar__icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
