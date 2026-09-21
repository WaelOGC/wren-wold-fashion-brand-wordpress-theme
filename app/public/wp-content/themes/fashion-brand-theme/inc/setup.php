<?php
/**
 * Theme setup.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme defaults and WordPress feature support.
 */
function fashion_brand_theme_setup() {
	load_theme_textdomain( 'fashion-brand-theme', FASHION_BRAND_THEME_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Navigation', 'fashion-brand-theme' ),
			'utility' => esc_html__( 'Utility Navigation', 'fashion-brand-theme' ),
			'footer'  => esc_html__( 'Footer Menu', 'fashion-brand-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'fashion_brand_theme_setup' );

/**
 * Set the content width in pixels.
 */
function fashion_brand_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'fashion_brand_theme_content_width', 1200 );
}
add_action( 'after_setup_theme', 'fashion_brand_theme_content_width', 0 );

/**
 * Ensure the Collections landing page exists for nav and page-collections.php.
 *
 * @return void
 */
function fashion_brand_theme_ensure_collections_page() {
	if ( get_page_by_path( 'collections' ) ) {
		return;
	}

	wp_insert_post(
		array(
			'post_title'   => __( 'Collections', 'fashion-brand-theme' ),
			'post_name'    => 'collections',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		)
	);
}
add_action( 'init', 'fashion_brand_theme_ensure_collections_page' );

/**
 * Ensure core WooCommerce product categories exist with SEO defaults.
 *
 * Creates top-level product_cat terms when missing. Yoast SEO for newly
 * created terms is stored via WPSEO_Taxonomy_Meta (wpseo_taxonomy_meta option),
 * not plain term meta — that is how this Yoast version reads taxonomy SEO.
 *
 * @return void
 */
function fashion_brand_theme_ensure_product_categories() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}

	$categories = array(
		'blouses' => array(
			'name'        => 'Blouses',
			'description' => 'Effortless blouses in breathable fabrics, from tailored silhouettes to relaxed daywear.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's blouses",
				'wpseo_title'   => "Women's Blouses | WREN WOLD",
				'wpseo_desc'    => "Shop women's blouses at WREN WOLD — tailored and relaxed silhouettes in breathable fabrics, made for everyday wear.",
			),
		),
		'skirts'  => array(
			'name'        => 'Skirts',
			'description' => 'Skirts for every season, from clean daily cuts to statement silhouettes.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's skirts",
				'wpseo_title'   => "Women's Skirts | WREN WOLD",
				'wpseo_desc'    => "Shop women's skirts at WREN WOLD — clean daily cuts and statement silhouettes for every season.",
			),
		),
		'coats'   => array(
			'name'        => 'Coats',
			'description' => 'Outerwear built for European weather, from lightweight jackets to structured coats.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's coats",
				'wpseo_title'   => "Women's Coats & Jackets | WREN WOLD",
				'wpseo_desc'    => "Shop women's coats and jackets at WREN WOLD — lightweight to structured outerwear for European weather.",
			),
		),
	);

	foreach ( $categories as $slug => $category ) {
		if ( term_exists( $slug, 'product_cat' ) ) {
			continue;
		}

		$result = wp_insert_term(
			$category['name'],
			'product_cat',
			array(
				'slug'        => $slug,
				'description' => $category['description'],
			)
		);

		if ( is_wp_error( $result ) || empty( $result['term_id'] ) ) {
			continue;
		}

		$term_id = (int) $result['term_id'];

		if ( class_exists( 'WPSEO_Taxonomy_Meta' ) ) {
			WPSEO_Taxonomy_Meta::set_values( $term_id, 'product_cat', $category['yoast'] );
		}
	}
}
add_action( 'init', 'fashion_brand_theme_ensure_product_categories', 20 );

/**
 * Ensure WooCommerce product subcategories exist with SEO defaults.
 *
 * Creates child product_cat terms under existing parents when missing.
 * Registered on the same init priority after ensure_product_categories so
 * parent terms (including Blouses/Skirts/Coats) exist first. Yoast SEO uses
 * WPSEO_Taxonomy_Meta::set_values(), matching the parent-category helper.
 *
 * @return void
 */
function fashion_brand_theme_ensure_product_subcategories() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}

	$subcategories = array(
		'dresses-daily'       => array(
			'parent'      => 'dresses',
			'name'        => 'Daily Dresses',
			'description' => 'Easy everyday dresses in soft, breathable fabrics built for daily wear.',
			'yoast'       => array(
				'wpseo_focuskw' => 'daily dresses',
				'wpseo_title'   => 'Daily Dresses for Women | WREN WOLD',
				'wpseo_desc'    => 'Shop everyday dresses at WREN WOLD — soft, breathable styles made for daily wear.',
			),
		),
		'dresses-evening'     => array(
			'parent'      => 'dresses',
			'name'        => 'Evening Dresses',
			'description' => 'Elegant evening dresses designed to stand out at parties and special occasions.',
			'yoast'       => array(
				'wpseo_focuskw' => 'evening dresses',
				'wpseo_title'   => 'Evening Dresses for Women | WREN WOLD',
				'wpseo_desc'    => 'Shop evening dresses at WREN WOLD — elegant styles made to stand out after dark.',
			),
		),
		'dresses-formal'      => array(
			'parent'      => 'dresses',
			'name'        => 'Formal & Cocktail Dresses',
			'description' => 'Refined cocktail and formal dresses for weddings, galas, and elegant evenings out.',
			'yoast'       => array(
				'wpseo_focuskw' => 'cocktail dresses',
				'wpseo_title'   => 'Formal & Cocktail Dresses | WREN WOLD',
				'wpseo_desc'    => 'Shop formal and cocktail dresses at WREN WOLD — refined pieces for weddings and galas.',
			),
		),
		'pants-overalls'      => array(
			'parent'      => 'pants',
			'name'        => 'Overalls',
			'description' => 'Relaxed overalls that pair easy comfort with a confident, modern silhouette.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's overalls",
				'wpseo_title'   => "Women's Overalls | WREN WOLD",
				'wpseo_desc'    => "Shop women's overalls at WREN WOLD — relaxed comfort with a confident, modern silhouette.",
			),
		),
		'pants-fitted-shorts' => array(
			'parent'      => 'pants',
			'name'        => 'Fitted Shorts',
			'description' => 'Tailored fitted shorts cut close to the body for warm-weather styling.',
			'yoast'       => array(
				'wpseo_focuskw' => 'fitted shorts',
				'wpseo_title'   => 'Fitted Shorts for Women | WREN WOLD',
				'wpseo_desc'    => 'Shop fitted shorts at WREN WOLD — tailored, close-cut styles for warm-weather dressing.',
			),
		),
		'pants-training'      => array(
			'parent'      => 'pants',
			'name'        => 'Training Pants',
			'description' => 'Soft, flexible training pants built for movement, comfort, and everyday ease.',
			'yoast'       => array(
				'wpseo_focuskw' => 'training pants',
				'wpseo_title'   => "Women's Training Pants | WREN WOLD",
				'wpseo_desc'    => 'Shop training pants at WREN WOLD — soft, flexible styles built for movement and ease.',
			),
		),
		'pants-long'          => array(
			'parent'      => 'pants',
			'name'        => 'Long Pants',
			'description' => 'Classic long pants in versatile cuts, made for work, weekends, and travel.',
			'yoast'       => array(
				'wpseo_focuskw' => 'long pants',
				'wpseo_title'   => "Women's Long Pants | WREN WOLD",
				'wpseo_desc'    => 'Shop long pants at WREN WOLD — classic, versatile cuts for work, weekends, and travel.',
			),
		),
		'pants-leggings'      => array(
			'parent'      => 'pants',
			'name'        => 'Leggings',
			'description' => 'Stretch leggings built for all-day comfort, layering, and easy movement.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's leggings",
				'wpseo_title'   => "Women's Leggings | WREN WOLD",
				'wpseo_desc'    => "Shop women's leggings at WREN WOLD — stretch comfort built for layering and movement.",
			),
		),
		'pants-elegant'       => array(
			'parent'      => 'pants',
			'name'        => 'Elegant Pants',
			'description' => 'Elegant tailored pants that dress up easily for the office or evening.',
			'yoast'       => array(
				'wpseo_focuskw' => 'elegant pants',
				'wpseo_title'   => 'Elegant Pants for Women | WREN WOLD',
				'wpseo_desc'    => 'Shop elegant pants at WREN WOLD — tailored styles that dress up for office or evening.',
			),
		),
		'knitwear-sweaters'   => array(
			'parent'      => 'knitwear',
			'name'        => 'Regular Sweaters',
			'description' => 'Soft everyday sweaters in easy knits, made for layering through cooler seasons.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's sweaters",
				'wpseo_title'   => "Women's Sweaters | WREN WOLD",
				'wpseo_desc'    => "Shop women's sweaters at WREN WOLD — soft knits made for layering through cooler seasons.",
			),
		),
		'knitwear-turtlenecks' => array(
			'parent'      => 'knitwear',
			'name'        => 'Turtle-Necks',
			'description' => 'Fitted turtle-neck knits that layer cleanly under coats and structured jackets.',
			'yoast'       => array(
				'wpseo_focuskw' => 'turtle-neck sweaters',
				'wpseo_title'   => 'Turtle-Neck Sweaters | WREN WOLD',
				'wpseo_desc'    => 'Shop turtle-neck sweaters at WREN WOLD — fitted knits that layer under coats and jackets.',
			),
		),
		'tshirts-bodysuits'   => array(
			'parent'      => 't-shirts',
			'name'        => 'Bodysuits',
			'description' => 'Sleek bodysuits that tuck in seamlessly for a clean, polished silhouette.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's bodysuits",
				'wpseo_title'   => "Women's Bodysuits | WREN WOLD",
				'wpseo_desc'    => "Shop women's bodysuits at WREN WOLD — sleek, seamless styles for a clean silhouette.",
			),
		),
	);

	foreach ( $subcategories as $slug => $subcategory ) {
		if ( term_exists( $slug, 'product_cat' ) ) {
			continue;
		}

		$parent = get_term_by( 'slug', $subcategory['parent'], 'product_cat' );
		if ( ! $parent || is_wp_error( $parent ) ) {
			continue;
		}

		$result = wp_insert_term(
			$subcategory['name'],
			'product_cat',
			array(
				'slug'        => $slug,
				'description' => $subcategory['description'],
				'parent'      => (int) $parent->term_id,
			)
		);

		if ( is_wp_error( $result ) || empty( $result['term_id'] ) ) {
			continue;
		}

		$term_id = (int) $result['term_id'];

		if ( class_exists( 'WPSEO_Taxonomy_Meta' ) ) {
			WPSEO_Taxonomy_Meta::set_values( $term_id, 'product_cat', $subcategory['yoast'] );
		}
	}
}
add_action( 'init', 'fashion_brand_theme_ensure_product_subcategories', 20 );

/**
 * Backfill description + Yoast SEO for original product categories.
 *
 * Only fills terms whose description is still empty, so re-runs and manual
 * owner edits are left untouched. Yoast uses WPSEO_Taxonomy_Meta::set_values().
 *
 * @return void
 */
function fashion_brand_theme_ensure_product_category_seo() {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return;
	}

	$categories = array(
		't-shirts' => array(
			'description' => 'Everyday T-shirts in soft, breathable cotton, made for effortless year-round wear.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's t-shirts",
				'wpseo_title'   => "Women's T-Shirts | WREN WOLD",
				'wpseo_desc'    => "Shop women's T-shirts at WREN WOLD — soft, breathable cotton made for everyday wear.",
			),
		),
		'hoodies'  => array(
			'description' => 'Relaxed hoodies in cozy fabrics, built for easy layering and off-duty comfort.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's hoodies",
				'wpseo_title'   => "Women's Hoodies | WREN WOLD",
				'wpseo_desc'    => "Shop women's hoodies at WREN WOLD — cozy layers built for easy, off-duty comfort.",
			),
		),
		'knitwear' => array(
			'description' => 'Soft knitwear pieces made for layering and warmth through the cooler seasons.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's knitwear",
				'wpseo_title'   => "Women's Knitwear | WREN WOLD",
				'wpseo_desc'    => "Shop women's knitwear at WREN WOLD — soft layers made for warmth all season.",
			),
		),
		'shirts'   => array(
			'description' => 'Tailored shirts in clean cuts, versatile enough for the office or weekends.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's shirts",
				'wpseo_title'   => "Women's Shirts | WREN WOLD",
				'wpseo_desc'    => "Shop women's shirts at WREN WOLD — tailored cuts for the office or weekends.",
			),
		),
		'pants'    => array(
			'description' => 'Versatile pants in tailored and relaxed cuts, built for work, travel, and everyday wear.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's pants",
				'wpseo_title'   => "Women's Pants | WREN WOLD",
				'wpseo_desc'    => "Shop women's pants at WREN WOLD — tailored and relaxed cuts for everyday wear.",
			),
		),
		'dresses'  => array(
			'description' => 'Dresses for every occasion, from easy daily wear to elegant evening styles.',
			'yoast'       => array(
				'wpseo_focuskw' => "women's dresses",
				'wpseo_title'   => "Women's Dresses | WREN WOLD",
				'wpseo_desc'    => "Shop women's dresses at WREN WOLD — daily, evening, and formal styles in one place.",
			),
		),
	);

	foreach ( $categories as $slug => $category ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}

		if ( '' !== trim( (string) $term->description ) ) {
			continue;
		}

		$term_id = (int) $term->term_id;

		$updated = wp_update_term(
			$term_id,
			'product_cat',
			array(
				'description' => $category['description'],
			)
		);

		if ( is_wp_error( $updated ) ) {
			continue;
		}

		if ( class_exists( 'WPSEO_Taxonomy_Meta' ) ) {
			WPSEO_Taxonomy_Meta::set_values( $term_id, 'product_cat', $category['yoast'] );
		}
	}
}
add_action( 'init', 'fashion_brand_theme_ensure_product_category_seo', 21 );

/**
 * Ensure the Contact page exists for nav and page-contact.php.
 *
 * @return void
 */
function fashion_brand_theme_ensure_contact_page() {
	if ( get_page_by_path( 'contact' ) ) {
		return;
	}

	wp_insert_post(
		array(
			'post_title'   => __( 'Contact', 'fashion-brand-theme' ),
			'post_name'    => 'contact',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		)
	);
}
add_action( 'init', 'fashion_brand_theme_ensure_contact_page' );

/**
 * Ensure the Home page exists and is set as the static front page.
 *
 * @return void
 */
function fashion_brand_theme_ensure_home_page() {
	$home_page = get_page_by_path( 'home' );

	if ( ! $home_page ) {
		$page_id = wp_insert_post(
			array(
				'post_title'   => __( 'Home', 'fashion-brand-theme' ),
				'post_name'    => 'home',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			)
		);
	} else {
		$page_id = $home_page->ID;
	}

	if ( ! $page_id ) {
		return;
	}

	if ( 'page' !== get_option( 'show_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
	}

	if ( (int) get_option( 'page_on_front' ) !== (int) $page_id ) {
		update_option( 'page_on_front', $page_id );
	}
}
add_action( 'init', 'fashion_brand_theme_ensure_home_page' );
