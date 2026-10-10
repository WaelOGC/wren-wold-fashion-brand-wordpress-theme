<?php
/**
 * 404 template.
 *
 * @package Fashion_Brand_Theme
 */

get_header();

$hero_eyebrow = __( 'Page Not Found', 'fashion-brand-theme' );
$hero_heading = __( 'Looks like this page stepped out.', 'fashion-brand-theme' );
$body_copy    = __( "The page you're looking for may have been moved, renamed, or no longer exists. Let's get you back to where the good things are.", 'fashion-brand-theme' );
$shop_label   = __( 'Return to Shop', 'fashion-brand-theme' );
$home_label   = __( 'Back to Homepage', 'fashion-brand-theme' );
?>

<main id="primary" class="site-main error-404-page">
	<section class="error-404-scene" aria-labelledby="error-404-heading">
		<div
			class="error-404-scene__image"
			style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/404-photography/404-empty-rack.webp' ); ?>');"
		></div>
		<div class="error-404-scene__scrim" aria-hidden="true"></div>

		<div class="error-404-scene__content">
			<p class="error-404-scene__eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
			<h1 id="error-404-heading" class="error-404-scene__heading">
				<?php echo esc_html( $hero_heading ); ?>
			</h1>
			<p class="error-404-scene__body"><?php echo esc_html( $body_copy ); ?></p>

			<div class="error-404-scene__actions">
				<a class="error-404-scene__cta" href="<?php echo esc_url( fashion_brand_theme_get_shop_url() ); ?>">
					<?php echo esc_html( $shop_label ); ?>
				</a>
				<a class="error-404-scene__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php echo esc_html( $home_label ); ?>
				</a>
			</div>

			<form class="error-404-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="error-404-search-field"><?php esc_html_e( 'Search', 'fashion-brand-theme' ); ?></label>
				<input type="search" id="error-404-search-field" class="error-404-search__field" name="s" placeholder="<?php esc_attr_e( 'Search the shop…', 'fashion-brand-theme' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
				<button type="submit" class="error-404-search__submit"><?php esc_html_e( 'Search', 'fashion-brand-theme' ); ?></button>
			</form>
		</div>
	</section>
</main>

<?php
get_footer();
