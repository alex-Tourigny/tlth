/**
 * Testimonial Feature Block JavaScript
 * Slider initialization
 */

(function($) {
	'use strict';

	/**
	 * Initialize testimonial slider
	 * @param {jQuery} $context - The context to search for the slider in
	 */
	function initTestimonialSlider($context) {
		const $blocks = $context ? $context.find('.testimonial-feature').addBack('.testimonial-feature') : $('.testimonial-feature');
		
		$blocks.each(function() {
			const $block = $(this);
			const $slider = $block.find('.testimonial-slider');
			const $dotsContainer = $block.find('.testimonial-pagination');
			
			if ($slider.hasClass('slick-initialized')) return;

			if ($slider.children().length > 1) {
				// Initialize slider
				$slider.on('init', function(event, slick) {
					// Move dots to the first slide's pagination container
					const $dots = $(slick.$slider).find('.slick-dots');
					const $firstPagination = $(slick.$slides[0]).find('.testimonial-pagination');
					$firstPagination.append($dots);
				});

				$slider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
					// Move dots to the next slide's pagination container
					const $dots = $(slick.$slider).find('.slick-dots');
					const $nextPagination = $(slick.$slides[nextSlide]).find('.testimonial-pagination');
					$nextPagination.append($dots);

					// Change image programmatically
					const $nextSlide = $(slick.$slides[nextSlide]);
					const imageUrl = $nextSlide.data('image');
					const $mainImage = $block.find('.main-testimonial-image');
					
					if (imageUrl && $mainImage.length) {
						$mainImage.css('opacity', 0);
						setTimeout(function() {
							$mainImage.attr('src', imageUrl);
							$mainImage.css('opacity', 1);
						}, 300);
					}
				});

				$slider.slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: false,
					dots: true,
					fade: true,
					cssEase: 'linear',
					infinite: true,
					adaptiveHeight: true
				});
			}
		});
	}

	// Initialize when document is ready
	$(document).ready(function() {
		initTestimonialSlider();
	});

	// Support for block editor
	if (window.acf) {
		window.acf.addAction('render_block_preview/type=testimonial-feature', function($el) {
			initTestimonialSlider($el);
		});
	}

})(jQuery);

