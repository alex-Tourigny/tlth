<?php

/**
 * Team Slider Block
 * Center-focused member carousel with accent borders and CTA.
 */

$section_title = get_field('section_title') ?: "Rencontre notre équipe dévouée au /*coeur d'enfant*/";
$members       = get_field('members');
$cta_link      = get_field('cta_link');
$block_id      = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';

$accent_classes = [
	'blue'   => ['border' => 'border-light-blue', 'role' => 'text-light-blue'],
	'yellow' => ['border' => 'border-yellow', 'role' => 'text-yellow'],
	'coral'  => ['border' => 'border-coral', 'role' => 'text-coral'],
];
$accent_cycle = ['blue', 'yellow', 'coral'];
?>

<section <?php echo $block_id; ?> class="team-slider relative pt-16 pb-24 lg:pt-24 lg:pb-40 bg-white overflow-x-clip overflow-y-visible">
	<div class="container w-full mx-auto px-4 relative z-10">
		<?php if ($section_title) { ?>
			<h2 class="text-3xl lg:text-4xl text-center text-deep-blue mb-10 md:mb-14 max-w-3xl mx-auto leading-tight" data-animate="fade-up" data-animate-delay="100">
				<?= tlth_colored_text($section_title, 'teal') ?>
			</h2>
		<?php } ?>

		<?php if ($members && count($members) > 0) {
			$chev_left  = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="16" viewBox="0 0 10 16" fill="none" aria-hidden="true"><path d="M8 2L2 8l6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			$chev_right = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="16" viewBox="0 0 10 16" fill="none" aria-hidden="true"><path d="M2 2l6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
			$mi         = 0;
		?>
			<div class="team-slider-stage team-slider-stage--fullbleed relative pb-6 md:pb-10 overflow-x-clip overflow-y-visible" data-animate="fade-up" data-animate-delay="300">
				<div class="swiper team-slider-swiper w-full mx-auto">
					<div class="swiper-wrapper">
						<?php
						for ($i = 0; $i < 3; $i++) {
							foreach ($members as $member) {
								$photo_id = $member['photo'] ?? null;
								$name     = $member['name'] ?? '';
								$role     = $member['role'] ?? '';
								$accent   = isset($member['accent_color']) && $member['accent_color'] !== '' && $member['accent_color'] !== null
									? $member['accent_color']
									: $accent_cycle[$mi % 3];
								$mi++;
								if (!isset($accent_classes[$accent])) {
									$accent = 'yellow';
								}
								$ac = $accent_classes[$accent];
						?>
								<div class="swiper-slide team-slider-slide h-full">
									<article class="team-slider-card w-full mx-auto rounded-[2rem] border-[12px] <?= esc_attr($ac['border']); ?> bg-white overflow-hidden shadow-soft">
										<div class="team-slider-card__media aspect-[3/4] relative bg-beige">
											<?php
											if ($photo_id) {
												echo wp_get_attachment_image(
													$photo_id,
													'large',
													false,
													[
														'class'    => 'absolute inset-0 w-full h-full object-cover object-top',
														'alt'      => $name ? esc_attr($name) : '',
														'loading'  => 'lazy',
														'decoding' => 'async',
													]
												);
											}
											?>
										</div>
										<div class="team-slider-card__caption absolute left-6 bottom-0 w-[calc(100%-48px)] z-[1] -mt-6 mb-4 md:mb-5 rounded-2xl bg-white px-4 py-3 md:px-5 md:py-4 shadow-soft text-center">
											<?php if ($name) { ?>
												<p class="text-base md:text-lg font-semibold text-deep-blue leading-snug">
													<?= esc_html($name) ?>
												</p>
											<?php } ?>
											<?php if ($role) { ?>
												<p class="text-sm md:text-[15px] font-medium <?= esc_attr($ac['role']); ?> mt-1 leading-snug">
													<?= esc_html($role) ?>
												</p>
											<?php } ?>
										</div>
									</article>
								</div>
						<?php }
						} ?>
					</div>
				</div>
				<?php if (count($members) > 1) { ?>
					<button type="button" class="team-slider-nav team-slider-nav--prev" aria-label="<?= esc_attr__('Slide précédente', 'tlth'); ?>">
						<?= $chev_left ?>
					</button>
					<button type="button" class="team-slider-nav team-slider-nav--next" aria-label="<?= esc_attr__('Slide suivante', 'tlth'); ?>">
						<?= $chev_right ?>
					</button>
				<?php } ?>
			</div>
		<?php } ?>

		<?php if (!empty($cta_link['url'])) { ?>
			<div class="flex justify-center -mt-[70px] md:-mt-[115px] relative z-10" data-animate="fade-up" data-animate-delay="500">
				<?= tlth_btn($cta_link, 'btn-secondary') ?>
			</div>
		<?php } ?>
	</div>
</section>