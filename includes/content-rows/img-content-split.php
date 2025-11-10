<?php
if( get_sub_field('img-side') == 'right' ){
    $class_1 = 'order-0 order-lg-1';
    $class_2 = 'order-1 order-lg-0';
} else {
	$class_1 = 'order-0';
	$class_2 = 'order-1';
}
?>

<div class="c2a">

	<? if( get_sub_field('background-img') ){ echo FW::get_image( get_sub_field('background-img') ); } ?>

    <? if( get_sub_field('overlay-color') ){ ?>
        <div class="overlay" style="background-color: <?= get_sub_field('overlay-color'); ?>; <?= get_sub_field('overlay-opacity') ? 'opacity: ' . get_sub_field('overlay-opacity') . ';' : ''; ?>"></div>
    <? } ?>

    <div class="wrapper big">

        <div class="row align-items-center">

            <div class="col-12 col-lg-6 <?= $class_1; ?>">

                <div data-aos="fade-up">
					<?
					if( get_sub_field('featured-img') ){
						echo FW::get_image( get_sub_field('featured-img') );
					}
					?>
                </div>

            </div>

            <div class="col-12 col-lg-6 <?= $class_2; ?>">

                <div class="c2a-content">

					<div data-aos="fade-up">
						<?
						if( get_sub_field('content-img') ){
							echo FW::get_image( get_sub_field('content-img'), (''), ('c2a-content-img')  );
						}
						?>
                    </div>

					<?php if( get_sub_field('subtitle') ) { ?>
                        <h4 data-aos="fade-up" data-aos-delay="200" style="color: <?=get_sub_field('content-color'); ?>"><?= get_sub_field('subtitle'); ?></h4>
					<?php } ?>

                    <? if( get_sub_field('title') ){ ?>
                        <h2 data-aos="fade-up" data-aos-delay="200" style="color: <?=get_sub_field('content-color'); ?>"><?= get_sub_field('title'); ?></h2>
                    <? } ?>

                    <div class="the-content" data-aos="fade-up" data-aos-delay="400" style="color: <?=get_sub_field('content-color'); ?>"><?= get_sub_field('content'); ?></div>

                    <div class="btn-spacing" data-aos="fade-up" data-aos-delay="500">
						<?php echo FW::button( get_sub_field('btn'), ['btn', 'red'] ); ?>
                    </div>

                </div>

            </div>

        </div>


    </div>


</div>