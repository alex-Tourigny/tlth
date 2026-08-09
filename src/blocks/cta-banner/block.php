<?php
/**
 * CTA Banner Block
 * Full-width colored banner with headline and CTA button.
 * Used as the closing section on the École page.
 */

$headline         = get_field('headline') ?: 'Recevez 7$ par livre vendu!';
$cta_link         = get_field('cta_link');
$background_color = get_field('background_color') ?: 'teal';
$block_id         = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="cta-banner bg-[#E1F8F7] pt-12 lg:pt-16 !pb-40 overflow-x-clip">
	<div class="shape shape-triple-half yellow absolute bottom-[6rem] sm:-bottom-12 md:-bottom-16 lg:-bottom-20 -left-[12vw] sm:-left-[10vw] xl:left-[10vw] -rotate-[95deg] w-[200px] h-[260px] sm:w-[280px] sm:h-[364px] md:w-[360px] md:h-[468px] lg:w-[400px] lg:h-[520px]" data-animate="fade-left" data-animate-delay="100">
		<?= file_get_contents(THEME_PATH . '/assets/images/shapes/triple-half.svg'); ?>
	</div>
	<div class="container max-w-content-lg mx-auto px-4 relative z-10">
		<div class="flex flex-col items-center justify-center gap-8 text-deep-blue">
			<?php if ($headline) { ?>
				<h3 class="text-3xl sm:text-4xl md:text-5xl lg:text-[65px] font-medium text-center max-w-[550px] leading-none" data-animate="fade-up" data-animate-delay="100">
					<?= tlth_colored_text($headline) ?>
				</h3>
			<?php } ?>

			<?php if ($cta_link) { ?>
				<div class="cta-wrapper flex-shrink-0" data-animate="fade-up" data-animate-delay="300">
					<?= tlth_btn($cta_link, 'btn-primary') ?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
