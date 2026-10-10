<?php
/**
 * Login / Register — v3 auth split frame.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_customer_login_form' );

$show_register = ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) );
?>

<section class="auth" id="customer_login">
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
			<div class="panel active" id="auth-login">
				<div class="eyebrow"><?php esc_html_e( 'Your account', 'fashion-brand-theme' ); ?></div>
				<h1><?php esc_html_e( 'Good to see you.', 'fashion-brand-theme' ); ?></h1>
				<p class="lede"><?php esc_html_e( 'Sign in to follow your orders, revisit your favourites and manage your details.', 'fashion-brand-theme' ); ?></p>

				<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>
					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<div class="field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="username"><?php esc_html_e( 'Email address', 'fashion-brand-theme' ); ?></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" />
					</div>
					<div class="field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="password"><?php esc_html_e( 'Password', 'fashion-brand-theme' ); ?></label>
						<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
					</div>

					<?php do_action( 'woocommerce_login_form' ); ?>

					<p class="form-row form-row--remember">
						<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
							<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
							<span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
						</label>
					</p>

					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="primary full woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>">
						<?php esc_html_e( 'Sign in', 'fashion-brand-theme' ); ?> <span aria-hidden="true">↗</span>
					</button>

					<a class="forgot" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot your password?', 'fashion-brand-theme' ); ?></a>

					<?php if ( $show_register ) : ?>
						<div class="auth-links">
							<span><?php esc_html_e( 'New here?', 'fashion-brand-theme' ); ?></span>
							<button type="button" class="auth-toggle" data-auth-panel="register"><?php esc_html_e( 'Create account ↗', 'fashion-brand-theme' ); ?></button>
						</div>
					<?php endif; ?>

					<?php do_action( 'woocommerce_login_form_end' ); ?>
				</form>
			</div>

			<?php if ( $show_register ) : ?>
				<div class="panel" id="auth-register">
					<div class="eyebrow"><?php esc_html_e( 'Join the world', 'fashion-brand-theme' ); ?></div>
					<h1><?php esc_html_e( 'Make it personal.', 'fashion-brand-theme' ); ?></h1>
					<p class="lede"><?php esc_html_e( 'Create an account to enjoy a smoother checkout and keep your considered pieces close.', 'fashion-brand-theme' ); ?></p>

					<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
						<?php do_action( 'woocommerce_register_form_start' ); ?>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
							<div class="field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?></label>
								<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" />
							</div>
						<?php endif; ?>

						<div class="field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_email"><?php esc_html_e( 'Email address', 'fashion-brand-theme' ); ?></label>
							<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" />
						</div>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
							<div class="field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<label for="reg_password"><?php esc_html_e( 'Password', 'fashion-brand-theme' ); ?></label>
								<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
							</div>
						<?php else : ?>
							<p class="lede lede--compact"><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>
						<?php endif; ?>

						<?php do_action( 'woocommerce_register_form' ); ?>

						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<button type="submit" class="primary full woocommerce-Button woocommerce-button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>">
							<?php esc_html_e( 'Create account', 'fashion-brand-theme' ); ?> ↗
						</button>

						<div class="auth-links">
							<span><?php esc_html_e( 'Already a member?', 'fashion-brand-theme' ); ?></span>
							<button type="button" class="auth-toggle" data-auth-panel="login"><?php esc_html_e( 'Sign in', 'fashion-brand-theme' ); ?></button>
						</div>

						<?php do_action( 'woocommerce_register_form_end' ); ?>
					</form>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
