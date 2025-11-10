<div class="step" data-step-id="<?= ++$field_count; ?>">
	<div class="row align-items-center">
		<div class="col-12 col-sm-6">
			<? if( get_field('workshop-character-2-img', $book_id ) ){ ?>
				<div class="img text-right">
					<?= FW::get_image( get_field('workshop-character-2-img', $book_id ) ); ?>
				</div>
			<? } ?>
		</div>

		<div class="col-12 col-sm-6">
			<div class="dialog-left">
				<? if( get_field('character-1-step-4-content') ){ ?>
					<div class="dialog">
						<div class="the-content"><?= get_field('character-1-step-4-content'); ?></div>

						<div class="buttons">
							<a href="javascript:;" class="btn small red form-step-nav prev">
								<span><?= pll__("Précédent"); ?></span>
							</a>

							<a href="javascript:;" class="btn small green form-step-nav next generate-workshop-book-content">
								<span><?= pll__("Suivant"); ?></span>
							</a>
						</div>
					</div>
				<? } ?>
			</div>
		</div>
	</div>
</div>

<div class="step" data-step-id="<?= ++$field_count; ?>">
	<div id="book-slider" class="workshop-book-holder">
		<a href="javascript:;" class="workshop-book-nav prev hidden"></a>

		<div class="inner"><? /* JS injects here */ ?></div>

		<a href="javascript:;" class="workshop-book-nav next"></a>
	</div>
</div>