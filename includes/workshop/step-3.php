<?php
$product = wc_get_product( $book_id );

$gform_data = get_post_meta( $book_id, '_gravity_form_data', true);
$gform_id = $gform_data['id'];
$gform = GFAPI::get_form($gform_id);
$gform_fields = $gform['fields'];

$fields = [];
//$message = [];

if( ! empty($gform_fields) )
{
	$accepted_field_types = ['text', 'number', 'radio', 'select'];

	foreach($gform_fields as $gform_field)
	{
		$field_type = $gform_field->type;
		$css_classes = explode(' ', $gform_field->cssClass );

		// Add hidden field for message
		/*if( in_array( 'message', $css_classes) ){
			$message = $gform_field;
		}*/

		// Clean out unwanted field types
		if( ! in_array($field_type, $accepted_field_types) ) continue;

		// Do not add fields that have no-workshop class
		if( in_array( 'no-workshop', $css_classes) ) continue;

		$fields[] = $gform_field;
	}
}

$field_count = count($fields);

$workshop_attributes = get_field('workshop-attributes');
if( ! empty($workshop_attributes) ){

	$the_workshop_atts = [];
	foreach($workshop_attributes as &$workshop_attribute)
	{
		$the_workshop_atts[$workshop_attribute['slug']] = $workshop_attribute['value'];
	}
}
?>

<section class="workshop-slice workshop-landing">
	<?
	if( get_field('workshop-bg') ){
		echo FW::get_image( get_field('workshop-bg'), '', 'bg' );
	}
	?>

	<form method="post" enctype="multipart/form-data" data-product_id="<?= $book_id; ?>" action="<?= $workshop_url; ?>">
		<?php wp_nonce_field( '_gform_submit_nonce_' . $gform_id, '_gform_submit_nonce_' . $gform_id, false ); ?>
		<?php wp_nonce_field( '_wpnonce', '_wpnonce', ); ?>

		<input type="hidden" name="is_workshop" value="true">
		<input type="hidden" name="book_complete" value="true">

		<input type="hidden" name="quantity" value="1">
		<input type="hidden" name="product_id" value="<?= $book_id; ?>">

		<input type="hidden" name="wc_gforms_form_id" value="<?= $gform_id; ?>">
		<input type="hidden" name="wc_gforms_next_page" value="0">
		<input type="hidden" name="wc_gforms_previous_page" value="0">
		<input type="hidden" name="wc_gforms_product_type" value="<?= $product->get_type(); ?>">
		<input type="hidden" name="wc_gforms_next_page" value="0">
		<input type="hidden" name="wc_gforms_previous_page" value="0">

		<input type="hidden" name="gform_form_id" value="<?= $gform_id; ?>">
		<input type="hidden" name="gform_old_submit" value="<?= $gform_id; ?>">
		<input type="hidden" name="gform_unique_id" value="">
		<input type="hidden" name="gform_target_page_number_<?= $gform_id; ?>" value="0">
		<input type="hidden" name="gform_source_page_number_<?= $gform_id; ?>" value="1">
		<input type="hidden" name="gform_field_values" value="">
		<input type="hidden" name="is_submit_<?= $gform_id; ?>" value="1">

		<input type="hidden" name="state_<?= $gform_id; ?>" value="<?= GFFormDisplay::get_state( $gform, '' ); ?>">
		<input type="hidden" name="variation_id" value="<?= find_matching_product_variation_id($book_id, $the_workshop_atts); ?>">

		<? if( ! empty($the_workshop_atts) ){ ?>
			<? foreach($the_workshop_atts as $slug => $value){ ?>
				<input type="hidden" name="<?= $slug; ?>" value="<?= $value; ?>">
			<? } ?>
		<? } ?>

		<? /* if( ! empty($message) ){ ?>
			<input type="hidden" name="input_<?= $message['id']; ?>" value="<?= htmlspecialchars( get_field('workshop-message', $book_id), ENT_QUOTES); ?>">
			<input type="hidden" name="book_message" value="<?= htmlspecialchars( get_field('workshop-message', $book_id), ENT_QUOTES); ?>">
		<? } */ ?>

		<div class="wrapper small" data-step="3">
			<div class="stage">
				<div class="form-steps">
					<div class="step active" data-step-id="0">
						<div class="character character-1 with-dialog dialog-left dialog-size-normal">
							<? if( get_field('character-1-img') ) { ?>
								<div class="img">
									<?= FW::get_image( get_field('character-1-img') ); ?>
								</div>
							<? } ?>

							<? if( get_field('character-1-step-3-content') ){ ?>

								<div class="dialog">
									<div class="the-content"><?= get_field('character-1-step-3-content'); ?></div>

									<a href="javascript:;" class="form-step-nav next btn small green"><?= pll__("Continuer"); ?></a>
								</div>

							<? } ?>
						</div>
					</div>

					<?
					if( ! empty($fields) ){

						$gform_step_id = 1;
						foreach($fields as $field)
						{
							include( THEME_PATH . '/includes/workshop/gform-field-step.php' );

							$gform_step_id++;
						}
					}
					?>

					<?
					include( THEME_PATH . '/includes/workshop/images-step.php' );
					include( THEME_PATH . '/includes/workshop/confirmation-step.php' );
					include( THEME_PATH . '/includes/workshop/add-to-cart-step.php' );
					?>
				</div>
			</div>
		</div>
	</form>
</section>