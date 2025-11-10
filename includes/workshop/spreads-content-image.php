<?
$correct_image = $page['image'];

$selectable_images = array_diff($all_images, [$correct_image]);
$two_random_images = array_rand($selectable_images, 2);

$image_sets['correct'] = $correct_image;

if( ! empty($two_random_images) ){
	foreach($two_random_images as $random_image)
	{
		$image_sets['incorrect'][] = $selectable_images[$random_image];
	}
}
?>

<img src="<?= $page['image']; ?>">