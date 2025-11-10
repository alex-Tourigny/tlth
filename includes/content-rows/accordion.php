<? if( have_rows('accordion') ){ ?>

		<? while( have_rows('accordion') ){ the_row();
			$title = get_sub_field('title');
			$content = get_sub_field('content');
		?>
		<div class="dropdown">
			<div class="dropdown-content">
				<? if( get_sub_field('title') ){ ?>
					<h5><?= get_sub_field('title'); ?><span class="open-drop"><?= file_get_contents( THEME_PATH . '/assets/images/dropdown.svg');?></span></h5>
				<? } ?>
				<? if( get_sub_field('content') ){ ?>
					<div class="the-content"><?= get_sub_field('content'); ?> </div>
				<? } ?>
			</div>
		</div>
		<? } ?>

<? } ?>
