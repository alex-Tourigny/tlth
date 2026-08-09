/**
 * Split Img Full — cursor sparkle trail on magic image hover.
 */
(function ($) {
	'use strict';

	var COLORS = ['#ffc635', '#48c0b6', '#ffffff'];
	var SPAWN_INTERVAL = 55;
	var MAX_SPARKLES = 36;

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function spawnSparkle(layer, x, y) {
		var sparkle = document.createElement('span');
		var size = 12 + Math.random() * 10;
		var drift = (Math.random() - 0.5) * 28;
		var fall = 40 + Math.random() * 28;

		sparkle.className = 'split-img-full-sparkle split-img-full-sparkle--trail';
		sparkle.style.left = x + 'px';
		sparkle.style.top = y + 'px';
		sparkle.style.width = size + 'px';
		sparkle.style.height = size + 'px';
		sparkle.style.color = COLORS[Math.floor(Math.random() * COLORS.length)];
		sparkle.style.setProperty('--drift', drift + 'px');
		sparkle.style.setProperty('--fall', fall + 'px');
		sparkle.style.animationDuration = 0.75 + Math.random() * 0.45 + 's';

		layer.appendChild(sparkle);

		while (layer.children.length > MAX_SPARKLES) {
			layer.removeChild(layer.firstChild);
		}

		sparkle.addEventListener('animationend', function () {
			sparkle.remove();
		});
	}

	function initMagicSparkles($context) {
		var $medias = $context
			? $context.find('.split-img-full-media--magic').addBack('.split-img-full-media--magic')
			: $('.split-img-full-media--magic');

		$medias.each(function () {
			var $media = $(this);

			if ($media.data('magic-sparkles-init')) {
				return;
			}

			$media.data('magic-sparkles-init', true);

			var layer = $media.find('.split-img-full-sparkles')[0];
			if (!layer) {
				return;
			}

			var lastSpawn = 0;

			$media.on('mousemove.splitImgFullSparkles', function (e) {
				if (prefersReducedMotion()) {
					return;
				}

				var now = Date.now();
				if (now - lastSpawn < SPAWN_INTERVAL) {
					return;
				}

				lastSpawn = now;

				var rect = $media[0].getBoundingClientRect();
				spawnSparkle(layer, e.clientX - rect.left, e.clientY - rect.top);
			});

			$media.on('mouseleave.splitImgFullSparkles', function () {
				lastSpawn = 0;
			});
		});
	}

	$(function () {
		initMagicSparkles();
	});

	if (window.acf) {
		window.acf.addAction('render_block_preview/type=split-img-full', function ($el) {
			initMagicSparkles($el);
		});
	}
})(jQuery);
