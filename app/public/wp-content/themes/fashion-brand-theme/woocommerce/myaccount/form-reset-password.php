<?php
/**
 * Reset password form — themed auth split.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.2.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );
?>

<section class="auth auth--reset-password">
	<div class="auth-frame">
		<div class="auth-visual">
			<div class="tag"><?php esc_html_e( 'WREN WOLD / Member space', 'fashion-brand-theme' ); ?></div>
			<div class="abstract-shape" aria-hidden="true"></div>
			<div class="visual-copy">
				<small><?php esc_html_e( 'Made for the considered life', 'fashion-brand-theme' ); ?></small>
				<h2><?php esc_html_e( 'Less, but', 'fashion-brand-theme' ); ?><br><em><?php esc_html_e( 'better.', 'fashion-brand-theme' ); ?></em></h2>
			</div>
			<div class="bottom-note"><?php esc_html_e( 'Personal account · Private access', 'fashion-brand-theme' ); ?></div>
		</div>

		<div class="auth-form">
			<div class="panel active">
				<div class="eyebrow"><?php esc_html_e( 'Account recovery', 'fashion-brand-theme' ); ?></div>
				<h1><?php esc_html_e( 'Choose a new password.', 'fashion-brand-theme' ); ?></h1>
				<p class="lede">
					<?php echo esc_html( apply_filters( 'woocommerce_reset_password_message', __( 'Enter a new password below.', 'woocommerce' ) ) ); ?>
				</p>

				<form method="post" class="woocommerce-ResetPassword lost_reset_password" novalidate>
					<div class="field woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
						<label for="password_1"><?php esc_html_e( 'New password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_1" id="password_1" autocomplete="new-password" required aria-required="true" />
					</div>
					<div class="field woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
						<label for="password_2"><?php esc_html_e( 'Re-enter new password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_2" id="password_2" autocomplete="new-password" required aria-required="true" />
					</div>

					<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
					<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

					<div class="clear"></div>

					<?php do_action( 'woocommerce_resetpassword_form' ); ?>

					<input type="hidden" name="wc_reset_password" value="true" />
					<button type="submit" class="primary full woocommerce-Button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" value="<?php esc_attr_e( 'Save', 'woocommerce' ); ?>">
						<?php esc_html_e( 'Save new password', 'fashion-brand-theme' ); ?> <span aria-hidden="true">↗</span>
					</button>

					<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>

					<a class="forgot" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
						<?php esc_html_e( 'Back to sign in ↗', 'fashion-brand-theme' ); ?>
					</a>
				</form>
			</div>
		</div>
	</div>
</section>

<?php
do_action( 'woocommerce_after_reset_password_form' );
