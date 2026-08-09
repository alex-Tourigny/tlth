<?php

/**
 * Volume Discount Block
 * Teal promo section with tier pills, ghost CTA, and featured image with badge.
 */

$fields = get_fields() ?: array();

$section_title  = $fields['section_title'] ?? 'Plus tu achètes de livres, plus tu économises!';
$description    = $fields['description'] ?? null;
$discount_tiers = $fields['discount_tiers'] ?? null;
$cta_link       = $fields['cta_link'] ?? null;
$featured_image = $fields['featured_image'] ?? null;
$show_badge     = (bool) ($fields['show_badge'] ?? true);
$badge_image    = $fields['badge_image'] ?? null;

$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';

$has_content = $section_title || $description || $discount_tiers || $cta_link || $featured_image;
if (!$has_content) {
	return;
}
?>

<section <?php echo $block_id; ?> class="volume-discount relative bg-teal py-16 lg:py-28 min-h-[600px] overflow-x-clip">
	<div class="shapes-container overflow-x-clip pointer-events-none" aria-hidden="true">
		<div class="shape shape-half-circle blue -top-[4rem] md:-top-[8rem] -right-[6rem] md:-right-[10rem] w-[180px] h-[210px] md:w-[260px] md:h-[300px] lg:w-[350px] lg:h-[390px] -rotate-[30deg]" data-animate="fade-right" data-animate-delay="200">
			<?= file_get_contents(THEME_PATH . '/assets/images/shapes/half-circle.svg'); ?>
		</div>
	</div>

	<div class="container max-w-content-lg mx-auto px-4 relative z-10">
		<div class="flex flex-col <?= $featured_image ? 'lg:flex-row items-center lg:items-start' : 'items-center'; ?> justify-end lg:justify-start gap-8 lg:gap-12">
			<?php if ($section_title || $description || $discount_tiers || $cta_link) { ?>
				<div class="w-full lg:w-1/2 max-w-[450px] relative z-10 text-center lg:text-left" data-animate="fade-up" data-animate-delay="100">
					<?php if ($section_title) { ?>
						<h2 class="text-2xl md:text-[35px] leading-tight font-medium text-white mb-7">
							<?= esc_html($section_title); ?>
						</h2>
					<?php } ?>

					<?php if ($description) { ?>
						<div class="the-content text-white text-[15px] leading-tight font-light mb-5">
							<?= $description; ?>
						</div>
					<?php } ?>

					<?php if ($discount_tiers) { ?>
						<div class="flex flex-col items-center lg:items-start gap-3 mb-10">
							<?php foreach ($discount_tiers as $index => $tier) {
								$tier_text = $tier['tier_text'] ?? '';
								if (!$tier_text) {
									continue;
								}
							?>
								<div class="volume-discount-tier flex items-center gap-3 w-fit bg-white px-6 py-3 rounded-full" data-animate="fade-up" data-animate-delay="<?= ($index * 80) + 150; ?>">
									<p class="text-base md:text-lg leading-tight font-semibold text-deep-blue mb-0">
										<?= esc_html($tier_text); ?>
									</p>
								</div>
							<?php } ?>
						</div>
					<?php } ?>

					<?php if ($cta_link) { ?>
						<div class="cta-wrapper flex justify-center lg:justify-start" data-animate="fade-up" data-animate-delay="350">
							<?= tlth_btn($cta_link, 'btn-ghost-white'); ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>

			<?php if ($featured_image) { ?>
				<div class="flex relative lg:absolute -right-8 lg:right-0 top-0 h-full w-full lg:w-1/2 items-center justify-center lg:justify-start" data-animate="fade-right" data-animate-delay="200">
					<div class="shapes-container -z-[1]">	
						<div class="shape shape-circle bg-[#6CDBD2] -left-[10%] lg:-left-[4%] top-[45%] w-[140px] h-[140px] lg:w-[180px] lg:h-[180px] rounded-full" data-animate="fade-left" data-animate-delay="300"></div>
						<div class="shape shape-half-circle light-teal -right-[10%] -bottom-[2%] lg:-bottom-[20%] w-[370px] h-[400px] lg:w-[500px] lg:h-[570px] rotate-[48deg]" data-animate="fade-right" data-animate-delay="400">
							<?= file_get_contents(THEME_PATH . '/assets/images/shapes/half-circle.svg'); ?>
						</div>
					</div>
					<div class="volume-discount-media relative w-full max-w-[500px] lg:max-w-none">
						<?= wp_get_attachment_image($featured_image, 'large', false, ['class' => 'w-full lg:w-[115%] lg:max-w-[115%] h-auto rounded-lg object-contain object-right']); ?>

						<?php if ($show_badge) { ?>
							<?php
							$badge_classes = 'discount-img-badge absolute bottom-0 right-0 z-[3] translate-x-[12%] translate-y-[12%] w-[120px] h-[120px] sm:w-[140px] sm:h-[140px] lg:w-[180px] lg:h-[180px] rotate-[13deg] pointer-events-none';

							if ($badge_image) {
								$badge_is_svg = get_post_mime_type($badge_image) === 'image/svg+xml';

								if ($badge_is_svg) {
									$badge_path = get_attached_file($badge_image);
									if ($badge_path && file_exists($badge_path)) { ?>
										<div class="<?= esc_attr($badge_classes); ?>">
											<?= file_get_contents($badge_path); ?>
										</div>
									<?php }
								} else { ?>
									<img src="<?= esc_url(wp_get_attachment_image_url($badge_image, 'full')); ?>"
										alt=""
										class="<?= esc_attr($badge_classes); ?>">
								<?php }
							} ?>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
