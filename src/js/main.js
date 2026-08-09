(function($) {

	$(document).ready(function(){
		/*
		 * Header class
		 */
		header_class();

		/*
		 * Sticky sidebar offset (native position:sticky; replaces sticky-sidebar JS)
		 */
		(function tlthInitStickySidebarTop(){
			var timer;
			function set(){
				var el = document.getElementById('header');
				var top = el ? el.offsetHeight + 40 : 120;
				document.documentElement.style.setProperty('--tlth-sticky-sidebar-top', top + 'px');
			}
			function onResize(){
				clearTimeout(timer);
				timer = setTimeout(set, 100);
			}
			set();
			$(window).on('resize orientationchange', onResize);
		})();

		/*
		 * Mobile menu
		 */
		var tlthMobileNavResizeObserver = null;

		function tlthSetMobileNavTop(){
			var header = document.getElementById('header');
			var headerContent = header && header.querySelector('.header-content');
			if( !headerContent ){
				return;
			}

			// offsetHeight ignores position:fixed children; bottom can inflate while the menu opens.
			var headerTop = header.getBoundingClientRect().top;
			var top = Math.round(headerTop + headerContent.offsetHeight);
			var viewportH = window.visualViewport ? window.visualViewport.height : window.innerHeight;
			var maxTop = Math.max(48, viewportH - 48);

			top = Math.min(Math.max(top, 48), maxTop);
			document.documentElement.style.setProperty('--tlth-mobile-nav-top', top + 'px');
		}

		function tlthToggleMobileNavTopObserver(enable){
			var header = document.getElementById('header');
			if( !header ){
				return;
			}

			if( enable ){
				if( !tlthMobileNavResizeObserver ){
					tlthMobileNavResizeObserver = new ResizeObserver(function(){
						if( $('#header').hasClass('active') ){
							tlthSetMobileNavTop();
						}
					});
				} else {
					tlthMobileNavResizeObserver.disconnect();
				}

				tlthMobileNavResizeObserver.observe(header);

				var noticeBar = document.getElementById('shop-notice-bar');
				if( noticeBar ){
					tlthMobileNavResizeObserver.observe(noticeBar);
				}

				var headerContent = header.querySelector('.header-content');
				if( headerContent ){
					tlthMobileNavResizeObserver.observe(headerContent);
				}

				tlthSetMobileNavTop();
				requestAnimationFrame(function(){
					tlthSetMobileNavTop();
					requestAnimationFrame(tlthSetMobileNavTop);
				});
			} else if( tlthMobileNavResizeObserver ){
				tlthMobileNavResizeObserver.disconnect();
			}
		}

		function tlthRefreshNoticeSlider(){
			var $slider = $('.notice-slider.slick-initialized');
			if( !$slider.length ){
				return;
			}
			$slider.slick('setPosition');
		}

		function tlthOpenMobileMenu(){
			// Measure before opening so #primary-nav is still display:none and cannot inflate the header.
			tlthSetMobileNavTop();
			$('body, #primary-nav, #header, #main').addClass('active');
			$('#site-burger').addClass('active');
			tlthToggleMobileNavTopObserver(true);
		}

		$(window).on('resize orientationchange scroll', function(){
			if( $('#header').hasClass('active') ){
				tlthSetMobileNavTop();
			}
		});

		function tlthCloseMobileMenu(){
			tlthToggleMobileNavTopObserver(false);
			$('body, #primary-nav, #header, #main').removeClass('active');
			$('#site-burger').removeClass('active');
			requestAnimationFrame(function(){
				requestAnimationFrame(tlthRefreshNoticeSlider);
			});
		}

		$('.mobile-nav-trigger').click(function(){
			if( $('#header').hasClass('active') ){
				tlthCloseMobileMenu();
			} else {
				tlthOpenMobileMenu();
			}
		});

		$('body').on('click', '#main.active', tlthCloseMobileMenu);



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
		 * Shop archive ajax search
		 */
		function tlth_debounce(fn, wait){
			let timer = null;
			return function(){
				const context = this;
				const args = arguments;
				clearTimeout(timer);
				timer = setTimeout(function(){
					fn.apply(context, args);
				}, wait);
			};
		}

		function tlth_sync_shop_filter_details($root){
			const mq = window.matchMedia('(min-width: 992px)');
			const $scope = $root && $root.length ? $root : $(document);
			$scope.find('.product-search-filter-terms details').each(function(){
				this.open = mq.matches;
			});
		}

		function init_shop_filter_details(){
			const $archive = $('.shop-archive');
			if( !$archive.length ){
				return;
			}

			const mq = window.matchMedia('(min-width: 992px)');
			tlth_sync_shop_filter_details($archive);

			if( typeof mq.addEventListener === 'function' ){
				mq.addEventListener('change', function(){
					tlth_sync_shop_filter_details($archive);
				});
			}else if( typeof mq.addListener === 'function' ){
				mq.addListener(function(){
					tlth_sync_shop_filter_details($archive);
				});
			}
		}

		function init_shop_archive_ajax(){
			const $archive = $('.shop-archive');
			if( !$archive.length ){
				return;
			}

			const $form = $archive.find('form[role="search"]');
			const $sidebar = $archive.find('.the-sidebar');
			const $results = $archive.find('[data-shop-results]');
			const $toolbar = $archive.find('[data-shop-toolbar]');
			let request = null;

			const buildUrl = function(overrideUrl){
				const baseUrl = overrideUrl || window.location.href;
				const url = new URL(baseUrl, window.location.origin);
				const searchValue = $.trim($form.find('input[type="search"]').val());

				// WooCommerce shop is usually a PAGE; forcing post_type=product here breaks the main
				// query when combined with product_cat/product_tag filters (500 on some stacks).
				if( searchValue ){
					url.searchParams.set('post_type', 'product');
					url.searchParams.set('s', searchValue);
				}else{
					url.searchParams.delete('post_type');
					url.searchParams.delete('s');
				}

				url.searchParams.delete('paged');

				/*
				 * Remove every prior taxonomy filter key before re-applying sidebar fields.
				 * PHP / redirects may use product_cat[0]=, product_cat[]=, or product_cat= — delete() only matches exact keys,
				 * so stale keys (e.g. product_cat[0]) would survive and keep the category selected after AJAX.
				 */
				(function(){
					const keysToDelete = new Set();
					url.searchParams.forEach(function(_value, key){
						if(
							key === 'product_cat' ||
							key.indexOf('product_cat[') === 0 ||
							key === 'product_tag' ||
							key.indexOf('product_tag[') === 0
						){
							keysToDelete.add(key);
						}
					});
					keysToDelete.forEach(function(key){
						url.searchParams.delete(key);
					});
				})();

				const fields = $sidebar.find('input[name], select[name]').not('[type="search"]');
				const fieldNames = {};

				fields.each(function(){
					const name = $(this).attr('name');
					if( name ){
						fieldNames[name] = true;
					}
				});

				$.each(fieldNames, function(name){
					url.searchParams.delete(name);
				});

				fields.each(function(){
					const $field = $(this);
					const name = $field.attr('name');
					if( !name || $field.is(':disabled') ){
						return;
					}

					if( $field.is('select') ){
						const value = $field.val();
						if( value ){
							url.searchParams.set(name, value);
						}
						return;
					}

					const type = $field.attr('type');
					if( type === 'checkbox' ){
						if( $field.is(':checked') ){
							url.searchParams.append(name, $field.val() || '1');
						}
						return;
					}

					if( type === 'radio' ){
						if( $field.is(':checked') ){
							url.searchParams.set(name, $field.val());
						}
						return;
					}

					if( $field.val() ){
						url.searchParams.set(name, $field.val());
					}
				});

				return url.toString();
			};

			const updateResults = function(url){
				if( !$results.length ){
					return;
				}

				if( request ){
					request.abort();
				}

				$archive.addClass('is-loading');
				$results.attr('aria-busy', 'true');

				request = $.get(url, function(response){
					const $response = $('<div>').append($.parseHTML(response));
					const $newResults = $response.find('[data-shop-results]');
					const $newToolbar = $response.find('[data-shop-toolbar]');
					const $newSidebar = $response.find('.shop-archive .the-sidebar');

					if( $newResults.length ){
						$results.html($newResults.html());
					}

					if( $toolbar.length && $newToolbar.length ){
						$toolbar.html($newToolbar.html());
					}

					if( $sidebar.length && $newSidebar.length ){
						$sidebar.html($newSidebar.html());
						tlth_sync_shop_filter_details($archive);
					}

					if( typeof $.fn.chosen === 'function' ){
						const $orderby = $archive.find('.woocommerce-ordering > .orderby');
						if( $orderby.length ){
							$orderby.chosen();
						}
					}

					window.history.replaceState({}, '', url);
				}).always(function(){
					$archive.removeClass('is-loading');
					$results.attr('aria-busy', 'false');
					request = null;
				});
			};

			// Debounce only live search typing — filter checkboxes and ordering should fire immediately.
			const debouncedSearchUpdate = tlth_debounce(function(){
				updateResults(buildUrl());
			}, 300);

			$form.on('submit', function(e){
				e.preventDefault();
				updateResults(buildUrl());
			});

			$form.on('input', 'input[type="search"]', function(){
				debouncedSearchUpdate();
			});

			$sidebar.on('change', 'input, select', function(){
				updateResults(buildUrl());
			});

			$sidebar.on('submit', 'form', function(e){
				e.preventDefault();
				updateResults(buildUrl());
			});

			$sidebar.on('click', 'a', function(e){
				const href = $(this).attr('href');
				if( !href || href === '#' || href.indexOf('javascript:') === 0 ){
					return;
				}

				e.preventDefault();
				updateResults(buildUrl(href));
			});

			$archive.on('change', '.woocommerce-ordering .orderby', function(){
				updateResults(buildUrl());
			});

			$archive.on('submit', '.woocommerce-ordering', function(e){
				e.preventDefault();
				updateResults(buildUrl());
			});

			$archive.on('click', '[data-shop-results] .page-numbers a, [data-shop-toolbar] .page-numbers a', function(e){
				const href = $(this).attr('href');
				if( !href ){
					return;
				}

				e.preventDefault();
				updateResults(buildUrl(href));
			});
		}

		init_shop_filter_details();
		init_shop_archive_ajax();

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
		var tlthFlipbook = {
			width: 880,
			height: 528,
			padding: 48,
		};

		function initFlipbook( $inner ) {
			if ( ! $inner.length ) {
				return;
			}

			if ( $inner.data( 'pages' ) ) {
				$inner.turn( 'size', tlthFlipbook.width, tlthFlipbook.height );
				$inner.turn( 'resize' );
				return;
			}

			$inner.turn( {
				width: tlthFlipbook.width,
				height: tlthFlipbook.height,
			} );
		}

		if ( $( '.flipbook-inner' ).length ) {
			$( '.flipbook-inner' ).each( function () {
				initFlipbook( $( this ) );
			} );
		}

		$( document ).on( 'afterShow.fb', function ( e, instance, slide ) {
			var $inner = slide.$content.find( '.flipbook-inner' );

			if ( ! $inner.length ) {
				$inner = slide.$content.filter( '.flipbook-inner' );
			}

			if ( $inner.length ) {
				var pad = tlthFlipbook.padding;

				instance.$refs.container.addClass( 'fancybox--flipbook' );
				initFlipbook( $inner );

				slide.$content.css( {
					width: tlthFlipbook.width + pad * 2,
					height: tlthFlipbook.height + pad * 2,
					padding: pad,
					overflow: 'visible',
					boxSizing: 'border-box',
				} );
			}
		} );

		$( document ).on( 'afterClose.fb', function ( e, instance ) {
			instance.$refs.container.removeClass( 'fancybox--flipbook' );
		} );


	/*
	 * Notice slider - only initialize if multiple messages
	 */
	if( $('.notice-slider').length && $('.notice-slider').data('notice-count') > 1 ){
		$('.notice-slider').slick({
			autoplay: true,
			arrows: false,
			dots: false,
			autoplaySpeed: 4000,
			adaptiveHeight: true,
			speed: 500,
			fade: true,
			cssEase: 'ease-in-out'
		});
	}

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
	var tlthNoticeBarHidden = false;

	function header_class()
	{
		var scroll = $(window).scrollTop();

		// Hysteresis avoids shrink toggling when layout shifts nudge scrollTop near the threshold.
		if (scroll > 35) {
			$('#header, #main').addClass('shrink');
			$('#back-to-top').addClass('active');
		} else if (scroll < 25) {
			$('#header, #main').removeClass('shrink');
			$('#back-to-top').removeClass('active');
		}

		var $noticeBar = $('#shop-notice-bar');
		if ($noticeBar.length) {
			if (scroll <= 0) {
				if (tlthNoticeBarHidden) {
					$noticeBar.removeClass('is-hidden');
					tlthNoticeBarHidden = false;
				}
			} else if (!tlthNoticeBarHidden && !window.tlthAnchorScrolling) {
				// Collapsing the notice shrinks the document; restore scrollTop so we do not
				// bounce back below the shrink threshold (infinite header twitch).
				var scrollBefore = scroll;
				$noticeBar.addClass('is-hidden');
				tlthNoticeBarHidden = true;
				requestAnimationFrame(function() {
					window.scrollTo(0, scrollBefore);
					requestAnimationFrame(function() {
						window.scrollTo(0, scrollBefore);
					});
				});
			}

			var headerEl = document.getElementById('header');
			if (headerEl) {
				document.documentElement.style.setProperty('--tlth-sticky-sidebar-top', (headerEl.offsetHeight + 40) + 'px');
			}
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
		if (typeof window.tlthScrollToAnchor === 'function') {
			window.tlthScrollToAnchor(id.charAt(0) === '#' ? id : '#' + id, true);
			return;
		}

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