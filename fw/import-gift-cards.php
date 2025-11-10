<?
/*
 * Register menu
 */
add_action( 'admin_menu', 'kantaloup_import_gift_cards_admin_menu' );
function kantaloup_import_gift_cards_admin_menu()
{

	add_submenu_page(
		'yith_plugin_panel',
		'Importer un CSV de cartes cadeaux',
		'Importer un CSV',
		'manage_options',
		'kantaloup-import-gift-cards',
		'kantaloup_import_gift_cards_page',
		99
	);
}


function kantaloup_import_gift_cards_page()
{
	include(THEME_PATH . '/includes/admin/gift-cards/admin-page.php');
}

/*
 * Import gift cards
 */
add_action('wp_ajax_ajax_import_gift_cards', 'ajax_import_gift_cards');
function ajax_import_gift_cards()
{
	$form_data = FW::handle_ajax_data($_POST, $_FILES, '', ['application/vnd.ms-excel','text/plain','text/csv','text/tsv']);

	$file_attachment_id = $form_data['file'][0]['attachment_id'];
	$file = fopen( get_attached_file($file_attachment_id), 'r' );

	$cards_to_import = [];
	$i = 0;
	while( ( $data = fgetcsv($file) ) !== FALSE )
	{
		$card_number = $data[0];
		$amount = $data[1];

		// Skip empty celled cards
		if( empty($card_number) || empty($amount) ) continue;

		$cards_to_import[$i] = array(
			'card_number' => $card_number,
			'card_amount' => $amount
		);

		$i++;
	}

	if( empty($cards_to_import) ){
		wp_send_json_error('Aucune carte a importer');
	}

	$imported_gif_cards = [];
	$not_imported_gif_cards = [];

	foreach($cards_to_import as $card_to_import)
	{
		$card_number = $card_to_import['card_number'];
		$card_amount = $card_to_import['card_amount'];

		if( get_page_by_title($card_number, OBJECT, 'gift_card') ){
			$not_imported_gif_cards[] = $card_to_import;
			continue;
		}

		$gift_card_id = wp_insert_post(
			array(
				'post_title' => $card_number,
				'post_type' => 'gift_card',
				'post_status' => 'publish',
				'meta_input' => array(
					'_ywgc_amount_total' => $card_amount,
					'_ywgc_balance_total' => $card_amount
				)
			)
		);

		$imported_gif_cards[] = $card_to_import;
	}

	wp_delete_attachment($file_attachment_id, true);

	wp_send_json_success(
		array(
			'message' => sprintf(
				' %d cartes importées avec succèes. %d cartes n\'ont pas été importées car elles existaient déjà.',
				count($imported_gif_cards),
				count($not_imported_gif_cards)
			),
			'cards' => $imported_gif_cards
		)
	);
}