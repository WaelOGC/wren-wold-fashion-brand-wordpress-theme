<?php
/**
 * Product card — shop grid.
 *
 * @package Fashion_Brand_Theme
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$images    = fashion_brand_theme_get_product_card_image_ids( $product );
$name      = $product->get_name();
$badges    = fashion_brand_theme_get_product_badges( $product );
$colors    = fashion_brand_theme_get_product_color_terms( $product );
$permalink = get_permalink( $product->get_id() );

$swatch_limit = 4;
$swatch_total = is_array( $colors ) ? count( $colors ) : 0;
$swatch_show  = $swatch_total > 0 ? array_slice( $colors, 0, $swatch_limit ) : array();
$swatch_more  = max( 0, $swatch_total - $swatch_limit );

$in_stock     = $product->is_in_stock();
$is_variable  = $product->is_type( 'variable' );
$is_purchasable = $product->is_purchasable();
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
				<svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
				</svg>
			</button>
		</div>

		<div class="product-card-info">
			<div class="product-card-info__row">
				<a class="product-card__title-link" href="<?php echo esc_url( $permalink ); ?>">
					<span class="product-card-name"><?php echo esc_html( $name ); ?></span>
				</a>

				<?php if ( fashion_brand_theme_is_setting_enabled( 'shop_show_price' ) ) : ?>
					<span class="product-card-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $swatch_show ) ) : ?>
				<span class="product-card__swatches" aria-label="<?php esc_attr_e( 'Available colors', 'fashion-brand-theme' ); ?>">
					<?php foreach ( $swatch_show as $term ) : ?>
						<span class="product-card__swatch" style="--swatch:<?php echo esc_attr( fashion_brand_theme_get_color_hex( $term->slug ) ); ?>" title="<?php echo esc_attr( $term->name ); ?>"></span>
					<?php endforeach; ?>
					<?php if ( $swatch_more > 0 ) : ?>
						<span class="product-card__swatch-more">+<?php echo esc_html( (string) $swatch_more ); ?></span>
					<?php endif; ?>
				</span>
			<?php endif; ?>

			<?php if ( ! $in_stock ) : ?>
				<button type="button" class="product-card__cart product-card__cart--sold-out" disabled>
					<?php esc_html_e( 'Sold out', 'fashion-brand-theme' ); ?>
				</button>
			<?php elseif ( $is_variable ) : ?>
				<button
					type="button"
					class="product-card__cart"
					data-quick-view
					data-product-id="<?php echo esc_attr( (string) $product->get_id() ); ?>"
				>
					<?php esc_html_e( 'Add to cart', 'fashion-brand-theme' ); ?>
				</button>
			<?php elseif ( $is_purchasable ) : ?>
				<a
					href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
					class="product-card__cart button add_to_cart_button ajax_add_to_cart"
					data-product_id="<?php echo esc_attr( (string) $product->get_id() ); ?>"
					data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
					data-quantity="1"
					aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>"
					rel="nofollow"
				>
					<?php esc_html_e( 'Add to cart', 'fashion-brand-theme' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</article>
</li>
