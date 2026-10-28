<?php
/**
 * GDPR cookie consent — helpers, storage, and render gate.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Cookie name (first-party, readable by PHP). */
define( 'FASHION_BRAND_THEME_COOKIE_CONSENT_NAME', 'wrenwold_cookie_consent' );

/**
 * Policy text version. Bump when the Privacy Policy consent copy meaningfully changes
 * so returning visitors are asked again.
 */
define( 'FASHION_BRAND_THEME_COOKIE_CONSENT_VERSION', 1 );

/** Consent lifetime in seconds (12 months). */
define( 'FASHION_BRAND_THEME_COOKIE_CONSENT_TTL', YEAR_IN_SECONDS );

/**
 * Whether the cookie banner should render on the front end.
 *
 * @return bool
 */
function fashion_brand_theme_should_render_cookie_consent() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return false;
	}

	if ( ! fashion_brand_theme_is_setting_enabled( 'cookie_banner_enabled' ) ) {
		return false;
	}

	// Valid, current consent already stored — no banner.
	if ( fashion_brand_theme_get_cookie_consent() ) {
		return false;
	}

	return true;
}

/**
 * Parse and validate the stored consent cookie.
 *
 * @return array{necessary:bool,analytics:bool,marketing:bool,ts:int,v:int}|null
 */
function fashion_brand_theme_get_cookie_consent() {
	if ( empty( $_COOKIE[ FASHION_BRAND_THEME_COOKIE_CONSENT_NAME ] ) ) {
		return null;
	}

	$raw = wp_unslash( $_COOKIE[ FASHION_BRAND_THEME_COOKIE_CONSENT_NAME ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$raw = rawurldecode( $raw );
	$data = json_decode( $raw, true );

	if ( ! is_array( $data ) ) {
		return null;
	}

	$version = isset( $data['v'] ) ? (int) $data['v'] : 0;
	$ts      = isset( $data['ts'] ) ? (int) $data['ts'] : 0;

	if ( $version < (int) FASHION_BRAND_THEME_COOKIE_CONSENT_VERSION ) {
		return null;
	}

	if ( $ts <= 0 || ( time() - $ts ) > (int) FASHION_BRAND_THEME_COOKIE_CONSENT_TTL ) {
		return null;
	}

	return array(
		'necessary' => true,
		'analytics' => ! empty( $data['analytics'] ),
		'marketing' => ! empty( $data['marketing'] ),
		'ts'        => $ts,
		'v'         => $version,
	);
}

/**
 * Whether the visitor has granted consent for a category.
 *
 * Necessary is always treated as granted. Analytics/marketing require an explicit yes.
 * Use when wrapping future third-party scripts:
 *
 *   if ( fashion_brand_theme_has_cookie_consent( 'analytics' ) ) { … }
 *
 * @param string $category Category slug: necessary|analytics|marketing.
 * @return bool
 */
function fashion_brand_theme_has_cookie_consent( $category ) {
	$category = sanitize_key( (string) $category );

	if ( 'necessary' === $category ) {
		return true;
	}

	$consent = fashion_brand_theme_get_cookie_consent();

	if ( ! $consent ) {
		return false;
	}

	return ! empty( $consent[ $category ] );
}

/**
 * Privacy Policy URL for the banner link.
 *
 * @return string
 */
function fashion_brand_theme_get_cookie_consent_privacy_url() {
	$privacy_page = get_page_by_path( 'privacy-policy' );

	if ( $privacy_page instanceof WP_Post ) {
		return get_permalink( $privacy_page );
	}

	$url = get_privacy_policy_url();

	return $url ? $url : home_url( '/' );
}

/**
 * Script localization for cookie-consent.js.
 *
 * @return array<string, mixed>
 */
function fashion_brand_theme_cookie_consent_script_data() {
	return array(
		'cookieName' => FASHION_BRAND_THEME_COOKIE_CONSENT_NAME,
		'version'    => (int) FASHION_BRAND_THEME_COOKIE_CONSENT_VERSION,
		'maxAgeDays' => (int) round( FASHION_BRAND_THEME_COOKIE_CONSENT_TTL / DAY_IN_SECONDS ),
		'privacyUrl' => fashion_brand_theme_get_cookie_consent_privacy_url(),
	);
}
