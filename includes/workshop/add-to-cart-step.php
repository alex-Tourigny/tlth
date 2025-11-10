<div class="step" data-step-id="<?= ++$field_count; ?>">
	<div class="row align-items-center">
		<div class="col-12 col-sm-6">
			<? if( get_field('workshop-character-2-img', $book_id) ) { ?>
				<div class="img">
					<?= FW::get_image( get_field('workshop-character-2-img', $book_id) ); ?>
				</div>
			<? } ?>
		</div>

		<div class="col-12 col-sm-6">
			<? if( get_field('character-1-step-6-content') ){ ?>
				<div class="dialog-left dialog-size-normal">
					<div class="dialog">
						<div class="the-content"><?= get_field('character-1-step-6-content'); ?></div>

						<div class="buttons">
							<a href="<?= $workshop_url; ?>" class="btn small red">
								<span><?= pll__("Retour à l'atelier"); ?></span>
							</a>

							<a href="javascript:;" class="add-workshop-book-to-cart btn small green">
								<span><?= pll__("Ajouter au panier"); ?></span>
							</a>
						</div>
					</div>
				</div>
			<? } ?>
		</div>
	</div>
</div>