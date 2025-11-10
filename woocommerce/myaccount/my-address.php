<?php
/**
 * My Addresses – Custom Merged Version
 *
 * This template is a merged version of the WooCommerce My Addresses template,
 * based on the updated version (v9.3.0) and incorporating legacy modifications
 * from version (v2.6.0). It displays your billing (and shipping) addresses using
 * a responsive grid layout.
 *
 * The following addresses will be used on the checkout page by default.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
    $get_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array(
            'billing'  => __( 'Billing address', 'woocommerce' ),
            'shipping' => __( 'Shipping address', 'woocommerce' ),
        ),
        $customer_id
    );
} else {
    $get_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array(
            'billing' => __( 'Billing address', 'woocommerce' ),
        ),
        $customer_id
    );
}
?>

<p>
    <?php
    echo apply_filters(
        'woocommerce_my_account_my_address_description',
        esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' )
    );
    ?>
</p>

<div class="woocommerce-Addresses row addresses">
    <?php foreach ( $get_addresses as $name => $address_title ) :
        $address = wc_get_account_formatted_address( $name );
        // Determine the column class: use two columns if both billing and shipping exist.
        $column_class = ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) ? 'col-12 col-sm-6' : 'col-12';
        ?>
        <div class="<?php echo esc_attr( $column_class ); ?> woocommerce-Address">
            <header class="woocommerce-Address-title title">
                <h3><?php echo esc_html( $address_title ); ?></h3>
                <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit">
                    <?php
                    /* translators: %s: Address title */
                    printf(
                        $address ? esc_html__( 'Edit %s', 'woocommerce' ) : esc_html__( 'Add %s', 'woocommerce' ),
                        esc_html( $address_title )
                    );
                    ?>
                </a>
            </header>
            <address>
                <?php
                echo $address ? wp_kses_post( $address ) : esc_html__( 'You have not set up this type of address yet.', 'woocommerce' );
                /**
                 * Used to output content after core address fields.
                 *
                 * @param string $name Address type.
                 * @since 8.7.0
                 */
                do_action( 'woocommerce_my_account_after_my_address', $name );
                ?>
            </address>
        </div>
    <?php endforeach; ?>
</div>
