<?php
/**
 * Theme Settings field registration.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme settings sections and fields.
 *
 * @return void
 */
function fashion_brand_theme_register_settings() {
	register_setting(
		'fashion_brand_theme_settings_group',
		FASHION_BRAND_THEME_SETTINGS_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'fashion_brand_theme_sanitize_settings',
			'default'           => fashion_brand_theme_get_default_settings(),
		)
	);

	fashion_brand_theme_register_settings_sections();
	fashion_brand_theme_register_settings_fields();
}
add_action( 'admin_init', 'fashion_brand_theme_register_settings' );

/**
 * Register settings sections.
 *
 * @return void
 */
function fashion_brand_theme_register_settings_sections() {
	$sections = array(
		'fashion_brand_theme_general'  => array(
			'title'       => __( 'General', 'fashion-brand-theme' ),
			'description' => __( 'Optional announcement messaging and display label fallback.', 'fashion-brand-theme' ),
		),
		'fashion_brand_theme_header'   => array(
			'title'       => __( 'Header', 'fashion-brand-theme' ),
			'description' => __( 'Utility header controls. Navigation structure remains code-controlled.', 'fashion-brand-theme' ),
		),
		'fashion_brand_theme_footer'   => array(
			'title'       => __( 'Footer', 'fashion-brand-theme' ),
			'description' => __( 'Footer visibility and content. Menus are managed under Appearance → Menus.', 'fashion-brand-theme' ),
		),
		'fashion_brand_theme_homepage' => array(
			'title'       => __( 'Homepage', 'fashion-brand-theme' ),
			'description' => __( 'Enable or disable homepage sections and edit limited hero/CTA copy. Section order and layout remain code-controlled.', 'fashion-brand-theme' ),
		),
		'fashion_brand_theme_shop'     => array(
			'title'       => __( 'Shop Presentation', 'fashion-brand-theme' ),
			'description' => __( 'Presentation-only settings applied when WooCommerce is installed. Product, pricing, and checkout configuration remain in WooCommerce.', 'fashion-brand-theme' ),
		),
		'fashion_brand_theme_social'   => array(
			'title'       => __( 'Social & Integrations', 'fashion-brand-theme' ),
			'description' => __( 'External profile links for theme output. URLs are sanitized on save.', 'fashion-brand-theme' ),
		),
	);

	foreach ( $sections as $id => $section ) {
		add_settings_section(
			$id,
			$section['title'],
			static function () use ( $section ) {
				echo '<p>' . esc_html( $section['description'] ) . '</p>';
			},
			'fashion-brand-theme'
		);
	}
}

/**
 * Canonical field definitions for Theme Settings (registration + dashboard UI).
 *
 * Each item: key, label, section, type, description.
 *
 * @return array<int, array{0:string,1:string,2:string,3:string,4?:string}>
 */
function fashion_brand_theme_get_settings_fields_definition() {
	return array(
		// General.
		array( 'display_label', __( 'Display Label Fallback', 'fashion-brand-theme' ), 'fashion_brand_theme_general', 'text', __( 'Used when no custom logo is set. Does not replace the WordPress site title.', 'fashion-brand-theme' ) ),
		array( 'announcement_enabled', __( 'Enable Announcement Bar', 'fashion-brand-theme' ), 'fashion_brand_theme_general', 'checkbox', __( 'Optional bar above the site header.', 'fashion-brand-theme' ) ),
		array( 'announcement_text', __( 'Announcement Text', 'fashion-brand-theme' ), 'fashion_brand_theme_general', 'textarea' ),
		array( 'announcement_link_enabled', __( 'Enable Announcement Link', 'fashion-brand-theme' ), 'fashion_brand_theme_general', 'checkbox' ),
		array( 'announcement_link_label', __( 'Announcement Link Label', 'fashion-brand-theme' ), 'fashion_brand_theme_general', 'text' ),
		array( 'announcement_link_url', __( 'Announcement Link URL', 'fashion-brand-theme' ), 'fashion_brand_theme_general', 'url' ),

		// Header.
		array( 'header_search_enabled', __( 'Show Search', 'fashion-brand-theme' ), 'fashion_brand_theme_header', 'checkbox' ),
		array( 'header_account_enabled', __( 'Show Account Link', 'fashion-brand-theme' ), 'fashion_brand_theme_header', 'checkbox' ),
		array( 'header_cart_enabled', __( 'Show Cart Link', 'fashion-brand-theme' ), 'fashion_brand_theme_header', 'checkbox' ),
		array( 'header_search_panel_enabled', __( 'Enable Search Panel', 'fashion-brand-theme' ), 'fashion_brand_theme_header', 'checkbox', __( 'Uses the header search panel. If disabled, search links to the site search results page.', 'fashion-brand-theme' ) ),
		array( 'header_mobile_menu_enabled', __( 'Enable Mobile Menu', 'fashion-brand-theme' ), 'fashion_brand_theme_header', 'checkbox', __( 'Collapses primary navigation behind the menu button on smaller viewports.', 'fashion-brand-theme' ) ),
		array( 'header_sticky_enabled', __( 'Enable Sticky Header', 'fashion-brand-theme' ), 'fashion_brand_theme_header', 'checkbox', __( 'Disabled by default to preserve current behavior.', 'fashion-brand-theme' ) ),
		array( 'header_scroll_shadow_enabled', __( 'Enable Header Shadow on Scroll', 'fashion-brand-theme' ), 'fashion_brand_theme_header', 'checkbox', __( 'Disabled by default to preserve current behavior.', 'fashion-brand-theme' ) ),

		// Footer.
		array( 'footer_visible', __( 'Show Footer', 'fashion-brand-theme' ), 'fashion_brand_theme_footer', 'checkbox' ),
		array( 'footer_copyright_enabled', __( 'Show Copyright', 'fashion-brand-theme' ), 'fashion_brand_theme_footer', 'checkbox' ),
		array( 'footer_copyright_text', __( 'Footer Copyright Text', 'fashion-brand-theme' ), 'fashion_brand_theme_footer', 'text', __( 'Optional override. Leave empty to use the default copyright line.', 'fashion-brand-theme' ) ),
		array( 'footer_social_enabled', __( 'Show Social Links', 'fashion-brand-theme' ), 'fashion_brand_theme_footer', 'checkbox' ),
		array( 'footer_menu_enabled', __( 'Show Footer Menu', 'fashion-brand-theme' ), 'fashion_brand_theme_footer', 'checkbox', __( 'Assign a menu to the Footer Menu location under Appearance → Menus.', 'fashion-brand-theme' ) ),
		array( 'cookie_banner_enabled', __( 'Enable Cookie Consent Banner', 'fashion-brand-theme' ), 'fashion_brand_theme_footer', 'checkbox', __( 'Shows the GDPR cookie bar until the visitor accepts, rejects, or saves preferences. Strictly necessary cookies are unaffected.', 'fashion-brand-theme' ) ),

		// Homepage visibility.
		array( 'homepage_hero_enabled', __( 'Show Hero', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'checkbox' ),
		array( 'homepage_philosophy_enabled', __( 'Show Philosophy', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'checkbox' ),
		array( 'homepage_categories_enabled', __( 'Show Categories', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'checkbox' ),
		array( 'homepage_collection_enabled', __( 'Show Featured Collection', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'checkbox' ),
		array( 'homepage_guides_enabled', __( 'Show Guides', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'checkbox' ),
		array( 'homepage_cta_enabled', __( 'Show Closing CTA', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'checkbox' ),

		// Homepage hero content.
		array( 'homepage_hero_eyebrow', __( 'Hero Eyebrow', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'text' ),
		array( 'homepage_hero_heading', __( 'Hero Heading', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'text' ),
		array( 'homepage_hero_text', __( 'Hero Supporting Text', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'textarea' ),
		array( 'homepage_hero_cta_label', __( 'Hero Primary CTA Label', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'text' ),
		array( 'homepage_hero_cta_url', __( 'Hero Primary CTA URL', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'url' ),

		// Homepage closing CTA content.
		array( 'homepage_cta_heading', __( 'Closing CTA Heading', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'text' ),
		array( 'homepage_cta_text', __( 'Closing CTA Supporting Text', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'textarea' ),
		array( 'homepage_cta_primary_label', __( 'Closing CTA Primary Label', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'text' ),
		array( 'homepage_cta_primary_url', __( 'Closing CTA Primary URL', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'url' ),
		array( 'homepage_cta_secondary_label', __( 'Closing CTA Secondary Label', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'text' ),
		array( 'homepage_cta_secondary_url', __( 'Closing CTA Secondary URL', 'fashion-brand-theme' ), 'fashion_brand_theme_homepage', 'url' ),

		// Shop presentation.
		array( 'shop_grid_columns', __( 'Product Grid Columns', 'fashion-brand-theme' ), 'fashion_brand_theme_shop', 'number', __( 'Applied when WooCommerce is installed. Allowed range: 2–4.', 'fashion-brand-theme' ) ),
		array( 'shop_products_per_page', __( 'Products Per Page', 'fashion-brand-theme' ), 'fashion_brand_theme_shop', 'number', __( 'Applied when WooCommerce is installed. Allowed range: 1–48.', 'fashion-brand-theme' ) ),
		array( 'shop_show_price', __( 'Show Product Price', 'fashion-brand-theme' ), 'fashion_brand_theme_shop', 'checkbox' ),
		array( 'shop_show_excerpt', __( 'Show Product Excerpt', 'fashion-brand-theme' ), 'fashion_brand_theme_shop', 'checkbox' ),
		array( 'shop_show_category', __( 'Show Product Category', 'fashion-brand-theme' ), 'fashion_brand_theme_shop', 'checkbox' ),
		array( 'shop_show_badges', __( 'Show Product Badges', 'fashion-brand-theme' ), 'fashion_brand_theme_shop', 'checkbox' ),

		// Social.
		array( 'social_enabled', __( 'Enable Social Links', 'fashion-brand-theme' ), 'fashion_brand_theme_social', 'checkbox' ),
		array( 'social_open_new_tab', __( 'Open Social Links in a New Tab', 'fashion-brand-theme' ), 'fashion_brand_theme_social', 'checkbox' ),
		array( 'social_instagram', __( 'Instagram URL', 'fashion-brand-theme' ), 'fashion_brand_theme_social', 'url' ),
		array( 'social_facebook', __( 'Facebook URL', 'fashion-brand-theme' ), 'fashion_brand_theme_social', 'url' ),
		array( 'social_pinterest', __( 'Pinterest URL', 'fashion-brand-theme' ), 'fashion_brand_theme_social', 'url' ),
		array( 'social_tiktok', __( 'TikTok URL', 'fashion-brand-theme' ), 'fashion_brand_theme_social', 'url' ),
		array( 'social_linkedin', __( 'LinkedIn URL', 'fashion-brand-theme' ), 'fashion_brand_theme_social', 'url' ),
		array( 'social_youtube', __( 'YouTube URL', 'fashion-brand-theme' ), 'fashion_brand_theme_social', 'url' ),
	);
}

/**
 * Normalized field map keyed by setting key.
 *
 * @return array<string, array{key:string,label:string,section:string,type:string,description:string,tab:string}>
 */
function fashion_brand_theme_get_settings_fields_map() {
	static $map = null;

	if ( null !== $map ) {
		return $map;
	}

	$map      = array();
	$sections = fashion_brand_theme_get_settings_tab_sections();

	foreach ( fashion_brand_theme_get_settings_fields_definition() as $field ) {
		$section = $field[2];
		$tab     = 'general';

		foreach ( $sections as $tab_id => $section_ids ) {
			if ( in_array( $section, $section_ids, true ) ) {
				$tab = $tab_id;
				break;
			}
		}

		$map[ $field[0] ] = array(
			'key'         => $field[0],
			'label'       => $field[1],
			'section'     => $section,
			'type'        => $field[3],
			'description' => $field[4] ?? '',
			'tab'         => $tab,
		);
	}

	return $map;
}

/**
 * Register individual settings fields.
 *
 * @return void
 */
function fashion_brand_theme_register_settings_fields() {
	foreach ( fashion_brand_theme_get_settings_fields_definition() as $field ) {
		$callback = 'fashion_brand_theme_render_text_field';

		if ( 'checkbox' === $field[3] ) {
			$callback = 'fashion_brand_theme_render_checkbox_field';
		} elseif ( 'textarea' === $field[3] ) {
			$callback = 'fashion_brand_theme_render_textarea_field';
		} elseif ( 'url' === $field[3] ) {
			$callback = 'fashion_brand_theme_render_url_field';
		} elseif ( 'number' === $field[3] ) {
			$callback = 'fashion_brand_theme_render_number_field';
		}

		add_settings_field(
			$field[0],
			$field[1],
			$callback,
			'fashion-brand-theme',
			$field[2],
			array(
				'key'         => $field[0],
				'description' => $field[4] ?? '',
			)
		);
	}
}
