<?php
/**
 * Custom action and filter hooks.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output opening markup for the site header region.
 *
 * Front page uses the cinematic overlay chrome instead of the persistent header.
 */
function fashion_brand_theme_header() {
	do_action( 'fashion_brand_theme_before_header' );

	if ( ! is_front_page() ) {
		get_template_part( 'template-parts/header/site', 'header' );
	}

	do_action( 'fashion_brand_theme_after_header' );
}

/**
 * Output closing markup for the site footer region.
 */
function fashion_brand_theme_footer() {
	do_action( 'fashion_brand_theme_before_footer' );
	get_template_part( 'template-parts/footer/site', 'footer' );
	do_action( 'fashion_brand_theme_after_footer' );
}

/**
 * Whether the fixed mobile bottom tab bar should render.
 *
 * @return bool
 */
function fashion_brand_theme_should_show_mobile_bottom_nav() {
	if ( is_front_page() ) {
		return false;
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		return false;
	}

	if ( function_exists( 'is_cart' ) && is_cart() ) {
		return false;
	}

	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		return false;
	}

	if ( is_page_template( 'page-home.php' ) ) {
		return false;
	}

	return true;
}

/**
 * Body class when the mobile bottom tab bar will render.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function fashion_brand_theme_mobile_bottom_nav_body_class( $classes ) {
	if ( fashion_brand_theme_should_show_mobile_bottom_nav() ) {
		$classes[] = 'has-mobile-bottom-nav';
	}

	return $classes;
}
add_filter( 'body_class', 'fashion_brand_theme_mobile_bottom_nav_body_class' );
