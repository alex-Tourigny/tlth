<?
/*
 * Register menu
 */
add_action( 'admin_menu', 'kantaloup_import_users_admin_menu' );
function kantaloup_import_users_admin_menu()
{

	add_submenu_page(
		'users.php',
		'Importer un CSV',
		'Importer un CSV',
		'manage_options',
		'kantaloup-import-users',
		'kantaloup_import_users_page',
		99
	);
}

function kantaloup_import_users_page()
{
	include(THEME_PATH . '/includes/admin/users/admin-page.php');
}

/*
 * Import users
 */
add_action('wp_ajax_ajax_import_users', 'ajax_import_users');
function ajax_import_users()
{
	$form_data = FW::handle_ajax_data($_POST, $_FILES, '', ['application/vnd.ms-excel','text/plain','text/csv','text/tsv']);

	$file_attachment_id = $form_data['file'][0]['attachment_id'];
	$file = fopen( get_attached_file($file_attachment_id), 'r' );

	$users_to_import = [];
	$i = 0;
	while( ( $data = fgetcsv($file) ) !== FALSE )
	{
		$user_role = 'subscriber';

		switch ($data[13]){
			case 'Ecole':
				$user_role = 'school';
				break;
			case 'Milieu de garde':
				$user_role = 'daycare';
				break;
			case 'Particulier':
				$user_role = 'subscriber';
				break;
		}

		$fields = array(
			'last_name' => $data[0],
			'first_name' => $data[1],
			'user_email' => $data[8],
			'user_login' => $data[8],
			'user_pass' => FW::random_string(25),
			'role' => $user_role
		);

		$meta = array(
			'billing_address_1' => $data[3],
			'shipping_address_1' => $data[3],

			'billing_city' => $data[4],
			'shipping_city' => $data[4],

			'billing_postcode' => $data[7],
			'shipping_postcode' => $data[7],

			'billing_last_name' => $data[0],
			'shipping_last_name' => $data[0],

			'billing_first_name' => $data[1],
			'shipping_first_name' => $data[1],

			'billing_state' => $data[5],
			'shipping_state' => $data[5],

			'billing_country' => $data[6],
			'shipping_country' => $data[6],
		);

		$acf = array(
			'phone' => $data[9],
		);

		$users_to_import[] = array(
			'fields' => $fields,
			'meta' => $meta,
			'acf' => $acf,
		);

		$i++;
	}

	if( empty($users_to_import) ){
		wp_send_json_error('Aucun utilisateur à importer');
	}

	$imported_users = [];
	$not_imported_users = [];
	$extra_log = [];

	foreach($users_to_import as $user_to_import)
	{
		if( username_exists($user_to_import['fields']['user_email']) ){
			$not_imported_users[] = $user_to_import;
			continue;
		}

		$user_id = wp_insert_user($user_to_import['fields']);

		if ( is_wp_error( $user_id ) ) {
			$extra_log[] = $user_to_import['fields']['first_name'] . ': ' . $user_id->get_error_message();
			continue;
		}

		/*
		 * Meta
		 */
		foreach($user_to_import['meta'] as $meta_key => $meta_value)
		{
			update_user_meta($user_id, $meta_key, $meta_value);
		}

		/*
		 * ACF
		 */
		foreach($user_to_import['acf'] as $meta_key => $meta_value)
		{
			update_field($meta_key, $meta_value, 'user_' . $user_id);
		}

		$imported_users[] = $user_to_import;
	}

	wp_delete_attachment($file_attachment_id, true);

	wp_send_json_success(
		array(
			'message' => sprintf(
				' %d utilisateurs importés avec succèes. %d utilisateurs n\'ont pas été importées car ils existaient déjà. Extra : %s',
				count($imported_users),
				count($not_imported_users),
				implode('<br>', $extra_log)
			),
			'users' => $imported_users,
		)
	);
}