<?php
/*
 * Template Name: Formulaire prix gagné
 */
include( THEME_PATH . "/jeu-de-noel-header.php");

$form = get_field("prize-form");
$prize_disclaimer = get_field("prize-disclaimer");

if (isset($_POST['isWinner']) && isset($_POST['thePrize'])) {

    if ($_POST['isWinner'] == "1") {
	    $isWinner = true;

    } else {
        $isWinner = false;
    }

}

if ($isWinner) {
    $prize = $_POST['thePrize'];

    if( isset($_GET['thePrize']) ){
		$prize = $_GET['thePrize'];
    }
    $prize_value = get_the_title($prize);

	$winner_big_title = get_field("winner-big-title");
	$winner_prize = get_field("winner-prize");

?>

    <div class="wrapper small winner d-flex flex-column justify-content-center align-items-center">

	    <?php if( $winner_big_title ){ ?>
            <h3 class="big-title"><?= $winner_big_title ?></h3>
	    <?php } ?>


        <div class="prize-container">
            <div class="prize-img-container">
                <?= FW::get_image(get_field("prize-img", $prize)) ?>
            </div>

            <div class="winner-text-container"><?= get_field("prize-winner-text", $prize) ?></div>
        </div>



    </div>

<?php
} else {
    $prize = get_field("participation-prize-fr", "option")->ID;
	$prize_value = get_the_title($prize);
	$loser_big_title = get_field("loser-big-title");
	$loser_prize = get_field("loser-prize");
?>
    <div class="wrapper loser d-flex flex-column justify-content-center align-items-center">
		<?php if( $loser_big_title ){ ?>
            <h3 class="big-title"><?= $loser_big_title ?></h3>
		<?php } ?>
		<?php if( $loser_prize ){ ?>
            <p><?= $loser_prize ?></p>
		<?php } ?>

        <div class="prize-container">
            <div class="prize-img-container">
                <?= FW::get_image(get_field("prize-img", $prize)) ?>
            </div>

            <div class="winner-text-container"><?= get_field("prize-winner-text", $prize) ?></div>
        </div>

    </div>
<?php
}
?>

<div class="wrapper small" id="prize">
	<?php gravity_form( $form['id'], false, false, false, array("prix_gagne" => $prize_value, "id_prix_gagne" => $prize), true ); ?>
</div>


<?php include( THEME_PATH . "/jeu-de-noel-footer.php"); ?>
