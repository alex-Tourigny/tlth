/**
 * Book Showcase Block JavaScript
 * Navigation and pagination functionality
 */

(function($) {
	'use strict';

	/**
	 * Initialize book showcase navigation
	 */
	function initBookShowcase() {
		$('.book-showcase').each(function() {
			const $showcase = $(this);
			const $slider = $showcase.find('.books-grid');
			const $prevBtn = $showcase.find('.nav-arrow.prev');
			const $nextBtn = $showcase.find('.nav-arrow.next');
			const $dotsContainer = $showcase.find('.pagination-dots');
			
			if ($slider.hasClass('slick-initialized')) return;

			$slider.slick({
				slidesToShow: 4,
				slidesToScroll: 1,
				arrows: false,
				dots: true,
				appendDots: $dotsContainer,
				responsive: [
					{
						breakpoint: 1024,
						settings: {
							slidesToShow: 2
						}
					},
					{
						breakpoint: 640,
						settings: {
							slidesToShow: 1
						}
					}
				]
			});
			
			// Connect custom arrows
			$prevBtn.on('click', function() {
				$slider.slick('slickPrev');
			});

			$nextBtn.on('click', function() {
				$slider.slick('slickNext');
			});
		});
	}

	// Initialize when document is ready
	$(document).ready(function() {
		initBookShowcase();
	});

})(jQuery);
