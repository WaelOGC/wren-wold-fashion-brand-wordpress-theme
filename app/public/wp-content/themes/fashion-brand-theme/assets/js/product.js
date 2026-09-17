/**
 * Product page: gallery thumbs, zoom, variation UI, sticky ATC, size guide, share.
 *
 * @package Fashion_Brand_Theme
 */
(function () {
	'use strict';

	function setMainImage(main, src, srcset) {
		if (!main || !src) {
			return;
		}
		main.setAttribute('src', src);
		if (srcset) {
			main.setAttribute('srcset', srcset);
		} else {
			main.removeAttribute('srcset');
		}
	}

	function syncThumbActive(root, src) {
		var thumbs = root.querySelectorAll('[data-gallery-thumb]');
		if (!thumbs.length || !src) {
			return;
		}
		thumbs.forEach(function (btn) {
			var thumbSrc = btn.getAttribute('data-image-src') || '';
			var match = thumbSrc && (thumbSrc === src || src.indexOf(thumbSrc) !== -1 || thumbSrc.indexOf(src) !== -1);
			btn.classList.toggle('is-active', !!match);
			btn.setAttribute('aria-pressed', match ? 'true' : 'false');
		});
	}

	function initGallery(root) {
		var main = root.querySelector('[data-gallery-main], .product-gallery-main img');
		var thumbs = root.querySelectorAll('[data-gallery-thumb]');
		if (!main || !thumbs.length) {
			return;
		}

		thumbs.forEach(function (thumb) {
			thumb.addEventListener('click', function () {
				var src = thumb.getAttribute('data-image-src');
				var srcset = thumb.getAttribute('data-image-srcset');
				if (!src) {
					return;
				}
				setMainImage(main, src, srcset);
				thumbs.forEach(function (btn) {
					btn.classList.remove('is-active');
					btn.setAttribute('aria-pressed', 'false');
				});
				thumb.classList.add('is-active');
				thumb.setAttribute('aria-pressed', 'true');
			});
		});
	}

	function getProductVariations(form) {
		if (window.jQuery) {
			var data = window.jQuery(form).data('product_variations');
			if (Array.isArray(data)) {
				return data;
			}
		}
		var raw = form.getAttribute('data-product_variations');
		if (!raw || raw === 'false') {
			return [];
		}
		try {
			var parsed = JSON.parse(raw);
			return Array.isArray(parsed) ? parsed : [];
		} catch (err) {
			return [];
		}
	}

	function readChosenAttributes(form) {
		var chosen = {};
		form.querySelectorAll('table.variations select').forEach(function (select) {
			chosen[select.name] = select.value || '';
		});
		return chosen;
	}

	function findVariationImage(variations, chosen) {
		var candidates = variations.filter(function (variation) {
			if (!variation || !variation.attributes) {
				return false;
			}
			for (var key in chosen) {
				if (!Object.prototype.hasOwnProperty.call(chosen, key)) {
					continue;
				}
				if (!chosen[key]) {
					continue;
				}
				if (String(variation.attributes[key] || '') !== String(chosen[key])) {
					return false;
				}
			}
			return true;
		});

		for (var i = 0; i < candidates.length; i++) {
			if (candidates[i].image && candidates[i].image.src) {
				return candidates[i].image;
			}
		}
		return null;
	}

	function initVariationGallery() {
		var form = document.querySelector('form.variations_form');
		if (!form || !window.jQuery) {
			return;
		}

		var galleryRoot = document.querySelector('[data-product-gallery]');
		var main = galleryRoot
			? galleryRoot.querySelector('[data-gallery-main], .product-gallery-main img')
			: document.querySelector('[data-gallery-main], .product-gallery-main img');
		if (!main) {
			return;
		}

		var defaultSrc = main.getAttribute('src') || '';
		var defaultSrcset = main.getAttribute('srcset') || '';
		var variations = getProductVariations(form);

		function applyGalleryForCurrentSelection() {
			var chosen = readChosenAttributes(form);
			var anySelected = Object.keys(chosen).some(function (key) {
				return !!chosen[key];
			});

			if (!anySelected) {
				if (defaultSrc) {
					setMainImage(main, defaultSrc, defaultSrcset);
					if (galleryRoot) {
						syncThumbActive(galleryRoot, defaultSrc);
					}
				}
				return;
			}

			var image = findVariationImage(variations, chosen);
			if (image && image.src) {
				setMainImage(main, image.src, image.srcset || '');
				if (galleryRoot) {
					syncThumbActive(galleryRoot, image.src);
				}
			}
		}

		window.jQuery(form).on('found_variation', function (event, variation) {
			if (!variation || !variation.image || !variation.image.src) {
				return;
			}
			setMainImage(main, variation.image.src, variation.image.srcset || '');
			if (galleryRoot) {
				syncThumbActive(galleryRoot, variation.image.src);
			}
		});

		// WooCommerce fires reset_data when the selection is incomplete (e.g. color
		// chosen but size not). Do not snap back to the default image in that case —
		// update from any variation that matches the currently chosen attributes.
		window.jQuery(form).on('reset_data', function () {
			applyGalleryForCurrentSelection();
		});

		window.jQuery(form).on('change', 'table.variations select', function () {
			applyGalleryForCurrentSelection();
		});
	}

	function initZoom(root) {
		var wrap = root.querySelector('[data-zoom-root]');
		var img = wrap && wrap.querySelector('img');
		var lens = root.querySelector('[data-zoom-lens]');
		if (!wrap || !img || !lens || window.matchMedia('(max-width: 900px)').matches) {
			return;
		}

		function move(event) {
			var rect = wrap.getBoundingClientRect();
			var x = event.clientX - rect.left;
			var y = event.clientY - rect.top;
			var pctX = Math.max(0, Math.min(1, x / rect.width));
			var pctY = Math.max(0, Math.min(1, y / rect.height));
			lens.hidden = false;
			lens.style.left = x - lens.offsetWidth / 2 + 'px';
			lens.style.top = y - lens.offsetHeight / 2 + 'px';
			lens.style.backgroundImage = 'url("' + (img.currentSrc || img.src) + '")';
			lens.style.backgroundSize = rect.width * 2 + 'px ' + rect.height * 2 + 'px';
			lens.style.backgroundPosition = pctX * 100 + '% ' + pctY * 100 + '%';
		}

		function leave() {
			lens.hidden = true;
		}

		wrap.addEventListener('mousemove', move);
		wrap.addEventListener('mouseleave', leave);
	}

	function adjustColorBrightness(hex, percent) {
		hex = String(hex || '').replace(/^#/, '');
		if (hex.length === 3) {
			hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
		}
		if (hex.length !== 6 || !/^[0-9a-fA-F]+$/.test(hex)) {
			return '#D6D2CB';
		}
		var out = '';
		for (var i = 0; i < 3; i++) {
			var channel = parseInt(hex.substr(i * 2, 2), 16);
			channel = Math.round(channel + channel * (percent / 100));
			channel = Math.max(0, Math.min(255, channel));
			out += ('0' + channel.toString(16)).slice(-2);
		}
		return '#' + out.toUpperCase();
	}

	function colorHex(slug) {
		var map = (window.fashionBrandThemeProduct && window.fashionBrandThemeProduct.colorMap) || {};
		slug = String(slug || '').toLowerCase();
		if (map[slug]) {
			return map[slug];
		}
		if (slug.indexOf('light-') === 0) {
			var lightBase = slug.slice(6);
			if (map[lightBase]) {
				return adjustColorBrightness(map[lightBase], 18);
			}
		} else if (slug.indexOf('dark-') === 0) {
			var darkBase = slug.slice(5);
			if (map[darkBase]) {
				return adjustColorBrightness(map[darkBase], -18);
			}
		}
		return '#D6D2CB';
	}

	function enhanceVariations() {
		var form = document.querySelector('form.variations_form');
		if (!form) {
			return;
		}

		form.querySelectorAll('table.variations select').forEach(function (select) {
			if (select.dataset.uiReady) {
				return;
			}
			select.dataset.uiReady = '1';
			select.style.position = 'absolute';
			select.style.opacity = '0';
			select.style.pointerEvents = 'none';
			select.style.height = '0';
			select.style.width = '0';

			var name = select.getAttribute('name') || '';
			var isColor = name.indexOf('color') !== -1;
			var ui = document.createElement('div');
			ui.className = 'variation-ui' + (isColor ? ' variation-ui--swatches' : '');
			ui.setAttribute('data-variation-ui', name);

			Array.prototype.forEach.call(select.options, function (opt) {
				if (!opt.value) {
					return;
				}
				var btn = document.createElement('button');
				btn.type = 'button';
				btn.className = 'variation-ui__btn' + (isColor ? ' variation-ui__swatch' : '');
				btn.dataset.value = opt.value;
				if (isColor) {
					btn.style.setProperty('--swatch', colorHex(opt.value));
					btn.setAttribute('aria-label', opt.textContent.trim());
					btn.title = opt.textContent.trim();
				} else {
					btn.textContent = opt.textContent.trim();
				}
				if (opt.disabled) {
					btn.classList.add('is-disabled');
					btn.disabled = true;
				}
				btn.addEventListener('click', function () {
					if (btn.disabled) {
						return;
					}
					select.value = opt.value;
					select.dispatchEvent(new Event('change', { bubbles: true }));
					ui.querySelectorAll('.variation-ui__btn').forEach(function (b) {
						b.classList.toggle('is-active', b === btn);
					});
				});
				ui.appendChild(btn);
			});

			select.parentNode.appendChild(ui);

			if (window.jQuery) {
				window.jQuery(form).on('woocommerce_update_variation_values', function () {
					ui.querySelectorAll('.variation-ui__btn').forEach(function (btn) {
						var opt = Array.prototype.find.call(select.options, function (o) {
							return o.value === btn.dataset.value;
						});
						var disabled = !opt || opt.disabled || opt.getAttribute('disabled') !== null;
						btn.disabled = disabled;
						btn.classList.toggle('is-disabled', disabled);
						btn.classList.toggle('is-active', select.value === btn.dataset.value);
					});
				});
			}
		});
	}

	function initStickyAtc() {
		var bar = document.querySelector('[data-sticky-atc]');
		var main = document.querySelector('[data-main-atc]');
		var trigger = document.querySelector('[data-sticky-atc-trigger]');
		if (!bar || !main) {
			return;
		}

		function update() {
			if (!window.matchMedia('(max-width: 900px)').matches) {
				bar.hidden = true;
				return;
			}
			var rect = main.getBoundingClientRect();
			bar.hidden = rect.bottom > 0;
		}

		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		update();

		if (trigger) {
			trigger.addEventListener('click', function () {
				var btn = document.querySelector('.product-info .single_add_to_cart_button');
				if (btn) {
					btn.click();
				}
			});
		}
	}

	function initSizeGuide() {
		var modal = document.querySelector('[data-size-guide-modal]');
		if (!modal) {
			return;
		}
		document.addEventListener('click', function (event) {
			if (event.target.closest('[data-size-guide-open]')) {
				modal.hidden = false;
				document.body.style.overflow = 'hidden';
			}
			if (event.target.closest('[data-size-guide-close]')) {
				modal.hidden = true;
				document.body.style.overflow = '';
			}
		});
	}

	function initShare() {
		document.addEventListener('click', function (event) {
			var btn = event.target.closest('[data-share-product]');
			if (!btn) {
				return;
			}
			var url = window.location.href;
			if (navigator.share) {
				navigator.share({ title: document.title, url: url }).catch(function () {});
			} else if (navigator.clipboard) {
				navigator.clipboard.writeText(url);
				btn.textContent = 'Link copied';
			}
		});
	}

	function boot() {
		document.querySelectorAll('[data-product-gallery]').forEach(function (root) {
			initGallery(root);
			initZoom(root);
		});
		enhanceVariations();
		initVariationGallery();
		initStickyAtc();
		initSizeGuide();
		initShare();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
