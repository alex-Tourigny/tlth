<div class="wrapper big">
<?php if( get_sub_field('content') ){ ?>
	<div class="the-content"><?= get_sub_field('content');?></div>
<?php } ?>
	<? if( the_content() ){?>
		<div class="the-content"><?= the_content(); ?></div>
	<? } ?>
</div>
