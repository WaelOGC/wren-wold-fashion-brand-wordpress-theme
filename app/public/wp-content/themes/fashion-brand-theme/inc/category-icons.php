<?php
/**
 * Product category line icons (admin assignment + front-end helpers).
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Original hand-drawn line icon definitions for top-level categories.
 *
 * @return array<string, array{label: string, svg: string}>
 */
function fashion_brand_theme_get_category_icon_definitions() {
	static $definitions = null;

	if ( null !== $definitions ) {
		return $definitions;
	}

	$svg_open = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" focusable="false">';
	$svg_close = '</svg>';

	$definitions = array(
		'tshirt'  => array(
			'label' => __( 'T-Shirt', 'fashion-brand-theme' ),
			'svg'   => $svg_open . '<path d="M8 8 L4 6 L2 9 L6 11 V20 H18 V11 L22 9 L20 6 L16 8" /><path d="M8 8 V6.5 C8 6.5 10 8 12 8 C14 8 16 6.5 16 6.5 V8" />' . $svg_close,
		),
		'hoodie'  => array(
			'label' => __( 'Hoodie', 'fashion-brand-theme' ),
			'svg'   => $svg_open . '<path d="M8 9 L4 7 L2 10 L6 12 V20 H18 V12 L22 10 L20 7 L16 9" /><path d="M8 9 C8 9 9 4.5 12 4.5 C15 4.5 16 9 16 9" /><path d="M10 20 V14 H14 V20" />' . $svg_close,
		),
		'sweater' => array(
			'label' => __( 'Sweater / Knitwear', 'fashion-brand-theme' ),
			'svg'   => $svg_open . '<path d="M8 7 L3 9 V13 L6 12 V20 H18 V12 L21 13 V9 L16 7" /><path d="M8 7 V5.5 C8 5.5 10 7 12 7 C14 7 16 5.5 16 5.5 V7" />' . $svg_close,
		),
		'shirt'   => array(
			'label' => __( 'Shirt', 'fashion-brand-theme' ),
			'svg'   => $svg_open . '<path d="M8 8 L4 6 L2 9 L6 11 V20 H18 V11 L22 9 L20 6 L16 8" /><path d="M9 8 L12 11 L15 8" /><path d="M12 11 V16" />' . $svg_close,
		),
		'blouse'  => array(
			'label' => __( 'Blouse', 'fashion-brand-theme' ),
			'svg'   => $svg_open . '<path d="M8 7 L4 5.5 L2 8.5 L6 10 V14 L5 20 H19 L18 14 V10 L22 8.5 L20 5.5 L16 7" /><path d="M8 7 C8 7 10 8.5 12 8.5 C14 8.5 16 7 16 7" />' . $svg_close,
		),
		'pants'   => array(
			'label' => __( 'Pants', 'fashion-brand-theme' ),
			'svg'   => $svg_open . '<path d="M9 4 H15 V9.5 L16.5 20.5 H13 L12 11.5 L11 20.5 H7.5 L9 9.5 Z" />' . $svg_close,
		),
		'skirt'   => array(
			'label' => __( 'Skirt', 'fashion-brand-theme' ),
			'svg'   => $svg_open . '<path d="M9 5 H15" /><path d="M9 5 L5.5 20 H18.5 L15 5" />' . $svg_close,
		),
		'dress'   => array(
			'label' => __( 'Dress', 'fashion-brand-theme' ),
			'svg'   => $svg_open . '<path d="M9 4 H15 V9 L19.5 20.5 H4.5 L9 9 Z" /><path d="M10 4 C10 4 11 5.5 12 5.5 C13 5.5 14 4 14 4" />' . $svg_close,
		),
		'coat'    => array(
			'label' => __( 'Coat', 'fashion-brand-theme' ),
			'svg'   => $svg_open . '<path d="M8 6 L4 4 L2 7 L6 9 V21 H18 V9 L22 7 L20 4 L16 6" /><path d="M9 6 L12 11 L15 6" /><path d="M12 11 V21" />' . $svg_close,
		),
	);

	return $definitions;
}

/**
 * Return inline SVG markup for a category icon key.
 *
 * @param string $icon_key Icon key.
 * @return string
 */
function fashion_brand_theme_get_category_icon_svg( $icon_key ) {
	$definitions = fashion_brand_theme_get_category_icon_definitions();

	if ( ! is_string( $icon_key ) || ! isset( $definitions[ $icon_key ] ) ) {
		return '';
	}

	return $definitions[ $icon_key ]['svg'];
}

/**
 * Dropdown options for the category icon admin field.
 *
 * @return array<string, string>
 */
function fashion_brand_theme_get_category_icon_options() {
	$options = array();

	foreach ( fashion_brand_theme_get_category_icon_definitions() as $key => $definition ) {
		$options[ $key ] = $definition['label'];
	}

	return $options;
}

/**
 * Resolve the icon key for a product category term.
 *
 * Uses stored term meta when set. Top-level terms without meta fall back to
 * the canonical slug → icon map. Subcategories without meta return empty.
 *
 * @param int $term_id Term ID.
 * @return string
 */
function fashion_brand_theme_get_category_icon_key( $term_id ) {
	$term_id = (int) $term_id;

	if ( $term_id <= 0 ) {
		return '';
	}

	$stored = get_term_meta( $term_id, '_fbt_category_icon', true );
	$options = fashion_brand_theme_get_category_icon_options();

	if ( is_string( $stored ) && '' !== $stored && isset( $options[ $stored ] ) ) {
		return $stored;
	}

	$term = get_term( $term_id, 'product_cat' );

	if ( ! $term || is_wp_error( $term ) ) {
		return '';
	}

	if ( (int) $term->parent > 0 ) {
		return '';
	}

	$defaults = array(
		't-shirts' => 'tshirt',
		'hoodies'  => 'hoodie',
		'knitwear' => 'sweater',
		'shirts'   => 'shirt',
		'blouses'  => 'blouse',
		'pants'    => 'pants',
		'skirts'   => 'skirt',
		'dresses'  => 'dress',
		'coats'    => 'coat',
	);

	return isset( $defaults[ $term->slug ] ) ? $defaults[ $term->slug ] : '';
}

/**
 * Render the icon select on the add category form.
 *
 * @return void
 */
function fashion_brand_theme_product_cat_icon_add_field() {
	?>
	<div class="form-field term-fbt-category-icon-wrap">
		<label for="fbt_category_icon"><?php esc_html_e( 'Category icon', 'fashion-brand-theme' ); ?></label>
		<select name="fbt_category_icon" id="fbt_category_icon">
			<option value=""><?php esc_html_e( '— Auto (based on category) —', 'fashion-brand-theme' ); ?></option>
			<?php foreach ( fashion_brand_theme_get_category_icon_options() as $key => $label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<p><?php esc_html_e( 'Shown in the shop sidebar filter and category archive header for top-level categories.', 'fashion-brand-theme' ); ?></p>
	</div>
	<?php
}
add_action( 'product_cat_add_form_fields', 'fashion_brand_theme_product_cat_icon_add_field' );

/**
 * Render the icon select on the edit category form.
 *
 * @param WP_Term $term Current term.
 * @return void
 */
function fashion_brand_theme_product_cat_icon_edit_field( $term ) {
	$current = get_term_meta( (int) $term->term_id, '_fbt_category_icon', true );
	$current = is_string( $current ) ? $current : '';
	?>
	<tr class="form-field term-fbt-category-icon-wrap">
		<th scope="row">
			<label for="fbt_category_icon"><?php esc_html_e( 'Category icon', 'fashion-brand-theme' ); ?></label>
		</th>
		<td>
			<select name="fbt_category_icon" id="fbt_category_icon">
				<option value=""><?php esc_html_e( '— Auto (based on category) —', 'fashion-brand-theme' ); ?></option>
				<?php foreach ( fashion_brand_theme_get_category_icon_options() as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php esc_html_e( 'Shown in the shop sidebar filter and category archive header for top-level categories. Leave on Auto to use the default icon for known category slugs.', 'fashion-brand-theme' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'product_cat_edit_form_fields', 'fashion_brand_theme_product_cat_icon_edit_field' );

/**
 * Save the category icon term meta.
 *
 * @param int $term_id Term ID.
 * @return void
 */
function fashion_brand_theme_save_product_cat_icon( $term_id ) {
	if ( ! current_user_can( 'manage_product_terms' ) && ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	if ( ! isset( $_POST['fbt_category_icon'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	$value   = sanitize_text_field( wp_unslash( $_POST['fbt_category_icon'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$options = fashion_brand_theme_get_category_icon_options();

	if ( '' !== $value && ! isset( $options[ $value ] ) ) {
		$value = '';
	}

	if ( '' === $value ) {
		delete_term_meta( $term_id, '_fbt_category_icon' );
		return;
	}

	update_term_meta( $term_id, '_fbt_category_icon', $value );
}
add_action( 'created_product_cat', 'fashion_brand_theme_save_product_cat_icon' );
add_action( 'edited_product_cat', 'fashion_brand_theme_save_product_cat_icon' );
