<?php
/**
 * Navigation helpers and fallbacks.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical WordPress page slugs for top-level theme destinations.
 *
 * Used as placeholder paths until real pages exist. When a matching page is
 * published, `fashion_brand_theme_get_page_url()` resolves to its permalink.
 *
 * @return array<string, string> Page slug => navigation label.
 */
function fashion_brand_theme_get_theme_page_slugs() {
	return array(
		'collections' => __( 'Collections', 'fashion-brand-theme' ),
		'guides'      => __( 'Guides', 'fashion-brand-theme' ),
		'about'       => __( 'About', 'fashion-brand-theme' ),
		'contact'     => __( 'Contact', 'fashion-brand-theme' ),
	);
}

/**
 * Approved WooCommerce product category slugs.
 *
 * Canonical shop category set (order matters for homepage index + nav).
 *
 * @return array<string, string> Category slug => label.
 */
function fashion_brand_theme_get_product_category_slugs() {
	return array(
		't-shirts' => __( 'T-Shirts', 'fashion-brand-theme' ),
		'hoodies'  => __( 'Hoodies', 'fashion-brand-theme' ),
		'knitwear' => __( 'Knitwear', 'fashion-brand-theme' ),
		'shirts'   => __( 'Shirts', 'fashion-brand-theme' ),
		'blouses'  => __( 'Blouses', 'fashion-brand-theme' ),
		'pants'    => __( 'Pants', 'fashion-brand-theme' ),
		'skirts'   => __( 'Skirts', 'fashion-brand-theme' ),
		'dresses'  => __( 'Dresses', 'fashion-brand-theme' ),
		'coats'    => __( 'Coats', 'fashion-brand-theme' ),
	);
}

/**
 * Nested shop category tree for the sidebar filter (top-level + children).
 *
 * Preserves canonical top-level order from fashion_brand_theme_get_product_category_slugs().
 * Children are ordered by name; empty when a parent has none or the term is missing.
 *
 * @return array<string, array{label: string, term_id: int, children: array<string, string>}>
 */
function fashion_brand_theme_get_shop_category_tree() {
	$tree = array();

	foreach ( fashion_brand_theme_get_product_category_slugs() as $slug => $label ) {
		$children = array();
		$term_id  = 0;

		if ( taxonomy_exists( 'product_cat' ) ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );

			if ( $term && ! is_wp_error( $term ) ) {
				$term_id     = (int) $term->term_id;
				$child_terms = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'parent'     => $term_id,
						'hide_empty' => false,
						'orderby'    => 'name',
					)
				);

				if ( ! is_wp_error( $child_terms ) && ! empty( $child_terms ) ) {
					foreach ( $child_terms as $child ) {
						$children[ $child->slug ] = $child->name;
					}
				}
			}
		}

		$tree[ $slug ] = array(
			'label'    => $label,
			'term_id'  => $term_id,
			'children' => $children,
		);
	}

	return $tree;
}

/**
 * Active product-tag collections for navigation and the Collections page.
 *
 * Only tags opted in via `_fbt_show_in_collections` with at least one product.
 *
 * @return array<int, array<string, mixed>>
 */
function fashion_brand_theme_get_active_collections() {
	if ( ! taxonomy_exists( 'product_tag' ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'product_tag',
			'hide_empty' => true,
			'meta_query' => array(
				array(
					'key'   => '_fbt_show_in_collections',
					'value' => 'yes',
				),
			),
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	$collections = array();

	foreach ( $terms as $term ) {
		$term_link = get_term_link( $term );

		if ( is_wp_error( $term_link ) ) {
			continue;
		}

		$image_id  = (int) get_term_meta( $term->term_id, '_fbt_collection_image_id', true );
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium_large' ) : '';

		$collections[] = array(
			'term_id'   => (int) $term->term_id,
			'slug'      => $term->slug,
			'name'      => $term->name,
			'url'       => $term_link,
			'image_url' => $image_url ? $image_url : '',
		);
	}

	usort(
		$collections,
		static function ( $a, $b ) {
			return strcasecmp( $a['name'], $b['name'] );
		}
	);

	return $collections;
}

/**
 * Shop submenu categories for navigation (includes All Products → shop URL).
 *
 * @return array<string, string> Category slug => label.
 */
function fashion_brand_theme_get_shop_categories() {
	return array_merge(
		array(
			'all-products' => __( 'All Products', 'fashion-brand-theme' ),
		),
		fashion_brand_theme_get_product_category_slugs()
	);
}

/**
 * Resolve a WooCommerce or placeholder shop URL.
 *
 * @return string
 */
function fashion_brand_theme_get_shop_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$shop_url = wc_get_page_permalink( 'shop' );

		if ( $shop_url ) {
			return $shop_url;
		}
	}

	return home_url( '/shop/' );
}

/**
 * Resolve a product category URL.
 *
 * @param string $slug Category slug.
 * @return string
 */
function fashion_brand_theme_get_product_category_url( $slug ) {
	$slug = sanitize_title( (string) $slug );

	if ( 'all-products' === $slug ) {
		return fashion_brand_theme_get_shop_url();
	}

	if ( taxonomy_exists( 'product_cat' ) ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );

		if ( $term && ! is_wp_error( $term ) ) {
			$term_link = get_term_link( $term );

			if ( ! is_wp_error( $term_link ) ) {
				return $term_link;
			}
		}
	}

	return home_url( '/product-category/' . $slug . '/' );
}

/**
 * Resolve a theme page URL by slug.
 *
 * @param string $slug Page slug.
 * @return string
 */
function fashion_brand_theme_get_page_url( $slug ) {
	$slug = sanitize_title( (string) $slug );
	$page = get_page_by_path( $slug );

	if ( $page ) {
		return get_permalink( $page );
	}

	return home_url( '/' . trim( $slug, '/' ) . '/' );
}

/**
 * Resolve the site search URL.
 *
 * @return string
 */
function fashion_brand_theme_get_search_url() {
	return get_search_link();
}

/**
 * Resolve the account URL.
 *
 * @return string
 */
function fashion_brand_theme_get_account_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$account_url = wc_get_page_permalink( 'myaccount' );

		if ( $account_url ) {
			return $account_url;
		}
	}

	return fashion_brand_theme_get_page_url( 'my-account' );
}

/**
 * Resolve the cart URL.
 *
 * @return string
 */
function fashion_brand_theme_get_cart_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$cart_url = wc_get_page_permalink( 'cart' );

		if ( $cart_url ) {
			return $cart_url;
		}
	}

	return fashion_brand_theme_get_page_url( 'cart' );
}

/**
 * Primary navigation fallback markup.
 *
 * @param array<string, mixed> $args Menu arguments.
 * @return void
 */
function fashion_brand_theme_primary_nav_fallback( $args ) {
	$menu_id    = ! empty( $args['menu_id'] ) ? $args['menu_id'] : 'primary-menu';
	$menu_class = ! empty( $args['menu_class'] ) ? $args['menu_class'] : 'primary-menu';

	$primary_items = array(
		array(
			'label'    => __( 'Shop', 'fashion-brand-theme' ),
			'url'      => fashion_brand_theme_get_shop_url(),
			'children' => fashion_brand_theme_get_shop_categories(),
		),
		array(
			'label'     => __( 'Collections', 'fashion-brand-theme' ),
			'url'       => fashion_brand_theme_get_page_url( 'collections' ),
			'children'  => fashion_brand_theme_get_active_collections(),
			'child_url' => 'collection',
		),
	);

	foreach ( fashion_brand_theme_get_theme_page_slugs() as $slug => $label ) {
		if ( 'collections' === $slug ) {
			continue;
		}

		$primary_items[] = array(
			'label' => $label,
			'url'   => fashion_brand_theme_get_page_url( $slug ),
		);
	}

	echo '<ul id="' . esc_attr( $menu_id ) . '" class="' . esc_attr( $menu_class ) . '">';

	foreach ( $primary_items as $index => $item ) {
		$has_children = ! empty( $item['children'] );
		$item_classes = array( 'menu-item' );

		if ( $has_children ) {
			$item_classes[] = 'menu-item-has-children';
		}

		$submenu_id = $has_children ? sanitize_title( $item['label'] ) . '-submenu' : '';

		echo '<li class="' . esc_attr( implode( ' ', $item_classes ) ) . '">';
		echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';

		if ( $has_children ) {
			fashion_brand_theme_render_submenu_toggle( $submenu_id, $item['label'] );
			echo '<ul id="' . esc_attr( $submenu_id ) . '" class="sub-menu">';

			$is_collections = ! empty( $item['child_url'] ) && 'collection' === $item['child_url'];

			if ( $is_collections ) {
				foreach ( $item['children'] as $collection ) {
					echo '<li class="menu-item">';
					echo '<a href="' . esc_url( $collection['url'] ) . '">' . esc_html( $collection['name'] ) . '</a>';
					echo '</li>';
				}
			} else {
				foreach ( $item['children'] as $slug => $label ) {
					echo '<li class="menu-item">';
					echo '<a href="' . esc_url( fashion_brand_theme_get_product_category_url( $slug ) ) . '">' . esc_html( $label ) . '</a>';
					echo '</li>';
				}
			}

			echo '</ul>';
		}

		echo '</li>';
	}

	echo '</ul>';
}

/**
 * Output an accessible submenu toggle button.
 *
 * @param string $submenu_id Submenu element ID.
 * @param string $label      Parent item label.
 * @return void
 */
function fashion_brand_theme_render_submenu_toggle( $submenu_id, $label ) {
	printf(
		'<button type="button" class="submenu-toggle" aria-expanded="false" aria-controls="%1$s" aria-label="%2$s">',
		esc_attr( $submenu_id ),
		esc_attr(
			sprintf(
				/* translators: %s: parent navigation item label. */
				__( 'Toggle %s submenu', 'fashion-brand-theme' ),
				$label
			)
		)
	);
	echo '<span class="submenu-toggle__icon" aria-hidden="true"></span>';
	echo '</button>';
}

/**
 * Utility navigation fallback markup.
 *
 * @param array<string, mixed> $args Menu arguments.
 * @return void
 */
function fashion_brand_theme_utility_nav_fallback( $args ) {
	$menu_id    = ! empty( $args['menu_id'] ) ? $args['menu_id'] : 'utility-menu';
	$menu_class = ! empty( $args['menu_class'] ) ? $args['menu_class'] : 'utility-menu';

	echo '<ul id="' . esc_attr( $menu_id ) . '" class="' . esc_attr( $menu_class ) . '">';
	echo '<li class="menu-item">';
	echo '<a href="' . esc_url( fashion_brand_theme_get_account_url() ) . '">' . esc_html__( 'Account', 'fashion-brand-theme' ) . '</a>';
	echo '</li>';
	echo '<li class="menu-item">';
	echo '<a href="' . esc_url( fashion_brand_theme_get_cart_url() ) . '">' . esc_html__( 'Cart', 'fashion-brand-theme' ) . '</a>';
	echo '</li>';
	echo '</ul>';
}
