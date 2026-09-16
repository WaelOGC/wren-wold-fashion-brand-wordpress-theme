/**
 * Contact page — subtle parallax on the hero banner image.
 *
 * @package Fashion_Brand_Theme
 */

( function () {
	'use strict';

	var image = document.querySelector( '.contact-hero-banner__image' );

	if ( ! image ) {
		return;
	}

	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );
	var ticking = false;

	function updateParallax() {
		ticking = false;

		if ( reduceMotion.matches ) {
			image.style.transform = '';
			return;
		}

		var offset = window.scrollY * 0.3;
		image.style.transform = 'translate3d(0, ' + offset + 'px, 0)';
	}

	function onScroll() {
		if ( ticking ) {
			return;
		}

		ticking = true;
		window.requestAnimationFrame( updateParallax );
	}

	if ( reduceMotion.matches ) {
		return;
	}

	window.addEventListener( 'scroll', onScroll, { passive: true } );
	updateParallax();

	if ( typeof reduceMotion.addEventListener === 'function' ) {
		reduceMotion.addEventListener( 'change', function () {
			if ( reduceMotion.matches ) {
				image.style.transform = '';
				window.removeEventListener( 'scroll', onScroll );
			} else {
				window.addEventListener( 'scroll', onScroll, { passive: true } );
				updateParallax();
			}
		} );
	}
} )();
