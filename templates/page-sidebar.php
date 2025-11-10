<?php
/*
 * Template Name: Page avec barre latérale
 */

get_header();

if( have_posts() ){

	while( have_posts() ){

		the_post(); ?>

		<section class="page-top">
			<div class="wrapper">
				<div class="page-title">
					<h1><?= the_title(); ?></h1>
				</div>
			</div>
		</section>

		<div class="inner-page-content">
			<div class="wrapper">
				<div class="row">
					<div class="col-12 col-lg-8 col-xl-7">

						<? if( have_rows('content-rows') ){ ?>

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

						<?php } ?>

					</div>
					<div class="col-12 col-lg-4 col-xl-5">
						<div class="the-sidebar">
							<div class="sidebar__inner">

								<? if( have_rows('sidebar-rows') ){ ?>

									<section class="flexible-content">

										<?php
										while( have_rows('sidebar-rows') ){
											the_row();

											include(THEME_PATH . '/includes/sidebar-rows/helpers/open.php');
											include(THEME_PATH . '/includes/sidebar-rows/' . get_row_layout() . '.php');
											include(THEME_PATH . '/includes/sidebar-rows/helpers/close.php');
										}
										?>

									</section>

								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>




		<?php }
} ?>

<?php get_footer(); ?>
