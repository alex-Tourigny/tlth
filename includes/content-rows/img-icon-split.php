<?php
if (get_sub_field('img-side') == 'right') {
	$class_1 = 'order-0 order-lg-1';
	$class_2 = 'order-1 order-lg-0';
} else {
	$class_1 = 'order-0';
	$class_2 = 'order-1';
}

$col_count = get_sub_field('col-count');
?>


<div class="img-icon-split">
    <div class="wrapper big">
        <div class="row align-items-center">

            <div class="col-12 col-lg-5 <?= $class_1; ?>">
                <div class="icon-feature-img" data-aos="fade-up">
					<?
					if (get_sub_field('featured-img')) {
						echo FW::get_image(get_sub_field('featured-img'));
					}
					?>
                </div>
            </div>

			<?php if (have_rows('icon-block')) { ?>
                <div class="col-12 col-lg-7 <?= $class_2; ?>">
                    <div class="row align-items-center justify-content-center">

                        <?php
						$i = 1;
                        while( have_rows('icon-block') ){ the_row();
                            $icon = get_sub_field('icon');
                            $title = get_sub_field('title');
                            $content = get_sub_field('content'); ?>

                            <div class="col-sm-<?= $col_count; ?>" data-aos="fade-up" data-aos-delay="<?= $i * 50; ?>">
                                <div class="icon-block">
                                    <? if (get_sub_field('icon')) {
                                        echo FW::get_image(get_sub_field('icon'));
                                        }
                                    ?>

                                    <h4><?= get_sub_field('title'); ?></h4>

                                    <div class="the-content"><?= get_sub_field('content'); ?></div>
                                </div>
                            </div>
                        <? $i++; } ?>

                    </div>
                </div>
			<? } ?>

        </div>
    </div>
</div>