<?php

/**
 * Home Hero Block for TLTH
 * Main hero section with colorful title and call-to-action
 */

$title = get_field('title') ?: "Le héros de l'histoire, c'est toi!";
$subtitle = get_field('subtitle') ?: "Laisse-toi emporter par la magie de Ton Livre, Ton Histoire: un univers merveilleux où";
$button_text = get_field('button_text') ?: 'Choisis ton aventure';
$button_link = get_field('button_link');
$hero_image = get_field('hero_image'); // Image field

// Get the block ID (anchor) if set
$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="hero-homepage relative overflow-hidden py-16 lg:pt-24 lg:pb-16">
	<div class="hero-content text-center sm:max-w-[1208px] sm:px-8 mx-auto">
		<div class="hero-content-inner px-8 sm:px-0">
			<h1 class="hero-title text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold mb-6" data-animate="fade-up">
				<?= tlth_colored_text($title) ?>
			</h1>

			<?php if ($subtitle) { ?>
				<p class="hero-subtitle text-base md:text-lg text-deep-blue max-w-3xl mx-auto mb-4" data-animate="fade-up" data-animate-delay="200">
					<?= $subtitle ?>
				</p>
			<?php } ?>
		</div>

		<?php if ($hero_image) { ?>
			<div class="img-wrapper w-full h-[200px] md:h-[360px] my-4 md:my-0 relative" data-animate="fade-up" data-animate-delay="300">
				<div class="shapes-container">
					<div class="shape shape-half-circle" data-animate="fade-left" data-animate-delay="900"></div>
					<div class="shape shape-hat" data-animate="fade-down" data-animate-delay="400"></div>
					<div class="shape shape-circle" data-animate="fade-right" data-animate-delay="700"></div>
					<div class="shape shape-zero" data-animate="fade-right" data-animate-delay="1200">
						<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
					</div>
				</div>
				<img src="<?= wp_get_attachment_image_url($hero_image, 'full'); ?>"
					alt="<?= get_post_meta($hero_image, '_wp_attachment_image_alt', true); ?>"
					class="w-full h-full object-contain">
			</div>
		<?php } ?>

		<?php if ($button_link) { ?>
			<div class="hero-cta mt-1 px-8 sm:px-0" data-animate="fade-up" data-animate-delay="400">
				<?= tlth_btn($button_link, 'btn-primary'); ?>
			</div>
		<?php } ?>
	</div>
</section>