<?php include(THEME_PATH . '/includes/slice-intro.php'); ?>


<div class="steps">

	<div class="wrapper big">

        <?php  if ( have_rows('steps') ) { ?>
            <div class="row justify-content-center">
                <?
                $i = 1;
                while ( have_rows('steps') ) { the_row();
                    $img = get_sub_field('img');
                    $bg_color = get_sub_field('bg-color');
                    $title = get_sub_field('title');
                    $subtitle = get_sub_field('subtitle');
                    $page_link = get_sub_field('page-link');
                ?>

                <div class="col-md-6 col-lg-4">
                    <div class="step-block">
                        <div class="top-img" data-aos="fade-up" data-aos-delay="<?= $i * 100; ?>">
                            <div class="step-title"><?= get_row_index(); ?></div>
                            <?
                            if( get_sub_field('img') ){
                                echo FW::get_image( get_sub_field('img'), '', 'img' );
                            }

                            if( get_sub_field('bg-color') ){
                                echo FW::get_image( get_sub_field('bg-color'), '', 'bg-blob', 'animate__pulse');
                            }
                            ?>
                        </div>

                        <h5 data-aos="fade-up" data-aos-delay="<?= $i * 100; ?>"><?= get_sub_field('subtitle'); ?></h5>

                        <?php if( get_sub_field('pagelink') ) { ?>
                            <div class="page-link">
                                <?= get_sub_field('pagelink'); ?>
                            </div>
                        <? } ?>

                    </div>
                </div>

                <? $i++; } ?>

            </div>

        <? } ?>

	</div>

</div>


<?php include(THEME_PATH . '/includes/slice-outro.php'); ?>
