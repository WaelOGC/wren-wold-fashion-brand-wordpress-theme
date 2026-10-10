<?php
/**
 * Search results template — live product search.
 *
 * @package Fashion_Brand_Theme
 */

get_header();

$search_term  = get_search_query();
$has_term     = '' !== trim( (string) $search_term );
$result_limit = 24;
$match_count  = 0;

ob_start();

if ( $has_term ) {
	$match_count = fashion_brand_theme_render_live_search_results( $search_term, $result_limit );
} else {
	fashion_brand_theme_render_live_search_idle();
}

$results_html = ob_get_clean();
?>

<main id="primary" class="site-main search-page">
	<header class="search-page__hero">
		<div class="search-page__waves" aria-hidden="true">
			<svg class="search-page__wave search-page__wave--1" viewBox="0 0 1440 120" preserveAspectRatio="none" focusable="false">
				<path fill="currentColor" d="M0,64 C240,120 480,0 720,48 C960,96 1200,16 1440,64 L1440,120 L0,120 Z"/>
			</svg>
			<svg class="search-page__wave search-page__wave--2" viewBox="0 0 1440 120" preserveAspectRatio="none" focusable="false">
				<path fill="currentColor" d="M0,80 C200,20 420,100 720,60 C1020,20 1240,90 1440,40 L1440,120 L0,120 Z"/>
			</svg>
			<svg class="search-page__wave search-page__wave--3" viewBox="0 0 1440 120" preserveAspectRatio="none" focusable="false">
				<path fill="currentColor" d="M0,40 C280,90 520,10 780,55 C1040,100 1260,30 1440,70 L1440,120 L0,120 Z"/>
			</svg>
		</div>

		<div class="search-page__hero-inner">
			<p class="search-page__eyebrow"><?php esc_html_e( 'Search', 'fashion-brand-theme' ); ?></p>
			<h1 class="search-page__heading"><?php esc_html_e( 'What are you looking for?', 'fashion-brand-theme' ); ?></h1>

			<form
				role="search"
				method="get"
				class="search-page__form"
				action="<?php echo esc_url( home_url( '/' ) ); ?>"
			>
				<label class="screen-reader-text" for="search-page-field">
					<?php esc_html_e( 'Search products', 'fashion-brand-theme' ); ?>
				</label>
				<input
					id="search-page-field"
					class="search-page__field"
					type="search"
					name="s"
					value="<?php echo esc_attr( $search_term ); ?>"
					placeholder="<?php esc_attr_e( 'Search products', 'fashion-brand-theme' ); ?>"
					autocomplete="off"
					data-live-search-input
					data-live-search-results="search-page-results"
					data-live-search-status="search-page-status"
					data-live-search-limit="<?php echo esc_attr( (string) $result_limit ); ?>"
					data-live-search-update-url
				/>
			</form>
		</div>
	</header>

	<section class="search-page__results-section" aria-live="polite">
		<p id="search-page-status" class="search-page__status"<?php echo $has_term ? '' : ' hidden'; ?>>
			<?php
			if ( $has_term ) {
				printf(
					/* translators: %d: number of search results. */
					esc_html( _n( '%d result', '%d results', $match_count, 'fashion-brand-theme' ) ),
					(int) $match_count
				);
			}
			?>
		</p>

		<div id="search-page-results" class="search-page__results">
			<?php echo $results_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- product card / state markup from theme helpers. ?>
		</div>
	</section>
</main>

<?php get_template_part( 'template-parts/shop/quick-view', 'modal' ); ?>

<?php
get_footer();
