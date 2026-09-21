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
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
