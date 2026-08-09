<?php

/**
 * Split Image Block
 * When $block['context'] is a string (e.g. 'boutique'), reads from that options page.
 * When $block['context'] is an array, uses the values directly (for PHP template includes).
 */
if (isset($block['context']) && is_array($block['context'])) {
	// Inline data passed by a page template
	$shapes        = $block['context']['shapes'] ?? null;
	$section_title = $block['context']['section_title'] ?? 'Ton Livre Ton Histoire: bien plus que des livres!';
	$description   = $block['context']['description'] ?? null;
	$cta_link      = $block['context']['cta_link'] ?? null;
	$featured_image = $block['context']['featured_image'] ?? null;
	$img_rounded   = $block['context']['img_rounded'] ?? 'rounded-3xl';
	$badge_image   = $block['context']['badge_image'] ?? null;
	$show_hat      = $block['context']['show_hat'] ?? false;
	$hat_top       = $block['context']['hat_top'] ?? -20;
	$hat_left      = $block['context']['hat_left'] ?? 35;
	$hat_size      = $block['context']['hat_size'] ?? 72;
	$extra_padding_top    = $block['context']['extra_padding_top'] ?? 'default';
	$extra_padding_bottom = $block['context']['extra_padding_bottom'] ?? 'default';
} else {
	// Boutique options share the global ACF options storage ('option'), not the admin menu slug.
	$block_context = isset( $block['context'] ) && $block['context'] === 'boutique'
		? 'option'
		: ( isset( $block['context'] ) ? $block['context'] : null );

	$fields = get_fields( $block_context ) ?: array();
	$shapes               = $fields['shapes'] ?? null;
	$section_title        = ( $fields['section_title'] ?? '' ) ?: 'Ton Livre Ton Histoire: bien plus que des livres!';
	$description          = $fields['description'] ?? null;
	$cta_link             = $fields['cta_link'] ?? null;
	$featured_image       = $fields['featured_image'] ?? null;
	$img_rounded          = ( $fields['img_rounded'] ?? '' ) ?: 'rounded-3xl';
	$badge_image          = $fields['badge_image'] ?? null;
	$show_hat             = (bool) ( $fields['show_hat'] ?? false );
	$hat_top              = $fields['hat_top'] ?? -20;
	$hat_left             = $fields['hat_left'] ?? 35;
	$hat_size             = $fields['hat_size'] ?? 72;
	$hat_top              = is_numeric( $hat_top ) ? (float) $hat_top : -20;
	$hat_left             = is_numeric( $hat_left ) ? (float) $hat_left : 35;
	$hat_size             = is_numeric( $hat_size ) ? (float) $hat_size : 72;
	$extra_padding_top    = ( $fields['extra_padding_top'] ?? '' ) ?: 'default';
	$extra_padding_bottom = ( $fields['extra_padding_bottom'] ?? '' ) ?: 'default';
}

$split_img_pt_classes = [
	'default' => 'pt-16 lg:pt-28',
	'lg'      => 'pt-32 lg:pt-44',
];
$split_img_pb_classes = [
	'default' => 'pb-16 lg:pb-24',
	'lg'      => 'pb-32 lg:pb-40',
];
$pt_class = $split_img_pt_classes[ $extra_padding_top ] ?? $split_img_pt_classes['default'];
$pb_class = $split_img_pb_classes[ $extra_padding_bottom ] ?? $split_img_pb_classes['default'];

$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="relative z-[2] <?php echo esc_attr($pb_class . ' ' . $pt_class); ?> bg-white split-img-section">
	<!-- Decorative elements -->
	<?php if ($shapes && $shapes != 'none') { ?>
		<div class="shapes-container overflow-x-clip">
			<?php if ($shapes == 'option-1') { ?>
				<div class="shape shape-zero h-[180px] md:h-[240px] lg:h-[320px] -left-[14vw] md:-left-[12vw] lg:-left-[10vw] top-[72%] md:top-[80%]" data-animate="fade-left" data-animate-delay="200">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
				</div>
				<div class="shape shape-intrinsic shape-triple-half origin-top-right scale-[0.4] md:scale-[0.75] lg:scale-100 -top-[1rem] md:-top-[6rem] lg:-top-[8rem] -right-[20vw] xs:-right-[14vw] md:-right-[10vw] lg:-right-[8vw]" data-animate="fade-right" data-animate-delay="400">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/triple-half.svg'); ?>
				</div>
			<?php } elseif ($shapes == 'option-2') { ?>
				<div class="shape shape-zero teal h-[160px] lg:h-[220px] xl:h-[300px] -right-[10vw] md:-right-[7vw] lg:-right-[5vw] top-[80%] lg:top-[70%] -rotate-[100deg]" data-animate="fade-right" data-animate-delay="200">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
				</div>
			<?php } elseif ($shapes == 'option-3') { ?>
				<div class="shape shape-half-circle yellow -left-[14%] md:-left-[8%] xl:-left-[5%] 2xl:left-[11%] bottom-[200px] md:bottom-[150px] lg:bottom-auto lg:-top-28 w-[220px] h-[230px] md:w-[300px] md:h-[315px] lg:w-[400px] lg:h-[420px] -rotate-[75deg]" data-animate="fade-left" data-animate-delay="200">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/half-circle.svg'); ?>
				</div>
				<div class="shape shape-intrinsic shape-triple-half coral origin-bottom-right scale-[0.55] md:scale-[0.75] xl:scale-100 -bottom-[10%] lg:-bottom-[55%] -right-[20vw] md:-right-[15vw] xl:-right-[6vw] 2xl:-right-[4vw] -rotate-[30deg]" data-animate="fade-right" data-animate-delay="400">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/triple-half.svg'); ?>
				</div>
			<?php } elseif ($shapes == 'option-4') { ?>
				<div class="shape shape-intrinsic shape-triple-half origin-top-right scale-[0.55] md:scale-[0.75] lg:scale-100 -top-[4rem] md:-top-[6rem] lg:-top-[8rem] -right-[12vw] md:-right-[10vw] lg:-right-[8vw]" data-animate="fade-right" data-animate-delay="200">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/triple-half.svg'); ?>
				</div>
			<?php } elseif ($shapes == 'option-5') { ?>
				<div class="shape shape-intrinsic shape-triple-half hidden xl:block origin-top-left top-[22rem] -left-[8vw] -rotate-[105deg]" data-animate="fade-left" data-animate-delay="200">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/triple-half.svg'); ?>
				</div>
			<?php } elseif ($shapes == 'option-6') { ?>
				<div class="shape shape-half-circle yellow -left-[14%] md:-left-[8%] xl:-left-[5%] 2xl:left-[14%] bottom-[200px] md:bottom-[150px] lg:bottom-auto lg:top-8 w-[220px] h-[230px] md:w-[300px] md:h-[315px] lg:w-[400px] lg:h-[420px] -rotate-[90deg]" data-animate="fade-left" data-animate-delay="200">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/half-circle.svg'); ?>
				</div>
				<div class="shape shape-zero light-teal h-[160px] lg:h-[220px] xl:h-[300px] -right-[10vw] md:-right-[7vw] lg:-right-[5vw] top-[80%] lg:top-[70%] -rotate-[100deg]" data-animate="fade-right" data-animate-delay="200">
					<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>

	<div class="container max-w-content-lg mx-auto relative z-[2]">
		<div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">
			<!-- Left side - Image -->
			<?php if ($featured_image) { ?>
				<div class="split-img-media relative w-full lg:w-[53%] order-2 lg:order-1 <?php if ($img_rounded == 'rounded-t-full') {
																	echo 'h-[300px] xs:h-[485px] max-w-[485px]';
																} else {
																	echo 'h-[300px]';
																} ?>" data-animate="fade-up" data-animate-delay="100">
					<img src="<?= esc_url(wp_get_attachment_image_url($featured_image, 'full')); ?>"
						alt="<?= $section_title; ?>"
						class="split-img-image w-full !h-full object-cover <?= $img_rounded; ?>">

					<?php if ($badge_image) {
						$badge_classes = 'discount-img-badge absolute top-0 left-0 z-[3] -translate-x-1/4 -translate-y-1/4 w-[130px] h-[130px] lg:h-[182px] lg:w-[182px] rotate-[13deg] pointer-events-none';
						$badge_is_svg   = get_post_mime_type($badge_image) === 'image/svg+xml';

						if ($badge_is_svg) {
							$badge_path = get_attached_file($badge_image);
							if ($badge_path && file_exists($badge_path)) { ?>
								<div class="<?= esc_attr($badge_classes); ?>">
									<?= file_get_contents($badge_path); ?>
								</div>
							<?php }
						} else { ?>
							<img src="<?= esc_url(wp_get_attachment_image_url($badge_image, 'full')); ?>"
								alt=""
								class="<?= esc_attr($badge_classes); ?>">
						<?php }
					} ?>

					<?php if ($show_hat) { ?>
						<div class="split-img-hat absolute z-[4] pointer-events-none rotate-[45deg]"
							style="top: <?= esc_attr($hat_top); ?>px; left: <?= esc_attr($hat_left); ?>%; width: <?= esc_attr($hat_size); ?>px;">
							<?= file_get_contents(THEME_PATH . '/assets/images/shapes/hat.svg'); ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>

			<!-- Right side - Content -->
			<div class="w-full lg:w-[47%] order-1 lg:order-2" data-animate="fade-up" data-animate-delay="50">
				<?php if ($section_title) { ?>
					<h2 class="text-3xl lg:text-4xl font-medium mb-7">
						<?= nl2br(tlth_colored_text($section_title, 'teal')); ?>
					</h2>
				<?php } ?>

				<?php if ($description) { ?>
					<div class="the-content">
						<?= $description ?>
					</div>
				<?php } ?>

				<!-- CTA Button -->
				<?php if ($cta_link) { ?>
					<div class="cta-wrapper mt-7">
						<?= tlth_btn($cta_link, 'btn-primary'); ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</section>