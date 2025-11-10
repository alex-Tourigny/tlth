<?php
$slides = get_sub_field('slider');

if( $slides ){ ?>
	
	<div class="hero-slider">
		
		<?
		//shuffle($slides);
		foreach($slides as $slide)
		{
			$background_img = $slide['background-img'];
			$icon = $slide['banner-icon'];
			$surtitle = $slide['surtitle'];
			$subtitle = $slide['subtitle'];
			$title = $slide['title'];
			$content = $slide['content'];
			$btn = $slide['btn'];
			$hightlight_img = $slide['highlight-img'];
			$color = $slide['overlay-color'];
			$opacity = $slide['overlay-opacity']; ?>
			
				<div class="hero">
					<? if($background_img){ ?>
						<img data-lazy="<?= $background_img['url']; ?>" alt="<?= $background_img['alt']; ?>">
					<? } ?>
		
					<? if($color){ ?>
						<div class="overlay" style="background-color: <?= $color; ?>; <?= $opacity ? 'opacity: ' . $opacity . ';' : ''; ?>"></div>
					<? } ?>
		
					<div class="wrapper">
						<div class="row align-items-center">
							<div class="col-12 col-md-6 d-flex align-items-center">
								<div class="hero-inner">
									<?php if( $icon ) { ?>
                                        <img src="<?= $icon['url']; ?>" alt="<?= $icon['alt']; ?>">
									<?php } ?>

                                    <?php if( $surtitle ) { ?>
										<h4><?= $surtitle; ?></h4>
									<?php } ?>

                                    <?php if( $subtitle ) { ?>
										<h2><?= $subtitle; ?></h2>
									<?php } ?>
		
									<?php if( $title ){ ?>
										<h1><?= $title; ?></h1>
									<?php } ?>
		
									<?php if( $content ){ ?>
										<div class="the-content"><?= $content; ?></div>
									<?php } ?>
		
									<?php if( $btn ){ ?>
										<div class="btn-spacing">
											<?php echo FW::button( $btn, ['btn', 'red'] ); ?>
										</div>
									<? } ?>
								</div>
							</div>
							<div class="col-12 col-md-6">
								<div class="hero-featured-img">
									<? if( $hightlight_img ){
										echo FW::get_image( $hightlight_img, '', '' );
									} ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			
		<? } ?>
		
	</div>
	
<?php } ?>