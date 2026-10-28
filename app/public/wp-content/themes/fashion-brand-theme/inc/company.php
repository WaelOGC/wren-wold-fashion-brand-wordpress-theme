<?php
/**
 * Company & Contact details — getters, address formatting, Schema.org.
 *
 * Data is entered in the Customizer; visibility is gated by Theme Settings.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether company details may render on the front end.
 *
 * @return bool
 */
function fashion_brand_theme_company_details_visible() {
	return fashion_brand_theme_is_setting_enabled( 'company_details_visible' );
}

/**
 * Retrieve a trimmed company field, or empty string.
 *
 * @param string $key Setting key (e.g. company_name).
 * @return string
 */
function fashion_brand_theme_get_company_field( $key ) {
	$value = fashion_brand_theme_get_setting( $key );

	if ( ! is_string( $value ) ) {
		return '';
	}

	return trim( $value );
}

/**
 * Build address lines from street / postcode / city (empty parts omitted).
 *
 * @return string[]
 */
function fashion_brand_theme_get_company_address_lines() {
	if ( ! fashion_brand_theme_company_details_visible() ) {
		return array();
	}

	$lines  = array();
	$street = fashion_brand_theme_get_company_field( 'company_street' );

	if ( '' !== $street ) {
		$lines[] = $street;
	}

	$postcode = fashion_brand_theme_get_company_field( 'company_postcode' );
	$city     = fashion_brand_theme_get_company_field( 'company_city' );
	$locality = trim( $postcode . ( '' !== $postcode && '' !== $city ? ' ' : '' ) . $city );

	if ( '' !== $locality ) {
		$lines[] = $locality;
	}

	return $lines;
}

/**
 * tel: href from a phone display string.
 *
 * @param string $phone Phone number.
 * @return string
 */
function fashion_brand_theme_get_company_tel_href( $phone ) {
	$tel = preg_replace( '/[^\d+]/', '', $phone );

	return is_string( $tel ) ? $tel : '';
}

/**
 * Whether any customer-facing company detail (name / address / phone) is available.
 *
 * @return bool
 */
function fashion_brand_theme_has_company_public_details() {
	if ( ! fashion_brand_theme_company_details_visible() ) {
		return false;
	}

	if ( '' !== fashion_brand_theme_get_company_field( 'company_name' ) ) {
		return true;
	}

	if ( '' !== fashion_brand_theme_get_company_field( 'company_phone' ) ) {
		return true;
	}

	return ! empty( fashion_brand_theme_get_company_address_lines() );
}

/**
 * Whether KVK and/or VAT should appear in the footer legal line.
 *
 * @return bool
 */
function fashion_brand_theme_has_company_legal_ids() {
	if ( ! fashion_brand_theme_company_details_visible() ) {
		return false;
	}

	return '' !== fashion_brand_theme_get_company_field( 'company_kvk' )
		|| '' !== fashion_brand_theme_get_company_field( 'company_vat' );
}

/**
 * Output Organization JSON-LD when the master switch is on and company name is set.
 *
 * @return void
 */
function fashion_brand_theme_output_company_schema() {
	if ( is_admin() || ! fashion_brand_theme_company_details_visible() ) {
		return;
	}

	$name = fashion_brand_theme_get_company_field( 'company_name' );

	if ( '' === $name ) {
		return;
	}

	$data = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => $name,
		'url'      => home_url( '/' ),
	);

	$phone = fashion_brand_theme_get_company_field( 'company_phone' );
	if ( '' !== $phone ) {
		$data['telephone'] = $phone;
	}

	$email = fashion_brand_theme_get_company_field( 'company_email' );
	if ( '' !== $email ) {
		$data['email'] = $email;
	}

	$street   = fashion_brand_theme_get_company_field( 'company_street' );
	$postcode = fashion_brand_theme_get_company_field( 'company_postcode' );
	$city     = fashion_brand_theme_get_company_field( 'company_city' );

	if ( '' !== $street || '' !== $postcode || '' !== $city ) {
		$address = array( '@type' => 'PostalAddress' );

		if ( '' !== $street ) {
			$address['streetAddress'] = $street;
		}
		if ( '' !== $postcode ) {
			$address['postalCode'] = $postcode;
		}
		if ( '' !== $city ) {
			$address['addressLocality'] = $city;
		}

		$data['address'] = $address;
	}

	$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

	if ( ! is_string( $json ) || '' === $json ) {
		return;
	}

	echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD from sanitized settings.
}
add_action( 'wp_head', 'fashion_brand_theme_output_company_schema', 5 );
