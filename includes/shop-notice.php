<?php if( ! is_shop_notice_enabled() ) return; ?>

<?php if( have_rows('shop-notice-row', 'option')) { ?>

	<div class="notice-slider">

		<?php
		while( have_rows('shop-notice-row', 'option') ){
			the_row();

			$start_time = get_sub_field('shop-notice-date-start-' . LANG, 'option');
			$end_time = get_sub_field('shop-notice-date-end-' . LANG, 'option');
			$content = get_sub_field('shop-notice-content-' . LANG, 'option');
			$link = get_sub_field('shop-notice-link-' . LANG, 'option');
			$bg_color = get_sub_field('bg-color-' . LANG, 'option');
			$text_color = get_sub_field('text-color-' . LANG, 'option');

			$current_time = time();

			if( $current_time < $start_time || $current_time > $end_time ) continue; ?>

				<a href="<?= $link ? $link : 'javascript:;';?>" class="notice-boutique" style="background: <?= $bg_color; ?>">
					<div class="notice-content">
						<? if( $content) { ?>
							<div class="the-content" style="color: <?= $text_color; ?>"><?= $content; ?></div>
						<? } ?>
					</div>
				</a>

		<?php } ?>

	</div>

<?php } ?>
