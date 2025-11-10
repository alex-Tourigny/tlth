<?php
if( get_sub_field('img-side') == 'right' ){
	$class_1 = 'order-0 order-lg-1';
	$class_2 = 'order-1 order-lg-0';
} else {
	$class_1 = 'order-0';
	$class_2 = 'order-1';
}
?>

<div class="wrapper">
	<div class="c2a-section-block">

		<? if( get_sub_field('bg-img') ){ echo FW::get_image( get_sub_field('bg-img') ); } ?>

		<? if( get_sub_field('overlay-color') ){ ?>
			<div class="overlay" style="background-color: <?= get_sub_field('overlay-color'); ?>; <?= get_sub_field('overlay-opacity') ? 'opacity: ' . get_sub_field('overlay-opacity') . ';' : ''; ?>"></div>
		<? } ?>

		<div class="wrapper">
			<div class="row align-items-center justify-content-center">
				<div class="col-12 col-md-6 <?= $class_1; ?>">
					<div class="c2a-feature-img">
						<?
						if( get_sub_field('featured-img') ){
							echo FW::get_image( get_sub_field('featured-img') );
						}
						?>
					</div>
				</div>

				<div class="col-12 col-md-6 <?= $class_2; ?>">
					<div class="c2a-content">
						<? if( get_sub_field('title') ){ ?>
							<h2 data-aos="fade-up" data-aos-delay="200" style="color: <?=get_sub_field('content-color'); ?>"><?= get_sub_field('title'); ?></h2>
						<? } ?>

						<? if( get_sub_field('btn') ){ ?>
						<div class="btn-spacing">
							<?php echo FW::button( get_sub_field('btn'), ['btn', 'red'] ); ?>
						</div>
						<? } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>