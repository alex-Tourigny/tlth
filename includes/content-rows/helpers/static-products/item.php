<? if( have_rows('static-products') ){ ?>

	<? while( have_rows('static-products') ){ the_row();
		$link = get_sub_field('prod-link');
		$img = get_sub_field('prod-img');
		$title = get_sub_field('prod-title');
		$subtitle = get_sub_field('prod-subtitle');
		$btn = get_sub_field('prod-btn');
		?>
		<div class="col-12 col-md-4">
			<div class="product-block static">

				<a href="<?= get_sub_field('prod-link');?>">
					<? if( get_sub_field('prod-img') ) {
						echo FW::get_image( get_sub_field('prod-img') );
					} ?>
				</a>

				<? if( get_sub_field('prod-title')){ ?>
					<h2 data-mh="product-title"><?=get_sub_field('prod-title'); ?></h2>
				<? } ?>
				<? if( get_sub_field('prod-subtitle')){ ?>
					<h4 data-mh="product-subtitle"><?=get_sub_field('prod-subtitle'); ?></h4>
				<? } ?>
			</div>
		</div>
	<? } ?>

<? } ?>
