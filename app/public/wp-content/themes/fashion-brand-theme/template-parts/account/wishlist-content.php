<?php
/**
 * Shared wishlist content (standalone page + My Account tab).
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$account_url  = fashion_brand_theme_get_account_url();
$shop_url     = fashion_brand_theme_get_shop_url();
$is_logged_in = is_user_logged_in();
$wishlist_ids = $is_logged_in ? fashion_brand_theme_get_wishlist_ids() : array();

if ( ! $is_logged_in ) :
	?>
	<div class="wishlist-page__signin" role="status">
		<p class="wishlist-page__signin-text"><?php esc_html_e( 'Sign in to save favorites', 'fashion-brand-theme' ); ?></p>
		<a class="button button--primary wishlist-page__signin-cta" href="<?php echo esc_url( $account_url ); ?>">
			<?php esc_html_e( 'Sign in', 'fashion-brand-theme' ); ?>
		</a>
	</div>
	<?php
elseif ( empty( $wishlist_ids ) ) :
	?>
	<div class="wishlist-page__empty" role="status" data-wishlist-empty>
		<p class="wishlist-page__empty-text"><?php esc_html_e( 'Your wishlist is empty.', 'fashion-brand-theme' ); ?></p>
		<a class="button button--primary" href="<?php echo esc_url( $shop_url ); ?>">
			<?php esc_html_e( 'Shop now', 'fashion-brand-theme' ); ?>
		</a>
	</div>
	<?php
else :
	?>
	<script>
	(function () {
		try {
			var v = localStorage.getItem('wrenwold_wishlist_view');
			if (v === 'list' || v === 'grid') {
				document.documentElement.setAttribute('data-wishlist-view', v);
			}
		} catch (e) {}
	})();
	</script>
	<div class="wishlist-page__toolbar" data-wishlist-view-toggle>
		<span class="wishlist-page__toolbar-label"><?php esc_html_e( 'View', 'fashion-brand-theme' ); ?></span>
		<div class="wishlist-page__view-btns" role="group" aria-label="<?php esc_attr_e( 'Wishlist layout', 'fashion-brand-theme' ); ?>">
			<button type="button" class="wishlist-page__view-btn is-active" data-wishlist-view="grid" aria-pressed="true" aria-label="<?php esc_attr_e( 'Grid view', 'fashion-brand-theme' ); ?>">
				<?php echo fashion_brand_theme_get_icon( 'layout-grid', array( 'width' => 18, 'height' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php esc_html_e( 'Grid', 'fashion-brand-theme' ); ?></span>
			</button>
			<button type="button" class="wishlist-page__view-btn" data-wishlist-view="list" aria-pressed="false" aria-label="<?php esc_attr_e( 'List view', 'fashion-brand-theme' ); ?>">
				<?php echo fashion_brand_theme_get_icon( 'list', array( 'width' => 18, 'height' => 18 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php esc_html_e( 'List', 'fashion-brand-theme' ); ?></span>
			</button>
		</div>
	</div>

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
	<?php
endif;
