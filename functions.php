<?php
/*
 * Load the framework
 */
require_once('fw/setup.php');

/*
 * Enqueue our admin CSS
 */
add_action( 'admin_enqueue_scripts', 'book_maker_css' );
function book_maker_css($hook)
{
	wp_enqueue_style( 'book_maker', THEME_URL . '/assets/css/admin.css');
	wp_enqueue_script( 'book_maker', THEME_URL . '/assets/js/admin.js');
}

/*
 * Require secondary files
 */
require_once('fw/woocommerce.php');
require_once('fw/book-maker.php');
//require_once('fw/import-gift-cards.php');
//require_once('fw/import-users.php');
require_once( 'fw/dompdf/autoload.inc.php' );

use Dompdf\Dompdf;

/*
 * Check if shop notice is enabled
 */
function is_shop_notice_enabled()
{
	return get_field('shop-notice-activated', 'option');
}


/*
 * Adding tweaks to menu items
 */
add_filter('wp_nav_menu_objects', 'tweaking_menu_items', 10, 2);
function tweaking_menu_items( $items, $args )
{
	foreach( $items as &$item )
	{
		$highlight = get_field('highlight-link', $item);
		$is_btn = get_field('is-btn', $item);
		$lang_btn = get_field('lang-btn', $item);

		if( $highlight ){
			$item->classes[] = 'highlight-link';
		}

		if( $is_btn ){
			$item->classes[] = 'is-btn';
		}

		if( $lang_btn ){
			$item->classes[] = 'lang-switcher';
		}

	}

	return $items;
}

/*
 * Return product category colors
 */
function get_product_cat_color($term_id)
{
	return get_field('product-cat-color', 'product_cat_' . $term_id);
}


function get_product_categories_badges()
{
	$product_categories = get_the_terms( get_the_ID(), 'product_cat' );

	if( ! empty($product_categories) ){ ?>
        <div class="product-themes">
            <ul>
				<? foreach($product_categories as $product_category){ ?>
                    <li>
                        <a href="<?= get_term_link($product_category); ?>" class="product-theme-icon" <? if( get_product_cat_color($product_category->term_id) ){ ?>style="background-color: <?= get_product_cat_color($product_category->term_id); ?>"<? } ?>>
							<?
							$product_cat_img_id = get_term_meta( $product_category->term_id, 'thumbnail_id', true );

							if( ! empty($product_cat_img_id) ) {
								echo '<span class="img">' . file_get_contents( get_attached_file($product_cat_img_id) ) . '</span>';
							}
							?>

                            <span class="label"><?= $product_category->name; ?></span>
                        </a>
                    </li>
				<? } ?>
            </ul>
        </div>
		<?php
	}
}

/*
 * Add AffilaiteWP to Woo account page
 */

function moh_add_aff_wp_endpoint()
{
	add_rewrite_endpoint( 'aff', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'moh_add_aff_wp_endpoint' );

function moh_add_aff_wp_link_my_account( $items )
{
	if ( function_exists( 'affwp_is_affiliate' ) && affwp_is_affiliate() ) {
		$logout = array_pop( $items );
		$items['aff'] = __('Affiliate Area', 'affiliate-wp');
		$items['customer-logout'] = $logout;
	}
	return $items;
}
add_filter( 'woocommerce_account_menu_items', 'moh_add_aff_wp_link_my_account' );


function moh_aff_wp_content()
{
	if ( ! class_exists( 'Affiliate_WP_Shortcodes' ) ) {
		return;
	}
	$shortcode = new Affiliate_WP_Shortcodes;
	echo $shortcode->affiliate_area( null );
}
add_action( 'woocommerce_account_aff_endpoint', 'moh_aff_wp_content' );


function moh_filter_aff_tabs( $url, $page_id, $tab )
{
	return esc_url_raw( add_query_arg( 'tab', $tab ) );
}
add_filter( 'affwp_affiliate_area_page_url', 'moh_filter_aff_tabs', 10, 3 );

function get_review_data()
{

	// OLD  - $api_key = 'AIzaSyDq4EQopiff2zbeL-0xqcLqvClhL6qUhWY';
	$api_key = 'AIzaSyA933ZswohWGbkXhvUR1fAOnDtJVjMiaJY'; 

	$place_id = 'ChIJJ9rllACkyUwRdZ5wgINOFxw';
	$lang = LANG;
	$url = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$place_id}&language={$lang}&fields=rating,reviews,user_ratings_total&key={$api_key}";

	// create curl resource
	$ch = curl_init();

	// set url
	curl_setopt($ch, CURLOPT_URL, $url);

	// return the transfer as a string
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// $output contains the output string
	$output = curl_exec($ch);

	// close curl resource to free up system resources
	curl_close($ch);

	$output = json_decode($output);

	//print_r($output);


	if( $output->status == 'OK' ) {
		return $output;
	} else {
		return [];
	}
}

/*
 * Stylized strings
 */
function stylized_string_red($string)
{
	$string = str_replace('/*', '<span class="color-red">', $string);
	$string = str_replace('*/', '</span>', $string);

	return $string;
}

/*
 * Free shipping badge
 */
function get_product_free_shipping_badge()
{
	if( ! get_field('show-free-shipping-tag' ) ) return;

	echo '<div class="free-shipping-badge">' . pll__('free-shipping') . '</div>';
}

/*
 * Create cookie on newsletter form submission
 */
add_action('gform_after_submission_14', 'set_cookie_after_newsletter_form');
//add_action('gform_after_submission_14', 'set_cookie_after_newsletter_form');
function set_cookie_after_newsletter_form()
{
	setcookie('show-newsletter-badge', 'false', strtotime('+1 year'), '/' );
}

/*
 * Exclude product categories
 */
function get_shop_excluded_product_categories()
{
	$product_categories = get_field('shop-product-categories-to-hide', 'option');

	return $product_categories;
}

add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );
function custom_pre_get_posts_query( $q )
{

	$tax_query = (array) $q->get( 'tax_query' );

	$tax_query[] = array(
		'taxonomy' => 'product_cat',
		'terms' => get_shop_excluded_product_categories(),
		'operator' => 'NOT IN'
	);

	$q->set( 'tax_query', $tax_query );

}

/*
 * Only show cheque to school and daycares
 */
add_filter('woocommerce_available_payment_gateways', 'filter_gateways', 1);
function filter_gateways($gateways)
{
	if( ! is_user_logged_in() || ! in_array(CURRENT_USER_ROLE, ['school', 'daycare', 'administrator']) ){
		unset($gateways['cheque']);
	}

	return $gateways;
}

add_action( 'template_redirect', 'define_default_payment_gateway' );
function define_default_payment_gateway()
{
	if( is_checkout() && ! is_wc_endpoint_url() )
	{
		$default_payment_id = 'stripe';

		WC()->session->set( 'chosen_payment_method', $default_payment_id );
	}
}

/*
 * Check if string starts with a vowel
 */
function is_vowel($string)
{
	$first_letter = mb_substr($string, 0, 1);

	return in_array( $first_letter, ['a','e','i','o','u', 'A', 'E', 'I', 'O', 'U', 'É', 'È', 'Ê', 'é', 'è', 'ê']);
}

/*
 * WooCommerce AJAX update header cart count
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'wc_refresh_mini_cart_count');
function wc_refresh_mini_cart_count($fragments){
	ob_start();
	?>
    <span id="cart-count" class="cart-count"><?= WC()->cart->get_cart_contents_count(); ?></span>
	<?php
	$fragments['#cart-count'] = ob_get_clean();

	ob_start();
	?>
    <span id="cart-total" class="total"><?= wc_price( WC()->cart->cart_contents_total ); ?></span>
	<?php
	$fragments['#cart-total'] = ob_get_clean();

	return $fragments;
}


/*
 * NEW - Dynamically add gift cards to cart depending on cart subtotal - NEW - 1 gift card, with meter and dynamic notice
*/
add_action( 'woocommerce_before_calculate_totals', 'product_specials_on_books', 10, 1);
function product_specials_on_books( $cart )
{

	if( ! is_cart() ) return; {
		if( did_action( 'woocommerce_before_calculate_totals') >= 2 ){
			return;
		}

		if( ! get_field('shop-gift-card-75', 'option') ){
			return;
		}

		if( ! get_field('shop-gift-card-value', 'option') ){
			return;
		}

		$shop_value = get_field('shop-gift-card-value', 'option');

		$threshold_amount = $shop_value;
		$product_id = get_field('shop-gift-card-75-'. LANG, 'option');

		$cart_items_total = 0;

		//cart loop
		foreach( $cart->get_cart() as $cart_item_key => $cart_item ){

			//check if gift card is in cart
			if( $cart_item['data']->get_id() == $product_id ){
				$free_item_key = $cart_item_key;
			}

			$cart_items_total += $cart_item['line_total'] ?? 0;
		}

		// Add %s to your order notice before $shop_value
		if( $cart_items_total < $threshold_amount ){

			$needed_value = $shop_value - $cart_items_total;

			$meter =  '<meter id="shop-meter" value="' . ( $cart_items_total / $shop_value ) * 100 . '" min="0" max="100"></meter>';

			wc_print_notice( sprintf( pll__('Ajoutez %s à votre commande pout obtenir une carte cadeau de 25$'), $needed_value ) . $meter, 'meter' );
		}

		// Add gift card
		if( $cart_items_total >= $threshold_amount ){

			if( ! isset($free_item_key) ) {
				$cart->add_to_cart($product_id);

				wc_print_notice( pll__('giftcard-75'), 'success' );
			}
		}

		// Remove all gift cards
		if( $cart_items_total < $threshold_amount ){

			if( isset($free_item_key) ){
				$cart->remove_cart_item( $free_item_key );
			}
		}
	}

}

/*
 * Christmas promotion
*/
/*
add_action( 'woocommerce_before_calculate_totals', 'product_specials_on_books', 10, 1);
function product_specials_on_books( $cart )
{

	if( ! is_cart() ) return; {
		if( did_action( 'woocommerce_before_calculate_totals') >= 2 ){
			return;
		}

		$shop_nb_books_needed = get_field('shop-nb-books', 'option');

		$cart_book_total = 0;

		//cart loop
		foreach( $cart->get_cart() as $cart_item_key => $cart_item ){

			//print_r(get_the_terms($cart_item['product_id'], "product_tag"));

			if( get_the_terms($cart_item['product_id'], "product_tag")[0]->name == "Livres" || get_the_terms($cart_item['product_id'], "product_tag")[0]->name == "Books" ){
				$cart_book_total+=$cart_item["quantity"];
			}

		}

		// Add %s to your order notice before $shop_value
		if( $cart_book_total < $shop_nb_books_needed ){

			$needed_value = $shop_nb_books_needed - $cart_book_total;

			$meter =  '<meter id="shop-meter" value="' . ( $cart_book_total / $shop_nb_books_needed ) * 100 . '" min="0" max="100"></meter>';

            if($needed_value > 1){
	            wc_print_notice( sprintf( pll__("Ajoute %s livres à ta commande et le montant de 24,95$ équivalant à un livre à couverture souple sera automatiquement déduit de ta facture totale."), $needed_value ) . $meter, 'meter' );
            } else{
	            wc_print_notice( sprintf( pll__("Ajoute %s livre à ta commande et le montant de 24,95$ équivalant à un livre à couverture souple sera automatiquement déduit de ta facture totale."), $needed_value ) . $meter, 'meter' );
            }
		}

		// Add gift card
		if( $cart_book_total >= $shop_nb_books_needed ){

			wc_print_notice( pll__('xmas-book-promo'), 'success' );

		}

	}

}
*/

function weighted_random($values, $weights){
	$count = count($values);
	$i = 0;
	$n = 0;
	$num = mt_rand(0, array_sum($weights));
	while($i < $count){
		$n += $weights[$i];
		if($n >= $num){
			break;
		}
		$i++;
	}
	return $values[$i];
}

/*
 * Create promo code after gform submission
 */
add_action('gform_pre_submission_40', 'generate_promo_code_from_chirstmas_game');
function generate_promo_code_from_chirstmas_game($form)
{
    //Prize
    $prize_id = $_POST['input_9'];
    $discount_type = get_field("prize-value-type", $prize_id);
    $discount_amount = get_field("prize-value", $prize_id);
	$products_ids = [];

	$end_of_promo_code = 'jeudenoel';
	$promo_code =  $end_of_promo_code . '-' . FW::random_string();
	$_POST['input_11'] = $promo_code;

	$coupon_code_post_id = wp_insert_post(
		array(
			'post_title' => $promo_code,
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type' => 'shop_coupon'
		)
	);

	update_post_meta( $coupon_code_post_id, 'discount_type', $discount_type );
	update_post_meta( $coupon_code_post_id, 'coupon_amount', $discount_amount );

	if( !get_field("purchased-required", $prize_id) ) {
		update_post_meta( $coupon_code_post_id, 'minimum_amount', "24,95" );
	}

	if( !get_field("add-participation-prize", $prize_id) ) {
		update_post_meta($coupon_code_post_id, 'individual_use', 'yes');
	}

	update_post_meta( $coupon_code_post_id, 'usage_limit', 1 );
	update_post_meta( $coupon_code_post_id, 'usage_limit_per_user', 1 );
	update_post_meta( $coupon_code_post_id, 'customer_email', $_POST['input_7'] );
	update_post_meta( $coupon_code_post_id, 'date_expires', strtotime('1 January 2024') );

	if( $discount_type == "fixed_cart"){
	    foreach( get_field("gift-product", $prize_id) as $product){
			$products_ids[] = $product->ID;
        }

		update_post_meta( $coupon_code_post_id, 'product_ids', implode(",", $products_ids) );
    }

	if( get_field("add-participation-prize", $prize_id) ){

		$prize_id = get_field("participation-prize-" . LANG, "option")->ID;
		$discount_type = get_field("prize-value-type", $prize_id);
		$discount_amount = get_field("prize-value", $prize_id);
		$products_ids = [];

		$promo_code =  $end_of_promo_code . '-' . FW::random_string();
		$_POST['input_12'] = $promo_code;

		$coupon_code_post_id = wp_insert_post(
			array(
				'post_title' => $promo_code,
				'post_content' => '',
				'post_status' => 'publish',
				'post_author' => 1,
				'post_type' => 'shop_coupon'
			)
		);

		update_post_meta( $coupon_code_post_id, 'discount_type', $discount_type );
		update_post_meta( $coupon_code_post_id, 'coupon_amount', $discount_amount );

		if( !get_field("purchased-required", $prize_id) ) {
			update_post_meta( $coupon_code_post_id, 'minimum_amount', "24,95" );
		}

		/*if( !get_field("add-participation-prize", $prize_id) ){
			update_post_meta( $coupon_code_post_id, 'individual_use', 'yes' );
		}*/

		update_post_meta( $coupon_code_post_id, 'usage_limit', 1 );
		update_post_meta( $coupon_code_post_id, 'usage_limit_per_user', 1 );
		update_post_meta( $coupon_code_post_id, 'customer_email', $_POST['input_7'] );
		update_post_meta( $coupon_code_post_id, 'date_expires', strtotime('1 January 2024') );

	}
}

add_action( 'gform_after_submission_40', 'send_game_participation_email', 10, 2 );
function send_game_participation_email( $entry, $form ) {

	$to = rgar( $entry, '7' );
	$prize_id = rgar( $entry, '9' );
	$name = rgar( $entry, '5' );

	$promo_code = rgar( $entry, '11' );


	if( get_field("add-participation-prize", $prize_id) ){
		$promo_code .= "<br>" . rgar( $entry, '12' );
	}


	$subject = pll__("Jeu de noël - Ton livre ton histoire");

	if( $prize_id == get_field("participation-prize-fr", "option")->ID ){

	    $body = str_replace(['[prix]', '[prenom]'], ['<strong>' . strtolower(get_the_title($prize_id)) . '</strong>', $name], get_field('christmas-game-participation-email-fr', 'option'));

	}else if( get_field("add-participation-prize", $prize_id) ){

	    $body = str_replace(['[prix]', '[prenom]', '[url]'], ['<strong>' . strtolower(get_the_title($prize_id)) . '</strong>', $name, get_permalink(get_field("gift-product",$prize_id)[0]->ID)], get_field('christmas-game-winner-2-promo-codes-email-fr', 'option'));

	}else{
		$body = str_replace(['[prix]', '[prenom]'], ['<strong>' . strtolower(get_the_title($prize_id)) . '</strong>', $name], get_field('christmas-game-winner-email-fr', 'option'));
	}

	$btn_text = pll__('Voir le site');
	$template_file = 'generic.php';

	// Template markup
	ob_start();
	include THEME_PATH . "/templates/email/{$template_file}";
	$markup = ob_get_clean();
	$replyto = "noreply@tonlivretonhistoire.ca";
	$sender_name = "Ton livre ton histoire";
	$headers = "From: {$sender_name} <{$replyto}>\r\n";
	$headers .= "Reply-To: {$replyto}\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
	$sent = wp_mail($to, $subject, $markup, $headers);

	return $sent;
}


/*
 * Create promo code after gform submission
 */
add_action('gform_pre_submission', 'generate_promo_code_from_gform');
function generate_promo_code_from_gform($form)
{
	$promo_code_form_id = get_field('promo-code-form-id');
	if( $form['id'] != $promo_code_form_id ) return;

	$end_of_promo_code = 'livre10';

	$promo_code = FW::random_string() . '-' . $end_of_promo_code;

	$_POST['input_4'] = $promo_code;

	$coupon_code_post_id = wp_insert_post(
		array(
			'post_title' => $promo_code,
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type' => 'shop_coupon'
		)
	);

	update_post_meta( $coupon_code_post_id, 'discount_type', 'percent' );
	update_post_meta( $coupon_code_post_id, 'coupon_amount', 10 );
	update_post_meta( $coupon_code_post_id, 'individual_use', 'yes' );
	update_post_meta( $coupon_code_post_id, 'usage_limit', 1 );
	update_post_meta( $coupon_code_post_id, 'usage_limit_per_user', 1 );
	update_post_meta( $coupon_code_post_id, 'customer_email', $_POST['input_3'] );
	update_post_meta( $coupon_code_post_id, 'date_expires', strtotime('+1 day') );
}

/*
 * Auto uncheck "Ship to a different address"
 */
add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );

/*
 * Hide production variatins price range
 */
add_filter( 'woocommerce_variable_sale_price_html', 'wpglorify_variation_price_format', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wpglorify_variation_price_format', 10, 2 );

function wpglorify_variation_price_format( $price, $product )
{
	if( isset($_GET['attribute_pa_choix-de-couverture']) && ! empty($_GET['attribute_pa_choix-de-couverture']) ){
		$variation_id = find_matching_product_variation_id( get_the_ID(), array('attribute_pa_choix-de-couverture' => $_GET['attribute_pa_choix-de-couverture']) );
		$variable_product = wc_get_product($variation_id);
		$price = $variable_product->get_price();

		return wc_price($price);
	}
	

	return $price;
}

/*
 * Fixes accents in emails
 */
function format_email_text($str)
{
	//$str = htmlentities($str);
	$str = str_replace(['&lt;', '&gt;'], ['<', '>'], $str);


	return $str;
}

add_action('template_redirect', function() {
    if (function_exists('pll_get_post_language')) {
        $current_language = pll_current_language();
        if ($current_language === 'en') {
            $redirect_url = site_url('/');
            wp_redirect($redirect_url, 301);
            exit;
        }
    }
});

add_filter( 'single_product_archive_thumbnail_size', function( $size ) {
    return 'full'; // ou 'large' ou une taille custom déclarée (voir méthode 2)
});
