<?php
/**
 * Lost password confirmation — themed auth split.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || exit;
?>

<section class="auth auth--lost-password-confirm">
	<div class="auth-frame">
		<?php fashion_brand_theme_render_auth_visual(); ?>

		<div class="auth-form">
			<div class="panel active">
				<div class="eyebrow"><?php esc_html_e( 'Account recovery', 'fashion-brand-theme' ); ?></div>
				<h1><?php esc_html_e( 'Check your inbox.', 'fashion-brand-theme' ); ?></h1>

				<?php wc_print_notice( esc_html__( 'Password reset email has been sent.', 'woocommerce' ) ); ?>

				<?php do_action( 'woocommerce_before_lost_password_confirmation_message' ); ?>

				<p class="lede">
					<?php echo esc_html( apply_filters( 'woocommerce_lost_password_confirmation_message', __( 'A password reset email has been sent to the email address on file for your account, but may take several minutes to show up in your inbox. Please wait at least 10 minutes before attempting another reset.', 'woocommerce' ) ) ); ?>
				</p>

				<?php do_action( 'woocommerce_after_lost_password_confirmation_message' ); ?>

				<a class="forgot" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
					<?php esc_html_e( 'Back to sign in ↗', 'fashion-brand-theme' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
