<?php

/**
 * Campaign Steps Block
 * 6-step fundraising guide — used on the École/Garderie page.
 */

$section_title = get_field('section_title') ?: 'Organisez votre campagne de financement en /*6 étapes faciles*/';
$description   = get_field('description');
$cta_link      = get_field('cta_link');
$steps         = get_field('steps');
$block_id      = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="campaign-steps relative py-16 lg:py-24 bg-[#E1F8F7] overflow-x-clip">
	<div class="container max-w-content-lg mx-auto px-4">

		<div class="flex flex-col lg:flex-row gap-8 lg:gap-24 mb-14 lg:mb-20 items-start">
			<div class="flex flex-col items-start gap-0 w-full lg:w-[54%]" data-animate="fade-up" data-animate-delay="100">
				<?php if ($section_title) { ?>
					<h2 class="text-3xl lg:text-[35px] font-medium text-deep-blue leading-tight">
						<?= tlth_colored_text($section_title, 'teal') ?>
					</h2>
				<?php } ?>

				<?php if ($cta_link) { ?>
					<?= tlth_btn($cta_link, 'btn-primary') ?>
				<?php } ?>
			</div>

			<?php if ($description) { ?>
				<div class="text-[15px] font-light text-deep-blue leading-relaxed w-full lg:w-[46%]" data-animate="fade-up" data-animate-delay="300">
					<?= nl2br(wp_kses_post($description)) ?>
				</div>
			<?php } ?>
		</div>

		<?php if ($steps && count($steps) > 0) { ?>
			<div class="steps-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-20 lg:gap-y-16">
				<?php foreach ($steps as $index => $step) {
					$number     = sprintf('%02d', $index + 1);
					$step_title = $step['step_title'] ?? '';
					$step_desc  = $step['step_description'] ?? '';
					$step_image = $step['step_image'] ?? null;
				?>
					<div class="campaign-step relative flex flex-col items-center text-center gap-5 bg-white shadow-soft rounded-3xl px-2 pt-16 pb-11" data-animate="fade-up" data-animate-delay="<?= $index * 50 + 200; ?>">
						<div class="step-badge absolute -top-[50px] flex items-center justify-center w-[100px] h-[100px] text-[40px] font-medium text-white">
							<?= file_get_contents(THEME_PATH . '/assets/images/shapes/badge.svg'); ?>
							<span class="relative z-10"><?= $number ?></span>
						</div>

						<?php if ($step_image) { ?>
							<div class="step-illustration h-[120px] w-full flex items-center justify-center">
								<img src="<?= esc_url(wp_get_attachment_image_url($step_image, 'medium')) ?>"
									alt="<?= esc_attr($step_title) ?>"
									class="step-img h-full w-auto object-contain">
							</div>
						<?php } ?>

						<div class="flex flex-col items-center gap-1">
							<?php if ($step_title) { ?>
								<p class="text-[20px] font-semibold text-deep-blue leading-[1.25]">
									<?= esc_html($step_title) ?>
								</p>
							<?php } ?>

							<?php if ($step_desc) { ?>
								<p class="text-[15px] font-light text-deep-blue leading-relaxed">
									<?= esc_html($step_desc) ?>
								</p>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</section>