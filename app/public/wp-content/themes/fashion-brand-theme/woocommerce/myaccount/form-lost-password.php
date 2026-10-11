<?php
/**
 * Lost password form — themed auth split.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>

<section class="auth auth--lost-password">
	<div class="auth-frame">
		<?php fashion_brand_theme_render_auth_visual(); ?>

		<div class="auth-form">
			<div class="panel active">
				<div class="eyebrow"><?php esc_html_e( 'Account recovery', 'fashion-brand-theme' ); ?></div>
				<h1><?php esc_html_e( 'Forgot your password?', 'fashion-brand-theme' ); ?></h1>
				<p class="lede">
					<?php echo esc_html( apply_filters( 'woocommerce_lost_password_message', __( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'woocommerce' ) ) ); ?>
				</p>

				<form method="post" class="woocommerce-ResetPassword lost_reset_password" novalidate>
					<div class="field woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
						<label for="user_login"><?php esc_html_e( 'Username or email', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" autocomplete="username" required aria-required="true" />
					</div>

					<div class="clear"></div>

					<?php do_action( 'woocommerce_lostpassword_form' ); ?>

					<input type="hidden" name="wc_reset_password" value="true" />
					<button type="submit" class="primary full woocommerce-Button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>">
						<?php esc_html_e( 'Email reset link', 'fashion-brand-theme' ); ?> <span aria-hidden="true">↗</span>
					</button>

					<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

					<a class="forgot" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
						<?php esc_html_e( 'Back to sign in ↗', 'fashion-brand-theme' ); ?>
					</a>
				</form>
			</div>
		</div>
	</div>
</section>

<?php
do_action( 'woocommerce_after_lost_password_form' );
