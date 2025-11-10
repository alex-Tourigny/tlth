<?php
$book_url = add_query_arg(['step' => 3, 'book' => get_the_ID()], $workshop_url);
$images = get_field('preview-gallery');
?>

<div class="col-12 col-sm-6 col-lg-4">
	<div class="book-block" data-id="<?= get_the_ID(); ?>">
		<? if( FW::featured_image() ){ ?>
			<div class="top">
				<a href="<?= $book_url; ?>">
					<?= FW::featured_image(); ?>
				</a>
			</div>
		<? } ?>

		<div class="bottom text-center">
			<h2>
				<a href="<?= $book_url; ?>"><?= get_the_title(); ?></a>
			</h2>

			<? if( get_field('workshop-excerpt') ){ ?>
				<div class="the-content"><?= get_field('workshop-excerpt'); ?></div>
			<? } ?>

			<div class="buttons">
				<a href="javascript:;" data-fancybox data-src="#flipbook-<?= get_the_ID(); ?>" class="btn small blue">
					<span><?= pll__("Aperçu"); ?></span>
				</a>

				<a href="<?= $book_url; ?>" class="btn small red">
					<span><?= pll__("Choisir"); ?></span>
				</a>
			</div>
		</div>

		<?php if( ! empty($images) ){ ?>
			<div class="flipbook" id="flipbook-<?= get_the_ID(); ?>">
				<div class="flipbook-inner">
					<?
					$total_pages = count($images);

					$i = 1;
					foreach($images as $image){
						$is_hard = $i == 1 || $i == 2 || $i == $total_pages - 1 || $i == $total_pages ? true : false; ?>

						<div <? if($is_hard){ ?>class="hard"<? } ?>>
							<?= FW::get_image($image); ?>
						</div>

						<? $i++; } ?>
				</div>
			</div>
		<?php } ?>

	</div>
</div>