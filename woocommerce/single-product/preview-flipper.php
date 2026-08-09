<?php
global $product;

$product_id = $product ? $product->get_id() : get_the_ID();
$image_urls = tlth_get_product_flipbook_image_urls( $product_id );

if ( empty( $image_urls ) ) {
	return;
}
?>

<div id="flipbook" class="flipbook hidden" aria-hidden="true">
	<?= tlth_render_flipbook_inner( $image_urls ); ?>
</div>

<a href="javascript:;" data-fancybox data-src="#flipbook" data-options='{"touch":false,"autoFocus":false}' class="btn product-hero-btn product-hero-btn--secondary inline-flex items-center justify-center gap-2 px-[18px] py-2 bg-transparent text-deep-blue border border-deep-blue rounded-full text-[15px] font-semibold hover:bg-deep-blue/5 transition-colors duration-200">
	<?= file_get_contents( THEME_PATH . '/assets/images/icons/book.svg' ); ?>
	<span><?= esc_html( function_exists( 'pll__' ) ? pll__( 'view-product-preview' ) : 'Prévisualise le livre' ); ?></span>
</a>
