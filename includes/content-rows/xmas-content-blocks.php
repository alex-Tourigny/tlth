<?php

$title = get_sub_field("slice-title");
$subtitle = get_sub_field("sub-title");
$date = get_sub_field("countdown-end-date");
$date_obj = DateTime::createFromFormat('Y-m-d H:i:s', $date, wp_timezone() );
$timestamp_date = $date_obj->format('U');

$date = strtotime($date);
$remaining = $timestamp_date - time();
$days_remaining = floor($remaining / 86400);
$hours_remaining = floor(($remaining % 86400) / 3600) - 1;
$minutes_remaining = floor(($remaining % 3600) / 60);

?>


<div class="wrapper medium">

	<?php if( $title || $subtitle ){ ?>
        <div class="slice-intro padding-bottom">
			<?php if( $title ){ ?>
				<h3><?= $title ?></h3>
			<?php } ?>
			<?php if( $subtitle ){ ?>
				<h3 class="subtitle"><?= $subtitle ?></h3>
			<?php } ?>
        </div>
	<?php } ?>

    <?php if( $date ){ ?>
        <div class="countdown-container" data-days-remaining="<?= $days_remaining ?>" data-hours-remaining="<?= $hours_remaining ?>" data-minutes-remaining="<?= $minutes_remaining ?>">
            <div class="row gutter-24">
                <div class="col-4">
                    <div class="days number">
                        <?= $days_remaining ?>
                    </div>
                    <span><?= pll__("Jours") ?></span>
                </div>
                <div class="col-4">
                    <div class="hours number">
                        <?= $hours_remaining ?>
                    </div>
                    <span><?= pll__("Heures") ?></span>
                </div>
                <div class="col-4">
                    <div class="number minutes">
                        <?= $minutes_remaining ?>
                    </div>
                    <span><?= pll__("Minutes") ?></span>
                </div>
            </div>
        </div>
	<?php } ?>

	<?php if ( have_rows('content-blocks') ) { ?>
		<div class="row justify-content-center content-block-row">
			<?
			$i = 1;
			while ( have_rows('content-blocks') ) { the_row();
				$img = get_sub_field('icon');
				$bg_color = get_sub_field('bg-color');
				$title = get_sub_field('title');
				$text = get_sub_field('text');
				$title_color = get_sub_field('title-color');

				?>

				<div class="col-12 col-md-6">
					<div class="content-block" style="background: <?= $bg_color ?>">

						<?php if( $img ){ ?>
							<div class="image">
								<?= FW::get_image( $img, '', 'img' ) ?>
							</div>
						<?php } ?>

						<?php if( $title ){ ?>
							<h3 style="color:<?= $title_color ?>;">
								<?= $title ?>
							</h3>
						<?php } ?>

						<?php if( $text ){ ?>
							<div class="the-content">
								<?= $text ?>
							</div>
						<?php } ?>

					</div>
				</div>

				<? $i++; } ?>

		</div>

	<? } ?>
</div>

<?php if( get_sub_field("btn") ){ ?>
	<div class="slice-outro padding-both">
		<div class="wrapper big">
			<?= FW::button( get_sub_field("btn"), ['btn', 'red']) ?>
		</div>
	</div>
<?php } ?>