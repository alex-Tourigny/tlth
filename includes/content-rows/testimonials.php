<?php
$review_data = get_review_data();

$score = $review_data->result->rating;
$total = $review_data->result->user_ratings_total;


$top_sub_title = sprintf( get_sub_field( 'top-subtitle' ), $score, $total );
$top_title = get_sub_field('title');


include(THEME_PATH . '/includes/slice-intro.php');
?>

<div class="testimonials">
	<div class="wrapper big">
		<div class="testimonials-slider">

			<?php foreach($review_data->result->reviews as $review){ ?>

				<div class="testimonial-block" data-mh="tb">

					<? $stars = $review->rating; ?>
					<div class="stars">
						<? for($i = 1; $i <= 5; $i++){
							if($stars < $i) continue; ?>

							<span class="star"></span>

						<? } ?>
					</div>

					<div class="the-content">
						<?= $review->text; ?>
					</div>

					<h5><?= $review->author_name; ?></h5>
				</div>

			<? } ?>

		</div>
	</div>
</div>

<?php include(THEME_PATH . '/includes/slice-outro.php'); ?>
