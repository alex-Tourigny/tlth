<?php
/*
 * Template Name: L'Atelier
 */
$step = isset($_GET['step']) && ! empty($_GET['step']) ? $_GET['step'] : 1;
$workshop_url = get_permalink( FW::get_page_id_by_template('templates/workshop') );

$book_has_been_created = isset($_POST['book_complete']) && $_POST['book_complete'] == 'true' ? true : false;
if( $book_has_been_created ){
	$_POST = [];
	wp_redirect( add_query_arg(['step' => '2', 'book-added' => 'true'], $workshop_url) );
	exit();
}

if($step >= 3)
{
	$book_id = isset($_GET['book']) && ! empty($_GET['book']) ? $_GET['book'] : false;

	if( ! $book_id ){
		wp_redirect($workshop_url);
		exit();
	}

}

get_header(); if( have_posts() ){ while( have_posts() ){ the_post();

include( THEME_PATH . '/includes/workshop/step-' . $step . '.php');

} } get_footer(); ?>
