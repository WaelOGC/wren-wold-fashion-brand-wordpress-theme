/**
 * Header interactions.
 *
 * @package Fashion_Brand_Theme
 */

( function () {
	'use strict';

	const siteHeader = document.querySelector( '.site-header' );
	const navigation = document.querySelector( '[data-header-navigation]' );
	const menuToggle = document.querySelector( '[data-menu-toggle]' );
	const searchPanel = document.querySelector( '[data-header-search-panel]' );
	const searchToggles = document.querySelectorAll( '[data-search-toggle]' );
	const desktopHoverQuery = window.matchMedia( '(min-width: 56.25rem) and (hover: hover) and (pointer: fine)' );
	const compactNavQuery = window.matchMedia( '(max-width: 56.24rem)' );

	function setExpanded( element, isExpanded ) {
		if ( ! element ) {
			return;
		}

		element.setAttribute( 'aria-expanded', isExpanded ? 'true' : 'false' );
	}

	function getSubmenuToggles() {
		return document.querySelectorAll( '.submenu-toggle' );
	}

	function usesCompactNavigation() {
		return compactNavQuery.matches;
	}

	function usesHoverDropdowns() {
		return desktopHoverQuery.matches;
	}

	function updateHeaderOffset() {
		if ( ! siteHeader ) {
			return;
		}

		document.documentElement.style.setProperty(
			'--site-header-offset',
			siteHeader.offsetHeight + 'px'
		);
	}

	function closeNavigation() {
		if ( ! navigation || ! menuToggle ) {
			return;
		}

		const wasOpen = navigation.classList.contains( 'is-open' );

		navigation.classList.remove( 'is-open' );
		document.body.classList.remove( 'is-header-nav-open' );
		setExpanded( menuToggle, false );

		if ( wasOpen && usesCompactNavigation() ) {
			menuToggle.focus();
		}
	}

	function focusFirstNavLink() {
		if ( ! navigation || ! usesCompactNavigation() ) {
			return;
		}

		const firstLink = navigation.querySelector( '.primary-menu > .menu-item > a' );

		if ( firstLink ) {
			firstLink.focus();
		}
	}

	function closeSearchPanel() {
		if ( ! searchPanel ) {
			return;
		}

		searchPanel.hidden = true;

		searchToggles.forEach( function ( toggle ) {
			setExpanded( toggle, false );
		} );
	}

	function closeSubmenus( exceptToggle ) {
		getSubmenuToggles().forEach( function ( toggle ) {
			if ( exceptToggle && toggle === exceptToggle ) {
				return;
			}

			setExpanded( toggle, false );

			const submenuId = toggle.getAttribute( 'aria-controls' );
			const submenu = submenuId ? document.getElementById( submenuId ) : null;

			if ( submenu ) {
				submenu.classList.remove( 'is-open' );
			}
		} );
	}

	function bindSubmenuToggles() {
		getSubmenuToggles().forEach( function ( toggle ) {
			if ( toggle.dataset.bound === 'true' ) {
				return;
			}

			toggle.dataset.bound = 'true';

			toggle.addEventListener( 'click', function () {
				if ( usesHoverDropdowns() ) {
					return;
				}

				const submenuId = toggle.getAttribute( 'aria-controls' );
				const submenu = submenuId ? document.getElementById( submenuId ) : null;

				if ( ! submenu ) {
					return;
				}

				const willOpen = ! submenu.classList.contains( 'is-open' );

				if ( willOpen ) {
					closeSubmenus( toggle );
				}

				submenu.classList.toggle( 'is-open', willOpen );
				setExpanded( toggle, willOpen );
				updateHeaderOffset();
			} );
		} );
	}

	if ( menuToggle && navigation ) {
		menuToggle.addEventListener( 'click', function () {
			const isOpen = ! navigation.classList.contains( 'is-open' );

			if ( isOpen ) {
				closeSearchPanel();
				closeSubmenus();
				navigation.classList.add( 'is-open' );
				document.body.classList.add( 'is-header-nav-open' );
				setExpanded( menuToggle, true );
				updateHeaderOffset();
				focusFirstNavLink();
			} else {
				closeNavigation();
				updateHeaderOffset();
			}
		} );
	}

	searchToggles.forEach( function ( toggle ) {
		toggle.addEventListener( 'click', function () {
			if ( ! searchPanel ) {
				return;
			}

			const willOpen = searchPanel.hidden;
			closeNavigation();
			closeSubmenus();

			searchPanel.hidden = ! willOpen;
			setExpanded( toggle, willOpen );
			updateHeaderOffset();

			if ( willOpen ) {
				const searchField = searchPanel.querySelector( '#header-search-field' );

				if ( searchField ) {
					searchField.focus();
				}
			}
		} );
	} );

	bindSubmenuToggles();

	function bindShopNavToggles() {
		document.addEventListener( 'click', function ( event ) {
			const target = event.target;

			if ( ! ( target instanceof Element ) ) {
				return;
			}

			const btn = target.closest( '.shop-nav__toggle' );

			if ( ! btn ) {
				return;
			}

			event.preventDefault();
			event.stopPropagation();

			const sublistId = btn.getAttribute( 'aria-controls' );
			const sublist = sublistId ? document.getElementById( sublistId ) : null;

			if ( ! sublist ) {
				return;
			}

			const willOpen = ! sublist.classList.contains( 'is-open' );
			sublist.classList.toggle( 'is-open', willOpen );
			setExpanded( btn, willOpen );
			updateHeaderOffset();
		} );
	}

	bindShopNavToggles();

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key ) {
			return;
		}

		closeNavigation();
		closeSearchPanel();
		closeSubmenus();
	} );

	document.addEventListener( 'click', function ( event ) {
		const target = event.target;

		if ( ! ( target instanceof Element ) ) {
			return;
		}

		if ( searchPanel && ! searchPanel.hidden ) {
			const clickedInsideSearch = target.closest( '[data-header-search-panel]' ) ||
				target.closest( '[data-search-toggle]' );

			if ( ! clickedInsideSearch ) {
				closeSearchPanel();
			}
		}
	} );

	function handleViewportChange() {
		updateHeaderOffset();

		if ( ! usesCompactNavigation() ) {
			closeNavigation();
		}

		if ( usesHoverDropdowns() ) {
			closeSubmenus();
		}
	}

	desktopHoverQuery.addEventListener( 'change', handleViewportChange );
	compactNavQuery.addEventListener( 'change', handleViewportChange );
	window.addEventListener( 'resize', updateHeaderOffset );
	window.addEventListener( 'orientationchange', updateHeaderOffset );

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', updateHeaderOffset );
	} else {
		updateHeaderOffset();
	}

	if ( siteHeader && siteHeader.classList.contains( 'site-header--scroll-shadow' ) ) {
		window.addEventListener(
			'scroll',
			function () {
				siteHeader.classList.toggle( 'is-scrolled', window.scrollY > 0 );
			},
			{ passive: true }
		);
	}

	function getWishlistConfig() {
		return window.fashionBrandThemeWishlist || {};
	}

	function isWishlistLoggedIn() {
		const v = getWishlistConfig().isLoggedIn;
		return v === true || v === 1 || v === '1';
	}

	function syncMobileWishlistBadge( count ) {
		const badges = document.querySelectorAll( '[data-wishlist-count]' );
		if ( ! badges.length ) {
			return;
		}

		const n = typeof count === 'number' ? count : 0;

		badges.forEach( function ( badge ) {
			badge.textContent = String( n );
			if ( n > 0 ) {
				badge.removeAttribute( 'hidden' );
			} else {
				badge.setAttribute( 'hidden', '' );
			}
		} );
	}

	function syncWishlistButtons( ids ) {
		const idSet = {};
		( ids || [] ).forEach( function ( id ) {
			idSet[ String( id ) ] = true;
		} );

		document.querySelectorAll( '[data-wishlist-toggle]' ).forEach( function ( btn ) {
			const id = String( btn.getAttribute( 'data-product-id' ) || '' );
			const on = !! idSet[ id ];
			btn.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
			btn.classList.toggle( 'is-active', on );
		} );
	}

	function emitWishlistChanged( detail ) {
		try {
			document.dispatchEvent( new CustomEvent( 'wren:wishlist-changed', { detail: detail || {} } ) );
		} catch ( e ) {
			/* ignore */
		}
	}

	function dismissGuestTip() {
		document.querySelectorAll( '[data-wishlist-signin-tip]' ).forEach( function ( tip ) {
			tip.remove();
		} );
	}

	function showGuestSignInTip( btn ) {
		dismissGuestTip();

		const cfg = getWishlistConfig();
		const tip = document.createElement( 'div' );
		tip.className = 'wishlist-signin-tip';
		tip.setAttribute( 'data-wishlist-signin-tip', '' );
		tip.setAttribute( 'role', 'status' );

		const text = document.createElement( 'span' );
		text.className = 'wishlist-signin-tip__text';
		text.textContent = ( cfg.i18n && cfg.i18n.signIn ) || 'Sign in to save favorites';

		tip.appendChild( text );

		if ( cfg.accountUrl ) {
			const link = document.createElement( 'a' );
			link.className = 'wishlist-signin-tip__link';
			link.href = cfg.accountUrl;
			link.textContent = ( cfg.i18n && cfg.i18n.signInCta ) || 'Sign in';
			tip.appendChild( link );
		}

		document.body.appendChild( tip );

		const rect = btn.getBoundingClientRect();
		const tipWidth = tip.offsetWidth || 220;
		let left = rect.left + rect.width / 2 - tipWidth / 2;
		left = Math.max( 8, Math.min( left, window.innerWidth - tipWidth - 8 ) );
		tip.style.position = 'fixed';
		tip.style.left = left + 'px';
		tip.style.top = rect.bottom + 8 + 'px';

		const dismissTimer = window.setTimeout( dismissGuestTip, 4000 );

		function onDocClick( event ) {
			if ( event.target.closest( '[data-wishlist-signin-tip]' ) ) {
				return;
			}
			if ( event.target.closest( '[data-wishlist-toggle]' ) === btn ) {
				return;
			}
			window.clearTimeout( dismissTimer );
			dismissGuestTip();
			document.removeEventListener( 'click', onDocClick );
		}

		window.setTimeout( function () {
			document.addEventListener( 'click', onDocClick );
		}, 0 );
	}

	function applyWishlistState( data ) {
		const ids = data && data.ids ? data.ids : [];
		const count = typeof data.count === 'number' ? data.count : ids.length;
		syncWishlistButtons( ids );
		syncMobileWishlistBadge( count );
		emitWishlistChanged( {
			ids: ids,
			count: count,
			added: data && typeof data.added === 'boolean' ? data.added : undefined,
			productId: data && data.product_id ? data.product_id : undefined,
		} );
	}

	function fetchWishlistState() {
		const cfg = getWishlistConfig();
		if ( ! isWishlistLoggedIn() || ! cfg.ajaxUrl || ! cfg.nonce ) {
			syncMobileWishlistBadge( 0 );
			syncWishlistButtons( [] );
			return;
		}

		const url =
			cfg.ajaxUrl +
			( cfg.ajaxUrl.indexOf( '?' ) === -1 ? '?' : '&' ) +
			'action=fbt_wishlist_get&nonce=' +
			encodeURIComponent( cfg.nonce );

		fetch( url, { credentials: 'same-origin' } )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( json ) {
				if ( ! json || ! json.success || ! json.data ) {
					return;
				}
				applyWishlistState( json.data );
			} )
			.catch( function () {
				/* ignore */
			} );
	}

	function toggleWishlist( productId, btn ) {
		const cfg = getWishlistConfig();
		if ( ! cfg.ajaxUrl || ! cfg.nonce ) {
			return;
		}

		if ( btn ) {
			btn.disabled = true;
		}

		const body = new FormData();
		body.append( 'action', 'fbt_wishlist_toggle' );
		body.append( 'nonce', cfg.nonce );
		body.append( 'product_id', String( productId ) );

		fetch( cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body,
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( json ) {
				if ( ! json || ! json.success || ! json.data ) {
					return;
				}
				applyWishlistState( json.data );
			} )
			.catch( function () {
				/* ignore */
			} )
			.finally( function () {
				if ( btn ) {
					btn.disabled = false;
				}
			} );
	}

	document.addEventListener( 'click', function ( event ) {
		const btn = event.target.closest( '[data-wishlist-toggle]' );
		if ( ! btn ) {
			return;
		}

		event.preventDefault();
		event.stopPropagation();

		const cfg = getWishlistConfig();
		if ( ! isWishlistLoggedIn() ) {
			showGuestSignInTip( btn );
			return;
		}

		const productId = btn.getAttribute( 'data-product-id' );
		if ( ! productId ) {
			return;
		}

		toggleWishlist( productId, btn );
	} );

	window.__fbtWishlistClickBound = true;

	fetchWishlistState();
	document.addEventListener( 'wren:wishlist-changed', function ( event ) {
		const detail = event && event.detail ? event.detail : null;
		if ( detail && typeof detail.count === 'number' ) {
			syncMobileWishlistBadge( detail.count );
		}
	} );
}() );
