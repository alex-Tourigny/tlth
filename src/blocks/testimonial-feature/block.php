<?php

/**
 * Testimonial Feature Block
 * Featured testimonial with image and quote
 */

$section_title = get_field('section_title') ?: 'Merci à nos chers lecteurs';
$quote_icon = get_field('quote_icon');
$testimonials = get_field('testimonials');

$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="testimonial-feature relative py-16 lg:py-24 bg-beige overflow-hidden">

	<div class="container max-w-content-lg mx-auto px-4 relative z-10">

		<div class="testimonial-container flex flex-col md:flex-row items-stretch gap-12 lg:gap-16">
			<!-- Left side - Image (Static Container) -->
			<div class="w-full md:w-1/2 relative">
				<div class="quote-icon-bg absolute -top-2 right-2 translate-x-1/4 -translate-y-1/4 z-20" data-animate="fade-up" data-animate-delay="250">
					<img src="<?= get_template_directory_uri(); ?>/assets/images/shapes/quote.svg"
						alt="Quote"
						class="w-8 h-8 md:w-16 md:h-16 lg:w-28 lg:h-28">
				</div>

				<div class="main-image-wrapper rounded-4xl md:rounded-5xl overflow-hidden" data-animate="fade-up" data-animate-delay="100">
					<?php
					$first_image = $testimonials[0]['testimonial_image'] ?? null;
					if ($first_image) : ?>
						<img src="<?= esc_url(wp_get_attachment_image_url($first_image, 'large')); ?>"
							alt="Testimonial"
							class="main-testimonial-image w-full h-auto object-cover aspect-[4/3] md:aspect-square transition-opacity duration-500">
					<?php endif; ?>
				</div>
			</div>

			<!-- Right side - Testimonial Slider -->
			<div class="w-full md:w-1/2 py-10 relative" data-animate="fade-up" data-animate-delay="350">
				<h2 class="text-3xl lg:text-4xl font-normal text-center lg:text-left mb-8">
					<?= $section_title ?>
				</h2>
				<div class="testimonial-slider">
					<?php if ($testimonials) : ?>
						<?php foreach ($testimonials as $testimonial) :
							$text = $testimonial['testimonial_text'];
							$author = $testimonial['testimonial_author'];
							$image_id = $testimonial['testimonial_image'];
							$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
						?>
							<div class="testimonial-slide" data-image="<?= esc_url($image_url); ?>">
								<?php if ($text) : ?>
									<blockquote class="testimonial-quote text-base md:text-lg mb-6 leading-relaxed text-center md:text-left">
										<?= $text ?>
									</blockquote>
								<?php endif; ?>

								<?php if ($author) : ?>
									<div class="testimonial-author text-center md:text-left">
										<p class="text-lg font-bold text-teal mb-10">
											<?= $author ?>
										</p>
									</div>
								<?php endif; ?>

							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>