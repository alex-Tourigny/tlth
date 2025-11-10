(function($) {

	$(document).ready(function(){
		/*
		 * Header class
		 */
		header_class();

		/*
		 * Mobile menu
		 */
		$('.mobile-nav-trigger').click(function(){
			$('body, #mobile-nav, #header, #main').toggleClass('active');
		});

		$('body').on('click', '#main.active', function(){
			$('body, #mobile-nav, #header, #main').toggleClass('active');
		});



		/*
		 * Sticky sidebar
		 */
		let header_h = $('#header').outerHeight() + 40;
		$('.the-sidebar').stickySidebar({
			topSpacing: header_h,
			bottomSpacing: 60,
			minWidth: 991,
		});

		/*
		 * Sliders
		 */
		let hero_slider = $('.hero-slider')

		hero_slider.on('init', function(event, slick, currentSlide, nextSlide){
			hero_slider.find('.hero').addClass('init');
		});

		hero_slider.slick({
			dots: false,
			arrows: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: false,
			autoplaySpeed: 3000,
			lazyLoad: 'progressive',
		});

		$('.testimonials-slider').slick({
			dots: true,
			slidesToShow: 2,
			slidesToScroll: 2,
			responsive: [
				{
					breakpoint: 1024,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						infinite: true,
						dots: true,
					}
				}
			]
		});

		/*
		 * Animate on scroll
		 */
		AOS.init({
			once: true
		});

		$(function() {
			AOS.init();
		});

		/*- ScrollMagic -*/

		if( $(window).width() > 768 ){
			// Product - Slider
			var nbSlide = $("#sm-container .slide").length;
			var stepAmount = 100/nbSlide;
			var offsetTop = -120;


			console.log(nbSlide);

			var controller = new ScrollMagic.Controller();

			// About us - Slider
			new ScrollMagic.Scene({
				triggerElement: '#sm-container',
				triggerHook: 0,
				offset: offsetTop,
				duration: 1000*(nbSlide) + "px", // the scene should last for a scroll distance of 100px
			})
				.setPin('#sm-container') // pins the element for the the scene's duration
				.on("progress", function (e) {
					var currentStep = Math.ceil(( e.progress*100) / stepAmount );
					var stepProgression = ((e.progress*100) - ((currentStep-1)*stepAmount)) * nbSlide;

					console.log(currentStep)

					if(currentStep!= 0){

						$("#sm-container .slide").eq(currentStep).css("transform","translateY(" + Math.ceil(100 - stepProgression) + "%)");

					}

					for(var i=1; i<= nbSlide+1; i++){

						if(i<currentStep){
							// set previous slides to final state
							$("#sm-container .slide").eq(i).css("transform","translateY(0%)");

						}else if( i>currentStep ){
							// set next slides to initial state
							$("#sm-container .slide").eq(i).css("transform","translateY(100%)");

						}

					}

				})
				.addTo(controller); // assign the scene to the controller
		}




		/*- xmas countdown -*/

		if( $('.countdown-container') ){

			var minutes = parseInt( $('.countdown-container').data("minutes-remaining") );
			var hours = parseInt( $('.countdown-container').data("hours-remaining") );
			var days = parseInt( $('.countdown-container').data("days-remaining") );

			setInterval(function() {

				if( minutes > 0){
					minutes--;
				}else{
					minutes = 59;

					if( hours > 0){
						hours--;

					}else{
						hours = 23;

						if( days > 0){
							days--;

						}else{
							days=0;
						}
					}
				}

				$('.number.minutes').text(minutes);
				$('.number.hours').text(hours);
				$('.number.days').text(days);

			}, 60000);

		}


		/*
		 * Accordeons
		 */
		$('.dropdown').on('click', function(){
			$(this).toggleClass('active');
			$(this).find('.the-content').slideToggle(200);
		});

		/*
		 * Chosen dropdowns
		 */
		$('.woocommerce-ordering > .orderby').chosen();

		$('.input-select').chosen();

		$('.country_to_state').chosen();
		$('.state_select').chosen();

		$('.affwp-graphs-date-options').chosen();

		$('.mailpoet_select').chosen();

		/*
		 * Shop filter toggle
		 */
		$('.widget-filter').click(function(){
			$('.widget').toggleClass('active');
		});

		/*
		 * Workshop buttons
		 */
		$('.form-step-nav.next').click(function(){
			navigate_workshop( $(this), 'next');
		});

		$('.form-step-nav.prev').click(function(){
			navigate_workshop( $(this), 'prev');
		});

		$('.generate-workshop-book-content').click(function(e){
			e.preventDefault();

			$('.workshop-book-holder').addClass('loading');

			let form = $(this).parents('form');
			let form_data = new FormData();

			form_data.append('action', 'ajax_generate_workshop_book_content');

			form.serializeArray().reduce(function(obj, item) {
				form_data.append(item.name, item.value);
			}, {});

			$.ajax({
				url: window.ajax_url,
				data: form_data,
				type: "POST",
				processData: false,
				contentType: false,
				success: function (r, textStatus, jqXHR) {

					$('#book-slider').removeClass('loading');

					$('#book-slider .inner').html(r.data);
					$('#book-slider .image-sets').shuffleChildren();
					$('#book-slider .inner > .spread:first-child').addClass('active');
				}
			});
		});

		$('body').on('click', '.workshop-book-nav.prev', function(){
			navigate_book( $(this), 'prev');
		});

		$('body').on('click', '.workshop-book-nav.next', function(){
			navigate_book( $(this), 'next');
		});

		$('body').on('click', '.image-sets a', function(){
			let status = $(this).data('status');
			let image_set = $(this).parents('.image-sets');
			let spread = $(this).parents('.spread');

			image_set.find('a').removeClass('active');

			$(this).addClass('active');

			if(status == 'correct'){
				image_set.removeClass('incorrect').addClass('correct');
				spread.find('.book-page.image').addClass('correct');
			} else {
				image_set.removeClass('correct').addClass('incorrect');
				spread.find('.book-page.image').removeClass('correct');
			}

			spread.find('.messages p').removeClass('active');
			spread.find('.messages p.' +  status).addClass('active');
		});

		$('body').on('click', '.workshop-verify-book', function(){
			let dialog = $(this).parents('.dialog');

			dialog.addClass('loading');
			$('#workshop-confirmation-book').addClass('loading');

			let form = $(this).parents('form');
			let form_data = new FormData();

			form_data.append('action', 'ajax_generate_workshop_confirmed_book');

			form.serializeArray().reduce(function(obj, item) {
				form_data.append(item.name, item.value);
			}, {});

			$.ajax({
				url: window.ajax_url,
				data: form_data,
				type: "POST",
				processData: false,
				contentType: false,
				success: function (r, textStatus, jqXHR) {
					dialog.removeClass('loading');

					$('#workshop-confirmation-book').removeClass('loading');
					$('#workshop-confirmation-book > .inner').html(r.data);
					$('#workshop-confirmation-book > .inner > .spread:first-child').addClass('active');

					$.fancybox.open({
						type: 'inline',
						src: '#workshop-confirmation-book'
					});
				}
			});
		});

		$('body').on('click', '.confirming-book', function(){
			$.fancybox.close();
			navigate_workshop( $('.workshop-verify-book'), 'next');
		});

		$('body').on('click', '.add-workshop-book-to-cart', function(){
			let form = $(this).parents('form');
			let product_id = form.data('product_id');
			let dialog = $(this).parents('.dialog');

			dialog.addClass('loading');

			form.append('<input type="hidden" name="add-to-cart" value="' + product_id + '">');

			setTimeout(function(){
				form.trigger('submit');
			}, 0);
		});

		$('#giftcard_code').on("keyup", function(e){

			var str = $('#giftcard_code').val().replace(/\s/g, '');
			var strArr = str.split("");
			var formatedValue = "";
			var counter = 0;

			for( const char of strArr){

				if( counter > 0 && counter % 4 == 0 ){
					formatedValue = formatedValue + " ";
				}

				formatedValue += "" + char;
				counter++;
			}

			console.log("trigger format")

			$('#giftcard_code').val(formatedValue);
		})

		/*$('body').on('submit', '.workshop-book-form', function(e){
			e.preventDefault();

			let form = $(this);
			let form_data = new FormData();

			form_data.append('action', 'ajax_add_workshop_book_to_cart');

			form.serializeArray().reduce(function(obj, item) {
				form_data.append(item.name, item.value);
			}, {});

			$.ajax({
				url: window.ajax_url,
				data: form_data,
				type: "POST",
				processData: false,
				contentType: false,
				success: function (r, textStatus, jqXHR) {

					if(r.success){
						window.location.reload(r.data);
					} else {

					}
				}
			});
		});*/

		/*
		 * Product flipbooks
		 */
		if( $('.flipbook-inner').length ){

			$('.flipbook-inner').each(function(){

				$(this).turn({
					width: 700,
					height: 420
				});

			});

		}


		/*
		 * Notice slider
		 */
		$('.notice-slider').slick({
			autoplay: true,
			arrows: false,
			dots: false,
			autoplaySpeed: 4000,
			adaptiveHeight: true,
		});

		/*
		 * Cookies for newsletter popup
		 */
		$('.close-tab').click(function(){
			$('.close-tab, .newsletter-pop').toggleClass('closed');

			Cookies.set('show-newsletter-badge', 'false', {expires: 1, });
		});
	});

	window.addEventListener( "pageshow", function ( event ) {
		var historyTraversal = event.persisted ||
			( typeof window.performance != "undefined" &&
				window.performance.navigation.type === 2 );
		if ( historyTraversal ) {
			let data = {
				'action': 'ajax_refresh_minicart',
			}
			$.post(window.ajax_url, data, function(r){
				if(r.success){
					let count = r.data['count'];
					let total = r.data['total'];

					$('#cart-count').text(count);
					$("#cart-total").replaceWith(total);
				}
			});
		}
	});

	$(window).load(function(){
	});



	$(window).scroll(function(){
		/*
		 * Header class
		 */
		header_class();
	});

	/*
	 * Toggle header classes
	 */
	function header_class()
	{
		let scroll = $(window).scrollTop();

		if (scroll > 35 ){
			$('#header, #main').addClass('shrink');
			$('#back-to-top').addClass('active');
		} else {
			$('#header, #main').removeClass('shrink');
			$('#back-to-top').removeClass('active');
		}
	}

	/*
	 * Generic function invoked by infinite scroll for loading more items and inserting into the container.
	 *
	 * @param el The container with the .infinite-scroll class. The data-action and data-child-block attributes are required on the element
	 */
	function trigger_infinite(el, method)
	{
		if( loading_infinite || el.hasClass('infinite-finished') ) {
			return;
		}

		loading_infinite = true;

		var action = el.data('action');
		var child_block = el.data('child-block');
		var injection_parent_attr = el.attr('data-injection-parent');

		if( typeof action == 'undefined' || action == '' ) {
			console.warn('trigger_infinite() stopped because .infinite-scroll is missing data-action');
			return false;
		}

		if( typeof child_block == 'undefined' || child_block == '' ) {
			console.warn('trigger_infinite() stopped because .infinite-scroll is missing data-child-block');
			return false;
		}

		// If no injection-parent is specified then inject directly into the el
		if( typeof injection_parent_attr == 'undefined' || injection_parent_attr == '' ) {
			var injection_parent = el;
		} else {
			var injection_parent = $(injection_parent_attr);
		}

		el.addClass('loading');

		var used_ids = [];

		// Gather the used id's
		if( el.find(child_block).length ) {
			el.find(child_block).each(function(){
				used_ids.push( $(this).data('id') );
			});
		}

		var data = {
			action: action,
			used_ids: used_ids
		};

		/*
		data.tax = el.attr('data-tax');

		if( el.attr('data-terms').trim() != '' ) {
			data.terms = JSON.parse(el.attr('data-terms'));
		}
		*/

		$.post(window.ajax_url, data, function(r){
			loading_infinite = false;
			el.removeClass('loading');

			r = JSON.parse(r);

			if( r.success ) {
				// Empty data means no more posts to load
				if( 'data' in r ) {

					if(method == 'isotope'){
						el.find('.isotope').append(r.data);

						var new_items = el.find('.added');

						isotope.isotope('appended', new_items);

						isotope.imagesLoaded().progress(function(){
							isotope.isotope('layout');
						});

						el.find('.added').removeClass('added');
					}

				} else {
					el.append('<p class="finished wow fadeInUp">' + window.i18n.infinite_scroll_finished + '</p>');
					el.addClass('infinite-finished');
				}

				$('.isotope').isotope('reloadItems');

			}
		});
	}

	/*
	 * Scroll to
	 */
	function scroll_to(id)
	{
		var pos = $(id).offset().top - $('#header').outerHeight();

		$('html, body').animate({
			scrollTop: pos
		}, 250);
	}

	/*
	 * AJAX forms
	 */
	function handle_form(form, _callback)
	{
		form.addClass('loading');
		form.removeClass('errors');

		// Declare base variables
		var form_message = form.find('.form-message');

		// Pre-AJAX form manipulations
		form_message.html('');

		// Build the form data
		var form_data = new FormData();
		form_data.append('action', form.data('action'));

		form.serializeArray().reduce(function(obj, item) {
			form_data.append(item.name, item.value);
		}, {});

		// If the form contains files, add them to the form data
		if(form.find('input[type="file"]').length){
			var file_fields = form.find('input[type="file"]');

			$.each(file_fields, function(index, element){
				if( $(element).get(0).files.length !== 0) {
					var field_name = $(element).attr('name');

					form_data.append(field_name, $(element)[0].files[0]);
				}
			});
		}

		// AJAX request
		$.ajax({
			url: window.ajax_url,
			data: form_data,
			type: "POST",
			processData: false,
			contentType: false,
			success: function (r, textStatus, jqXHR) {

				form.removeClass('loading');

				if(r.success){

					_callback(r);

				} else {
					form.addClass('errors');
					form_message.html(r.data);

					$('html, body').animate({
						scrollTop: form.offset().top - $('#header').height()
					}, 250);
				}
			}
		});
	}

	function navigate_workshop(_this, direction)
	{
		var this_step = _this.parents('.step');
		var this_type = this_step.data('step-type');
		var prev_step = this_step.prev('.step');
		var next_step = this_step.next('.step');
		var targetted_step = direction == 'next' ? next_step : prev_step;
		var is_valid = direction == 'prev' ? true : false;

		if( this_step.data('step-id') == 0 ){
			is_valid = true;
		}

		if( this_type == 'text' ){
			var this_input = this_step.find('input[type="text"]');
			var input_val = this_input.val();

			if( input_val != '' ){
				this_input.removeClass('invalid');
				is_valid = true;
			} else {
				this_input.addClass('invalid');
			}
		}

		if( this_type == 'number' ){
			var this_input = this_step.find('input[type="number"]');
			var input_val = this_input.val();

			if( input_val != '' ){
				this_input.removeClass('invalid');
				is_valid = true;
			} else {
				this_input.addClass('invalid');
			}
		}

		if( this_type == 'radio' ){
			var this_input = this_step.find('input[type="radio"]:checked');

			if( this_input.length != 0 ){
				this_input.removeClass('invalid');
				is_valid = true;
			} else {
				this_input.addClass('invalid');
			}
		}

		// Not a field step so always valid
		if( this_type === undefined ){
			is_valid = true;
		}

		if(is_valid) {
			this_step.removeClass('active');
			targetted_step.addClass('active');
		}
	}

	function navigate_book(_this, direction) {
		var this_book_holder = _this.parents('.workshop-book-holder');
		var this_step = this_book_holder.find('> .inner > .spread.active');
		var step_index = this_step.index();
		var total_steps = this_book_holder.find(' > .inner > .spread').length;

		var prev_step = this_step.prev('.spread');
		var next_step = this_step.next('.spread');

		var targetted_step = direction == 'next' ? next_step : prev_step;
		var is_valid = direction == 'prev' ? true : false;

		// cant go back on first spread
		if (direction == 'prev' && step_index == 0) {
			navigate_workshop(this_book_holder, 'prev');
			return;
		}

		// if is last spread (and not confirmation step), go to next step
		if (this_book_holder.attr('id') != 'workshop-confirmation-book'){
			if (direction == 'next' && ++step_index == total_steps) {
				navigate_workshop(this_book_holder, 'next');
				return;
			}
		}

		// check if has image sets
		var image_sets = this_step.find('.image-sets')
		if( image_sets.length ){

			if( image_sets.hasClass('correct') ){
				is_valid = true;
			}

		} else {
			// always valid if not image sets
			is_valid = true;
		}

		if(is_valid) {
			this_step.find('.messages p').removeClass('active');
			this_step.removeClass('active');
			targetted_step.addClass('active');
		}
	}

	$.fn.shuffleChildren = function() {
		$.each(this.get(), function(index, el) {
			var $el = $(el);
			var $find = $el.children();

			$find.sort(function() {
				return 0.5 - Math.random();
			});

			$el.empty();
			$find.appendTo($el);
		});
	};

})(jQuery);