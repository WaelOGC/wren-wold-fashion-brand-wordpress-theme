<?php
/**
 * Header search panel (popup / modal).
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div
	id="header-search-panel"
	class="header-search-panel"
	data-header-search-panel
	hidden
>
	<div class="header-search-panel__backdrop" data-search-panel-close tabindex="-1"></div>
	<div
		class="header-search-panel__dialog"
		role="dialog"
		aria-modal="true"
		aria-labelledby="header-search-field-label"
	>
		<button
			type="button"
			class="header-search-panel__close"
			data-search-panel-close
			aria-label="<?php esc_attr_e( 'Close search', 'fashion-brand-theme' ); ?>"
		>
			<?php echo fashion_brand_theme_get_icon( 'x', array( 'width' => 20, 'height' => 20 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>

		<form
			role="search"
			method="get"
			class="header-search-form"
			action="<?php echo esc_url( home_url( '/' ) ); ?>"
		>
			<label id="header-search-field-label" class="header-search-form__label" for="header-search-field">
				<?php esc_html_e( 'Search', 'fashion-brand-theme' ); ?>
			</label>
			<input
				id="header-search-field"
				class="header-search-form__field"
				type="search"
				name="s"
				value="<?php echo esc_attr( get_search_query() ); ?>"
				placeholder="<?php esc_attr_e( 'Search products', 'fashion-brand-theme' ); ?>"
				autocomplete="off"
				data-live-search-input
				data-live-search-results="header-live-search-results"
				data-live-search-status="header-live-search-status"
				data-live-search-view-all="header-live-search-view-all"
				data-live-search-limit="6"
			/>
		</form>

		<p id="header-live-search-status" class="header-search-panel__status" hidden></p>

		<div id="header-live-search-results" class="header-search-panel__results">
			<?php fashion_brand_theme_render_live_search_idle(); ?>
		</div>

		<a
			id="header-live-search-view-all"
			class="header-search-panel__view-all"
			href="<?php echo esc_url( fashion_brand_theme_get_search_url() ); ?>"
			hidden
		>
			<?php esc_html_e( 'View all results', 'fashion-brand-theme' ); ?>
		</a>
	</div>
</div>
