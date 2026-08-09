<?php
$book_url   = add_query_arg(['step' => 3, 'book' => get_the_ID()], $workshop_url);
$image_urls = tlth_get_product_flipbook_image_urls( get_the_ID() );
?>

<div class="col-12 col-sm-6 col-lg-4">
	<div class="book-block" data-id="<?= get_the_ID(); ?>">
		<? if( TLTH::featured_image() ){ ?>
			<div class="top">
				<a href="<?= $book_url; ?>">
					<?= TLTH::featured_image(); ?>
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

		<?php if ( ! empty( $image_urls ) ) { ?>
			<div class="flipbook" id="flipbook-<?= get_the_ID(); ?>">
				<?= tlth_render_flipbook_inner( $image_urls ); ?>
			</div>
		<?php } ?>

	</div>
</div>