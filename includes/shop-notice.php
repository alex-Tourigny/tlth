<?php if( ! TLTH::is_shop_notice_enabled() ) return; ?>

<?php if( have_rows('shop-notice-row', 'option')) { 
	// Collect valid notices first to count them
	$valid_notices = [];
	
	while( have_rows('shop-notice-row', 'option') ){
		the_row();

		$start_time = get_sub_field('shop-notice-date-start-' . LANG, 'option');
		$end_time = get_sub_field('shop-notice-date-end-' . LANG, 'option');
		$content = get_sub_field('shop-notice-content-' . LANG, 'option');
		$link = get_sub_field('shop-notice-link-' . LANG, 'option');

		$current_time = time();

		if( $current_time < $start_time || $current_time > $end_time ) continue;
		
		$valid_notices[] = [
			'content' => $content,
			'link' => $link,
		];
	}
	
	// Only show if we have valid notices
	if( count($valid_notices) > 0 ) {
		$has_multiple = count($valid_notices) > 1;
		$slider_class = $has_multiple ? 'notice-slider' : 'notice-single';
		?>

		<div id="shop-notice-bar" class="<?= $slider_class; ?> bg-secondary" data-notice-count="<?= count($valid_notices); ?>">
			<?php foreach($valid_notices as $key => $notice) { ?>
				<?php if( $notice['link'] ) { ?>
					<a href="<?= $notice['link']; ?>" class="notice-boutique">
						<div class="notice-content">
							<?php if( $notice['content']) { ?>
								<div class="the-content text-white font-bold"><?= $notice['content']; ?></div>
							<?php } ?>
						</div>
					</a>
				<?php } else { ?>
					<div class="notice-boutique">
						<div class="notice-content">
							<?php if( $notice['content']) { ?>
								<div class="the-content text-white font-bold"><?= $notice['content']; ?></div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		</div>

	<?php }
} ?>
