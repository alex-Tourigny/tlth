<?php
/**
 * Content wrappers
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$template = wc_get_theme_slug_for_templates();
?>

<?php if ( is_account_page() && is_user_logged_in() ) { ?>
	<div class="wc-account-page max-w-content mx-auto px-8 py-12 lg:py-20">
		<div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8 lg:gap-12">
<?php } else { ?>
	<div class="">
<?php } ?>
