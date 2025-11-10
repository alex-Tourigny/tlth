<?
if( get_sub_field('video-format') == 'link'){
	$video_format = get_sub_field('video-url');
	$video_url = $video_format;
} else {
	if( get_sub_field('video-format') == 'media') {
		$video_format = get_sub_field('video-media')['url'];
		$video_url = $video_format;
	}
}
?>

<div class="wrapper">

	<div class="video-slice">

		<div class="image">
			<?= FW::get_image( get_sub_field('video-thumbnail') );?>

			<a href="<?= $video_url;?>" class="video-icon" id="play-video" data-fancybox>
				<span><?= pll__('Lire la vidéo');?></span><?= file_get_contents(THEME_PATH . '/assets/images/icon-play.svg');?>
			</a>
		</div>

	</div>

</div>
