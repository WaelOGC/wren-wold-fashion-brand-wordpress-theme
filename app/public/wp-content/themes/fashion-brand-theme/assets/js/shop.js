/**
 * Shop interactions: wishlist, quick view, filters drawer, price labels.
 *
 * @package Fashion_Brand_Theme
 */
(function () {
	'use strict';

	function getWishlistConfig() {
		return window.fashionBrandThemeWishlist || {};
	}

	function isWishlistLoggedIn() {
		var v = getWishlistConfig().isLoggedIn;
		return v === true || v === 1 || v === '1';
	}

	function syncWishlistButtons(ids) {
		var idSet = {};
		(ids || []).forEach(function (id) {
			idSet[String(id)] = true;
		});
		document.querySelectorAll('[data-wishlist-toggle]').forEach(function (btn) {
			var id = String(btn.getAttribute('data-product-id') || '');
			var on = !!idSet[id];
			btn.setAttribute('aria-pressed', on ? 'true' : 'false');
			btn.classList.toggle('is-active', on);
		});
	}

	function emitWishlistChanged(detail) {
		try {
			document.dispatchEvent(new CustomEvent('wren:wishlist-changed', { detail: detail || {} }));
		} catch (e) {
			/* ignore */
		}
	}

	function dismissGuestTip() {
		document.querySelectorAll('[data-wishlist-signin-tip]').forEach(function (tip) {
			tip.remove();
		});
	}

	function showGuestSignInTip(btn) {
		dismissGuestTip();
		var cfg = getWishlistConfig();
		var tip = document.createElement('div');
		tip.className = 'wishlist-signin-tip';
		tip.setAttribute('data-wishlist-signin-tip', '');
		tip.setAttribute('role', 'status');

		var text = document.createElement('span');
		text.className = 'wishlist-signin-tip__text';
		text.textContent = (cfg.i18n && cfg.i18n.signIn) || 'Sign in to save favorites';
		tip.appendChild(text);

		if (cfg.accountUrl) {
			var link = document.createElement('a');
			link.className = 'wishlist-signin-tip__link';
			link.href = cfg.accountUrl;
			link.textContent = (cfg.i18n && cfg.i18n.signInCta) || 'Sign in';
			tip.appendChild(link);
		}

		document.body.appendChild(tip);

		var rect = btn.getBoundingClientRect();
		var tipWidth = tip.offsetWidth || 220;
		var left = rect.left + rect.width / 2 - tipWidth / 2;
		left = Math.max(8, Math.min(left, window.innerWidth - tipWidth - 8));
		tip.style.position = 'fixed';
		tip.style.left = left + 'px';
		tip.style.top = rect.bottom + 8 + 'px';

		var dismissTimer = window.setTimeout(dismissGuestTip, 4000);

		function onDocClick(event) {
			if (event.target.closest('[data-wishlist-signin-tip]')) {
				return;
			}
			if (event.target.closest('[data-wishlist-toggle]') === btn) {
				return;
			}
			window.clearTimeout(dismissTimer);
			dismissGuestTip();
			document.removeEventListener('click', onDocClick);
		}

		window.setTimeout(function () {
			document.addEventListener('click', onDocClick);
		}, 0);
	}

	function toggleWishlistAjax(productId, btn) {
		var cfg = getWishlistConfig();
		if (!cfg.ajaxUrl || !cfg.nonce) {
			return;
		}

		if (btn) {
			btn.disabled = true;
		}

		var body = new FormData();
		body.append('action', 'fbt_wishlist_toggle');
		body.append('nonce', cfg.nonce);
		body.append('product_id', String(productId));

		fetch(cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body
		})
			.then(function (r) {
				return r.json();
			})
			.then(function (json) {
				if (!json || !json.success || !json.data) {
					return;
				}
				syncWishlistButtons(json.data.ids || []);
				emitWishlistChanged({
					ids: json.data.ids || [],
					count: json.data.count,
					added: json.data.added,
					productId: json.data.product_id
				});
			})
			.catch(function () {
				/* ignore */
			})
			.finally(function () {
				if (btn) {
					btn.disabled = false;
				}
			});
	}

	function initWishlist() {
		// main.js owns the global click handler; shop only binds if main did not.
		if (window.__fbtWishlistClickBound) {
			return;
		}
		window.__fbtWishlistClickBound = true;

		document.addEventListener('click', function (event) {
			var btn = event.target.closest('[data-wishlist-toggle]');
			if (!btn) {
				return;
			}
			event.preventDefault();
			event.stopPropagation();

			var cfg = getWishlistConfig();
			if (!isWishlistLoggedIn()) {
				showGuestSignInTip(btn);
				return;
			}

			var id = btn.getAttribute('data-product-id');
			if (!id) {
				return;
			}
			toggleWishlistAjax(id, btn);
		});
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
