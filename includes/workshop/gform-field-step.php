<?php
$field_type = $field->type;
$field_id = $field->id;
$field_label = $field->description;

$is_select_field = $field_type == 'radio' || $field_type == 'select' || $field_type == 'checkbox' ? true : false;

$field_type = $is_select_field ? 'radio' : $field_type;
?>

<div class="step" data-step-id="<?= $gform_step_id; ?>" data-step-type="<?= $field_type; ?>">

	<div class="row align-items-center">
		<div class="col-12 col-sm-6 order-1 order-sm-0">
			<div class="gform-question dialog-right">
				<? if( $field_type == 'text' || $field_type == 'number' ){ ?>
					<div class="dialog">
						<input type="<?= $field_type; ?>" name="input_<?= $field_id; ?>" placeholder="<?= $field_label; ?>">
					</div>
				<? } ?>

				<? if( $is_select_field ){ ?>
					<div class="dialog">

						<? if( ! empty($field->choices) ){ ?>

							<div class="faux-radios">
								<p><?= $field_label; ?></p>

								<? foreach($field->choices as $choice){ ?>
									<label class="faux-radio">
										<input type="radio" name="input_<?= $field_id; ?>" value="<?= $choice['value']; ?>">
										<span><?= $choice['text']; ?></span>
									</label>
								<? } ?>
							</div>

						<? } ?>
					</div>
				<? } ?>

				<div class="buttons">
					<a href="javascript:;" class="btn small red form-step-nav prev">
						<span><?= pll__("Précédent"); ?></span>
					</a>

					<a href="javascript:;" class="btn small green form-step-nav next">
						<span><?= pll__("Suivant"); ?></span>
					</a>
				</div>
			</div>
		</div>

		<div class="col-12 col-sm-6 order-0 order-sm-1">
			<? if( get_field('workshop-character-img', $book_id ) ){ ?>
				<div class="img">
					<?= FW::get_image( get_field('workshop-character-img', $book_id ) ); ?>
				</div>
			<? } ?>
		</div>
	</div>

</div>
