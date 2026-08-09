<?php
/**
 * Shared book product card markup (shop loop + book showcase).
 *
 * Expects `$book_card_args` with:
 * - href         (string) Product URL.
 * - title        (string) Plain-text product title.
 * - image_id     (int)    Featured image attachment ID.
 * - price_html   (string) WooCommerce price HTML; may be empty.
 * - post_id      (int)    Post ID (hero background from ACF `bg-color` / `bg-color-custom`).
 */
defined( 'ABSPATH' ) || exit;

if ( ! isset( $book_card_args ) || ! is_array( $book_card_args ) ) {
	return;
}

$book_card_args = wp_parse_args(
	$book_card_args,
	array(
		'href'       => '',
		'title'      => '',
		'image_id'   => 0,
		'price_html' => '',
		'post_id'    => 0,
	)
);

$href         = esc_url( $book_card_args['href'] );
$title        = $book_card_args['title'];
$image_id     = absint( $book_card_args['image_id'] );
$post_id      = absint( $book_card_args['post_id'] );
$price_html   = $book_card_args['price_html'];

if ( '' === $href || ! $post_id ) {
	return;
}

$hero_bg_class = tlth_product_book_hero_bg_class( $post_id );
$hero_bg_style = tlth_product_book_hero_bg_style_attr( $post_id );

$img_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';

$product   = wc_get_product( $post_id );
$cta_label = esc_html(
	( $product && $product->is_type( 'variable' ) )
		? ( function_exists( 'pll__' ) ? pll__( 'Personnalise-moi' ) : 'Personnalise-moi' )
		: ( function_exists( 'pll__' ) ? pll__( 'Voir le produit' ) : 'Voir le produit' )
);
?>
<a href="<?php echo $href; ?>" class="book-card group relative flex h-full min-h-0 flex-col overflow-hidden rounded-3xl bg-white no-underline">
	<div class="<?php echo esc_attr( $hero_bg_class ); ?> relative flex h-[225px] sm:h-[282px] shrink-0 items-center justify-center overflow-hidden"<?php echo $hero_bg_style ? ' style="' . esc_attr( $hero_bg_style ) . '"' : ''; ?>>
		<div class="book-card-image-inner transition-transform duration-300 ease-in-out drop-shadow-[0_10px_28px_rgba(16,66,109,0.18)] group-hover:-translate-y-2 group-focus-within:-translate-y-2">
			<div class="image -rotate-[15deg] h-[160px] sm:h-[200px] w-[170px] max-h-[80%] max-w-full">
				<?php if ( $img_url ) { ?>
					<img
						src="<?php echo esc_url( $img_url ); ?>"
						alt="<?php echo esc_attr( $title ); ?>"
						class="book-cover mx-auto !h-full !w-full max-h-full max-w-full object-contain"
					>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="pointer-events-none absolute left-1/2 top-[225px] sm:top-[282px] z-20 -translate-x-1/2 -translate-y-1/2">
		<span
			class="inline-flex max-w-[calc(100vw-2rem)] items-center whitespace-nowrap rounded-full bg-deep-blue px-5 py-2 text-center text-[13px] font-medium text-white shadow-sm opacity-100 translate-y-0 transition-all duration-300 ease-in-out lg:opacity-0 lg:translate-y-1 lg:group-hover:translate-y-0 lg:group-hover:opacity-100 lg:group-focus-within:translate-y-0 lg:group-focus-within:opacity-100 sm:text-sm"
		>
			<?php echo $cta_label; ?>
		</span>
	</div>

	<div class="book-info relative z-10 flex flex-1 flex-col bg-white px-6 pb-6 pt-8 lg:pt-5 text-center transition-transform duration-300 ease-in-out group-hover:translate-y-2 group-focus-within:translate-y-2">
		<h3 class="mb-1 text-lg font-bold leading-tight text-deep-blue">
			<?php echo esc_html( $title ); ?>
		</h3>
		<?php if ( $price_html ) { ?>
			<p class="text-[15px] text-deep-blue">
				<?php echo wp_kses_post( $price_html ); ?>
			</p>
		<?php } ?>
	</div>
</a>
