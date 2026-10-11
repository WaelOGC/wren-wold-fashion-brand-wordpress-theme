<?php
/**
 * My Account page shell — v3 reference layout.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_user_logged_in() ) {
	do_action( 'woocommerce_account_content' );
	return;
}
?>
<div class="ww-account">
	<div class="layout">
		<?php do_action( 'woocommerce_account_navigation' ); ?>
		<div class="content woocommerce-MyAccount-content">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>
	</div>
</div>
