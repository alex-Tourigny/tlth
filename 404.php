<?php get_header(); ?>

<?php
$content = get_field('404-content-' . LANG, 'option');
$link = get_field('404-link-' . LANG, 'option');
?>

<section class="relative isolate flex min-h-[65vh] items-center overflow-hidden py-20 sm:py-28 lg:py-32">
	<div class="shapes-container pointer-events-none" aria-hidden="true">
		<div class="shape shape-triple-half teal -left-24 top-12 h-[270px] w-[200px] -rotate-12 sm:-left-12 lg:left-[5vw]">
			<?= file_get_contents(THEME_PATH . '/assets/images/shapes/triple-half.svg'); ?>
		</div>
		<div class="shape shape-zero coral -right-8 top-8 h-[145px] w-[138px] rotate-[150deg] sm:right-[4vw]">
			<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
		</div>
		<div class="shape shape-half-circle yellow bottom-8 right-[12vw] h-[140px] w-[125px] rotate-12">
			<?= file_get_contents(THEME_PATH . '/assets/images/shapes/half-circle.svg'); ?>
		</div>
	</div>

	<div class="mx-auto w-full max-w-content px-8 relative z-10">
		<div class="mx-auto max-w-3xl rounded-4xl bg-white px-6 py-12 text-center shadow-soft sm:px-12 sm:py-16 lg:px-20">
			<p class="mb-6 text-7xl font-semibold leading-none tracking-tight text-teal sm:text-8xl lg:text-9xl" aria-hidden="true">404</p>

			<?php if ($content) { ?>
				<div class="the-content text-deep-blue [&_a]:text-teal [&_a]:underline [&_a]:underline-offset-4 [&_h1]:mb-5 [&_h1]:text-3xl [&_h1]:font-medium [&_h1]:leading-tight [&_h2]:mb-5 [&_p]:text-base [&_p]:font-light [&_p]:leading-relaxed sm:[&_h1]:text-4xl">
					<?= wp_kses_post($content); ?>
				</div>
			<?php } ?>

			<?php if ($link) { ?>
				<div class="mt-8 flex justify-center">
					<?= tlth_btn($link, 'btn-secondary'); ?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>