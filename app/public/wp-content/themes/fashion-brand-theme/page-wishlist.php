<?php
/**
 * Wishlist page template.
 *
 * Used automatically for a Page with slug "wishlist".
 *
 * @package Fashion_Brand_Theme
 */

get_header();
?>

<main id="primary" class="site-main wishlist-page">
	<header class="wishlist-page__header">
		<h1 class="wishlist-page__title"><?php esc_html_e( 'My Wishlist', 'fashion-brand-theme' ); ?></h1>
	</header>

	<?php get_template_part( 'template-parts/account/wishlist', 'content' ); ?>
</main>

<?php
get_footer();
