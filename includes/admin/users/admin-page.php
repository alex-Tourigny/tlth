<div class="wrap">
	<h1>Importer un .CSV d'utilisateurs</h1>
	<p>Assurez-vous d'avoir ce format :</p>

	<table class="admin-table">
		<tr>
			<td>Nom</td>
			<td>Prénom</td>
			<td>Titre</td>
			<td>Adresse</td>
			<td>Ville</td>
			<td>Province</td>
			<td>Pays</td>
			<td>Code postal</td>
			<td>Courriel</td>
			<td>Téléphone</td>
			<td>Date d'inscription</td>
			<td>Infolettre</td>
			<td>CHAMP VIDE</td>
			<td>Type de compte (Particulier/Milieu de garde/Ecole)</td>
		</tr>
	</table>

	<form id="kantaloup-import-users" class="kant-form" data-action="ajax_import_users">
		<div class="form-row">
			<input type="file" name="file" required>
		</div>

		<div class="form-row">
			<input type="submit" class="button button-primary button-large" value="Importer">
		</div>
	</form>
</div>