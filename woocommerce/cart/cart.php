<?php

/**
 * Cart Page – Custom Updated
 *
 * This template is a custom updated version of the WooCommerce Cart template,
 * based on the official template (v7.9.0) with legacy customizations merged in.
 *
 * Customizations include:
 * - A custom error pop-up for cart display issues.
 * - A Gravity Forms cart item permalink fix.
 * - An extra link ("ajouter-livre") in the cart actions.
 * - A free shipping message via an ACF field (shown when the cart qualifies for free delivery).
 * - Two-column layout: cart table left, totals right, cross-sells below.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined('ABSPATH') || exit;
?>

<?php
$tlth_free_shipping_msg = get_field('shop-free-shipping-msg', 'option');
if ($tlth_free_shipping_msg && tlth_cart_qualifies_for_free_shipping()) { ?>
	<div class="tlth-cart-shipping-notice woocommerce-info">
		<?php echo $tlth_free_shipping_msg; ?>
	</div>
<?php }

do_action('woocommerce_before_cart');
?>

<div class="tlth-cart-page pt-8 pb-24 lg:pb-40">

	<div class="tlth-cart-layout grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_min(100%,24rem)] lg:gap-10 xl:grid-cols-[minmax(0,1fr)_min(100%,28rem)]">

		<div class="tlth-cart-main min-w-0">
			<form class="woocommerce-cart-form min-w-0" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
				<?php do_action('woocommerce_before_cart_table'); ?>

				<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
					<thead>
						<tr>
							<th class="product-remove"><span class="screen-reader-text"><?php esc_html_e('Remove item', 'woocommerce'); ?></span></th>
							<th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
							<th class="product-quantity"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
							<th class="product-subtotal"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php do_action('woocommerce_before_cart_contents'); ?>

						<?php
						foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
							$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
							$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
							$product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

							if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
								$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
						?>
								<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
									<td class="product-remove">
										<?php
										echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											'woocommerce_cart_item_remove_link',
											sprintf(
												'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
												esc_url(wc_get_cart_remove_url($cart_item_key)),
												esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
												esc_attr($product_id),
												esc_attr($_product->get_sku())
											),
											$cart_item_key
										);
										?>
									</td>

									<td class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
										<?php
										$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
										?>
										<div class="tlth-cart-product flex items-start gap-4">
											<div class="tlth-cart-product__media shrink-0">
												<?php
												if (! $product_permalink) {
													echo $thumbnail; // PHPCS: XSS ok.
												} else {
													printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
												}
												?>
											</div>
											<div class="tlth-cart-product__details min-w-0 flex-1">
												<div class="tlth-cart-product__title">
													<?php
													if (! $product_permalink) {
														echo wp_kses_post($product_name);
													} else {
														// Fix EN cart item key is not added to permalink with WC functions
														if (! empty($cart_item['_gravity_form_data']) && ! strpos($product_permalink, $cart_item_key)) {
															$product_permalink .= "&wc_gforms_cart_item_key=" . $cart_item_key;
														}
														echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
													}
													?>
												</div>

												<?php
												do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

												// Meta data.
												echo wc_get_formatted_cart_item_data($cart_item);

												// Backorder notification.
												if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
													echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
												}
												?>

												<div class="tlth-cart-product__price">
													<?php
													echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
													?>
												</div>
											</div>
										</div>
									</td>

									<td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
										<?php
										if ($_product->is_sold_individually()) {
											$min_quantity = 1;
											$max_quantity = 1;
										} else {
											$min_quantity = 0;
											$max_quantity = $_product->get_max_purchase_quantity();
										}

										$product_quantity = woocommerce_quantity_input(
											array(
												'input_name'   => "cart[{$cart_item_key}][qty]",
												'input_value'  => $cart_item['quantity'],
												'max_value'    => $max_quantity,
												'min_value'    => $min_quantity,
												'product_name' => $product_name,
											),
											$_product,
											false
										);

										echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
										?>
									</td>

									<td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
										<?php
										echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
										?>
									</td>
								</tr>
						<?php
							}
						}
						?>

						<?php do_action('woocommerce_cart_contents'); ?>

						<tr>
							<td colspan="4" class="actions">
								<?php if (wc_coupons_enabled()) { ?>
									<div class="coupon">
										<label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Coupon:', 'woocommerce'); ?></label>
										<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" />
										<button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>">
											<?php esc_html_e('Apply coupon', 'woocommerce'); ?>
										</button>
										<?php do_action('woocommerce_cart_coupon'); ?>
									</div>
								<?php } ?>

								<div class="add-extra-product">
									<a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-primary"><?php echo pll__('ajouter-livre'); ?></a>
									<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
										<?php esc_html_e('Update cart', 'woocommerce'); ?>
									</button>
								</div>

								<?php do_action('woocommerce_cart_actions'); ?>

								<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
							</td>
						</tr>

						<?php do_action('woocommerce_after_cart_contents'); ?>
					</tbody>
				</table>
				<?php do_action('woocommerce_after_cart_table'); ?>
			</form>
		</div>

		<?php do_action('woocommerce_before_cart_collaterals'); ?>

		<aside class="cart-collaterals w-full min-w-0">
			<?php woocommerce_cart_totals(); ?>
		</aside>

	</div>

	<div class="tlth-cart-cross-sells">
		<?php woocommerce_cross_sell_display(3,3); ?>
	</div>
</div>

<?php do_action('woocommerce_after_cart'); ?>