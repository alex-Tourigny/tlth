<?php
/*
 * Template Name: Jeu de noël
 */
include( THEME_PATH . "/jeu-de-noel-header.php");

$infos_big_title = get_field("infos-big-title");
$infos_small_title = get_field("infos-small-title");
$infos_table_title = get_field("infos-table-title");
$infos_text = get_field("infos-text");

$notice_big_title = get_field("notice-big-title");
$notice_small_title = get_field("notice-small-title");

$mobile_big_title = get_field("mobile-big-title");
$mobile_small_title = get_field("mobile-small-title");


$args = array(
	'post_type' => 'prix',
	'posts_per_page' => -1,
);

$prize_array = [];
$prize_rarity_array = [];
$prize_name = "";

$prizes = new WP_Query($args);

if ($prizes->have_posts()) {

	while ($prizes->have_posts()) {
		$prizes->the_post();

		if (get_field("price_qty") > 0) {

			$prize_array[] = get_the_ID();
			$prize_rarity_array[] = get_field("prize-rarity");

		}

	}
	wp_reset_postdata();

}

$prize = weighted_random($prize_array, $prize_rarity_array);

//echo get_the_title($prize);
?>

	<div class="slice game-infos">
		<div class="wrapper">
			<div class="row gutter-120">
				<div class="col-12 col-lg-7 col-game">

					<?php if( $infos_big_title && $infos_small_title ){ ?>
						<h2 class="title text-center">
							<?= $infos_small_title ?>
							<br>
							<span><?= $infos_big_title ?></span>
						</h2>
					<?php } ?>

					<?php if( $infos_text ){ ?>
						<div class="the-content">
							<?= $infos_text ?>
						</div>
					<?php } ?>
					<div id="game" class="slice game d-flex justify-content-center align-items-center wrapper">

						<div class="game-content d-flex flex-wrap justify-content-center">

						</div>

					</div>

				</div>
				<div class="col-12 col-lg-5">
					<div class="prize-container">
						<?php if( $infos_table_title ){ ?>
							<h3 class="prizes-title" colspan="3"><?= $infos_table_title ?></h3>
						<?php } ?>

						<?php if( have_rows("prize-table") ){
							while( have_rows("prize-table") ){
								the_row(); ?>
								<div class="prize-block">
									<div class="img-container">
										<?= FW::get_image(get_sub_field("prize-img")) ?>
									</div>
									<h3><?= get_sub_field("prize-title") ?></h3>
									<p>
										<?= get_sub_field("prize-description") ?>
									</p>
								</div>

							<?php } } ?>


					</div>

				</div>
			</div>

		</div>

		<div class="send-prize">
			<form action="/prix-gagne#prize" method="post" name="prizeForm" id="prizeForm">
				<input id="thePrize" name="thePrize" type="hidden" value="<?php echo $prize; ?>">
				<input id="isWinner" name="isWinner" type="hidden" value="">
			</form>
		</div>
	</div>

    <div class="slice game-notice">
		<?php if( $notice_big_title ){ ?>
            <h2 class="big-title"><?= $notice_big_title ?></h2>
		<?php } ?>
		<?php if( $notice_small_title ){ ?>
            <p><?= $notice_small_title ?></p>
		<?php } ?>
    </div>


<?php include( THEME_PATH . "/jeu-de-noel-footer.php"); ?>