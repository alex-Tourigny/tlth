(function($) {
	$(document).ready(function() {

		// Overwrite logo URL
		$('h1 a').attr('href', 'https://agencerubik.com').attr('target', '_blank');


		let slides = $('#login-slider > a');
		if( slides.length > 1 ){

			$('#login-slider').slick({
				infinite: true,
				slidesToShow: 1,
				dots: false,
				arrows: false,
				autoplay: true,
				autoPlayTimer: 5000,
				fade: true
			});
		}

	});

})(jQuery);