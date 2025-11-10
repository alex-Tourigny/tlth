<?php
require_once('../../../../wp-load.php');

if( ! is_user_logged_in() ) die();
if( ! current_user_can('administrator') ) die();

use Dompdf\Dompdf;

$order_id = $_GET['order-id'];
$item_id = $_GET['item-id'];
$font_size = $_GET['font-size'];
$product_id = $_GET['product-id'];

$regenerate_pdf = isset( $_GET['regenerate'] ) && $_GET['regenerate'] == 'true' ? true : false;

if( ! $regenerate_pdf ){
	$pdf_pages = wc_get_order_item_meta($item_id, '_book_maker_pdf_content');
} else {
	$gf_history = wc_get_order_item_meta($item_id, '_gravity_forms_history', true);
	$form_id = $gf_history['_gravity_form_lead']['form_id'];
	$entry_id = $gf_history['_gravity_form_linked_entry_id'];

	$entry = GFAPI::get_entry($entry_id);
	$form = GFAPI::get_form($form_id);

	$form_fields = $form['fields'];

	$is_workshop = wc_get_order_item_meta($item_id, '_is_workshop', true);

	$pdf_pages = get_book_maker_pdf_content($product_id, $entry, $is_workshop);
}

ob_start();
include( THEME_PATH . '/fw/generate-book-pdf-html.php' );
$html = ob_get_clean();

/*print_r($html);
echo $html;
return;*/

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream('livre-' . $order_id . '-' . $item_id . '.pdf');
?>