<?php

/**
 * Shop Archive Promo
 * Education promo banner at the bottom of the product archive. Reads from Boutique options.
 */

$title       = get_field('shop_archive_promo_title', 'option');
$description = get_field('shop_archive_promo_description', 'option');
$cta_link    = get_field('shop_archive_promo_cta', 'option');
$image       = get_field('shop_archive_promo_image', 'option');
$show_badge  = (bool) get_field('shop_archive_promo_show_badge', 'option');
$badge_image = get_field('shop_archive_promo_badge_image', 'option');

$has_content = $title || $description || $cta_link || $image;
if (!$has_content) {
	return;
}

$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="shop-archive-promo pb-16 lg:pb-24">
	<div class="max-w-content mx-auto px-8">
		<div class="shop-archive-promo__card relative flex flex-col lg:flex-row rounded-4xl overflow-hidden shadow-soft min-h-[280px] lg:min-h-[320px]" data-animate="fade-up">
			<div class="bg-teal text-white flex flex-col justify-center px-4 xs:px-8 pt-10 pb-20 lg:py-14 lg:pl-14 lg:pr-[110px] lg:w-[50%] lg:flex-shrink-0 relative z-10">
				<?php if ($title) { ?>
					<h2 class="text-2xl md:text-4xl font-medium text-white mb-7">
						<?= tlth_colored_text($title, 'white') ?>
					</h2>
				<?php } ?>

				<?php if ($description) { ?>
					<div class="the-content text-white text-[15px] font-light leading-snug mb-8 [&_p:last-child]:mb-0">
						<?= $description ?>
					</div>
				<?php } ?>

				<?php if ($cta_link) { ?>
					<div class="cta-wrapper">
						<?= tlth_btn($cta_link, 'btn-ghost-white') ?>
					</div>
				<?php } ?>
			</div>

			<?php if ($image) { ?>
				<div class="shop-archive-promo__media relative lg:flex-1 min-h-[350px] lg:min-h-0">
					<img
						src="<?= esc_url(wp_get_attachment_image_url($image, 'large')) ?>"
						alt="<?= esc_attr(wp_strip_all_tags($title ?: get_post_meta($image, '_wp_attachment_image_alt', true))) ?>"
						class="absolute inset-0 w-full !h-full object-cover object-center">
				</div>
			<?php } ?>

			<?php if ($show_badge && $badge_image) {
				$badge_classes = 'discount-img-badge absolute z-20 pointer-events-none w-[100px] h-[100px] sm:w-[120px] sm:h-[120px] lg:w-[150px] lg:h-[150px] left-1/2 -translate-x-1/2 bottom-[300px] lg:bottom-0 lg:left-[50%] lg:top-1/2 lg:bottom-auto lg:-translate-y-1/2';
				$badge_is_svg  = get_post_mime_type($badge_image) === 'image/svg+xml';

				if ($badge_is_svg) {
					$badge_path = get_attached_file($badge_image);
					if ($badge_path && file_exists($badge_path)) { ?>
						<div class="<?= esc_attr($badge_classes) ?>" aria-hidden="true">
							<?= file_get_contents($badge_path) ?>
						</div>
					<?php }
				} else { ?>
					<img
						src="<?= esc_url(wp_get_attachment_image_url($badge_image, 'full')) ?>"
						alt=""
						class="<?= esc_attr($badge_classes) ?>"
						aria-hidden="true">
				<?php }
			} ?>
		</div>
	</div>
</section>
