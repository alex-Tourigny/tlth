<?php
$faq_categories = get_terms('faq-category');

foreach($faq_categories as $faq_category){ ?>

	<h2><?= $faq_category->name; ?></h2>

	<?php
	$questions = new WP_Query(
		array(
			'post_type' => 'faq',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => $faq_category->taxonomy,
					'terms' => $faq_category->term_id
				)
			)
		)
	);

	if( $questions->have_posts() ){
		while( $questions->have_posts() ){ $questions->the_post(); ?>

		<div class="dropdown">
			<div class="dropdown-content">
				<h5>
					<?= get_the_title(); ?>
					<span class="open-drop"><?= file_get_contents( THEME_PATH . '/assets/images/dropdown.svg');?></span>
				</h5>

				<div class="the-content"><?= the_content(); ?> </div>
			</div>
		</div>

	<?php } } wp_reset_postdata(); ?>

<? } ?>
