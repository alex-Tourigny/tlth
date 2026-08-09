<?php
/**
 * Personalization Steps Block
 * Shows the 3-step personalization process
 */

$steps = get_field('steps');
$button_link = get_field('button_link');

if ($button_link && empty($button_link['title'])) {
	$button_link['title'] = __('Personnalise ton livre', 'tlth');
}

$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="personalization-steps relative py-16 lg:py-24 bg-white">
	<div class="container max-w-content-lg mx-auto px-4">
		<div class="steps-surface relative md:rounded-[32px] md:bg-[#E1F8F7] md:px-6 md:py-12 lg:px-12 lg:py-16" data-animate="fade-up" data-animate-delay="100">
			<div class="steps-grid grid gap-20 md:grid-cols-3 md:gap-10">
				<?php if ($steps) { ?>
					<?php foreach ($steps as $index => $step) { ?>
						<?php
						$number = sprintf('%02d', $index + 1);
						$step_title = isset($step['step_title']) ? $step['step_title'] : '';
						$step_description = isset($step['step_description']) ? $step['step_description'] : '';
						$step_image = isset($step['step_image']) ? $step['step_image'] : null;
						?>
						<div class="step-item relative flex flex-col items-center text-center gap-4 rounded-[32px] md:rounded-none bg-[#E1F8F7] px-6 pt-16 pb-12 md:bg-transparent md:px-0 md:py-0 md:pt-0 md:pb-0" data-animate="fade-up" data-animate-delay="<?= $index * 80 + 100; ?>">
							<div class="step-badge absolute -top-[50px] left-1/2 flex -translate-x-1/2 items-center justify-center text-[40px] font-medium text-white w-[100px] h-[100px] md:relative md:left-auto md:top-auto md:translate-x-0 md:-mt-[115px] <?= $step['badge_color'] ?>">
								<?= file_get_contents(THEME_PATH . '/assets/images/shapes/badge.svg'); ?>
								<span class="relative z-10"><?= esc_html($number); ?></span>
							</div>
							<?php if ($step_image) { ?>
								<div class="step-illustration mt-4 mb-2 w-full max-w-[220px] overflow-hidden">
									<?= wp_get_attachment_image($step_image, 'large', false, ['class' => 'w-full h-auto object-contain']); ?>
								</div>
							<?php } ?>
							<?php if ($step_title) { ?>
								<p class="step-title text-lg md:text-xl text-deep-blue">
									<?= esc_html($step_title); ?>
								</p>
							<?php } ?>
						</div>
					<?php } ?>
				<?php } else { ?>
					<p class="step-empty text-lg text-deep-blue md:col-span-3 text-center">
						<?= esc_html__('Ajoutez trois étapes dans le bloc pour afficher ce module.', 'tlth'); ?>
					</p>
				<?php } ?>
			</div>
		</div>
		<?php if ($button_link) { ?>
			<div class="cta-wrapper mt-10 text-center">
				<?= tlth_btn($button_link, 'btn-primary'); ?>
			</div>
		<?php } ?>
	</div>
</section>