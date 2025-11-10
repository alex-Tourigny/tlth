<div class="featured-block-preview">
	<a class="featured-block-img" href="<?= get_permalink(); ?>">
		<?php if( FW::featured_image() ){ ?>
			<? echo FW::featured_image(); ?>
		<?php } ?>
	</a>
	<div class="featured-block-content">
		<h3>
			<a class="blog-title" href="<?= get_permalink(); ?>"><?= get_the_title(); ?></a>
		</h3>

		<div class="the-content blog-date"><?= date_i18n('j M Y', get_the_time('U') );?> / <?= get_the_author(); ?></div>

		<div class="the-content blog-excerpt"><?= wp_trim_words(get_the_content(), 20, ' [...]'); ?></div>

		<a class="" href="<?= get_permalink();?>"><?= pll__('read-more'); ?></a>
	</div>
</div>