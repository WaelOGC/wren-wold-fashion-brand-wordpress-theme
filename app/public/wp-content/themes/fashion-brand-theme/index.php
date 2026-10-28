<?php
/**
 * Blog posts index (posts page / main query fallback).
 *
 * @package Fashion_Brand_Theme
 */

get_header();

$hero_eyebrow = __( 'Journal', 'fashion-brand-theme' );
$hero_heading = __( 'Latest', 'fashion-brand-theme' );
?>

<main id="primary" class="site-main blog-page">
	<header class="blog-hero-banner" aria-labelledby="blog-hero-heading">
		<div class="blog-hero-banner__inner">
			<p class="blog-hero-banner__eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
			<h1 id="blog-hero-heading" class="blog-hero-banner__heading">
				<?php echo esc_html( $hero_heading ); ?>
			</h1>
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
