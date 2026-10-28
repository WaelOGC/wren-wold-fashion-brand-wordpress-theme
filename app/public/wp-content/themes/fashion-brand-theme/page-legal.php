<?php
/**
 * Template Name: Legal Page
 * Shared template for Privacy Policy, Terms, and Shipping & Returns.
 *
 * @package Fashion_Brand_Theme
 */

get_header();

$eyebrow = __( 'Policy', 'fashion-brand-theme' );
?>

<main id="primary" class="site-main legal-page">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<header class="legal-hero-banner" aria-labelledby="legal-hero-heading">
			<div class="legal-hero-banner__inner">
				<p class="legal-hero-banner__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<h1 id="legal-hero-heading" class="legal-hero-banner__heading">
					<?php the_title(); ?>
				</h1>
				<?php if ( get_the_modified_time( 'U' ) ) : ?>
					<p class="legal-hero-banner__updated">
						<?php
						printf(
							/* translators: %s: localized modified date */
							esc_html__( 'Last updated %s', 'fashion-brand-theme' ),
							esc_html( get_the_modified_date() )
						);
						?>
					</p>
				<?php endif; ?>
			</div>
		</header>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'legal-page__article' ); ?>>
			<div class="legal-page__content entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
