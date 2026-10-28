<?php
/**
 * One-off: create temporary journal test posts for layout verification.
 * Posts are published briefly for front-end checks; set to draft at the end of this script
 * unless --keep-published is passed.
 *
 * Usage (Local PHP + site php.ini):
 *   php.exe -c ".../php.ini" seed-blog-test-posts.php
 *   php.exe -c ".../php.ini" seed-blog-test-posts.php --keep-published
 *   php.exe -c ".../php.ini" seed-blog-test-posts.php --draft-only
 *
 * @package Fashion_Brand_Theme
 */

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';
if ( ! file_exists( $wp_load ) ) {
	fwrite( STDERR, "wp-load.php not found at {$wp_load}\n" );
	exit( 1 );
}

require $wp_load;

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$keep_published = in_array( '--keep-published', $argv, true );
$draft_only     = in_array( '--draft-only', $argv, true );
$final_status   = ( $keep_published && ! $draft_only ) ? 'publish' : 'draft';
$create_status  = $draft_only ? 'draft' : 'publish';

$cat = get_term_by( 'slug', 'field-notes', 'category' );
if ( ! $cat ) {
	$created = wp_insert_term( 'Field Notes', 'category', array( 'slug' => 'field-notes' ) );
	$cat_id  = is_array( $created ) ? (int) $created['term_id'] : 0;
} else {
	$cat_id = (int) $cat->term_id;
}

$posts = array(
	array(
		'slug'    => 'ww-test-building-a-capsule-wardrobe',
		'title'   => 'Building a Capsule Wardrobe',
		'excerpt' => 'A practical approach to fewer, better pieces that work across your week.',
		'content' => "<p>A capsule wardrobe is less about owning almost nothing and more about choosing pieces that earn their place.</p>\n<p>Start with fabric and fit. Add colour only when it connects to what you already wear. The goal is a quieter morning — not a smaller identity.</p>\n<blockquote><p>Fewer pieces, chosen thoughtfully, made to work across the rhythms of a modern European life.</p></blockquote>\n<p>When in doubt, leave it out. The edit is the point.</p>",
		'image'   => 'guides/guides-canal-01.webp',
		'long'    => false,
	),
	array(
		'slug'    => 'ww-test-fabric-choices-that-last-through-seasons-and-years',
		'title'   => 'Fabric Choices That Last Through Seasons and Years of Everyday Wear',
		'excerpt' => 'Understanding materials, drape and care before you buy — so the piece still feels right next spring.',
		'content' => "<p>Good fabric does quiet work. It holds a crease when you want one, softens where the body moves, and survives a careful wash.</p>\n<h2>What to look for</h2>\n<ul>\n<li>Natural fibres with enough structure for your climate</li>\n<li>A hand-feel that still appeals after you walk away and come back</li>\n<li>Care instructions you will actually follow</li>\n</ul>\n<p>This longer title exists to stress the listing card and the single hero band. The layout should remain calm.</p>",
		'image'   => null,
		'long'    => true,
	),
);

$theme_dir = get_template_directory();
$ids       = array();

foreach ( $posts as $post_data ) {
	$existing = get_page_by_path( $post_data['slug'], OBJECT, 'post' );

	$postarr = array(
		'post_title'   => $post_data['title'],
		'post_name'    => $post_data['slug'],
		'post_content' => $post_data['content'],
		'post_excerpt' => $post_data['excerpt'],
		'post_status'  => $create_status,
		'post_type'    => 'post',
		'post_author'  => 1,
	);

	if ( $existing instanceof WP_Post ) {
		$postarr['ID'] = $existing->ID;
		$post_id       = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) ) {
		fwrite( STDERR, $post_id->get_error_message() . "\n" );
		continue;
	}

	if ( $cat_id ) {
		wp_set_post_categories( $post_id, array( $cat_id ) );
	}

	if ( $post_data['image'] && ! has_post_thumbnail( $post_id ) ) {
		$src = $theme_dir . '/assets/images/homepage-photography/' . $post_data['image'];
		if ( ! file_exists( $src ) ) {
			$src = $theme_dir . '/assets/images/' . $post_data['image'];
		}
		if ( file_exists( $src ) ) {
			$tmp = wp_tempnam( basename( $src ) );
			copy( $src, $tmp );
			$file_array = array(
				'name'     => basename( $src ),
				'tmp_name' => $tmp,
			);
			$attach_id = media_handle_sideload( $file_array, $post_id );
			if ( ! is_wp_error( $attach_id ) ) {
				set_post_thumbnail( $post_id, $attach_id );
			} else {
				@unlink( $tmp );
				fwrite( STDERR, 'Thumbnail skip: ' . $attach_id->get_error_message() . "\n" );
			}
		}
	}

	if ( 'publish' === $create_status && 'draft' === $final_status ) {
		// Leave published for verification when --keep-published; otherwise caller will draft after checking.
	}

	$ids[] = array(
		'id'     => $post_id,
		'slug'   => $post_data['slug'],
		'status' => get_post_status( $post_id ),
		'url'    => get_permalink( $post_id ),
	);
}

if ( 'draft' === $final_status && 'publish' === $create_status ) {
	foreach ( $ids as $row ) {
		wp_update_post(
			array(
				'ID'          => $row['id'],
				'post_status' => 'draft',
			)
		);
	}
	foreach ( $ids as &$row ) {
		$row['status'] = 'draft';
	}
	unset( $row );
}

$posts_page = (int) get_option( 'page_for_posts' );
echo wp_json_encode(
	array(
		'posts'      => $ids,
		'posts_page' => $posts_page ? get_permalink( $posts_page ) : null,
		'home'       => home_url( '/' ),
		'blog_url'   => $posts_page ? get_permalink( $posts_page ) : home_url( '/?post_type=post' ),
		'note'       => $keep_published ? 'left published' : 'final status draft',
	),
	JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
) . "\n";
