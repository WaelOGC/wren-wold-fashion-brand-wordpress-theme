/**
 * Shop interactions: view toggle, wishlist, quick view, filters labels.
 *
 * @package Fashion_Brand_Theme
 */
(function () {
	'use strict';

	var VIEW_KEY = 'wren_shop_view';
	var WISH_KEY = 'wren_wishlist';

	function getWishlist() {
		try {
			return JSON.parse(localStorage.getItem(WISH_KEY) || '[]');
		} catch (e) {
			return [];
		}
	}

	function setWishlist(ids) {
		localStorage.setItem(WISH_KEY, JSON.stringify(ids));
	}

	function syncWishlistButtons() {
		var ids = getWishlist();
		document.querySelectorAll('[data-wishlist-toggle]').forEach(function (btn) {
			var id = String(btn.getAttribute('data-product-id'));
			var on = ids.indexOf(id) !== -1;
			btn.setAttribute('aria-pressed', on ? 'true' : 'false');
			btn.classList.toggle('is-active', on);
		});
	}

	function initViewToggle() {
		var main = document.querySelector('.shop-main');
		var buttons = document.querySelectorAll('[data-shop-view]');
		if (!main || !buttons.length) {
			return;
		}

		var saved = localStorage.getItem(VIEW_KEY) || 'grid';
		applyView(saved);

		buttons.forEach(function (btn) {
			btn.addEventListener('click', function () {
				applyView(btn.getAttribute('data-shop-view'));
			});
		});

		function applyView(view) {
			view = view === 'list' ? 'list' : 'grid';
			main.classList.toggle('is-list-view', view === 'list');
			localStorage.setItem(VIEW_KEY, view);
			buttons.forEach(function (btn) {
				var active = btn.getAttribute('data-shop-view') === view;
				btn.classList.toggle('is-active', active);
				btn.setAttribute('aria-pressed', active ? 'true' : 'false');
			});
		}
	}

	function initWishlist() {
		document.addEventListener('click', function (event) {
			var btn = event.target.closest('[data-wishlist-toggle]');
			if (!btn) {
				return;
			}
			event.preventDefault();
			var id = String(btn.getAttribute('data-product-id'));
			var ids = getWishlist();
			var idx = ids.indexOf(id);
			if (idx === -1) {
				ids.push(id);
			} else {
				ids.splice(idx, 1);
			}
			setWishlist(ids);
			syncWishlistButtons();
		});
		syncWishlistButtons();
	}

	function initQuickView() {
		var modal = document.querySelector('[data-quick-view-modal]');
		var content = document.querySelector('[data-quick-view-content]');
		if (!modal || !content || !window.fashionBrandThemeShop) {
			return;
		}

		function close() {
			modal.hidden = true;
			content.innerHTML = '';
			document.body.style.overflow = '';
		}

		document.addEventListener('click', function (event) {
			if (event.target.closest('[data-quick-view-close]')) {
				close();
				return;
			}

			var btn = event.target.closest('[data-quick-view]');
			if (!btn) {
				return;
			}

			event.preventDefault();
			var id = btn.getAttribute('data-product-id');
			content.innerHTML = '<p class="quick-view__loading">Loading…</p>';
			modal.hidden = false;
			document.body.style.overflow = 'hidden';

			var body = new FormData();
			body.append('action', 'fashion_brand_quick_view');
			body.append('nonce', window.fashionBrandThemeShop.nonce);
			body.append('product_id', id);

			fetch(window.fashionBrandThemeShop.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: body
			})
				.then(function (r) {
					return r.json();
				})
				.then(function (json) {
					if (!json || !json.success) {
						content.innerHTML = '<p>Unable to load product.</p>';
						return;
					}
					content.innerHTML = json.data.html;
					if (window.jQuery) {
						window.jQuery(document.body).trigger('wc_fragment_refresh');
					}
				})
				.catch(function () {
					content.innerHTML = '<p>Unable to load product.</p>';
				});
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && !modal.hidden) {
				close();
			}
		});
	}

	function initPriceLabels() {
		var root = document.querySelector('[data-price-filter]');
		if (!root) {
			return;
		}
		var minInput = root.querySelector('[data-price-min]');
		var maxInput = root.querySelector('[data-price-max]');
		var minLabel = root.querySelector('[data-price-min-label]');
		var maxLabel = root.querySelector('[data-price-max-label]');
		var currency = (window.fashionBrandThemeShop && window.fashionBrandThemeShop.currencySymbol) || '€';

		function format(n) {
			return currency + '\u00a0' + Number(n).toFixed(0);
		}

		function sync() {
			var min = Number(minInput.value);
			var max = Number(maxInput.value);
			if (min > max) {
				minInput.value = max;
				min = max;
			}
			minLabel.textContent = format(min);
			maxLabel.textContent = format(max);
		}

		minInput.addEventListener('input', sync);
		maxInput.addEventListener('input', sync);
	}

	function initFiltersDrawer() {
		var toggle = document.querySelector('[data-shop-filters-toggle]');
		var sidebar = document.querySelector('[data-shop-sidebar]');
		if (!toggle || !sidebar) {
			return;
		}

		function setOpen(open) {
			document.body.classList.toggle('shop-filters-open', open);
			sidebar.classList.toggle('is-open', open);
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			document.body.style.overflow = open ? 'hidden' : '';
		}

		toggle.addEventListener('click', function () {
			setOpen(!sidebar.classList.contains('is-open'));
		});

		document.addEventListener('click', function (event) {
			if (event.target.closest('[data-shop-filters-close]')) {
				setOpen(false);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				setOpen(false);
			}
		});

		window.addEventListener('resize', function () {
			if (window.matchMedia('(min-width: 901px)').matches) {
				setOpen(false);
			}
		});
	}

	function initCategorySublists() {
		document.addEventListener('click', function (event) {
			var btn = event.target.closest('.shop-filters__toggle');
			if (!btn) {
				return;
			}

			var sublistId = btn.getAttribute('aria-controls');
			var sublist = sublistId ? document.getElementById(sublistId) : null;
			if (!sublist) {
				return;
			}

			var willOpen = !sublist.classList.contains('is-open');
			sublist.classList.toggle('is-open', willOpen);
			btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
		});
	}

	function boot() {
		initViewToggle();
		initWishlist();
		initQuickView();
		initPriceLabels();
		initFiltersDrawer();
		initCategorySublists();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
