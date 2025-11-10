<?
/*
 * Require PDF libs
 */
//require_once(THEME_PATH . '/fw/pdf/fpdf/fpdf.php');
//require_once(THEME_PATH . '/fw/pdf/fpdi/autoload.php');
//require_once(THEME_PATH . '/fw/pdf/html2pdf.php');

/*
 * Add HTML2PDF in admin
 */
add_action( 'admin_enqueue_scripts', 'rubik_admin_enqueues' );
function rubik_admin_enqueues($hook)
{
	if ( 'post.php' != $hook ) return;

	wp_enqueue_style( 'html2pdf', THEME_URL . '/assets/css/html2pdf.css' );
}

/*
 * Get product gravity form data
 */
function get_gravity_form_data($product_id, $data_to_return = '')
{
	$meta = get_post_meta($product_id, '_gravity_form_data', true);

	if( empty($data_to_return) ) return $meta;

	if( ! isset($meta[$data_to_return]) ) return false;

	return $meta[$data_to_return];
}

/*
 * Pass workshop as cart item meta for later
 */
add_filter( 'woocommerce_add_cart_item_data', 'is_workshop_cart_item_meta', 10, 2 );
function is_workshop_cart_item_meta( $cart_item_data, $product_id )
{
	$cart_item_data['is_workshop'] = isset( $_POST['is_workshop'] ) && $_POST['is_workshop'] == 'true' ? 'true' : 'false';

	return $cart_item_data;
}

add_action( 'woocommerce_checkout_create_order_line_item', 'add_workshop_cart_item_meta', 10, 4 );
function add_workshop_cart_item_meta( $item, $cart_item_key, $values, $order )
{
	if ( empty( $values['is_workshop'] ) ) {
		return;
	}

	$item->add_meta_data( '_is_workshop', $values['is_workshop'] );
}

/*
 * Update book maker ACF fields description to make variables available
 */
add_filter('acf/load_field/key=field_5fb6dda3fb334', 'update_book_maker_acf_description', 10, 1);
function update_book_maker_acf_description($field)
{
	$instructions = [];

	global $post;
	$product_id = $post->ID;
	$product_gf_id = get_gravity_form_data($product_id, 'id');


	$instructions[] = '<div class="book-maker-instruction">';
		$instructions[] = '<div class="admin-c2a">';
			$instructions[] = '<h2>Assurez-vous de lier le bon Gravity Forms et de mettre à jour le produit pour pouvoir accéder aux variables.</h2>';
		$instructions[] = '</div>';

		if($product_gf_id) {
			$form_fields = GFAPI::get_form($product_gf_id);
			$form_fields = $form_fields['fields'];

			if( empty($form_fields) ){
				$instructions[] = '<em>Vous devez ajouter des champs à votre formulaire</em>';
			} else {
				$instructions[] = '<p><strong>Variables disponibles (copier-coller la variable à remplacer dans le contenu) : </strong></p>';

				$instructions[] = '<ul class="book-maker-fields widefat fixed">';
					$instructions[] = '<li>';
						$instructions[] = '<strong>Champ</strong>';
						$instructions[] = '<span>Variables</span>';
					$instructions[] = '</li>';

					$i = 0;
					foreach($form_fields as $form_field)
					{
						$tr_class = $i % 2 == 0 ? 'alternate' : '';

						$instructions[] = '<li class="' . $tr_class . '">';
							$instructions[] = '<strong>' . $form_field['label'] . '</strong>';
							$instructions[] = '<span>
													[gf id="' . $form_field['id'] . '"],
													[gf id="' . $form_field['id'] . '" if_vowel="" if_not_vowel=""],
													[gf id="' . $form_field['id'] . '" if_val_is_XX="" if_val_is_XXX=""],
													[gf id="' . $form_field['id'] . '" if_val_not_XX=""],
													[gf multiple_ids="' . $form_field['id'] . ', id_x, id_xx" type="OR/AND/MIXED" val="" true="" false=""],
											</span>';
						$instructions[] = '</li>';

						$i++;
					}

				$instructions[] = '</ul>';
			}
		}


	$instructions[] = '</div>';

	$instructions = implode('', $instructions);

	$field['instructions'] = $instructions;

	return $field;
}

/*
 * Generate the PDFs on payment complete
 */
//add_action( 'woocommerce_thankyou', 'generate_book_maker_pdfs_and_link_them_to_order' );
add_action( 'woocommerce_payment_complete', 'generate_book_maker_pdfs_and_link_them_to_order' );
function generate_book_maker_pdfs_and_link_them_to_order( $order_id )
{
	$order = wc_get_order($order_id);

	$items = $order->get_items();
	if( empty($items) ) return;

	foreach($items as $item_id => $item)
	{
		$gf_history = wc_get_order_item_meta($item_id, '_gravity_forms_history', true);

		// Item has not linked GF - skip it
		if( empty($gf_history) ) continue;

		$product_id = $item->get_product_id();
		$form_id = $gf_history['_gravity_form_lead']['form_id'];
		$entry_id = $gf_history['_gravity_form_linked_entry_id'];

		$entry = GFAPI::get_entry($entry_id);
		$form = GFAPI::get_form($form_id);

		$form_fields = $form['fields'];

		$is_workshop = wc_get_order_item_meta($item_id, '_is_workshop', true) == 'true' ? true : false;

		$pdf_contents = get_book_maker_pdf_content($product_id, $entry, $is_workshop);
		wc_add_order_item_meta($item_id, '_book_maker_pdf_content', $pdf_contents);
	}
}

function find_matching_product_variation_id($product_id, $attributes)
{
	return (new \WC_Product_Data_Store_CPT())->find_matching_product_variation(
		new \WC_Product($product_id),
		$attributes
	);
}

/*
 * Add PDF buttons to order items in WC Order page
 */
add_action( 'woocommerce_after_order_itemmeta', 'add_book_maker_pdf_button_to_item_meta_rows', 10, 3 );
function add_book_maker_pdf_button_to_item_meta_rows( $item_id, $item, $product )
{
	$product_id = $item['product_id'];
	$font_size = get_field('pdf-font-size', $product_id) ? get_field('pdf-font-size', $product_id) : 18;
	$is_workshop = wc_get_order_item_meta($item_id, '_is_workshop', true);

	if( is_object($item) )
	{
		$meta_to_display = array();
		$meta_data_items = $item->get_meta_data();

		foreach ( $meta_data_items as $meta ) {

			if( $meta->key == '_book_maker_pdf_content' && ! empty($is_workshop) ) {

				$gform_data = wc_get_order_item_meta($item_id, '_gravity_forms_history', true);
				$gform_id = $gform_data['_gravity_form_lead']['form_id'];
				$entry_id = $gform_data['_gravity_form_linked_entry_id'];

				$edit_url = add_query_arg(
					array(
						'page' => 'gf_entries',
						'view' => 'entry',
						'id' => $gform_id,
						'lid' => $entry_id,
						'operator' => '',
						'edit' => '1'
					),
					admin_url('admin.php')
				);

				echo '<div class="view pdf-view-btn">';
					echo '<a href="' . add_query_arg(['product-id' => $product_id, 'order-id' => $_GET['post'], 'item-id' => $item_id, 'font-size' => $font_size], THEME_URL . '/fw/generate-book-pdf.php') . '" class="button button-primary">Voir le PDF du livre</a>';
					echo '&nbsp;<a href="' . add_query_arg(['product-id' => $product_id, 'order-id' => $_GET['post'], 'item-id' => $item_id, 'regenerate' => 'true', 'font-size' => $font_size], THEME_URL . '/fw/generate-book-pdf.php') . '" class="button button-secondary">Regénérer le PDF</a>';
					echo '&nbsp;<a href="' . $edit_url . '" class="button button-secondary" target="_blank">Modifier</a>';
					//echo '<a href="javascript:;" data-order-id="' . $_GET['post'] . '" data-item-id="' . $item_id . '" class="ajax-admin-generate-book-pdf button button-primary">Voir le PDF du livre</a>';
				echo '</div>';
			}
		}
	}
}


add_shortcode( 'gf', 'book_maker_shortcodes' );
function book_maker_shortcodes( $atts )
{
	global $book_entry_values;

	$field_id = isset($atts['id']) && ! empty($atts['id']) ? $atts['id'] : '';
	$field_multiple_ids =  isset($atts['multiple_ids']) && ! empty($atts['multiple_ids']) ? $atts['multiple_ids'] : '';

	if( $book_entry_values['mode'] == 'workshop' ) {
		$field_value = $field_id ? $book_entry_values['input_' . $field_id] : '';
	} else {
		$field_value = $field_id ? $book_entry_values[$field_id] : '';
	}

	// Single variable conditional
	if( $field_id ) {

		// Clean field return
		if (count($atts) == 1) {
			return $field_value;
		}

		// Vowels
		if (
			( isset($atts['if_vowel']) && ! empty($atts['if_vowel']) )
			&&
			( isset($atts['if_not_vowel']) && ! empty($atts['if_not_vowel']) )
		) {
			return is_vowel($field_value) ? $atts['if_vowel'] : $atts['if_not_vowel'];
		}

		// ID - Wildcards
		//print_r($atts);

		foreach ($atts as $key => $val) {
			if (strpos($key, 'if_val_is_') === false) continue;
			
			$lowercase_field_value = strtolower($field_value);

			return $atts['if_val_is_' . $lowercase_field_value];
		}

		foreach ($atts as $key => $val) {
			if (strpos($key, 'if_val_not_') === false) continue;

			$lowercase_field_value = strtolower($field_value);

			$queried_field_value = str_replace('if_val_not_', '', $key);
			$queried_field_value = strtolower($queried_field_value);

			return $lowercase_field_value != $queried_field_value ? $val : '';
		}
	}

	// Multiple variable conditional
	if( $field_multiple_ids ){
		$field_ids = explode(',', $field_multiple_ids);

		$compare = isset( $atts['type'] ) && ! empty($atts['type']) ? $atts['type'] : 'OR';
		$searched_val = isset( $atts['val'] ) && ! empty($atts['val']) ? strtolower( $atts['val'] ) : '';
		$true_value = isset( $atts['true'] ) && ! empty($atts['true']) ? $atts['true'] : '';
		$false_value = isset( $atts['false'] ) && ! empty($atts['false']) ? $atts['false'] : '';

		$field_ids_and_values = [];

		foreach($field_ids as $field_id)
		{
			$field_id = trim($field_id);

			$field_value = $book_entry_values['mode'] == 'workshop' ? $book_entry_values['input_' . $field_id] : $book_entry_values[$field_id];
			$field_value = strtolower($field_value);

			$field_ids_and_values[$field_id] = $field_value;
		}

		if( $compare == 'OR' ){
			return in_array($searched_val, $field_ids_and_values) ? $true_value : $false_value;
		}

		if( $compare == 'AND' ){
			return count( array_flip($field_ids_and_values) ) === 1 && end($field_ids_and_values) === $searched_val ? $true_value : $false_value;
		}

		if( $compare == 'MIXED' ){
			return ( count( array_unique($field_ids_and_values) ) !== 1) ? $true_value : $false_value;
		}
	}
}

/*
 * Loop through book maker repeater and replace our home-made vars by real GF entry
 */
function get_book_maker_pdf_content($product_id, $entry_values, $is_workshop = true)
{
	global $book_entry_values;

	$book_entry_values = $entry_values;
	$book_entry_values['mode'] = $is_workshop ? 'workshop' : 'product';

	$pdf_pages = get_field('book-maker', $product_id);
	if( empty($pdf_pages) ) return;

	$the_pages = [];
	foreach($pdf_pages as $pdf_page)
	{
		// Blank pages
		if($pdf_page['is-blank-page']){
			$the_pages[] = array(
				'type' => 'blank'
			);

			continue;
		}

		// Image pages
		if($pdf_page['is-image-page']){
			$the_pages[] = array(
				'type' => 'image',
				'image' => $pdf_page['image']['url']
			);

			continue;
		}

		// Cover pages
		if($pdf_page['is-cover-page']){
			$the_pages[] = array(
				'type' => 'cover',
				'image' => $pdf_page['image']['url']
			);

			continue;
		}

		// Message pages
		if($pdf_page['is-message-page']){

			$original_content = $is_workshop ? $pdf_page['page-content'] : get_field('book-message', $product_id);

			$new_content = apply_filters( 'the_content', $original_content);

			$the_pages[] = array(
				'type' => 'message',
				'content' => $new_content
			);

			continue;
		}

		// Content page
		$original_content = $pdf_page['page-content'];
		$new_content = apply_filters( 'the_content', $original_content);

		$the_pages[] = array(
			'type' => 'content',
			'content' => $new_content
		);
	}

	return $the_pages;
}

/*
 * If an entry is edited, retrigger to book generation
 */
add_action( 'gform_after_update_entry', 'update_entry', 10, 2 );
function update_entry( $form, $entry_id )
{
	$entry = GFAPI::get_entry($entry_id);

	$order_id = gform_get_meta($entry_id, 'woocommerce_order_number');
	$order_item_id = gform_get_meta($entry_id, 'woocommerce_order_item_number');

	$order = wc_get_order($order_id);

	$items = $order->get_items();
	if( empty($items) ) return;

	foreach($items as $item_id => $item)
	{
		$gf_history = wc_get_order_item_meta($item_id, '_gravity_forms_history', true);

		// Item has not linked GF - skip it
		if( empty($gf_history) ) continue;

		$product_id = $item->get_product_id();

		$form_fields = $form['fields'];

		$is_workshop = wc_get_order_item_meta($item_id, '_is_workshop', true) == 'true' ? true : false;

		$pdf_contents = get_book_maker_pdf_content($product_id, $entry, $is_workshop);
		wc_update_order_item_meta($item_id, '_book_maker_pdf_content', $pdf_contents);

		// Update the admin order item meta
		$gf_history = wc_get_order_item_meta($item_id, '_gravity_forms_history', true);
		$gf_lead = $gf_history['_gravity_form_lead'];

		$new_lead_values = [];
		foreach($entry as $entry_key => $entry_value)
		{
			if( is_numeric($entry_key) ){
				$gf_lead[$entry_key] = $entry_value;
			}
		}

		$gf_history['_gravity_form_lead'] = $gf_lead;
		$gf_history = array_unique($gf_history);

		wc_update_order_item_meta($item_id, '_gravity_forms_history', $gf_history);

		clean_post_cache( $order_id );
		wc_delete_shop_order_transients( $order_id );
		wp_cache_delete( 'order-items-' . $order_id, 'orders' );
	}
}