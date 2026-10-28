<?php
/**
 * Single post template — long-form reading layout.
 *
 * @package Fashion_Brand_Theme
 */

get_header();

$blog_url = (int) get_option( 'page_for_posts' );
$blog_url = $blog_url > 0 ? get_permalink( $blog_url ) : home_url( '/' );

$categories  = get_the_category();
$primary_cat = ( ! empty( $categories ) && ! is_wp_error( $categories ) ) ? $categories[0] : null;
?>

<main id="primary" class="site-main blog-single">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<header class="blog-single-hero" aria-labelledby="blog-single-heading">
			<div class="blog-single-hero__inner">
				<p class="blog-single-hero__eyebrow"><?php esc_html_e( 'Journal', 'fashion-brand-theme' ); ?></p>

				<?php if ( $primary_cat instanceof WP_Term ) : ?>
					<p class="blog-single-hero__category">
						<a href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>">
							<?php echo esc_html( $primary_cat->name ); ?>
						</a>
					</p>
				<?php endif; ?>

				<h1 id="blog-single-heading" class="blog-single-hero__heading">
					<?php the_title(); ?>
				</h1>

				<p class="blog-single-hero__date">
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
						<?php echo esc_html( get_the_date() ); ?>
					</time>
				</p>
			</div>
		</header>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-single__article' ); ?>>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="blog-single__featured">
					<?php
					the_post_thumbnail(
						'large',
						array(
							'class' => 'blog-single__featured-image',
							'alt'   => the_title_attribute( array( 'echo' => false ) ),
						)
					);
					?>
				</figure>
			<?php endif; ?>

			<div class="blog-single__content entry-content">
				<?php the_content(); ?>
			</div>

			<footer class="blog-single__footer">
				<a class="blog-single__back" href="<?php echo esc_url( $blog_url ); ?>">
					<span class="blog-single__back-arrow" aria-hidden="true">←</span>
					<?php esc_html_e( 'Back to Journal', 'fashion-brand-theme' ); ?>
				</a>
			</footer>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
