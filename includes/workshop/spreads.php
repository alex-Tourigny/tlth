<?php
$is_book_confirmation = isset($is_book_confirmation) ? $is_book_confirmation : false;
$total_pages = count($init_book_pages);
$all_images = array_column($init_book_pages, 'image');

$i = 0;

$spreads = array_chunk($init_book_pages, 2);
foreach($spreads as $key => $pages) { ?>

	<div class="spread <?= $is_book_confirmation ? 'is-confirmation' : ''; ?>">
		<div class="pages row no-gutters">
			<a href="javascript:;" class="workshop-book-nav prev"></a>
			<a href="javascript:;" class="workshop-book-nav next"></a>

			<?
			$image_sets = [];
			foreach($pages as $page){ ?>
				<div class="col-12 col-md-6">
					<div class="book-page <?= $page['type']; ?>">
						<? include( THEME_PATH . '/includes/workshop/spreads-content-' . $page['type'] . '.php' ); ?>
					</div>
				</div>
			<? } ?>
		</div>

		<?
		// We'll shuffle the image set in JS for simplicity in markup buildup
		if( ! $is_book_confirmation ) { ?>
			<?php if( ! empty($image_sets) ){ ?>
				<div class="messages">
					<p class="incorrect"><?= pll__("Oups! Ce n'est pas la bonne image. Essaie de nouveau."); ?></p>
					<p class="correct"><?= pll__("Bravo!"); ?></p>
				</div>

				<div class="image-sets row incorrect">
					<div class="col-4">
						<a href="javascript:;" data-status="correct">
							<img src="<?= $image_sets['correct']; ?>">
						</a>
					</div>

					<div class="col-4">
						<a href="javascript:;" data-status="incorrect">
							<img src="<?= $image_sets['incorrect'][0]; ?>">
						</a>
					</div>

					<div class="col-4">
						<a href="javascript:;" data-status="incorrect">
							<img src="<?= $image_sets['incorrect'][1]; ?>">
						</a>
					</div>
				</div>
			<? } ?>
		<? } ?>
	</div>

<?php } ?>