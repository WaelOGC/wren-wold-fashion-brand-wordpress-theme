<?php
/**
 * Email Header — WREN WOLD branded band + logo.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package Fashion_Brand_Theme
 * @version 10.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$store_name = $store_name ?? get_bloginfo( 'name', 'display' );
$logo_url   = function_exists( 'fashion_brand_theme_get_email_logo_url' )
	? fashion_brand_theme_get_email_logo_url()
	: '';
$header_image_url = apply_filters( 'woocommerce_email_header_image_url', home_url() );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta content="width=device-width, initial-scale=1.0" name="viewport">
		<title><?php echo esc_html( $store_name ); ?></title>
	</head>
	<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
		<table width="100%" id="outer_wrapper" role="presentation">
			<tr>
				<td><!-- Deliberately empty to support consistent sizing and layout across multiple email clients. --></td>
				<td width="600">
					<div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
						<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="inner_wrapper" role="presentation">
							<tr>
								<td align="center" valign="top">
									<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_container" role="presentation">
										<tr>
											<td align="center" valign="top" id="ww-email-brand-band" bgcolor="#3f5348" style="background-color:#3f5348;padding:22px 24px;">
												<?php if ( $logo_url ) : ?>
													<?php if ( $header_image_url ) : ?>
														<a href="<?php echo esc_url( $header_image_url ); ?>" target="_blank" style="display:inline-block;text-decoration:none;">
															<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $store_name ); ?>" width="160" style="display:block;margin:0 auto;border:0;outline:none;text-decoration:none;height:auto;max-width:160px;" />
														</a>
													<?php else : ?>
														<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $store_name ); ?>" width="160" style="display:block;margin:0 auto;border:0;outline:none;text-decoration:none;height:auto;max-width:160px;" />
													<?php endif; ?>
												<?php else : ?>
													<p style="margin:0;font-family:Georgia,'Times New Roman',serif;font-size:20px;letter-spacing:0.12em;color:#fafaf8;text-transform:uppercase;">
														<?php echo esc_html( $store_name ); ?>
													</p>
												<?php endif; ?>
											</td>
										</tr>
										<tr>
											<td align="center" valign="top">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header" role="presentation">
													<tr>
														<td id="header_wrapper">
															<h1><?php echo esc_html( $email_heading ); ?></h1>
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td align="center" valign="top">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body" role="presentation">
													<tr>
														<td valign="top" id="body_content">
															<table border="0" cellpadding="20" cellspacing="0" width="100%" role="presentation">
																<tr>
																	<td valign="top" id="body_content_inner_cell">
																		<div id="body_content_inner">
