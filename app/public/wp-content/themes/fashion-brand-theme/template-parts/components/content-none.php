<?php
/**
 * No content partial — empty journal / archive state.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
if ( ! $shop_url ) {
	$shop_url = home_url( '/' );
}
?>
<section class="blog-empty" role="status">
	<p class="blog-empty__eyebrow"><?php esc_html_e( 'Journal', 'fashion-brand-theme' ); ?></p>
	<h2 class="blog-empty__title"><?php esc_html_e( 'Nothing here yet', 'fashion-brand-theme' ); ?></h2>
	<p class="blog-empty__text">
		<?php esc_html_e( 'New field notes and guides will appear here. In the meantime, explore the shop.', 'fashion-brand-theme' ); ?>
	</p>
	<a class="button button--primary" href="<?php echo esc_url( $shop_url ); ?>">
		<?php esc_html_e( 'Shop now', 'fashion-brand-theme' ); ?>
	</a>
</section>
