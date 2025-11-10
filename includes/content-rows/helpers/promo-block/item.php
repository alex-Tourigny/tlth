<div class="promo-inner">
	<?
	if( get_sub_field('featured-img') ){
		echo FW::get_image( get_sub_field('featured-img') );
	}
	?>


	<?php if( get_sub_field('title') ){ ?>
		<h2><?= get_sub_field('title'); ?></h2>
	<?php } ?>

	<?php if( get_sub_field('subtitle') ){ ?>
	<h3><?= get_sub_field('subtitle'); ?></h3>
	<?php } ?>

	<?php if( get_sub_field('content') ){ ?>
		<div class="the-content"><?= get_sub_field('content'); ?></div>
	<?php } ?>

	<?php if( get_sub_field('btn') ){ ?>
		<div class="btn-spacing">
			<?php echo FW::button( get_sub_field('btn'), ['btn', 'red'] ); ?>
		</div>
	<? } ?>
</div>
