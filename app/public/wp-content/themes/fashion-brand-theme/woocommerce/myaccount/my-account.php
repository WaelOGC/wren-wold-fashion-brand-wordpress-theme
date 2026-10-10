<?php
/**
 * My Account page shell.
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

<div class="acct-page">
	<h1 class="acct-title"><?php esc_html_e( 'My Account', 'fashion-brand-theme' ); ?></h1>
	<div class="acct-layout">
		<?php do_action( 'woocommerce_account_navigation' ); ?>
		<div class="acct-content woocommerce-MyAccount-content">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>
	</div>
</div>
