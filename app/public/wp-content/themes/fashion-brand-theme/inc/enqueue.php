<?php
/**
 * Enqueue scripts and styles.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue front-end assets.
 */
function fashion_brand_theme_enqueue_assets() {
	$stylesheets = array(
		'tokens/colors',
		'tokens/typography',
		'tokens/spacing',
		'tokens/layout',
		'tokens/effects',
		'base/fonts',
		'base/reset',
		'base/typography',
		'base/buttons',
		'base/utilities',
		'components/header',
		'components/announcement-bar',
		'components/homepage',
		'components/collections',
		'components/shop',
		'components/product',
		'components/cart',
		'components/cart-drawer',
		'components/cookie-consent',
		'components/whatsapp-button',
		'components/checkout',
	);

	$previous_handle = null;

	foreach ( $stylesheets as $stylesheet ) {
		$handle = 'fashion-brand-theme-' . str_replace( '/', '-', $stylesheet );

		wp_enqueue_style(
			$handle,
			FASHION_BRAND_THEME_URI . '/assets/css/' . $stylesheet . '.css',
			null === $previous_handle ? array() : array( $previous_handle ),
			FASHION_BRAND_THEME_VERSION
		);

		$previous_handle = $handle;
	}

	wp_enqueue_style(
		'fashion-brand-theme-main',
		FASHION_BRAND_THEME_URI . '/assets/css/main.css',
		array( $previous_handle ),
		FASHION_BRAND_THEME_VERSION
	);

	$main_deps = array();

	if ( class_exists( 'WooCommerce' ) ) {
		$main_deps[] = 'jquery';
	}

	wp_enqueue_script(
		'fashion-brand-theme-main',
		FASHION_BRAND_THEME_URI . '/assets/js/main.js',
		$main_deps,
		FASHION_BRAND_THEME_VERSION,
		true
	);

	wp_localize_script(
		'fashion-brand-theme-main',
		'fashionBrandThemeWishlist',
		fashion_brand_theme_wishlist_script_data()
	);

	if ( class_exists( 'WooCommerce' ) && function_exists( 'fashion_brand_theme_should_render_cart_drawer' ) && fashion_brand_theme_should_render_cart_drawer() ) {
		wp_localize_script(
			'fashion-brand-theme-main',
			'fashionBrandThemeCartDrawer',
			array(
				'wcAjaxUrl' => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : '',
			)
		);
	}

	if ( function_exists( 'fashion_brand_theme_should_render_cookie_consent' ) && fashion_brand_theme_should_render_cookie_consent() ) {
		wp_enqueue_script(
			'fashion-brand-theme-cookie-consent',
			FASHION_BRAND_THEME_URI . '/assets/js/cookie-consent.js',
			array(),
			FASHION_BRAND_THEME_VERSION,
			true
		);

		wp_localize_script(
			'fashion-brand-theme-cookie-consent',
			'fashionBrandThemeCookieConsent',
			fashion_brand_theme_cookie_consent_script_data()
		);
	}

	if ( function_exists( 'fashion_brand_theme_should_render_whatsapp_button' ) && fashion_brand_theme_should_render_whatsapp_button() ) {
		wp_enqueue_script(
			'fashion-brand-theme-whatsapp-button',
			FASHION_BRAND_THEME_URI . '/assets/js/whatsapp-button.js',
			array(),
			FASHION_BRAND_THEME_VERSION,
			true
		);
	}

	wp_enqueue_script(
		'fashion-brand-theme-live-search',
		FASHION_BRAND_THEME_URI . '/assets/js/live-search.js',
		array(),
		FASHION_BRAND_THEME_VERSION,
		true
	);

	wp_localize_script(
		'fashion-brand-theme-live-search',
		'fashionBrandThemeLiveSearch',
		array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'fashion_brand_theme_live_search' ),
			'minChars'  => 2,
			'searchUrl' => fashion_brand_theme_get_search_url(),
			'i18n'      => array(
				'result'  => __( 'result', 'fashion-brand-theme' ),
				'results' => __( 'results', 'fashion-brand-theme' ),
				'viewAll' => __( 'View all results', 'fashion-brand-theme' ),
			),
		)
	);

	if ( is_front_page() ) {
		wp_enqueue_script(
			'fashion-brand-theme-homepage-cinematic',
			FASHION_BRAND_THEME_URI . '/assets/js/homepage-cinematic.js',
			array(),
			FASHION_BRAND_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'fashion-brand-theme-custom-cursor',
			FASHION_BRAND_THEME_URI . '/assets/js/custom-cursor.js',
			array(),
			FASHION_BRAND_THEME_VERSION,
			true
		);
	}

	$is_shop = function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() || is_product() );

	if ( $is_shop ) {
		wp_enqueue_script(
			'fashion-brand-theme-shop',
			FASHION_BRAND_THEME_URI . '/assets/js/shop.js',
			array(),
			FASHION_BRAND_THEME_VERSION,
			true
		);

		wp_localize_script(
			'fashion-brand-theme-shop',
			'fashionBrandThemeShop',
			array(
				'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( 'fashion_brand_theme_shop' ),
				'currencySymbol' => function_exists( 'get_woocommerce_currency_symbol' ) ? html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' ) : '€',
			)
		);

		wp_localize_script(
			'fashion-brand-theme-shop',
			'fashionBrandThemeWishlist',
			fashion_brand_theme_wishlist_script_data()
		);
	}

	if ( function_exists( 'is_product' ) && is_product() ) {
		wp_enqueue_script(
			'fashion-brand-theme-product',
			FASHION_BRAND_THEME_URI . '/assets/js/product.js',
			array( 'jquery', 'wc-add-to-cart-variation' ),
			FASHION_BRAND_THEME_VERSION,
			true
		);

		wp_localize_script(
			'fashion-brand-theme-product',
			'fashionBrandThemeProduct',
			array(
				'colorMap' => fashion_brand_theme_color_swatch_map(),
			)
		);
	}

	if ( is_page( 'contact' ) ) {
		wp_enqueue_style(
			'fashion-brand-theme-contact',
			FASHION_BRAND_THEME_URI . '/assets/css/components/contact.css',
			array( 'fashion-brand-theme-main' ),
			FASHION_BRAND_THEME_VERSION
		);

		wp_enqueue_script(
			'fashion-brand-theme-contact-phone',
			FASHION_BRAND_THEME_URI . '/assets/js/contact-phone.js',
			array(),
			FASHION_BRAND_THEME_VERSION,
			true
		);

		wp_enqueue_script(
			'fashion-brand-theme-contact-hero',
			FASHION_BRAND_THEME_URI . '/assets/js/contact-hero.js',
			array(),
			FASHION_BRAND_THEME_VERSION,
			true
		);
	}

	if ( is_page( 'about' ) ) {
		wp_enqueue_style(
			'fashion-brand-theme-about',
			FASHION_BRAND_THEME_URI . '/assets/css/pages/about.css',
			array( 'fashion-brand-theme-main' ),
			FASHION_BRAND_THEME_VERSION
		);

		wp_enqueue_script(
			'fashion-brand-theme-about-hero',
			FASHION_BRAND_THEME_URI . '/assets/js/about-hero.js',
			array(),
			FASHION_BRAND_THEME_VERSION,
			true
		);
	}

	if ( is_page( 'faq' ) ) {
		wp_enqueue_style(
			'fashion-brand-theme-faq',
			FASHION_BRAND_THEME_URI . '/assets/css/pages/faq.css',
			array( 'fashion-brand-theme-main' ),
			FASHION_BRAND_THEME_VERSION
		);
	}

	if ( is_page_template( 'page-legal.php' ) ) {
		wp_enqueue_style(
			'fashion-brand-theme-legal',
			FASHION_BRAND_THEME_URI . '/assets/css/pages/legal.css',
			array( 'fashion-brand-theme-main' ),
			FASHION_BRAND_THEME_VERSION
		);
	}

	if ( is_404() ) {
		wp_enqueue_style(
			'fashion-brand-theme-404',
			FASHION_BRAND_THEME_URI . '/assets/css/pages/404.css',
			array( 'fashion-brand-theme-main' ),
			FASHION_BRAND_THEME_VERSION
		);
	}

	if ( is_search() ) {
		wp_enqueue_style(
			'fashion-brand-theme-search',
			FASHION_BRAND_THEME_URI . '/assets/css/pages/search.css',
			array( 'fashion-brand-theme-main' ),
			FASHION_BRAND_THEME_VERSION
		);
	}

	$load_blog_css = ! is_search()
		&& (
			is_home()
			|| is_singular( 'post' )
			|| is_category()
			|| is_tag()
			|| is_author()
			|| is_date()
		);

	if ( $load_blog_css ) {
		wp_enqueue_style(
			'fashion-brand-theme-blog',
			FASHION_BRAND_THEME_URI . '/assets/css/pages/blog.css',
			array( 'fashion-brand-theme-main' ),
			FASHION_BRAND_THEME_VERSION
		);
	}

	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		wp_enqueue_style(
			'fashion-brand-theme-account',
			FASHION_BRAND_THEME_URI . '/assets/css/pages/account.css',
			array( 'fashion-brand-theme-main' ),
			FASHION_BRAND_THEME_VERSION
		);

		wp_enqueue_script(
			'fashion-brand-theme-account',
			FASHION_BRAND_THEME_URI . '/assets/js/account.js',
			array(),
			FASHION_BRAND_THEME_VERSION,
			true
		);

		wp_localize_script(
			'fashion-brand-theme-account',
			'fashionBrandThemeAccount',
			array(
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'fashion_brand_theme_avatar_upload' ),
				'invalidType' => __( 'Please choose a JPG, PNG, GIF or WebP image.', 'fashion-brand-theme' ),
				'uploadError' => __( 'Upload failed. Please try again.', 'fashion-brand-theme' ),
			)
		);
	}

	$load_wishlist_page = is_page( 'wishlist' )
		|| ( function_exists( 'is_account_page' ) && is_account_page() && function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'wishlist' ) );

	if ( $load_wishlist_page ) {
		$wishlist_deps = array( 'fashion-brand-theme-main' );

		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			$wishlist_deps[] = 'fashion-brand-theme-account';
		}

		wp_enqueue_style(
			'fashion-brand-theme-wishlist',
			FASHION_BRAND_THEME_URI . '/assets/css/pages/wishlist.css',
			$wishlist_deps,
			FASHION_BRAND_THEME_VERSION
		);

		wp_enqueue_script(
			'fashion-brand-theme-wishlist-page',
			FASHION_BRAND_THEME_URI . '/assets/js/wishlist-page.js',
			array( 'fashion-brand-theme-main' ),
			FASHION_BRAND_THEME_VERSION,
			true
		);

		wp_localize_script(
			'fashion-brand-theme-wishlist-page',
			'fashionBrandThemeWishlist',
			fashion_brand_theme_wishlist_script_data()
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fashion_brand_theme_enqueue_assets' );

/**
 * Remove WooCommerce's bundled default frontend stylesheets.
 *
 * The theme hand-styles WooCommerce surfaces; keep WC's general, layout,
 * and small-screen CSS from loading site-wide.
 *
 * @return void
 */
function fashion_brand_theme_dequeue_woocommerce_styles() {
	wp_dequeue_style( 'woocommerce-general' );
	wp_dequeue_style( 'woocommerce-layout' );
	wp_dequeue_style( 'woocommerce-smallscreen' );
}
add_action( 'wp_enqueue_scripts', 'fashion_brand_theme_dequeue_woocommerce_styles', 20 );

/**
 * Ensure WooCommerce cart fragments + AJAX add-to-cart scripts load site-wide
 * so the mini-cart drawer stays in sync without a page reload.
 *
 * @return void
 */
function fashion_brand_theme_enqueue_cart_drawer_scripts() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	if ( ! function_exists( 'fashion_brand_theme_should_render_cart_drawer' ) || ! fashion_brand_theme_should_render_cart_drawer() ) {
		return;
	}

	wp_enqueue_script( 'wc-add-to-cart' );
	wp_enqueue_script( 'wc-cart-fragments' );
}
add_action( 'wp_enqueue_scripts', 'fashion_brand_theme_enqueue_cart_drawer_scripts', 25 );

/**
 * Output production favicon links when no Customizer site icon is set.
 *
 * @return void
 */
function fashion_brand_theme_brand_icons() {
	if ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
		return;
	}

	$ico   = fashion_brand_theme_get_brand_asset_uri( 'favicon/wren-wold-favicon.ico' );
	$png   = fashion_brand_theme_get_brand_asset_uri( 'favicon/wren-wold-favicon-32.png' );
	$apple = fashion_brand_theme_get_brand_asset_uri( 'favicon/wren-wold-favicon-180.png' );

	if ( $ico ) {
		printf( '<link rel="icon" href="%s" sizes="any">' . "\n", esc_url( $ico ) );
	}

	if ( $png ) {
		printf( '<link rel="icon" type="image/png" href="%s" sizes="32x32">' . "\n", esc_url( $png ) );
	}

	if ( $apple ) {
		printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( $apple ) );
	}
}
add_action( 'wp_head', 'fashion_brand_theme_brand_icons', 2 );
