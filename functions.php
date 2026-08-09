<?php
/*
 * Load the framework
 */

define('THEME_URL', get_template_directory_uri());
define('THEME_PATH', get_stylesheet_directory());

// Load the framework
require_once THEME_PATH . '/inc/core/framework.class.php';

// Init and override
$tlth = new TLTH(
	array(
		'queue'			=> ['aos', 'chosen', 'fancybox', 'cookie', 'scrollMagic', 'scrollMagicDebug'],
		'js_files'		=> ['lib/turn.min.js'],
		'options_pages' => ['General', 'Boutique'],
	)
);

// Core
require_once THEME_PATH . '/inc/core/theme-setup.php';
require_once THEME_PATH . '/inc/core/helpers.php';

// Setup
require_once THEME_PATH . '/inc/setup/assets.php';
require_once THEME_PATH . '/inc/setup/navigation.php';
require_once THEME_PATH . '/inc/setup/post-types.php';

// Integrations
require_once THEME_PATH . '/inc/integrations/third-party.php';
require_once THEME_PATH . '/inc/integrations/acf-config.php';
require_once THEME_PATH . '/inc/integrations/woocommerce.php';
require_once THEME_PATH . '/inc/integrations/book-personalization-wizard.php';
require_once THEME_PATH . '/inc/integrations/ajax.php';
require_once THEME_PATH . '/inc/integrations/translations.php';

/*
 * Enqueue our admin CSS
 */
function book_maker_css($hook)
{
	wp_enqueue_script('book_maker', THEME_URL . '/dist/js/admin.js');
}
add_action('admin_enqueue_scripts', 'book_maker_css');

/*
 * Require secondary files
 */
require_once('inc/features/book/book-maker.php');
require_once('inc/features/book/vendor/dompdf/autoload.inc.php');
use Dompdf\Dompdf;

/*
 * Suppress WordPress 6.7+ plugin translation warnings EARLY
 * Must be set before plugins load their text domains
 */
add_filter('doing_it_wrong_trigger_error', function($trigger, $function, $message) {
	// Suppress translation loading warnings for plugins
	if ($function === '_load_textdomain_just_in_time' && 
		(strpos($message, 'affiliate-wp') !== false ||
		 strpos($message, 'rp_wcdpd') !== false ||
		 strpos($message, 'woocommerce') !== false ||
		 strpos($message, 'yith-woocommerce-gift-cards') !== false ||
		 strpos($message, 'polylang') !== false ||
		 strpos($message, 'acf') !== false)) {
		return false;
	}
	return $trigger;
}, 1, 3); // Priority 1 to run early

/*
 * Fix Polylang comment count queries (WordPress 6.7.0+ compatibility)
 * Polylang incorrectly references the main posts table instead of the JOIN alias
 */
add_filter('comments_clauses', 'fix_polylang_comment_queries', 20, 2);
function fix_polylang_comment_queries($clauses, $query) {
	global $wpdb;
	
	// Check if Polylang modified the query
	if (!isset($clauses['join']) || strpos($clauses['join'], 'pll_tr') === false) {
		return $clauses;
	}
	
	// Find the correct table alias from the LEFT JOIN
	// Pattern: LEFT JOIN tlth_posts AS some_alias ON comment_post_ID = some_alias.ID
	$posts_table = $wpdb->posts; // e.g., tlth_posts
	$pattern = '/LEFT\s+JOIN\s+' . preg_quote($posts_table, '/') . '\s+AS\s+(\w+)\s+ON\s+comment_post_ID\s*=\s*\1\.ID/i';
	
	if (preg_match($pattern, $clauses['join'], $matches)) {
		$correct_alias = $matches[1];
		
		// Replace all instances of tlth_posts.ID with correct_alias.ID in the entire join clause
		$clauses['join'] = str_replace($posts_table . '.ID', $correct_alias . '.ID', $clauses['join']);
	}
	
	return $clauses;
}

/*
 * Adding tweaks to menu items
 */
add_filter('wp_nav_menu_objects', 'tweaking_menu_items', 10, 2);
function tweaking_menu_items($items, $args)
{
	foreach ($items as &$item) {
		$highlight = get_field('highlight-link', $item);
		$is_btn = get_field('is-btn', $item);
		$lang_btn = get_field('lang-btn', $item);

		if ($highlight) {
			$item->classes[] = 'highlight-link';
		}

		if ($is_btn) {
			$item->classes[] = 'is-btn';
		}

		if ($lang_btn) {
			$item->classes[] = 'lang-switcher';
		}
	}

	return $items;
}

/*
 * Add AffilaiteWP to Woo account page
 */

function moh_add_aff_wp_endpoint()
{
	add_rewrite_endpoint('aff', EP_ROOT | EP_PAGES);
}
add_action('init', 'moh_add_aff_wp_endpoint');

function moh_add_aff_wp_link_my_account($items)
{
	if (function_exists('affwp_is_affiliate') && affwp_is_affiliate()) {
		$logout = array_pop($items);
		$items['aff'] = __('Affiliate Area', 'affiliate-wp');
		$items['customer-logout'] = $logout;
	}
	return $items;
}
add_filter('woocommerce_account_menu_items', 'moh_add_aff_wp_link_my_account');


function moh_aff_wp_content()
{
	if (! class_exists('Affiliate_WP_Shortcodes')) {
		return;
	}
	$shortcode = new Affiliate_WP_Shortcodes;
	echo $shortcode->affiliate_area(null);
}
add_action('woocommerce_account_aff_endpoint', 'moh_aff_wp_content');


function moh_filter_aff_tabs($url, $page_id, $tab)
{
	return esc_url_raw(add_query_arg('tab', $tab));
}
add_filter('affwp_affiliate_area_page_url', 'moh_filter_aff_tabs', 10, 3);

/*
 * Create cookie on newsletter form submission
 */
add_action('gform_after_submission_14', 'set_cookie_after_newsletter_form');
function set_cookie_after_newsletter_form()
{
	setcookie('show-newsletter-badge', 'false', strtotime('+1 year'), '/');
}

add_action('woocommerce_product_query', 'tlth_shop_deprioritize_product_categories_query', 20, 2);
function tlth_shop_deprioritize_product_categories_query($q, $_wc_query = null)
{
	if (! class_exists('TLTH')) {
		return;
	}

	$deprioritized = TLTH::get_shop_deprioritized_product_category_ids();

	if (empty($deprioritized)) {
		return;
	}

	if (tlth_shop_request_filters_deprioritized_category($deprioritized)) {
		return;
	}

	$q->set('tlth_deprioritized_cat_ids', $deprioritized);
	add_filter('posts_clauses', 'tlth_shop_deprioritize_product_categories_clauses', 20, 2);
}

function tlth_shop_request_filters_deprioritized_category(array $deprioritized_ids)
{
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter state from URL.
	if (! isset($_REQUEST['product_cat'])) {
		return false;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$raw = wp_unslash($_REQUEST['product_cat']);

	if (is_array($raw)) {
		$slugs = $raw;
	} else {
		$slugs = preg_split('/\s*,\s*/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY);
	}

	$slugs = array_values(array_filter(array_map('sanitize_title', $slugs)));

	if (empty($slugs)) {
		return false;
	}

	$lookup = array_flip(array_map('intval', $deprioritized_ids));

	foreach ($slugs as $slug) {
		$term = get_term_by('slug', $slug, 'product_cat');

		if ($term && ! is_wp_error($term) && isset($lookup[(int) $term->term_id])) {
			return true;
		}
	}

	return false;
}

function tlth_shop_deprioritize_product_categories_clauses($clauses, $query)
{
	$deprioritized = $query->get('tlth_deprioritized_cat_ids');

	if (empty($deprioritized) || 'product_query' !== $query->get('wc_query')) {
		return $clauses;
	}

	remove_filter('posts_clauses', 'tlth_shop_deprioritize_product_categories_clauses', 20);

	global $wpdb;
	$ids_list = implode(',', array_map('intval', $deprioritized));

	$clauses['join'] .= " LEFT JOIN (
		SELECT DISTINCT tr.object_id
		FROM {$wpdb->term_relationships} tr
		INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
		WHERE tt.taxonomy = 'product_cat' AND tt.term_id IN ({$ids_list})
	) AS tlth_deprioritized_products ON {$wpdb->posts}.ID = tlth_deprioritized_products.object_id ";

	$orderby = trim((string) $clauses['orderby']);

	if ('' === $orderby) {
		$orderby = "{$wpdb->posts}.menu_order ASC, {$wpdb->posts}.post_date DESC";
	}

	$clauses['orderby'] = "CASE WHEN tlth_deprioritized_products.object_id IS NULL THEN 0 ELSE 1 END ASC, {$orderby}";

	return $clauses;
}

/**
 * WP_Query::parse_tax_query() calls wp_basename() on hierarchical taxonomy query vars before
 * it coerces array values to strings. product_cat[]=slug therefore passes an array into
 * wp_basename() and fatals on PHP 8+. Flatten to a comma-separated slug list (core handles that path).
 */
add_filter('request', 'tlth_flatten_hierarchical_product_tax_request_arrays', 0);
function tlth_flatten_hierarchical_product_tax_request_arrays($query_vars)
{
	if (! is_array($query_vars)) {
		return $query_vars;
	}

	foreach (get_taxonomies(array('object_type' => array('product')), 'objects') as $tax_obj) {
		if (empty($tax_obj->query_var) || empty($tax_obj->rewrite['hierarchical'])) {
			continue;
		}

		$key = $tax_obj->query_var;

		if (! isset($query_vars[$key]) || ! is_array($query_vars[$key])) {
			continue;
		}

		$slugs = array_values(array_filter(array_map('sanitize_title', $query_vars[$key])));

		if (empty($slugs)) {
			unset($query_vars[$key]);
		} else {
			$query_vars[$key] = implode(',', $slugs);
		}
	}

	return $query_vars;
}

/**
 * Canonical shop URLs normally omit post_type=product; when it is appended (legacy JS /
 * AJAX) it can collide with shop-as-page routing and fatal the request.
 */
add_action('template_redirect', 'tlth_strip_post_type_on_shop_catalog', 0);
function tlth_strip_post_type_on_shop_catalog()
{
	if (is_admin() || ! function_exists('is_shop') || ! function_exists('remove_query_arg')) {
		return;
	}

	if (! is_shop()) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$s = isset($_GET['s']) ? (string) wp_unslash($_GET['s']) : '';

	if ('' !== trim($s)) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if (! isset($_GET['post_type']) || 'product' !== $_GET['post_type']) {
		return;
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';

	if ('' === $request_uri || '/' === $request_uri) {
		return;
	}

	// Strip only post_type — keep taxonomy / pagination query args intact.
	$redirect_path = remove_query_arg('post_type', $request_uri);

	if ($redirect_path === $request_uri) {
		return;
	}

	wp_safe_redirect(home_url($redirect_path), 301);
	exit;
}

/**
 * Temporarily redirect public blog routes without affecting WooCommerce archives.
 */
add_action('template_redirect', 'tlth_redirect_blog_routes_to_home', 1);
function tlth_redirect_blog_routes_to_home()
{
	$is_blog_taxonomy = false;

	if (is_tax()) {
		$term     = get_queried_object();
		$taxonomy = isset($term->taxonomy) ? get_taxonomy($term->taxonomy) : false;

		$is_blog_taxonomy = $taxonomy && in_array('post', $taxonomy->object_type, true);
	}

	if (
		is_singular('post')
		|| (is_home() && ! is_front_page())
		|| is_category()
		|| is_tag()
		|| is_author()
		|| is_date()
		|| $is_blog_taxonomy
	) {
		wp_safe_redirect(home_url('/'), 302);
		exit;
	}
}

/*
 * Only show cheque to school and daycares
 */
add_filter('woocommerce_available_payment_gateways', 'filter_gateways', 1);
function filter_gateways($gateways)
{
	if (! is_user_logged_in() || ! in_array(CURRENT_USER_ROLE, ['school', 'daycare', 'administrator'])) {
		unset($gateways['cheque']);
	}

	return $gateways;
}

add_action('template_redirect', 'define_default_payment_gateway');
function define_default_payment_gateway()
{
	if (is_checkout() && ! is_wc_endpoint_url()) {
		$default_payment_id = 'stripe';

		WC()->session->set('chosen_payment_method', $default_payment_id);
	}
}

/*
 * WooCommerce AJAX update header cart count
 */
add_filter('woocommerce_add_to_cart_fragments', 'wc_refresh_mini_cart_count');
function wc_refresh_mini_cart_count($fragments)
{
	ob_start();
	?>
	<span id="cart-count" class="cart-count"><?= WC()->cart->get_cart_contents_count(); ?></span>
	<?php
	$fragments['#cart-count'] = ob_get_clean();

	ob_start();
	?>
	<span id="cart-total" class="total"><?= wc_price(WC()->cart->cart_contents_total); ?></span>
<?php
	$fragments['#cart-total'] = ob_get_clean();

	return $fragments;
}


/**
 * Gift-card threshold progress bar (theme-styled; replaces native <meter>).
 */
function tlth_shop_meter_html($current, $goal)
{
	$percent = $goal > 0 ? min(100, ($current / $goal) * 100) : 0;
	$percent_attr = esc_attr((string) round($percent, 1));

	return sprintf(
		'<div id="shop-meter" class="shop-meter" role="progressbar" aria-valuenow="%1$s" aria-valuemin="0" aria-valuemax="100"><div class="shop-meter__track"><div class="shop-meter__fill" style="width:%1$s%%"></div></div></div>',
		$percent_attr
	);
}

/**
 * Whether gift-card threshold logic should run (cart page + cart form POST/AJAX, not checkout).
 *
 * Cart updates via AJAX POST to the cart URL call calculate_totals() on wp_loaded, before the
 * main query runs, so is_cart() is false there. WOOCOMMERCE_CART is defined during that flow.
 */
function tlth_should_run_cart_gift_card_logic()
{
	if (is_admin() && ! wp_doing_ajax()) {
		return false;
	}

	if (wp_doing_ajax() && ! empty($_REQUEST['wc-ajax'])) {
		$wc_ajax = wc_clean(wp_unslash($_REQUEST['wc-ajax']));
		if (in_array($wc_ajax, array('update_order_review', 'checkout'), true)) {
			return false;
		}
	}

	if (is_checkout()) {
		return false;
	}

	if (is_cart()) {
		return true;
	}

	return defined('WOOCOMMERCE_CART') && WOOCOMMERCE_CART;
}

/*
 * NEW - Dynamically add gift cards to cart depending on cart subtotal - NEW - 1 gift card, with meter and dynamic notice
*/
add_action('woocommerce_before_calculate_totals', 'product_specials_on_books', 10, 1);
function product_specials_on_books($cart)
{
	if (! tlth_should_run_cart_gift_card_logic()) {
		return;
	}

	if (did_action('woocommerce_before_calculate_totals') >= 2) {
		return;
	}

	if (! get_field('shop-gift-card-75', 'option')) {
		return;
	}

	if (! get_field('shop-gift-card-value', 'option')) {
		return;
	}

	$shop_value       = (float) get_field('shop-gift-card-value', 'option');
	$threshold_amount = $shop_value;
	$product_id       = (int) get_field('shop-gift-card-75-' . LANG, 'option');
	$cart_items_total = 0;
	$free_item_key    = null;

	foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
		if ((int) $cart_item['data']->get_id() === $product_id) {
			$free_item_key = $cart_item_key;
			continue;
		}

		$cart_items_total += (float) $cart_item['data']->get_price() * (int) $cart_item['quantity'];
	}

	if ($cart_items_total < $threshold_amount) {
		$needed_value = $shop_value - $cart_items_total;
		$meter        = tlth_shop_meter_html($cart_items_total, $shop_value);

		wc_print_notice(
			sprintf(pll__('Ajoutez %s à votre commande pout obtenir une carte cadeau de 25$'), $needed_value) . $meter,
			'meter'
		);
	}

	if ($cart_items_total >= $threshold_amount) {
		if (null === $free_item_key) {
			$cart->add_to_cart($product_id);
			wc_print_notice(pll__('giftcard-75'), 'success');
		}
	} elseif (null !== $free_item_key) {
		$cart->remove_cart_item($free_item_key);
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

function weighted_random($values, $weights)
{
	$count = count($values);
	$i = 0;
	$n = 0;
	$num = mt_rand(0, array_sum($weights));
	while ($i < $count) {
		$n += $weights[$i];
		if ($n >= $num) {
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
	$promo_code =  $end_of_promo_code . '-' . TLTH::random_string();
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

	update_post_meta($coupon_code_post_id, 'discount_type', $discount_type);
	update_post_meta($coupon_code_post_id, 'coupon_amount', $discount_amount);

	if (!get_field("purchased-required", $prize_id)) {
		update_post_meta($coupon_code_post_id, 'minimum_amount', "24,95");
	}

	if (!get_field("add-participation-prize", $prize_id)) {
		update_post_meta($coupon_code_post_id, 'individual_use', 'yes');
	}

	update_post_meta($coupon_code_post_id, 'usage_limit', 1);
	update_post_meta($coupon_code_post_id, 'usage_limit_per_user', 1);
	update_post_meta($coupon_code_post_id, 'customer_email', $_POST['input_7']);
	update_post_meta($coupon_code_post_id, 'date_expires', strtotime('1 January 2024'));

	if ($discount_type == "fixed_cart") {
		foreach (get_field("gift-product", $prize_id) as $product) {
			$products_ids[] = $product->ID;
		}

		update_post_meta($coupon_code_post_id, 'product_ids', implode(",", $products_ids));
	}

	if (get_field("add-participation-prize", $prize_id)) {

		$prize_id = get_field("participation-prize-" . LANG, "option")->ID;
		$discount_type = get_field("prize-value-type", $prize_id);
		$discount_amount = get_field("prize-value", $prize_id);
		$products_ids = [];

		$promo_code =  $end_of_promo_code . '-' . TLTH::random_string();
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

		update_post_meta($coupon_code_post_id, 'discount_type', $discount_type);
		update_post_meta($coupon_code_post_id, 'coupon_amount', $discount_amount);

		if (!get_field("purchased-required", $prize_id)) {
			update_post_meta($coupon_code_post_id, 'minimum_amount', "24,95");
		}

		/*if( !get_field("add-participation-prize", $prize_id) ){
			update_post_meta( $coupon_code_post_id, 'individual_use', 'yes' );
		}*/

		update_post_meta($coupon_code_post_id, 'usage_limit', 1);
		update_post_meta($coupon_code_post_id, 'usage_limit_per_user', 1);
		update_post_meta($coupon_code_post_id, 'customer_email', $_POST['input_7']);
		update_post_meta($coupon_code_post_id, 'date_expires', strtotime('1 January 2024'));
	}
}

add_action('gform_after_submission_40', 'send_game_participation_email', 10, 2);
function send_game_participation_email($entry, $form)
{

	$to = rgar($entry, '7');
	$prize_id = rgar($entry, '9');
	$name = rgar($entry, '5');

	$promo_code = rgar($entry, '11');


	if (get_field("add-participation-prize", $prize_id)) {
		$promo_code .= "<br>" . rgar($entry, '12');
	}


	$subject = pll__("Jeu de noël - Ton livre ton histoire");

	if ($prize_id == get_field("participation-prize-fr", "option")->ID) {

		$body = str_replace(['[prix]', '[prenom]'], ['<strong>' . strtolower(get_the_title($prize_id)) . '</strong>', $name], get_field('christmas-game-participation-email-fr', 'option'));
	} else if (get_field("add-participation-prize", $prize_id)) {

		$body = str_replace(['[prix]', '[prenom]', '[url]'], ['<strong>' . strtolower(get_the_title($prize_id)) . '</strong>', $name, get_permalink(get_field("gift-product", $prize_id)[0]->ID)], get_field('christmas-game-winner-2-promo-codes-email-fr', 'option'));
	} else {
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
	if ($form['id'] != $promo_code_form_id) return;

	$end_of_promo_code = 'livre10';

	$promo_code = TLTH::random_string() . '-' . $end_of_promo_code;

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

	update_post_meta($coupon_code_post_id, 'discount_type', 'percent');
	update_post_meta($coupon_code_post_id, 'coupon_amount', 10);
	update_post_meta($coupon_code_post_id, 'individual_use', 'yes');
	update_post_meta($coupon_code_post_id, 'usage_limit', 1);
	update_post_meta($coupon_code_post_id, 'usage_limit_per_user', 1);
	update_post_meta($coupon_code_post_id, 'customer_email', $_POST['input_3']);
	update_post_meta($coupon_code_post_id, 'date_expires', strtotime('+1 day'));
}

/*
 * Auto uncheck "Ship to a different address"
 */
add_filter('woocommerce_ship_to_different_address_checked', '__return_false');

/*
 * Hide production variatins price range
 */
add_filter('woocommerce_variable_sale_price_html', 'wpglorify_variation_price_format', 10, 2);
add_filter('woocommerce_variable_price_html', 'wpglorify_variation_price_format', 10, 2);

function wpglorify_variation_price_format($price, $product)
{
	if (
		$product
		&& isset($_GET['attribute_pa_choix-de-couverture'])
		&& $_GET['attribute_pa_choix-de-couverture'] !== ''
	) {
		$variation_id = find_matching_product_variation_id(
			$product->get_id(),
			array('attribute_pa_choix-de-couverture' => sanitize_title(wp_unslash($_GET['attribute_pa_choix-de-couverture'])))
		);
		$variable_product = $variation_id ? wc_get_product($variation_id) : false;

		if ($variable_product) {
			return wc_price($variable_product->get_price());
		}
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

/**
 * Redirect English pages and products to their main-language equivalents.
 */
add_action('template_redirect', 'tlth_redirect_english_content_to_default_language');
function tlth_redirect_english_content_to_default_language()
{
	if (
		! function_exists('pll_current_language')
		|| ! function_exists('pll_default_language')
		|| ! function_exists('pll_get_post')
		|| 'en' !== pll_current_language('slug')
		|| ! is_singular(array('page', 'product'))
	) {
		return;
	}

	$default_language = pll_default_language('slug');
	$translated_id    = pll_get_post(get_queried_object_id(), $default_language);
	$redirect_url     = $translated_id ? get_permalink($translated_id) : home_url('/');

	wp_safe_redirect($redirect_url, 302);
	exit;
}

add_filter('single_product_archive_thumbnail_size', function ($size) {
	return 'full'; // ou 'large' ou une taille custom déclarée (voir méthode 2)
});
