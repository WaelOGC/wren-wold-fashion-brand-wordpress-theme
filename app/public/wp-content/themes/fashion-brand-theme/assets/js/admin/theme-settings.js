/**
 * Theme Settings dashboard — tabs, Overview mirrors, unsaved bar.
 *
 * @package Fashion_Brand_Theme
 */
( function () {
	'use strict';

	var root = document.querySelector( '.fbt-settings' );
	if ( ! root ) {
		return;
	}

	var cfg = window.fashionBrandThemeSettings || {};
	var overviewMeta = cfg.overview || {};
	var i18n = cfg.i18n || {};
	var form = document.getElementById( 'fbt-settings-form' );
	var bar = document.getElementById( 'fbt-savebar' );
	var toast = document.getElementById( 'fbt-toast' );
	var tabs = [].slice.call( root.querySelectorAll( '.fbt-settings__nav [role="tab"]' ) );
	var dirty = false;
	var ready = false;

	function readTabFromUrl() {
		var params = new URLSearchParams( window.location.search );
		var q = params.get( 'tab' );
		if ( q ) {
			return q;
		}
		var hash = ( window.location.hash || '' ).replace( /^#/, '' );
		if ( hash.indexOf( 'tab=' ) === 0 ) {
			return hash.slice( 4 );
		}
		return root.getAttribute( 'data-active-tab' ) || 'overview';
	}

	function setUrlTab( tab ) {
		var url = new URL( window.location.href );
		url.searchParams.set( 'tab', tab );
		url.hash = tab;
		history.replaceState( null, '', url.pathname + url.search + url.hash );

		if ( form ) {
			var ref = form.querySelector( 'input[name="_wp_http_referer"]' );
			if ( ref ) {
				ref.value = url.pathname + url.search + url.hash;
			}
		}
	}

	function go( tab ) {
		if ( ! document.getElementById( 'fbt-panel-' + tab ) ) {
			tab = 'overview';
		}

		tabs.forEach( function ( btn ) {
			btn.setAttribute( 'aria-selected', btn.dataset.tab === tab ? 'true' : 'false' );
		} );

		root.querySelectorAll( '.fbt-settings__panel' ).forEach( function ( panel ) {
			var active = panel.id === 'fbt-panel-' + tab;
			panel.classList.toggle( 'is-active', active );
			if ( active ) {
				panel.removeAttribute( 'hidden' );
			} else {
				panel.setAttribute( 'hidden', '' );
			}
		} );

		setUrlTab( tab );
		window.scrollTo( { top: 0 } );
	}

	tabs.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			go( btn.dataset.tab );
		} );
	} );

	function escapeHtml( str ) {
		return String( str )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' );
	}

	var grid = document.getElementById( 'fbt-ov-grid' );
	if ( grid ) {
		Object.keys( overviewMeta ).forEach( function ( key ) {
			var main = document.querySelector( '[data-key="' + key + '"]' );
			if ( ! main ) {
				return;
			}

			var card = main.closest( '.fbt-card' );
			var iconWrap = card ? card.querySelector( '.fbt-card__icon' ) : null;
			var iconHtml = iconWrap ? iconWrap.innerHTML : '';
			var meta = overviewMeta[ key ];
			var item = document.createElement( 'div' );
			item.className = 'fbt-ov';
			item.innerHTML =
				'<div class="fbt-ov__top"><div class="fbt-card__icon">' +
				iconHtml +
				'</div><label class="fbt-sw"><input type="checkbox" data-mirror="' +
				escapeHtml( key ) +
				'" aria-label="' +
				escapeHtml( meta.label ) +
				'"' +
				( main.checked ? ' checked' : '' ) +
				'><span></span></label></div><div><h4>' +
				escapeHtml( meta.label ) +
				'</h4><p>' +
				escapeHtml( meta.description ) +
				'</p></div><a class="fbt-ov__link" href="#" data-go="' +
				escapeHtml( meta.tab ) +
				'">' +
				escapeHtml( i18n.settings || 'Settings →' ) +
				'</a>';
			grid.appendChild( item );
		} );

		grid.addEventListener( 'click', function ( event ) {
			var link = event.target.closest( '[data-go]' );
			if ( ! link ) {
				return;
			}
			event.preventDefault();
			go( link.getAttribute( 'data-go' ) );
		} );
	}

	function sync( key, value ) {
		document.querySelectorAll( '[data-key="' + key + '"], [data-mirror="' + key + '"]' ).forEach( function ( input ) {
			input.checked = value;
		} );

		document.querySelectorAll( '[data-badge-for="' + key + '"]' ).forEach( function ( badge ) {
			badge.textContent = value ? i18n.on || 'On' : i18n.off || 'Off';
			badge.classList.toggle( 'is-off', ! value );
		} );

		document.querySelectorAll( '.fbt-card[data-master="' + key + '"]' ).forEach( function ( card ) {
			var body = card.querySelector( '.fbt-card__body' );
			if ( body ) {
				body.hidden = ! value;
			}
		} );

		refresh();
	}

	function refresh() {
		var count = 0;
		Object.keys( overviewMeta ).forEach( function ( key ) {
			var el = document.querySelector( '[data-key="' + key + '"]' );
			if ( el && el.checked ) {
				count += 1;
			}
		} );

		var countEl = root.querySelector( '[data-count-on]' );
		if ( countEl ) {
			countEl.textContent = String( count );
		}

		var byTab = {};
		Object.keys( overviewMeta ).forEach( function ( key ) {
			var tab = overviewMeta[ key ].tab;
			if ( ! byTab[ tab ] ) {
				byTab[ tab ] = [];
			}
			byTab[ tab ].push( key );
		} );

		Object.keys( byTab ).forEach( function ( tab ) {
			var on = byTab[ tab ].some( function ( key ) {
				var el = document.querySelector( '[data-key="' + key + '"]' );
				return el && el.checked;
			} );
			var dot = root.querySelector( '.fbt-settings__dot[data-for="' + tab + '"]' );
			if ( dot ) {
				dot.classList.toggle( 'is-on', on );
			}
		} );
	}

	function markDirty() {
		if ( ! ready || dirty ) {
			return;
		}
		dirty = true;
		if ( bar ) {
			bar.hidden = false;
			bar.classList.add( 'is-visible' );
		}
	}

	document.addEventListener( 'change', function ( event ) {
		var target = event.target;
		if ( ! target ) {
			return;
		}

		var key = target.getAttribute( 'data-key' ) || target.getAttribute( 'data-mirror' );
		if ( key ) {
			sync( key, !! target.checked );
		}

		if ( ( form && form.contains( target ) ) || target.getAttribute( 'data-mirror' ) ) {
			markDirty();
		}
	} );

	document.addEventListener( 'input', function ( event ) {
		if ( form && form.contains( event.target ) ) {
			markDirty();
		}
	} );

	if ( bar ) {
		var saveBtn = document.getElementById( 'fbt-save' );
		var discardBtn = document.getElementById( 'fbt-discard' );

		if ( saveBtn && form ) {
			saveBtn.addEventListener( 'click', function () {
				dirty = false;
				form.submit();
			} );
		}

		if ( discardBtn ) {
			discardBtn.addEventListener( 'click', function () {
				dirty = false;
				window.location.reload();
			} );
		}
	}

	window.addEventListener( 'beforeunload', function ( event ) {
		if ( ! dirty ) {
			return;
		}
		event.preventDefault();
		event.returnValue = '';
	} );

	Object.keys( overviewMeta ).forEach( function ( key ) {
		var el = document.querySelector( '[data-key="' + key + '"]' );
		if ( el ) {
			sync( key, el.checked );
		}
	} );

	refresh();
	go( readTabFromUrl() );
	ready = true;

	if ( toast && root.getAttribute( 'data-saved' ) === '1' ) {
		toast.classList.add( 'is-visible' );
		setTimeout( function () {
			toast.classList.remove( 'is-visible' );
		}, 1800 );

		var clean = new URL( window.location.href );
		clean.searchParams.delete( 'settings-updated' );
		history.replaceState( null, '', clean.pathname + clean.search + clean.hash );
	}
}() );
