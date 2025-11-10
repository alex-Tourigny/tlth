<?php
$images = get_field('preview-gallery');
if( ! empty($images) ){ ?>

	<div id="flipbook" class="flipbook">
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

	<a href="javascript:;" data-fancybox data-src="#flipbook" class="btn red"><?= pll__('view-product-preview'); ?></a>
<?php } ?>
