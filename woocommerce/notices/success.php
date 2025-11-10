<?php
/**
 * Show success messages – Custom Merged Version
 *
 * This template is a merged version of the WooCommerce success messages template,
 * based on the updated version (v8.6.0) and incorporating legacy modifications
 * from version (v3.9.0). It displays each success notice inside a <div> with the class
 * "woocommerce-message" and a role attribute set to "alert".
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/success.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! $notices ) {
    return;
}
?>

<?php foreach ( $notices as $notice ) : ?>
    <div class="woocommerce-message"<?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="alert">
        <?php echo wc_kses_notice( $notice['notice'] ); ?>
    </div>
<?php endforeach; ?>
