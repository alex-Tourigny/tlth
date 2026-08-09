(function($){

	$(document).ready(function () {

		/*
		 * Generate printabel PDfs from HTML
		 */
		$('.ajax-admin-generate-book-pdf').click(function(e){
			e.preventDefault();

			let _this = $(this);
			let order_id = $(this).data('order-id');
			let item_id = $(this).data('item-id');

			/*
			 * AJAX
			 */
			_this.after('<span class="spinner is-active"></span>');

			let data = {
				'action': 'ajax_generate_book_html_2_pdf',
				'order_id': order_id,
				'item_id': item_id
			};

			$.post(ajaxurl, data, function(r){
				_this.siblings('.spinner').remove();
			});
		});

		/*
		 * Import gift cards
		 */
		$('#kantaloup-import-gift-cards').submit(function(e){
			e.preventDefault();

			let _form = $(this);

			handle_form(_form, function(r){

				if(r.success){

					_form.find('.form-row').remove();
					_form.append('<p>' + r.data.message + '</p>');

				} else {

					_form.before(r.data);
				}

			});
		});

		/*
		 * Import users
		 */
		$('#kantaloup-import-users').submit(function(e){
			e.preventDefault();

			let _form = $(this);

			handle_form(_form, function(r){

				if(r.success){

					_form.find('.form-row').remove();
					_form.append('<p>' + r.data.message + '</p>');

				} else {

					_form.before(r.data);
				}

			});
		});

		/*
		 * Handlers
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
				url: ajaxurl,
				data: form_data,
				type: "POST",
				processData: false,
				contentType: false,
				success: function (r, textStatus, jqXHR) {

					form.removeClass('loading');

					_callback(r);
				}
			});
		}
	});
})(jQuery);