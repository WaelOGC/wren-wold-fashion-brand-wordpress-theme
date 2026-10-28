/**
 * Keep the WhatsApp float clear of the cookie banner and other bottom bars.
 *
 * @package Fashion_Brand_Theme
 */
( function () {
	'use strict';

	const button = document.querySelector( '[data-whatsapp-float]' );

	if ( ! button ) {
		return;
	}

	const GAP = 16;
	let scheduled = false;
	let updating = false;

	function isVisible( el ) {
		if ( ! el || el.hidden ) {
			return false;
		}

		const style = window.getComputedStyle( el );

		if ( 'none' === style.display || 'hidden' === style.visibility ) {
			return false;
		}

		const rect = el.getBoundingClientRect();

		return rect.height > 0 && rect.width > 0;
	}

	function readPx( styles, name, fallback ) {
		const raw = styles.getPropertyValue( name ).trim();
		const n = parseFloat( raw );

		return Number.isFinite( n ) ? n : fallback;
	}

	function baseBottomOffset() {
		const styles = window.getComputedStyle( button );
		const inset = readPx( styles, '--fbt-float-rail-inset', 24 );
		const nav = readPx( styles, '--whatsapp-float-nav-clearance', 0 );

		return inset + nav;
	}

	function updateClearance() {
		updating = true;

		const base = baseBottomOffset();
		let neededBottom = base;

		document.querySelectorAll( '[data-cookie-consent], .sticky-atc' ).forEach( function ( el ) {
			if ( ! isVisible( el ) ) {
				return;
			}

			const rect = el.getBoundingClientRect();
			neededBottom = Math.max( neededBottom, window.innerHeight - rect.top + GAP );
		} );

		const clearance = Math.max( 0, neededBottom - base );
		button.style.setProperty( '--whatsapp-float-banner-clearance', clearance + 'px' );

		updating = false;
	}

	function scheduleUpdate() {
		if ( updating || scheduled ) {
			return;
		}

		scheduled = true;
		window.requestAnimationFrame( function () {
			scheduled = false;
			updateClearance();
		} );
	}

	updateClearance();

	document.addEventListener( 'wrenwold:consent-updated', scheduleUpdate );
	window.addEventListener( 'resize', scheduleUpdate );

	if ( 'MutationObserver' in window ) {
		new MutationObserver( function ( mutations ) {
			if ( updating ) {
				return;
			}

			for ( let i = 0; i < mutations.length; i++ ) {
				const m = mutations[ i ];
				if ( m.target === button || button.contains( m.target ) ) {
					continue;
				}
				scheduleUpdate();
				return;
			}
		} ).observe( document.body, {
			attributes: true,
			attributeFilter: [ 'hidden', 'class', 'style' ],
			childList: true,
			subtree: true,
		} );
	}

	if ( 'ResizeObserver' in window ) {
		const ro = new ResizeObserver( scheduleUpdate );
		document.querySelectorAll( '[data-cookie-consent], .sticky-atc' ).forEach( function ( el ) {
			ro.observe( el );
		} );
	}
} )();
