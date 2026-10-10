<?php
/**
 * Template Name: About Page
 * About page template.
 *
 * Used automatically for a Page with slug "about".
 *
 * @package Fashion_Brand_Theme
 */

get_header();

// Editable hero copy — change these strings to update the page wording.
$hero_eyebrow = __( 'Our Story', 'fashion-brand-theme' );
$hero_heading = __( 'Less noise. More character.', 'fashion-brand-theme' );

// Editable body copy.
$intro_copy = __(
	'The name WRENWOLD comes from two quiet ideas. The <em>wren</em> — small, clever, and unmistakably present despite its size. And the <em>wold</em> — an old word for open land, for space and stillness. Together, they describe what we want to be: confident without needing to shout, elegant without needing to try too hard.',
	'fashion-brand-theme'
);

// Pull-quote split of $intro_copy (same wording; layout-only).
$intro_quote = __(
	'The name WRENWOLD comes from two quiet ideas. The <em>wren</em> — small, clever, and unmistakably present despite its size. And the <em>wold</em> — an old word for open land, for space and stillness.',
	'fashion-brand-theme'
);
$intro_support = __(
	'Together, they describe what we want to be: confident without needing to shout, elegant without needing to try too hard.',
	'fashion-brand-theme'
);

$started_heading = __( 'Where it started', 'fashion-brand-theme' );
$started_p1      = __(
	"Our founder's story with fashion began early — not as a business plan, but as a genuine fascination with fabric and how clothes are made. That early spark only grew stronger after an encounter with a fashion designer who sketched her ideas by hand and watched them become real garments. It was the kind of moment that stays with you.",
	'fashion-brand-theme'
);
$started_p2 = __(
	'Life took its path elsewhere for a while — different countries, different work, including time inside a major European fashion retailer, quietly learning how a brand is really built and run at scale. But the pull back to fashion never fully went away.',
	'fashion-brand-theme'
);
$started_p3 = __(
	'WRENWOLD is that return — this time with the experience, the patience, and the clarity to build it properly.',
	'fashion-brand-theme'
);

$believe_heading = __( 'What we believe', 'fashion-brand-theme' );
$believe_p1      = __(
	"WRENWOLD is for the woman who already knows who she is. She doesn't need logos or loud statements to prove it. What she wears Monday to Friday — to work, to university, to the quieter parts of her life — should feel just as considered as what she wears on the weekend, with people she love.",
	'fashion-brand-theme'
);
$believe_p2 = __(
	"We also believe fashion shouldn't come at the planet's expense. Too much of what we buy is worn twice and thrown away — and that has a real cost to nature. It's something we think about with every piece we choose today, and it will only grow stronger as WRENWOLD builds more of its own products in the future, with sustainability and conscious materials at the center.",
	'fashion-brand-theme'
);
$believe_p3 = __(
	"We're still early in this journey. Every piece, every decision, is a step toward something built the right way.",
	'fashion-brand-theme'
);

$closing_line = __( 'WRENWOLD — Less noise. More character.', 'fashion-brand-theme' );

$allowed_inline = array(
	'em' => array(),
);
?>

<main id="primary" class="site-main about-page">
	<section class="about-hero-banner" aria-labelledby="about-hero-heading">
		<div
			class="about-hero-banner__image"
			style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/about-photography/hero-about-wide.jpg' ); ?>');"
		></div>
		<div class="about-hero-banner__overlay">
			<p class="about-hero-banner__eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
			<h1 id="about-hero-heading" class="about-hero-banner__heading">
				<?php echo esc_html( $hero_heading ); ?>
			</h1>
		</div>
	</section>

	<section class="about-page__content" aria-label="<?php esc_attr_e( 'About WREN WOLD', 'fashion-brand-theme' ); ?>">
		<div class="about-pullquote about-reveal">
			<p class="about-pullquote__quote"><?php echo wp_kses( $intro_quote, $allowed_inline ); ?></p>
			<p class="about-pullquote__support"><?php echo esc_html( $intro_support ); ?></p>
		</div>

		<div class="about-section about-section--band about-reveal">
			<div class="about-page__inner">
				<p class="about-section__eyebrow"><?php echo esc_html( $started_heading ); ?></p>
				<h2 class="about-section__heading"><?php echo esc_html( $started_heading ); ?></h2>
				<p class="about-section__text"><?php echo esc_html( $started_p1 ); ?></p>
				<p class="about-section__text"><?php echo esc_html( $started_p2 ); ?></p>
				<p class="about-section__text"><?php echo esc_html( $started_p3 ); ?></p>
			</div>
		</div>

		<div class="about-page__inner">
			<div class="about-section about-reveal">
				<p class="about-section__eyebrow"><?php echo esc_html( $believe_heading ); ?></p>
				<h2 class="about-section__heading"><?php echo esc_html( $believe_heading ); ?></h2>
				<p class="about-section__text"><?php echo esc_html( $believe_p1 ); ?></p>
				<p class="about-section__text"><?php echo esc_html( $believe_p2 ); ?></p>
				<p class="about-section__text"><?php echo esc_html( $believe_p3 ); ?></p>
			</div>

			<p class="about-page__closing about-reveal"><?php echo esc_html( $closing_line ); ?></p>
		</div>
	</section>
</main>

<script>
document.addEventListener( 'DOMContentLoaded', function () {
	'use strict';

	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var scrollReveals = document.querySelectorAll( '.about-page__content .about-reveal' );
	var contentSection = document.querySelector( '.about-page__content' );

	function activate( element ) {
		if ( element ) {
			element.classList.add( 'is-active' );
		}
	}

	if ( reduceMotion ) {
		scrollReveals.forEach( activate );
		return;
	}

	if ( ! contentSection || ! scrollReveals.length ) {
		return;
	}

	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) {
					return;
				}

				scrollReveals.forEach( activate );
				observer.disconnect();
			} );
		},
		{ threshold: 0.15 }
	);

	observer.observe( contentSection );
} );
</script>

<?php
get_footer();
