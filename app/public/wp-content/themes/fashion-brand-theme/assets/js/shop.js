/**
 * Shop interactions: wishlist, quick view, filters drawer, price labels.
 *
 * @package Fashion_Brand_Theme
 */
(function () {
	'use strict';

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
		try {
			document.dispatchEvent(new CustomEvent('wren:wishlist-changed'));
		} catch (e) {
			/* ignore */
		}
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

		function syncTrack() {
			var min = Number(minInput.value);
			var max = Number(maxInput.value);
			var rangeMin = Number(root.getAttribute('data-min')) || 0;
			var rangeMax = Number(root.getAttribute('data-max')) || 100;
			var span = rangeMax - rangeMin || 1;
			var left = ((min - rangeMin) / span) * 100;
			var right = ((max - rangeMin) / span) * 100;
			root.style.setProperty('--price-left', left + '%');
			root.style.setProperty('--price-right', right + '%');
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
			syncTrack();
		}

		minInput.addEventListener('input', sync);
		maxInput.addEventListener('input', sync);
		sync();
	}

	function initFiltersDrawer() {
		var sidebar = document.querySelector('[data-shop-sidebar]');
		if (!sidebar) {
			return;
		}

		var openButtons = document.querySelectorAll('[data-shop-filters-open]');

		function setOpen(open, focusGroup) {
			document.body.classList.toggle('shop-filters-open', open);
			sidebar.classList.toggle('is-open', open);
			openButtons.forEach(function (btn) {
				btn.setAttribute('aria-expanded', open ? 'true' : 'false');
			});
			document.body.style.overflow = open ? 'hidden' : '';

			if (open && focusGroup) {
				var group = sidebar.querySelector('[data-shop-filter-group="' + focusGroup + '"]');
				if (group) {
					window.requestAnimationFrame(function () {
						group.scrollIntoView({ behavior: 'smooth', block: 'start' });
					});
				}
			}
		}

		document.addEventListener('click', function (event) {
			var openBtn = event.target.closest('[data-shop-filters-open]');
			if (openBtn) {
				event.preventDefault();
				var group = openBtn.getAttribute('data-shop-filters-open') || '';
				setOpen(true, group);
				return;
			}

			if (event.target.closest('[data-shop-filters-close]')) {
				setOpen(false);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && sidebar.classList.contains('is-open')) {
				setOpen(false);
			}
		});
	}

	function boot() {
		initWishlist();
		initQuickView();
		initPriceLabels();
		initFiltersDrawer();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
