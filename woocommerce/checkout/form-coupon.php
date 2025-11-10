<?php
/**
 * Checkout Coupon Form – Custom Updated
 *
 * This template is a merged version of the WooCommerce checkout coupon form template,
 * based on the official version (v7.0.1) and incorporating legacy modifications
 * from version (v3.4.4).
 *
 * Customizations include:
 * - A check to ensure the user is logged in (legacy customization).
 * - A screen-reader label for the coupon input field.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-coupon.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

// Legacy: Require user to be logged in before showing the coupon form.
// Remove or comment out the line below if you wish to allow guests to use coupons.
if ( ! is_user_logged_in() ) {
    return;
}

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
    return;
}
?>
<div class="woocommerce-form-coupon-toggle">
    <?php
    echo wc_print_notice(
        apply_filters(
            'woocommerce_checkout_coupon_message',
            esc_html__( 'Have a coupon?', 'woocommerce' ) . ' <a href="#" class="showcoupon">' . esc_html__( 'Click here to enter your code', 'woocommerce' ) . '</a>'
        ),
        'notice'
    );
    ?>
</div>

<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none">

    <p><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'woocommerce' ); ?></p>

    <p class="form-row form-row-first">
        <label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
        <input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" />
    </p>

    <p class="form-row form-row-last">
        <button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>">
            <?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?>
        </button>
    </p>

    <div class="clear"></div>
</form>
