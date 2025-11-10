<?php
$has_wrap = get_sub_field('has-wrap');

if($has_wrap){
	$class_wrap = 'wrapper small';
} else{
	$class_wrap = '';
}

?>

<div class="<?= $class_wrap; ?>">
	<? if( get_sub_field('form-title') ){ ?>
		<h2><?= get_sub_field('form-title'); ?></h2>
	<? } ?>

	<? if( get_sub_field('form-desc') ){ ?>
		<div class="the-content"><?= get_sub_field('form-desc'); ?></div>
	<? } ?>
</div>

<div class="<?= $class_wrap; ?>">
	<? gravity_form( get_sub_field('form-id'), false, false, false, [], true ); ?>
</div>
