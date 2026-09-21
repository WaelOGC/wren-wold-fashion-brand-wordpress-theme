<?php
/**
 * Single product content.
 *
 * @package Fashion_Brand_Theme
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

$gallery_ids  = fashion_brand_theme_get_product_page_gallery_ids( $product );
$main_id      = $gallery_ids[0] ?? (int) $product->get_image_id();
$gallery_count = max( 1, count( $gallery_ids ) );
$categories   = wc_get_product_category_list( $product->get_id(), ', ' );
$cat_terms    = get_the_terms( $product->get_id(), 'product_cat' );
$primary_cat  = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0] : null;
$rating       = (float) $product->get_average_rating();
$review_count = (int) $product->get_review_count();
$shipping     = fashion_brand_theme_get_shipping_returns_copy();
$detail_meta  = fashion_brand_theme_get_product_detail_meta( $product->get_id() );
$product_tags = wc_get_product_tag_list( $product->get_id(), ', ' );
$is_organic   = has_term( 'organic', 'product_tag', $product->get_id() );
$shop_url     = fashion_brand_theme_get_shop_url();
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'product-page', $product ); ?>>

	<div class="product-mobile-back" data-product-mobile-back>
		<button
			type="button"
			class="product-mobile-back__btn"
			data-product-back
			data-shop-url="<?php echo esc_url( $shop_url ); ?>"
			aria-label="<?php esc_attr_e( 'Go back', 'fashion-brand-theme' ); ?>"
		>
			<span aria-hidden="true">&larr;</span>
		</button>
	</div>

	<nav class="product-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'fashion-brand-theme' ); ?>">
		<a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop', 'fashion-brand-theme' ); ?></a>
		<span aria-hidden="true">›</span>
		<?php if ( $primary_cat ) : ?>
			<a href="<?php echo esc_url( get_term_link( $primary_cat ) ); ?>"><?php echo esc_html( $primary_cat->name ); ?></a>
			<span aria-hidden="true">›</span>
		<?php endif; ?>
		<span><?php the_title(); ?></span>
	</nav>

	<p class="product-back">
		<a href="<?php echo esc_url( $shop_url ); ?>">&larr; <?php esc_html_e( 'Back to the field', 'fashion-brand-theme' ); ?></a>
	</p>

	<div class="product-split">
		<div class="product-gallery" data-product-gallery>
			<?php if ( count( $gallery_ids ) > 1 ) : ?>
				<div class="product-gallery-thumbs" role="tablist" aria-label="<?php esc_attr_e( 'Product images', 'fashion-brand-theme' ); ?>">
					<?php foreach ( $gallery_ids as $index => $attachment_id ) : ?>
						<button
							type="button"
							class="product-gallery-thumbs__btn<?php echo 0 === $index ? ' is-active' : ''; ?>"
							data-gallery-thumb
							data-image-src="<?php echo esc_url( wp_get_attachment_image_url( $attachment_id, 'woocommerce_single' ) ); ?>"
							data-image-srcset="<?php echo esc_attr( (string) wp_get_attachment_image_srcset( $attachment_id, 'woocommerce_single' ) ); ?>"
							aria-label="<?php echo esc_attr( sprintf( __( 'View image %d', 'fashion-brand-theme' ), $index + 1 ) ); ?>"
							aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>"
						>
							<?php echo wp_get_attachment_image( $attachment_id, 'woocommerce_gallery_thumbnail', false, array( 'alt' => '' ) ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="product-gallery-main" data-zoom-root>
				<?php
				if ( $main_id ) {
					echo wp_get_attachment_image(
						$main_id,
						'woocommerce_single',
						false,
						array(
							'class'             => 'product-gallery-main__img',
							'data-gallery-main' => 'true',
							'alt'               => esc_attr( $product->get_name() ),
							'draggable'         => 'false',
						)
					);
				}
				?>
				<div class="product-gallery-zoom" data-zoom-lens hidden></div>

				<?php if ( $gallery_count > 1 ) : ?>
					<button type="button" class="product-gallery-nav product-gallery-nav--prev" data-gallery-prev aria-label="<?php esc_attr_e( 'Previous image', 'fashion-brand-theme' ); ?>">
						<span aria-hidden="true">&lsaquo;</span>
					</button>
					<button type="button" class="product-gallery-nav product-gallery-nav--next" data-gallery-next aria-label="<?php esc_attr_e( 'Next image', 'fashion-brand-theme' ); ?>">
						<span aria-hidden="true">&rsaquo;</span>
					</button>
					<span class="product-gallery-counter" data-gallery-counter aria-live="polite">1/<?php echo esc_html( (string) $gallery_count ); ?></span>
				<?php endif; ?>
			</div>
		</div>

		<div class="product-info summary entry-summary">
			<p class="product-eyebrow">
				<?php if ( $primary_cat ) : ?>
					<span class="product-eyebrow__cat"><?php echo esc_html( $primary_cat->name ); ?></span>
					<span aria-hidden="true">·</span>
				<?php endif; ?>
				<span class="product-eyebrow__num"><?php echo esc_html( fashion_brand_theme_get_product_number_label( $product ) ); ?></span>
			</p>

			<h1 class="product-title"><?php the_title(); ?></h1>

			<?php if ( $review_count > 0 && $rating > 0 ) : ?>
				<p class="product-rating">
					<a href="#tab-title-reviews">
						<?php echo wp_kses_post( wc_get_rating_html( $rating, $review_count ) ); ?>
						<span>(<?php echo esc_html( (string) $review_count ); ?>)</span>
					</a>
				</p>
			<?php endif; ?>

			<p class="product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>

			<?php if ( $product->get_short_description() ) : ?>
				<div class="product-desc"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
			<?php endif; ?>

			<div class="product-cart-wrap" data-main-atc>
				<div class="product-atc-row">
					<div class="product-atc-row__form">
						<?php
						add_action(
							'woocommerce_after_add_to_cart_button',
							static function () {
								static $buy_now_rendered = false;
								if ( $buy_now_rendered ) {
									return;
								}
								$buy_now_rendered = true;
								?>
								<button type="submit" name="buy_now" value="1" class="button product-buy-now">
									<?php esc_html_e( 'Buy it now', 'fashion-brand-theme' ); ?>
								</button>
								<?php
							},
							20
						);

						/**
						 * Hook: woocommerce_single_product_summary — add to cart / variations.
						 */
						do_action( 'woocommerce_single_product_summary' );
						?>
					</div>

					<div class="product-atc-row__actions" aria-label="<?php esc_attr_e( 'Product actions', 'fashion-brand-theme' ); ?>">
						<button type="button" class="product-icon-btn product-icon-btn--icon-only" data-wishlist-toggle data-product-id="<?php echo esc_attr( (string) $product->get_id() ); ?>" aria-pressed="false" aria-label="<?php esc_attr_e( 'Add to wishlist', 'fashion-brand-theme' ); ?>">
							<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
						</button>
						<button type="button" class="product-icon-btn product-icon-btn--icon-only" data-share-product aria-label="<?php esc_attr_e( 'Share', 'fashion-brand-theme' ); ?>">
							<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 8a3 3 0 1 0-2.8-4H12a3 3 0 0 0 .2 4L8.7 12.2a3 3 0 1 0 1.4 1.4L14 9.4A3 3 0 0 0 15 8zm-9 9a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" fill="currentColor"/></svg>
						</button>
					</div>
				</div>

				<?php
				$shipping_label = __( 'Free shipping', 'fashion-brand-theme' );
				$returns_label  = __( 'Easy returns', 'fashion-brand-theme' );
				$returns_sub    = __( '14 days', 'fashion-brand-theme' );
				?>
				<ul class="product-trust-badges" aria-label="<?php esc_attr_e( 'Purchase benefits', 'fashion-brand-theme' ); ?>">
					<li class="product-trust-badges__item">
						<span class="product-trust-badges__icon" aria-hidden="true">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" focusable="false"><path d="M3 7h11v10H3V7zm11 3h4l3 3v4h-7V10z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="7" cy="18.5" r="1.5" stroke="currentColor" stroke-width="1.5"/><circle cx="17" cy="18.5" r="1.5" stroke="currentColor" stroke-width="1.5"/></svg>
						</span>
						<span class="product-trust-badges__text">
							<span class="product-trust-badges__label" title="<?php echo esc_attr( $shipping['shipping'] ); ?>"><?php echo esc_html( $shipping_label ); ?></span>
						</span>
					</li>
					<li class="product-trust-badges__item">
						<span class="product-trust-badges__icon" aria-hidden="true">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" focusable="false"><path d="M4 7h10a4 4 0 0 1 0 8H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M8 11 4 7l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</span>
						<span class="product-trust-badges__text">
							<span class="product-trust-badges__label" title="<?php echo esc_attr( $shipping['returns'] ); ?>"><?php echo esc_html( $returns_label ); ?></span>
							<span class="product-trust-badges__sub"><?php echo esc_html( $returns_sub ); ?></span>
						</span>
					</li>
					<?php if ( $is_organic ) : ?>
						<li class="product-trust-badges__item">
							<span class="product-trust-badges__icon" aria-hidden="true">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" focusable="false"><path d="M12 21c0-7 4-11 9-12-1 6-5 10-9 12z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M12 21c0-7-4-11-9-12 1 6 5 10 9 12z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M12 21V11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
							</span>
							<span class="product-trust-badges__text">
								<span class="product-trust-badges__label"><?php esc_html_e( 'Sustainable materials', 'fashion-brand-theme' ); ?></span>
							</span>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>

	<?php
	$details_parts = array();
	if ( $product->get_short_description() ) {
		$details_parts[] = wpautop( $product->get_short_description() );
	}
	if ( $product->get_description() ) {
		$details_parts[] = wpautop( $product->get_description() );
	}
	if ( ! empty( $detail_meta['origin'] ) ) {
		$details_parts[] = '<p><strong>' . esc_html__( 'Origin', 'fashion-brand-theme' ) . ':</strong> ' . esc_html( $detail_meta['origin'] ) . '</p>';
	}

	$materials_parts = array();
	if ( ! empty( $detail_meta['composition'] ) ) {
		$materials_parts[] = '<p><strong>' . esc_html__( 'Composition', 'fashion-brand-theme' ) . ':</strong> ' . esc_html( $detail_meta['composition'] ) . '</p>';
	}
	if ( ! empty( $detail_meta['care'] ) ) {
		$materials_parts[] = '<p><strong>' . esc_html__( 'Care', 'fashion-brand-theme' ) . ':</strong> ' . esc_html( $detail_meta['care'] ) . '</p>';
	}
	?>

	<div class="product-mobile-accordions" data-product-accordions>
		<div class="product-accordion">
			<button type="button" class="product-accordion__trigger" aria-expanded="false" data-accordion-trigger>
				<span><?php esc_html_e( 'Details', 'fashion-brand-theme' ); ?></span>
				<span class="product-accordion__chevron" aria-hidden="true"></span>
			</button>
			<div class="product-accordion__panel" hidden data-accordion-panel>
				<?php
				if ( ! empty( $details_parts ) ) {
					echo wp_kses_post( implode( '', $details_parts ) );
				} else {
					echo '<p>' . esc_html__( 'No details available.', 'fashion-brand-theme' ) . '</p>';
				}
				?>
			</div>
		</div>

		<div class="product-accordion">
			<button type="button" class="product-accordion__trigger" aria-expanded="false" data-accordion-trigger>
				<span><?php esc_html_e( 'Materials & Care', 'fashion-brand-theme' ); ?></span>
				<span class="product-accordion__chevron" aria-hidden="true"></span>
			</button>
			<div class="product-accordion__panel" hidden data-accordion-panel>
				<?php
				if ( ! empty( $materials_parts ) ) {
					echo wp_kses_post( implode( '', $materials_parts ) );
				} else {
					echo '<p>' . esc_html__( 'No materials information available.', 'fashion-brand-theme' ) . '</p>';
				}
				?>
			</div>
		</div>

		<div class="product-accordion">
			<button type="button" class="product-accordion__trigger" aria-expanded="false" data-accordion-trigger>
				<span><?php esc_html_e( 'Shipping & Returns', 'fashion-brand-theme' ); ?></span>
				<span class="product-accordion__chevron" aria-hidden="true"></span>
			</button>
			<div class="product-accordion__panel" hidden data-accordion-panel>
				<p><?php echo esc_html( $shipping['shipping'] ); ?></p>
				<p><?php echo esc_html( $shipping['returns'] ); ?></p>
			</div>
		</div>

		<div class="product-accordion">
			<button type="button" class="product-accordion__trigger" aria-expanded="false" data-accordion-trigger data-accordion-reviews>
				<span><?php esc_html_e( 'Reviews', 'fashion-brand-theme' ); ?></span>
				<span class="product-accordion__chevron" aria-hidden="true"></span>
			</button>
			<div class="product-accordion__panel" hidden data-accordion-panel>
				<p>
					<a class="product-accordion__reviews-link" href="#tab-title-reviews">
						<?php
						printf(
							/* translators: %d: review count */
							esc_html( _n( 'View %d review', 'View %d reviews', max( 1, $review_count ), 'fashion-brand-theme' ) ),
							(int) max( 0, $review_count )
						);
						?>
					</a>
				</p>
			</div>
		</div>
	</div>

	<?php
	if ( $product->get_sku() || $categories || $product_tags ) {
		add_action(
			'woocommerce_product_additional_information',
			static function () use ( $product, $categories, $product_tags ) {
				?>
				<table class="woocommerce-product-attributes shop_attributes product-detail-meta">
					<?php if ( $product->get_sku() ) : ?>
						<tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--sku">
							<th class="woocommerce-product-attributes-item__label"><?php esc_html_e( 'SKU', 'fashion-brand-theme' ); ?></th>
							<td class="woocommerce-product-attributes-item__value"><?php echo esc_html( $product->get_sku() ); ?></td>
						</tr>
					<?php endif; ?>
					<?php if ( $categories ) : ?>
						<tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--category">
							<th class="woocommerce-product-attributes-item__label"><?php esc_html_e( 'Category', 'fashion-brand-theme' ); ?></th>
							<td class="woocommerce-product-attributes-item__value"><?php echo wp_kses_post( $categories ); ?></td>
						</tr>
					<?php endif; ?>
					<?php if ( $product_tags ) : ?>
						<tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--tags">
							<th class="woocommerce-product-attributes-item__label"><?php esc_html_e( 'Tags', 'fashion-brand-theme' ); ?></th>
							<td class="woocommerce-product-attributes-item__value"><?php echo wp_kses_post( $product_tags ); ?></td>
						</tr>
					<?php endif; ?>
				</table>
				<?php
			},
			5
		);
	}
	?>

	<div class="product-tabs-wrap woocommerce-tabs wc-tabs-wrapper">
		<?php woocommerce_output_product_data_tabs(); ?>
	</div>

	<section class="product-related-wrap">
		<?php
		$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'You may also like', 'fashion-brand-theme' ) );
		echo '<header class="product-related-wrap__header">';
		echo '<h2>' . esc_html( $heading ) . '</h2>';
		echo '<p class="product-related-wrap__subtitle">' . esc_html__( 'Other pieces from the same field.', 'fashion-brand-theme' ) . '</p>';
		echo '</header>';
		woocommerce_output_related_products();
		?>
	</section>
</div>

<?php get_template_part( 'template-parts/product/size-guide', 'modal' ); ?>
<?php get_template_part( 'template-parts/product/sticky', 'atc' ); ?>

<?php do_action( 'woocommerce_after_single_product' ); ?>
