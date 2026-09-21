<?php
/**
 * Wishlist page template.
 *
 * Used automatically for a Page with slug "wishlist".
 *
 * @package Fashion_Brand_Theme
 */

get_header();

$account_url = fashion_brand_theme_get_account_url();
$shop_url    = fashion_brand_theme_get_shop_url();
$is_logged_in = is_user_logged_in();
$wishlist_ids = $is_logged_in ? fashion_brand_theme_get_wishlist_ids() : array();
?>

<main id="primary" class="site-main wishlist-page">
	<header class="wishlist-page__header">
		<h1 class="wishlist-page__title"><?php esc_html_e( 'My Wishlist', 'fashion-brand-theme' ); ?></h1>
	</header>

	<?php if ( ! $is_logged_in ) : ?>
		<div class="wishlist-page__signin" role="status">
			<p class="wishlist-page__signin-text"><?php esc_html_e( 'Sign in to save favorites', 'fashion-brand-theme' ); ?></p>
			<a class="button button--primary wishlist-page__signin-cta" href="<?php echo esc_url( $account_url ); ?>">
				<?php esc_html_e( 'Sign in', 'fashion-brand-theme' ); ?>
			</a>
		</div>
	<?php elseif ( empty( $wishlist_ids ) ) : ?>
		<div class="wishlist-page__empty" role="status" data-wishlist-empty>
			<p class="wishlist-page__empty-text"><?php esc_html_e( 'Your wishlist is empty.', 'fashion-brand-theme' ); ?></p>
			<a class="button button--primary" href="<?php echo esc_url( $shop_url ); ?>">
				<?php esc_html_e( 'Shop now', 'fashion-brand-theme' ); ?>
			</a>
		</div>
	<?php else : ?>
		<div class="wishlist-page__grid shop-main container container--wide" data-wishlist-grid>
			<?php
			$wishlist_query = new WP_Query(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'post__in'       => $wishlist_ids,
					'posts_per_page' => -1,
					'orderby'        => 'post__in',
				)
			);

			if ( $wishlist_query->have_posts() ) {
				wc_set_loop_prop( 'columns', 4 );
				woocommerce_product_loop_start();

				while ( $wishlist_query->have_posts() ) {
					$wishlist_query->the_post();
					do_action( 'woocommerce_shop_loop' );
					wc_get_template_part( 'content', 'product' );
				}

				woocommerce_product_loop_end();
				wp_reset_postdata();
			} else {
				?>
				<div class="wishlist-page__empty" role="status" data-wishlist-empty>
					<p class="wishlist-page__empty-text"><?php esc_html_e( 'Your wishlist is empty.', 'fashion-brand-theme' ); ?></p>
					<a class="button button--primary" href="<?php echo esc_url( $shop_url ); ?>">
						<?php esc_html_e( 'Shop now', 'fashion-brand-theme' ); ?>
					</a>
				</div>
				<?php
			}
			?>
		</div>

		<template data-wishlist-empty-template>
			<div class="wishlist-page__empty" role="status" data-wishlist-empty>
				<p class="wishlist-page__empty-text"><?php esc_html_e( 'Your wishlist is empty.', 'fashion-brand-theme' ); ?></p>
				<a class="button button--primary" href="<?php echo esc_url( $shop_url ); ?>">
					<?php esc_html_e( 'Shop now', 'fashion-brand-theme' ); ?>
				</a>
			</div>
		</template>
	<?php endif; ?>
</main>

<?php
get_footer();
