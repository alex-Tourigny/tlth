<?php

	$title = get_sub_field("title");
	$btn = get_sub_field("countdown-btn");
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

	<div class="countdown-container row gutter-50" data-days-remaining="<?= $days_remaining ?>" data-hours-remaining="<?= $hours_remaining ?>" data-minutes-remaining="<?= $minutes_remaining ?>">
		<div class="col-4">
			<div class="days number">
				<?= $days_remaining ?>
			</div>
			<h4><?= pll__("jours") ?></h4>
		</div>
		<div class="col-4">
			<div class="hours number">
				<?= $hours_remaining ?>
			</div>
			<h4><?= pll__("heures") ?></h4>
		</div>
		<div class="col-4">
			<div class="number minutes">
				<?= $minutes_remaining ?>
			</div>
			<h4><?= pll__("minutes") ?></h4>
		</div>
	</div>

</div>