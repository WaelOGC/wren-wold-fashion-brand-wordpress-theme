<?php
/**
 * Product card — shop grid / list.
 *
 * @package Fashion_Brand_Theme
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$images   = fashion_brand_theme_get_product_card_image_ids( $product );
$name     = $product->get_name();
$badges   = fashion_brand_theme_get_product_badges( $product );
$colors   = fashion_brand_theme_get_product_color_terms( $product );
$rating   = (float) $product->get_average_rating();
$count    = (int) $product->get_review_count();
$permalink = get_permalink( $product->get_id() );
?>
<li <?php wc_product_class( 'product-card-item', $product ); ?>>
	<article class="product-card" data-product-id="<?php echo esc_attr( (string) $product->get_id() ); ?>">
		<div class="product-card__media">
			<a class="product-card__link" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $name ); ?>">
				<span class="product-card-img-wrap">
					<?php
					fashion_brand_theme_render_product_img( $images['primary'], 'woocommerce_thumbnail', 'product-card-img', $name );
					fashion_brand_theme_render_product_img( $images['hover'], 'woocommerce_thumbnail', 'product-card-img hover-img', $name );
					?>
				</span>
			</a>

			<?php if ( ! empty( $badges ) ) : ?>
				<span class="product-card__badges">
					<?php foreach ( $badges as $badge ) : ?>
						<span class="product-card__badge product-card__badge--<?php echo esc_attr( $badge['key'] ); ?>"><?php echo esc_html( $badge['label'] ); ?></span>
					<?php endforeach; ?>
				</span>
			<?php endif; ?>

			<button
				type="button"
				class="product-card__wishlist"
				data-wishlist-toggle
				data-product-id="<?php echo esc_attr( (string) $product->get_id() ); ?>"
				aria-pressed="false"
				aria-label="<?php esc_attr_e( 'Add to wishlist', 'fashion-brand-theme' ); ?>"
			>
				<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 21s-7.2-4.6-9.5-8.2C.6 9.7 2.1 6 5.5 6c1.9 0 3.2 1.1 3.9 2.2C10.1 7.1 11.4 6 13.3 6c3.4 0 4.9 3.7 3 6.8C19.2 16.4 12 21 12 21z" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
			</button>

			<button
				type="button"
				class="product-card__quick-view"
				data-quick-view
				data-product-id="<?php echo esc_attr( (string) $product->get_id() ); ?>"
			>
				<?php esc_html_e( 'Quick View', 'fashion-brand-theme' ); ?>
			</button>
		</div>

		<div class="product-card-info">
			<a class="product-card__title-link" href="<?php echo esc_url( $permalink ); ?>">
				<span class="product-card-name"><?php echo esc_html( $name ); ?></span>
			</a>

			<?php if ( fashion_brand_theme_is_setting_enabled( 'shop_show_price' ) ) : ?>
				<span class="product-card-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $colors ) ) : ?>
				<span class="product-card__swatches" aria-label="<?php esc_attr_e( 'Available colors', 'fashion-brand-theme' ); ?>">
					<?php foreach ( $colors as $term ) : ?>
						<span class="product-card__swatch" style="--swatch:<?php echo esc_attr( fashion_brand_theme_get_color_hex( $term->slug ) ); ?>" title="<?php echo esc_attr( $term->name ); ?>"></span>
					<?php endforeach; ?>
				</span>
			<?php endif; ?>

			<?php if ( $count > 0 && $rating > 0 ) : ?>
				<span class="product-card__rating">
					<?php echo wp_kses_post( wc_get_rating_html( $rating, $count ) ); ?>
					<span class="product-card__review-count">(<?php echo esc_html( (string) $count ); ?>)</span>
				</span>
			<?php endif; ?>
		</div>
	</article>
</li>
