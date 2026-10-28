<?php
/**
 * Company & Contact details — getters, address formatting, Schema.org.
 *
 * Data is entered in the Customizer; each field has its own Theme Settings visibility toggle.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map company data keys to their visibility setting keys.
 *
 * @return array<string, string>
 */
function fashion_brand_theme_get_company_visibility_map() {
	return array(
		'company_name'     => 'company_name_visible',
		'company_phone'    => 'phone_visible',
		'company_email'    => 'email_visible',
		'company_street'   => 'street_visible',
		'company_postcode' => 'postcode_visible',
		'company_city'     => 'city_visible',
		'company_kvk'      => 'kvk_visible',
		'company_vat'      => 'vat_visible',
	);
}

/**
 * Whether a company data field’s visibility toggle is on.
 *
 * @param string $field_key Data setting key (e.g. company_phone).
 * @return bool
 */
function fashion_brand_theme_is_company_field_visible( $field_key ) {
	$map = fashion_brand_theme_get_company_visibility_map();

	if ( ! isset( $map[ $field_key ] ) ) {
		return false;
	}

	return fashion_brand_theme_is_setting_enabled( $map[ $field_key ] );
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
 * Company field value only when non-empty and its visibility toggle is on.
 *
 * @param string $key Data setting key.
 * @return string
 */
function fashion_brand_theme_get_visible_company_field( $key ) {
	$value = fashion_brand_theme_get_company_field( $key );

	if ( '' === $value || ! fashion_brand_theme_is_company_field_visible( $key ) ) {
		return '';
	}

	return $value;
}

/**
 * Build address lines from visible street / postcode / city (empty or hidden parts omitted).
 *
 * @return string[]
 */
function fashion_brand_theme_get_company_address_lines() {
	$lines  = array();
	$street = fashion_brand_theme_get_visible_company_field( 'company_street' );

	if ( '' !== $street ) {
		$lines[] = $street;
	}

	$postcode = fashion_brand_theme_get_visible_company_field( 'company_postcode' );
	$city     = fashion_brand_theme_get_visible_company_field( 'company_city' );
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
 * Whether any customer-facing company detail (name / address / phone) should render.
 *
 * @return bool
 */
function fashion_brand_theme_has_company_public_details() {
	if ( '' !== fashion_brand_theme_get_visible_company_field( 'company_name' ) ) {
		return true;
	}

	if ( '' !== fashion_brand_theme_get_visible_company_field( 'company_phone' ) ) {
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
	return '' !== fashion_brand_theme_get_visible_company_field( 'company_kvk' )
		|| '' !== fashion_brand_theme_get_visible_company_field( 'company_vat' );
}

/**
 * Output Organization JSON-LD for whichever company fields are visible and non-empty.
 *
 * @return void
 */
function fashion_brand_theme_output_company_schema() {
	if ( is_admin() ) {
		return;
	}

	$name  = fashion_brand_theme_get_visible_company_field( 'company_name' );
	$phone = fashion_brand_theme_get_visible_company_field( 'company_phone' );
	$email = fashion_brand_theme_get_visible_company_field( 'company_email' );
	$street   = fashion_brand_theme_get_visible_company_field( 'company_street' );
	$postcode = fashion_brand_theme_get_visible_company_field( 'company_postcode' );
	$city     = fashion_brand_theme_get_visible_company_field( 'company_city' );

	$has_address = '' !== $street || '' !== $postcode || '' !== $city;

	if ( '' === $name && '' === $phone && '' === $email && ! $has_address ) {
		return;
	}

	$data = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'url'      => home_url( '/' ),
	);

	if ( '' !== $name ) {
		$data['name'] = $name;
	}

	if ( '' !== $phone ) {
		$data['telephone'] = $phone;
	}

	if ( '' !== $email ) {
		$data['email'] = $email;
	}

	if ( $has_address ) {
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
