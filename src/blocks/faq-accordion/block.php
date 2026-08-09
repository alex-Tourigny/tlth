<?php
/**
 * FAQ Accordion Block
 * Pulls from the FAQ CPT grouped by faq-category taxonomy.
 */

$section_title     = get_field('section_title') ?: 'Foire aux questions';
$filter_categories = get_field('faq_categories'); // array of term IDs or empty for all
$block_id          = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';

$term_args = [
    'taxonomy'   => 'faq-category',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
];
if (!empty($filter_categories)) {
    $term_args['include'] = $filter_categories;
}
$categories = get_terms($term_args);
?>

<section <?php echo $block_id; ?> class="faq-accordion relative py-16 lg:py-24 bg-[#E1F8F7]">
	<div class="shapes-container overflow-x-clip pointer-events-none">
		<div class="shape shape-zero light-blue absolute h-[150px] sm:h-[200px] lg:h-[250px] 2xl:h-[350px] -left-[4rem] sm:-left-[12vw] xl:-left-[6vw] -top-20 xl:-top-[8%] rotate-12" data-animate="fade-left" data-animate-delay="300">
			<?= file_get_contents(THEME_PATH . '/assets/images/shapes/zero.svg'); ?>
		</div>
	</div>

	<div class="container max-w-content mx-auto !px-4 md:!px-8 flex flex-col justify-center items-center">

		<?php if ($section_title) { ?>
			<h2 class="mb-8" data-animate="fade-up" data-animate-delay="100">
				<?= tlth_colored_text($section_title, 'teal') ?>
			</h2>
		<?php } ?>

		<?php if (!is_wp_error($categories) && !empty($categories)) {
			foreach ($categories as $category) {
				$faqs = get_posts([
					'post_type'      => 'faq',
					'posts_per_page' => -1,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'tax_query'      => [[
						'taxonomy' => 'faq-category',
						'field'    => 'term_id',
						'terms'    => $category->term_id,
					]],
				]);

				if (empty($faqs)) continue;
			?>
				<div class="faq-category mb-10" data-animate="fade-up" data-animate-delay="100">
					<h3 class="text-xl font-medium text-deep-blue mb-4 flex items-center gap-2">
						<?= esc_html($category->name) ?>
					</h3>

					<div class="faq-list space-y-3">
						<?php foreach ($faqs as $faq) { ?>
							<div class="faq-item rounded-4xl overflow-hidden bg-white shadow-sm">
								<button class="faq-question w-full flex items-center justify-between text-left px-6 py-4 gap-4 text-deep-blue font-medium lg:hover:text-teal transition-colors duration-200" aria-expanded="false">
									<span><?= esc_html(get_the_title($faq)) ?></span>
									<span class="faq-icon flex-shrink-0 flex items-center justify-center transition-transform duration-300">
										<?= file_get_contents( THEME_PATH . '/assets/images/icons/chev-down.svg') ?>
									</span>
								</button>
								<div class="faq-answer overflow-hidden max-h-0 transition-[max-height] duration-300 ease-in-out">
									<div class="the-content px-6 pb-5 text-[15px] leading-relaxed">
										<?= apply_filters('the_content', $faq->post_content) ?>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
		<?php }
		} ?>
	</div>
</section>
