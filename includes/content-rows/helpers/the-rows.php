<?
$cr_page_id = isset($cr_page_id) ? $cr_page_id : get_the_ID();

if( ! have_rows('content-rows', $cr_page_id) ) return; ?>

<section class="flexible-content">

	<?php
	while( have_rows('content-rows', $cr_page_id) ){
		the_row();

		include(THEME_PATH . '/includes/content-rows/helpers/open.php');
		include(THEME_PATH . '/includes/content-rows/' . get_row_layout() . '.php');
		include(THEME_PATH . '/includes/content-rows/helpers/close.php');
	}
	?>

</section>