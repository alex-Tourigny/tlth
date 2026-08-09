<?php

/**
 * Split Img Full Block
 * Left-aligned mission title with CTA and optional magic overlay image.
 */

 $fields = get_fields() ?: array();

$title = $fields['title'] ?? 'notre mission: donner la piqûre de la /*lecture*/';
$button_link = $fields['button_link'] ?? null;
$main_image = $fields['main_image'] ?? null;
$show_magic = (bool) ($fields['show_magic'] ?? true);
$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="split-img-full overflow-hidden pt-10">
	<div class="max-w-content mx-auto">
		<div class="grid items-center gap-8 lg:grid-cols-[1.05fr_1fr] lg:gap-10">
			<div class="max-w-[540px] lg:py-10">
				<?php if ($title) { ?>
					<h2 class="h1" data-animate="fade-up" data-animate-delay="100">
						<?= tlth_colored_text($title); ?>
					</h2>
				<?php } ?>

				<?php if ($button_link) { ?>
					<div data-animate="fade-up" data-animate-delay="300">
						<?= tlth_btn($button_link, 'btn-primary'); ?>
					</div>
				<?php } ?>
			</div>

			<?php if ($main_image) { ?>
				<div
					class="split-img-full-media relative mx-auto w-full max-w-[540px]<?= $show_magic ? ' split-img-full-media--magic overflow-visible' : ''; ?>"
					data-animate="fade-up"
					data-animate-delay="500">
					<img
						src="<?= esc_url(wp_get_attachment_image_url($main_image, 'full')); ?>"
						alt="<?= esc_attr(get_post_meta($main_image, '_wp_attachment_image_alt', true) ?: wp_get_attachment_caption($main_image) ?: 'Mission image'); ?>"
						class="relative z-[2] h-auto w-full object-contain">

					<?php if ($show_magic) { ?>
						<div class="split-img-full-sparkles pointer-events-none absolute inset-0 z-[4] overflow-visible" aria-hidden="true"></div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
