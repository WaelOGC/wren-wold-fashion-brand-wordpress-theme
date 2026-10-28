<?php
/**
 * GDPR cookie consent bar + preferences panel.
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fashion_brand_theme_should_render_cookie_consent' ) || ! fashion_brand_theme_should_render_cookie_consent() ) {
	return;
}

$privacy_url = fashion_brand_theme_get_cookie_consent_privacy_url();
?>
<div
	id="cookie-consent"
	class="cookie-consent"
	data-cookie-consent
	role="dialog"
	aria-labelledby="cookie-consent-title"
	aria-describedby="cookie-consent-desc"
>
	<div class="cookie-consent__inner">
		<div class="cookie-consent__bar">
			<div class="cookie-consent__copy">
				<p id="cookie-consent-title" class="cookie-consent__title">
					<?php esc_html_e( 'Cookies', 'fashion-brand-theme' ); ?>
				</p>
				<p id="cookie-consent-desc" class="cookie-consent__text">
					<?php
					echo wp_kses(
						sprintf(
							/* translators: %s: Privacy Policy link */
							__( 'We use cookies to run the shop and, with your permission, to understand how the site is used and improve our marketing. See our %s for details.', 'fashion-brand-theme' ),
							'<a class="cookie-consent__privacy" href="' . esc_url( $privacy_url ) . '">' . esc_html__( 'Privacy Policy', 'fashion-brand-theme' ) . '</a>'
						),
						array(
							'a' => array(
								'class' => true,
								'href'  => true,
							),
						)
					);
					?>
				</p>
			</div>

			<div class="cookie-consent__actions">
				<button type="button" class="cookie-consent__btn cookie-consent__btn--primary" data-cookie-accept-all>
					<?php esc_html_e( 'Accept all', 'fashion-brand-theme' ); ?>
				</button>
				<button type="button" class="cookie-consent__btn cookie-consent__btn--ghost" data-cookie-reject>
					<?php esc_html_e( 'Reject non-essential', 'fashion-brand-theme' ); ?>
				</button>
				<button
					type="button"
					class="cookie-consent__btn cookie-consent__btn--text"
					data-cookie-customize
					aria-expanded="false"
					aria-controls="cookie-consent-prefs"
				>
					<?php esc_html_e( 'Customize', 'fashion-brand-theme' ); ?>
				</button>
			</div>
		</div>

		<div id="cookie-consent-prefs" class="cookie-consent__prefs" data-cookie-prefs hidden>
			<ul class="cookie-consent__categories">
				<li class="cookie-consent__category">
					<div class="cookie-consent__category-head">
						<span class="cookie-consent__category-label"><?php esc_html_e( 'Necessary', 'fashion-brand-theme' ); ?></span>
						<label class="cookie-consent__switch cookie-consent__switch--locked">
							<input type="checkbox" checked disabled data-cookie-category="necessary" />
							<span class="cookie-consent__switch-ui" aria-hidden="true"></span>
							<span class="screen-reader-text"><?php esc_html_e( 'Necessary cookies — always on', 'fashion-brand-theme' ); ?></span>
						</label>
					</div>
					<p class="cookie-consent__category-desc">
						<?php esc_html_e( 'Required for the cart, login session, and spam protection. Always on.', 'fashion-brand-theme' ); ?>
					</p>
				</li>

				<li class="cookie-consent__category">
					<div class="cookie-consent__category-head">
						<span class="cookie-consent__category-label" id="cookie-cat-analytics-label"><?php esc_html_e( 'Analytics', 'fashion-brand-theme' ); ?></span>
						<label class="cookie-consent__switch">
							<input type="checkbox" data-cookie-category="analytics" aria-labelledby="cookie-cat-analytics-label" />
							<span class="cookie-consent__switch-ui" aria-hidden="true"></span>
						</label>
					</div>
					<p class="cookie-consent__category-desc">
						<?php esc_html_e( 'Helps us understand how visitors use the site so we can improve it.', 'fashion-brand-theme' ); ?>
					</p>
				</li>

				<li class="cookie-consent__category">
					<div class="cookie-consent__category-head">
						<span class="cookie-consent__category-label" id="cookie-cat-marketing-label"><?php esc_html_e( 'Marketing', 'fashion-brand-theme' ); ?></span>
						<label class="cookie-consent__switch">
							<input type="checkbox" data-cookie-category="marketing" aria-labelledby="cookie-cat-marketing-label" />
							<span class="cookie-consent__switch-ui" aria-hidden="true"></span>
						</label>
					</div>
					<p class="cookie-consent__category-desc">
						<?php esc_html_e( 'Used for newsletters and ads that are relevant to you. Off until you opt in.', 'fashion-brand-theme' ); ?>
					</p>
				</li>
			</ul>

			<button type="button" class="cookie-consent__btn cookie-consent__btn--primary cookie-consent__save" data-cookie-save>
				<?php esc_html_e( 'Save preferences', 'fashion-brand-theme' ); ?>
			</button>
		</div>
	</div>
</div>
