<?php
/**
 * My Account Dashboard – Custom Themed Version
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);

$user = wp_get_current_user();
?>

<div class="wc-dashboard">
	<div class="bg-white rounded-2xl p-6 lg:p-8 shadow-soft mb-6">
		<p class="text-[15px] text-deep-blue">
			<?php
			printf(
				wp_kses( __( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'woocommerce' ), $allowed_html ),
				'<strong class="text-teal">' . esc_html( $user->display_name ) . '</strong>',
				esc_url( wc_logout_url() )
			);
			?>
		</p>
		<p class="text-[15px] text-muted-blue mt-2">
			<?php
			printf(
				wp_kses( __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' ), $allowed_html ),
				esc_url( wc_get_endpoint_url( 'orders' ) ),
				esc_url( wc_get_endpoint_url( 'edit-address' ) ),
				esc_url( wc_get_endpoint_url( 'edit-account' ) )
			);
			?>
		</p>
	</div>

	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>" class="group bg-white rounded-2xl p-6 shadow-soft hover:shadow-medium transition-all duration-300 no-underline">
			<div class="w-10 h-10 rounded-full bg-teal/10 flex items-center justify-center mb-3 group-hover:bg-teal/20 transition-colors duration-300">
				<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
			</div>
			<h3 class="text-base font-medium text-deep-blue group-hover:text-teal transition-colors duration-200"><?php esc_html_e( 'Orders', 'woocommerce' ); ?></h3>
			<p class="text-sm text-muted-blue mt-1"><?php esc_html_e( 'Voir vos commandes', 'tlth' ); ?></p>
		</a>

		<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>" class="group bg-white rounded-2xl p-6 shadow-soft hover:shadow-medium transition-all duration-300 no-underline">
			<div class="w-10 h-10 rounded-full bg-coral/10 flex items-center justify-center mb-3 group-hover:bg-coral/20 transition-colors duration-300">
				<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-coral" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
			</div>
			<h3 class="text-base font-medium text-deep-blue group-hover:text-coral transition-colors duration-200"><?php esc_html_e( 'Addresses', 'woocommerce' ); ?></h3>
			<p class="text-sm text-muted-blue mt-1"><?php esc_html_e( 'Gérer vos adresses', 'tlth' ); ?></p>
		</a>

		<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>" class="group bg-white rounded-2xl p-6 shadow-soft hover:shadow-medium transition-all duration-300 no-underline">
			<div class="w-10 h-10 rounded-full bg-gold/10 flex items-center justify-center mb-3 group-hover:bg-gold/20 transition-colors duration-300">
				<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
			</div>
			<h3 class="text-base font-medium text-deep-blue group-hover:text-gold transition-colors duration-200"><?php esc_html_e( 'Account details', 'woocommerce' ); ?></h3>
			<p class="text-sm text-muted-blue mt-1"><?php esc_html_e( 'Modifier vos informations de compte', 'tlth' ); ?></p>
		</a>
	</div>
</div>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );
?>
