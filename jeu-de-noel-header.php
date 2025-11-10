<!doctype html>
<html lang="<?php echo LANG; ?>">
<head>
	<title><?php is_front_page() ? the_title() : wp_title(''); ?> | <?php bloginfo('name'); ?></title>

	<link rel="stylesheet" href="https://use.typekit.net/fje3iel.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;700&display=swap" rel="stylesheet">

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php

$header_big_title = get_field("header-big-title-" . LANG, "option");
$header_small_title = get_field("header-small-title-" . LANG, "option");
$header_small_title_wishes = get_field("header-small-title-wishes-" . LANG, "option");
$header_text = get_field("header-text-" . LANG, "option");
$header_btn = get_field("header-btn-" . LANG, "option");

$header_img_left = get_field("header-img-left", "option");
$header_img_right = get_field("header-img-right", "option");

?>

<header id="header">
	<div class="logo-container wrapper large d-flex justify-content-between align-items-center">

		<?php $logo = get_field( 'logo', 'option' );
		if ( $logo ) { ?>
			<div class="logo">
				<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" />
			</div>
		<?php } ?>

		<div class="language-switcher">
			<?php
			/*wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container' => '',
					'items_wrap' => '%3$s'
				)
			);*/
			?>

			<a class="back-link" href="https://tonlivretonhistoire.ca/<?= LANG == "en" ? "en" : null ?>"><?= file_get_contents( THEME_PATH . "/assets/images/i-arrow-back.svg" ) ?>Retour au site «Ton Livre Ton Histoire» </a>
		</div>

	</div>

	<div class="header-content d-flex flex-wrap">
		<div class="wrapper small">
			<?php if( $header_small_title && $header_big_title ){ ?>
				<h1 class="title">
					<?= $header_small_title ?>
					<br/>
					<span><?= $header_big_title ?></span>
				</h1>
			<?php } ?>

			<?php if( $header_text ){ ?>
				<p><?= $header_text ?></p>
			<?php } ?>
			<?php if ( $header_btn ) { ?>
				<div class="btn-container">
					<a class="button" href="<?php echo esc_url( $header_btn['url'] ); ?>" target="<?php echo esc_attr( $header_btn['target'] ); ?>"><span><?php echo esc_html( $header_btn['title'] ); ?></span></a>
				</div>
			<?php } ?>
		</div>

	</div>

	<?php if( $header_img_left ) { ?>
		<div class="header-img left">
			<?= FW::get_image($header_img_left) ?>
		</div>
	<?php } ?>
	<?php if( $header_img_right ) { ?>
		<div class="header-img right">
			<?= FW::get_image($header_img_right) ?>
		</div>
	<?php } ?>

</header>


<main id="main">