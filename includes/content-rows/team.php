<?php include(THEME_PATH . '/includes/slice-intro.php'); ?>

<div class="team">
	<div class="wrapper big">
		<? if( have_rows('team') ){ ?>
			<div class="row justify-content-center">
				<? while( have_rows('team') ){ the_row();
					$img = get_sub_field('portrait');
					$bg_color = get_sub_field('bg-color');
					$name = get_sub_field('name');
					$tite = get_sub_field('title');
					$bio = get_sub_field('bio');
				?>
					<div class="col-12 col-md-6">
						<div class="team-member" data-mh="team-member">
							<? if( get_sub_field('portrait') ){ ?>
								<div class="top-img">
									<?php echo FW::get_image( get_sub_field('portrait'), '', 'img' ); ?>

									<? if( get_sub_field('bg-color') ){
										echo FW::get_image( get_sub_field('bg-color'), '', 'bg-blob');
									}
									?>
								</div>
							<? } ?>
							<? if( get_sub_field('name') ){ ?>
								<h2><?= get_sub_field('name'); ?></h2>
							<? } ?>
							<? if( get_sub_field('title') ){ ?>
								<h4><?= get_sub_field('title'); ?></h4>
							<? } ?>
							<? if( get_sub_field('bio') ){ ?>
								<div class="the-content"><?= get_sub_field('bio'); ?></div>
							<? } ?>
						</div>
					</div>
				<? } ?>
			</div>
		<? } ?>
	</div>
</div>

<?php include(THEME_PATH . '/includes/slice-outro.php'); ?>