<?php
/**
 * Footer template.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	</div><!-- #content -->

	<?php fashion_brand_theme_footer(); ?>
	<?php get_template_part( 'template-parts/global/mobile', 'bottom-nav' ); ?>
	<?php
	if ( function_exists( 'fashion_brand_theme_should_render_cart_drawer' ) && fashion_brand_theme_should_render_cart_drawer() ) {
		get_template_part( 'template-parts/cart/cart', 'drawer' );
	}
	?>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
