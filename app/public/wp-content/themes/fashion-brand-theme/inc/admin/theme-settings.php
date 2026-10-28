<?php
/**
 * Theme Settings admin page and field renderers (dashboard UI).
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register admin menu pages.
 *
 * @return void
 */
function fashion_brand_theme_register_admin_menu() {
	add_menu_page(
		__( 'Fashion Brand', 'fashion-brand-theme' ),
		__( 'Fashion Brand', 'fashion-brand-theme' ),
		'manage_options',
		'fashion-brand-theme',
		'fashion_brand_theme_render_settings_page',
		'dashicons-art',
		59
	);

	add_submenu_page(
		'fashion-brand-theme',
		__( 'Theme Settings', 'fashion-brand-theme' ),
		__( 'Theme Settings', 'fashion-brand-theme' ),
		'manage_options',
		'fashion-brand-theme',
		'fashion_brand_theme_render_settings_page'
	);
}
add_action( 'admin_menu', 'fashion_brand_theme_register_admin_menu' );

/**
 * Enqueue Theme Settings dashboard assets.
 *
 * @param string $hook_suffix Current admin page hook.
 * @return void
 */
function fashion_brand_theme_admin_assets( $hook_suffix ) {
	if ( 'toplevel_page_fashion-brand-theme' !== $hook_suffix ) {
		return;
	}

	wp_enqueue_style(
		'fashion-brand-theme-admin-settings',
		FASHION_BRAND_THEME_URI . '/assets/css/admin/theme-settings.css',
		array(),
		FASHION_BRAND_THEME_VERSION
	);

	wp_enqueue_script(
		'fashion-brand-theme-admin-settings',
		FASHION_BRAND_THEME_URI . '/assets/js/admin/theme-settings.js',
		array(),
		FASHION_BRAND_THEME_VERSION,
		true
	);

	$overview = array();
	foreach ( fashion_brand_theme_get_settings_fields_map() as $field ) {
		if ( 'checkbox' !== $field['type'] ) {
			continue;
		}

		$overview[ $field['key'] ] = array(
			'tab'         => $field['tab'],
			'label'       => $field['label'],
			'description' => $field['description'] ? $field['description'] : $field['label'],
		);
	}

	wp_localize_script(
		'fashion-brand-theme-admin-settings',
		'fashionBrandThemeSettings',
		array(
			'overview' => $overview,
			'i18n'     => array(
				'settings' => __( 'Settings →', 'fashion-brand-theme' ),
				'on'       => __( 'On', 'fashion-brand-theme' ),
				'off'      => __( 'Off', 'fashion-brand-theme' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'fashion_brand_theme_admin_assets' );

/**
 * Card groups per settings tab (visual grouping only — field keys unchanged).
 *
 * @return array<string, array<int, array<string, mixed>>>
 */
function fashion_brand_theme_get_settings_card_groups() {
	return array(
		'general'  => array(
			array(
				'id'          => 'branding',
				'title'       => __( 'Branding', 'fashion-brand-theme' ),
				'description' => __( 'Fallback label when no custom logo is set.', 'fashion-brand-theme' ),
				'icon'        => 'brand',
				'fields'      => array( 'display_label' ),
			),
			array(
				'id'          => 'announcement',
				'title'       => __( 'Announcement bar', 'fashion-brand-theme' ),
				'description' => __( 'Optional message above the site header.', 'fashion-brand-theme' ),
				'icon'        => 'megaphone',
				'master'      => 'announcement_enabled',
				'fields'      => array(
					'announcement_enabled',
					'announcement_text',
					'announcement_link_enabled',
					'announcement_link_label',
					'announcement_link_url',
				),
			),
		),
		'header'   => array(
			array(
				'id'          => 'header-utility',
				'title'       => __( 'Utility links', 'fashion-brand-theme' ),
				'description' => __( 'Search, account, and cart controls in the header.', 'fashion-brand-theme' ),
				'icon'        => 'header',
				'fields'      => array( 'header_search_enabled', 'header_account_enabled', 'header_cart_enabled' ),
			),
			array(
				'id'          => 'header-search-panel',
				'title'       => __( 'Search panel', 'fashion-brand-theme' ),
				'description' => __( 'Uses the header search panel. If disabled, search links to results.', 'fashion-brand-theme' ),
				'icon'        => 'search',
				'master'      => 'header_search_panel_enabled',
				'fields'      => array( 'header_search_panel_enabled' ),
			),
			array(
				'id'          => 'header-mobile-menu',
				'title'       => __( 'Mobile menu', 'fashion-brand-theme' ),
				'description' => __( 'Collapses primary navigation behind the menu button on smaller viewports.', 'fashion-brand-theme' ),
				'icon'        => 'menu',
				'master'      => 'header_mobile_menu_enabled',
				'fields'      => array( 'header_mobile_menu_enabled' ),
			),
			array(
				'id'          => 'header-sticky',
				'title'       => __( 'Sticky header', 'fashion-brand-theme' ),
				'description' => __( 'Keeps the header fixed while scrolling. Disabled by default.', 'fashion-brand-theme' ),
				'icon'        => 'pin',
				'master'      => 'header_sticky_enabled',
				'fields'      => array( 'header_sticky_enabled' ),
			),
			array(
				'id'          => 'header-shadow',
				'title'       => __( 'Header shadow on scroll', 'fashion-brand-theme' ),
				'description' => __( 'Adds a subtle shadow after the page scrolls. Disabled by default.', 'fashion-brand-theme' ),
				'icon'        => 'shadow',
				'master'      => 'header_scroll_shadow_enabled',
				'fields'      => array( 'header_scroll_shadow_enabled' ),
			),
		),
		'footer'   => array(
			array(
				'id'          => 'footer-visibility',
				'title'       => __( 'Footer visibility', 'fashion-brand-theme' ),
				'description' => __( 'Show or hide the site footer.', 'fashion-brand-theme' ),
				'icon'        => 'footer',
				'master'      => 'footer_visible',
				'fields'      => array( 'footer_visible' ),
			),
			array(
				'id'          => 'footer-copyright',
				'title'       => __( 'Copyright', 'fashion-brand-theme' ),
				'description' => __( 'Copyright line in the footer.', 'fashion-brand-theme' ),
				'icon'        => 'copyright',
				'master'      => 'footer_copyright_enabled',
				'fields'      => array( 'footer_copyright_enabled', 'footer_copyright_text' ),
			),
			array(
				'id'          => 'footer-social',
				'title'       => __( 'Footer social links', 'fashion-brand-theme' ),
				'description' => __( 'Show social icons in the footer when profiles are configured.', 'fashion-brand-theme' ),
				'icon'        => 'share',
				'master'      => 'footer_social_enabled',
				'fields'      => array( 'footer_social_enabled' ),
			),
			array(
				'id'          => 'footer-menu',
				'title'       => __( 'Footer menu', 'fashion-brand-theme' ),
				'description' => __( 'Assign a menu to the Footer Menu location under Appearance → Menus.', 'fashion-brand-theme' ),
				'icon'        => 'menu',
				'master'      => 'footer_menu_enabled',
				'fields'      => array( 'footer_menu_enabled' ),
			),
			array(
				'id'          => 'cookie-banner',
				'title'       => __( 'Cookie consent banner', 'fashion-brand-theme' ),
				'description' => __( 'GDPR cookie bar until the visitor accepts, rejects, or saves preferences.', 'fashion-brand-theme' ),
				'icon'        => 'cookie',
				'master'      => 'cookie_banner_enabled',
				'fields'      => array( 'cookie_banner_enabled' ),
			),
		),
		'homepage' => array(
			array(
				'id'          => 'homepage-sections',
				'title'       => __( 'Homepage sections', 'fashion-brand-theme' ),
				'description' => __( 'Toggle which cinematic sections appear. Order stays code-controlled.', 'fashion-brand-theme' ),
				'icon'        => 'home',
				'fields'      => array(
					'homepage_hero_enabled',
					'homepage_philosophy_enabled',
					'homepage_categories_enabled',
					'homepage_collection_enabled',
					'homepage_guides_enabled',
					'homepage_cta_enabled',
				),
			),
			array(
				'id'          => 'homepage-hero',
				'title'       => __( 'Hero content', 'fashion-brand-theme' ),
				'description' => __( 'Limited copy overrides for the homepage hero.', 'fashion-brand-theme' ),
				'icon'        => 'hero',
				'fields'      => array(
					'homepage_hero_eyebrow',
					'homepage_hero_heading',
					'homepage_hero_text',
					'homepage_hero_cta_label',
					'homepage_hero_cta_url',
				),
			),
			array(
				'id'          => 'homepage-cta',
				'title'       => __( 'Closing CTA content', 'fashion-brand-theme' ),
				'description' => __( 'Limited copy overrides for the closing call-to-action.', 'fashion-brand-theme' ),
				'icon'        => 'cta',
				'fields'      => array(
					'homepage_cta_heading',
					'homepage_cta_text',
					'homepage_cta_primary_label',
					'homepage_cta_primary_url',
					'homepage_cta_secondary_label',
					'homepage_cta_secondary_url',
				),
			),
		),
		'shop'     => array(
			array(
				'id'          => 'shop-layout',
				'title'       => __( 'Catalog layout', 'fashion-brand-theme' ),
				'description' => __( 'Applied when WooCommerce is installed.', 'fashion-brand-theme' ),
				'icon'        => 'shop',
				'fields'      => array( 'shop_grid_columns', 'shop_products_per_page' ),
			),
			array(
				'id'          => 'shop-display',
				'title'       => __( 'Product card display', 'fashion-brand-theme' ),
				'description' => __( 'What appears on product cards in the shop grid.', 'fashion-brand-theme' ),
				'icon'        => 'tag',
				'fields'      => array( 'shop_show_price', 'shop_show_excerpt', 'shop_show_category', 'shop_show_badges' ),
			),
		),
		'contact'  => array(
			array(
				'id'          => 'whatsapp',
				'title'       => __( 'WhatsApp button', 'fashion-brand-theme' ),
				'description' => __( 'Floating contact link to WhatsApp. Requires both the toggle and a phone number.', 'fashion-brand-theme' ),
				'icon'        => 'whatsapp',
				'master'      => 'whatsapp_enabled',
				'fields'      => array(
					'whatsapp_enabled',
					'whatsapp_number',
					'whatsapp_message',
				),
			),
		),
		'company'  => array(
			array(
				'id'          => 'company-details',
				'title'       => __( 'Company details', 'fashion-brand-theme' ),
				'description' => __( 'Show company & contact data on the Contact page, footer, and Schema.org. Edit the fields under Appearance → Customize → Company & Contact.', 'fashion-brand-theme' ),
				'icon'        => 'company',
				'master'      => 'company_details_visible',
				'fields'      => array( 'company_details_visible' ),
			),
		),
		'social'   => array(
			array(
				'id'          => 'social-master',
				'title'       => __( 'Social links', 'fashion-brand-theme' ),
				'description' => __( 'Theme Settings social URLs (separate from Appearance → Customize → Social Media).', 'fashion-brand-theme' ),
				'icon'        => 'share',
				'master'      => 'social_enabled',
				'fields'      => array(
					'social_enabled',
					'social_open_new_tab',
					'social_instagram',
					'social_facebook',
					'social_pinterest',
					'social_tiktok',
					'social_linkedin',
					'social_youtube',
				),
			),
		),
	);
}

/**
 * Tab descriptions for panel headers.
 *
 * @return array<string, string>
 */
function fashion_brand_theme_get_settings_tab_descriptions() {
	return array(
		'overview' => __( 'The status of every toggle at a glance. Switch a feature here, or open its section for related settings.', 'fashion-brand-theme' ),
		'general'  => __( 'Optional announcement messaging and display label fallback.', 'fashion-brand-theme' ),
		'header'   => __( 'Utility header controls. Navigation structure remains code-controlled.', 'fashion-brand-theme' ),
		'footer'   => __( 'Footer visibility and content. Menus are managed under Appearance → Menus.', 'fashion-brand-theme' ),
		'homepage' => __( 'Enable or disable homepage sections and edit limited hero/CTA copy.', 'fashion-brand-theme' ),
		'shop'     => __( 'Presentation-only settings applied when WooCommerce is installed.', 'fashion-brand-theme' ),
		'contact'  => __( 'Simple floating WhatsApp contact link. No AI — just a pre-filled chat to your number.', 'fashion-brand-theme' ),
		'company'  => __( 'Show or hide company & contact details. Enter the data under Appearance → Customize → Company & Contact.', 'fashion-brand-theme' ),
		'social'   => __( 'External profile links for theme output. URLs are sanitized on save.', 'fashion-brand-theme' ),
	);
}

/**
 * Inline SVG icon for admin cards / nav.
 *
 * @param string $name Icon key.
 * @return string
 */
function fashion_brand_theme_settings_icon_svg( $name ) {
	$icons = array(
		'overview'  => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
		'general'   => '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
		'header'    => '<path d="M4 6h16M4 10h16M4 18h16"/><path d="M4 14h8"/>',
		'footer'    => '<path d="M4 6h16v12H4z"/><path d="M4 14h16"/>',
		'homepage'  => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
		'shop'      => '<path d="M6 7h12l1 12H5z"/><path d="M9 7a3 3 0 0 1 6 0"/>',
		'contact'   => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
		'whatsapp'  => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
		'company'   => '<path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-6h6v6"/><path d="M9 10h.01M15 10h.01M9 14h.01M15 14h.01"/>',
		'social'    => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/>',
		'brand'     => '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/>',
		'megaphone' => '<path d="M3 11v2a1 1 0 0 0 1 1h2l5 4V6L6 10H4a1 1 0 0 0-1 1zM15 9a3 3 0 0 1 0 6M18 6a7 7 0 0 1 0 12"/>',
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
		'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'pin'       => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
		'shadow'    => '<path d="M4 18h16M6 14c2-4 4-6 6-6s4 2 6 6"/>',
		'copyright' => '<circle cx="12" cy="12" r="9"/><path d="M15 10.5a3.5 3.5 0 1 0 0 3"/>',
		'share'     => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/>',
		'cookie'    => '<circle cx="12" cy="12" r="9"/><circle cx="9" cy="10" r="1"/><circle cx="14" cy="9" r="1"/><circle cx="11" cy="14" r="1"/><circle cx="15" cy="14" r="1"/>',
		'home'      => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
		'hero'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 14l4-3 3 2 5-4 6 5"/>',
		'cta'       => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'tag'       => '<path d="M20 12l-8 8-9-9V3h8z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
		'external'  => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
	);

	$path = $icons[ $name ] ?? $icons['general'];

	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}

/**
 * Render On/Off badge for a checkbox setting.
 *
 * @param string $key Setting key.
 * @return void
 */
function fashion_brand_theme_render_settings_badge( $key ) {
	$on = fashion_brand_theme_is_setting_enabled( $key );
	printf(
		'<span class="fbt-badge%s" data-badge-for="%s">%s</span>',
		$on ? '' : ' is-off',
		esc_attr( $key ),
		esc_html( $on ? __( 'On', 'fashion-brand-theme' ) : __( 'Off', 'fashion-brand-theme' ) )
	);
}

/**
 * Render a toggle switch bound to a settings checkbox.
 *
 * @param string $key   Setting key.
 * @param string $label Accessible label.
 * @return void
 */
function fashion_brand_theme_render_settings_switch( $key, $label ) {
	$checked = fashion_brand_theme_is_setting_enabled( $key );
	$name    = fashion_brand_theme_settings_field_name( $key );
	?>
	<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="0" />
	<label class="fbt-sw">
		<input
			type="checkbox"
			id="<?php echo esc_attr( $key ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			value="1"
			data-key="<?php echo esc_attr( $key ); ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
			<?php checked( $checked ); ?>
		/>
		<span></span>
	</label>
	<?php
}

/**
 * Render the Theme Settings dashboard page.
 *
 * @return void
 */
function fashion_brand_theme_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$tabs         = fashion_brand_theme_get_settings_tabs();
	$descriptions = fashion_brand_theme_get_settings_tab_descriptions();
	$card_groups  = fashion_brand_theme_get_settings_card_groups();
	$fields_map   = fashion_brand_theme_get_settings_fields_map();
	$theme        = wp_get_theme();
	$version      = (string) $theme->get( 'Version' );
	$saved        = isset( $_GET['settings-updated'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only UI flag.
	$active_tab   = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'overview'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'header-footer' === $active_tab ) {
		$active_tab = 'header';
	}

	$valid_tabs = array_merge( array( 'overview' => true ), $tabs );
	if ( ! isset( $valid_tabs[ $active_tab ] ) ) {
		$active_tab = 'overview';
	}

	$enabled_count = 0;
	foreach ( $fields_map as $field ) {
		if ( 'checkbox' === $field['type'] && fashion_brand_theme_is_setting_enabled( $field['key'] ) ) {
			++$enabled_count;
		}
	}

	$nav_icons = array(
		'overview' => 'overview',
		'general'  => 'general',
		'header'   => 'header',
		'footer'   => 'footer',
		'homepage' => 'homepage',
		'shop'     => 'shop',
		'contact'  => 'contact',
		'company'  => 'company',
		'social'   => 'social',
	);

	$tab_toggle_keys = array();
	foreach ( $fields_map as $field ) {
		if ( 'checkbox' !== $field['type'] ) {
			continue;
		}
		$tab_toggle_keys[ $field['tab'] ][] = $field['key'];
	}
	?>
	<div class="wrap fbt-settings-wrap">
		<hr class="wp-header-end" />
		<div class="fbt-settings" data-saved="<?php echo $saved ? '1' : '0'; ?>" data-active-tab="<?php echo esc_attr( $active_tab ); ?>">
			<header class="fbt-settings__head">
				<div class="fbt-settings__head-copy">
					<p class="fbt-settings__eyebrow"><?php esc_html_e( 'WREN WOLD', 'fashion-brand-theme' ); ?></p>
					<h1><?php esc_html_e( 'Theme Settings', 'fashion-brand-theme' ); ?></h1>
					<p><?php esc_html_e( 'Operational controls for site owners. Design system, layout architecture, responsive behavior, and major visual changes remain code-controlled.', 'fashion-brand-theme' ); ?></p>
				</div>
				<a class="fbt-settings__view" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'View website', 'fashion-brand-theme' ); ?>
					<?php echo fashion_brand_theme_settings_icon_svg( 'external' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</a>
			</header>

			<div class="fbt-settings__body">
				<nav class="fbt-settings__nav" aria-label="<?php esc_attr_e( 'Theme Settings sections', 'fashion-brand-theme' ); ?>">
					<ul role="tablist">
						<li>
							<button type="button" role="tab" data-tab="overview" aria-selected="<?php echo 'overview' === $active_tab ? 'true' : 'false'; ?>">
								<?php echo fashion_brand_theme_settings_icon_svg( 'overview' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php esc_html_e( 'Overview', 'fashion-brand-theme' ); ?>
							</button>
						</li>
						<li class="fbt-settings__nav-sep" aria-hidden="true"></li>
						<?php foreach ( $tabs as $tab_id => $label ) : ?>
							<?php
							$tab_on = false;
							foreach ( $tab_toggle_keys[ $tab_id ] ?? array() as $toggle_key ) {
								if ( fashion_brand_theme_is_setting_enabled( $toggle_key ) ) {
									$tab_on = true;
									break;
								}
							}
							?>
							<li>
								<button type="button" role="tab" data-tab="<?php echo esc_attr( $tab_id ); ?>" aria-selected="<?php echo $active_tab === $tab_id ? 'true' : 'false'; ?>">
									<?php echo fashion_brand_theme_settings_icon_svg( $nav_icons[ $tab_id ] ?? 'general' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<span class="fbt-settings__nav-label"><?php echo esc_html( $label ); ?></span>
									<i class="fbt-settings__dot<?php echo $tab_on ? ' is-on' : ''; ?>" data-for="<?php echo esc_attr( $tab_id ); ?>"></i>
								</button>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>

				<div class="fbt-settings__main">
					<form id="fbt-settings-form" action="options.php" method="post">
						<?php settings_fields( 'fashion_brand_theme_settings_group' ); ?>

						<section class="fbt-settings__panel<?php echo 'overview' === $active_tab ? ' is-active' : ''; ?>" id="fbt-panel-overview" role="tabpanel" data-tab-panel="overview" <?php echo 'overview' === $active_tab ? '' : 'hidden'; ?>>
							<h2 class="fbt-settings__panel-title"><?php esc_html_e( 'Overview', 'fashion-brand-theme' ); ?></h2>
							<p class="fbt-settings__panel-desc"><?php echo esc_html( $descriptions['overview'] ); ?></p>

							<div class="fbt-settings__stats">
								<div class="fbt-settings__stat">
									<strong data-count-on><?php echo esc_html( (string) $enabled_count ); ?></strong>
									<span><?php esc_html_e( 'features active', 'fashion-brand-theme' ); ?></span>
								</div>
								<div class="fbt-settings__stat">
									<strong><?php esc_html_e( 'Live', 'fashion-brand-theme' ); ?></strong>
									<span><?php esc_html_e( 'website visible to visitors', 'fashion-brand-theme' ); ?></span>
								</div>
								<div class="fbt-settings__stat">
									<strong><?php echo esc_html( $version ); ?></strong>
									<span><?php esc_html_e( 'theme version', 'fashion-brand-theme' ); ?></span>
								</div>
							</div>

							<div class="fbt-settings__ov-grid" id="fbt-ov-grid" aria-live="polite"></div>
						</section>

						<?php foreach ( $tabs as $tab_id => $label ) : ?>
							<section
								class="fbt-settings__panel<?php echo $active_tab === $tab_id ? ' is-active' : ''; ?>"
								id="fbt-panel-<?php echo esc_attr( $tab_id ); ?>"
								role="tabpanel"
								data-tab-panel="<?php echo esc_attr( $tab_id ); ?>"
								<?php echo $active_tab === $tab_id ? '' : 'hidden'; ?>
							>
								<h2 class="fbt-settings__panel-title"><?php echo esc_html( $label ); ?></h2>
								<p class="fbt-settings__panel-desc"><?php echo esc_html( $descriptions[ $tab_id ] ?? '' ); ?></p>

								<?php foreach ( $card_groups[ $tab_id ] ?? array() as $group ) : ?>
									<?php
									$master     = $group['master'] ?? '';
									$master_on  = $master ? fashion_brand_theme_is_setting_enabled( $master ) : true;
									$body_keys  = array();
									foreach ( $group['fields'] as $field_key ) {
										if ( $master && $field_key === $master ) {
											continue;
										}
										$body_keys[] = $field_key;
									}
									?>
									<div class="fbt-card" data-card="<?php echo esc_attr( $group['id'] ); ?>"<?php echo $master ? ' data-master="' . esc_attr( $master ) . '"' : ''; ?>>
										<div class="fbt-card__head">
											<div class="fbt-card__icon">
												<?php echo fashion_brand_theme_settings_icon_svg( $group['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											</div>
											<div class="fbt-card__title-wrap">
												<h3 class="fbt-card__title">
													<?php echo esc_html( $group['title'] ); ?>
													<?php
													if ( $master ) {
														fashion_brand_theme_render_settings_badge( $master );
													}
													?>
												</h3>
												<p class="fbt-card__desc"><?php echo esc_html( $group['description'] ); ?></p>
											</div>
											<?php
											if ( $master ) {
												fashion_brand_theme_render_settings_switch( $master, $group['title'] );
											}
											?>
										</div>

										<?php if ( ! empty( $body_keys ) ) : ?>
											<div class="fbt-card__body"<?php echo ( $master && ! $master_on ) ? ' hidden' : ''; ?>>
												<?php foreach ( $body_keys as $field_key ) : ?>
													<?php
													$field = $fields_map[ $field_key ] ?? null;
													if ( ! $field ) {
														continue;
													}
													?>
													<div class="fbt-field" data-field-type="<?php echo esc_attr( $field['type'] ); ?>">
														<label class="fbt-field__label" for="<?php echo esc_attr( $field_key ); ?>">
															<?php echo esc_html( $field['label'] ); ?>
														</label>
														<div class="fbt-field__control">
															<?php
															$args = array(
																'key'         => $field_key,
																'description' => $field['description'],
															);

															if ( 'checkbox' === $field['type'] ) {
																fashion_brand_theme_render_settings_switch( $field_key, $field['label'] );
																if ( $field['description'] ) {
																	echo '<p class="description">' . esc_html( $field['description'] ) . '</p>';
																}
															} elseif ( 'textarea' === $field['type'] ) {
																fashion_brand_theme_render_textarea_field( $args );
															} elseif ( 'url' === $field['type'] ) {
																fashion_brand_theme_render_url_field( $args );
															} elseif ( 'number' === $field['type'] ) {
																fashion_brand_theme_render_number_field( $args );
															} else {
																fashion_brand_theme_render_text_field( $args );
															}
															?>
														</div>
													</div>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</section>
						<?php endforeach; ?>

						<button type="submit" id="fbt-settings-submit" class="button button-primary screen-reader-text"><?php esc_html_e( 'Save settings', 'fashion-brand-theme' ); ?></button>
					</form>
				</div>
			</div>

			<footer class="fbt-settings__credit">
				<span>
					<?php
					echo wp_kses(
						sprintf(
							/* translators: %s: agency website URL */
							__( 'Theme designed & developed by <a href="%s" target="_blank" rel="noopener">OGC NewFinity</a>', 'fashion-brand-theme' ),
							esc_url( 'https://ogcnewfinity.com' )
						),
						array(
							'a' => array(
								'href'   => true,
								'target' => true,
								'rel'    => true,
							),
						)
					);
					?>
				</span>
				<span class="fbt-settings__credit-version">
					<?php
					printf(
						/* translators: %s: theme version */
						esc_html__( 'Version %s', 'fashion-brand-theme' ),
						esc_html( FASHION_BRAND_THEME_VERSION )
					);
					?>
				</span>
			</footer>

			<div class="fbt-settings__savebar" id="fbt-savebar" hidden>
				<p><?php esc_html_e( 'You have unsaved changes', 'fashion-brand-theme' ); ?></p>
				<button type="button" class="button" id="fbt-discard"><?php esc_html_e( 'Discard', 'fashion-brand-theme' ); ?></button>
				<button type="button" class="button button-primary" id="fbt-save"><?php esc_html_e( 'Save settings', 'fashion-brand-theme' ); ?></button>
			</div>

			<div class="fbt-settings__toast" id="fbt-toast" role="status" aria-live="polite">
				<?php esc_html_e( 'Settings saved', 'fashion-brand-theme' ); ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Retrieve the current value for a settings field.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function fashion_brand_theme_get_settings_field_value( $key ) {
	return fashion_brand_theme_get_setting( $key );
}

/**
 * Build the field name for the settings form.
 *
 * @param string $key Setting key.
 * @return string
 */
function fashion_brand_theme_settings_field_name( $key ) {
	return FASHION_BRAND_THEME_SETTINGS_OPTION . '[' . $key . ']';
}

/**
 * Render a text field.
 *
 * @param array<string, string> $args Field arguments.
 * @return void
 */
function fashion_brand_theme_render_text_field( $args ) {
	$key   = $args['key'];
	$value = fashion_brand_theme_get_settings_field_value( $key );
	?>
	<input
		type="text"
		id="<?php echo esc_attr( $key ); ?>"
		name="<?php echo esc_attr( fashion_brand_theme_settings_field_name( $key ) ); ?>"
		value="<?php echo esc_attr( is_string( $value ) ? $value : '' ); ?>"
		class="regular-text"
	/>
	<?php fashion_brand_theme_render_field_description( $args ); ?>
	<?php
}

/**
 * Render a textarea field.
 *
 * @param array<string, string> $args Field arguments.
 * @return void
 */
function fashion_brand_theme_render_textarea_field( $args ) {
	$key   = $args['key'];
	$value = fashion_brand_theme_get_settings_field_value( $key );
	?>
	<textarea
		id="<?php echo esc_attr( $key ); ?>"
		name="<?php echo esc_attr( fashion_brand_theme_settings_field_name( $key ) ); ?>"
		rows="3"
		class="large-text"
	><?php echo esc_textarea( is_string( $value ) ? $value : '' ); ?></textarea>
	<?php fashion_brand_theme_render_field_description( $args ); ?>
	<?php
}

/**
 * Render a URL field.
 *
 * @param array<string, string> $args Field arguments.
 * @return void
 */
function fashion_brand_theme_render_url_field( $args ) {
	$key   = $args['key'];
	$value = fashion_brand_theme_get_settings_field_value( $key );
	?>
	<input
		type="url"
		id="<?php echo esc_attr( $key ); ?>"
		name="<?php echo esc_attr( fashion_brand_theme_settings_field_name( $key ) ); ?>"
		value="<?php echo esc_url( is_string( $value ) ? $value : '' ); ?>"
		class="regular-text code"
		placeholder="https://"
	/>
	<?php fashion_brand_theme_render_field_description( $args ); ?>
	<?php
}

/**
 * Render a number field.
 *
 * @param array<string, string> $args Field arguments.
 * @return void
 */
function fashion_brand_theme_render_number_field( $args ) {
	$key   = $args['key'];
	$value = fashion_brand_theme_get_settings_field_value( $key );
	?>
	<input
		type="number"
		id="<?php echo esc_attr( $key ); ?>"
		name="<?php echo esc_attr( fashion_brand_theme_settings_field_name( $key ) ); ?>"
		value="<?php echo esc_attr( (string) $value ); ?>"
		class="small-text"
		min="1"
		step="1"
	/>
	<?php fashion_brand_theme_render_field_description( $args ); ?>
	<?php
}

/**
 * Render a checkbox field (legacy table UI / Settings API fallback).
 *
 * @param array<string, string> $args Field arguments.
 * @return void
 */
function fashion_brand_theme_render_checkbox_field( $args ) {
	$key = $args['key'];
	fashion_brand_theme_render_settings_switch( $key, $args['key'] );
	fashion_brand_theme_render_field_description( $args );
}

/**
 * Render an optional field description.
 *
 * @param array<string, string> $args Field arguments.
 * @return void
 */
function fashion_brand_theme_render_field_description( $args ) {
	if ( empty( $args['description'] ) ) {
		return;
	}

	echo '<p class="description">' . esc_html( $args['description'] ) . '</p>';
}
