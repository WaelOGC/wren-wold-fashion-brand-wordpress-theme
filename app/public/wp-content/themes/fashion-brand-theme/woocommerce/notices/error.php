<?php
/**
 * Show error notices — themed with Lucide icon.
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

<ul class="woocommerce-error ww-notice ww-notice--error" role="alert">
	<?php foreach ( $notices as $notice ) : ?>
		<li class="ww-notice__row"<?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<span class="ww-notice__icon" aria-hidden="true">
				<?php fashion_brand_theme_icon( 'circle-alert', array( 'width' => 18, 'height' => 18 ) ); ?>
			</span>
			<span class="ww-notice__body">
				<?php echo wc_kses_notice( $notice['notice'] ); ?>
			</span>
		</li>
	<?php endforeach; ?>
</ul>
