/**
 * Scroll-triggered animations for [data-animate] elements site-wide.
 * Also handles same-page anchor links (header offset + reveal animations before scroll).
 */
(function ($) {
	'use strict';

	var animationObserver = null;
	var tlthAnchorScrolling = false;

	function triggerAnimation($el) {
		if ($el.hasClass('animate-in')) return;

		var delay = parseInt($el.attr('data-animate-delay'), 10) || 0;

		$el.css('transition-delay', delay + 'ms');
		$el.addClass('animate-in');

		if (animationObserver) {
			animationObserver.unobserve($el[0]);
		}
	}

	function getHeaderOffset() {
		var header = document.getElementById('header');
		return header ? header.offsetHeight + 16 : 16;
	}

	function updateScrollOffsetVar() {
		document.documentElement.style.setProperty('--tlth-scroll-offset', getHeaderOffset() + 'px');
	}

	function revealAnimationsUpTo(target) {
		var targetBottom = target.getBoundingClientRect().bottom + window.scrollY;

		$('[data-animate]').each(function () {
			var el = this;
			var elTop = el.getBoundingClientRect().top + window.scrollY;

			if (elTop <= targetBottom) {
				triggerAnimation($(el));
			}
		});
	}

	function scrollToElement(target, smooth) {
		updateScrollOffsetVar();

		var offset = getHeaderOffset();
		var top = target.getBoundingClientRect().top + window.scrollY - offset;

		tlthAnchorScrolling = true;
		window.tlthAnchorScrolling = true;

		window.scrollTo({
			top: Math.max(0, top),
			behavior: smooth ? 'smooth' : 'instant',
		});

		window.setTimeout(function () {
			tlthAnchorScrolling = false;
			window.tlthAnchorScrolling = false;
		}, smooth ? 900 : 150);
	}

	function scrollToAnchor(hash, smooth) {
		if (!hash || hash === '#') {
			return false;
		}

		var target;

		try {
			target = document.querySelector(hash);
		} catch (err) {
			return false;
		}

		if (!target) {
			return false;
		}

		revealAnimationsUpTo(target);
		scrollToElement(target, smooth !== false);

		if (window.history && window.history.pushState) {
			window.history.pushState(null, '', hash);
		} else {
			window.location.hash = hash;
		}

		return true;
	}

	function getSamePageHash(href) {
		if (!href || href === '#') {
			return null;
		}

		try {
			var url = new URL(href, window.location.href);

			if (url.origin !== window.location.origin) {
				return null;
			}

			var currentPath = window.location.pathname.replace(/\/$/, '') || '/';
			var linkPath = url.pathname.replace(/\/$/, '') || '/';

			if (linkPath !== currentPath) {
				return null;
			}

			return url.hash || null;
		} catch (err) {
			if (href.charAt(0) === '#') {
				return href;
			}

			return null;
		}
	}

	function initAnchorLinks() {
		if ($('body').hasClass('block-editor-page') || $('.editor-styles-wrapper').length) {
			return;
		}

		updateScrollOffsetVar();
		$(window).on('resize orientationchange', updateScrollOffsetVar);

		$(document).on('click', 'a[href*="#"]', function (e) {
			if ($(this).is('[data-fancybox], .showcoupon')) {
				return;
			}

			var hash = getSamePageHash($(this).attr('href'));

			if (!hash || hash === '#') {
				return;
			}

			var target;

			try {
				target = document.querySelector(hash);
			} catch (err) {
				return;
			}

			if (!target) {
				return;
			}

			e.preventDefault();
			scrollToAnchor(hash, true);
		});

		var initialHashHandled = false;

		function handleInitialHash() {
			if (initialHashHandled || !window.location.hash) {
				return;
			}

			if (scrollToAnchor(window.location.hash, false)) {
				initialHashHandled = true;
			}
		}

		if (window.location.hash) {
			if ('scrollRestoration' in history) {
				history.scrollRestoration = 'manual';
			}

			window.scrollTo(0, 0);
			$(window).on('load', handleInitialHash);
		}
	}

	function initScrollAnimations() {
		if ($('body').hasClass('block-editor-page') || $('.editor-styles-wrapper').length) {
			return;
		}

		var elements = $('[data-animate]');

		if (elements.length === 0) {
			return;
		}

		animationObserver = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}

				var $el = $(entry.target);
				triggerAnimation($el);

				// Nested elements may never intersect alone (e.g. off-screen shapes).
				$el.find('[data-animate]').each(function () {
					triggerAnimation($(this));
				});

				animationObserver.unobserve(entry.target);
			});
		}, {
			threshold: 0.1,
			rootMargin: '0px 0px -50px 0px',
		});

		elements.each(function () {
			animationObserver.observe(this);
		});
	}

	window.tlthScrollToAnchor = scrollToAnchor;
	window.tlthGetHeaderOffset = getHeaderOffset;

	$(document).ready(function () {
		initScrollAnimations();
		initAnchorLinks();
	});
})(jQuery);
