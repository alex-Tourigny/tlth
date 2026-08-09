/**
 * FAQ Accordion Block JavaScript
 */

(function($) {
	'use strict';

	function initFaqAccordion($context) {
		const $scope = $context || $(document);

		$scope.find('.faq-question').on('click', function() {
			const $item = $(this).closest('.faq-item');
			const isOpen = $item.hasClass('is-open');

			// Close all open items in the same list
			$item.closest('.faq-list').find('.faq-item.is-open').removeClass('is-open')
				.find('.faq-question').attr('aria-expanded', 'false');

			if (!isOpen) {
				$item.addClass('is-open');
				$(this).attr('aria-expanded', 'true');
			}
		});
	}

	$(document).ready(function() {
		initFaqAccordion();
	});

	if (window.acf) {
		window.acf.addAction('render_block_preview/type=faq-accordion', function($el) {
			initFaqAccordion($el);
		});
	}

})(jQuery);
