<?php
/**
 * Shop sub-category row (siblings/children of current category).
 *
 * @package Fashion_Brand_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_product_category() ) {
	return;
}

$term = get_queried_object();
if ( ! $term instanceof WP_Term ) {
	return;
}

$parent_id = (int) $term->parent;
$child_of  = $parent_id > 0 ? $parent_id : (int) $term->term_id;

$terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'parent'     => $child_of,
		'orderby'    => 'name',
	)
);

if ( is_wp_error( $terms ) || empty( $terms ) ) {
	return;
}

// When viewing a parent with no children, nothing to show (handled above).
// When on /shop/, this template is not called with a category — already returned.
?>
<nav class="shop-subcats container container--wide" aria-label="<?php esc_attr_e( 'Subcategories', 'fashion-brand-theme' ); ?>">
	<?php foreach ( $terms as $sub ) : ?>
		<?php
		$url       = get_term_link( $sub );
		$is_active = ( (int) $sub->term_id === (int) $term->term_id );
		if ( is_wp_error( $url ) ) {
			continue;
		}
		?>
		<a
			class="shop-subcats__pill<?php echo $is_active ? ' is-active' : ''; ?>"
			href="<?php echo esc_url( $url ); ?>"
			<?php echo $is_active ? ' aria-current="page"' : ''; ?>
		>
			<?php echo esc_html( $sub->name ); ?>
		</a>
	<?php endforeach; ?>
</nav>
