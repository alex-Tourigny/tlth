<section class="workshop-slice workshop-landing">
	<div class="wrapper big" data-step="2">
		<div class="stage">
			<? if( isset($_GET['book-added']) && $_GET['book-added'] == 'true' ){ ?>
				<div class="book-added-message"><?= pll__("Félicitations! Ton livre a été ajouté au panier!"); ?></div>
			<? } ?>

			<? if( get_field('step-2-title') ){ ?>
				<div class="stage-intro text-center">
					<h1><?= get_field('step-2-title'); ?></h1>
				</div>
			<? } ?>

			<?
			$workshop_books_acf = get_field('step-2-books');
			$workshop_books_ids = $workshop_books_acf ? wp_list_pluck($workshop_books_acf, 'ID') : [];

			$workshop_books = new WP_Query(
				array(
					'post_type' => 'product',
					'posts_per_page' => -1,
					'post__in' => $workshop_books_ids,
					'orderby' => 'post__in'
				)
			);

			if( $workshop_books->have_posts() ){ ?>

				<div class="row justify-content-center">
					<?
					while( $workshop_books->have_posts() ){
						$workshop_books->the_post();

						include( THEME_PATH . '/includes/workshop/book-block.php' );
					}
					?>
				</div>

			<? } ?>
		</div>
	</div>
</section>