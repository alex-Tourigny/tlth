<div class="contact-block">

		<? if( get_sub_field('bg-img') ){
			echo FW::get_image( get_sub_field('bg-img') );
		} ?>

	<div class="contact-content">
		<div class="row align-items-center justify-content-center">
			<div class="col-md-4">
				<div class="contact-img">
					<?php if( get_sub_field('img')){
						echo FW::get_image( get_sub_field('img') );
					} ?>
				</div>
			</div>
			<div class="col-md-6">
				<div class="inner-contact">
					<? if( get_sub_field('title') ){ ?>
						<h3><?= get_sub_field('title'); ?></h3>
					<? } ?>
					<? if( get_sub_field('address') ){ ?>
						<div class="the-content"><?= get_sub_field('address'); ?></div>
					<? } ?>
					<? if( get_sub_field('tel') ){ ?>
						<a class="tel" href="tel:<?= FW::sanitize_phone_number(get_sub_field('tel') ); ?>"><?= get_sub_field('tel');?></a>
					<? } ?>
				</div>
			</div>
		</div>
	</div>

</div>
