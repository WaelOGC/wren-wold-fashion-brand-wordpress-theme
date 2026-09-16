<?php
/**
 * Matterhorn WooCommerce XML importer (WP-CLI only).
 *
 * USAGE
 * -----
 * 1. Upload the feed via SFTP / File Manager to:
 *    wp-content/uploads/matterhorn/feed-woocommerce.xml
 *    (Do not commit the XML into the theme repo — it is data, not code.)
 *
 * 2. Test a small batch first:
 *    wp matterhorn import --limit=20
 *
 * 3. Full run (resumable — safe to re-run; matches on _matterhorn_product_id):
 *    wp matterhorn import
 *
 * Optional:
 *    wp matterhorn import --offset=100 --limit=50
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * TODO: set real markup before running import.
 * Wholesale (price_netto / sale_price_netto) is multiplied by this constant
 * to produce storefront regular_price / sale_price. Default 1 = no markup.
 */
define( 'MATTERHORN_PRICE_MARKUP_MULTIPLIER', 2 );

/**
 * WP-CLI command group: wp matterhorn …
 */
class Fashion_Brand_Theme_Matterhorn_CLI extends WP_CLI_Command {

	/**
	 * Import / update products from the Matterhorn WooCommerce XML feed.
	 *
	 * ## OPTIONS
	 *
	 * [--limit=<n>]
	 * : Process at most N products (for test batches).
	 *
	 * [--offset=<n>]
	 * : Skip the first N products in the feed before importing.
	 *
	 * ## EXAMPLES
	 *
	 *     wp matterhorn import --limit=20
	 *     wp matterhorn import
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Associative flags.
	 * @return void
	 */
	public function import( $args, $assoc_args ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			WP_CLI::error( 'WooCommerce is not active.' );
		}

		$limit  = isset( $assoc_args['limit'] ) ? max( 0, (int) $assoc_args['limit'] ) : 0;
		$offset = isset( $assoc_args['offset'] ) ? max( 0, (int) $assoc_args['offset'] ) : 0;

		$feed = fashion_brand_theme_matterhorn_feed_path();

		if ( ! file_exists( $feed ) ) {
			WP_CLI::error(
				sprintf(
					'Feed not found at %s. Upload feed-woocommerce.xml there first.',
					$feed
				)
			);
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		if ( function_exists( 'fashion_brand_theme_ensure_product_attributes' ) ) {
			fashion_brand_theme_ensure_product_attributes();
		}

		fashion_brand_theme_matterhorn_ensure_brand_attribute();

		$total             = fashion_brand_theme_matterhorn_count_products( $feed );
		$imported          = 0; // Newly created products.
		$updated           = 0;
		$skipped_unmapped  = 0;
		$skipped_other     = 0;
		$errors            = 0;
		$seen              = 0;

		WP_CLI::log(
			sprintf(
				'Starting Matterhorn import (total in feed: %d, offset: %d, limit: %s, markup: %sx).',
				$total,
				$offset,
				$limit > 0 ? (string) $limit : 'none',
				(string) MATTERHORN_PRICE_MARKUP_MULTIPLIER
			)
		);

		$reader = new XMLReader();

		if ( ! $reader->open( $feed, null, LIBXML_NONET | LIBXML_COMPACT ) ) {
			WP_CLI::error( 'Could not open feed with XMLReader.' );
		}

		while ( $reader->read() ) {
			if ( XMLReader::ELEMENT !== $reader->nodeType || 'product' !== $reader->localName ) {
				continue;
			}

			$node_xml = $reader->readOuterXML();

			if ( '' === $node_xml ) {
				continue;
			}

			++$seen;

			if ( $seen <= $offset ) {
				continue;
			}

			$processed = $imported + $updated + $skipped_unmapped + $skipped_other + $errors;

			if ( $limit > 0 && $processed >= $limit ) {
				break;
			}

			$product_data = fashion_brand_theme_matterhorn_parse_product_node( $node_xml );

			if ( empty( $product_data['product_id'] ) ) {
				++$skipped_other;
				WP_CLI::warning( sprintf( 'Skipped product at position %d — missing product_id.', $seen ) );
				continue;
			}

			$category_slug = fashion_brand_theme_matterhorn_map_category_slug( $product_data['category'] );

			if ( null === $category_slug ) {
				++$skipped_unmapped;
				WP_CLI::log(
					sprintf(
						'Skipped #%s — category not mapped (%s).',
						$product_data['product_id'],
						fashion_brand_theme_matterhorn_strip_category_prefix( $product_data['category'] )
					)
				);
				continue;
			}

			$cat_term = get_term_by( 'slug', $category_slug, 'product_cat' );

			if ( ! $cat_term || is_wp_error( $cat_term ) ) {
				++$skipped_other;
				WP_CLI::warning(
					sprintf(
						'Skipped #%s — product_cat slug "%s" does not exist. Create the canonical category first.',
						$product_data['product_id'],
						$category_slug
					)
				);
				continue;
			}

			try {
				$result = fashion_brand_theme_matterhorn_upsert_product( $product_data, (int) $cat_term->term_id );

				if ( 'created' === $result['action'] ) {
					++$imported;
				} else {
					++$updated;
				}

				WP_CLI::log(
					sprintf(
						'Imported %d / %d — %s #%s (%s) [%s → %s]',
						$imported + $updated + $offset,
						$total,
						$result['action'],
						$product_data['product_id'],
						$result['sku'],
						$result['type'],
						$category_slug
					)
				);
			} catch ( Exception $e ) {
				++$errors;
				WP_CLI::warning(
					sprintf(
						'Error on product_id %s: %s',
						$product_data['product_id'],
						$e->getMessage()
					)
				);
			}

			// Free memory between products.
			unset( $node_xml, $product_data, $result );
			if ( 0 === ( ( $imported + $updated ) % 25 ) ) {
				wp_cache_flush();
			}
		}

		$reader->close();

		WP_CLI::success(
			sprintf(
				'Done. Imported %d, updated %d, skipped (unmapped category) %d, skipped (other) %d, errors %d.',
				$imported,
				$updated,
				$skipped_unmapped,
				$skipped_other,
				$errors
			)
		);
	}
}

WP_CLI::add_command( 'matterhorn', 'Fashion_Brand_Theme_Matterhorn_CLI' );

/**
 * Absolute path to the Matterhorn feed file.
 *
 * @return string
 */
function fashion_brand_theme_matterhorn_feed_path() {
	$uploads = wp_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . 'matterhorn';

	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
	}

	return trailingslashit( $dir ) . 'feed-woocommerce.xml';
}

/**
 * Count <product> nodes with a lightweight XMLReader pass.
 *
 * @param string $feed Feed path.
 * @return int
 */
function fashion_brand_theme_matterhorn_count_products( $feed ) {
	$count  = 0;
	$reader = new XMLReader();

	if ( ! $reader->open( $feed, null, LIBXML_NONET | LIBXML_COMPACT ) ) {
		return 0;
	}

	while ( $reader->read() ) {
		if ( XMLReader::ELEMENT === $reader->nodeType && 'product' === $reader->localName ) {
			++$count;
		}
	}

	$reader->close();

	return $count;
}

/**
 * Ensure a global Brand attribute exists (pa_brand).
 *
 * @return void
 */
function fashion_brand_theme_matterhorn_ensure_brand_attribute() {
	if ( ! function_exists( 'wc_create_attribute' ) ) {
		return;
	}

	$existing = wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_name' );

	if ( ! in_array( 'brand', $existing, true ) ) {
		wc_create_attribute(
			array(
				'name'         => 'Brand',
				'slug'         => 'brand',
				'type'         => 'select',
				'order_by'     => 'menu_order',
				'has_archives' => false,
			)
		);
		delete_transient( 'wc_attribute_taxonomies' );
	}

	if ( ! taxonomy_exists( 'pa_brand' ) ) {
		register_taxonomy(
			'pa_brand',
			array( 'product' ),
			array(
				'hierarchical' => false,
				'label'        => 'Brand',
				'query_var'    => true,
				'rewrite'      => false,
				'show_ui'      => false,
				'public'       => false,
			)
		);
	}
}

/**
 * Parse one <product> outer XML string into a structured array.
 *
 * @param string $node_xml Outer XML for a single product.
 * @return array<string, mixed>
 */
function fashion_brand_theme_matterhorn_parse_product_node( $node_xml ) {
	$previous = libxml_use_internal_errors( true );
	$xml      = simplexml_load_string( $node_xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	if ( ! $xml ) {
		return array();
	}

	$attrs      = $xml->attributes();
	$product_id = isset( $attrs['product_id'] ) ? (string) $attrs['product_id'] : '';

	$sizes = array();

	if ( isset( $xml->sizes->size ) ) {
		foreach ( $xml->sizes->size as $size_node ) {
			$size_attrs = $size_node->attributes();
			$sizes[]    = array(
				'name'    => isset( $size_attrs['name'] ) ? (string) $size_attrs['name'] : '',
				'count'   => isset( $size_attrs['count'] ) ? (int) $size_attrs['count'] : 0,
				'uid'     => isset( $size_attrs['uid'] ) ? (string) $size_attrs['uid'] : '',
				'enabled' => isset( $size_attrs['enabled'] ) ? (string) $size_attrs['enabled'] : '1',
			);
		}
	}

	$photos = array();

	if ( isset( $xml->photos->photo ) ) {
		foreach ( $xml->photos->photo as $photo ) {
			$url = trim( (string) $photo );
			if ( '' !== $url ) {
				$photos[] = $url;
			}
		}
	}

	return array(
		'product_id'        => $product_id,
		'name'              => isset( $xml->name ) ? trim( (string) $xml->name ) : '',
		'description'       => isset( $xml->description ) ? (string) $xml->description : '',
		'code'              => isset( $xml->code ) ? trim( (string) $xml->code ) : '',
		'producer'          => isset( $xml->producer ) ? trim( (string) $xml->producer ) : '',
		'photos'            => $photos,
		'category'          => isset( $xml->category ) ? trim( (string) $xml->category ) : '',
		'price_netto'       => isset( $xml->price_netto ) ? (float) (string) $xml->price_netto : 0.0,
		'sale'              => isset( $xml->sale ) ? (string) $xml->sale : '0',
		'sale_price_netto'  => isset( $xml->sale_price_netto ) ? (float) (string) $xml->sale_price_netto : 0.0,
		'sizes'             => $sizes,
		'color'             => isset( $xml->color ) ? trim( (string) $xml->color ) : '',
		'available'         => isset( $xml->available ) ? trim( (string) $xml->available ) : '',
	);
}

/**
 * Strip size tables / prod_data blocks; keep prose.
 *
 * @param string $html Raw description HTML.
 * @return string
 */
function fashion_brand_theme_matterhorn_clean_description( $html ) {
	$html = (string) $html;

	$html = preg_replace( '/<table\b[^>]*>.*?<\/table>/is', '', $html );
	$html = preg_replace( '/<div[^>]*class=(["\'])[^"\']*\bprod_data\b[^"\']*\1[^>]*>.*?<\/div>/is', '', $html );

	return trim( wp_kses_post( $html ) );
}

/**
 * Apply markup multiplier and format as WooCommerce price string.
 *
 * @param float $netto Netto price from feed.
 * @return string
 */
function fashion_brand_theme_matterhorn_apply_markup( $netto ) {
	$price = (float) $netto * (float) MATTERHORN_PRICE_MARKUP_MULTIPLIER;

	return wc_format_decimal( $price, wc_get_price_decimals() );
}

/**
 * Strip leading "VOOR HAAR" / "Women's fashion" segments from a feed category path.
 *
 * @param string $path Raw feed category path.
 * @return string Normalized path with leading slash, e.g. "/jurken/dagelijks jurken".
 */
function fashion_brand_theme_matterhorn_strip_category_prefix( $path ) {
	$path = html_entity_decode( (string) $path, ENT_QUOTES, 'UTF-8' );
	$path = str_replace( '|', '/', $path );
	$parts = array_values(
		array_filter(
			array_map( 'trim', explode( '/', $path ) ),
			static function ( $part ) {
				return '' !== $part;
			}
		)
	);

	$strip = array( 'voor haar', "women's fashion", 'womens fashion' );

	while ( count( $parts ) >= 1 && in_array( strtolower( $parts[0] ), $strip, true ) ) {
		array_shift( $parts );
	}

	if ( empty( $parts ) ) {
		return '';
	}

	return '/' . implode( '/', $parts );
}

/**
 * Map stripped feed category paths to the theme's 6 canonical product_cat slugs.
 * Keys must match the path after stripping "/VOOR HAAR/Women's fashion".
 *
 * @return array<string, string>
 */
function fashion_brand_theme_matterhorn_category_map() {
	return array(
		'/t-shirts'                                             => 't-shirts',
		'/Shirts, Blouses/Tops, T-shirts, T-shirts'             => 't-shirts',
		'/Shirts, Blouses/Tops, T-shirts, T-shirts/T-shirts / Tops' => 't-shirts',
		'/Grote maten mode/Grote maten t-shirt'                 => 't-shirts',
		'/Shirts, Blouses/Blouses, tunieken'                    => 'shirts',
		'/Shirts, Blouses/shirts Vrouwen'                       => 'shirts',
		'/Shirts, Blouses/lichaam'                              => 'shirts',
		'/Grote maten mode/Blouses in grote maten'              => 'shirts',
		'/Broeken, shorts'                                      => 'pants',
		'/Broeken, shorts/Broeken elegante'                     => 'pants',
		'/Broeken, shorts/Shorts, bijgesneden'                  => 'pants',
		'/Broeken, shorts/lange broek'                          => 'pants',
		'/Broeken, shorts/leggings'                             => 'pants',
		'/Broeken, shorts/overalls'                             => 'pants',
		'/Broeken, shorts/trainingsbroek'                       => 'pants',
		'/jurken/Formele jurken, cocktail'                      => 'dresses',
		'/jurken/avondjurken'                                   => 'dresses',
		'/jurken/dagelijks jurken'                              => 'dresses',
		'/Grote maten mode/Grote maten jurken'                  => 'dresses',
		'/Truien/Truien'                                        => 'knitwear',
		'/Truien/Truien, Turtle-Necks'                          => 'knitwear',
		'/Grote maten mode/Dames sweaters groot formaat'        => 'knitwear',
		'/Grote maten mode/Grote maten damessweater'            => 'knitwear',
		// No feed category maps to 'hoodies'.
	);
}

/**
 * Resolve a feed category path to a canonical product_cat slug, or null if unmapped.
 *
 * @param string $path Raw feed category path.
 * @return string|null Canonical slug, or null when the product must be skipped.
 */
function fashion_brand_theme_matterhorn_map_category_slug( $path ) {
	$stripped = fashion_brand_theme_matterhorn_strip_category_prefix( $path );
	$map      = fashion_brand_theme_matterhorn_category_map();

	if ( '' === $stripped || ! isset( $map[ $stripped ] ) ) {
		return null;
	}

	return $map[ $stripped ];
}

/**
 * Ensure a taxonomy term exists; return its slug.
 *
 * @param string $taxonomy Taxonomy name.
 * @param string $name     Term display name.
 * @return string Term slug, or empty string.
 */
function fashion_brand_theme_matterhorn_ensure_term( $taxonomy, $name ) {
	$name = trim( (string) $name );

	if ( '' === $name || ! taxonomy_exists( $taxonomy ) ) {
		return '';
	}

	$slug = sanitize_title( $name );
	$term = get_term_by( 'slug', $slug, $taxonomy );

	if ( $term && ! is_wp_error( $term ) ) {
		return $term->slug;
	}

	$inserted = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );

	if ( is_wp_error( $inserted ) ) {
		$existing = get_term_by( 'name', $name, $taxonomy );
		return ( $existing && ! is_wp_error( $existing ) ) ? $existing->slug : '';
	}

	$term = get_term( (int) $inserted['term_id'], $taxonomy );

	return ( $term && ! is_wp_error( $term ) ) ? $term->slug : $slug;
}

/**
 * Find an existing product ID by Matterhorn product_id meta.
 *
 * @param string $matterhorn_id Feed product_id.
 * @return int
 */
function fashion_brand_theme_matterhorn_find_product_id( $matterhorn_id ) {
	$ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_matterhorn_product_id',
			'meta_value'     => (string) $matterhorn_id,
		)
	);

	return ! empty( $ids[0] ) ? (int) $ids[0] : 0;
}

/**
 * Sideload (or reuse) an image by source URL; store _matterhorn_source_url.
 *
 * @param string $url        Remote image URL.
 * @param int    $product_id Parent product ID.
 * @return int Attachment ID, or 0.
 */
function fashion_brand_theme_matterhorn_sideload_image( $url, $product_id ) {
	$url = esc_url_raw( trim( (string) $url ) );

	if ( '' === $url ) {
		return 0;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_matterhorn_source_url',
			'meta_value'     => $url,
		)
	);

	if ( ! empty( $existing[0] ) ) {
		return (int) $existing[0];
	}

	$attachment_id = media_sideload_image( $url, $product_id, null, 'id' );

	if ( is_wp_error( $attachment_id ) ) {
		WP_CLI::warning( sprintf( 'Image sideload failed (%s): %s', $url, $attachment_id->get_error_message() ) );
		return 0;
	}

	$attachment_id = (int) $attachment_id;

	if ( $attachment_id > 0 ) {
		update_post_meta( $attachment_id, '_matterhorn_source_url', $url );
	}

	return $attachment_id;
}

/**
 * Create or update a WooCommerce product from parsed feed data.
 *
 * @param array<string, mixed> $data             Parsed product.
 * @param int                  $category_term_id Existing product_cat term ID to assign.
 * @return array{action:string,sku:string,type:string,id:int}
 * @throws Exception On fatal product save failure.
 */
function fashion_brand_theme_matterhorn_upsert_product( array $data, $category_term_id ) {
	$matterhorn_id = (string) $data['product_id'];
	$sku           = (string) $data['code'];
	$existing_id   = fashion_brand_theme_matterhorn_find_product_id( $matterhorn_id );

	$enabled_sizes = array_values(
		array_filter(
			$data['sizes'],
			static function ( $size ) {
				$enabled = isset( $size['enabled'] ) ? strtolower( (string) $size['enabled'] ) : '1';
				$name    = isset( $size['name'] ) ? trim( (string) $size['name'] ) : '';

				return '' !== $name && ! in_array( $enabled, array( '0', 'false', 'no' ), true );
			}
		)
	);

	$is_variable = count( $data['sizes'] ) > 1;
	$type        = $is_variable ? 'variable' : 'simple';
	$is_visible  = ( 'visible' === strtolower( trim( (string) $data['available'] ) ) );
	$status      = $is_visible ? 'publish' : 'draft';
	$catalog     = $is_visible ? 'visible' : 'hidden';

	$regular_price = fashion_brand_theme_matterhorn_apply_markup( $data['price_netto'] );
	$on_sale       = ( '1' === (string) $data['sale'] || 1 === (int) $data['sale'] ) && (float) $data['sale_price_netto'] > 0;
	$sale_price    = $on_sale ? fashion_brand_theme_matterhorn_apply_markup( $data['sale_price_netto'] ) : '';

	$description = fashion_brand_theme_matterhorn_clean_description( $data['description'] );

	if ( $existing_id ) {
		$product = wc_get_product( $existing_id );
		$action  = 'updated';

		if ( ! $product ) {
			$existing_id = 0;
		} elseif ( $product->get_type() !== $type ) {
			// Type mismatch: remove old product and recreate under same Matterhorn ID.
			wp_delete_post( $existing_id, true );
			$existing_id = 0;
			$product     = null;
		}
	}

	if ( ! $existing_id ) {
		$product = $is_variable ? new WC_Product_Variable() : new WC_Product_Simple();
		$action  = 'created';
	}

	$product->set_name( $data['name'] );
	$product->set_status( $status );
	$product->set_catalog_visibility( $catalog );
	$product->set_description( $description );
	$product->set_short_description( '' );

	if ( '' !== $sku ) {
		$product->set_sku( $sku );
	}

	$product->update_meta_data( '_matterhorn_product_id', $matterhorn_id );
	$product->update_meta_data( '_matterhorn_brand', $data['producer'] );

	// Attributes: size (variation when variable), color (filter), brand (filter).
	$attributes   = array();
	$size_slugs   = array();
	$color_slug   = fashion_brand_theme_matterhorn_ensure_term( 'pa_color', $data['color'] );
	$brand_slug   = fashion_brand_theme_matterhorn_ensure_term( 'pa_brand', $data['producer'] );

	foreach ( $enabled_sizes as $size ) {
		$slug = fashion_brand_theme_matterhorn_ensure_term( 'pa_size', $size['name'] );
		if ( '' !== $slug ) {
			$size_slugs[] = $slug;
		}
	}
	$size_slugs = array_values( array_unique( $size_slugs ) );

	if ( ! empty( $size_slugs ) && taxonomy_exists( 'pa_size' ) ) {
		$size_attr = new WC_Product_Attribute();
		$size_attr->set_id( wc_attribute_taxonomy_id_by_name( 'pa_size' ) );
		$size_attr->set_name( 'pa_size' );
		$size_attr->set_options( $size_slugs );
		$size_attr->set_visible( true );
		$size_attr->set_variation( $is_variable );
		$attributes[] = $size_attr;
	}

	if ( '' !== $color_slug && taxonomy_exists( 'pa_color' ) ) {
		$color_attr = new WC_Product_Attribute();
		$color_attr->set_id( wc_attribute_taxonomy_id_by_name( 'pa_color' ) );
		$color_attr->set_name( 'pa_color' );
		$color_attr->set_options( array( $color_slug ) );
		$color_attr->set_visible( true );
		$color_attr->set_variation( false );
		$attributes[] = $color_attr;
	}

	if ( '' !== $brand_slug && taxonomy_exists( 'pa_brand' ) ) {
		$brand_attr = new WC_Product_Attribute();
		$brand_attr->set_id( wc_attribute_taxonomy_id_by_name( 'pa_brand' ) );
		$brand_attr->set_name( 'pa_brand' );
		$brand_attr->set_options( array( $brand_slug ) );
		$brand_attr->set_visible( true );
		$brand_attr->set_variation( false );
		$attributes[] = $brand_attr;
	}

	$product->set_attributes( $attributes );

	if ( ! $is_variable ) {
		$product->set_regular_price( $regular_price );
		$product->set_sale_price( $sale_price );
		$product->set_manage_stock( true );

		$stock = 0;
		if ( ! empty( $enabled_sizes[0] ) ) {
			$stock = max( 0, (int) $enabled_sizes[0]['count'] );
		}
		$product->set_stock_quantity( $stock );
		$product->set_stock_status( $stock > 0 ? 'instock' : 'outofstock' );
	} else {
		$product->set_manage_stock( false );
	}

	$product_id = $product->save();

	if ( ! $product_id ) {
		throw new Exception( 'WooCommerce product save returned empty ID.' );
	}

	// Re-fetch for type-specific work.
	$product = wc_get_product( $product_id );

	if ( ! empty( $size_slugs ) ) {
		wp_set_object_terms( $product_id, $size_slugs, 'pa_size' );
	}
	if ( '' !== $color_slug ) {
		wp_set_object_terms( $product_id, array( $color_slug ), 'pa_color' );
	}
	if ( '' !== $brand_slug ) {
		wp_set_object_terms( $product_id, array( $brand_slug ), 'pa_brand' );
	}

	$cat_id = (int) $category_term_id;
	if ( $cat_id > 0 ) {
		wp_set_object_terms( $product_id, array( $cat_id ), 'product_cat' );
	}

	// Images.
	$image_ids = array();
	foreach ( $data['photos'] as $photo_url ) {
		$aid = fashion_brand_theme_matterhorn_sideload_image( $photo_url, $product_id );
		if ( $aid > 0 ) {
			$image_ids[] = $aid;
		}
	}

	if ( ! empty( $image_ids ) ) {
		$product->set_image_id( $image_ids[0] );
		$gallery = array_slice( $image_ids, 1 );
		$product->set_gallery_image_ids( $gallery );
		$product->save();
	}

	if ( $is_variable && $product instanceof WC_Product_Variable ) {
		fashion_brand_theme_matterhorn_sync_variations(
			$product,
			$enabled_sizes,
			$regular_price,
			$sale_price,
			$sku
		);
	}

	return array(
		'action' => $action,
		'sku'    => $sku,
		'type'   => $type,
		'id'     => $product_id,
	);
}

/**
 * Sync size variations for a variable product.
 *
 * @param WC_Product_Variable $product       Parent product.
 * @param array               $enabled_sizes Enabled size rows from feed.
 * @param string              $regular_price Regular price string.
 * @param string              $sale_price    Sale price string (may be empty).
 * @param string              $parent_sku    Parent SKU for variation SKU suffix.
 * @return void
 */
function fashion_brand_theme_matterhorn_sync_variations( WC_Product_Variable $product, array $enabled_sizes, $regular_price, $sale_price, $parent_sku ) {
	$parent_id      = $product->get_id();
	$existing_vars  = $product->get_children();
	$kept_ids       = array();
	$uid_to_var     = array();

	foreach ( $existing_vars as $variation_id ) {
		$uid = get_post_meta( $variation_id, '_matterhorn_size_uid', true );
		if ( '' !== (string) $uid ) {
			$uid_to_var[ (string) $uid ] = (int) $variation_id;
		}
	}

	foreach ( $enabled_sizes as $size ) {
		$size_name = trim( (string) $size['name'] );
		$size_slug = fashion_brand_theme_matterhorn_ensure_term( 'pa_size', $size_name );

		if ( '' === $size_slug ) {
			continue;
		}

		$uid = (string) $size['uid'];
		$vid = ( '' !== $uid && isset( $uid_to_var[ $uid ] ) ) ? $uid_to_var[ $uid ] : 0;

		if ( $vid ) {
			$variation = wc_get_product( $vid );
			if ( ! $variation || ! $variation instanceof WC_Product_Variation ) {
				$variation = new WC_Product_Variation();
				$vid       = 0;
			}
		} else {
			$variation = new WC_Product_Variation();
		}

		$variation->set_parent_id( $parent_id );
		$variation->set_attributes( array( 'pa_size' => $size_slug ) );
		$variation->set_status( 'publish' );
		$variation->set_regular_price( $regular_price );
		$variation->set_sale_price( $sale_price );
		$variation->set_manage_stock( true );

		$qty = max( 0, (int) $size['count'] );
		$variation->set_stock_quantity( $qty );
		$variation->set_stock_status( $qty > 0 ? 'instock' : 'outofstock' );

		if ( '' !== $parent_sku ) {
			$variation->set_sku( $parent_sku . '-' . strtoupper( $size_slug ) );
		}

		if ( '' !== $uid ) {
			$variation->update_meta_data( '_matterhorn_size_uid', $uid );
		}

		$new_id = $variation->save();
		if ( $new_id ) {
			$kept_ids[] = (int) $new_id;
		}
	}

	// Remove stale variations no longer in the feed.
	foreach ( $existing_vars as $variation_id ) {
		if ( ! in_array( (int) $variation_id, $kept_ids, true ) ) {
			wp_delete_post( (int) $variation_id, true );
		}
	}

	WC_Product_Variable::sync( $parent_id );
}
