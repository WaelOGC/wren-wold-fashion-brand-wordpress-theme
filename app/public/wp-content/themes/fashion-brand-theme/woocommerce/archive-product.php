<?php
/**
 * The Template for displaying product archives (Shop / Category).
 *
 * @package Fashion_Brand_Theme
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 */
do_action( 'woocommerce_before_main_content' );

$hero_images = fashion_brand_theme_get_shop_hero_images();
$hero_desktop = isset( $hero_images['desktop'] ) ? $hero_images['desktop'] : '';
$hero_mobile  = isset( $hero_images['mobile'] ) ? $hero_images['mobile'] : '';
$cat_desc       = '';
$is_product_cat = is_product_category();

if ( $is_product_cat ) {
	$queried = get_queried_object();
	if ( $queried instanceof WP_Term && ! empty( $queried->description ) ) {
		$cat_desc = wp_strip_all_tags( $queried->description );
	}
}
?>

<section id="shop-view" class="shop-view">
	<header class="shop-hero">
		<?php if ( $hero_desktop ) : ?>
			<picture class="shop-hero__picture">
				<source media="(max-width: 900px)" srcset="<?php echo esc_url( $hero_mobile ? $hero_mobile : $hero_desktop ); ?>" />
				<img
					class="shop-hero__image"
					src="<?php echo esc_url( $hero_desktop ); ?>"
					alt=""
					loading="eager"
					fetchpriority="high"
					decoding="async"
				/>
			</picture>
		<?php endif; ?>

		<div class="shop-hero__inner container container--wide">
			<div class="shop-hero__copy">
				<p class="shop-hero__eyebrow"><?php esc_html_e( 'Timeless essentials', 'fashion-brand-theme' ); ?></p>

				<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
					<h1 class="shop-hero__title"><?php woocommerce_page_title(); ?></h1>
				<?php endif; ?>

				<?php if ( $cat_desc ) : ?>
					<p class="shop-hero__subtitle"><?php echo esc_html( $cat_desc ); ?></p>
				<?php else : ?>
					<p class="shop-hero__subtitle shop-hero__subtitle--desktop">
						<?php esc_html_e( 'Thoughtfully designed pieces for a more intentional everyday.', 'fashion-brand-theme' ); ?>
					</p>
					<p class="shop-hero__subtitle shop-hero__subtitle--mobile">
						<?php esc_html_e( 'Everyday pieces for a more intentional tomorrow.', 'fashion-brand-theme' ); ?>
					</p>
				<?php endif; ?>

				<div class="shop-hero__rule">
					<span class="shop-hero__rule-line" aria-hidden="true"></span>
					<span class="shop-hero__rule-label shop-hero__rule-label--desktop"><?php esc_html_e( 'Wear a brighter tomorrow', 'fashion-brand-theme' ); ?></span>
					<span class="shop-hero__rule-label shop-hero__rule-label--mobile"><?php esc_html_e( 'Clothing with intention', 'fashion-brand-theme' ); ?></span>
				</div>
			</div>
		</div>

		<p class="shop-hero__script" aria-hidden="true">
			<span>More</span>
			<span>than</span>
			<span>clothes</span>
		</p>
	</header>

	<?php get_template_part( 'template-parts/shop/toolbar' ); ?>

	<?php get_template_part( 'template-parts/shop/category', 'filter' ); ?>

	<div class="shop-main container container--wide">
		<?php if ( woocommerce_product_loop() ) : ?>
			<?php do_action( 'woocommerce_before_shop_loop' ); ?>

			<?php woocommerce_product_loop_start(); ?>

			<?php
			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();
					do_action( 'woocommerce_shop_loop' );
					wc_get_template_part( 'content', 'product' );
				}
			}
			?>

			<?php woocommerce_product_loop_end(); ?>

			<?php do_action( 'woocommerce_after_shop_loop' ); ?>
		<?php else : ?>
			<?php do_action( 'woocommerce_no_products_found' ); ?>
		<?php endif; ?>
	</div>

	<div class="shop-side-decor" aria-hidden="true">
		<div class="shop-side-decor__left">
			<span class="shop-side-decor__rule"></span>
			<span class="shop-side-decor__text"><?php echo esc_html__( 'Clothing with intention', 'fashion-brand-theme' ); ?></span>
		</div>
		<div class="shop-side-decor__right">
			<span class="shop-side-decor__text"><?php echo esc_html__( 'For a brighter tomorrow', 'fashion-brand-theme' ); ?></span>
			<span class="shop-side-decor__rule"></span>
		</div>
	</div>

	<?php get_template_part( 'template-parts/shop/sidebar', 'filters' ); ?>
</section>

<?php get_template_part( 'template-parts/shop/quick-view', 'modal' ); ?>

<?php
do_action( 'woocommerce_after_main_content' );
get_footer( 'shop' );
