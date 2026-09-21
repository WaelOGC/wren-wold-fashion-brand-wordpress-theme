<?php
/**
 * Shop page hero images — Meta Box + front-end helper.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve desktop/mobile shop hero image URLs.
 *
 * Priority: Shop page attachment meta → theme default files (if present).
 * If only one URL is available it is used for both breakpoints.
 *
 * @return array{desktop: string, mobile: string}
 */
function fashion_brand_theme_get_shop_hero_images() {
	$desktop = '';
	$mobile  = '';

	$shop_id = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0;

	if ( $shop_id > 0 ) {
		$desktop_id = (int) get_post_meta( $shop_id, '_fbt_shop_hero_desktop_id', true );
		$mobile_id  = (int) get_post_meta( $shop_id, '_fbt_shop_hero_mobile_id', true );

		if ( $desktop_id > 0 ) {
			$url = wp_get_attachment_image_url( $desktop_id, 'full' );
			if ( $url ) {
				$desktop = $url;
			}
		}

		if ( $mobile_id > 0 ) {
			$url = wp_get_attachment_image_url( $mobile_id, 'full' );
			if ( $url ) {
				$mobile = $url;
			}
		}
	}

	if ( '' === $desktop ) {
		$rel = 'assets/images/shop-photography/hero-shop.jpg';
		if ( file_exists( get_theme_file_path( $rel ) ) ) {
			$desktop = get_theme_file_uri( $rel );
		}
	}

	if ( '' === $mobile ) {
		$rel = 'assets/images/shop-photography/hero-shop-Smartphones.jpg';
		if ( file_exists( get_theme_file_path( $rel ) ) ) {
			$mobile = get_theme_file_uri( $rel );
		}
	}

	if ( $desktop && ! $mobile ) {
		$mobile = $desktop;
	} elseif ( $mobile && ! $desktop ) {
		$desktop = $mobile;
	}

	return array(
		'desktop' => $desktop,
		'mobile'  => $mobile,
	);
}

/**
 * Whether the given post ID is the WooCommerce Shop page.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function fashion_brand_theme_is_shop_page_id( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'wc_get_page_id' ) ) {
		return false;
	}

	$shop_id = (int) wc_get_page_id( 'shop' );

	return $shop_id > 0 && $post_id === $shop_id;
}

/**
 * Register the Shop Hero Images meta box (Shop page only).
 *
 * @param string  $post_type Post type.
 * @param WP_Post $post      Post object.
 * @return void
 */
function fashion_brand_theme_shop_hero_add_meta_box( $post_type, $post ) {
	if ( 'page' !== $post_type || ! ( $post instanceof WP_Post ) ) {
		return;
	}

	if ( ! fashion_brand_theme_is_shop_page_id( (int) $post->ID ) ) {
		return;
	}

	add_meta_box(
		'fbt_shop_hero_images',
		__( 'Shop Hero Images', 'fashion-brand-theme' ),
		'fashion_brand_theme_shop_hero_render_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'fashion_brand_theme_shop_hero_add_meta_box', 10, 2 );

/**
 * Render one image picker field.
 *
 * @param string $key   Field key (desktop|mobile).
 * @param string $label Field label.
 * @param string $help  Help text.
 * @param int    $id    Attachment ID.
 * @return void
 */
function fashion_brand_theme_shop_hero_render_field( $key, $label, $help, $id ) {
	$id       = (int) $id;
	$url      = $id > 0 ? wp_get_attachment_image_url( $id, 'medium' ) : '';
	$input_id = 'fbt_shop_hero_' . $key . '_id';
	?>
	<div class="fbt-shop-hero-field" data-fbt-shop-hero-field>
		<p><strong><?php echo esc_html( $label ); ?></strong></p>
		<input
			type="hidden"
			class="fbt-shop-hero-id"
			name="<?php echo esc_attr( $input_id ); ?>"
			id="<?php echo esc_attr( $input_id ); ?>"
			value="<?php echo esc_attr( (string) $id ); ?>"
		/>
		<div class="fbt-shop-hero-preview">
			<?php if ( $url ) : ?>
				<img src="<?php echo esc_url( $url ); ?>" alt="" style="max-width:100%;max-height:160px;height:auto;display:block;" />
			<?php endif; ?>
		</div>
		<p>
			<button type="button" class="button fbt-shop-hero-select"><?php esc_html_e( 'Choose image', 'fashion-brand-theme' ); ?></button>
			<button type="button" class="button fbt-shop-hero-remove" <?php echo $id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Remove', 'fashion-brand-theme' ); ?></button>
		</p>
		<p class="description"><?php echo esc_html( $help ); ?></p>
	</div>
	<?php
}

/**
 * Meta box markup.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function fashion_brand_theme_shop_hero_render_meta_box( $post ) {
	wp_nonce_field( 'fashion_brand_theme_save_shop_hero', 'fashion_brand_theme_shop_hero_nonce' );

	$desktop_id = (int) get_post_meta( $post->ID, '_fbt_shop_hero_desktop_id', true );
	$mobile_id  = (int) get_post_meta( $post->ID, '_fbt_shop_hero_mobile_id', true );
	?>
	<div class="fbt-shop-hero-fields" style="display:grid;gap:24px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
		<?php
		fashion_brand_theme_shop_hero_render_field(
			'desktop',
			__( 'Desktop image', 'fashion-brand-theme' ),
			__( 'Landscape, about 2400×700. Keep the subject on the right and the left ~45% light — the headline sits there.', 'fashion-brand-theme' ),
			$desktop_id
		);
		fashion_brand_theme_shop_hero_render_field(
			'mobile',
			__( 'Mobile image', 'fashion-brand-theme' ),
			__( 'Portrait or square. Keep the subject on the right; text sits on the left.', 'fashion-brand-theme' ),
			$mobile_id
		);
		?>
	</div>
	<?php
}

/**
 * Enqueue media picker on the Shop page edit screen only.
 *
 * @param string $hook_suffix Current admin page hook.
 * @return void
 */
function fashion_brand_theme_shop_hero_admin_assets( $hook_suffix ) {
	if ( 'post.php' !== $hook_suffix && 'post-new.php' !== $hook_suffix ) {
		return;
	}

	$post_id = 0;
	if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post_id = absint( wp_unslash( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	} elseif ( isset( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof WP_Post ) {
		$post_id = (int) $GLOBALS['post']->ID;
	}

	if ( ! fashion_brand_theme_is_shop_page_id( $post_id ) ) {
		return;
	}

	wp_enqueue_media();

	wp_add_inline_script(
		'jquery',
		"(function ($) {
			function bindShopHeroPickers() {
				$('[data-fbt-shop-hero-field]').each(function () {
					var \$field = $(this);
					if (\$field.data('fbt-bound')) {
						return;
					}
					\$field.data('fbt-bound', true);

					var \$input = \$field.find('.fbt-shop-hero-id');
					var \$preview = \$field.find('.fbt-shop-hero-preview');
					var \$remove = \$field.find('.fbt-shop-hero-remove');

					\$field.on('click', '.fbt-shop-hero-select', function (event) {
						event.preventDefault();

						var frame = wp.media({
							title: '" . esc_js( __( 'Select shop hero image', 'fashion-brand-theme' ) ) . "',
							button: { text: '" . esc_js( __( 'Use image', 'fashion-brand-theme' ) ) . "' },
							library: { type: 'image' },
							multiple: false
						});

						frame.on('select', function () {
							var attachment = frame.state().get('selection').first().toJSON();
							var url = (attachment.sizes && attachment.sizes.medium) ? attachment.sizes.medium.url : attachment.url;
							\$input.val(attachment.id);
							\$preview.html('<img src=\"' + url + '\" alt=\"\" style=\"max-width:100%;max-height:160px;height:auto;display:block;\" />');
							\$remove.show();
						});

						frame.open();
					});

					\$remove.on('click', function (event) {
						event.preventDefault();
						\$input.val('');
						\$preview.empty();
						\$remove.hide();
					});
				});
			}

			$(bindShopHeroPickers);
		})(jQuery);"
	);
}
add_action( 'admin_enqueue_scripts', 'fashion_brand_theme_shop_hero_admin_assets' );

/**
 * Save Shop hero attachment IDs.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function fashion_brand_theme_shop_hero_save( $post_id ) {
	if ( ! isset( $_POST['fashion_brand_theme_shop_hero_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fashion_brand_theme_shop_hero_nonce'] ) ), 'fashion_brand_theme_save_shop_hero' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! fashion_brand_theme_is_shop_page_id( $post_id ) ) {
		return;
	}

	$fields = array(
		'fbt_shop_hero_desktop_id' => '_fbt_shop_hero_desktop_id',
		'fbt_shop_hero_mobile_id'  => '_fbt_shop_hero_mobile_id',
	);

	foreach ( $fields as $request_key => $meta_key ) {
		$id = isset( $_POST[ $request_key ] ) ? absint( wp_unslash( $_POST[ $request_key ] ) ) : 0;

		if ( $id > 0 && ! wp_attachment_is_image( $id ) ) {
			$id = 0;
		}

		if ( $id > 0 ) {
			update_post_meta( $post_id, $meta_key, $id );
		} else {
			delete_post_meta( $post_id, $meta_key );
		}
	}
}
add_action( 'save_post_page', 'fashion_brand_theme_shop_hero_save' );
