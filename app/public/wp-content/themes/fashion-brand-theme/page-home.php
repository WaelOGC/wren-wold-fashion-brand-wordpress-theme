<?php
/**
 * Front page — cinematic scroll composition.
 *
 * @package Fashion_Brand_Theme
 */

get_header();
?>

<main id="primary" class="site-main site-main--front-page site-main--cinematic">
	<?php get_template_part( 'template-parts/homepage/cinematic', 'chrome' ); ?>

	<div class="scroll-wrap">
		<div class="scene-track">
			<?php fashion_brand_theme_render_homepage_sections(); ?>
		</div>
	</div>
</main>

<?php
get_footer();
