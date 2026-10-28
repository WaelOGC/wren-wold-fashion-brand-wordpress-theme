<?php
/**
 * WordPress Customizer integration.
 *
 * Presentation-level controls only. Operational settings remain in Theme Settings.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer setting keys exposed in the admin preview panel.
 *
 * @return string[]
 */
function fashion_brand_theme_get_customizer_setting_keys() {
	return array(
		'display_label',
		'announcement_enabled',
		'announcement_text',
	);
}

/**
 * Register Customizer settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function fashion_brand_theme_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'fashion_brand_theme_presentation',
		array(
			'title'       => __( 'Fashion Brand Theme', 'fashion-brand-theme' ),
			'description' => __( 'Lightweight presentation controls. Full theme settings are managed under Fashion Brand → Theme Settings.', 'fashion-brand-theme' ),
			'priority'    => 30,
		)
	);

	$controls = array(
		'display_label'        => array(
			'label' => __( 'Display Label Fallback', 'fashion-brand-theme' ),
			'type'  => 'text',
		),
		'announcement_enabled' => array(
			'label' => __( 'Enable Announcement Bar', 'fashion-brand-theme' ),
			'type'  => 'checkbox',
		),
		'announcement_text'    => array(
			'label' => __( 'Announcement Text', 'fashion-brand-theme' ),
			'type'  => 'textarea',
		),
	);

	foreach ( $controls as $key => $control ) {
		$setting_id = fashion_brand_theme_get_customizer_setting_id( $key );

		$wp_customize->add_setting(
			$setting_id,
			array(
				'capability'        => 'edit_theme_options',
				'default'           => fashion_brand_theme_get_default_settings()[ $key ],
				'transport'         => 'refresh',
				'sanitize_callback' => static function ( $value ) use ( $key ) {
					return fashion_brand_theme_persist_setting( $key, $value );
				},
			)
		);

		add_filter(
			'customize_value_' . $setting_id,
			static function () use ( $key ) {
				return fashion_brand_theme_get_setting( $key );
			}
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $control['label'],
				'section' => 'fashion_brand_theme_presentation',
				'type'    => $control['type'],
			)
		);
	}
}
add_action( 'customize_register', 'fashion_brand_theme_customize_register' );

/**
 * Register Customizer social media URL fields.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function fashion_brand_theme_customize_register_social_media( $wp_customize ) {
	$wp_customize->add_section(
		'fashion_brand_theme_social_media',
		array(
			'title'       => __( 'Social Media', 'fashion-brand-theme' ),
			'description' => __( 'Add profile URLs and choose which platforms appear across the theme. Uncheck “Show” to hide a platform temporarily without deleting its URL.', 'fashion-brand-theme' ),
			'priority'    => 35,
		)
	);

	foreach ( fashion_brand_theme_get_social_platforms() as $platform => $label ) {
		$setting_id         = fashion_brand_theme_get_social_setting_id( $platform );
		$enabled_setting_id = fashion_brand_theme_get_social_enabled_setting_id( $platform );

		$wp_customize->add_setting(
			$setting_id,
			array(
				'capability'        => 'edit_theme_options',
				'default'           => '',
				'transport'         => 'refresh',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => 'fashion_brand_theme_social_media',
				'type'    => 'url',
			)
		);

		$wp_customize->add_setting(
			$enabled_setting_id,
			array(
				'capability'        => 'edit_theme_options',
				'default'           => true,
				'transport'         => 'refresh',
				'sanitize_callback' => 'wp_validate_boolean',
			)
		);

		$wp_customize->add_control(
			$enabled_setting_id,
			array(
				/* translators: %s: social platform name. */
				'label'   => sprintf( __( 'Show %s', 'fashion-brand-theme' ), $label ),
				'section' => 'fashion_brand_theme_social_media',
				'type'    => 'checkbox',
			)
		);
	}
}
add_action( 'customize_register', 'fashion_brand_theme_customize_register_social_media', 20 );

/**
 * Company & Contact Customizer setting keys (stored in Theme Settings option).
 *
 * @return string[]
 */
function fashion_brand_theme_get_company_customizer_setting_keys() {
	return array(
		'company_name',
		'company_phone',
		'company_email',
		'company_street',
		'company_postcode',
		'company_city',
		'company_kvk',
		'company_vat',
	);
}

/**
 * Register Customizer Company & Contact fields.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function fashion_brand_theme_customize_register_company_contact( $wp_customize ) {
	$wp_customize->add_section(
		'fashion_brand_theme_company_contact',
		array(
			'title'       => __( 'Company & Contact', 'fashion-brand-theme' ),
			'description' => __( 'Business details for the Contact page, footer, and Schema.org. Visibility is controlled under Fashion Brand → Theme Settings → Company. Leave any field blank to omit it.', 'fashion-brand-theme' ),
			'priority'    => 36,
		)
	);

	$controls = array(
		'company_name'     => array(
			'label' => __( 'Company Name', 'fashion-brand-theme' ),
			'type'  => 'text',
		),
		'company_phone'    => array(
			'label' => __( 'Phone', 'fashion-brand-theme' ),
			'type'  => 'tel',
		),
		'company_email'    => array(
			'label' => __( 'Email', 'fashion-brand-theme' ),
			'type'  => 'email',
		),
		'company_street'   => array(
			'label' => __( 'Street + Number', 'fashion-brand-theme' ),
			'type'  => 'text',
		),
		'company_postcode' => array(
			'label' => __( 'Postcode', 'fashion-brand-theme' ),
			'type'  => 'text',
		),
		'company_city'     => array(
			'label' => __( 'City', 'fashion-brand-theme' ),
			'type'  => 'text',
		),
		'company_kvk'      => array(
			'label' => __( 'KVK Number', 'fashion-brand-theme' ),
			'type'  => 'text',
		),
		'company_vat'      => array(
			'label' => __( 'VAT Number', 'fashion-brand-theme' ),
			'type'  => 'text',
		),
	);

	$defaults = fashion_brand_theme_get_default_settings();

	foreach ( $controls as $key => $control ) {
		$setting_id = fashion_brand_theme_get_customizer_setting_id( $key );

		$wp_customize->add_setting(
			$setting_id,
			array(
				'capability'        => 'edit_theme_options',
				'default'           => $defaults[ $key ] ?? '',
				'transport'         => 'refresh',
				'sanitize_callback' => static function ( $value ) use ( $key ) {
					return fashion_brand_theme_persist_setting( $key, $value );
				},
			)
		);

		add_filter(
			'customize_value_' . $setting_id,
			static function () use ( $key ) {
				return fashion_brand_theme_get_setting( $key );
			}
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $control['label'],
				'section' => 'fashion_brand_theme_company_contact',
				'type'    => $control['type'],
			)
		);
	}
}
add_action( 'customize_register', 'fashion_brand_theme_customize_register_company_contact', 25 );

/**
 * Clear Customizer theme_mods for company fields (values live in Theme Settings option).
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function fashion_brand_theme_customize_save_company_contact_settings( $wp_customize ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	foreach ( fashion_brand_theme_get_company_customizer_setting_keys() as $key ) {
		remove_theme_mod( fashion_brand_theme_get_customizer_setting_id( $key ) );
	}
}
add_action( 'customize_save_after', 'fashion_brand_theme_customize_save_company_contact_settings' );

/**
 * Persist unchecked social platform enabled toggles.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function fashion_brand_theme_customize_save_social_media_settings( $wp_customize ) {
	foreach ( array_keys( fashion_brand_theme_get_social_platforms() ) as $platform ) {
		$enabled_setting = $wp_customize->get_setting(
			fashion_brand_theme_get_social_enabled_setting_id( $platform )
		);

		if ( $enabled_setting && false === $enabled_setting->post_value( false ) ) {
			set_theme_mod( fashion_brand_theme_get_social_enabled_setting_id( $platform ), false );
		}
	}
}
add_action( 'customize_save_after', 'fashion_brand_theme_customize_save_social_media_settings' );

/**
 * Build a Customizer setting ID for a theme setting key.
 *
 * @param string $key Setting key.
 * @return string
 */
function fashion_brand_theme_get_customizer_setting_id( $key ) {
	return 'fashion_brand_theme_' . $key;
}

/**
 * Ensure unchecked Customizer checkboxes persist as disabled values.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function fashion_brand_theme_customize_save_presentation_settings( $wp_customize ) {
	$checkbox_setting = $wp_customize->get_setting(
		fashion_brand_theme_get_customizer_setting_id( 'announcement_enabled' )
	);

	if ( $checkbox_setting && false === $checkbox_setting->post_value( false ) ) {
		fashion_brand_theme_persist_setting( 'announcement_enabled', false );
	}

	foreach ( fashion_brand_theme_get_customizer_setting_keys() as $key ) {
		remove_theme_mod( fashion_brand_theme_get_customizer_setting_id( $key ) );
	}
}
add_action( 'customize_save_after', 'fashion_brand_theme_customize_save_presentation_settings' );
