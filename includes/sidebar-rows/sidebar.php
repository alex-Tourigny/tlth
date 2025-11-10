<aside class="sidebar">
	<? if( have_rows('c2a-sidebar') ){ ?>
		<? while( have_rows('c2a-sidebar') ){ the_row();
			$img = get_sub_field('img');
			$question = get_sub_field('question');
			$btn = get_sub_field('btn');
			?>
			<div class="c2a-sidebar">
				<? if( get_sub_field('img') ){
					echo FW::get_image( get_sub_field('img'), (''), ('')  );
				} ?>
				<? if( get_sub_field('question') ){ ?>
					<h4><?= get_sub_field('question'); ?></h4>
				<? } ?>
				<? if( get_sub_field('btn') ){ ?>
					<div class="btn-spacing">
						<?php echo FW::button( get_sub_field('btn'), ['btn', 'red'] ); ?>
					</div>
				<? } ?>
			</div>
		<? } ?>
	<? } ?>
</aside>
