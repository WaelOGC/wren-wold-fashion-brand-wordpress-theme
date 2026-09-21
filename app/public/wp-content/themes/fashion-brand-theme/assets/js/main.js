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

		navigation.classList.remove( 'is-open' );
		document.body.classList.remove( 'is-header-nav-open' );
		setExpanded( menuToggle, false );
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
			} else {
				closeNavigation();
			}

			updateHeaderOffset();
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
}() );
