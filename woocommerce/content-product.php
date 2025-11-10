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
?>

<li <?php wc_product_class( 'product-block', $product ); ?>>
    <?php
    // Display custom product category badges if the function exists.
    if ( function_exists( 'get_product_categories_badges' ) ) {
        get_product_categories_badges();
    }
    ?>

    <div class="product-meta">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-lg-6">
                <a href="<?php echo esc_url( get_permalink() ); ?>" class="single-product-img">
                    <?php
                    /**
                     * Hook: woocommerce_before_shop_loop_item_title.
                     *
                     * @hooked woocommerce_show_product_loop_sale_flash - 10
                     * @hooked woocommerce_template_loop_product_thumbnail - 10
                     */
                    do_action( 'woocommerce_before_shop_loop_item_title' );
                    ?>
                </a>
            </div>

            <div class="col-12 col-lg-6">
                <div class="single-product-content">
                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="product-title">
                        <?php
                        /**
                         * Hook: woocommerce_shop_loop_item_title.
                         *
                         * @hooked woocommerce_template_loop_product_title - 10
                         */
                        do_action( 'woocommerce_shop_loop_item_title' );
                        ?>
                    </a>

                    <?php
                    /**
                     * Hook: woocommerce_after_shop_loop_item_title.
                     *
                     * @hooked woocommerce_template_loop_rating - 5
                     * @hooked woocommerce_template_loop_price - 10
                     */
                    do_action( 'woocommerce_after_shop_loop_item_title' );
                    ?>

                    <div class="the-content product-desc">
                        <?php the_excerpt(); ?>
                    </div>

                    <?php
                    /**
                     * Hook: woocommerce_after_shop_loop_item.
                     *
                     * @hooked woocommerce_template_loop_add_to_cart - 10
                     */
                    do_action( 'woocommerce_after_shop_loop_item' );
                    ?>
                </div>
            </div>
        </div>
    </div>
</li>
