<div class="affiliate">
	<div class="wrapper">
		<div class="row align-items-center">
			<div class="col-lg-6">
				<div class="affiliate-content">
					<? if( get_sub_field('subtitle') ){ ?>
						<h3><?= get_sub_field('subtitle'); ?></h3>
					<? } ?>

					<? if( get_sub_field('title') ){ ?>
						<h2><?= get_sub_field('title');?></h2>
					<? } ?>

					<? if( get_sub_field('content') ){ ?>
						<div class="the-content"><?= get_sub_field('content'); ?></div>
					<? } ?>

					<? if( get_sub_field('btn-title') ){ ?>
						<h3 class="btn-title"><?= get_sub_field('btn-title'); ?></h3>
					<? } ?>

					<?php if( get_sub_field('btn') ){ ?>
						<div class="btn-spacing">
							<?php echo FW::button( get_sub_field('btn'), ['btn', 'red'] ); ?>
						</div>
					<? } ?>
				</div>
			</div>

			<div class="col-lg-6">
					<div class="promo-block" style="background-color:<?= get_sub_field('bg-color'); ?>">

						<? if( get_sub_field('bg-img') ){
							echo FW::get_image( get_sub_field('bg-img') );
						} ?>

					<? if( have_rows('promo-block') ){ ?>
						<? while( have_rows('promo-block') ){ the_row();
							$title = get_sub_field('title');
							$subtitle = get_sub_field('subtitle');
							$content = get_sub_field('content');
							$btn = get_sub_field('btn');
							$img = get_sub_field('featured-img');
							?>
							<div class="promo-block-content">
								<? include( THEME_PATH . '/includes/content-rows/helpers/promo-block/item.php'); ?>
							</div>
						<? } ?>
					</div>
				<? } ?>
			</div>
		</div>
	</div>
</div>