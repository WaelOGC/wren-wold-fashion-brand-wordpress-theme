<?php
/**
 * Theme setup.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme defaults and WordPress feature support.
 */
function fashion_brand_theme_setup() {
	load_theme_textdomain( 'fashion-brand-theme', FASHION_BRAND_THEME_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Navigation', 'fashion-brand-theme' ),
			'utility' => esc_html__( 'Utility Navigation', 'fashion-brand-theme' ),
			'footer'  => esc_html__( 'Footer Menu', 'fashion-brand-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'fashion_brand_theme_setup' );

/**
 * Set the content width in pixels.
 */
function fashion_brand_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'fashion_brand_theme_content_width', 1200 );
}
add_action( 'after_setup_theme', 'fashion_brand_theme_content_width', 0 );

/**
 * Ensure the Collections landing page exists for nav and page-collections.php.
 *
 * @return void
 */
function fashion_brand_theme_ensure_collections_page() {
	if ( get_page_by_path( 'collections' ) ) {
		return;
	}

	wp_insert_post(
		array(
			'post_title'   => __( 'Collections', 'fashion-brand-theme' ),
			'post_name'    => 'collections',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		)
	);
}
add_action( 'init', 'fashion_brand_theme_ensure_collections_page' );

/**
 * Ensure the Contact page exists for nav and page-contact.php.
 *
 * @return void
 */
function fashion_brand_theme_ensure_contact_page() {
	if ( get_page_by_path( 'contact' ) ) {
		return;
	}

	wp_insert_post(
		array(
			'post_title'   => __( 'Contact', 'fashion-brand-theme' ),
			'post_name'    => 'contact',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		)
	);
}
add_action( 'init', 'fashion_brand_theme_ensure_contact_page' );

/**
 * Ensure the Home page exists and is set as the static front page.
 *
 * @return void
 */
function fashion_brand_theme_ensure_home_page() {
	$home_page = get_page_by_path( 'home' );

	if ( ! $home_page ) {
		$page_id = wp_insert_post(
			array(
				'post_title'   => __( 'Home', 'fashion-brand-theme' ),
				'post_name'    => 'home',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			)
		);
	} else {
		$page_id = $home_page->ID;
	}

	if ( ! $page_id ) {
		return;
	}

	if ( 'page' !== get_option( 'show_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
	}

	if ( (int) get_option( 'page_on_front' ) !== (int) $page_id ) {
		update_option( 'page_on_front', $page_id );
	}
}
add_action( 'init', 'fashion_brand_theme_ensure_home_page' );
