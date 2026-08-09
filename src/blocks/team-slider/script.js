/**
 * Team Slider — Swiper: 370px-wide slides (`slidesPerView: 'auto'`), loop when ≥3 members, shallow Figma arc.
 *
 * During CSS transitions Swiper sets `swiper.translate` to the target immediately while the
 * wrapper still animates, so `slide.progress` would jump. We read the wrapper’s live matrix each
 * frame (rAF) and call `updateSlidesProgress(visualTranslate)` so arc motion follows the circle.
 */
(function ($) {
	'use strict';

	function stopTeamArcRaf(swiper) {
		if (swiper && swiper._teamArcRaf) {
			cancelAnimationFrame(swiper._teamArcRaf);
			swiper._teamArcRaf = null;
		}
	}

	function destroySwiper($swiperEl) {
		var el = $swiperEl && $swiperEl[0];
		if (el && el.swiper) {
			stopTeamArcRaf(el.swiper);
			el.swiper.destroy(true, true);
		}
	}

	/** Horizontal translate (px) from the wrapper’s computed transform (translate3d / matrix). */
	function readWrapperTranslateX(swiper) {
		var el = swiper.wrapperEl;
		if (!el || !window.getComputedStyle) {
			return null;
		}
		var t = window.getComputedStyle(el).transform;
		if (!t || t === 'none') {
			return 0;
		}
		if (t.indexOf('matrix3d') === 0) {
			var p3 = t
				.slice(9, -1)
				.split(',')
				.map(function (n) {
					return parseFloat(n.trim());
				});
			if (p3.length >= 13) {
				return p3[12];
			}
		}
		if (t.indexOf('matrix') === 0) {
			var p2 = t
				.slice(7, -1)
				.split(',')
				.map(function (n) {
					return parseFloat(n.trim());
				});
			if (p2.length >= 6) {
				return p2[4];
			}
		}
		return null;
	}

	/**
	 * Swiper’s internal `translate` vs what is painted on the wrapper can differ during CSS
	 * transitions; return the value that matches the painted frame.
	 */
	function translateForSlidesProgress(swiper) {
		var internal = swiper.translate;
		var actual = readWrapperTranslateX(swiper);
		var expected = swiper.rtlTranslate ? -internal : internal;
		if (actual !== null && !isNaN(actual) && Math.abs(actual - expected) > 0.75) {
			return swiper.rtlTranslate ? -actual : actual;
		}
		return internal;
	}

	/** Clip Swiper to 3×slide + 2×gap (positive gap = gutters between slides, no overlap). */
	function updateTeamThreeUpClip(swiper) {
		if (!swiper || !swiper.el || !swiper.slides || !swiper.slides.length) {
			return;
		}
		var w = swiper.slides[0].offsetWidth;
		if (!w) {
			return;
		}
		var g = swiper.params.spaceBetween;
		if (typeof g !== 'number' || isNaN(g)) {
			g = 0;
		}
		var span = Math.round(3 * w + 2 * g);
		if (span > 0) {
			swiper.el.style.setProperty('--team-3up-clip', span + 'px');
		}
	}

	function startTeamArcRaf(swiper) {
		stopTeamArcRaf(swiper);
		function step() {
			if (!swiper || swiper.destroyed) {
				stopTeamArcRaf(swiper);
				return;
			}
			applyArcTransforms(swiper);
			var expected = swiper.rtlTranslate ? -swiper.translate : swiper.translate;
			var actual = readWrapperTranslateX(swiper);
			var mismatch = actual !== null && !isNaN(actual) && Math.abs(actual - expected) > 0.5;
			if (mismatch || swiper.animating) {
				swiper._teamArcRaf = requestAnimationFrame(step);
			} else {
				swiper._teamArcRaf = null;
			}
		}
		swiper._teamArcRaf = requestAnimationFrame(step);
	}

	/**
	 * Semicircle: center at top of arc (smallest ty), sides lower; rotation follows tangent.
	 */
	function applyArcTransforms(swiper) {
		if (!swiper || !swiper.slides || swiper.slides.length < 2) {
			return;
		}

		var tr = translateForSlidesProgress(swiper);
		if (typeof swiper.updateSlidesProgress === 'function') {
			swiper.updateSlidesProgress(tr);
		}

		var vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
		var swiperW = swiper.width || (swiper.el && swiper.el.clientWidth) || vw;
		var R = Math.min(Math.max(swiperW * 0.8, 420), 800);
		var thetaMax = vw < 480 ? 0.22 : vw < 768 ? 0.26 : 0.30;
		var centerLift = vw < 640 ? 16 : 22;

		for (var i = 0; i < swiper.slides.length; i++) {
			var slideEl = swiper.slides[i];
			var p = slideEl.progress;
			if (typeof p !== 'number' || isNaN(p)) {
				p = 0;
			}
			p = Math.max(-3, Math.min(3, p));
			var theta = p * thetaMax;
			var cos = Math.cos(theta);
			var tx = 0;
			var ty = R * (1 - cos) - (1 - Math.min(1, Math.abs(p))) * centerLift;
			var rot = (-theta * 180) / Math.PI;
			var absP = Math.abs(p);
			var scale = 1.04 - absP * 0.045;
			if (scale < 0.9) {
				scale = 0.9;
			}

			/* Visible slides fade out smoothly as they leave the arc. */
			var op;
			if (absP <= 1.05) {
				op = 1;
			} else if (absP < 1.8) {
				op = 1 - (absP - 1.05) / 0.75;
			} else {
				op = 0;
			}
			slideEl.style.pointerEvents = op < 0.1 ? 'none' : '';

			slideEl.style.setProperty('--ts-arc-tx', tx.toFixed(2) + 'px');
			slideEl.style.setProperty('--ts-arc-ty', ty.toFixed(2) + 'px');
			slideEl.style.setProperty('--ts-arc-rot', rot.toFixed(3) + 'deg');
			slideEl.style.setProperty('--ts-arc-scale', scale.toFixed(3));
			slideEl.style.setProperty('--ts-arc-opacity', op.toFixed(3));

			var z = 40 + Math.round((1 - Math.min(1, absP)) * 35);
			slideEl.style.zIndex = String(z);
		}
	}

	function initTeamSlider($context) {
		var $blocks = $context
			? $context.find('.team-slider').addBack('.team-slider')
			: $('.team-slider');

		if (typeof Swiper === 'undefined') {
			return;
		}

		$blocks.each(function () {
			var $root = $(this);
			var $swiperEl = $root.find('.team-slider-swiper');
			if (!$swiperEl.length) {
				return;
			}

			destroySwiper($swiperEl);

			var slideCount = $swiperEl.find('.swiper-slide').length;
			if (slideCount < 2) {
				return;
			}

			var $prev = $root.find('.team-slider-nav--prev');
			var $next = $root.find('.team-slider-nav--next');

			/* ~3×370px visible when viewport allows; infinite loop when ≥3 real slides (Swiper duplicates DOM). */
			var initialSlide = 0;
			var useLoop = slideCount >= 3;

			var swiperInstance = new Swiper($swiperEl[0], {
				grabCursor: true,
				centeredSlides: true,
				slidesPerView: 'auto',
				initialSlide: initialSlide,
				speed: 520,
				loop: useLoop,
				rewind: slideCount === 2,
				watchSlidesProgress: true,
				slideToClickedSlide: true,
				spaceBetween: 36,
				breakpoints: {
					0: {
						spaceBetween: 20,
					},
					480: {
						spaceBetween: 28,
					},
					768: {
						spaceBetween: 34,
					},
					1024: {
						spaceBetween: 40,
					},
					1280: {
						spaceBetween: 48,
					},
				},
				navigation: {
					prevEl: $prev[0],
					nextEl: $next[0],
				},
				keyboard: {
					enabled: true,
					onlyInViewport: true,
				},
				on: {
					init: function () {
						updateTeamThreeUpClip(this);
						applyArcTransforms(this);
					},
					breakpoint: function () {
						updateTeamThreeUpClip(this);
						applyArcTransforms(this);
					},
					setTranslate: function () {
						applyArcTransforms(this);
					},
					transitionStart: function () {
						if (this.params.speed > 0) {
							startTeamArcRaf(this);
						}
					},
					transitionEnd: function () {
						stopTeamArcRaf(this);
						updateTeamThreeUpClip(this);
						applyArcTransforms(this);
					},
					resize: function () {
						updateTeamThreeUpClip(this);
						applyArcTransforms(this);
					},
				},
			});

			updateTeamThreeUpClip(swiperInstance);
			applyArcTransforms(swiperInstance);
		});
	}

	$(function () {
		initTeamSlider();
	});

	if (window.acf) {
		window.acf.addAction('render_block_preview/type=team-slider', function ($el) {
			initTeamSlider($el);
		});
	}
})(jQuery);
