<?php
if( ! is_plugin_active('yith-woocommerce-gift-cards-premium/init.php') ) {
	include(THEME_PATH . '/includes/admin/gift-cards/admin-notice.php');
}
?>

<div class="wrap">
	<h1>Importer un .CSV de cartes cadeaux</h1>
	<p>Assurez-vous d'avoir ce format :</p>

	<table class="admin-table">
		<tr>
			<td># de la carte</td>
			<td>Valeur monétaire</td>
		</tr>
	</table>

	<form id="kantaloup-import-gift-cards" class="kant-form" data-action="ajax_import_gift_cards">
		<div class="form-row">
			<input type="file" name="file" required>
		</div>

		<div class="form-row">
			<input type="submit" class="button button-primary button-large" value="Importer">
		</div>
	</form>
</div>