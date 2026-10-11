<?php
/**
 * Edit account form — v3 profile block.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );
?>

<form class="woocommerce-EditAccountForm edit-account account-form-wrap" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>
	<div class="block">
		<div class="block-head">
			<h2><?php esc_html_e( 'Your profile', 'fashion-brand-theme' ); ?></h2>
			<span class="micro"><?php esc_html_e( 'Personal details', 'fashion-brand-theme' ); ?></span>
		</div>

		<div class="avatar-row">
			<?php fashion_brand_theme_render_account_avatar( array( 'size' => 'profile' ) ); ?>
			<button type="button" class="text-link avatar-change" data-avatar-open>
				<?php esc_html_e( 'Change photo ↗', 'fashion-brand-theme' ); ?>
			</button>
		</div>

		<div class="account-form">
			<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

			<div class="field woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
				<label for="account_first_name"><?php esc_html_e( 'First name', 'fashion-brand-theme' ); ?></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" aria-required="true" />
			</div>

			<div class="field woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
				<label for="account_last_name"><?php esc_html_e( 'Last name', 'fashion-brand-theme' ); ?></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" aria-required="true" />
			</div>

			<div class="clear"></div>

			<div class="field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide account-field--display-name">
				<label for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" aria-required="true" />
				<span id="account_display_name_description" class="screen-reader-text"><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></span>
			</div>

			<div class="field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="account_email"><?php esc_html_e( 'Email address', 'fashion-brand-theme' ); ?></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--email input-email" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" aria-required="true" />
			</div>

			<fieldset class="account-password-fields">
				<legend class="screen-reader-text"><?php esc_html_e( 'Password change', 'woocommerce' ); ?></legend>

				<div class="field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide account-field--password-current">
					<label for="password_current"><?php esc_html_e( 'Current password', 'woocommerce' ); ?></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
				</div>

				<div class="field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="password_1"><?php esc_html_e( 'New password', 'fashion-brand-theme' ); ?></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="new-password" placeholder="<?php esc_attr_e( 'Leave blank to keep current', 'fashion-brand-theme' ); ?>" />
				</div>

				<div class="field woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide account-field--password-confirm">
					<label for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
				</div>
			</fieldset>

			<div class="clear"></div>

			<?php do_action( 'woocommerce_edit_account_form' ); ?>

			<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
			<button type="submit" class="save woocommerce-Button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>">
				<?php esc_html_e( 'Save changes', 'fashion-brand-theme' ); ?> ↗
			</button>
			<input type="hidden" name="action" value="save_account_details" />

			<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
		</div>
	</div>
</form>

<dialog class="avatar-modal" id="avatar-modal" aria-labelledby="avatar-modal-title">
	<div class="avatar-modal__panel">
		<button type="button" class="avatar-modal__close" data-avatar-close aria-label="<?php esc_attr_e( 'Close', 'fashion-brand-theme' ); ?>">×</button>
		<div class="eyebrow"><?php esc_html_e( 'Your profile', 'fashion-brand-theme' ); ?></div>
		<h2 id="avatar-modal-title"><?php esc_html_e( 'Change photo', 'fashion-brand-theme' ); ?></h2>
		<p class="avatar-modal__lede"><?php esc_html_e( 'Choose a clear portrait. JPG, PNG, GIF or WebP.', 'fashion-brand-theme' ); ?></p>

		<div class="avatar-modal__drop" data-avatar-drop tabindex="0" role="button" aria-controls="account_avatar">
			<div class="avatar-modal__preview" data-avatar-preview hidden></div>
			<div class="avatar-modal__drop-copy" data-avatar-drop-copy>
				<span class="avatar-modal__drop-label"><?php esc_html_e( 'Drop an image here', 'fashion-brand-theme' ); ?></span>
				<span class="avatar-modal__drop-sub"><?php esc_html_e( 'or click to browse', 'fashion-brand-theme' ); ?></span>
			</div>
		</div>

		<p class="avatar-modal__error" data-avatar-error hidden></p>

		<div class="avatar-modal__actions">
			<button type="button" class="avatar-modal__cancel text-link" data-avatar-close><?php esc_html_e( 'Cancel', 'fashion-brand-theme' ); ?></button>
			<button type="button" class="save avatar-modal__save" data-avatar-save disabled>
				<?php esc_html_e( 'Save photo', 'fashion-brand-theme' ); ?> ↗
			</button>
		</div>

		<input
			type="file"
			id="account_avatar"
			name="account_avatar"
			class="fbt-hidden-file"
			accept="image/jpeg,image/png,image/gif,image/webp"
			tabindex="-1"
			aria-hidden="true"
		/>
	</div>
</dialog>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
