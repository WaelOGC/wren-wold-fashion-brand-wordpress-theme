<?php
/**
 * Theme settings — defaults, getters, sanitization, and groups.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FASHION_BRAND_THEME_SETTINGS_OPTION', 'fashion_brand_theme_settings' );

/**
 * Admin settings tabs.
 *
 * @return array<string, string>
 */
function fashion_brand_theme_get_settings_tabs() {
	return array(
		'general'  => __( 'General', 'fashion-brand-theme' ),
		'header'   => __( 'Header', 'fashion-brand-theme' ),
		'footer'   => __( 'Footer', 'fashion-brand-theme' ),
		'homepage' => __( 'Homepage', 'fashion-brand-theme' ),
		'shop'     => __( 'Shop Presentation', 'fashion-brand-theme' ),
		'contact'  => __( 'Contact', 'fashion-brand-theme' ),
		'social'   => __( 'Social & Integrations', 'fashion-brand-theme' ),
	);
}

/**
 * Map admin tabs to settings section IDs.
 *
 * @return array<string, string[]>
 */
function fashion_brand_theme_get_settings_tab_sections() {
	return array(
		'general'  => array( 'fashion_brand_theme_general' ),
		'header'   => array( 'fashion_brand_theme_header' ),
		'footer'   => array( 'fashion_brand_theme_footer' ),
		'homepage' => array( 'fashion_brand_theme_homepage' ),
		'shop'     => array( 'fashion_brand_theme_shop' ),
		'contact'  => array( 'fashion_brand_theme_contact' ),
		'social'   => array( 'fashion_brand_theme_social' ),
	);
}

/**
 * Default homepage copy used when settings are empty.
 *
 * @return array<string, string>
 */
function fashion_brand_theme_get_homepage_content_defaults() {
	return array(
		'homepage_hero_eyebrow'        => __( 'Quiet Character', 'fashion-brand-theme' ),
		'homepage_hero_heading'        => __( 'Clothing with intention.', 'fashion-brand-theme' ),
		'homepage_hero_text'           => __( 'Thoughtfully made fashion for a life well lived — considered pieces for modern European living.', 'fashion-brand-theme' ),
		'homepage_hero_cta_label'      => __( 'Explore the Shop', 'fashion-brand-theme' ),
		'homepage_cta_heading'         => __( 'Begin with pieces that feel considered — and stay relevant.', 'fashion-brand-theme' ),
		'homepage_cta_text'            => __( 'A calm, curated shop shaped by Quiet Character — quality over quantity.', 'fashion-brand-theme' ),
		'homepage_cta_primary_label'   => __( 'Shop the Edit', 'fashion-brand-theme' ),
		'homepage_cta_secondary_label' => __( 'View Collections', 'fashion-brand-theme' ),
	);
}

/**
 * Default theme settings.
 *
 * @return array<string, mixed>
 */
function fashion_brand_theme_get_default_settings() {
	$homepage_defaults = fashion_brand_theme_get_homepage_content_defaults();

	return array(
		// General.
		'display_label'              => '',
		'announcement_enabled'       => false,
		'announcement_text'          => '',
		'announcement_link_enabled'  => false,
		'announcement_link_url'      => '',
		'announcement_link_label'    => '',
		// Header.
		'header_search_enabled'      => true,
		'header_account_enabled'     => true,
		'header_cart_enabled'        => true,
		'header_sticky_enabled'      => false,
		'header_scroll_shadow_enabled' => false,
		'header_mobile_menu_enabled' => true,
		'header_search_panel_enabled' => true,
		// Footer.
		'footer_visible'             => true,
		'footer_copyright_text'      => '',
		'footer_copyright_enabled'   => true,
		'footer_social_enabled'      => true,
		'footer_menu_enabled'        => false,
		'cookie_banner_enabled'      => true,
		// Homepage visibility.
		'homepage_hero_enabled'      => true,
		'homepage_philosophy_enabled' => true,
		'homepage_categories_enabled' => true,
		'homepage_collection_enabled' => true,
		'homepage_guides_enabled'    => true,
		'homepage_cta_enabled'       => true,
		// Homepage content.
		'homepage_hero_eyebrow'      => '',
		'homepage_hero_heading'      => '',
		'homepage_hero_text'         => '',
		'homepage_hero_cta_label'    => '',
		'homepage_hero_cta_url'      => '',
		'homepage_cta_heading'       => '',
		'homepage_cta_text'          => '',
		'homepage_cta_primary_label' => '',
		'homepage_cta_primary_url'   => '',
		'homepage_cta_secondary_label' => '',
		'homepage_cta_secondary_url' => '',
		// Shop presentation (used when WooCommerce is available).
		'shop_grid_columns'          => 4,
		'shop_products_per_page'     => 12,
		'shop_show_price'            => true,
		'shop_show_excerpt'          => false,
		'shop_show_category'         => true,
		'shop_show_badges'           => true,
		'shop_shipping_info'         => '',
		'shop_returns_info'          => '',
		'shop_footer_tagline'        => 'Clothing with intention.',
		// Contact / WhatsApp.
		'whatsapp_enabled'           => false,
		'whatsapp_number'            => '',
		'whatsapp_message'           => __( 'Hi, I have a question about...', 'fashion-brand-theme' ),
		// Social.
		'social_enabled'             => true,
		'social_open_new_tab'        => true,
		'social_instagram'           => '',
		'social_facebook'            => '',
		'social_pinterest'           => '',
		'social_tiktok'              => '',
		'social_linkedin'            => '',
		'social_youtube'             => '',
	);
}

/**
 * Retrieve all theme settings merged with defaults.
 *
 * @return array<string, mixed>
 */
function fashion_brand_theme_get_settings() {
	$stored = get_option( FASHION_BRAND_THEME_SETTINGS_OPTION, array() );

	if ( ! is_array( $stored ) ) {
		$stored = array();
	}

	return wp_parse_args( $stored, fashion_brand_theme_get_default_settings() );
}

/**
 * Retrieve a single theme setting.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function fashion_brand_theme_get_setting( $key ) {
	$settings = fashion_brand_theme_get_settings();

	return $settings[ $key ] ?? null;
}

/**
 * Determine if a boolean theme setting is enabled.
 *
 * @param string $key Setting key.
 * @return bool
 */
function fashion_brand_theme_is_setting_enabled( $key ) {
	return (bool) fashion_brand_theme_get_setting( $key );
}

/**
 * Sanitize a checkbox value.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function fashion_brand_theme_sanitize_checkbox_value( $value ) {
	return '1' === (string) $value || 1 === $value || true === $value;
}

/**
 * Checkbox setting keys.
 *
 * @return string[]
 */
function fashion_brand_theme_get_checkbox_setting_keys() {
	return array(
		'announcement_enabled',
		'announcement_link_enabled',
		'header_search_enabled',
		'header_account_enabled',
		'header_cart_enabled',
		'header_sticky_enabled',
		'header_scroll_shadow_enabled',
		'header_mobile_menu_enabled',
		'header_search_panel_enabled',
		'footer_visible',
		'footer_copyright_enabled',
		'footer_social_enabled',
		'footer_menu_enabled',
		'cookie_banner_enabled',
		'homepage_hero_enabled',
		'homepage_philosophy_enabled',
		'homepage_categories_enabled',
		'homepage_collection_enabled',
		'homepage_guides_enabled',
		'homepage_cta_enabled',
		'shop_show_price',
		'shop_show_excerpt',
		'shop_show_category',
		'shop_show_badges',
		'whatsapp_enabled',
		'social_enabled',
		'social_open_new_tab',
	);
}

/**
 * Sanitize theme settings before saving.
 *
 * @param array<string, mixed>|mixed $input Submitted settings.
 * @return array<string, mixed>
 */
function fashion_brand_theme_sanitize_settings( $input ) {
	$defaults  = fashion_brand_theme_get_default_settings();
	$existing  = fashion_brand_theme_get_settings();
	$sanitized = $defaults;

	if ( ! is_array( $input ) ) {
		return $existing;
	}

	$merged = array_merge( $existing, $input );

	foreach ( $defaults as $key => $default_value ) {
		if ( ! array_key_exists( $key, $merged ) ) {
			continue;
		}

		$sanitized[ $key ] = fashion_brand_theme_sanitize_setting_value( $key, $merged[ $key ] );
	}

	return $sanitized;
}

/**
 * Sanitize an individual setting value by key.
 *
 * @param string $key   Setting key.
 * @param mixed  $value Raw value.
 * @return mixed
 */
function fashion_brand_theme_sanitize_setting_value( $key, $value ) {
	if ( in_array( $key, fashion_brand_theme_get_checkbox_setting_keys(), true ) ) {
		return fashion_brand_theme_sanitize_checkbox_value( $value );
	}

	switch ( $key ) {
		case 'announcement_text':
		case 'homepage_hero_text':
		case 'homepage_cta_text':
		case 'shop_shipping_info':
		case 'shop_returns_info':
		case 'whatsapp_message':
			return sanitize_textarea_field( wp_unslash( (string) $value ) );

		case 'whatsapp_number':
			return preg_replace( '/\D+/', '', (string) wp_unslash( $value ) );

		case 'display_label':
		case 'announcement_link_label':
		case 'footer_copyright_text':
		case 'homepage_hero_eyebrow':
		case 'homepage_hero_heading':
		case 'homepage_hero_cta_label':
		case 'homepage_cta_heading':
		case 'homepage_cta_primary_label':
		case 'homepage_cta_secondary_label':
		case 'shop_footer_tagline':
			return sanitize_text_field( wp_unslash( (string) $value ) );

		case 'announcement_link_url':
		case 'homepage_hero_cta_url':
		case 'homepage_cta_primary_url':
		case 'homepage_cta_secondary_url':
		case 'social_instagram':
		case 'social_facebook':
		case 'social_pinterest':
		case 'social_tiktok':
		case 'social_linkedin':
		case 'social_youtube':
			return esc_url_raw( wp_unslash( (string) $value ) );

		case 'shop_grid_columns':
			return max( 2, min( 4, absint( $value ) ) );

		case 'shop_products_per_page':
			return max( 1, min( 48, absint( $value ) ) );

		default:
			return $value;
	}
}

/**
 * Persist a single setting into the shared option array.
 *
 * @param string $key   Setting key.
 * @param mixed  $value Raw value.
 * @return mixed Sanitized value.
 */
function fashion_brand_theme_persist_setting( $key, $value ) {
	$settings         = fashion_brand_theme_get_settings();
	$settings[ $key ] = fashion_brand_theme_sanitize_setting_value( $key, $value );
	$sanitized        = fashion_brand_theme_sanitize_settings( $settings );

	update_option( FASHION_BRAND_THEME_SETTINGS_OPTION, $sanitized );

	return $sanitized[ $key ];
}

/**
 * Retrieve the front-end display label fallback.
 *
 * @return string
 */
function fashion_brand_theme_get_display_label() {
	$label = fashion_brand_theme_get_setting( 'display_label' );

	if ( is_string( $label ) && '' !== trim( $label ) ) {
		return $label;
	}

	return __( 'WREN WOLD', 'fashion-brand-theme' );
}

/**
 * Retrieve homepage text with fallback defaults.
 *
 * @param string $key Setting key.
 * @return string
 */
function fashion_brand_theme_get_homepage_text( $key ) {
	$value = fashion_brand_theme_get_setting( $key );

	if ( is_string( $value ) && '' !== trim( $value ) ) {
		return $value;
	}

	$defaults = fashion_brand_theme_get_homepage_content_defaults();

	return $defaults[ $key ] ?? '';
}

/**
 * Retrieve homepage URL with fallback.
 *
 * @param string $key          Setting key.
 * @param string $fallback_url Fallback URL.
 * @return string
 */
function fashion_brand_theme_get_homepage_url( $key, $fallback_url ) {
	$url = fashion_brand_theme_get_setting( $key );

	if ( is_string( $url ) && '' !== trim( $url ) ) {
		return $url;
	}

	return $fallback_url;
}

/**
 * Approved homepage sections in code-controlled order.
 *
 * @return array<string, array<string, string>>
 */
function fashion_brand_theme_get_homepage_sections() {
	return array(
		'hero'                 => array(
			'setting'  => 'homepage_hero_enabled',
			'template' => 'template-parts/homepage/hero',
		),
		'philosophy'           => array(
			'setting'  => 'homepage_philosophy_enabled',
			'template' => 'template-parts/homepage/philosophy',
		),
		'categories'           => array(
			'setting'  => 'homepage_categories_enabled',
			'template' => 'template-parts/homepage/categories',
		),
		'featured-collection'  => array(
			'setting'  => 'homepage_collection_enabled',
			'template' => 'template-parts/homepage/featured-collection',
		),
		'guides'               => array(
			'setting'  => 'homepage_guides_enabled',
			'template' => 'template-parts/homepage/guides',
		),
		'closing-cta'          => array(
			'setting'  => 'homepage_cta_enabled',
			'template' => 'template-parts/homepage/closing-cta',
		),
	);
}

/**
 * Render enabled homepage sections in approved order.
 *
 * @return void
 */
function fashion_brand_theme_render_homepage_sections() {
	foreach ( fashion_brand_theme_get_homepage_sections() as $slug => $section ) {
		if ( ! fashion_brand_theme_is_setting_enabled( $section['setting'] ) ) {
			continue;
		}

		get_template_part( $section['template'] );
	}
}

/**
 * Header CSS modifier classes from settings.
 *
 * @return string[]
 */
function fashion_brand_theme_get_header_modifier_classes() {
	$classes = array();

	if ( fashion_brand_theme_is_setting_enabled( 'header_sticky_enabled' ) ) {
		$classes[] = 'site-header--sticky';
	}

	if ( fashion_brand_theme_is_setting_enabled( 'header_scroll_shadow_enabled' ) ) {
		$classes[] = 'site-header--scroll-shadow';
	}

	if ( ! fashion_brand_theme_is_setting_enabled( 'header_mobile_menu_enabled' ) ) {
		$classes[] = 'site-header--no-mobile-menu';
	}

	return $classes;
}

/**
 * Output the announcement bar when enabled.
 *
 * @return void
 */
function fashion_brand_theme_render_announcement_bar() {
	if ( ! fashion_brand_theme_is_setting_enabled( 'announcement_enabled' ) ) {
		return;
	}

	$text = fashion_brand_theme_get_setting( 'announcement_text' );

	if ( ! is_string( $text ) || '' === trim( $text ) ) {
		return;
	}

	get_template_part( 'template-parts/global/announcement', 'bar' );
}
add_action( 'fashion_brand_theme_before_header', 'fashion_brand_theme_render_announcement_bar' );

/**
 * Apply shop presentation settings when WooCommerce is active.
 *
 * @return void
 */
function fashion_brand_theme_apply_shop_presentation_settings() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$columns = absint( fashion_brand_theme_get_setting( 'shop_grid_columns' ) );
	$per_page = absint( fashion_brand_theme_get_setting( 'shop_products_per_page' ) );

	add_filter(
		'loop_shop_columns',
		static function () use ( $columns ) {
			return $columns;
		}
	);

	add_filter(
		'loop_shop_per_page',
		static function () use ( $per_page ) {
			return $per_page;
		},
		20
	);
}
add_action( 'after_setup_theme', 'fashion_brand_theme_apply_shop_presentation_settings', 20 );
