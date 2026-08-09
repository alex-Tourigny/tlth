<?php
/**
 * Book personalization wizard — variations as steps 1–2, then Gravity Form pages.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Attribute slug => step heading (front-end).
 *
 * @return array<string, string>
 */
function tlth_book_wizard_step_titles() {
	return array(
		'pa_choix-de-couverture' => function_exists( 'pll__' )
			? pll__( 'Choisis ta couverture' )
			: 'Choisis ta couverture',
		'pa_type-de-lecteur'     => function_exists( 'pll__' )
			? pll__( 'Choisis ton type de lecteur' )
			: 'Choisis ton type de lecteur',
	);
}

/**
 * Intro copy above the wizard card.
 */
function tlth_book_wizard_intro() {
	$title = function_exists( 'pll__' )
		? pll__( 'Je personnalise mon aventure' )
		: 'Je personnalise mon aventure';
	$lead  = function_exists( 'pll__' )
		? pll__(
			'Prêt à vivre toute une histoire? Remplis ce formulaire pour créer un cadeau unique à ton image et reçois le tout par la poste.'
		)
		: 'Prêt à vivre toute une histoire? Remplis ce formulaire pour créer un cadeau unique à ton image et reçois le tout par la poste.';
	?>
	<header class="book-wizard-intro text-center max-w-xl mx-auto mb-8 lg:mb-10 relative z-10">
		<h2 class="text-2xl md:text-3xl font-medium text-deep-blue mb-3"><?= esc_html( $title ); ?></h2>
		<p class="text-[15px] leading-snug text-deep-blue/90 font-light"><?= esc_html( $lead ); ?></p>
	</header>
	<?php
}

/**
 * Product image URL for variation option cards.
 */
function tlth_book_wizard_option_image_url( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return '';
	}

	$image_id = $product->get_image_id();
	if ( $image_id ) {
		$src = wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' );
		if ( $src ) {
			return $src;
		}
	}

	return wc_placeholder_img_src( 'woocommerce_thumbnail' );
}

/**
 * Total GF pages for a multi-page form.
 *
 * @param int $form_id Gravity Form ID.
 * @return int
 */
function tlth_book_wizard_gf_page_count( $form_id ) {
	$form_id = absint( $form_id );
	if ( ! $form_id || ! class_exists( 'GFAPI' ) ) {
		return 1;
	}

	$form = GFAPI::get_form( $form_id );
	if ( empty( $form['fields'] ) ) {
		return 1;
	}

	if ( class_exists( 'GFFormDisplay' ) && method_exists( 'GFFormDisplay', 'get_max_page_number' ) ) {
		return max( 1, (int) GFFormDisplay::get_max_page_number( $form ) );
	}

	$page_breaks = 0;
	foreach ( $form['fields'] as $field ) {
		$type = is_object( $field ) ? $field->type : ( $field['type'] ?? '' );
		if ( 'page' === $type ) {
			$page_breaks++;
		}
	}

	return max( 1, $page_breaks + 1 );
}

/**
 * Enqueue wizard script on variable products with a linked Gravity Form.
 */
function tlth_enqueue_book_personalization_wizard() {
	if ( ! is_product() ) {
		return;
	}

	global $product;
	if ( ! $product instanceof WC_Product || ! $product->is_type( 'variable' ) ) {
		return;
	}

	$gf_data = get_post_meta( $product->get_id(), '_gravity_form_data', true );
	if ( empty( $gf_data['id'] ) ) {
		return;
	}

	$titles       = tlth_book_wizard_step_titles();
	$ordered_keys = array_keys( $titles );

	$script_deps = array( 'jquery' );
	foreach ( array( 'wc-gravityforms-product-addons', 'gravityforms', 'gform_conditional_logic', 'gform_gravityforms' ) as $handle ) {
		if ( wp_script_is( $handle, 'registered' ) ) {
			$script_deps[] = $handle;
			wp_enqueue_script( $handle );
		}
	}

	$script_rel = 'src/js/book-personalization-wizard.js';
	if ( file_exists( THEME_PATH . '/dist/js/book-personalization-wizard.js' ) ) {
		$script_rel = 'dist/js/book-personalization-wizard.js';
	}

	wp_enqueue_script(
		'tlth-book-wizard',
		THEME_URL . '/' . $script_rel,
		$script_deps,
		filemtime( THEME_PATH . '/' . $script_rel ),
		true
	);

	wp_localize_script(
		'tlth-book-wizard',
		'tlthBookWizard',
		array(
			'formId'        => (int) $gf_data['id'],
			'productId'     => (int) $product->get_id(),
			'gfPageCount'   => tlth_book_wizard_gf_page_count( (int) $gf_data['id'] ),
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'pageUrl'       => get_permalink( $product->get_id() ),
			'cartUrl'       => wc_get_cart_url(),
			'nonce'         => wp_create_nonce( 'tlth_book_wizard' ),
			'stepTitles'    => $titles,
			'attributeOrder'=> $ordered_keys,
			'optionImage'   => tlth_book_wizard_option_image_url( $product ),
			'debug'         => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || isset( $_GET['tlth_wizard_debug'] ),
			'i18n'          => array(
				'next'     => function_exists( 'pll__' ) ? pll__( 'Suivant' ) : 'Suivant',
				'prev'     => function_exists( 'pll__' ) ? pll__( 'Précédent' ) : 'Précédent',
				'stepOf'   => function_exists( 'pll__' ) ? pll__( 'Étape %1$d de %2$d' ) : 'Étape %1$d de %2$d',
				'selectOne'=> function_exists( 'pll__' ) ? pll__( 'Choisis une option pour continuer.' ) : 'Choisis une option pour continuer.',
				'selectVariation' => function_exists( 'pll__' )
					? pll__( 'Choisis toutes les options du produit pour continuer.' )
					: 'Choisis toutes les options du produit pour continuer.',
				'priceTpl' => function_exists( 'pll__' )
					? pll__( 'Le livre revient à %s l\'exemplaire.' )
					: 'Le livre revient à %s l\'exemplaire.',
				'addToCart'  => function_exists( 'pll__' )
					? pll__( 'Ajouter au panier' )
					: 'Ajouter au panier',
				'addedTitle' => function_exists( 'pll__' )
					? pll__( 'Produit ajouté au panier' )
					: 'Produit ajouté au panier',
				'addedTitlePlural' => function_exists( 'pll__' )
					? pll__( 'Produits ajoutés au panier' )
					: 'Produits ajoutés au panier',
				'viewCart'   => function_exists( 'pll__' )
					? pll__( 'Voir le panier' )
					: 'Voir le panier',
				'addToCartFailed' => function_exists( 'pll__' )
					? pll__( 'Le produit n\'a pas pu être ajouté au panier. Vérifie les champs et réessaie.' )
					: 'Le produit n\'a pas pu être ajouté au panier. Vérifie les champs et réessaie.',
				'outOfStock' => function_exists( 'pll__' )
					? pll__( 'Rupture de stock' )
					: 'Rupture de stock',
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'tlth_enqueue_book_personalization_wizard', 100 );

/**
 * Output wizard chrome before variations (progress filled by JS).
 */
function tlth_book_wizard_before_variations_form() {
	global $product;
	if ( ! $product instanceof WC_Product || ! $product->is_type( 'variable' ) ) {
		return;
	}

	$gf_data = get_post_meta( $product->get_id(), '_gravity_form_data', true );
	if ( empty( $gf_data['id'] ) ) {
		return;
	}
	?>
	<div class="book-wizard-chrome" data-book-wizard-chrome hidden>
		<div class="book-wizard-progress" data-book-wizard-progress role="navigation" aria-label="<?php esc_attr_e( 'Progression', 'tlth' ); ?>"></div>
	</div>
	<?php
}
add_action( 'woocommerce_before_variations_form', 'tlth_book_wizard_before_variations_form', 5 );

/**
 * Enable Gravity Forms AJAX paging on product personalization forms.
 *
 * @param array<string, mixed>   $args     Form display args.
 * @param string|int|null        $form_id  Form ID (optional; GF sometimes passes only $args).
 * @return array<string, mixed>
 */
function tlth_book_wizard_gform_args( $args, $form_id = null ) {
	if ( null === $form_id && ! empty( $args['form_id'] ) ) {
		$form_id = $args['form_id'];
	}

	if ( empty( $form_id ) ) {
		return $args;
	}

	if ( ! is_product() ) {
		return $args;
	}

	global $product;
	if ( ! $product instanceof WC_Product ) {
		return $args;
	}

	$gf_data = get_post_meta( $product->get_id(), '_gravity_form_data', true );
	if ( empty( $gf_data['id'] ) || (int) $gf_data['id'] !== (int) $form_id ) {
		return $args;
	}

	$args['ajax'] = true;

	if ( class_exists( 'GFFormDisplay' ) ) {
		$args['submission_method'] = defined( 'GFFormDisplay::SUBMISSION_METHOD_AJAX' )
			? GFFormDisplay::SUBMISSION_METHOD_AJAX
			: 'ajax';
	}

	return $args;
}
add_filter( 'gform_form_args', 'tlth_book_wizard_gform_args', 20, 1 );

/**
 * Ensure linked product forms have AJAX enabled in the form object.
 *
 * @param array $form Form object.
 * @return array
 */
function tlth_book_wizard_gform_pre_render( $form ) {
	if ( ! is_product() ) {
		return $form;
	}

	global $product;
	if ( ! $product instanceof WC_Product ) {
		return $form;
	}

	$gf_data = get_post_meta( $product->get_id(), '_gravity_form_data', true );
	if ( empty( $gf_data['id'] ) || (int) $gf_data['id'] !== (int) $form['id'] ) {
		return $form;
	}

	$form['ajax'] = true;

	if ( class_exists( 'GFFormDisplay' ) && defined( 'GFFormDisplay::SUBMISSION_METHOD_AJAX' ) ) {
		$form['submission_method'] = GFFormDisplay::SUBMISSION_METHOD_AJAX;
	}

	return $form;
}
add_filter( 'gform_pre_render', 'tlth_book_wizard_gform_pre_render', 20 );
add_filter( 'gform_pre_validation', 'tlth_book_wizard_gform_pre_render', 20 );

/**
 * Submit button label on book personalization product forms.
 *
 * @param string $button Button HTML.
 * @param array  $form   Form object.
 * @return string
 */
function tlth_book_wizard_gform_submit_button( $button, $form ) {
	if ( ! is_product() ) {
		return $button;
	}

	global $product;
	if ( ! $product instanceof WC_Product ) {
		return $button;
	}

	$gf_data = get_post_meta( $product->get_id(), '_gravity_form_data', true );
	if ( empty( $gf_data['id'] ) || (int) $gf_data['id'] !== (int) $form['id'] ) {
		return $button;
	}

	$label = function_exists( 'pll__' ) ? pll__( 'Ajouter au panier' ) : 'Ajouter au panier';

	return sprintf(
		'<button type="submit" class="gform_button button btn book-wizard__add-to-cart-submit" id="gform_submit_button_%1$d">%2$s</button>',
		(int) $form['id'],
		esc_html( $label )
	);
}
add_filter( 'gform_submit_button', 'tlth_book_wizard_gform_submit_button', 25, 2 );

/**
 * Log GF paging POST values when debugging.
 *
 * @param array $form Form object.
 */
function tlth_book_wizard_log_gf_submission( $form ) {
	$debug = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || isset( $_GET['tlth_wizard_debug'] );
	if ( ! $debug ) {
		return;
	}

	$form_id = (int) $form['id'];
	$source  = rgpost( 'gform_source_page_number_' . $form_id );
	$target  = rgpost( 'gform_target_page_number_' . $form_id );

	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	error_log(
		sprintf(
			'[tlthBookWizard] GF submission form %d source=%s target=%s ajax=%s',
			$form_id,
			$source,
			$target,
			rgpost( 'gform_ajax' ) ? 'yes' : 'no'
		)
	);
}
add_action( 'gform_pre_submission', 'tlth_book_wizard_log_gf_submission', 5 );

/**
 * Whether the cart already contains a line for this product/variation.
 *
 * @param int $product_id   Parent product ID.
 * @param int $variation_id Variation ID (0 for simple).
 */
function tlth_book_wizard_cart_has_line( $product_id, $variation_id = 0 ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return false;
	}

	$product_id   = absint( $product_id );
	$variation_id = absint( $variation_id );

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( (int) $cart_item['product_id'] !== $product_id ) {
			continue;
		}
		if ( $variation_id && (int) $cart_item['variation_id'] !== $variation_id ) {
			continue;
		}
		return true;
	}

	return false;
}

/**
 * Cart snapshot for wizard add-to-cart verification.
 */
function tlth_ajax_book_wizard_cart_snapshot() {
	check_ajax_referer( 'tlth_book_wizard', 'nonce' );

	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( array( 'message' => 'WooCommerce unavailable.' ) );
	}

	if ( null === WC()->cart ) {
		wc_load_cart();
	}

	$product_id   = absint( $_POST['product_id'] ?? 0 );
	$variation_id = absint( $_POST['variation_id'] ?? 0 );

	wp_send_json_success(
		array(
			'count'       => WC()->cart->get_cart_contents_count(),
			'has_product' => tlth_book_wizard_cart_has_line( $product_id, $variation_id ),
		)
	);
}
add_action( 'wp_ajax_tlth_book_wizard_cart_snapshot', 'tlth_ajax_book_wizard_cart_snapshot' );
add_action( 'wp_ajax_nopriv_tlth_book_wizard_cart_snapshot', 'tlth_ajax_book_wizard_cart_snapshot' );

/**
 * Whether WooCommerce error notices are Gravity Forms validation messages.
 *
 * @param array<int, string> $messages Notice strings.
 */
function tlth_book_wizard_notices_look_like_gf_validation( $messages ) {
	foreach ( $messages as $message ) {
		$message = strtolower( (string) $message );
		if (
			false !== strpos( $message, 'submission' ) ||
			false !== strpos( $message, 'fields below' ) ||
			false !== strpos( $message, 'champs ci-dessous' )
		) {
			return true;
		}
	}

	return false;
}

/**
 * Strip WooCommerce variable-form hash suffix from attribute field names.
 *
 * @param string $key POST field name.
 * @return string
 */
function tlth_book_wizard_normalize_attribute_post_key( $key ) {
	$key = (string) $key;
	if ( 0 !== strpos( $key, 'attribute_' ) ) {
		return $key;
	}

	return preg_replace( '/_[a-f0-9]{6,16}$/i', '', $key );
}

/**
 * Normalize $_POST keys expected by WooCommerce Gravity Forms Product Add-Ons.
 *
 * @param int $product_id Parent product ID.
 * @param int $form_id    Gravity Form ID.
 */
function tlth_book_wizard_normalize_wc_gf_post( $product_id, $form_id ) {
	$product_id = absint( $product_id );
	$form_id    = absint( $form_id );
	$max_page   = tlth_book_wizard_gf_page_count( $form_id );

	$_POST['add-to-cart']           = (string) $product_id;
	$_POST['product_id']            = (string) $product_id;
	$_POST['gform_form_id']         = (string) $form_id;
	$_POST['wc_gforms_form_id']     = (string) $form_id;
	$_POST['gform_old_submit']      = (string) $form_id;
	$_POST['wc_gforms_next_page']   = '0';
	$_POST['wc_gforms_previous_page'] = '0';
	$_POST[ 'gform_target_page_number_' . $form_id ] = '0';
	$_POST[ 'is_submit_' . $form_id ]               = '1';

	$source = isset( $_POST[ 'gform_source_page_number_' . $form_id ] )
		? absint( $_POST[ 'gform_source_page_number_' . $form_id ] )
		: 0;
	if ( $source < 1 || $source > $max_page ) {
		$source = $max_page;
	}
	$_POST[ 'gform_source_page_number_' . $form_id ] = (string) $source;

	// WC GF copies gform_old_submit into gform_submit before process_form().
	unset( $_POST['gform_submit'] );

	$parent = wc_get_product( $product_id );
	if ( $parent instanceof WC_Product ) {
		$_POST['wc_gforms_product_type'] = $parent->is_type( 'variable' ) ? 'variable' : 'simple';
	}

	if ( empty( $_POST['quantity'] ) ) {
		$_POST['quantity'] = 1;
	}

	if ( ! isset( $_POST['gform_field_values'] ) ) {
		$_POST['gform_field_values'] = '';
	}
}

/**
 * Prevent GF from treating the request as an AJAX postback (which echoes confirmation HTML).
 */
function tlth_book_wizard_strip_gf_ajax_from_request() {
	unset( $_POST['gform_ajax'], $_REQUEST['gform_ajax'], $_GET['gform_ajax'] );
}

/**
 * Whether the current admin-ajax request is the wizard add-to-cart action.
 */
function tlth_book_wizard_is_add_to_cart_ajax() {
	if ( ! wp_doing_ajax() ) {
		return false;
	}

	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';

	return 'tlth_book_wizard_add_to_cart' === $action;
}

/**
 * Strip gform_ajax before GF or WC GF hooks run on admin-ajax add-to-cart.
 */
function tlth_book_wizard_early_sanitize_add_to_cart_ajax() {
	if ( ! tlth_book_wizard_is_add_to_cart_ajax() ) {
		return;
	}

	tlth_book_wizard_strip_gf_ajax_from_request();
}
add_action( 'init', 'tlth_book_wizard_early_sanitize_add_to_cart_ajax', 0 );

/**
 * Suppress GF confirmation output during server-side cart processing.
 *
 * @param array|string $confirmation Confirmation config or message.
 * @param array        $form         Form array.
 * @param array        $entry        Entry array.
 * @param bool         $ajax         Whether this is an AJAX submission.
 * @return array|string
 */
function tlth_book_wizard_blank_gf_confirmation( $confirmation, $form, $entry, $ajax ) {
	unset( $form, $entry, $ajax );

	if ( is_array( $confirmation ) ) {
		return array( 'redirect' => '' );
	}

	return '';
}

/**
 * WC GF cart handler instance when available.
 */
function tlth_book_wizard_wc_gf_cart() {
	if ( ! class_exists( 'WC_GFPA_Cart' ) ) {
		return null;
	}

	return WC_GFPA_Cart::instance();
}

/**
 * Get GF submission array for a form after process_form().
 *
 * @param int $form_id Gravity Form ID.
 * @return array<string, mixed>|null
 */
function tlth_book_wizard_get_gf_submission( $form_id ) {
	$form_id = absint( $form_id );

	if ( ! class_exists( 'GFFormDisplay' ) || empty( GFFormDisplay::$submission ) ) {
		return null;
	}

	if ( isset( GFFormDisplay::$submission[ $form_id ] ) ) {
		return GFFormDisplay::$submission[ $form_id ];
	}

	return null;
}

/**
 * Read GF validation state after process_form().
 *
 * @param int $form_id Gravity Form ID.
 * @return array{valid:bool,page_number:int,empty:bool}
 */
function tlth_book_wizard_gf_submission_state( $form_id ) {
	$submission = tlth_book_wizard_get_gf_submission( $form_id );

	if ( null === $submission ) {
		return array(
			'valid'       => false,
			'page_number' => -1,
			'empty'       => true,
		);
	}

	return array(
		'valid'       => ! empty( $submission['is_valid'] ),
		'page_number' => isset( $submission['page_number'] ) ? (int) $submission['page_number'] : 0,
		'empty'       => false,
	);
}

/**
 * Collect per-field GF validation errors after process_form().
 *
 * @param int $form_id Gravity Form ID.
 * @return array<int, array{id:int,label:string,message:string}>
 */
function tlth_book_wizard_collect_gf_field_errors( $form_id ) {
	$form_id = absint( $form_id );
	$errors  = array();

	$submission = tlth_book_wizard_get_gf_submission( $form_id );
	if ( null === $submission || empty( $submission['form']['fields'] ) ) {
		return $errors;
	}

	foreach ( $submission['form']['fields'] as $field ) {
		$field_id = is_object( $field ) ? (int) $field->id : (int) ( $field['id'] ?? 0 );
		if ( ! $field_id ) {
			continue;
		}

		$failed = false;
		if ( is_object( $field ) && ! empty( $field->failed_validation ) ) {
			$failed = true;
		} elseif ( ! empty( $field['failed_validation'] ) ) {
			$failed = true;
		}

		if ( ! $failed ) {
			continue;
		}

		$label = '';
		if ( is_object( $field ) && ! empty( $field->label ) ) {
			$label = (string) $field->label;
		} elseif ( ! empty( $field['label'] ) ) {
			$label = (string) $field['label'];
		}

		$message = '';
		if ( is_object( $field ) && ! empty( $field->validation_message ) ) {
			$message = (string) $field->validation_message;
		} elseif ( ! empty( $field['validation_message'] ) ) {
			$message = (string) $field['validation_message'];
		}

		$errors[] = array(
			'id'      => $field_id,
			'label'   => wp_strip_all_tags( $label ),
			'message' => wp_strip_all_tags( $message ),
		);
	}

	return $errors;
}

/**
 * Collect GF validation messages after process_form().
 *
 * @param int $form_id Gravity Form ID.
 * @return array<int, string>
 */
function tlth_book_wizard_gf_validation_messages( $form_id ) {
	$messages = array();

	foreach ( tlth_book_wizard_collect_gf_field_errors( $form_id ) as $error ) {
		if ( ! empty( $error['message'] ) ) {
			$messages[] = $error['message'];
			continue;
		}
		if ( ! empty( $error['label'] ) ) {
			$messages[] = $error['label'];
		}
	}

	return $messages;
}

/**
 * Run GFFormDisplay::process_form() the same way WC GF does for add-to-cart validation.
 *
 * @param int $product_id Parent product ID.
 * @param int $form_id    Gravity Form ID.
 * @return array<string, mixed>|null Submission array or null.
 */
function tlth_book_wizard_process_gf_for_cart( $product_id, $form_id ) {
	$form_id    = absint( $form_id );
	$product_id = absint( $product_id );

	if ( ! $form_id || ! class_exists( 'GFFormDisplay' ) ) {
		return null;
	}

	tlth_book_wizard_normalize_wc_gf_post( $product_id, $form_id );
	tlth_book_wizard_strip_gf_ajax_from_request();

	$wc_gf_cart = tlth_book_wizard_wc_gf_cart();
	$err_level  = error_reporting();

	error_reporting( 0 );

	add_filter( 'gform_confirmation_' . $form_id, 'tlth_book_wizard_blank_gf_confirmation', 999, 4 );

	if ( $wc_gf_cart ) {
		add_filter( 'gform_disable_notification', array( $wc_gf_cart, 'disable_notifications' ), 999, 3 );
		add_filter( 'gform_disable_user_notification', array( $wc_gf_cart, 'disable_notifications' ), 999, 3 );
		add_filter( 'gform_disable_user_notification_' . $form_id, array( $wc_gf_cart, 'disable_notifications' ), 999, 3 );
		add_filter( 'gform_disable_admin_notification_' . $form_id, array( $wc_gf_cart, 'disable_notifications' ), 10, 3 );
		add_filter( 'gform_disable_notification_' . $form_id, array( $wc_gf_cart, 'disable_notifications' ), 999, 3 );
		add_filter( 'gform_confirmation_' . $form_id, array( $wc_gf_cart, 'disable_confirmation' ), 999, 4 );
		add_filter( 'gform_pre_process_' . $form_id, array( $wc_gf_cart, 'on_gform_pre_process' ) );
	}

	GFFormDisplay::$submission = array();

	$_POST['gform_submit'] = $_POST['gform_old_submit'];
	tlth_book_wizard_strip_gf_ajax_from_request();

	ob_start();
	GFFormDisplay::process_form( $form_id );
	ob_end_clean();

	if ( isset( $_POST['gform_submit'] ) ) {
		$_POST['gform_old_submit'] = $_POST['gform_submit'];
	}
	unset( $_POST['gform_submit'] );

	remove_filter( 'gform_confirmation_' . $form_id, 'tlth_book_wizard_blank_gf_confirmation', 999 );

	if ( $wc_gf_cart ) {
		remove_filter( 'gform_pre_process_' . $form_id, array( $wc_gf_cart, 'on_gform_pre_process' ) );
		remove_filter( 'gform_disable_notification', array( $wc_gf_cart, 'disable_notifications' ), 999 );
		remove_filter( 'gform_disable_user_notification', array( $wc_gf_cart, 'disable_notifications' ), 999 );
		remove_filter( 'gform_disable_user_notification_' . $form_id, array( $wc_gf_cart, 'disable_notifications' ), 999 );
		remove_filter( 'gform_disable_admin_notification_' . $form_id, array( $wc_gf_cart, 'disable_notifications' ), 10 );
		remove_filter( 'gform_disable_notification_' . $form_id, array( $wc_gf_cart, 'disable_notifications' ), 999 );
		remove_filter( 'gform_confirmation_' . $form_id, array( $wc_gf_cart, 'disable_confirmation' ), 999 );
	}

	error_reporting( $err_level );

	return tlth_book_wizard_get_gf_submission( $form_id );
}

/**
 * Debug payload for add-to-cart failures (only when WP_DEBUG or ?tlth_wizard_debug=1).
 *
 * @param int $form_id        Gravity Form ID.
 * @param int $variation_id   Variation ID.
 * @return array<string, mixed>|null
 */
function tlth_book_wizard_add_to_cart_debug( $form_id, $variation_id = 0 ) {
	$form_id      = absint( $form_id );
	$state        = rgpost( 'state_' . $form_id );
	$attributes   = array();
	$input_count  = 0;
	$post_inputs  = array();

	foreach ( $_POST as $key => $value ) {
		$key = (string) $key;
		if ( 0 === strpos( $key, 'attribute_' ) ) {
			$attributes[ tlth_book_wizard_normalize_attribute_post_key( $key ) ] = is_array( $value ) ? $value : (string) $value;
		}
		if ( 0 === strpos( $key, 'input_' ) ) {
			$input_count++;
			$post_inputs[] = $key;
		}
	}

	$wc_errors = array();
	if ( function_exists( 'wc_get_notices' ) ) {
		foreach ( wc_get_notices( 'error' ) as $notice ) {
			if ( ! empty( $notice['notice'] ) ) {
				$wc_errors[] = wp_strip_all_tags( $notice['notice'] );
			}
		}
	}

	return array(
		'form_id'           => $form_id,
		'variation_id'      => absint( $variation_id ),
		'variation_in_post' => absint( $_POST['variation_id'] ?? 0 ),
		'source_page'       => rgpost( 'gform_source_page_number_' . $form_id ),
		'target_page'       => rgpost( 'gform_target_page_number_' . $form_id ),
		'has_state'         => ! empty( $state ),
		'state_length'      => is_string( $state ) ? strlen( $state ) : 0,
		'has_gform_id'      => ! empty( $_POST['gform_form_id'] ),
		'input_field_count' => $input_count,
		'post_input_keys'   => $post_inputs,
		'attribute_keys'    => array_keys( $attributes ),
		'attributes'        => $attributes,
		'gf_submission'     => tlth_book_wizard_gf_submission_state( $form_id ),
		'wc_errors'         => $wc_errors,
	);
}

/**
 * Rewrite hashed attribute_* keys in $_POST to canonical WooCommerce names.
 */
function tlth_book_wizard_normalize_attribute_post_keys() {
	$rewrites = array();

	foreach ( $_POST as $key => $value ) {
		$key = (string) $key;
		if ( 0 !== strpos( $key, 'attribute_' ) ) {
			continue;
		}

		$normalized = tlth_book_wizard_normalize_attribute_post_key( $key );
		if ( $normalized === $key ) {
			continue;
		}

		// Prefer a non-empty value when both hashed and canonical keys exist.
		$incoming = is_array( $value ) ? $value : wp_unslash( $value );
		if ( ! isset( $rewrites[ $normalized ] ) || '' === (string) $rewrites[ $normalized ] ) {
			$rewrites[ $normalized ] = $incoming;
		}

		unset( $_POST[ $key ] );
	}

	foreach ( $rewrites as $key => $value ) {
		if ( ! isset( $_POST[ $key ] ) || '' === (string) wp_unslash( $_POST[ $key ] ) ) {
			$_POST[ $key ] = $value;
		}
	}
}

/**
 * Resolve an attribute value to one of the parent product's allowed options.
 *
 * @param WC_Product $product       Parent product.
 * @param string     $attribute_key Full attribute key (attribute_pa_…).
 * @param string     $value         Posted or stored value.
 * @return string Resolved value or empty string if not resolvable.
 */
function tlth_book_wizard_resolve_attribute_option( $product, $attribute_key, $value ) {
	$value = rawurldecode( (string) $value );
	if ( '' === $value || ! ( $product instanceof WC_Product ) ) {
		return '';
	}

	$attributes = $product->get_attributes();
	$attr_name  = preg_replace( '/^attribute_/', '', $attribute_key );

	if ( empty( $attributes[ $attr_name ] ) ) {
		// Try taxonomy form pa_xxx vs custom attribute name.
		foreach ( array_keys( $attributes ) as $name ) {
			if ( sanitize_title( $name ) === sanitize_title( $attr_name ) ) {
				$attr_name = $name;
				break;
			}
		}
	}

	if ( empty( $attributes[ $attr_name ] ) ) {
		return $value;
	}

	$attribute = $attributes[ $attr_name ];
	$options   = array();

	if ( $attribute->is_taxonomy() ) {
		$options = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'slugs' ) );
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		if ( in_array( $value, $options, true ) ) {
			return $value;
		}

		$sanitized = sanitize_title( $value );
		if ( in_array( $sanitized, $options, true ) ) {
			return $sanitized;
		}

		// Match term name / label → slug.
		$terms = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'all' ) );
		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( ! isset( $term->slug, $term->name ) ) {
					continue;
				}
				if ( $term->name === $value || sanitize_title( $term->name ) === $sanitized ) {
					return $term->slug;
				}
			}
		}

		return '';
	}

	$options = $attribute->get_options();
	if ( ! is_array( $options ) ) {
		$options = array();
	}

	if ( in_array( $value, $options, true ) ) {
		return $value;
	}

	$sanitized = sanitize_title( $value );
	foreach ( $options as $option ) {
		if ( sanitize_title( (string) $option ) === $sanitized ) {
			return (string) $option;
		}
	}

	return '';
}

/**
 * Attributes stored on a variation, resolved against the parent product options.
 *
 * @param int $variation_id Variation ID.
 * @param int $product_id   Parent product ID.
 * @return array<string, string>
 */
function tlth_book_wizard_variation_stored_attributes( $variation_id, $product_id = 0 ) {
	$variation_id = absint( $variation_id );
	if ( ! $variation_id ) {
		return array();
	}

	$variation = wc_get_product( $variation_id );
	if ( ! ( $variation instanceof WC_Product_Variation ) ) {
		return array();
	}

	$stored = $variation->get_variation_attributes();
	if ( empty( $stored ) || ! is_array( $stored ) ) {
		return array();
	}

	$product_id = absint( $product_id ) ?: (int) $variation->get_parent_id();
	$parent     = wc_get_product( $product_id );

	$attributes = array();
	foreach ( $stored as $key => $value ) {
		$value = is_string( $value ) ? rawurldecode( $value ) : (string) $value;
		// Empty means "Any" — do not pass to add_to_cart (WC treats it as invalid).
		if ( '' === $value ) {
			continue;
		}

		if ( $parent instanceof WC_Product ) {
			$resolved = tlth_book_wizard_resolve_attribute_option( $parent, $key, $value );
			if ( '' === $resolved ) {
				continue;
			}
			$value = $resolved;
		}

		$attributes[ $key ] = $value;
	}

	return $attributes;
}

/**
 * Build variation attribute array for add_to_cart().
 *
 * Prefer the variation's stored attributes (resolved to valid parent options) so
 * hashed keys / empty selects / label mismatches from the form POST cannot fail WC.
 *
 * @param int $variation_id Variation ID when known.
 * @param int $product_id   Parent product ID.
 * @return array<string, string>
 */
function tlth_book_wizard_variation_attributes_from_request( $variation_id = 0, $product_id = 0 ) {
	$variation_id = absint( $variation_id );
	$product_id   = absint( $product_id );

	tlth_book_wizard_normalize_attribute_post_keys();

	$stored = tlth_book_wizard_variation_stored_attributes( $variation_id, $product_id );
	if ( ! empty( $stored ) ) {
		foreach ( $stored as $key => $value ) {
			$_POST[ $key ] = $value;
		}
		return $stored;
	}

	$parent = $product_id ? wc_get_product( $product_id ) : null;
	$variation_attributes = array();

	foreach ( $_POST as $key => $value ) {
		if ( 0 !== strpos( (string) $key, 'attribute_' ) ) {
			continue;
		}

		if ( is_array( $value ) ) {
			$value = reset( $value );
		}

		$normalized_key = tlth_book_wizard_normalize_attribute_post_key( $key );
		$clean_value    = wc_clean( wp_unslash( $value ) );

		if ( '' === (string) $clean_value ) {
			continue;
		}

		if ( $parent instanceof WC_Product ) {
			$resolved = tlth_book_wizard_resolve_attribute_option( $parent, $normalized_key, $clean_value );
			if ( '' === $resolved ) {
				continue;
			}
			$clean_value = $resolved;
		}

		$variation_attributes[ $normalized_key ] = $clean_value;
	}

	return $variation_attributes;
}

/**
 * Build WC GF cart item meta from an already-processed GF submission.
 *
 * Mirrors WC_GFPA_Cart::add_cart_item_data() field copying so the cart line
 * receives _gravity_form_data / _gravity_form_lead without a second process_form().
 *
 * @param int $product_id   Parent product ID.
 * @param int $form_id      Gravity Form ID.
 * @param int $variation_id Variation ID.
 * @return array{ _gravity_form_data?:mixed, _gravity_form_lead?:array<string,mixed> }
 */
function tlth_book_wizard_build_cart_item_gf_data( $product_id, $form_id, $variation_id = 0 ) {
	$product_id   = absint( $product_id );
	$form_id      = absint( $form_id );
	$variation_id = absint( $variation_id );
	$submission   = tlth_book_wizard_get_gf_submission( $form_id );

	if ( empty( $submission['lead'] ) || ! is_array( $submission['lead'] ) ) {
		return array();
	}

	$lead = $submission['lead'];

	$gravity_form_data = null;
	if ( function_exists( 'wc_gfpa' ) ) {
		$gravity_form_data = wc_gfpa()->get_gravity_form_data( $product_id );
	}
	if ( ! is_array( $gravity_form_data ) ) {
		$gravity_form_data = get_post_meta( $product_id, '_gravity_form_data', true );
	}
	if ( ! is_array( $gravity_form_data ) ) {
		return array();
	}

	if ( ! class_exists( 'RGFormsModel' ) ) {
		return array();
	}

	$form_meta = RGFormsModel::get_form_meta( $form_id );
	if ( empty( $form_meta['fields'] ) ) {
		return array();
	}

	$cart_lead = array(
		'form_id'    => $form_id,
		'source_url' => isset( $lead['source_url'] ) ? $lead['source_url'] : '',
		'ip'         => isset( $lead['ip'] ) ? $lead['ip'] : '',
	);

	foreach ( $form_meta['fields'] as $field ) {
		if ( ! empty( $field->displayOnly ) || ( is_array( $field ) && ! empty( $field['displayOnly'] ) ) ) {
			continue;
		}

		$field_id = is_object( $field ) ? (int) $field->id : (int) ( $field['id'] ?? 0 );
		if ( ! $field_id ) {
			continue;
		}

		$value  = RGFormsModel::get_lead_field_value( $lead, $field );
		$inputs = is_object( $field ) && method_exists( $field, 'get_entry_inputs' )
			? $field->get_entry_inputs()
			: rgar( $field, 'inputs' );

		if ( is_array( $inputs ) ) {
			$lead_field_keys = array_keys( $lead );
			natsort( $lead_field_keys );

			foreach ( $lead_field_keys as $input_id ) {
				if ( ! is_numeric( $input_id ) || absint( $input_id ) !== $field_id ) {
					continue;
				}

				$input_value = is_array( $value ) && isset( $value[ strval( $input_id ) ] )
					? $value[ strval( $input_id ) ]
					: ( $lead[ strval( $input_id ) ] ?? '' );

				$cart_lead[ strval( $input_id ) ] = $input_value;
			}

			foreach ( $inputs as $input ) {
				$input_id = isset( $input['id'] ) ? strval( $input['id'] ) : '';
				if ( '' === $input_id || ! isset( $cart_lead[ $input_id ] ) ) {
					continue;
				}

				$cart_lead[ $input_id ] = apply_filters(
					'wcgf_gform_input_value',
					$cart_lead[ $input_id ],
					$product_id,
					$variation_id,
					$field,
					$input
				);
			}
		} else {
			$cart_lead[ strval( $field_id ) ] = apply_filters(
				'wcgf_gform_field_value',
				$value,
				$product_id,
				$variation_id,
				$field
			);
		}
	}

	$delete_cart_entries = ! ( isset( $gravity_form_data['keep_cart_entries'] ) && 'yes' === $gravity_form_data['keep_cart_entries'] );
	if ( apply_filters( 'woocommerce_gravityforms_delete_entries', $delete_cart_entries ) ) {
		$entry_id = isset( $lead['id'] ) ? absint( $lead['id'] ) : 0;
		if ( $entry_id && class_exists( 'GFAPI' ) ) {
			GFAPI::delete_entry( $entry_id );
		}
	}

	return array(
		'_gravity_form_data' => $gravity_form_data,
		'_gravity_form_lead' => $cart_lead,
	);
}

/**
 * JSON response when GF validation blocks add-to-cart.
 *
 * @param int    $product_id   Parent product ID.
 * @param int    $form_id      Gravity Form ID.
 * @param int    $variation_id Variation ID.
 * @param string $reason       Machine-readable failure reason.
 */
function tlth_book_wizard_send_add_to_cart_gf_validation_failed( $product_id, $form_id, $variation_id, $reason = 'invalid_fields' ) {
	$form_id      = absint( $form_id );
	$product_id   = absint( $product_id );
	$variation_id = absint( $variation_id );
	$gf_state     = tlth_book_wizard_gf_submission_state( $form_id );
	$gf_messages  = tlth_book_wizard_gf_validation_messages( $form_id );
	$field_errors = tlth_book_wizard_collect_gf_field_errors( $form_id );
	$debug        = tlth_book_wizard_add_to_cart_debug( $form_id, $variation_id );

	if ( empty( $gf_messages ) && ! empty( $debug['wc_errors'] ) ) {
		$gf_messages = $debug['wc_errors'];
	}

	wp_send_json_success(
		array(
			'added'                => false,
			'has_product'          => tlth_book_wizard_cart_has_line( $product_id, $variation_id ),
			'count'                => function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0,
			'cart_key'             => '',
			'validation_failed'    => true,
			'wc_validation_failed' => false,
			'gf_validation_failed' => true,
			'reason'               => $reason,
			'wc_errors'            => $debug['wc_errors'],
			'gf'                   => array(
				'status'       => 'validation_failed',
				'page_number'  => (int) $gf_state['page_number'],
				'messages'     => $gf_messages,
				'field_errors' => $field_errors,
			),
			'debug'                => $debug,
		)
	);
}

/**
 * Process add-to-cart through WooCommerce + WC GF validation (same as product page POST).
 */
function tlth_ajax_book_wizard_add_to_cart() {
	check_ajax_referer( 'tlth_book_wizard', 'nonce' );

	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( array( 'message' => 'WooCommerce unavailable.' ) );
	}

	if ( null === WC()->cart ) {
		wc_load_cart();
	}

	$product_id   = absint( $_POST['add-to-cart'] ?? $_POST['product_id'] ?? 0 );
	$variation_id = absint( $_POST['variation_id'] ?? 0 );
	$form_id      = absint( $_POST['gform_submit'] ?? $_POST['gform_form_id'] ?? $_POST['gform_old_submit'] ?? 0 );

	if ( ! $product_id || ! $form_id ) {
		wp_send_json_error( array( 'message' => 'Missing product or form.' ) );
	}

	tlth_book_wizard_strip_gf_ajax_from_request();
	tlth_book_wizard_normalize_wc_gf_post( $product_id, $form_id );

	$count_before = WC()->cart->get_cart_contents_count();
	$quantity     = max( 1, absint( $_POST['quantity'] ?? 1 ) );
	$product      = wc_get_product( $product_id );

	if ( $product instanceof WC_Product_Variable && ! $variation_id ) {
		wp_send_json_success(
			array(
				'added'                => false,
				'has_product'          => false,
				'count'                => WC()->cart->get_cart_contents_count(),
				'validation_failed'    => true,
				'wc_validation_failed' => true,
				'gf_validation_failed' => false,
				'reason'               => 'missing_variation_id',
				'debug'                => tlth_book_wizard_add_to_cart_debug( $form_id, $variation_id ),
			)
		);
	}

	wc_clear_notices();

	/*
	 * Validate GF once, then hand the resulting lead to WC as cart_item_data.
	 * Calling process_form() and then letting WC GF re-process during add_to_cart()
	 * consumes the unique submission and leaves the cart line without _gravity_form_lead.
	 */
	tlth_book_wizard_process_gf_for_cart( $product_id, $form_id );
	$gf_state = tlth_book_wizard_gf_submission_state( $form_id );
	$gf_valid = ! empty( $gf_state['valid'] ) && 0 === (int) $gf_state['page_number'];

	if ( ! $gf_valid ) {
		$reason = ! empty( $gf_state['empty'] ) ? 'gf_not_processed' : 'invalid_fields';
		if ( ! empty( $gf_state['valid'] ) && 0 !== (int) $gf_state['page_number'] ) {
			$reason = 'incomplete_pages';
		}
		tlth_book_wizard_send_add_to_cart_gf_validation_failed( $product_id, $form_id, $variation_id, $reason );
	}

	$cart_item_data = tlth_book_wizard_build_cart_item_gf_data( $product_id, $form_id, $variation_id );
	if ( empty( $cart_item_data['_gravity_form_lead'] ) || empty( $cart_item_data['_gravity_form_data'] ) ) {
		tlth_book_wizard_send_add_to_cart_gf_validation_failed( $product_id, $form_id, $variation_id, 'gf_lead_missing' );
	}

	// Resolve attributes after GF processing, using the variation's canonical values.
	$variation_attributes = tlth_book_wizard_variation_attributes_from_request( $variation_id, $product_id );

	$wc_gf_cart = tlth_book_wizard_wc_gf_cart();
	if ( $wc_gf_cart ) {
		// Skip WC GF re-validation/re-process; lead is already attached below.
		// Priority differs across plugin versions (10 historically, 99 in current).
		remove_filter( 'woocommerce_add_to_cart_validation', array( $wc_gf_cart, 'add_to_cart_validation' ), 99 );
		remove_filter( 'woocommerce_add_to_cart_validation', array( $wc_gf_cart, 'add_to_cart_validation' ), 10 );
	}

	wc_clear_notices();
	add_filter( 'gform_confirmation_' . $form_id, 'tlth_book_wizard_blank_gf_confirmation', 999, 4 );

	$cart_item_key = WC()->cart->add_to_cart(
		$product_id,
		$quantity,
		$variation_id,
		$variation_attributes,
		$cart_item_data
	);

	remove_filter( 'gform_confirmation_' . $form_id, 'tlth_book_wizard_blank_gf_confirmation', 999 );

	if ( $wc_gf_cart ) {
		add_filter( 'woocommerce_add_to_cart_validation', array( $wc_gf_cart, 'add_to_cart_validation' ), 99, 3 );
	}

	$count_after = WC()->cart->get_cart_contents_count();
	$has_product = tlth_book_wizard_cart_has_line( $product_id, $variation_id );
	$added       = (bool) $cart_item_key && $count_after > $count_before;
	$cart_item   = ( $added && $cart_item_key ) ? WC()->cart->get_cart_item( $cart_item_key ) : null;
	$has_gf_lead = is_array( $cart_item )
		&& ! empty( $cart_item['_gravity_form_lead'] )
		&& ! empty( $cart_item['_gravity_form_data'] );
	$link_failed = false;

	if ( $added && ! $has_gf_lead && $cart_item_key ) {
		WC()->cart->remove_cart_item( $cart_item_key );
		$count_after   = WC()->cart->get_cart_contents_count();
		$has_product   = tlth_book_wizard_cart_has_line( $product_id, $variation_id );
		$added         = false;
		$link_failed   = true;
		$cart_item_key = false;
	}

	if ( $added && $has_gf_lead ) {
		wp_send_json_success(
			array(
				'added'       => true,
				'has_product' => $has_product,
				'count'       => $count_after,
				'cart_key'    => (string) $cart_item_key,
			)
		);
	}

	$debug        = tlth_book_wizard_add_to_cart_debug( $form_id, $variation_id );
	$gf_state     = tlth_book_wizard_gf_submission_state( $form_id );
	$gf_valid     = ! empty( $gf_state['valid'] ) && 0 === (int) $gf_state['page_number'];
	$gf_failed    = ! $gf_valid && empty( $gf_state['empty'] );
	$gf_messages  = tlth_book_wizard_gf_validation_messages( $form_id );
	$field_errors = tlth_book_wizard_collect_gf_field_errors( $form_id );
	$reason       = $gf_failed ? 'invalid_fields' : ( $link_failed ? 'gf_not_linked' : 'add_to_cart_failed' );

	if ( $gf_failed && ! empty( $gf_state['valid'] ) && 0 !== (int) $gf_state['page_number'] ) {
		$reason = 'incomplete_pages';
	}

	if ( empty( $gf_messages ) && ! empty( $debug['wc_errors'] ) ) {
		$gf_messages = $debug['wc_errors'];
	}

	if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || isset( $_GET['tlth_wizard_debug'] ) ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log(
			'[tlthBookWizard] Add to cart failed: ' . wp_json_encode(
				array(
					'cart_item_key' => $cart_item_key,
					'debug'         => $debug,
					'gf_state'      => $gf_state,
				)
			)
		);
	}

	wp_send_json_success(
		array(
			'added'                => false,
			'has_product'          => $has_product,
			'count'                => $count_after,
			'cart_key'             => $cart_item_key ? (string) $cart_item_key : '',
			'validation_failed'    => true,
			'wc_validation_failed' => ! $gf_failed,
			'gf_validation_failed' => $gf_failed,
			'reason'               => $reason,
			'wc_errors'            => $debug['wc_errors'],
			'gf'                   => $gf_failed ? array(
				'status'       => 'validation_failed',
				'page_number'  => (int) $gf_state['page_number'],
				'messages'     => $gf_messages,
				'field_errors' => $field_errors,
			) : null,
			'debug'                => $debug,
		)
	);
}
add_action( 'wp_ajax_tlth_book_wizard_add_to_cart', 'tlth_ajax_book_wizard_add_to_cart' );
add_action( 'wp_ajax_nopriv_tlth_book_wizard_add_to_cart', 'tlth_ajax_book_wizard_add_to_cart' );

/**
 * AJAX handler for GF multi-page navigation inside the product wizard.
 * Delegates to Gravity Forms' native gf_submit_form handler.
 */
function tlth_ajax_book_wizard_gf_page() {
	check_ajax_referer( 'tlth_book_wizard', 'nonce' );

	if ( ! empty( $_POST['gform_old_submit'] ) ) {
		$_POST['gform_submit'] = absint( $_POST['gform_old_submit'] );
	}

	$form_id = absint( $_POST['gform_submit'] ?? $_POST['gform_form_id'] ?? 0 );
	if ( ! $form_id || ! class_exists( 'GFFormDisplay' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid form.' ), 400 );
	}

	$target_page = isset( $_POST[ 'gform_target_page_number_' . $form_id ] )
		? absint( $_POST[ 'gform_target_page_number_' . $form_id ] )
		: 0;
	$source_page = isset( $_POST[ 'gform_source_page_number_' . $form_id ] )
		? absint( $_POST[ 'gform_source_page_number_' . $form_id ] )
		: 1;
	$max_page    = tlth_book_wizard_gf_page_count( $form_id );

	if ( $target_page > 0 && $target_page > $max_page ) {
		wp_send_json_error( array( 'message' => 'Invalid page.' ), 400 );
	}

	if ( empty( $_POST['gform_ajax'] ) ) {
		$_POST['gform_ajax'] = 'form_id_' . $form_id;
	}

	$_POST['action']    = 'gf_submit_form';
	$_REQUEST['action'] = 'gf_submit_form';

	$debug = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || isset( $_GET['tlth_wizard_debug'] );

	ob_start();
	GFFormDisplay::process_form( $form_id );
	$output = ob_get_clean();

	if ( $output ) {
		$decoded = json_decode( $output, true );
		if ( is_array( $decoded ) ) {
			wp_send_json( $decoded );
		}
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_die();
	}

	wp_send_json_error(
		array(
			'message' => 'Empty response from GFFormDisplay::process_form.',
			'debug'   => $debug ? array(
				'form_id'     => $form_id,
				'source_page' => $source_page,
				'target_page' => $target_page,
				'max_page'    => $max_page,
				'gform_ajax'  => rgpost( 'gform_ajax' ),
			) : null,
		),
		500
	);
}
add_action( 'wp_ajax_tlth_book_wizard_gf_page', 'tlth_ajax_book_wizard_gf_page' );
add_action( 'wp_ajax_nopriv_tlth_book_wizard_gf_page', 'tlth_ajax_book_wizard_gf_page' );
