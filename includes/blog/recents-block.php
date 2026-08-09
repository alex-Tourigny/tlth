<li>
	<a class="recents-block-img" href="<?= get_permalink(); ?>">
		<?php if( TLTH::featured_image() ){ ?>
			<? echo TLTH::featured_image(); ?>
		<?php } ?>
	</a>
	<div class="recents-content">
		<a href="<?= get_permalink(); ?>"><?= get_the_title(); ?></a>

		<div class="the-content blog-date"><?= date_i18n('j M Y', get_the_time('U') );?></div>
	</div>
</li>
