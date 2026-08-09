<?php

/**
 * Page Hero Block
 * Inner-page hero used on À propos, École/Garderie and similar pages.
 */

$title             = get_field('title') ?: '';
$hero_image        = get_field('hero_image');
$intro_title       = get_field('intro_title');
$intro_description = get_field('intro_description');
$hero_cta          = get_field('hero_cta');
$boutique_cta      = get_field('boutique_cta');
$shape_set         = get_field('shape_set') ?: 'default';
$block_id          = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="page-hero relative pt-10 mb-10 lg:mb-20 overflow-x-clip">
	<div class="shapes-container z-10">
		<?php if ($shape_set === 'alt') { ?>
			<div class="shape shape-triple-half teal origin-top-left scale-[0.5] sm:scale-[0.7] md:scale-[0.9] lg:scale-100 top-[9rem] sm:top-[12rem] md:top-[14rem] lg:top-[17rem] -left-[4vw] xl:left-[2vw] 2xl:left-[22vw] w-[200px] h-[270px]" data-animate="fade-left" data-animate-delay="100">
				<?= file_get_contents(THEME_PATH . '/assets/images/shapes/triple-half.svg'); ?>
			</div>
			<div class="shape shape-zero coral top-[2rem] sm:top-[2.5rem] lg:top-[3rem] -right-[4vw] xl:right-[12vw] 2xl:right-[25vw] rotate-[180deg] w-[69px] h-[73px] sm:w-[97px] sm:h-[102px] md:w-[124px] md:h-[131px] lg:w-[138px] lg:h-[145px]" data-animate="fade-right" data-animate-delay="200">
				<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
			</div>
			<div class="shape shape-half-circle yellow top-[20.5rem] lg:top-[23rem] left-[4vw] sm:left-[12vw] 2xl:left-[30vw] rotate-[10deg] w-[63px] h-[70px] sm:w-[88px] sm:h-[98px] md:w-[113px] md:h-[126px] lg:w-[125px] lg:h-[140px]" data-animate="fade-left" data-animate-delay="300">
				<?= file_get_contents(THEME_PATH . '/assets/images/shapes/half-circle.svg'); ?>
			</div>
		<?php } else { ?>
			<div class="shape shape-triple-half blue -top-6 sm:-top-1 -right-[10vw] sm:-right-[5vw] lg:right-[5vw] 2xl:right-[18vw] scale-[0.5] sm:scale-[0.7] md:scale-[0.9] lg:scale-100 rotate-[150deg] w-[200px] h-[270px]" data-animate="fade-right" data-animate-delay="100">
				<?= file_get_contents(THEME_PATH . '/assets/images/shapes/triple-half.svg'); ?>
			</div>
			<div class="shape shape-zero yellow top-[19.2rem] -left-[4vw] xl:left-[2vw] 2xl:left-[22vw] rotate-[180deg] w-[69px] h-[73px] sm:w-[97px] sm:h-[102px] md:w-[124px] md:h-[131px] lg:w-[138px] lg:h-[145px]" data-animate="fade-left" data-animate-delay="200">
				<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
			</div>
			<div class="shape shape-half-circle coral top-[20.5rem] lg:top-[23rem] left-[4vw] xl:left-[6vw] 2xl:left-[27.5vw] rotate-[10deg] w-[63px] h-[70px] sm:w-[88px] sm:h-[98px] md:w-[113px] md:h-[126px] lg:w-[125px] lg:h-[140px]" data-animate="fade-left" data-animate-delay="300">
				<?= file_get_contents(THEME_PATH . '/assets/images/shapes/half-circle.svg'); ?>
			</div>
		<?php } ?>
	</div>

	<div class="container max-w-content mx-auto px-8 pb-1">
		<?php if ($title) { ?>
			<h1 class="text-deep-blue mb-6 relative z-10" data-animate="fade-up" data-animate-delay="100">
				<?= tlth_colored_text($title) ?>
			</h1>
		<?php } ?>

		<?php if ($hero_image) { ?>
			<div class="page-hero-image w-full mt-6 rounded-4xl overflow-hidden" data-animate="fade-up" data-animate-delay="200">
				<img src="<?= esc_url(wp_get_attachment_image_url($hero_image, 'full')); ?>"
					alt="<?= esc_attr($title); ?>"
					class="w-full h-[276px] object-cover object-center">
			</div>
		<?php } ?>

		<?php if ($intro_title) { ?>
			<div class="pt-9 relative z-10 max-w-[456px] ms-auto" data-animate="fade-up" data-animate-delay="200">
				<h2 class="text-[15px] font-bold text-deep-blue mb-5">
					<?= tlth_colored_text($intro_title) ?>
				</h2>
				<?php if ($intro_description) { ?>
					<div class="the-content text-deep-blue mb-8 font-light">
						<?= $intro_description ?>
					</div>
				<?php } ?>
				<?php if ($hero_cta) { ?>
					<?= tlth_btn($hero_cta, 'btn-primary') ?>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</section>