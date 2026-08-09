<?php
/**
 * Checkout Form – Custom Updated
 *
 * This template is a merged version of the WooCommerce checkout form template,
 * based on the official version (v9.4.0) with legacy customizations from an
 * older version (v3.5.0) incorporated.
 *
 * Customizations include:
 * - Two-column layout: customer details left, order review sticky right (matches cart).
 * - Billing and shipping fields in a responsive grid.
 * - Inclusion of an aria-label attribute on the form element.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

	<div class="tlth-checkout-page pt-8 pb-24 lg:pb-40">
		<div class="tlth-checkout-layout grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_min(100%,24rem)] lg:gap-10 xl:grid-cols-[minmax(0,1fr)_min(100%,28rem)]">

			<div class="tlth-checkout-main min-w-0">
				<?php if ( $checkout->get_checkout_fields() ) : ?>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div id="customer_details" class="tlth-checkout-customer grid grid-cols-1 gap-8">
						<div class="tlth-checkout-billing min-w-0">
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
						</div>

						<div class="tlth-checkout-shipping min-w-0">
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>
						</div>
					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<?php endif; ?>
			</div>

			<div class="tlth-checkout-sidebar min-w-0">
				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

				<h3 id="order_review_heading" class="tlth-checkout-order-heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>

		</div>
	</div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
