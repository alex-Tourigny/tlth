<?php
$has_cols = get_sub_field('has-cols') == '1' ? '1' : '2';

if($has_cols == '2') {
	$class_1 = 'col-lg-6 order-1 order-lg-0';
	$class_2 = 'col-lg-6 order-0 order-lg-1';
	$class_3 = '';
} else {
	$class_1 = 'col-12 order-1';
	$class_2 = 'col-12 order-0';
	$class_3 = 'one-col';
}
?>

<div class="page-top">
	<div class="wrapper">
		<div class="page-title" data-aos="fade-up">
			<h1><? the_title(); ?></h1>
		</div>
		<div class="row align-items-center justify-content-center">
			<div class="<?= $class_1 ; ?>">
				<div class="top-content <?= $class_3 ; ?>" data-aos="fade-up">
					<? if( get_sub_field('content-title') ){ ?>
						<h2><?= get_sub_field('content-title'); ?></h2>
					<? } ?>
					<? if( get_sub_field('content') ){ ?>
						<div class="the-content"><?= get_sub_field('content'); ?></div>
					<? } ?>
					<? if( get_sub_field('btn') ){ ?>
						<div class="btn-spacing">
							<?php echo FW::button( get_sub_field('btn'), ['btn', 'red'] ); ?>
						</div>
					<? } ?>
				</div>
			</div>

			<div class="<?= $class_2 ; ?>">
				<div class="page-top-img" data-aos="fade-up">
					<? if( get_sub_field('img') ){ echo FW::get_image( get_sub_field('img') ); } ?>
				</div>
			</div>
		</div>
	</div>
</div>