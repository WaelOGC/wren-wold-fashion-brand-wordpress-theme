<?php
/**
 * Archive template (categories, tags, dates, authors).
 *
 * @package Fashion_Brand_Theme
 */

get_header();

$hero_eyebrow = __( 'Journal', 'fashion-brand-theme' );

// Drop the default "Category:" / "Tag:" prefix for a cleaner band.
$prefix_filter = static function () {
	return '';
};
add_filter( 'get_the_archive_title_prefix', $prefix_filter );
$archive_title = wp_strip_all_tags( get_the_archive_title() );
remove_filter( 'get_the_archive_title_prefix', $prefix_filter );
$archive_desc  = get_the_archive_description();
?>

<main id="primary" class="site-main blog-page">
	<header class="blog-hero-banner" aria-labelledby="blog-hero-heading">
		<div class="blog-hero-banner__inner">
			<p class="blog-hero-banner__eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
			<h1 id="blog-hero-heading" class="blog-hero-banner__heading">
				<?php echo esc_html( $archive_title ? $archive_title : __( 'Archive', 'fashion-brand-theme' ) ); ?>
			</h1>
			<?php if ( $archive_desc ) : ?>
				<div class="blog-hero-banner__desc">
					<?php echo wp_kses_post( $archive_desc ); ?>
				</div>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="blog-page__content">
			<div class="blog-page__inner">
				<div class="blog-grid">
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php get_template_part( 'template-parts/components/content', get_post_type() ); ?>
					<?php endwhile; ?>
				</div>

				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => __( 'Previous', 'fashion-brand-theme' ),
						'next_text' => __( 'Next', 'fashion-brand-theme' ),
					)
				);
				?>
			</div>
		</div>
	<?php else : ?>
		<div class="blog-page__content">
			<div class="blog-page__inner">
				<?php get_template_part( 'template-parts/components/content', 'none' ); ?>
			</div>
		</div>
	<?php endif; ?>
</main>

<?php
get_footer();
