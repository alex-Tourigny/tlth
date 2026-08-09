<?php
/**
 * Empty cart page
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="cart-empty-container min-h-[75dvh] pt-16 pb-32">
	<div class="tlth-cart-empty mx-auto flex w-full max-w-lg flex-col justify-center rounded-3xl border-2 border-muted-blue/15 bg-white px-6 py-10 text-center shadow-soft sm:px-10 sm:py-12">
		<div class="tlth-cart-empty__icon mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-beige text-deep-blue" aria-hidden="true">
			<?= file_get_contents(THEME_PATH . '/assets/images/icon-cart.svg'); ?>
		</div>
		<div class="tlth-cart-empty__message text-[15px] leading-relaxed text-deep-blue">
			<?php
			/**
			 * Fires on empty cart; includes {@see wc_empty_cart_message} at priority 10.
			 *
			 * @hooked wc_empty_cart_message - 10
			 */
			do_action( 'woocommerce_cart_is_empty' );
			?>
		</div>
		<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
			<p class="return-to-shop mb-0">
				<a class="btn btn-primary" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
					<?php echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'Return to shop', 'woocommerce' ) ) ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
</div>