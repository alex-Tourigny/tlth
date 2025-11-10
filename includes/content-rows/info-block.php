<?php
$has_cols = get_sub_field('has-cols') == '1' ? '1' : '2';

if($has_cols == '2') {
	$class_1 = 'col-md-6 info-col';
} else {
	$class_1 = 'col-12 info-no-col';
}
?>

<div class="info-block">

	<? if( get_sub_field('bg-img') ){ echo FW::get_image( get_sub_field('bg-img') ); } ?>

	<? if( get_sub_field('overlay-color') ){ ?>
		<div class="overlay" style="background-color: <?= get_sub_field('overlay-color'); ?>; <?= get_sub_field('overlay-opacity') ? 'opacity: ' . get_sub_field('overlay-opacity') . ';' : ''; ?>"></div>
	<? } ?>

	<div class="wrapper big">
		<div class="info-block-inner">
			<? if( have_rows('info-block') ){ ?>
				<div class="row align-items-center justify-content-center">
					<? while( have_rows('info-block') ){ the_row();
						$img = get_sub_field('img');
						$title = get_sub_field('title');
						$content = get_sub_field('content');
						?>
						<div class="<?= $class_1 ; ?>">
							<div class="info-content">
								<div class="wrapper">
									<div class="info-img" data-mh="ii">
										<? if ( get_sub_field('img') ){ echo FW::get_image( get_sub_field('img'), '', 'img' ); } ?>
									</div>
									<? if( get_sub_field('title') ){ ?>
										<h2><?= get_sub_field('title'); ?></h2>
									<? } ?>
									<? if( get_sub_field('content') ){ ?>
										<div class="the-content"><?= get_sub_field('content'); ?></div>
									<? } ?>
								</div>
							</div>
						</div>
					<? } ?>
				</div>
			<? } ?>

			<div class="btn-spacing" data-aos="fade-up">
				<?php echo FW::button( get_sub_field('btn'), ['btn', 'red'] ); ?>
			</div>
		</div>

	</div>
</div>