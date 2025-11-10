<?php get_header(); ?>

<div class="hero">
	<div class="wrapper">

		<div class="the-content">
			<?php echo get_field('404-content-' . LANG, 'option'); ?>
		</div>
	</div>
</div>


<?php get_footer(); ?>