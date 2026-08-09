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

$template = wc_get_theme_slug_for_templates();
?>

<?php if ( is_account_page() && is_user_logged_in() ) { ?>
		</div>
	</div>
<?php } else { ?>
	</div>
<?php } ?>
