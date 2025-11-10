<?php
/*
 * Generate book content in Workshop
 */
add_action('wp_ajax_ajax_generate_workshop_book_content', 'ajax_generate_workshop_book_content');
add_action('wp_ajax_nopriv_ajax_generate_workshop_book_content', 'ajax_generate_workshop_book_content');
function ajax_generate_workshop_book_content()
{
	$form_data = $_POST;
	$product_id = $form_data['product_id'];

	$init_book_pages = get_book_maker_pdf_content($product_id, $form_data);

	if( empty($init_book_pages) ){
		wp_send_json_error();
	}

	ob_start();

	include( THEME_PATH . '/includes/workshop/spreads.php' );

	$markup = ob_get_clean();

	wp_send_json_success($markup);
}

/*
 * Generate confirmation book
 */
add_action('wp_ajax_ajax_generate_workshop_confirmed_book', 'ajax_generate_workshop_confirmed_book');
add_action('wp_ajax_nopriv_ajax_generate_workshop_confirmed_book', 'ajax_generate_workshop_confirmed_book');
function ajax_generate_workshop_confirmed_book()
{
	$form_data = $_POST;
	$product_id = $form_data['product_id'];

	$init_book_pages = get_book_maker_pdf_content($product_id, $form_data);

	if( empty($init_book_pages) ){
		wp_send_json_error();
	}

	ob_start();

	$is_book_confirmation = true;
	include( THEME_PATH . '/includes/workshop/spreads.php' );

	$markup = ob_get_clean();

	wp_send_json_success($markup);
}

/*
 * Generate confirmation book
 */
add_action('wp_ajax_ajax_refresh_minicart', 'ajax_refresh_minicart');
add_action('wp_ajax_nopriv_ajax_refresh_minicart', 'ajax_refresh_minicart');
function ajax_refresh_minicart()
{
	$form_data = $_POST;
	$count = WC()->cart->get_cart_contents_count();
	$total = '<span id="cart-total" class="total">'. wc_price( WC()->cart->cart_contents_total ).'</span>';

	$values = [
		"count" => $count,
		"total" => $total
	];

	wp_send_json_success($values);
}