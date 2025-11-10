<?php
$classes = [];

$classes[] = 'slice-outro';
$classes[] = 'padding-' . get_sub_field('padding');


?>

<div class="<?= implode(' ', $classes); ?>" id="outro">
	<div class="wrapper big">

		<? if (get_sub_field('end-content')) { ?>
			<h5><?= get_sub_field('end-content'); ?></h5>
		<? } ?>

		<?php if (have_rows('buttons')){ ?>
            <div class="buttons" data-aos="fade-left" data-aos-delay="200">
                <? while (have_rows('buttons')) {
                    the_row();
                    $btn = get_sub_field('btn');

                    ?>

                    <?php
                    if (get_sub_field('btn')) {
                        echo FW::button(get_sub_field('btn'), ['btn', 'red']);
                    }
                     ?>
                <? } ?>
            </div>
		<? } ?>
	</div>
</div>


