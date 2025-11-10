<div class="step confirmation" data-step-id="<?= ++$field_count; ?>">
	<div class="row align-items-center">
		<div class="col-12 col-sm-6">
			<? if( get_field('workshop-character-2-img', $book_id) ) { ?>
				<div class="img">
					<?= FW::get_image( get_field('workshop-character-2-img', $book_id) ); ?>
				</div>
			<? } ?>
		</div>

		<div class="col-12 col-sm-6">
			<? if( get_field('character-1-step-5-content') ){ ?>
				<div class="dialog-left dialog-size-normal">
					<div class="dialog">
						<div class="the-content"><?= get_field('character-1-step-5-content'); ?></div>

						<div class="buttons">
							<a href="javascript:;" class="btn small red form-step-nav prev">
								<span><?= pll__("Précédent"); ?></span>
							</a>

							<a href="javascript:;" class="workshop-verify-book btn small green">
								<span><?= pll__("Vérifier mon livre"); ?></span>
							</a>
						</div>
					</div>
				</div>
			<? } ?>
		</div>
	</div>

	<div id="workshop-confirmation-book" class="workshop-book-holder">
		<div class="inner">
			<? /* JS injects here */ ?>
		</div>

		<a href="javascript:;" class="btn small green confirming-book">
			<span><?= pll__("J'ai vérifié mon livre"); ?></span>
		</a>
	</div>
</div>