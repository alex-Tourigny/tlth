<?php
/**
 * My Addresses
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

<p class="text-[15px] text-muted-blue mb-6">
    <?php
    echo apply_filters(
        'woocommerce_my_account_my_address_description',
        esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' )
    );
    ?>
</p>

<div class="woocommerce-Addresses grid grid-cols-1 <?php echo ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) ? 'md:grid-cols-2' : ''; ?> gap-6">
    <?php foreach ( $get_addresses as $name => $address_title ) :
        $address = wc_get_account_formatted_address( $name );
        ?>
        <div class="woocommerce-Address bg-white rounded-2xl p-6 shadow-soft">
            <header class="woocommerce-Address-title flex flex-col gap-2 mb-4 pb-4 border-b border-muted-blue/10">
                <h3 class="text-lg font-medium text-deep-blue"><?php echo esc_html( $address_title ); ?></h3>
                <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="btn btn-primary text-sm !px-4 !py-1.5">
                    <?php
                    printf(
                        $address ? esc_html__( 'Edit %s', 'woocommerce' ) : esc_html__( 'Add %s', 'woocommerce' ),
                        esc_html( $address_title )
                    );
                    ?>
                </a>
            </header>
            <address class="not-italic text-[15px] text-deep-blue/80 leading-relaxed">
                <?php
                echo $address ? wp_kses_post( $address ) : '<span class="text-muted-blue italic">' . esc_html__( 'You have not set up this type of address yet.', 'woocommerce' ) . '</span>';
                do_action( 'woocommerce_my_account_after_my_address', $name );
                ?>
            </address>
        </div>
    <?php endforeach; ?>
</div>
