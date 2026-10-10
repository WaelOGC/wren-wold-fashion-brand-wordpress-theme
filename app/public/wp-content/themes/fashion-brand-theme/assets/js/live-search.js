/**
 * Live product search.
 *
 * @package Fashion_Brand_Theme
 */

( function () {
	'use strict';

	function getConfig() {
		return window.fashionBrandThemeLiveSearch || {};
	}

	function getById( id ) {
		if ( ! id ) {
			return null;
		}

		return document.getElementById( id );
	}

	function buildSearchPageUrl( term ) {
		var cfg = getConfig();
		var base = cfg.searchUrl || '/';

		try {
			var url = new URL( base, window.location.origin );

			if ( term ) {
				url.searchParams.set( 's', term );
			} else {
				url.searchParams.delete( 's' );
			}

			return url.toString();
		} catch ( e ) {
			var sep = base.indexOf( '?' ) === -1 ? '?' : '&';
			return term ? base + sep + 's=' + encodeURIComponent( term ) : base;
		}
	}

	function formatCountLabel( count ) {
		var cfg = getConfig();
		var i18n = cfg.i18n || {};
		var singular = i18n.result || 'result';
		var plural = i18n.results || 'results';
		var label = count === 1 ? singular : plural;

		return String( count ) + ' ' + label;
	}

	function updateMeta( input, count, term ) {
		var status = getById( input.getAttribute( 'data-live-search-status' ) );
		var viewAll = getById( input.getAttribute( 'data-live-search-view-all' ) );
		var cfg = getConfig();
		var minChars = typeof cfg.minChars === 'number' ? cfg.minChars : 2;
		var hasTerm = term.length >= minChars;

		if ( status ) {
			if ( hasTerm ) {
				status.textContent = formatCountLabel( count );
				status.hidden = false;
			} else {
				status.textContent = '';
				status.hidden = true;
			}
		}

		if ( viewAll ) {
			if ( hasTerm && count > 0 ) {
				viewAll.href = buildSearchPageUrl( term );
				viewAll.hidden = false;

				if ( cfg.i18n && cfg.i18n.viewAll ) {
					viewAll.textContent = cfg.i18n.viewAll;
				}
			} else {
				viewAll.hidden = true;
			}
		}
	}

	function updateSearchPageUrl( input, term ) {
		if ( ! input.hasAttribute( 'data-live-search-update-url' ) ) {
			return;
		}

		try {
			var url = new URL( window.location.href );

			if ( term ) {
				url.searchParams.set( 's', term );
			} else {
				url.searchParams.delete( 's' );
			}

			window.history.replaceState( null, '', url.toString() );
		} catch ( e ) {
			/* ignore */
		}
	}

	function runSearch( input ) {
		var cfg = getConfig();
		var results = getById( input.getAttribute( 'data-live-search-results' ) );

		if ( ! results || ! cfg.ajaxUrl || ! cfg.nonce ) {
			return;
		}

		var term = ( input.value || '' ).trim();
		var limitAttr = parseInt( input.getAttribute( 'data-live-search-limit' ) || '', 10 );
		var limit = limitAttr > 0 ? limitAttr : 6;
		var requestId = String( ( Number( input.dataset.liveSearchRequestId || 0 ) || 0 ) + 1 );

		input.dataset.liveSearchRequestId = requestId;

		var body = new FormData();
		body.append( 'action', 'fashion_brand_theme_live_search' );
		body.append( 'nonce', cfg.nonce );
		body.append( 'term', term );
		body.append( 'limit', String( limit ) );

		fetch( cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body,
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( json ) {
				if ( input.dataset.liveSearchRequestId !== requestId ) {
					return;
				}

				if ( ! json || ! json.success || ! json.data ) {
					return;
				}

				results.innerHTML = json.data.html || '';
				updateMeta( input, typeof json.data.count === 'number' ? json.data.count : 0, term );
				updateSearchPageUrl( input, term );
			} )
			.catch( function () {
				/* ignore */
			} );
	}

	function bindInput( input ) {
		var debounceMs = 300;
		var timer = null;

		function schedule() {
			window.clearTimeout( timer );
			timer = window.setTimeout( function () {
				runSearch( input );
			}, debounceMs );
		}

		input.addEventListener( 'input', schedule );

		input.addEventListener( 'keydown', function ( event ) {
			if ( event.key !== 'Enter' ) {
				return;
			}

			event.preventDefault();
			window.clearTimeout( timer );
			runSearch( input );
		} );

		var form = input.closest( 'form' );

		if ( form ) {
			form.addEventListener( 'submit', function ( event ) {
				event.preventDefault();
				window.clearTimeout( timer );
				runSearch( input );
			} );
		}
	}

	function init() {
		document.querySelectorAll( '[data-live-search-input]' ).forEach( bindInput );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
