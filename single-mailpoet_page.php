<?php

get_header();

if( have_posts() ){

	while( have_posts() ){

		the_post(); ?>

		<div class="wrapper">

			<div class="mailpoet-content-block">

				<?= the_content();?>

			</div>

		</div>

		<?php
	}
} ?>

<?php get_footer(); ?>