<?php
/**
 * Features Grid Block
 * Displays key features with icons
 */

$features = get_field('features'); // Repeater field for features

$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="features-grid relative pt-16 lg:pt-24">
	<div class="absolute bg-white z-0 w-full h-[65%] -top-2 left-0"></div>
	<div class="container max-w-content-lg mx-auto px-4 relative z-2">
		<?php if ($features && count($features) > 0) { ?>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-5 lg:gap-5">
				<?php foreach ($features as $index => $feature) { 
					$icon = $feature['icon']; // Image field
					$title = $feature['title'];
					$description = $feature['description'];
				?>
					<div class="feature-card text-center px-10 py-[60px] rounded-4xl bg-white" data-animate="fade-up" data-animate-delay="<?= $index * 150 + 100; ?>">
						<!-- Icon -->
						<?php if ($icon) { ?>
							<div class="feature-icon mb-6 flex justify-center">
								<img src="<?= esc_url(wp_get_attachment_image_url($icon, 'large')); ?>" 
									 alt="<?= esc_attr($title); ?>"
									 class="w-24 h-24 md:w-36 md:h-36 max-w-full object-contain">
							</div>
						<?php } ?>

						<!-- Description -->
						<?php if ($description) { ?>
							<p class="text-lg text-deep-blue leading-[1.5]">
								<?= $description ?>
							</p>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</section>