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

$user       = wp_get_current_user();
$first_name = $user->first_name ? $user->first_name : $user->display_name;
?>
<div class="ww-account">
	<header class="hero">
		<div>
			<div class="hero-kicker"><?php esc_html_e( 'WREN WOLD / Member space', 'fashion-brand-theme' ); ?></div>
			<h1><?php esc_html_e( 'Your space.', 'fashion-brand-theme' ); ?><br><em><?php esc_html_e( 'Your pace.', 'fashion-brand-theme' ); ?></em></h1>
		</div>
		<div class="hero-aside">
			<?php
			if ( $first_name ) {
				printf(
					/* translators: %s: customer first name */
					esc_html__( '%1$s, everything in its place. Manage your orders, saved pieces and personal details from one considered space.', 'fashion-brand-theme' ),
					esc_html( $first_name )
				);
			} else {
				esc_html_e( 'Everything in its place. Manage your orders, saved pieces and personal details from one considered space.', 'fashion-brand-theme' );
			}
			?>
		</div>
	</header>
	<div class="layout">
		<?php do_action( 'woocommerce_account_navigation' ); ?>
		<div class="content woocommerce-MyAccount-content">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>
	</div>
</div>
