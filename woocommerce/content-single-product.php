<?php

/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
	echo get_the_password_form();
	return;
}

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

$tlth_product_gallery_bg_class = tlth_product_book_hero_bg_class((int) $product->get_id());
$tlth_product_gallery_bg_style = tlth_product_book_hero_bg_style_attr((int) $product->get_id());
// add_action('woocommerce_single_product_summary', array('TLTH', 'get_product_categories_badges'), 5);
add_action('woocommerce_single_product_summary', function () {
	echo '<div class="product-hero-ctas flex flex-wrap gap-x-2.5 gap-y-1.5 mt-auto">';
}, 23);
add_action('woocommerce_single_product_summary', 'tlth_single_product_hero_primary_cta', 24);
add_action('woocommerce_single_product_summary', 'get_product_preview_pdf_btn', 25);
add_action('woocommerce_single_product_summary', function () {
	echo '</div>';
}, 26);

$fields = get_fields($product->get_id());
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('', $product); ?>>

	<!-- Section 1: Product hero (Figma layout) -->
	<section class="single-product-hero product-top py-10 lg:py-16">
		<div class="container max-w-content-lg mx-auto px-4">
			<div class="flex flex-col md:flex-row items-stretch gap-8 lg:gap-12">
				<!-- Left: Gallery -->
				<div class="w-full md:w-1/2 flex-shrink-0">
					<div class="single-product-gallery-wrap aspect-square <?= esc_attr($tlth_product_gallery_bg_class); ?> rounded-[2rem] lg:rounded-[2.25rem] p-0 flex items-center justify-center min-h-[320px] lg:min-h-[420px] w-full relative overflow-visible shadow-soft"<?= $tlth_product_gallery_bg_style ? ' style="' . esc_attr($tlth_product_gallery_bg_style) . '"' : ''; ?>>
						<?php
						/**
						 * Hook: woocommerce_before_single_product_summary.
						 * @hooked woocommerce_show_product_sale_flash - 10
						 * @hooked woocommerce_show_product_images - 20
						 */
						do_action('woocommerce_before_single_product_summary');
						?>
					</div>
				</div>

				<!-- Right: Back link, title, description, price, buttons -->
				<div class="w-full md:w-1/2 flex flex-col lg:py-10">
					<div class="back-to-shop mb-4">
						<a href="<?= esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="text-deep-blue text-[15px] hover:text-teal transition-colors duration-200 inline-flex items-center gap-1">
							<span aria-hidden="true">
								<div class="w-3 h-1.5 rotate-90 -mt-1"><?= file_get_contents(THEME_PATH . '/assets/images/icons/chev-down.svg'); ?></div>
							</span> <?= esc_html(function_exists('pll__') ? pll__('back-to-shop') : 'Retour Ã  la boutique'); ?>
						</a>
					</div>

					<h1>
						<?php the_title(); ?>
					</h1>

					<?php 
					$desc = $product->get_short_description();
					if(!$desc){
						$desc = $product->get_description();
					}
					if ($desc) { ?>
						<div class="product-hero-short-desc the-content text-deep-blue mb-6">
							<?= wpautop(wp_kses_post($desc)); ?>
						</div>
					<?php } ?>

					<?php do_action('woocommerce_single_product_summary'); ?>
				</div>
			</div>
		</div>
	</section>

	<!-- Section: Ce que tu recevras -->
	<?php if ($fields['inclusions-title'] || $fields['inclusions'] || $fields['inclusions-rptr']) { ?>
		<div class="bg-white pt-16 pb-8 lg:py-24 relative min-h-[600px] overflow-x-clip">
			<div class="container max-w-content-lg mx-auto px-4">
				<div class="flex flex-col <?= get_field('inclusions') ? 'md:flex-row' : ''; ?> justify-end lg:justify-start gap-8">
					<?php
					$inclusions_title = $fields['inclusions-title'];
					$inclusions_content = $fields['inclusions'];
					$inclusions = $fields['inclusions-rptr'];

					$img = $fields['inclusions-img'];

					if ($inclusions_title || $inclusions_content || $inclusions) { ?>
						<div class="w-full md:w-1/2 lg:max-w-md" data-animate="fade-up" data-animate-delay="100">
							<h2 class="text-2xl font-medium text-deep-blue mb-4">
								<?= $inclusions_title; ?>
							</h2>
							<div class="the-content text-deep-blue text-[15px] leading-tight font-light mb-8"><?= $inclusions_content; ?></div>
							<div class="flex flex-col gap-3">
								<?php foreach ($inclusions as $inclusion) { ?>
									<div class="the-content text-deep-blue w-full bg-[#E7F8F6] px-5 py-3 rounded-full">
										<p class="text-lg leading-tight font-semibold"><?= $inclusion['inc-item']; ?></p>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
					<?php if ($img) { ?>
						<div class="flex relative lg:absolute -right-8 lg:right-0 top-0 h-full w-full lg:w-1/2" data-animate="fade-right" data-animate-delay="200">
							<?= TLTH::get_image($img, '', 'w-full h-auto rounded-lg object-contain object-right'); ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php
	if (! empty($fields['group-tarif-check'])) {
		$block = array(
			'context' => 'boutique',
			'anchor'  => '',
		);
		include get_template_directory() . '/src/blocks/split-img/block.php';
	}

	if (tlth_product_has_book_personalization_wizard($product)) { ?>
		<div id="personnaliser-le-livre" class="personnaliser-section relative z-10 pt-20 lg:pt-20 pb-40 bg-[#E1F8F7] overflow-x-clip">
			<div class="shapes-container overflow-x-clip">
				<div class="shape shape-zero top-[20rem] lg:top-[8rem] blue -left-[7rem] md:-left-[9rem] lg:-left-[11rem] w-[220px] h-[220px] md:w-[300px] md:h-[300px] lg:w-[420px] lg:h-[420px] rotate-[20deg]">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
				</div>
				<div class="shape shape-half-circle teal -top-[6rem] lg:-top-[8rem] -right-[4rem] md:-right-[6rem] lg:-right-[8rem] -rotate-[25deg] w-[180px] h-[210px] md:w-[270px] md:h-[310px] lg:w-[340px] lg:h-[390px]">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/half-circle.svg'); ?>
				</div>
				<div class="shape shape-half-circle yellow -bottom-[6rem] md:-bottom-[9rem] lg:-bottom-[12rem] left-[25vw] md:left-[32vw] lg:left-[40vw] rotate-[125deg] w-[200px] h-[230px] md:w-[270px] md:h-[310px] lg:w-[340px] lg:h-[390px]">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/half-circle.svg'); ?>
				</div>
			</div>
			<div class="container max-w-content-lg mx-auto px-4">
				<?php tlth_book_wizard_intro(); ?>
				<div class="book-wizard-card relative rounded-[32px] bg-white shadow-soft px-5 py-6 md:px-10 md:py-8" data-book-wizard>
					<?php woocommerce_template_single_add_to_cart(); ?>
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<?php do_action('woocommerce_after_single_product'); ?>