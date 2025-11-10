<?php
/**
 * Show messages – Custom Merged Version
 *
 * This template is a merged version of the WooCommerce notices template,
 * based on the updated version (v8.6.0) and incorporating legacy modifications
 * from version (v3.9.0). It displays each notice inside a <div> with the class
 * "woocommerce-info".
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/notice.php.
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
    <div class="woocommerce-info"<?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
        <?php echo wc_kses_notice( $notice['notice'] ); ?>
    </div>
<?php endforeach; ?>
