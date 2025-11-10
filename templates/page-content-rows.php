<?php
/*
 * Template Name: Page avec rangées de contenu
 */

get_header();

if( have_posts() ){

	while( have_posts() ){

		the_post();

		include(THEME_PATH . '/includes/content-rows/helpers/the-rows.php');
	}
} ?>

<?php get_footer(); ?>