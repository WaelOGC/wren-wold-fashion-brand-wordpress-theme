/**
 * Wishlist page — remove card from grid when heart is toggled off.
 *
 * @package Fashion_Brand_Theme
 */
(function () {
	'use strict';

	function showEmptyState() {
		var grid = document.querySelector('[data-wishlist-grid]');
		var tpl = document.querySelector('[data-wishlist-empty-template]');
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
})();
