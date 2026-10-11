<?php
/**
 * Transactional email branding + customer email-verification bypass.
 *
 * WooCommerce 11+ ships CustomerEmailVerification which prompts new customers
 * to "Confirm your email address" on My Account → Orders. This theme marks
 * customers verified on registration so accounts are usable immediately, while
 * still sending the normal new-account / order emails.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Absolute URL for the white brand logo used in email headers.
 *
 * Prefer a PNG when present (broader client support); fall back to SVG.
 *
 * @return string
 */
function fashion_brand_theme_get_email_logo_url() {
	$candidates = array(
		'logo/wren-wold-logo-white.png',
		'logo/wren-wold-wordmark-white.png',
		'logo/wren-wold-logo-white.svg',
		'logo/wren-wold-wordmark-white.svg',
	);

	foreach ( $candidates as $relative ) {
		$path = FASHION_BRAND_THEME_DIR . '/assets/brand/' . $relative;
		if ( is_readable( $path ) ) {
			return FASHION_BRAND_THEME_URI . '/assets/brand/' . $relative;
		}
	}

	return '';
}

/**
 * Apply brand colors / logo / footer options for WooCommerce emails.
 *
 * @return void
 */
function fashion_brand_theme_configure_email_options() {
	if ( ! function_exists( 'WC' ) ) {
		return;
	}

	$defaults = array(
		'woocommerce_email_from_name'            => 'WREN WOLD',
		'woocommerce_email_from_address'         => 'hello@wrenwold.com',
		'woocommerce_email_header_image'         => fashion_brand_theme_get_email_logo_url(),
		'woocommerce_email_header_image_width'   => '160',
		'woocommerce_email_header_alignment'     => 'center',
		'woocommerce_email_base_color'           => '#3f5348',
		'woocommerce_email_background_color'     => '#f6f3ec',
		'woocommerce_email_body_background_color'=> '#fafaf8',
		'woocommerce_email_text_color'           => '#15181a',
		'woocommerce_email_footer_text_color'    => '#c8c4bc',
		'woocommerce_email_footer_text'          => '',
	);

	foreach ( $defaults as $option => $value ) {
		$current = get_option( $option, null );

		// Seed empty / missing options; always keep header image on the theme logo.
		if ( null === $current || '' === $current || 'woocommerce_email_header_image' === $option || 'woocommerce_email_footer_text' === $option ) {
			update_option( $option, $value );
		}
	}
}
add_action( 'after_setup_theme', 'fashion_brand_theme_configure_email_options', 30 );

/**
 * Mark a customer email as verified (WC 11+ CustomerEmailVerification).
 *
 * @param int $user_id User ID.
 * @return void
 */
function fashion_brand_theme_mark_customer_email_verified( $user_id ) {
	$user_id = absint( $user_id );

	if ( ! $user_id || ! function_exists( 'wc_get_container' ) ) {
		return;
	}

	try {
		$service = wc_get_container()->get( \Automattic\WooCommerce\Internal\CustomerEmailVerification\EmailVerificationService::class );
		if ( $service && method_exists( $service, 'mark_verified' ) ) {
			$service->mark_verified( $user_id );
		}
	} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		// Service unavailable — ignore.
	} catch ( Error $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		// Class missing on older WC — ignore.
	}
}

/**
 * Auto-verify on successful customer registration.
 *
 * @param int $customer_id New customer user ID.
 * @return void
 */
function fashion_brand_theme_verify_new_customer_email( $customer_id ) {
	fashion_brand_theme_mark_customer_email_verified( $customer_id );
}
add_action( 'woocommerce_created_customer', 'fashion_brand_theme_verify_new_customer_email', 20 );

/**
 * Remove the My Account “Confirm your email” prompt UI.
 *
 * @return void
 */
function fashion_brand_theme_disable_email_verification_prompt() {
	if ( ! function_exists( 'wc_get_container' ) ) {
		return;
	}

	try {
		$controller = wc_get_container()->get( \Automattic\WooCommerce\Internal\CustomerEmailVerification\VerificationController::class );
	} catch ( Exception $e ) {
		return;
	} catch ( Error $e ) {
		return;
	}

	if ( ! $controller ) {
		return;
	}

	remove_action( 'woocommerce_before_account_orders', array( $controller, 'render_prompt' ) );
	remove_action( 'woocommerce_before_account_orders', array( $controller, 'print_result_notice' ), 5 );

	// Still allow verify-links from old emails to complete, but never surface the send/resend CTA.
}
add_action( 'wp_loaded', 'fashion_brand_theme_disable_email_verification_prompt', 20 );

/**
 * Brand footer text: socials + copyright + contact — no physical address / KVK / tax.
 *
 * @param string   $text  Footer text.
 * @param WC_Email $email Email object.
 * @return string
 */
function fashion_brand_theme_email_footer_text( $text, $email = null ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	$year        = gmdate( 'Y' );
	$contact_url = function_exists( 'fashion_brand_theme_get_page_url' )
		? fashion_brand_theme_get_page_url( 'contact' )
		: home_url( '/contact/' );

	$lines   = array();
	$links   = function_exists( 'fashion_brand_theme_get_social_links' ) ? fashion_brand_theme_get_social_links() : array();
	$labels  = function_exists( 'fashion_brand_theme_get_social_platforms' ) ? fashion_brand_theme_get_social_platforms() : array();

	if ( ! empty( $links ) ) {
		$parts = array();
		foreach ( $links as $platform => $url ) {
			$label   = isset( $labels[ $platform ] ) ? $labels[ $platform ] : ucfirst( $platform );
			$parts[] = '<a href="' . esc_url( $url ) . '" style="color:#c8c4bc;text-decoration:none;margin:0 8px;">' . esc_html( $label ) . '</a>';
		}
		$lines[] = implode( '', $parts );
	}

	$lines[] = esc_html(
		sprintf(
			/* translators: %s: year */
			__( '© %s WREN WOLD', 'fashion-brand-theme' ),
			$year
		)
	);

	$lines[] = '<a href="' . esc_url( $contact_url ) . '" style="color:#c8c4bc;text-decoration:underline;">hello@wrenwold.com</a>';

	return implode( '<br />', $lines );
}
add_filter( 'woocommerce_email_footer_text', 'fashion_brand_theme_email_footer_text', 20, 2 );

/**
 * Force header image to the theme white logo.
 *
 * @param string $image Header image URL.
 * @return string
 */
function fashion_brand_theme_email_header_image( $image ) {
	$logo = fashion_brand_theme_get_email_logo_url();
	return $logo ? $logo : $image;
}
add_filter( 'woocommerce_email_header_image', 'fashion_brand_theme_email_header_image', 20 );
