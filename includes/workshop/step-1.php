<section class="workshop-slice workshop-landing">
	<?
	if( get_field('workshop-bg') ){
		echo FW::get_image( get_field('workshop-bg'), '', 'bg' );
	}
	?>

	<div class="wrapper ws-intro">
		<div class="stage">
			<div class="row align-items-end">
				<div class="d-none d-lg-flex col-lg-4">
					<div class="character character-2 with-dialog dialog-right dialog-size-small">
						<? if( get_field('character-2-step-1-content') ){ ?>
							<div class="dialog">
								<div class="the-content"><?= get_field('character-2-step-1-content'); ?></div>
							</div>
						<? } ?>

						<? if( get_field('character-2-img') ) { ?>
							<div class="img">
								<?= FW::get_image( get_field('character-2-img') ); ?>
							</div>
						<? } ?>
					</div>
				</div>

				<div class="col-12 col-lg-8">
					<div class="character character-1 with-dialog dialog-left dialog-size-normal">
						<? if( get_field('character-1-img') ) { ?>
							<div class="img">
								<?= FW::get_image( get_field('character-1-img') ); ?>
							</div>
						<? } ?>

						<? if( get_field('character-1-step-1-content') ){ ?>

							<div class="dialog">
								<div class="the-content"><?= get_field('character-1-step-1-content'); ?></div>
							</div>

						<? } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="stage-under text-center">
			<a href="<?= add_query_arg('step', 2, $workshop_url ); ?>" class="btn purple">
				<span><?= pll__("Commencer l'activité!"); ?></span>
			</a>
		</div>
	</div>
</section>