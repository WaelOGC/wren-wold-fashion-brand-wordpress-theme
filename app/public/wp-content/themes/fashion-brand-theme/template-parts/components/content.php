<?php
/**
 * Blog listing card (index / archive).
 *
 * @package Fashion_Brand_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$permalink = get_permalink();
$categories = get_the_category();
$primary_cat = ( ! empty( $categories ) && ! is_wp_error( $categories ) ) ? $categories[0] : null;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'blog-card' ); ?>>
	<a class="blog-card__media" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php
			the_post_thumbnail(
				'large',
				array(
					'class'   => 'blog-card__image',
					'loading' => 'lazy',
					'alt'     => the_title_attribute( array( 'echo' => false ) ),
				)
			);
			?>
		<?php else : ?>
			<span class="blog-card__placeholder" aria-hidden="true"></span>
		<?php endif; ?>
	</a>

	<div class="blog-card__body">
		<?php if ( $primary_cat instanceof WP_Term ) : ?>
			<p class="blog-card__meta">
				<a class="blog-card__category" href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>">
					<?php echo esc_html( $primary_cat->name ); ?>
				</a>
			</p>
		<?php endif; ?>

		<h2 class="blog-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>" rel="bookmark">
				<?php the_title(); ?>
			</a>
		</h2>

		<p class="blog-card__date">
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
		</p>

		<?php if ( has_excerpt() || get_the_content() ) : ?>
			<div class="blog-card__excerpt">
				<?php the_excerpt(); ?>
			</div>
		<?php endif; ?>

		<a class="blog-card__read" href="<?php echo esc_url( $permalink ); ?>">
			<?php esc_html_e( 'Read', 'fashion-brand-theme' ); ?>
			<span class="blog-card__read-arrow" aria-hidden="true">→</span>
		</a>
	</div>
</article>
