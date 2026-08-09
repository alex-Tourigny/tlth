<?php
$fields = get_fields() ?: [];
$section_title = $fields['section_title'];
$description = $fields['description'];
$address = $fields['address'];
$address_link = $fields['address_link'];
$phone = $fields['phone'];
$text_btn_txt = $fields['text_btn_txt'];
$btn_link = $fields['btn_link'];
$form_id = $fields['form_id'];
$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?php echo $block_id; ?> class="split-form-section relative py-10 sm:py-14 lg:py-24">
	<div class="container max-w-content-lg mx-auto px-4 sm:px-6">
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10 lg:gap-16 items-start">
			<div class="lg:col-span-5">
				<?php if ($section_title) { ?>
					<h1 data-animate="fade-up" data-animate-delay="100">
						<?= tlth_colored_text($section_title); ?>
					</h1>
				<?php } ?>

				<?php if ($description) { ?>
					<div class="the-content mt-4 sm:mt-6" data-animate="fade-up" data-animate-delay="200">
						<?= $description ?>
					</div>
				<?php } ?>

				<?php if ($address || $phone) { ?>
					<div class="split-form-contact mt-8 sm:mt-10 lg:mt-12 space-y-6 sm:space-y-8" data-animate="fade-up" data-animate-delay="300">
						<?php if ($address) { ?>
							<div class="split-form-contact-item flex items-start gap-3 sm:gap-4">
								<?php if ($address_link) { ?>	
									<a href="<?= $address_link['url']; ?>" target="<?= $address_link['target']; ?>" class="text-base sm:text-lg leading-tight flex items-start gap-2 sm:gap-3 split-form-contact-link">
										<span class="split-form-icon shrink-0 w-5 h-5 sm:w-6 sm:h-6"><?= file_get_contents(THEME_PATH . "/assets/images/icons/house.svg"); ?></span>
										<span><?= $address ?></span>
									</a>
								<?php } else { ?>
									<div class="text-base sm:text-lg leading-tight flex items-start gap-2 sm:gap-3">
										<span class="split-form-icon shrink-0 w-5 h-5 sm:w-6 sm:h-6"><?= file_get_contents(THEME_PATH . "/assets/images/icons/house.svg"); ?></span>
										<span><?= $address ?></span>
									</div>
								<?php } ?>
							</div>
						<?php } ?>

						<?php if ($phone) { ?>
							<div class="split-form-contact-item flex items-start gap-3 sm:gap-4">
								<a href="tel:<?= esc_attr(TLTH::sanitize_phone_number($phone)); ?>" class="text-base sm:text-lg leading-tight flex items-start gap-2 sm:gap-3 split-form-contact-link">
									<span class="split-form-icon shrink-0 w-5 h-5 sm:w-6 sm:h-6"><?= file_get_contents(THEME_PATH . "/assets/images/icons/phone.svg"); ?></span>
									<span><?= $phone ?></span>	
								</a>
							</div>
						<?php } ?>
					</div>
				<?php } ?>

				<?php if ($text_btn_txt) { ?>
					<div class="mt-6 sm:mt-8 text-sm sm:text-[15px] leading-tight font-bold" data-animate="fade-up" data-animate-delay="400">
						<p><?= $text_btn_txt ?></p>
					</div>
				<?php } ?>

				<?php if ($btn_link) { ?>
					<div class="split-form-cta mt-2 sm:mt-2.5" data-animate="fade-up" data-animate-delay="500">
						<?= tlth_btn($btn_link, 'btn-primary'); ?>
					</div>
				<?php } ?>
			</div>

			<div class="split-form-form-wrapper lg:col-span-7 bg-white p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl" data-animate="fade-up" data-animate-delay="400">
				<?php if ($form_id) { ?>
					<?php gravity_form((int) $form_id, false, false, false, [], true); ?>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
