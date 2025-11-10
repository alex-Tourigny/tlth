<?php
/**
 * Order Customer Details – Custom Merged Version
 *
 * This template is a merged version of the WooCommerce order customer details template,
 * combining the updated version (v8.7.0) with legacy modifications (v3.4.4).
 *
 * It displays the billing address (with phone and email) and, if applicable, the shipping
 * address using a responsive grid layout.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.7.0
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details">

    <?php if ( $show_shipping ) : ?>
    <section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses row">
        <div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-12 col-sm-6">
            <?php endif; ?>

            <h2 class="woocommerce-column__title"><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></h2>
            <address>
                <?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>

                <?php if ( $order->get_billing_phone() ) : ?>
                    <p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
                <?php endif; ?>

                <?php if ( $order->get_billing_email() ) : ?>
                    <p class="woocommerce-customer-details--email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
                <?php endif; ?>

                <?php
                /**
                 * Action hook fired after the billing address in the order customer details.
                 *
                 * @since 8.7.0
                 * @param string   $address_type 'billing'
                 * @param WC_Order $order        Order object.
                 */
                do_action( 'woocommerce_order_details_after_customer_address', 'billing', $order );
                ?>
            </address>

            <?php if ( $show_shipping ) : ?>
        </div><!-- /.col-1 -->

        <div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-12 col-sm-6">
            <h2 class="woocommerce-column__title"><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></h2>
            <address>
                <?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>

                <?php if ( $order->get_shipping_phone() ) : ?>
                    <p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_shipping_phone() ); ?></p>
                <?php endif; ?>

                <?php
                /**
                 * Action hook fired after the shipping address in the order customer details.
                 *
                 * @since 8.7.0
                 * @param string   $address_type 'shipping'
                 * @param WC_Order $order        Order object.
                 */
                do_action( 'woocommerce_order_details_after_customer_address', 'shipping', $order );
                ?>
            </address>
        </div><!-- /.col-2 -->
    </section><!-- /.col2-set -->
<?php endif; ?>

    <?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</section>
