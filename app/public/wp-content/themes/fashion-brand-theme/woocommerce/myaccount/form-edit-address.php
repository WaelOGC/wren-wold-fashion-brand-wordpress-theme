<?php
/**
 * Edit address form — themed block layout.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address )
	? esc_html__( 'Billing address', 'woocommerce' )
	: esc_html__( 'Shipping address', 'woocommerce' );

do_action( 'woocommerce_before_edit_account_address_form' );
?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

	<div class="block">
		<div class="block-head">
			<h2><?php echo apply_filters( 'woocommerce_my_account_edit_address_title', $page_title, $load_address ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			<a class="text-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>">
				<?php esc_html_e( 'Back to addresses ↗', 'fashion-brand-theme' ); ?>
			</a>
		</div>

		<form method="post" class="account-form woocommerce-address-fields-form" novalidate>
			<div class="woocommerce-address-fields">
				<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

				<div class="woocommerce-address-fields__field-wrapper">
					<?php
					foreach ( $address as $key => $field ) {
						woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
					}
					?>
				</div>

				<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

				<p class="form-row">
					<button type="submit" class="save button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>">
						<?php esc_html_e( 'Save address', 'fashion-brand-theme' ); ?> ↗
					</button>
					<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
					<input type="hidden" name="action" value="edit_address" />
				</p>
			</div>
		</form>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
