<?
/*
 * Setup WooCommerce
 */
function mytheme_add_woocommerce_support()
{
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 800,
			'single_image_width'    => 1000,
			'product_grid' => array(
				'default_rows'    => 3,
				'min_rows'        => 24,
				'max_rows'        => 24,

				'default_columns' => 1,
				'min_columns'     => 1,
				'max_columns'     => 1,
			),
		)
	);

	register_sidebar(
		array(
			'name' => 'Boutique',
			'id' => 'shop',
			'description'   => 'Sidebar à afficher à la boutique',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h2 class="widgettitle">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );

add_theme_support('wc-product-gallery-zoom');
add_theme_support('wc-product-gallery-lightbox');
add_theme_support('wc-product-gallery-slider');

/*
 * Generate back to show button
 */
function woocommerce_back_to_shop_url()
{
	echo '<div class="back-to-shop"><a href="' . get_permalink( wc_get_page_id( 'shop' ) ) . '" class="text-link red">' . pll__('back-to-shop') . '</a></div>';
}

/*
 * Generatre flipbook markup
 */
function get_product_preview_pdf_btn()
{
	if( ! get_field('preview-gallery') ) return;

	ob_start();
	include(THEME_PATH . '/woocommerce/single-product/preview-flipper.php');

	echo ob_get_clean();
}

/*
 * Add user roles
 */
function xx__update_custom_roles()
{
	if ( get_option( 'custom_roles_version' ) <= 1 ) {
		add_role( 'school', 'École', ['read' => true, 'level_0' => true]);
		add_role( 'daycare', 'Milieu de garde', ['read' => true, 'level_0' => true]);
		update_option( 'custom_roles_version', 1 );
	}
}
add_action( 'init', 'xx__update_custom_roles' );

/*
 * Email as username
 */
function my_new_customer_username( $username, $email, $new_user_args, $suffix ) {
	return $email;
}
add_filter( 'woocommerce_new_customer_username', 'my_new_customer_username', 10, 4 );

/*
 * If user is daycare, apply rebates
 */
/*
function applydaycare_rebate_to_cart_logic( $attribute_slug_term  )
{
	if( ! is_user_logged_in() || CURRENT_USER_ROLE != 'daycare' ){
		return;
	}

	global $woocommerce;

	if( WC()->cart->is_empty() ) {
		//return $found; // Exit
	} else {
		// Loop through cart items
		foreach ( WC()->cart->get_cart() as $cart_item ){

			if( $cart_item['variation_id'] > 0 ){

				// Loop through product attributes values set for the variation
				foreach( $cart_item['variation'] as $term_slug ){

					// comparing attribute term value with current attribute value
					if ( $term_slug === $attribute_slug_term ) {
						$product_id = $cart_item['product_id'];

						$var_id = $cart_item['variation_id'];
						$variation_data = new WC_Product_Variation($var_id);

						$original_price = $variation_data->get_price();
						$new_price = round($original_price * .75, 2);

						$cart_item['data']->set_price($new_price);

						wc_print_notice( pll__('weve-applied-your-daycare-pricing') );
					}
				}
			}
		}
	}
}

add_action('woocommerce_before_calculate_totals', 'applydaycare_rebate_to_cart');
function applydaycare_rebate_to_cart($cart)
{
	applydaycare_rebate_to_cart_logic('couverture-souple');
}
*/