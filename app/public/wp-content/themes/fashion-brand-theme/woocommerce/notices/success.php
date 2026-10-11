<?php
/**
 * Show success notices — themed with Lucide icon.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $notices ) {
	return;
}

?>

<?php foreach ( $notices as $notice ) : ?>
	<div class="woocommerce-message ww-notice ww-notice--success"<?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="alert">
		<span class="ww-notice__icon" aria-hidden="true">
			<?php fashion_brand_theme_icon( 'check-circle', array( 'width' => 18, 'height' => 18 ) ); ?>
		</span>
		<div class="ww-notice__body">
			<?php echo wc_kses_notice( $notice['notice'] ); ?>
		</div>
	</div>
<?php endforeach; ?>
