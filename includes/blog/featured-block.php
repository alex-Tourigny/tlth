
<div class="featured-block">
	<div class="wrapper">
		<div class="row justify-content-center align-items-center">
			<div class="col-12">
				<div class="featured-block-preview">
					<h3>
						<a class="blog-title mobile" href="<?= get_permalink(); ?>"><?= get_the_title(); ?></a>
					</h3>

					<div class="feat-img-block">
						<? the_category(); ?>

						<a class="featured-block-img" href="<?= get_permalink(); ?>">
							<?php if( FW::featured_image() ){ ?>
								<? echo FW::featured_image(); ?>
							<?php } ?>
						</a>
					</div>

					<div class="featured-block-content">
						<h3>
							<a class="blog-title desktop" href="<?= get_permalink(); ?>"><?= get_the_title(); ?></a>
						</h3>

						<div class="the-content blog-date"><?= date_i18n('j M Y', get_the_time('U') );?> / <?= get_the_author_meta('nickname'); ?></div>

						<?php if( get_the_excerpt() ){ ?>
							<div class="the-content blog-excerpt"><?= get_the_excerpt(); ?></div>
						<?php } ?>

						<a class="btn red" href="<?= get_permalink();?>"><?= pll__('discover-this'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
