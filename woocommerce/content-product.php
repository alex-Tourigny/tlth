<?php
/**
 * The template for displaying product content within loops – Custom Merged Version
 *
 * This template is a merged version of the WooCommerce content-product template,
 * based on the official version (v9.4.0) and incorporating legacy modifications
 * from version (v3.6.0). Customizations include:
 * - Removing default link open/close actions.
 * - Displaying product category badges via get_product_categories_badges().
 * - A custom grid layout using Bootstrap classes for the product image and content.
 * - Displaying the product excerpt.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Check if the product is a valid WooCommerce product and ensure its visibility before proceeding.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
    return;
}

// Remove default actions for wrapping product links, as our custom layout handles linking.
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

$product_id = $product->get_id();
$product_title = get_the_title( $product_id );
$product_link = get_permalink( $product_id );
$product_image = get_post_thumbnail_id( $product_id );
$price_html = $product->get_price_html();

$book_card_args = array(
	'href'       => $product_link,
	'title'      => $product_title,
	'image_id'   => $product_image,
	'price_html' => $price_html,
	'post_id'    => $product_id,
);
?>

<li <?php wc_product_class( 'book-card-wrapper', $product ); ?>>
	<?php include THEME_PATH . '/includes/book-card.php'; ?>
</li>
