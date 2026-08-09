<?php

/**
 * Discount Section Block
 * Displays bulk purchase discounts with decorative elements
 * When $block['context'] is set (e.g. 'boutique'), reads from that options page for single-product reuse.
 */

$block_context = isset( $block['context'] ) && $block['context'] === 'boutique'
	? 'option'
	: ( isset( $block['context'] ) ? $block['context'] : null );

$boutique_cta   = get_field('boutique_cta', $block_context);
$main_title     = get_field('main_title', $block_context) ?: 'Plus tu achètes de livres, plus tu économises!';
$discount_tiers = get_field('discount_tiers', $block_context);
$cta_link       = get_field('cta_link', $block_context);

$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="discount-section bg-white relative py-16 lg:py-24 overflow-x-clip">
	<div class="container max-w-content-lg mx-auto px-4 relative z-10">
		<div class="flex justify-between items-end gap-6 mb-8 flex-wrap">
			<?php if ($main_title) { ?>
				<h2 class="text-3xl md:text-4xl font-medium text-primary max-w-[475px] mb-0">
					<?= tlth_colored_text($main_title, 'teal') ?>
				</h2>
			<?php } ?>
			<?php if ($boutique_cta) { ?>
				<?= tlth_btn($boutique_cta, 'btn-primary') ?>
			<?php } ?>
		</div>

		<?php if ($discount_tiers && count($discount_tiers) > 0) {
		?>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
				<?php foreach ($discount_tiers as $index => $tier) {
					$badge_color = ['teal', 'coral', 'gold'][$index % 3];
					$range    = $tier['book_range'];
					$image    = $tier['image'];
					$discount = $tier['discount_percentage'];
				?>
					<div class="discount-tier-card flex flex-col items-center text-center gap-4 bg-[#E1F8F7] rounded-4xl pt-8 pb-10 px-8" data-animate="fade-up" data-animate-delay="<?= $index * 100 ?>">
						<div class="step-badge <?= $badge_color ?> relative flex items-center justify-center w-[80px] h-[80px] lg:w-[117px] lg:h-[117px] rounded-full flex-shrink-0">
							<span class="text-[24px] lg:text-[40px] font-medium text-white"><?= esc_html($discount) ?>%</span>
							<?= file_get_contents(THEME_PATH . '/assets/images/shapes/badge.svg'); ?>
						</div>
						<?php if ($image) { ?>
							<div class="image h-[175px] w-full">
								<img src="<?= esc_url(wp_get_attachment_image_url($image, 'medium')) ?>"
									alt="<?= esc_attr($range) ?>"
									class="w-full h-full object-contain object-center">
							</div>
						<?php } ?>
						<?php if ($range) { ?>
							<p class="text-primary text-lg font-semibold leading-snug">
								<?= nl2br(esc_html($range)) ?>
							</p>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</section>