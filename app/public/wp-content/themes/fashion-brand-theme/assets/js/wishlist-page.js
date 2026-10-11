/**
 * Wishlist page — view toggle + remove card when heart is toggled off.
 *
 * @package Fashion_Brand_Theme
 */
(function () {
	'use strict';

	var STORAGE_KEY = 'wrenwold_wishlist_view';

	function getStoredView() {
		try {
			var v = localStorage.getItem(STORAGE_KEY);
			return v === 'list' || v === 'grid' ? v : 'grid';
		} catch (e) {
			return 'grid';
		}
	}

	function setView(view) {
		var mode = view === 'list' ? 'list' : 'grid';
		document.documentElement.setAttribute('data-wishlist-view', mode);

		try {
			localStorage.setItem(STORAGE_KEY, mode);
		} catch (e) {}

		var buttons = document.querySelectorAll('[data-wishlist-view-toggle] [data-wishlist-view]');
		buttons.forEach(function (btn) {
			var active = btn.getAttribute('data-wishlist-view') === mode;
			btn.classList.toggle('is-active', active);
			btn.setAttribute('aria-pressed', active ? 'true' : 'false');
		});
	}

	function initViewToggle() {
		var toolbar = document.querySelector('[data-wishlist-view-toggle]');
		if (!toolbar) {
			return;
		}

		setView(getStoredView());

		toolbar.addEventListener('click', function (event) {
			var btn = event.target.closest('[data-wishlist-view]');
			if (!btn || !toolbar.contains(btn)) {
				return;
			}
			setView(btn.getAttribute('data-wishlist-view'));
		});
	}

	function showEmptyState() {
		var grid = document.querySelector('[data-wishlist-grid]');
		var tpl = document.querySelector('[data-wishlist-empty-template]');
		var toolbar = document.querySelector('[data-wishlist-view-toggle]');
		if (!grid) {
			return;
		}

		var products = grid.querySelectorAll('ul.products li.product');
		if (products.length > 0) {
			return;
		}

		var list = grid.querySelector('ul.products');
		if (list) {
			list.remove();
		}

		if (toolbar) {
			toolbar.remove();
		}

		if (tpl && tpl.content) {
			grid.appendChild(tpl.content.cloneNode(true));
		}
	}

	function removeCardForProduct(productId) {
		if (!productId) {
			return;
		}

		var btn = document.querySelector(
			'[data-wishlist-grid] [data-wishlist-toggle][data-product-id="' + String(productId) + '"]'
		);
		if (!btn) {
			return;
		}

		var card = btn.closest('li.product');
		if (card) {
			card.remove();
		}

		showEmptyState();
	}

	document.addEventListener('wren:wishlist-changed', function (event) {
		var detail = event && event.detail ? event.detail : null;
		if (!detail || detail.added !== false) {
			return;
		}
		removeCardForProduct(detail.productId);
	});

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initViewToggle);
	} else {
		initViewToggle();
	}
})();
