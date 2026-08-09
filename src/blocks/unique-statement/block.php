<?php
/**
 * Unique Statement Block
 * Large statement about each child being unique
 */

$image = get_field('image');
$main_text = get_field('main_text');
$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="unique-statement relative py-16 lg:py-24 bg-white overflow-x-clip">
	<div class="shapes-container overflow-x-clip relative z-[2]">
		<div class="shape shape-triple-half coral -top-14 xl:-top-12 -left-[10vw] md:-left-[6vw] lg:-left-[12vw] xl:left-[8vw] -rotate-[95deg] w-[150px] h-[225px] xs:w-[190px] xs:h-[275px] md:w-[260px] md:h-[375px] lg:w-[345px] lg:h-[500px]" data-animate="fade-left" data-animate-delay="100">
			<?= file_get_contents(THEME_PATH . '/assets/images/shapes/triple-half.svg'); ?>
		</div>
	</div>
	<div class="container max-w-content mx-auto relative z-10">
		<?php if ($image) { ?>
			<div class="image-wrapper w-full h-[95px] relative mb-6" data-animate="fade-up" data-animate-delay="100">
				<img src="<?= wp_get_attachment_image_url($image, 'full'); ?>"
					alt="<?= get_post_meta($image, '_wp_attachment_image_alt', true); ?>"
					class="w-full h-full object-contain">
			</div>
		<?php } ?>
		<span class="block text-center text-3xl xs:text-4xl md:text-5xl lg:text-[65px] lg:px-14 font-medium text-deep-blue leading-tight" data-animate="fade-up" data-animate-delay="200">
			<?= nl2br(tlth_colored_text($main_text)); ?>
		</span>
	</div>
</section>

