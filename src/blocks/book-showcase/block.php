<?php

/**
 * Book Showcase Block
 * Displays featured books in a grid layout with navigation
 */

$section_title = get_field('section_title') ?: 'Découvre nos contes';
$description = get_field('description');
$books = get_field('books');

if (empty($books)) {
	$books = get_posts(array(
		'post_type'      => 'product',
		'posts_per_page' => 12,
		'orderby'        => 'rand',
	));
}

// Get the block ID (anchor) if set
$block_id = isset($block['anchor']) && !empty($block['anchor']) ? 'id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section <?= $block_id; ?> class="book-showcase relative py-16 lg:py-24">
	<div class="max-w-content mx-auto">
		<!-- Section Header -->
		<div class="section-header mb-10 flex justify-between items-end">
			<div class="flex flex-col max-w-3xl">
				<h2 data-animate="fade-up" data-animate-delay="50">
					<?= $section_title ?>
				</h2>

				<?php if ($description) { ?>
					<div class="the-content max-w-[430px]" data-animate="fade-up" data-animate-delay="100">
						<?= $description ?>
					</div>
				<?php } ?>
			</div>

			<!-- Navigation arrows -->
			<div class="nav-arrows flex justify-center gap-2 mt-1" data-animate="fade-up" data-animate-delay="200">
				<button class="nav-arrow prev group" aria-label="Précédent">
					<?= file_get_contents(THEME_PATH . '/assets/images/prev-button.svg'); ?>
				</button>
				<button class="nav-arrow next group" aria-label="Suivant">
					<?= file_get_contents(THEME_PATH . '/assets/images/next-button.svg'); ?>
				</button>
			</div>
		</div>

		<!-- Books Grid -->
		<div class="books-grid -mx-1.5" data-animate="fade-up" data-animate-delay="200">
			<?php 
			if ($books) {
				foreach ($books as $book) {
					$book_id = $book->ID;
					$book_image = get_post_thumbnail_id($book_id);
					$book_title = get_the_title($book_id);
					$product = wc_get_product($book_id);
					$price = $product ? $product->get_price_html() : '';
					$book_link = get_permalink($book_id);

					$book_card_args = array(
						'href'       => $book_link,
						'title'      => $book_title,
						'image_id'   => $book_image,
						'price_html' => $price,
						'post_id'    => $book_id,
					);
				?>
					<div class="book-card-wrapper px-1.5">
						<?php include THEME_PATH . '/includes/book-card.php'; ?>
					</div>
				<?php } 
			} ?>
		</div>

		<!-- Pagination dots -->
		<div class="pagination-dots relative" data-animate="fade-up" data-animate-delay="200"></div>
	</div>
</section>