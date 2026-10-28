<?php
/**
 * Site footer — dark multi-column layout.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! fashion_brand_theme_is_setting_enabled( 'footer_visible' ) ) {
	return;
}

$copyright_text = fashion_brand_theme_get_setting( 'footer_copyright_text' );
$tagline        = fashion_brand_theme_get_setting( 'shop_footer_tagline' );
$categories     = fashion_brand_theme_get_product_category_slugs();
$monogram_uri   = fashion_brand_theme_get_monogram_uri( 'primary' );
$logo_uri       = fashion_brand_theme_get_logo_uri( 'primary' );

$resolve_page = static function ( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : '#';
};

$info_links = array(
	array( 'label' => __( 'About', 'fashion-brand-theme' ), 'url' => $resolve_page( 'about' ) ),
	array( 'label' => __( 'Journal', 'fashion-brand-theme' ), 'url' => $resolve_page( 'journal' ) ),
	array( 'label' => __( 'Shipping & Returns', 'fashion-brand-theme' ), 'url' => $resolve_page( 'refund_returns' ) ),
	array( 'label' => __( 'FAQ', 'fashion-brand-theme' ), 'url' => $resolve_page( 'faq' ) ),
	array( 'label' => __( 'Contact', 'fashion-brand-theme' ), 'url' => $resolve_page( 'contact' ) ),
);

$privacy_page = get_page_by_path( 'privacy-policy' );
$privacy      = $privacy_page ? get_permalink( $privacy_page ) : ( get_privacy_policy_url() ?: '#' );
$terms_page   = get_page_by_path( 'terms' );
$terms        = $terms_page ? get_permalink( $terms_page ) : '#';
?>
<footer id="colophon" class="site-footer site-footer--dark">
	<div class="site-footer__grid">
		<div class="site-footer__col site-footer__col--brand">
			<a class="site-footer__brand-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php if ( $monogram_uri ) : ?>
					<img class="site-footer__monogram" src="<?php echo esc_url( $monogram_uri ); ?>" alt="" width="40" height="40" decoding="async" loading="lazy" />
				<?php endif; ?>
				<span class="site-footer__wordmark"><?php echo esc_html( fashion_brand_theme_get_display_label() ); ?></span>
			</a>
			<?php if ( is_string( $tagline ) && '' !== trim( $tagline ) ) : ?>
				<p class="site-footer__tagline"><?php echo esc_html( $tagline ); ?></p>
			<?php endif; ?>
			<?php if ( fashion_brand_theme_is_setting_enabled( 'footer_social_enabled' ) && ! empty( fashion_brand_theme_get_social_links() ) ) : ?>
				<nav class="site-footer__social" aria-label="<?php esc_attr_e( 'Social links', 'fashion-brand-theme' ); ?>">
					<?php
					fashion_brand_theme_render_social_icons(
						array(
							'class' => 'site-footer__social-list social-icons',
						)
					);
					?>
				</nav>
			<?php endif; ?>
		</div>

		<div class="site-footer__col">
			<h2 class="site-footer__heading"><?php esc_html_e( 'Shop', 'fashion-brand-theme' ); ?></h2>
			<ul class="site-footer__list">
				<?php foreach ( $categories as $slug => $label ) : ?>
					<li><a href="<?php echo esc_url( fashion_brand_theme_get_product_category_url( $slug ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="site-footer__col">
			<h2 class="site-footer__heading"><?php esc_html_e( 'Information', 'fashion-brand-theme' ); ?></h2>
			<ul class="site-footer__list">
				<?php foreach ( $info_links as $item ) : ?>
					<li>
						<a href="<?php echo esc_url( $item['url'] ); ?>">
							<?php echo esc_html( $item['label'] ); ?>
							<?php if ( '#' === $item['url'] ) : ?>
								<span class="screen-reader-text"><?php esc_html_e( '(page placeholder)', 'fashion-brand-theme' ); ?></span>
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="site-footer__col site-footer__col--newsletter">
			<h2 class="site-footer__heading"><?php esc_html_e( 'Newsletter', 'fashion-brand-theme' ); ?></h2>
			<p class="site-footer__newsletter-note"><?php esc_html_e( 'Join for field notes. Form is visual-only until an email provider is connected.', 'fashion-brand-theme' ); ?></p>
			<form class="site-footer__newsletter" action="#" method="post" onsubmit="return false;" aria-label="<?php esc_attr_e( 'Newsletter signup', 'fashion-brand-theme' ); ?>">
				<label class="screen-reader-text" for="footer-newsletter-email"><?php esc_html_e( 'Email', 'fashion-brand-theme' ); ?></label>
				<input id="footer-newsletter-email" type="email" name="email" placeholder="<?php esc_attr_e( 'Email address', 'fashion-brand-theme' ); ?>" autocomplete="email" />
				<button type="submit"><?php esc_html_e( 'Join', 'fashion-brand-theme' ); ?></button>
			</form>
		</div>
	</div>

	<div class="site-footer__bottom">
		<p class="site-footer__copyright">
			<?php if ( is_string( $copyright_text ) && '' !== trim( $copyright_text ) ) : ?>
				<?php echo esc_html( $copyright_text ); ?>
			<?php else : ?>
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( fashion_brand_theme_get_display_label() ); ?>
			<?php endif; ?>
		</p>
		<p class="site-footer__legal">
			<a href="<?php echo esc_url( $privacy ?: '#' ); ?>"><?php esc_html_e( 'Privacy Policy', 'fashion-brand-theme' ); ?></a>
			<a href="<?php echo esc_url( $terms ); ?>"><?php esc_html_e( 'Terms', 'fashion-brand-theme' ); ?></a>
			<span class="site-footer__credit">
				<?php
				echo wp_kses(
					sprintf(
						/* translators: %s: agency website URL */
						__( 'Crafted by <a href="%s" target="_blank" rel="noopener">OGC NewFinity</a>', 'fashion-brand-theme' ),
						esc_url( 'https://ogcnewfinity.com' )
					),
					array(
						'a' => array(
							'href'   => true,
							'target' => true,
							'rel'    => true,
						),
					)
				);
				?>
			</span>
		</p>
	</div>
</footer>
