<?php
/**
 * Shop filters drawer — color, size, price.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active  = fashion_brand_theme_get_active_filters();
$range   = fashion_brand_theme_get_catalog_price_range();
$min_val = null !== $active['min_price'] ? $active['min_price'] : $range['min'];
$max_val = null !== $active['max_price'] ? $active['max_price'] : $range['max'];

$color_terms = taxonomy_exists( 'pa_color' )
	? get_terms( array( 'taxonomy' => 'pa_color', 'hide_empty' => true ) )
	: array();
$size_terms = taxonomy_exists( 'pa_size' )
	? get_terms( array( 'taxonomy' => 'pa_size', 'hide_empty' => true ) )
	: array();

$action = fashion_brand_theme_get_clear_filters_url();
?>
<aside id="shop-sidebar-filters" class="shop-sidebar" aria-label="<?php esc_attr_e( 'Product filters', 'fashion-brand-theme' ); ?>" data-shop-sidebar>
	<div class="shop-sidebar__overlay" data-shop-filters-close tabindex="-1"></div>
	<div class="shop-sidebar__panel" role="dialog" aria-modal="true" aria-labelledby="shop-filters-title">
		<div class="shop-sidebar__head">
			<h2 id="shop-filters-title" class="shop-sidebar__title"><?php esc_html_e( 'Filters', 'fashion-brand-theme' ); ?></h2>
			<button type="button" class="shop-sidebar__close" data-shop-filters-close aria-label="<?php esc_attr_e( 'Close filters', 'fashion-brand-theme' ); ?>">&times;</button>
		</div>

		<form class="shop-filters" method="get" action="<?php echo esc_url( $action ); ?>" data-shop-filters>
			<?php if ( ! empty( $_GET['orderby'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<input type="hidden" name="orderby" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) ); ?>" />
			<?php endif; ?>

			<div class="shop-filters__body">
				<?php if ( ! empty( $color_terms ) && ! is_wp_error( $color_terms ) ) : ?>
					<div class="shop-filters__group" data-shop-filter-group="color" id="shop-filter-group-color">
						<h3 class="shop-filters__title"><?php esc_html_e( 'Color', 'fashion-brand-theme' ); ?></h3>
						<ul class="shop-filters__swatches">
							<?php foreach ( $color_terms as $term ) : ?>
								<li>
									<label class="shop-filters__swatch-label">
										<input
											class="shop-filters__swatch-input"
											type="checkbox"
											name="filter_color[]"
											value="<?php echo esc_attr( $term->slug ); ?>"
											<?php checked( in_array( $term->slug, $active['colors'], true ) ); ?>
										/>
										<span
											class="shop-filters__swatch"
											style="--swatch:<?php echo esc_attr( fashion_brand_theme_get_color_hex( $term->slug ) ); ?>"
											aria-hidden="true"
										></span>
										<span class="shop-filters__swatch-name"><?php echo esc_html( $term->name ); ?></span>
									</label>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $size_terms ) && ! is_wp_error( $size_terms ) ) : ?>
					<div class="shop-filters__group" data-shop-filter-group="size" id="shop-filter-group-size">
						<h3 class="shop-filters__title"><?php esc_html_e( 'Size', 'fashion-brand-theme' ); ?></h3>
						<ul class="shop-filters__sizes">
							<?php foreach ( $size_terms as $term ) : ?>
								<li>
									<label class="shop-filters__size-label">
										<input
											class="shop-filters__size-input"
											type="checkbox"
											name="filter_size[]"
											value="<?php echo esc_attr( $term->slug ); ?>"
											<?php checked( in_array( $term->slug, $active['sizes'], true ) ); ?>
										/>
										<span class="shop-filters__size-chip"><?php echo esc_html( $term->name ); ?></span>
									</label>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<div class="shop-filters__group" data-shop-filter-group="price" id="shop-filter-group-price">
					<h3 class="shop-filters__title"><?php esc_html_e( 'Price', 'fashion-brand-theme' ); ?></h3>
					<div
						class="shop-filters__price"
						data-price-filter
						data-min="<?php echo esc_attr( (string) $range['min'] ); ?>"
						data-max="<?php echo esc_attr( (string) $range['max'] ); ?>"
					>
						<input type="range" name="min_price" class="shop-filters__range" min="<?php echo esc_attr( (string) $range['min'] ); ?>" max="<?php echo esc_attr( (string) $range['max'] ); ?>" value="<?php echo esc_attr( (string) $min_val ); ?>" data-price-min />
						<input type="range" name="max_price" class="shop-filters__range" min="<?php echo esc_attr( (string) $range['min'] ); ?>" max="<?php echo esc_attr( (string) $range['max'] ); ?>" value="<?php echo esc_attr( (string) $max_val ); ?>" data-price-max />
						<p class="shop-filters__price-labels">
							<span data-price-min-label><?php echo wp_kses_post( wc_price( $min_val ) ); ?></span>
							<span aria-hidden="true">–</span>
							<span data-price-max-label><?php echo wp_kses_post( wc_price( $max_val ) ); ?></span>
						</p>
					</div>
				</div>
			</div>

			<div class="shop-filters__actions">
				<button type="submit" class="shop-filters__submit"><?php esc_html_e( 'Show results', 'fashion-brand-theme' ); ?></button>
				<a class="shop-filters__clear" href="<?php echo esc_url( fashion_brand_theme_get_clear_filters_url() ); ?>"><?php esc_html_e( 'Clear all', 'fashion-brand-theme' ); ?></a>
			</div>
		</form>
	</div>
</aside>
