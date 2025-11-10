<?php
/*
 * Template Name: Confirmation prix réclamé
 */
include( THEME_PATH . "/jeu-de-noel-header.php");

session_start();
if(isset($_SESSION['views']))
    $_SESSION['views'] = $_SESSION['views'] + 1;
else
    $_SESSION['views'] = 1;


$prize = $_GET['id'];
$prize_value = get_the_title($prize);
$currentPrizeQty = get_field("price_qty", $prize);

//if ($_SESSION['views'] == 1) {
    update_field("price_qty", $currentPrizeQty - 1, $prize);

//}

$confirmation_big_title = get_field("confirmation-big-title");
$confirmation_text = get_field("confirmation-text");
$confirmation_img = get_field("confirmation-img");

?>
<div class="slice confirmation">
    <div class="wrapper medium">

        <div class="row gutter-80">
            <div class="col-12 col-md-4 text-center">
                <div class="img-container">
					<?= FW::get_image($confirmation_img) ?>
                </div>

            </div>
            <div class="col-12 col-md-8">
                <div class="the-content">
					<?php if( $confirmation_big_title ){ ?>
                        <h2 class="big-title"><?= $confirmation_big_title ?></h2>
					<?php } ?>
					<?php if( $confirmation_text ){ ?>
                        <p><?= $confirmation_text ?></p>
					<?php } ?>
                </div>
            </div>
        </div>



    </div>

</div>

<?php include( THEME_PATH . "/jeu-de-noel-footer.php"); ?>