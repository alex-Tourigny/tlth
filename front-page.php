<?php
/*
 * Template Name: Page d'accueil
 */

get_header();

if( have_posts() ){

	while( have_posts() ){

		the_post();

		if( have_rows('content-rows') ){ ?>

			<section class="flexible-content">

				<?php
				while( have_rows('content-rows') ){
					the_row();

					include(THEME_PATH . '/includes/content-rows/helpers/open.php');
					include(THEME_PATH . '/includes/content-rows/' . get_row_layout() . '.php');
					include(THEME_PATH . '/includes/content-rows/helpers/close.php');
				}
				?>

			</section>

		<?php }
	}
} ?>

<?php get_footer(); ?>
