/**
 * My Account — auth panel toggle + coupon copy.
 *
 * @package Fashion_Brand_Theme
 */

( function () {
	'use strict';

	function showAuth( which ) {
		var loginPanel = document.getElementById( 'auth-login' );
		var registerPanel = document.getElementById( 'auth-register' );

		if ( ! loginPanel ) {
			return;
		}

		loginPanel.classList.toggle( 'active', which === 'login' );

		if ( registerPanel ) {
			registerPanel.classList.toggle( 'active', which === 'register' );
		}
	}

	document.addEventListener( 'click', function ( event ) {
		var toggle = event.target.closest( '.auth-toggle[data-auth-panel]' );

		if ( toggle ) {
			event.preventDefault();
			showAuth( toggle.getAttribute( 'data-auth-panel' ) );
		}
	} );

	function copyText( text ) {
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			return navigator.clipboard.writeText( text );
		}

		return new Promise( function ( resolve, reject ) {
			var input = document.createElement( 'textarea' );
			input.value = text;
			input.setAttribute( 'readonly', '' );
			input.style.position = 'fixed';
			input.style.opacity = '0';
			document.body.appendChild( input );
			input.select();

			try {
				var ok = document.execCommand( 'copy' );
				document.body.removeChild( input );
				if ( ok ) {
					resolve();
				} else {
					reject();
				}
			} catch ( e ) {
				document.body.removeChild( input );
				reject( e );
			}
		} );
	}

	function showCopied( button ) {
		var defaultLabel =
			button.getAttribute( 'data-label-default' ) || button.textContent.trim();
		var copiedLabel =
			button.getAttribute( 'data-label-copied' ) || 'Copied ✓';

		button.textContent = copiedLabel;
		button.classList.add( 'is-copied' );
		button.setAttribute( 'aria-live', 'polite' );

		window.setTimeout( function () {
			button.textContent = defaultLabel;
			button.classList.remove( 'is-copied' );
		}, 1500 );
	}

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '[data-coupon-copy], .copy-btn[data-code]' );

		if ( ! button ) {
			return;
		}

		event.preventDefault();

		var code =
			button.getAttribute( 'data-coupon-code' ) ||
			button.getAttribute( 'data-code' ) ||
			'';

		if ( ! code ) {
			return;
		}

		copyText( code )
			.then( function () {
				showCopied( button );
			} )
			.catch( function () {
				/* ignore */
			} );
	} );
}() );
