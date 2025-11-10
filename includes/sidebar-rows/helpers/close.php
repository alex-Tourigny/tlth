</div>

<?
if( get_sub_field('internal-curve') ) {
	include(THEME_PATH . '/includes/content-rows/helpers/bottom-curve.php');
}
?>

<?
if( get_sub_field('external-curve') ) {
	include(THEME_PATH . '/includes/content-rows/helpers/top-curve.php');
}
?>
</div>