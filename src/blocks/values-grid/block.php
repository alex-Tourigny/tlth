<?php

/**
 * Values Grid Block
 * Displays brand values as illustrated cards with colored pill titles.
 */

$section_title = get_field('section_title') ?: 'Les valeurs qui guident nos /*créations*/';
$values        = get_field('values');
$block_id      = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';

$cycle_colors = ['teal', 'coral', 'gold'];
?>

<section <?php echo $block_id; ?> class="values-grid relative my-10 lg:my-20 overflow-x-clip">
	<div class="container max-w-content-lg mx-auto px-4 relative z-10">

		<?php if ($section_title) { ?>
			<h2 class="text-3xl lg:text-[35px] font-medium text-deep-blue leading-tight mb-10 lg:mb-12 max-w-[450px]" data-animate="fade-up" data-animate-delay="100">
				<?= tlth_colored_text($section_title, 'teal') ?>
			</h2>
		<?php } ?>

		<?php if ($values && count($values) > 0) { ?>
			<div class="values-list grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
				<?php foreach ($values as $index => $value) {
					$image       = $value['image'] ?? null;
					$title       = $value['title'] ?? '';
					$description = $value['description'] ?? '';
					$color       = !empty($value['color']) ? $value['color'] : $cycle_colors[$index % count($cycle_colors)];
				?>
					<div class="value-card relative flex flex-col items-center text-center bg-white rounded-4xl py-8 px-7" data-animate="fade-up" data-animate-delay="<?= $index * 100 ?>">

						<?php if ($image) { ?>
							<div class="value-illustration h-[110px] sm:h-[140px] w-full flex items-end justify-center mb-6">
								<img src="<?= esc_url(wp_get_attachment_image_url($image, 'medium')) ?>"
									alt="<?= esc_attr($title) ?>"
									class="h-full w-auto object-contain">
							</div>
						<?php } ?>

						<?php if ($title) { ?>
							<span class="value-pill w-full inline-flex items-center justify-center px-10 py-2 rounded-full text-white text-lg font-medium bg-<?= esc_attr($color) ?> mb-6">
								<?= esc_html($title) ?>
							</span>
						<?php } ?>

						<?php if ($description) { ?>
							<p class="text-[15px] font-light text-deep-blue leading-relaxed">
								<?= nl2br(esc_html($description)) ?>
							</p>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</section>
