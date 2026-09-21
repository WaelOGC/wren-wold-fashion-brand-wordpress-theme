<?php
/**
 * Shop sidebar filters — category, color, size, price.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$categories = fashion_brand_theme_get_shop_category_tree();
$active     = fashion_brand_theme_get_active_filters();
$range      = fashion_brand_theme_get_catalog_price_range();
$min_val    = null !== $active['min_price'] ? $active['min_price'] : $range['min'];
$max_val    = null !== $active['max_price'] ? $active['max_price'] : $range['max'];

$color_terms = taxonomy_exists( 'pa_color' )
	? get_terms( array( 'taxonomy' => 'pa_color', 'hide_empty' => true ) )
	: array();
$size_terms = taxonomy_exists( 'pa_size' )
	? get_terms( array( 'taxonomy' => 'pa_size', 'hide_empty' => true ) )
	: array();

$action = fashion_brand_theme_get_clear_filters_url();

$queried_cat_slug = '';
if ( is_product_category() ) {
	$queried_term = get_queried_object();
	if ( $queried_term instanceof WP_Term ) {
		$queried_cat_slug = $queried_term->slug;
	}
}
?>
<aside id="shop-sidebar-filters" class="shop-sidebar" aria-label="<?php esc_attr_e( 'Product filters', 'fashion-brand-theme' ); ?>" data-shop-sidebar>
	<div class="shop-sidebar__panel">
		<div class="shop-sidebar__head">
			<h2 class="shop-sidebar__title"><?php esc_html_e( 'Filters', 'fashion-brand-theme' ); ?></h2>
			<button type="button" class="shop-sidebar__close" data-shop-filters-close aria-label="<?php esc_attr_e( 'Close filters', 'fashion-brand-theme' ); ?>">&times;</button>
		</div>
	<form class="shop-filters" method="get" action="<?php echo esc_url( $action ); ?>" data-shop-filters>
		<?php if ( ! empty( $_GET['orderby'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<input type="hidden" name="orderby" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) ); ?>" />
		<?php endif; ?>

		<div class="shop-filters__group">
			<h2 class="shop-filters__title"><?php esc_html_e( 'Category', 'fashion-brand-theme' ); ?></h2>
			<ul class="shop-filters__list">
				<?php foreach ( $categories as $slug => $category ) : ?>
					<?php
					$label      = $category['label'];
					$term_id    = isset( $category['term_id'] ) ? (int) $category['term_id'] : 0;
					$children   = $category['children'];
					$has_kids   = ! empty( $children );
					$sublist_id = $has_kids ? 'shop-filter-cat-' . sanitize_html_class( $slug ) : '';
					$icon_svg   = '';

					if ( $term_id > 0 && function_exists( 'fashion_brand_theme_get_category_icon_key' ) ) {
						$icon_key = fashion_brand_theme_get_category_icon_key( $term_id );
						$icon_svg = fashion_brand_theme_get_category_icon_svg( $icon_key );
					}

					$is_self_active = ( $queried_cat_slug === $slug ) || in_array( $slug, $active['cats'], true );
					$child_active   = false;
					if ( $has_kids ) {
						foreach ( array_keys( $children ) as $child_slug ) {
							if ( ( $queried_cat_slug === $child_slug ) || in_array( $child_slug, $active['cats'], true ) ) {
								$child_active = true;
								break;
							}
						}
					}
					$is_active   = $is_self_active || $child_active;
					$sublist_open = $has_kids && $is_active;

					$parent_link = get_term_link( $slug, 'product_cat' );
					?>
					<li class="<?php echo $has_kids ? 'shop-filters__item shop-filters__item--has-children' : 'shop-filters__item'; ?>">
						<div class="shop-filters__row">
							<?php if ( ! is_wp_error( $parent_link ) ) : ?>
								<a class="shop-filters__cat-link<?php echo $is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( $parent_link ); ?>">
									<?php if ( $icon_svg ) : ?>
										<span class="shop-filters__cat-icon" aria-hidden="true"><?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted inline SVG from theme definitions. ?></span>
									<?php endif; ?>
									<span><?php echo esc_html( $label ); ?></span>
								</a>
							<?php else : ?>
								<span class="shop-filters__cat-link<?php echo $is_active ? ' is-active' : ''; ?>">
									<?php if ( $icon_svg ) : ?>
										<span class="shop-filters__cat-icon" aria-hidden="true"><?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted inline SVG from theme definitions. ?></span>
									<?php endif; ?>
									<span><?php echo esc_html( $label ); ?></span>
								</span>
							<?php endif; ?>
							<?php if ( $has_kids ) : ?>
								<button
									type="button"
									class="shop-filters__toggle"
									aria-expanded="<?php echo $sublist_open ? 'true' : 'false'; ?>"
									aria-controls="<?php echo esc_attr( $sublist_id ); ?>"
									aria-label="<?php
									echo esc_attr(
										sprintf(
											/* translators: %s: parent category label. */
											__( 'Toggle %s subcategories', 'fashion-brand-theme' ),
											$label
										)
									);
									?>"
								>
									<span class="shop-filters__toggle-icon" aria-hidden="true"></span>
								</button>
							<?php endif; ?>
						</div>
						<?php if ( $has_kids ) : ?>
							<ul id="<?php echo esc_attr( $sublist_id ); ?>" class="shop-filters__sublist<?php echo $sublist_open ? ' is-open' : ''; ?>">
								<?php foreach ( $children as $child_slug => $child_name ) : ?>
									<?php
									$child_is_active = ( $queried_cat_slug === $child_slug ) || in_array( $child_slug, $active['cats'], true );
									$child_link      = get_term_link( $child_slug, 'product_cat' );
									?>
									<li>
										<?php if ( ! is_wp_error( $child_link ) ) : ?>
											<a class="shop-filters__cat-link<?php echo $child_is_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( $child_link ); ?>">
												<span><?php echo esc_html( $child_name ); ?></span>
											</a>
										<?php else : ?>
											<span class="shop-filters__cat-link<?php echo $child_is_active ? ' is-active' : ''; ?>">
												<span><?php echo esc_html( $child_name ); ?></span>
											</span>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php if ( ! empty( $color_terms ) && ! is_wp_error( $color_terms ) ) : ?>
			<div class="shop-filters__group">
				<h2 class="shop-filters__title"><?php esc_html_e( 'Color', 'fashion-brand-theme' ); ?></h2>
				<ul class="shop-filters__list">
					<?php foreach ( $color_terms as $term ) : ?>
						<li>
							<label class="shop-filters__check shop-filters__check--swatch">
								<input type="checkbox" name="filter_color[]" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( in_array( $term->slug, $active['colors'], true ) ); ?> />
								<span class="shop-filters__swatch" style="--swatch:<?php echo esc_attr( fashion_brand_theme_get_color_hex( $term->slug ) ); ?>"></span>
								<span><?php echo esc_html( $term->name ); ?></span>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $size_terms ) && ! is_wp_error( $size_terms ) ) : ?>
			<div class="shop-filters__group">
				<h2 class="shop-filters__title"><?php esc_html_e( 'Size', 'fashion-brand-theme' ); ?></h2>
				<ul class="shop-filters__list shop-filters__list--sizes">
					<?php foreach ( $size_terms as $term ) : ?>
						<li>
							<label class="shop-filters__check">
								<input type="checkbox" name="filter_size[]" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( in_array( $term->slug, $active['sizes'], true ) ); ?> />
								<span><?php echo esc_html( $term->name ); ?></span>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="shop-filters__group">
			<h2 class="shop-filters__title"><?php esc_html_e( 'Price', 'fashion-brand-theme' ); ?></h2>
			<div class="shop-filters__price" data-price-filter data-min="<?php echo esc_attr( (string) $range['min'] ); ?>" data-max="<?php echo esc_attr( (string) $range['max'] ); ?>">
				<input type="range" name="min_price" class="shop-filters__range" min="<?php echo esc_attr( (string) $range['min'] ); ?>" max="<?php echo esc_attr( (string) $range['max'] ); ?>" value="<?php echo esc_attr( (string) $min_val ); ?>" data-price-min />
				<input type="range" name="max_price" class="shop-filters__range" min="<?php echo esc_attr( (string) $range['min'] ); ?>" max="<?php echo esc_attr( (string) $range['max'] ); ?>" value="<?php echo esc_attr( (string) $max_val ); ?>" data-price-max />
				<p class="shop-filters__price-labels">
					<span data-price-min-label><?php echo wp_kses_post( wc_price( $min_val ) ); ?></span>
					<span aria-hidden="true">–</span>
					<span data-price-max-label><?php echo wp_kses_post( wc_price( $max_val ) ); ?></span>
				</p>
			</div>
		</div>

		<div class="shop-filters__actions">
			<button type="submit" class="shop-filters__submit"><?php esc_html_e( 'Apply filters', 'fashion-brand-theme' ); ?></button>
			<a class="shop-filters__clear" href="<?php echo esc_url( fashion_brand_theme_get_clear_filters_url() ); ?>"><?php esc_html_e( 'Clear all filters', 'fashion-brand-theme' ); ?></a>
		</div>
	</form>
	</div>
</aside>
