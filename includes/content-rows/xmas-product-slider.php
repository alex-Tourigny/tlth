

<div id="sm-container" class="slider-container">
	<?php if ( have_rows('slider') ) { ?>

		<?php while ( have_rows('slider') ) { the_row();
			$background_img = get_sub_field("slide-bg-img");
			$title = get_sub_field("slide-title");
			$title_color = get_sub_field("slide-title-color");
			$content = get_sub_field("slide-text");
			$btn = get_sub_field("slide-btn");
			$btn2 = get_sub_field("slide-btn-2");
			$img = get_sub_field("slide-img");

			?>

			<div class="slide" style="background-image:url(<?= $background_img['url'] ?>); z-index:<?= get_row_index() ?>">
				<div class="wrapper big">
					<div class="row justify-content-center gutter-140">
						<div class="col-12 col-lg-6 col-text">
							<?php if( $title ){ ?>
								<h3 style="color:<?= $title_color ?>;">
									<?= $title ?>
								</h3>
							<?php } ?>

							<?php if( $content ){ ?>
								<div class="the-content">
									<?= $content ?>
								</div>
							<?php } ?>
							<?php if( $btn ){ ?>
								<?= FW::button($btn, ["btn", "red"]) ?>
							<?php } ?>
							<?php if( $btn2 ){ ?>
								<?= FW::button($btn2, ["btn", "red-outline"]) ?>
							<?php } ?>
						</div>
						<div class="col-12 col-lg-6 col-image">
							<?php if( $img ){ ?>
								<div class="image">
									<?= FW::get_image( $img, '', 'img' ) ?>
								</div>
							<?php } ?>
						</div>

					</div>
				</div>
			</div>
		<? } ?>
	<? } ?>
</div>


