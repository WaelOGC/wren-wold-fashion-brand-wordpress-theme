/**
 * Single product gallery — thumbs, arrows, counter, swipe.
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

	function updateCounter(counter, index, total) {
		if (!counter) {
			return;
		}
		counter.textContent = String(index + 1) + '/' + String(total);
	}

	function goToIndex(root, main, thumbs, counter, index) {
		if (!thumbs.length) {
			return;
		}
		var safe = ((index % thumbs.length) + thumbs.length) % thumbs.length;
		var thumb = thumbs[safe];
		var src = thumb.getAttribute('data-image-src');
		var srcset = thumb.getAttribute('data-image-srcset');
		if (!src) {
			return;
		}
		setMainImage(main, src, srcset);
		thumbs.forEach(function (btn, i) {
			var active = i === safe;
			btn.classList.toggle('is-active', active);
			btn.setAttribute('aria-pressed', active ? 'true' : 'false');
		});
		updateCounter(counter, safe, thumbs.length);
		root._galleryIndex = safe;
	}

	function currentIndex(thumbs) {
		for (var i = 0; i < thumbs.length; i++) {
			if (thumbs[i].classList.contains('is-active') || thumbs[i].getAttribute('aria-pressed') === 'true') {
				return i;
			}
		}
		return 0;
	}

	function initGallery(root) {
		var main = root.querySelector('[data-gallery-main], .product-gallery-main img');
		var thumbs = Array.prototype.slice.call(root.querySelectorAll('[data-gallery-thumb]'));
		var counter = root.querySelector('[data-gallery-counter]');
		var prev = root.querySelector('[data-gallery-prev]');
		var next = root.querySelector('[data-gallery-next]');
		var stage = root.querySelector('.product-gallery-main');

		if (!main) {
			return;
		}

		root._galleryIndex = currentIndex(thumbs);
		if (thumbs.length) {
			updateCounter(counter, root._galleryIndex, thumbs.length);
		}

		thumbs.forEach(function (thumb, index) {
			thumb.addEventListener('click', function () {
				goToIndex(root, main, thumbs, counter, index);
			});
		});

		if (prev) {
			prev.addEventListener('click', function (event) {
				event.preventDefault();
				goToIndex(root, main, thumbs, counter, (root._galleryIndex || 0) - 1);
			});
		}

		if (next) {
			next.addEventListener('click', function (event) {
				event.preventDefault();
				goToIndex(root, main, thumbs, counter, (root._galleryIndex || 0) + 1);
			});
		}

		if (!stage || thumbs.length < 2) {
			return;
		}

		var touchStartX = 0;
		var touchStartY = 0;
		var tracking = false;

		stage.addEventListener(
			'touchstart',
			function (event) {
				if (!event.changedTouches || !event.changedTouches.length) {
					return;
				}
				tracking = true;
				touchStartX = event.changedTouches[0].clientX;
				touchStartY = event.changedTouches[0].clientY;
			},
			{ passive: true }
		);

		stage.addEventListener(
			'touchend',
			function (event) {
				if (!tracking || !event.changedTouches || !event.changedTouches.length) {
					return;
				}
				tracking = false;
				var dx = event.changedTouches[0].clientX - touchStartX;
				var dy = event.changedTouches[0].clientY - touchStartY;
				if (Math.abs(dx) < 40 || Math.abs(dx) < Math.abs(dy)) {
					return;
				}
				if (dx < 0) {
					goToIndex(root, main, thumbs, counter, (root._galleryIndex || 0) + 1);
				} else {
					goToIndex(root, main, thumbs, counter, (root._galleryIndex || 0) - 1);
				}
			},
			{ passive: true }
		);
	}

	window.fashionBrandThemeInitProductGallery = initGallery;
})();
